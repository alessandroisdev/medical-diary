import * as bootstrap from 'bootstrap';
import axios from 'axios';

// Configurando Axios Global
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

// Configuração global para Datatables que usam POST e precisam do CSRF
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window !== 'undefined' && (window as any).$) {
        (window as any).$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // Anti-double-click global e Interceptação AJAX Nativa de Forms
    // -------------------------------------------------------------
    const forms = document.querySelectorAll('form');
    
    forms.forEach((form) => {
        // Se a classe .no-ajax existir, ignorar
        if(form.classList.contains('no-ajax')) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // Impede submissão padrão

            const submitBtn = form.querySelector('[type="submit"]') as HTMLButtonElement | null;
            if (submitBtn) {
                // Impede clique duplo
                if (submitBtn.disabled) return;
                submitBtn.disabled = true;
                
                // Salvar o texto original no atributo para recuperar depois
                if (!submitBtn.hasAttribute('data-original-text')) {
                    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
                }
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguarde...';
            }

            try {
                const formData = new FormData(form as HTMLFormElement);
                const method = (form.getAttribute('method') || 'POST').toUpperCase();
                const url = form.getAttribute('action') || window.location.href;

                // Transforma FormData em Obj (pode usar Object.fromEntries) para requisições json se desejado
                const response = await axios({
                    method: method,
                    url: url,
                    data: formData,
                });

                // Mostra Toast de sucesso se houver mensagem
                if (response.data && response.data.message) {
                    showToast('Sucesso', response.data.message, 'success');
                }

                // Se a API exigir redirect, faz imediatamente
                if (response.data && response.data.redirect) {
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 500);
                } else {
                    form.reset();
                }
            } catch (error: any) {
                // Error handling via Axios
                const msg = error.response?.data?.message || 'Erro inesperado na requisição.';
                showToast('Erro', msg, 'danger');
            } finally {
                // Restaura o botão
                if (submitBtn) {
                    submitBtn.disabled = false;
                    const originalText = submitBtn.getAttribute('data-original-text');
                    if (originalText) {
                        submitBtn.innerHTML = originalText;
                    }
                }
            }
        });
    });

    // Função global para Toasts do Bootstrap
    function showToast(title: string, message: string, type: 'success' | 'danger' | 'info' | 'warning' = 'info') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            console.error('Toast container not found in DOM.');
            return;
        }

        const iconMap = {
            'success': 'bi-check-circle-fill text-success',
            'danger': 'bi-x-circle-fill text-danger',
            'info': 'bi-info-circle-fill text-info',
            'warning': 'bi-exclamation-triangle-fill text-warning',
        };

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-bg-light border-0 mb-2 shadow`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        toastEl.innerHTML = `
          <div class="toast-header">
            <i class="bi ${iconMap[type]} me-2"></i>
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            ${message}
          </div>
        `;
        
        toastContainer.appendChild(toastEl);
        
        const bToast = new bootstrap.Toast(toastEl, { delay: 5000 });
        bToast.show();

        // Limpar do DOM após fechar
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    // Exportar pro escopo global se necessário
    (window as any).showToast = showToast;
});
