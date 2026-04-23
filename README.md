# Medical Diary

Sistema SAAS completo de Agendamento e Prontuário Médico.

## Arquitetura
- **Plataforma:** Laravel 11.x (pre-13.x adaptado ao ecosystem) rodando sobre PHP 8.4/8.5 FPM.
- **Frontend:** Bootstrap 5, Typescript Vanilla (AJAX puro + anti-double click protection + toasts).
- **Banco de Dados:** MariaDB e Redis para controle de filas, SS-Caching, e Session.
- **Docker:** Ambiente isolado em `/.Docker`. Mapeamento de portas na `8084` em `./www`.

## Padrões Adotados (Fundamental)
1. **Autenticação Multi-Nível:** Guards Isolados (`admin`, `doctors`, `collaborators`, `clients`) com Login interface unificado em `/login` mas redirecionamento em silo, todos sob o middleware `auth:*`.
2. **Datatables Server-Side:** Exclusivamente via método `POST` implementando `App\Support\DataTables\AbstractDataTable` para segurança JSON.
3. **Tracking & LGPD:** Todas as entidades possuem ID randômico mascarado (`UsesUuid`), mantendo controle estrito com `SoftDeletes` e auditoria minuciosa `\OwenIt\Auditing\Contracts\Auditable`.
4. **Resiliência AJAX:** Todas as requisições Frontend são interceptadas por um arquivo raiz TS (`app.ts`), anulando cliques errôneos e parseando FormDatas limpos.
5. **Gateway de Notificações Multi-Channel:** Configurado sob `AppointmentReminderNotification` preparado para despachar avisos via Mail, Redis Database, e estrutura reservada nativa para SMS e WhatsApp API.
6. **Emissão de Prontuários (A4 CSS):** Impressão de prontuários com hash dinâmico via gerador de layout CSS dedicado pra papeis físicos isolando layouts digitais.

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
