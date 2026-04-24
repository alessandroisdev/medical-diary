@extends('layouts.public')
@section('title', 'Local e Contato')

@section('content')
<div class="page-header text-center">
    <div class="container">
        <h1 class="fw-bold">Fale Conosco</h1>
        <p class="fs-5 opacity-75">Nossa recepção entrará em contato em instantes.</p>
    </div>
</div>

<section class="section-padding bg-light-subtle pt-2 mb-5">
    <div class="container">
        <div class="row g-5">
            <!-- Informações e Mapa -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h4 class="fw-bold mb-4">Mesa de Atendimento</h4>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-3">
                            <i class="bi bi-envelope-at-fill text-primary me-2"></i>
                            <strong>Canal Corporativo:</strong><br/>
                            <a href="mailto:{{ $settings['contact_email'] ?? '' }}" class="text-decoration-none text-muted">{{ $settings['contact_email'] ?? 'Aguardando Configuração' }}</a>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill text-primary me-2"></i>
                            <strong>WhatsApp / Recepção:</strong><br/>
                            <span class="text-muted">{{ $settings['contact_phone'] ?? 'Em Implantação' }}</span>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                            <strong>Endereço Presencial:</strong><br/>
                            <span class="text-muted">{{ $settings['clinic_address'] ?? 'Cidade' }}</span>
                        </li>
                    </ul>
                    
                    <div class="ratio ratio-16x9 rounded overflow-hidden">
                        @if(!empty($settings['map_iframe_url']))
                            <iframe src="{{ $settings['map_iframe_url'] }}" loading="lazy" frameborder="0" style="border:0;" allowfullscreen></iframe>
                        @else
                            <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-muted">Mapa Desativado</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Formulário Seguro -->
            <div class="col-lg-7">
                <div class="card border-0 border-top border-primary border-4 shadow-sm p-4 p-md-5">
                    <h5 class="fw-bold mb-4">Formulário Criptografado (Ticket)</h5>
                    
                    <div id="contactMsgContainer" class="d-none alert alert-success"></div>

                    <form id="contactForm" onsubmit="submitForm(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Seu Nome *</label>
                                <input type="text" class="form-control" name="name" required placeholder="João da Silva">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">E-mail Principal *</label>
                                <input type="email" class="form-control" name="email" required placeholder="joao@empresa.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Telefone (Opcional)</label>
                                <input type="text" class="form-control" name="phone" placeholder="(11) 90000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Assunto</label>
                                <input type="text" class="form-control" name="subject" placeholder="Dúvida sobre Agendamento">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Sua Mensagem *</label>
                                <textarea class="form-control" name="message" rows="4" required placeholder="Escreva os detalhes com o que precisa de ajuda..."></textarea>
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" id="btnSubmitContact" class="btn btn-primary fw-bold px-5 py-2">
                                    <i class="bi bi-send me-2"></i> Enviar a Mensagem
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    async function submitForm(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnSubmitContact');
        const alertBox = document.getElementById('contactMsgContainer');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando Segurança...';
        alertBox.classList.add('d-none');
        alertBox.classList.remove('alert-danger', 'alert-success');

        try {
            const formData = new FormData(form);
            const { data } = await axios.post("{{ route('contact.store') }}", formData, {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            
            alertBox.className = 'alert alert-success mt-3';
            alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + data.message;
            form.reset();
        } catch(error) {
            let msg = 'Erro ao enviar. Verifique sua conexão.';
            if(error.response?.status === 429) {
                msg = 'Muitas requisições. Aguarde um minuto para enviar outra mensagem (Proteção Anti-Spam).';
            } else if(error.response?.data?.message) {
                msg = error.response.data.message;
            }
            
            alertBox.className = 'alert alert-danger mt-3';
            alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + msg;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send me-2"></i> Enviar a Mensagem';
        }
    }
</script>
@endpush
