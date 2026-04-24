@extends('layouts.app')

@section('title', 'Detalhes da Mensagem - Inbox CRM')

@section('content')
<div class="row">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('inbox.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
            
            <div class="d-flex gap-2">
                @if($message->status !== 'replied')
                    <form action="{{ route('inbox.reply', $message->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-success"><i class="bi bi-check-all me-1"></i> Marcar como Respondido</button>
                    </form>
                @else
                    <span class="badge bg-success py-2 px-3 fw-bold"><i class="bi bi-check-all me-1"></i> Respondido</span>
                @endif

                <form action="{{ route('inbox.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Excluir esta mensagem permanentemente?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-4">
                <h4 class="mb-3 text-dark">{{ $message->subject }}</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $message->name }}</strong>
                        <span class="text-muted mx-2">&lt;{{ $message->email }}&gt;</span>
                    </div>
                    <div class="text-muted small">
                        {{ $message->created_at->format('d/m/Y H:i') }} ({{ $message->created_at->diffForHumans() }})
                    </div>
                </div>
                @if($message->phone)
                    <div class="mt-2 text-muted">
                        <i class="bi bi-telephone-fill me-1"></i> <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->phone) }}" target="_blank">{{ $message->phone }}</a>
                    </div>
                @endif
            </div>
            
            <div class="card-body p-4 bg-light" style="min-height: 250px;">
                <p class="fs-5 text-dark" style="white-space: pre-line;">
                    {{ $message->message }}
                </p>
            </div>
            
            <div class="card-footer bg-white text-muted p-3">
                <small>Mensagem Recebida via Formulário Site - Status Ticket: <strong>{{ strtoupper($message->status) }}</strong></small>
            </div>
        </div>
    </div>
</div>
@endsection
