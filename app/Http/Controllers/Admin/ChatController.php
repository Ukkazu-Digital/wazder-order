<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
        $this->middleware(['auth', 'verified']);
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
            ->groupBy('messages.contact_wa_id', 'contacts.customer_id', 'customers.customers_name', 'contacts.last_status')
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

        $result = $this->whatsapp->sendText($contact_wa_id, $request->body);

        if ($result['status'] === 'success') {
             Message::create([
                'contact_wa_id' => $contact_wa_id,
                'direction' => 'outbound',
                'type' => 'text',
                'body' => $request->body,
                'timestamp_unix' => now()->timestamp,
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 'sent',
            ]);
            return redirect()->route('admin.chats.show', $contact_wa_id)->with('success', 'Pesan berhasil dikirim.');
        }

        return redirect()->route('admin.chats.show', $contact_wa_id)->with('error', 'Gagal mengirim pesan: ' . ($result['message'] ?? 'Unknown Error'));
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

    public function completeCase($contact_wa_id)
    {
        try {
            DB::table('contacts')
                ->where('wa_id', $contact_wa_id)
                ->update(['last_status' => 'awaiting_response_bot']);

            $notificationMessage = "Terima kasih telah menghubungi kami! Chat dengan CS kami telah selesai. Sistem kami akan kembali membantu Anda jika diperlukan.";
            
            $result = $this->whatsapp->sendText($contact_wa_id, $notificationMessage);

            if ($result['status'] === 'success') {
                Message::create([
                    'contact_wa_id' => $contact_wa_id,
                    'direction' => 'outbound',
                    'type' => 'text',
                    'body' => $notificationMessage,
                    'timestamp_unix' => now()->timestamp,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'status' => 'sent',
                ]);
            }

            return redirect()->route('admin.chats.show', $contact_wa_id)->with('success', 'Case selesai. Notifikasi telah dikirim ke customer.');
        } catch (\Exception $e) {
            Log::error("Error updating case status: " . $e->getMessage());
            return redirect()->route('admin.chats.show', $contact_wa_id)->with('error', 'Gagal memperbarui status case.');
        }
    }
}
