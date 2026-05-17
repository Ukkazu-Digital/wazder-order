@extends('layouts.app')

@section('content')
<!-- Import Google Font & Bootstrap Icons untuk UI layaknya WhatsApp Web -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    :root {
        --chat-bg: #efeae2;
        --inbound-color: #ffffff;
        --outbound-color: #d9fdd3;
        --sidebar-width: 360px;
    }
    
    .chat-app-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
        height: calc(100vh - 120px);
        min-height: 550px;
        background: #fff;
    }

    .chat-wrapper-layout {
        display: flex;
        height: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    }

    /* Kiri: Sidebar Kontak */
    .chat-sidebar {
        width: var(--sidebar-width);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #f0f0f0;
        background: #fff;
    }

    .contact-search-box {
        position: relative;
        padding: 12px 16px;
        background: #fff;
        border-bottom: 1px solid #f5f5f5;
    }

    .contact-search-box .bi-search {
        position: absolute;
        left: 28px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0a0a0;
    }

    .contact-search-box input {
        padding-left: 38px;
        background-color: #f0f2f5;
        border: none;
        font-size: 14px;
        border-radius: 10px;
    }

    .contact-list-scroll {
        flex-grow: 1;
        overflow-y: auto;
    }

    .contact-item-link {
        display: block;
        padding: 14px 16px;
        border-bottom: 1px solid #f8f9fa;
        text-decoration: none !important;
        color: inherit;
        transition: background 0.2s ease;
    }

    .contact-item-link:hover {
        background-color: #f5f6f6;
    }

    .contact-item-link.active-contact-card {
        background-color: #f0f2f5;
    }

    /* Kanan: Panel Isi Pesan */
    .chat-main-panel {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--chat-bg);
        position: relative;
    }

    .chat-main-header {
        padding: 14px 20px;
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
        z-index: 10;
    }

    .chat-messages-area {
        flex-grow: 1;
        overflow-y: auto;
        padding: 24px;
        display: flex;
        flex-direction: column;
    }

    /* Balon Percakapan */
    .msg-row {
        display: flex;
        margin-bottom: 12px;
        width: 100%;
    }

    .msg-row.justify-inbound { justify-content: flex-start; }
    .msg-row.justify-outbound { justify-content: flex-end; }

    .bubble-card {
        max-width: 65%;
        padding: 8px 12px;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        position: relative;
        font-size: 14px;
        line-height: 20px;
    }

    .bubble-card.inbound-style {
        background-color: var(--inbound-color);
        color: #111b21;
        border-top-left-radius: 0;
    }

    .bubble-card.outbound-style {
        background-color: var(--outbound-color);
        color: #111b21;
        border-top-right-radius: 0;
    }

    .sender-tag {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .text-cs-tag { color: #0275d8; }
    .text-bot-tag { color: #5cb85c; }
    .text-cust-tag { color: #6f42c1; }

    .chat-meta-time {
        font-size: 10px;
        color: #667781;
        text-align: right;
        margin-top: 4px;
        display: block;
    }

    .chat-footer-input {
        padding: 12px 20px;
        background: #f0f2f5;
        border-top: 1px solid #e0e0e0;
    }

    .chat-footer-input input {
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 14px;
    }
</style>

<div class="container-fluid py-3">
    
    <!-- Pemberitahuan Flash Sistem -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="chat-app-container">
        <div class="chat-wrapper-layout">
            
            <!-- SIDEBAR KIRI: Daftar Kontak Chat -->
            <div class="chat-sidebar">
                <!-- Header Sidebar -->
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-whatsapp text-success me-2"></i>Inbox Chat Bot</h5>
                        <small class="text-muted fw-medium">{{ $contacts->count() }} Percakapan Aktif</small>
                    </div>
                </div>

                <!-- Kolom Pencarian Kontak Rapi -->
                <div class="contact-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchContact" class="form-control" placeholder="Cari nama pelanggan / nomor WA..." onkeyup="filterContacts()">
                </div>

                <!-- List Item Kontak -->
                <div class="contact-list-scroll" id="contactListGroup">
                    @forelse($contacts as $contact)
                        @php 
                            $isActive = isset($contact_wa_id) && $contact_wa_id === $contact->contact_wa_id;
                            $isWaiting = $contact->last_status === 'awaiting_response_cs';
                        @endphp
                        <div class="contact-card-item border-bottom bg-white">
                            <a href="{{ route('admin.chats.show', $contact->contact_wa_id) }}" class="contact-item-link {{ $isActive ? 'active-contact-card' : '' }}">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h6 class="mb-0 fw-bold text-truncate text-dark" style="max-width: 180px;">
                                        {{ $contact->customers_name ?? 'Pelanggan Baru' }}
                                    </h6>
                                    <small class="text-muted style-time" style="font-size: 11px;">
                                        {{ \Carbon\Carbon::createFromTimestamp($contact->last_timestamp)->diffForHumans(null, true) }}
                                    </small>
                                </div>
                                <div class="text-muted small mb-1 font-monospace" style="font-size: 11px;">
                                    <i class="bi bi-phone me-1"></i>{{ $contact->contact_wa_id }}
                                </div>
                                <p class="mb-0 text-truncate small text-secondary">
                                    <span class="fw-semibold text-dark">{{ $contact->last_direction === 'inbound' ? 'Masuk:' : 'Keluar:' }}</span> 
                                    {{ $contact->last_body }}
                                </p>
                            </a>
                            
                            <!-- Tombol Shortcut Selesaikan Case Jika Status Menunggu CS -->
                            @if($isWaiting)
                                <div class="px-3 pb-3 pt-1">
                                    <form action="{{ route('admin.chats.complete-case', $contact->contact_wa_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light border text-success fw-bold btn-sm w-100 py-1" style="font-size: 11px; border-radius: 8px;" onclick="return confirm('Tandai permasalahan ini telah selesai direspon?')">
                                            <i class="bi bi-check2-all me-1"></i>Tandai Case Selesai
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-dots fs-2 d-block mb-2 text-black-50"></i>
                            <span class="small">Belum ada riwayat pesan masuk.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- PANEL KANAN: Konten Isi Pesan / Ruang Obrolan -->
            <div class="chat-main-panel">
                @if(isset($contact_wa_id) && $contact_wa_id)
                    
                    <!-- Header Room Chat -->
                    <div class="chat-main-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">{{ $customerName ?? 'Pelanggan Tanpa Nama' }}</h5>
                            <small class="text-muted font-monospace"><i class="bi bi-whatsapp text-muted me-1"></i>{{ $contact_wa_id }}</small>
                        </div>
                        <div>
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-medium" style="font-size: 11px;">Sesi CS Terhubung</span>
                        </div>
                    </div>

                    <!-- Area Streaming Pesan -->
                    <div class="chat-messages-area" id="chat-messages">
                        @forelse($messages as $msg)
                            @php $isInbound = $msg->direction == 'inbound'; @endphp
                            <div class="msg-row {{ $isInbound ? 'justify-inbound' : 'justify-outbound' }}">
                                <div class="bubble-card {{ $isInbound ? 'inbound-style' : 'outbound-style' }}">
                                    <!-- Label Pengirim Pesan -->
                                    <div class="sender-tag {{ $isInbound ? 'text-cust-tag' : ($msg->type == 'interactive' ? 'text-bot-tag' : 'text-cs-tag') }}">
                                        @if($isInbound)
                                            <i class="bi bi-person-fill me-1"></i>Customer
                                        @elseif(!$isInbound && $msg->type == 'interactive')
                                            <i class="bi bi-robot me-1"></i>System Bot
                                        @else
                                            <i class="bi bi-headset me-1"></i>Customer Service (Anda)
                                        @endif
                                    </div>
                                    <!-- Isi Pesan -->
                                    <div class="msg-content-text text-break">{{ $msg->body }}</div>
                                    <!-- Penunjuk Waktu Kirim -->
                                    <span class="chat-meta-time">{{ date('d M Y, H:i', $msg->timestamp_unix) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="my-auto text-center text-muted py-5">
                                <i class="bi bi-chat-quote-fill fs-1 text-black-50 mb-2 d-block"></i>
                                Belum ada obrolan teks pada kontak ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Input Bar Balasan CS -->
                    <div class="chat-footer-input">
                        <form action="{{ route('admin.chats.send', $contact_wa_id) }}" method="POST" class="d-flex gap-2 align-items-center">
                            @csrf
                            <input type="text" name="body" class="form-control" placeholder="Ketik pesan balasan resmi CS ke WhatsApp..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                                <i class="bi bi-send-fill fs-5"></i>
                            </button>
                        </form>
                    </div>

                @else
                    <!-- Tampilan Default Jika Belum Pilih Kontak -->
                    <div class="my-auto text-center px-4">
                        <div class="mb-3">
                            <i class="bi bi-chat-left-heart text-primary" style="font-size: 70px; opacity: 0.8;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Pusat Layanan Konsumen (Omnichannel)</h4>
                        <p class="text-muted mx-auto mb-0" style="max-width: 420px; font-size: 14px;">
                            Pilih salah satu nomor pelanggan di panel kiri untuk meninjau log interaksi bot dan membalas pesan secara *real-time*.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    // Memaksa scrollbar pesan langsung lompat ke baris paling bawah saat halaman dibuka
    function scrollChatToBottom() {
        const messages = document.getElementById('chat-messages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    // Fungsi pencarian/filtering kontak lokal via javascript di sidebar
    function filterContacts() {
        const input = document.getElementById('searchContact').value.toLowerCase();
        const cards = document.getElementsByClassName('contact-card-item');

        for (let i = 0; i < cards.length; i++) {
            const content = cards[i].textContent || cards[i].innerText;
            if (content.toLowerCase().indexOf(input) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }

    // Generator render struktur HTML balon chat baru saat interval fetch terpanggil
    function renderMessages(messages) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        
        container.innerHTML = messages.map(msg => {
            const isInbound = msg.direction === 'inbound';
            const isBot = msg.type === 'interactive';
            
            let sender = '<i class="bi bi-headset me-1"></i>Customer Service (Anda)';
            let tagClass = 'text-cs-tag';
            
            if (isInbound) {
                sender = '<i class="bi bi-person-fill me-1"></i>Customer';
                tagClass = 'text-cust-tag';
            } else if (isBot) {
                sender = '<i class="bi bi-robot me-1"></i>System Bot';
                tagClass = 'text-bot-tag';
            }

            const formattedTime = new Date(msg.timestamp_unix * 1000).toLocaleString('id-ID', { 
                day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' 
            });

            return `
                <div class="msg-row ${isInbound ? 'justify-inbound' : 'justify-outbound'}">
                    <div class="bubble-card ${isInbound ? 'inbound-style' : 'outbound-style'}">
                        <div class="sender-tag ${tagClass}">${sender}</div>
                        <div class="msg-content-text text-break">${msg.body.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                        <span class="chat-meta-time">${formattedTime}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', function () {
        scrollChatToBottom();

        @if(isset($contact_wa_id) && $contact_wa_id)
            const refreshUrl = '{{ route('admin.chats.refresh', $contact_wa_id) }}';
            let latestCount = document.querySelectorAll('#chat-messages .bubble-card').length;

            // Mekanisme Long Polling berkala memantau update pesan whatsapp masuk baru
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
                    console.warn('Gagal melakukan sync updates obrolan chat.', error);
                }
            }, 5000);
        @endif
    });
</script>
@endsection