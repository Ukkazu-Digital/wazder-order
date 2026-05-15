<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    private $token;
    private $phoneId;
    private $apiUrl;

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->apiUrl = "https://graph.facebook.com/v25.0/{$this->phoneId}/messages";
    }

    private function getContacts()
    {
        return DB::table('messages')
            ->select(
                'messages.contact_wa_id',
                DB::raw('MAX(messages.timestamp_unix) as last_timestamp'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(messages.body ORDER BY messages.timestamp_unix DESC SEPARATOR "|||"), "|||", 1) as last_body'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(messages.direction ORDER BY messages.timestamp_unix DESC SEPARATOR "|||"), "|||", 1) as last_direction'),
                'contacts.last_status',
                'customers.customers_name'
            )
            ->leftJoin('contacts', 'messages.contact_wa_id', '=', 'contacts.wa_id')
            ->leftJoin('customers', 'contacts.customer_id', '=', 'customers.id')
            ->groupBy('messages.contact_wa_id', 'contacts.customer_id', 'customers.customers_name')
            ->orderByDesc('last_timestamp')
            ->get();
    }

    public function index(Request $request)
    {
        $contacts = $this->getContacts();
        $contact_wa_id = $request->query('contact', optional($contacts->first())->contact_wa_id);
        $messages = $contact_wa_id ? Message::where('contact_wa_id', $contact_wa_id)->orderBy('timestamp_unix', 'asc')->get() : collect();
        
        // Ambil data customer
        $customerName = '';
        if ($contact_wa_id) {
            $customerData = DB::table('contacts')
                ->leftJoin('customers', 'contacts.customer_id', '=', 'customers.id')
                ->where('contacts.wa_id', $contact_wa_id)
                ->select('customers.customers_name')
                ->first();
            $customerName = $customerData->customers_name ?? 'Unknown';
        }

        return view('admin.chats.index', compact('contacts', 'messages', 'contact_wa_id', 'customerName'));
    }

    public function show($contact_wa_id)
    {
        $contacts = $this->getContacts();
        $messages = Message::where('contact_wa_id', $contact_wa_id)->orderBy('timestamp_unix', 'asc')->get();
        
        // Ambil data customer
        $customerData = DB::table('contacts')
            ->leftJoin('customers', 'contacts.customer_id', '=', 'customers.id')
            ->where('contacts.wa_id', $contact_wa_id)
            ->select('customers.customers_name')
            ->first();
        
        $customerName = $customerData->customers_name ?? 'Unknown';

        return view('admin.chats.index', compact('contacts', 'messages', 'contact_wa_id', 'customerName'));
    }

    public function send(Request $request, $contact_wa_id)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $contact_wa_id,
            'type' => 'text',
            'text' => ['body' => $request->body]
        ];
        Log::info("Mengirim Pesan Teks ke $contact_wa_id");
        $this->executeSendMessage($payload, 'text', $request->body);

        return redirect()->route('admin.chats.show', $contact_wa_id)->with('success', 'Pesan berhasil dikirim.');
    }

    public function refresh($contact_wa_id)
    {
        $messages = Message::where('contact_wa_id', $contact_wa_id)
            ->orderBy('timestamp_unix', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'direction' => $msg->direction,
                    'type' => $msg->type,
                    'body' => $msg->body,
                    'timestamp_unix' => $msg->timestamp_unix,
                ];
            }),
        ]);
    }

    private function executeSendMessage($payload, $type, $bodyContent)
    {
        $response = Http::withToken($this->token)->post($this->apiUrl, $payload);

        if ($response->successful()) {
            Log::info("API Meta SUCCESS mengirim $type ke " . $payload['to']);
            
            // Simpan outbound ke DB
             Message::create([
                'contact_wa_id' => $payload['to'],
                'direction' => 'outbound',
                'type' => 'text',
                'body' => $bodyContent,
                'timestamp_unix' => now()->timestamp,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 'sent',
            ]);
        } else {
            Log::error("API Meta FAILED mengirim $type", [
                'response' => $response->json(),
                'payload' => $payload
            ]);
        }

        return $response->json();
    }

    public function completeCase($contact_wa_id)
    {
        try {
            DB::table('contacts')
                ->where('wa_id', $contact_wa_id)
                ->update(['last_status' => 'awaiting_response_bot']);

            // Kirim notifikasi ke customer
            $notificationMessage = "Terima kasih telah menghubungi kami! Chat dengan CS kami telah selesai. Sistem kami akan kembali membantu Anda jika diperlukan.";
            
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $contact_wa_id,
                'type' => 'text',
                'text' => ['body' => $notificationMessage]
            ];
            
            Log::info("Mengirim notifikasi case selesai ke $contact_wa_id");
            $this->executeSendMessage($payload, 'text', $notificationMessage);

            return redirect()->route('admin.chats.show', $contact_wa_id)->with('success', 'Case selesai. Notifikasi telah dikirim ke customer.');
        } catch (\Exception $e) {
            Log::error("Error updating case status: " . $e->getMessage());
            return redirect()->route('admin.chats.show', $contact_wa_id)->with('error', 'Gagal memperbarui status case.');
        }
    }
}
