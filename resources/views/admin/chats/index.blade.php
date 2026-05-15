@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">Chat CS</h1>
            <p class="text-muted mb-0">Pilih nomor di samping untuk melihat percakapan dan kirim pesan langsung.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="chat-wrapper shadow-sm rounded overflow-hidden">
        <div class="chat-sidebar-wrapper">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <div>
                    <h6 class="mb-1 fw-semibold">Daftar Kontak</h6>
                    <small class="text-muted">{{ $contacts->count() }} kontak aktif</small>
                </div>
            </div>
            <div class="list-group list-group-flush contact-list">
                @forelse($contacts as $contact)
                    <div class="list-group-item py-3 ps-3 pe-2 chat-contact-item {{ isset($contact_wa_id) && $contact_wa_id === $contact->contact_wa_id ? 'active-contact' : '' }}" data-contact="{{ $contact->contact_wa_id }}">
                        <a href="{{ route('admin.chats.show', $contact->contact_wa_id) }}" class="text-decoration-none text-dark d-block chat-contact">
                            <div class="mb-2">
                                <h6 class="mb-1 text-truncate fw-semibold">{{ $contact->customers_name ?? 'Unknown Customer' }}</h6>
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <small class="text-muted">{{ $contact->contact_wa_id }}</small>
                                    <small class="text-muted flex-shrink-0">{{ \Carbon\Carbon::createFromTimestamp($contact->last_timestamp)->diffForHumans() }}</small>
                                </div>
                            </div>
                            <p class="mb-0 text-truncate small text-muted">{{ $contact->last_direction === 'inbound' ? 'Masuk:' : 'Keluar:' }} {{ $contact->last_body }}</p>
                        </a>
                        @if($contact->last_status === 'awaiting_response_cs')
                            <div class="mt-2">
                                <form action="{{ route('admin.chats.complete-case', $contact->contact_wa_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Tandai case ini sebagai selesai?')">
                                        <i class="bi bi-check-circle"></i> Case Selesai
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        Belum ada chat tersimpan.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="chat-panel-wrapper">
            @if(isset($contact_wa_id) && $contact_wa_id)
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1 fw-semibold">{{ $customerName ?? 'Unknown' }}</h5>
                        <small class="text-muted">{{ $contact_wa_id }}</small>
                    </div>
                </div>
                <div class="flex-grow-1 overflow-auto p-4 chat-messages" id="chat-messages">
                    @forelse($messages as $msg)
                        <div class="d-flex mb-3 {{ $msg->direction == 'inbound' ? 'justify-content-start' : 'justify-content-end' }}">
                            <div class="chat-bubble {{ $msg->direction == 'inbound' ? 'inbound' : 'outbound' }}">
                                <div class="mb-1 text-muted small">
                                    @if($msg->direction == 'inbound')
                                        Customer
                                    @elseif($msg->direction == 'outbound' && $msg->type == 'interactive')
                                        Bot
                                    @else
                                        CS
                                    @endif
                                </div>
                                <div class="mb-2">{{ $msg->body }}</div>
                                <div class="text-end chat-time">{{ date('d M H:i', $msg->timestamp_unix) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            Belum ada pesan pada kontak ini. Silakan kirim pesan.</div>
                    @endforelse
                </div>
                <div class="chat-input p-3 bg-white border-top">
                    <form action="{{ route('admin.chats.send', $contact_wa_id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="body" class="form-control rounded-pill" placeholder="Ketik pesan..." autocomplete="off" required>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-grow-1 d-flex align-items-center justify-content-center p-5 text-center">
                    <div>
                        <div class="mb-3"><i class="bi bi-chat-left-text fs-1 text-primary"></i></div>
                        <h5 class="fw-semibold">Pilih kontak di sisi kiri</h5>
                        <p class="text-muted">Lalu Anda bisa membaca pesan dan mengirim balasan seperti di aplikasi chat.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function scrollChatToBottom() {
        const messages = document.getElementById('chat-messages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        container.innerHTML = messages.map(msg => {
            const isInbound = msg.direction === 'inbound';
            const sender = isInbound ? 'Customer' : (msg.type === 'interactive' ? 'Bot' : 'CS');
            return `
                <div class="d-flex mb-3 ${isInbound ? 'justify-content-start' : 'justify-content-end'}">
                    <div class="chat-bubble ${isInbound ? 'inbound' : 'outbound'}">
                        <div class="mb-1 text-muted small">${sender}</div>
                        <div class="mb-2">${msg.body.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                        <div class="text-end chat-time">${new Date(msg.timestamp_unix * 1000).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', function () {
        scrollChatToBottom();

        @if(isset($contact_wa_id) && $contact_wa_id)
            const refreshUrl = '{{ route('admin.chats.refresh', $contact_wa_id) }}';
            let latestCount = document.querySelectorAll('#chat-messages .chat-bubble').length;

            setInterval(async function () {
                try {
                    const response = await fetch(refreshUrl, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (!data.messages) return;
                    if (data.messages.length !== latestCount) {
                        renderMessages(data.messages);
                        latestCount = data.messages.length;
                        scrollChatToBottom();
                    }
                } catch (error) {
                    console.warn('Gagal refresh chat:', error);
                }
            }, 5000);
        @endif
    });
</script>
@endsection
