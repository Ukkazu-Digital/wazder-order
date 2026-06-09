<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppClient
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        // In a real SaaS, the API URL and Token might come from Tenant settings
        // For now, we use the global config
        $this->baseUrl = config('services.whatsapp_api.url', 'https://api.junandia.my.id/v1');
        $this->apiToken = config('services.whatsapp_api.token', 'secret_tenant_token'); 
    }

    public function sendText($to, $message)
    {
        try {
            $response = Http::withToken($this->apiToken)->post("{$this->baseUrl}/send-text", [
                'to' => $to,
                'message' => $message
            ]);

            if ($response->successful()) {
                return true;
            }
            
            Log::error("WhatsAppClient: Failed to send text to $to", ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsAppClient: Exception sending text to $to", ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendButton($to, $body, array $buttons)
    {
        try {
            $response = Http::withToken($this->apiToken)->post("{$this->baseUrl}/send-button", [
                'to' => $to,
                'body' => $body,
                'buttons' => $buttons
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("WhatsAppClient: Failed to send button to $to", ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsAppClient: Exception sending button to $to", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
