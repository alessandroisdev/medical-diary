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
3. **Totem e TV de Chamada (Real-Time)**: Implementação de Server-Sent Events (`SSE`) que permite "Chamada de Senha", tocando alertas na Fila Visual de maneira orgânica em PHP sem a sobrecarga de WebSockets Node.js, com fluxo atritivo entre Recepcionista x Médicos.
4. **Motor Matemático de Agendamentos (Self-Booking)**: Portal inteligente do paciente construído em Javascript assíncrono. Subtrai da agenda matriz (`doctor_availabilities`) os dias feriados logísticos (`DoctorSchedules`) e cria botões cirúrgicos do tempo ocioso para a compra/agendamento de consultas sem risco de Conflitos (Overbooking Nativo é bloqueado via API, mas liberado para a Recepção Local através de Override manual).
5. **Tracking & LGPD Contábil:** Modelos sensíveis como Entidades Administrativas e Transacionais (`Transaction`) mantêm controle com `SoftDeletes` impedindo que apagamentos manuais rompam integrações com o Livro-Caixa. Contabilidade é unificada via TomSelect.
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
