<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Kurir;
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
     * Send invoice via WhatsApp if customer's phone available
     */
    public function sendInvoice(Request $request, Order $order)
    {
        $phone = data_get($order, 'customer.customers_wa_id');
        if (!$phone) {
            return redirect()->back()->with('error', 'Nomor WhatsApp customer tidak tersedia.');
        }

        $token = env('WHATSAPP_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $apiUrl = "https://graph.facebook.com/v25.0/{$phoneId}/messages";

        // Build items text
        $itemsText = '';
        foreach ($order->details as $d) {
            $itemsText .= $d->product->name . ' (x' . $d->qty . ') - Rp ' . number_format($d->subtotal,0,',','.') . "\n";
        }

        $body = "Invoice {$order->order_code}\nTotal: Rp " . number_format($order->total_price,0,',','.') . "\n\nItems:\n" . $itemsText;

        try {
            // Generate receipt image and store publicly
            $imageUrl = $this->createReceiptImage($order);

            if (!$imageUrl) {
                return redirect()->back()->with('error', 'Gagal membuat gambar invoice');
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
                return redirect()->back()->with('success', 'Invoice berhasil dikirim via WhatsApp');
            }
            Log::error('Failed send invoice WA', ['resp' => $response->json()]);
            return redirect()->back()->with('error', 'Gagal mengirim invoice via WhatsApp');
        } catch (\Exception $e) {
            Log::error('Error send invoice WA: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim invoice');
        }
    }

    /**
     * Create a simple receipt image (PNG) for a given order using GD and return public URL
     */
    private function createReceiptImage(Order $order)
    {
        // Ensure storage folder exists
        $dir = public_path('storage/invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Prepare lines for the receipt
        $lines = [];
        $lines[] = config('app.name', 'Larawaba');
        $lines[] = 'INVOICE: ' . $order->order_code;
        $lines[] = 'Tgl: ' . $order->created_at->format('d-m-Y H:i');
        $lines[] = '------------------------------';
        foreach ($order->details as $d) {
            $name = substr($d->product->name ?? '-', 0, 24);
            $lines[] = sprintf('%-20s %3sx Rp%s', $name, $d->qty, number_format($d->buy_price,0,',','.'));
            $lines[] = sprintf('  Sub: Rp%s', number_format($d->subtotal,0,',','.'));
        }
        $lines[] = '------------------------------';
        $lines[] = 'TOTAL: Rp ' . number_format($order->total_price,0,',','.');
        $lines[] = '------------------------------';
        $lines[] = 'Pelanggan: ' . ($order->customer->customers_name ?? '-');
        $lines[] = 'Alamat: ' . ($order->customer->address ?? '-');
        $lines[] = 'Terima kasih!';

        // Image settings (thermal-like width)
        $width = 384; // typical thermal width
        $lineHeight = 14;
        $padding = 8;
        $height = $padding * 2 + count($lines) * $lineHeight;

        $img = imagecreate($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        // Background white
        imagefilledrectangle($img, 0, 0, $width, $height, $white);

        // Use built-in font
        $font = 3; // small built-in font
        $y = $padding;
        foreach ($lines as $line) {
            imagestring($img, $font, 6, $y, $line, $black);
            $y += $lineHeight;
        }

        $filename = $order->order_code . '_' . time() . '.png';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        imagepng($img, $path);
        imagedestroy($img);

        // Return public URL
        return url('storage/invoices/' . $filename);
    }
}
