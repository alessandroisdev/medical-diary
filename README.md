# Medical Diary

Sistema SAAS completo de Agendamento e Prontuário Médico.

## Arquitetura
- **Plataforma:** Laravel 11.x (pre-13.x adaptado ao ecosystem) rodando sobre PHP 8.4/8.5 FPM.
- **Frontend:** Bootstrap 5, Typescript Vanilla (AJAX puro + anti-double click protection + toasts).
- **Banco de Dados:** MariaDB e Redis para controle de filas, SS-Caching, e Session.
- **Docker:** Ambiente isolado em `/.Docker`. Mapeamento de portas na `8084` em `./www`.

## Padrões Adotados (Evolução Full-Page Anti-Modal)
O projeto recentemente passou por uma modernização arquitetural massiva abandonando engessamentos Modais JS-based em prol de rotas **Full-Page Resources**:
1. **Autenticação Multi-Nível:** Guards Isolados (`admin`, `doctors`, `collaborators`, `clients`) com Login interface unificado em `/login` redirecionando para views totalmente modularizadas (`Specialties`, `HealthInsurances`, `Doctors`, etc).
2. **Datatables Server-Side:** Exclusivamente via método `POST` implementando `App\Support\DataTables\AbstractDataTable` para segurança JSON, agora integrados perfeitamente à botões de gatilhos Full Page (Create/Edit) e Botões Nativos de Status "Check-In".
3. **Totem de Hardware (Autoatendimento) e Módulo de Senhas:** Construção de um serviço isolado (`/totem`) em Node.js projetado para rodar nativamente em balcão/quiosque que intercepta spoolers térmicos e recusa interações duplas de botão enviando chamadas POST transparentes direto ao Docker Backend (API de Senhas). 
4. **TV de Chamada Flexível (Real-Time)**: Implementação robusta de Server-Sent Events (`SSE`) em PHP substituindo websockets. Intercepta dinamicamente Chamadas Manuais de Consultório com Nomes Próprios E Chamadas Matemáticas de Fila do Guichê através de `1-click caller`. Modula vozes TTS sintetizadas para soletrar senhas à idosos com cores de alerta dinâmicas (Warning Yellow para Prioridades).
5. **Motor Matemático de Agendamentos (Self-Booking)**: Portal inteligente do paciente construído em Javascript assíncrono. Subtrai da agenda matriz (`doctor_availabilities`) os dias feriados logísticos (`DoctorSchedules`) e cria botões cirúrgicos do tempo ocioso para a compra/agendamento de consultas sem risco de Conflitos (Overbooking Nativo é bloqueado via API, mas liberado para a Recepção Local através de Override manual).
6. **Tracking & LGPD Contábil:** Modelos sensíveis como Entidades Administrativas e Transacionais (`Transaction`) mantêm controle com `SoftDeletes` impedindo que apagamentos manuais rompam integrações com o Livro-Caixa. Contabilidade é unificada via TomSelect.
7. **Emissão de Prontuários (A4 CSS):** Impressão de prontuários com hash dinâmico via gerador de layout CSS dedicado pra papeis físicos isolando layouts digitais.

## Executando o Projeto
```bash
# 1. Subir container de banco e nginx
docker-compose -f .Docker/docker-compose.yml up -d

# 2. Instalar Depedências
docker exec medical_diary_app composer install
docker exec medical_diary_app npm install
docker exec medical_diary_app npm run build

# 3. Rodar as migrações e sementes iniciais
docker exec medical_diary_app php artisan migrate:fresh --seed
```

### Perfis de Teste (Seeds)
O comando de seed gera massa de dados e cria usuários curingas fixos, com a senha universal `password` para todos:
- **Admin**: `admin@medical.diary` na aba "Admin"
- **Médico**: `doctor@medical.diary` na aba "Médico"
- **Atendente**: `collaborator@medical.diary` na aba "Recepção"
- **Paciente**: `client@medical.diary` na aba "Paciente"

## Mapa de URLs (Ecossistema Público e Privado)

Ao subir os containers no Docker (`localhost:8084`), o projeto fragmenta-se nesses macro-sistemas acessíveis navegando via URL:

| Aplicação | Rota HTTP / Path | Descrição do Módulo | Autenticação |
| :--- | :--- | :--- | :--- |
| **Site Institucional (CMS)** | `/` | Portal público (Home, Equipe, Especialidades, Infra). Baseado em CMS Dinâmico injetado via painel. | Nenhuma |
| **Página de Contato Segura** | `/contato` | Formulário seguro contra DDoS que envia e-mails em fila *ShouldQueue* para a clínica. | Nenhuma |
| **Área Privada (Login Universal)** | `/login` | Porta de entrada mágica inteligente. Após o login, redireciona de acordo com o guard (Admin vs Doctor vs Client). | Múltiplas |
| **Painel de Atendimento Total (TV)** | `/attendance` | Display estético Full HD focado para televisores na recepção do prédio. Atualiza via Server Sent Events (`SSE`) quando os médicos ou a Módulo da Fila chamam alguém no painel. | Nenhuma |
| **Microserviço: TV Totem Hardware** | `(Acesso: :8085)` | Frontend Kiosk UI de alto impacto visual (Glassmorphism e Neumorphism) atrelado diretamente ao Spooler C++ de Impressão Térmica via Node.JS puro. Hospedado na pasta raiz `/totem`. | Nenhuma |
| **Estação Guichê (Senhas)** | `/reception/queue` | Motor de ordenamento logístico. Avalia matematicamente filas comuns e prioritárias e empurra na métrica correta com 1-click caller. | `auth:collaborator` |
| **Agenda Inteligente (Self-Booking)** | `(Logado como Cliente)` | Sistema vivo onde o paciente subtrai agendas e compra um horário cirurgicamente bloqueado pelo motor Matemático. | `auth:client` |
| **CMS Global Settings** | `(Logado como Admin)` | Módulo responsável por editar métricas da fila (Ticket Ratio) ou editar SMTP dinâmico até trocar Textos e Logísticas do Site. | `auth:admin` |
| **CRM de SAC (Inbox)** | `(Dentro do Admin)` | Fila administrativa que guarda as threads de formulários enviados da HomePage, podendo deletar organicamente por UUID. | `auth:admin` |

*OBS: O Arquivo Documental `openapi.yaml` atrelado neste repositório descreve alguns os Endpoints mais técnicos e complexos da plataforma (Incluindo API da TV).*
