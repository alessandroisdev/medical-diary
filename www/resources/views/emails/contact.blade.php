<x-mail::message>
# Nova Mensagem de Contato

Você recebeu uma nova requisição vinda do formulário institucional, os detalhes seguem abaixo:

**Remetente:** {{ $msg->name }}  
**E-mail:** [{{ $msg->email }}](mailto:{{ $msg->email }})  
**Telefone/WhatsApp:** {{ $msg->phone ?? 'Não preenchido' }}  

<x-mail::panel>
**Assunto:** {{ $msg->subject }}  

{{ $msg->message }}
</x-mail::panel>

<x-mail::button :url="config('app.url') . '/admin/inbox'">
Abrir Inbox para Responder
</x-mail::button>

Essa mensagem foi gerada automaticamente pelo Sistema de Triagem.
<br>
Equipe Administrativa, {{ config('app.name') }}
</x-mail::message>
