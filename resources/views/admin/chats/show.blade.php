@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chat dengan {{ $contact_wa_id }}</h1>
    <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary mb-3">Kembali</a>
    <div class="card mb-3" style="max-height: 500px; overflow-y: auto;">
        <div class="card-body">
            @foreach($messages as $msg)
                <div class="mb-2">
                    <div class="d-flex {{ $msg->direction == 'inbound' ? 'justify-content-start' : 'justify-content-end' }}">
                        <div class="p-2 rounded {{ $msg->direction == 'inbound' ? 'bg-light' : 'bg-primary text-white' }}" style="max-width: 70%;">
                            <div>
                                <b>
                                    @if($msg->direction == 'inbound')
                                        Customer
                                    @elseif($msg->direction == 'outbound' && $msg->type == 'interactive')
                                        Bot
                                    @else
                                        CS
                                    @endif
                                </b>
                            </div>
                            <div>{{ $msg->body }}</div>
                            <div class="text-muted small">{{ date('d-m-Y H:i', $msg->timestamp_unix) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
