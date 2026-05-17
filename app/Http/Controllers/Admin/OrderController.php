<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Kurir;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $kurirs = Kurir::where('status', 'Aktif')->get();
        return view('admin.orders.show', compact('order', 'kurirs'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'kurir_id' => $request->status === 'shipped' ? 'required|exists:kurirs,id' : 'nullable',
        ]);

        $order->status = $request->status;

        // Assign kurir jika status berubah ke shipped
        if ($request->status === 'shipped' && $request->kurir_id) {
            $order->kurir_id = $request->kurir_id;
        }

        if ($order->save()) {
            $note = 'Pesanan berubah status menjadi ' . $request->status;
            if ($request->status === 'shipped' && $request->kurir_id) {
                $kurir = Kurir::find($request->kurir_id);
                $note .= ' - Kurir: ' . $kurir->name;
            }

            DB::table('order_histories')->insert([
                'order_id' => $order->id,
                'status' => $request->status,
                'note' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // OTOMATISASI: Jika status berubah menjadi completed, jalankan pengiriman invoice
            if ($request->status === 'completed') {
                $sendingJob = $this->executeSendInvoice($order);
                
                // Jika pengiriman otomatis gagal karena nomor WA tidak ada atau API error,
                // status pesanan tetap sukses berubah, namun beri tahu admin lewat flash message.
                if (!$sendingJob['success']) {
                    return redirect()->route('admin.orders.index')->with('success', 'Status updated to Completed, but invoice failed to send: ' . $sendingJob['message']);
                }
                
                return redirect()->route('admin.orders.index')->with('success', 'Status updated to Completed and Invoice sent via WhatsApp!');
            }
        }
        return redirect()->route('admin.orders.index')->with('success', 'Status updated!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted!');
    }

    /**
     * Render printable invoice view
     */
    public function invoice(Order $order)
    {
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Send invoice via WhatsApp if customer's phone available (Manual trigger from view)
     */
    public function sendInvoice(Request $request, Order $order)
    {
        $result = $this->executeSendInvoice($order);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Core Logic: Proses utama pengiriman file invoice gambar via API WhatsApp Cloud
     */
    private function executeSendInvoice(Order $order)
    {
        $idCustomer = data_get($order, 'customer.id');
        $phone = Contact::where('customer_id', $idCustomer)->value('wa_id');

        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp customer tidak tersedia.'
            ];
        }

        $token = env('WHATSAPP_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $apiUrl = "https://graph.facebook.com/v25.0/{$phoneId}/messages";

        try {
            // Generate receipt image and store publicly
            $imageUrl = $this->createReceiptImage($order);

            if (!$imageUrl) {
                return [
                    'success' => false,
                    'message' => 'Gagal membuat gambar invoice.'
                ];
            }

            // Payload to send image by link
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'image',
                'image' => [ 'link' => $imageUrl ]
            ];

            $response = Http::withToken($token)->post($apiUrl, $payload);
            
            if ($response->successful()) {
                Log::info('Invoice image sent via WhatsApp to ' . $phone, ['url' => $imageUrl]);
                return [
                    'success' => true,
                    'message' => 'Invoice berhasil dikirim via WhatsApp.'
                ];
            }
            
            Log::error('Failed send invoice WA', ['resp' => $response->json()]);
            return [
                'success' => false,
                'message' => 'Gagal mengirim invoice via WhatsApp API.'
            ];

        } catch (\Exception $e) {
            Log::error('Error send invoice WA: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mengirim invoice.'
            ];
        }
    }

    /**
     * Create a simple receipt image (PNG) for a given order using GD and return public URL
     */
    private function createReceiptImage(Order $order)
    {
        // Pastikan folder penyimpanan ada
        $dir = public_path('storage/invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Menggunakan font bawaan nomor 3 (lebar karakter sekitar 7px, tinggi baris cocok di 16px)
        // Dengan lebar gambar 384px, muat sekitar 50-52 karakter per baris. 
        // Kita targetkan 48 karakter aman agar ada margin kanan-kiri.
        $maxChars = 48; 
        $lines = [];

        // 1. Header Toko
        $appName = strtoupper(config('app.name', 'LARAWABA'));
        $lines[] = str_pad($appName, $maxChars, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('STRUK RESMI PEMBELIAN', $maxChars, ' ', STR_PAD_BOTH);
        $lines[] = str_repeat('-', $maxChars);

        // 2. Meta Informasi Nota (Sejajar menggunakan format teks)
        $lines[] = sprintf('%-10s: %s', 'Nota', $order->order_code);
        $lines[] = sprintf('%-10s: %s', 'Tanggal', $order->created_at->format('d-m-Y H:i'));
        $lines[] = sprintf('%-10s: %s', 'Kasir', auth()->user()->name ?? 'Admin POS');
        $lines[] = str_repeat('-', $maxChars);

        // 3. Daftar Item Belanjaan
        $lines[] = 'DAFTAR BELANJAAN:';
        foreach ($order->details as $d) {
            $productName = \Illuminate\Support\Str::limit($d->product->name ?? '-', 22, '');
            $qtyText = 'x' . $d->qty;
            
            // Baris Nama Produk & Qty (Rata Kiri & Rata Kanan)
            $spacesForFirstRow = $maxChars - strlen($productName) - strlen($qtyText);
            $lines[] = $productName . str_repeat(' ', max($spacesForFirstRow, 1)) . $qtyText;

            // Baris Harga Satuan & Subtotal (Inden ke dalam sedikit)
            $priceText = '  @ Rp ' . number_format($d->buy_price, 0, ',', '.');
            $subtotalText = 'Rp ' . number_format($d->subtotal, 0, ',', '.');
            $spacesForSecondRow = $maxChars - strlen($priceText) - strlen($subtotalText);
            $lines[] = $priceText . str_repeat(' ', max($spacesForSecondRow, 1)) . $subtotalText;
        }
        $lines[] = str_repeat('-', $maxChars);

        // 4. Kalkulasi Total Belanja
        $totalLabel = 'TOTAL AKHIR';
        $totalValue = 'Rp ' . number_format($order->total_price, 0, ',', '.');
        $spacesForTotal = $maxChars - strlen($totalLabel) - strlen($totalValue);
        $lines[] = $totalLabel . str_repeat(' ', max($spacesForTotal, 1)) . $totalValue;
        $lines[] = str_repeat('-', $maxChars);

        // 5. Data Penerima / Logistik Pelanggan
        $customerName = $order->customer->customers_name ?? 'Walk-in Customer';
        $lines[] = sprintf('%-10s: %s', 'Pelanggan', $customerName);
        
        if ($order->customer && $order->customer->address) {
            $lines[] = sprintf('%-10s: %s', 'Alamat', $order->customer->address);
        }
        
        if ($order->kurir) {
            $kurirText = sprintf('%s [%s]', $order->kurir->name, $order->kurir->plate_number);
            $lines[] = sprintf('%-10s: %s', 'Kurir', $kurirText);
        }
        $lines[] = str_repeat('-', $maxChars);

        // 6. Footer Penutup (Rata Tengah)
        $lines[] = str_pad('Terima kasih atas kunjungan Anda', $maxChars, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('Barang yang sudah dibeli tidak dapat', $maxChars, ' ', STR_PAD_BOTH);
        $lines[] = str_pad('ditukar/dikembalikan.', $maxChars, ' ', STR_PAD_BOTH);

        // Pengaturan Gambar Gambar GD
        $width = 384; 
        $lineHeight = 16; 
        $padding = 12;    
        $height = $padding * 2 + count($lines) * $lineHeight;

        $img = imagecreate($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        // Background Putih
        imagefilledrectangle($img, 0, 0, $width, $height, $white);

        // Rendering teks ke Gambar
        $font = 3; 
        $y = $padding;
        foreach ($lines as $line) {
            imagestring($img, $font, 12, $y, $line, $black);
            $y += $lineHeight;
        }

        $filename = $order->order_code . '_' . time() . '.png';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        imagepng($img, $path);
        imagedestroy($img);

        // Kembalikan Public URL
        return url('storage/invoices/' . $filename);
    }
}