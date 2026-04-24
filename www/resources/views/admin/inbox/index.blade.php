@extends('layouts.app')

@section('title', 'Inbox CRM - Contatos do Site')

@section('content')
<div class="row">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-dark"><i class="bi bi-inbox-fill text-primary me-2"></i> Caixa de Entrada (SAC)</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm border-top border-primary border-4">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($messages as $msg)
                        @php
                            $isUnread = $msg->status === 'pending';
                            $bgClass = $isUnread ? 'bg-light' : '';
                            $fwClass = $isUnread ? 'fw-bold text-dark' : 'text-secondary';
                        @endphp
                        <a href="{{ route('inbox.show', $msg->id) }}" class="list-group-item list-group-item-action p-4 {{ $bgClass }} border-bottom">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if($isUnread)
                                        <i class="bi bi-envelope-fill fs-4 text-primary"></i>
                                    @elseif($msg->status === 'read')
                                        <i class="bi bi-envelope-open fs-4 text-muted"></i>
                                    @else
                                        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 {{ $fwClass }}">{{ $msg->name }}</h6>
                                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-truncate" style="max-width: 70%;">{{ $msg->subject }} - <span class="text-muted fw-normal">{{ $msg->message }}</span></p>
                                    <small class="text-muted">{{ $msg->email }}</small>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-inbox fs-1 mb-2 d-block text-black-50"></i>
                            Caixa de entrada vazia. Nenhum contato pendente pela web.
                        </div>
                    @endforelse
                </div>
            </div>
            @if($messages->hasPages())
                <div class="card-footer bg-white border-0 mt-3 align-items-center">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
