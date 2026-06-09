<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiBase;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiBase = config('services.whatsapp.api_base', env('WHATSAPP_API_BASE', 'https://api.junandia.my.id'));
        $this->apiKey = config('services.whatsapp.api_key', env('WHATSAPP_API_KEY', ''));
    }

    /**
     * Send plain text message.
     */
    public function sendText(string $to, string $message): array
    {
        return $this->call('v1/send-text', [
            'to' => $to,
            'message' => $message
        ]);
    }

    /**
     * Send interactive button message.
     */
    public function sendButton(string $to, string $body, array $buttons): array
    {
        return $this->call('v1/send-button', [
            'to' => $to,
            'body' => $body,
            'buttons' => $buttons
        ]);
    }

    /**
     * Send invoice notification via WhatsApp.
     */
    public function sendInvoice(string $to, string $orderCode, string $total, string $link): array
    {
        $message = "🧾 *INVOICE PESANAN ANDA*\n\n"
            . "Kode: *{$orderCode}*\n"
            . "Total: *Rp {$total}*\n\n"
            . "Klik link berikut untuk melihat detail:\n{$link}\n\n"
            . "Terima kasih telah berbelanja! 🙏";

        return $this->sendText($to, $message);
    }

    /**
     * Send order status update notification.
     */
    public function sendStatusUpdate(string $to, string $orderCode, string $status): array
    {
        $statusEmoji = match ($status) {
            'confirmed' => '✅',
            'shipped' => '🚚',
            'delivered' => '📦',
            'cancelled' => '❌',
            default => 'ℹ️'
        };

        $message = "{$statusEmoji} *Update Pesanan {$orderCode}*\n\n"
            . "Status terbaru: *{$status}*\n\n"
            . "Klik link berikut untuk cek status:\n"
            . url('/order/status/' . base64_encode($orderCode));

        return $this->sendText($to, $message);
    }

    /**
     * Send the standard order flow buttons (Pesan / CS).
     */
    public function sendOrderMenu(string $to, string $greeting = 'Halo!'): array
    {
        return $this->sendButton($to, $greeting, [
            [
                'type' => 'reply',
                'reply' => ['id' => 'btn_order', 'title' => '🛒 Buat Pesanan']
            ],
            [
                'type' => 'reply',
                'reply' => ['id' => 'btn_cs', 'title' => '💬 Hubungi CS']
            ]
        ]);
    }

    /**
     * Low stock alert notification.
     */
    public function sendLowStockAlert(string $to, string $tankName, float $level, float $capacity): array
    {
        $percent = round(($level / $capacity) * 100, 1);
        $message = "⚠️ *PERINGATAN STOK RENDAH*\n\n"
            . "Tangki: *{$tankName}*\n"
            . "Level: *{$level} / {$capacity} L* ({$percent}%)\n\n"
            . "Segera lakukan pengisian ulang!";

        return $this->sendText($to, $message);
    }

    protected function call(string $endpoint, array $data): array
    {
        $url = rtrim($this->apiBase, '/') . '/' . $endpoint;
        try {
            $response = Http::timeout(15)->withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info("WhatsAppService: {$endpoint} success to {$data['to']}");
                return ['status' => 'success', 'data' => $response->json()];
            }

            Log::warning("WhatsAppService: {$endpoint} failed with " . $response->status(), [
                'response' => $response->body()
            ]);
            return ['status' => 'error', 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error("WhatsAppService: {$endpoint} exception", [
                'message' => $e->getMessage()
            ]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
