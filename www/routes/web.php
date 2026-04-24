<?php

use App\Http\Controllers\DoctorScheduleController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AttendanceCallController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\CollaboratorController;

Route::get('/', [\App\Http\Controllers\PublicSiteController::class, 'home'])->name('home');
Route::get('/sobre', [\App\Http\Controllers\PublicSiteController::class, 'about'])->name('public.about');
Route::get('/especialidades', [\App\Http\Controllers\PublicSiteController::class, 'specialties'])->name('public.specialties');
Route::get('/equipe', [\App\Http\Controllers\PublicSiteController::class, 'doctors'])->name('public.doctors');
Route::get('/contato', [\App\Http\Controllers\PublicSiteController::class, 'contact'])->name('public.contact');

Route::post('/contato', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

// Autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 1. ÁREA ADMINISTRATIVA (Financeiro e RH Corporativo)
Route::middleware(['auth:admin'])->group(function () {
    // Financeiro
    Route::get('/transactions/metrics', [TransactionController::class, 'metrics'])->name('transactions.metrics');
    Route::resource('transactions', TransactionController::class)->except(['show']);
    
    // Gestão de RH e Acessos
    Route::resource('doctors', DoctorController::class)->except(['show']);
    Route::resource('collaborators', CollaboratorController::class)->except(['show']);
    
    // Gestão Estrutural (Especialidades e Convênios)
    Route::resource('specialties', \App\Http\Controllers\Admin\SpecialtyController::class)->except(['show']);
    Route::resource('health-insurances', \App\Http\Controllers\Admin\HealthInsuranceController::class)->except(['show']);

    // Configurações Globais
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // CRM Inbox do Site
    Route::get('/inbox', [\App\Http\Controllers\Admin\InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{id}', [\App\Http\Controllers\Admin\InboxController::class, 'show'])->name('inbox.show');
    Route::patch('/inbox/{id}/reply', [\App\Http\Controllers\Admin\InboxController::class, 'markReplied'])->name('inbox.reply');
    Route::delete('/inbox/{id}', [\App\Http\Controllers\Admin\InboxController::class, 'destroy'])->name('inbox.destroy');
});

// Acesso Misto (Admin + Atendente)
Route::middleware(['auth:admin,collaborator'])->group(function() {
    Route::get('/schedules', [DoctorScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules/{doctor}/toggle', [DoctorScheduleController::class, 'toggle'])->name('schedules.toggle');
});

// 2. ÁREA MÉDICA (Prontuários e Impressão)
Route::middleware(['auth:doctor'])->group(function () {
    Route::resource('records', MedicalRecordController::class)->except(['show', 'destroy']);
    Route::get('/prescriptions/{id}/print', [App\Http\Controllers\PrintRecordController::class, 'printDocument'])->name('print.document');
});

// 3. ÁREA ATENDENTE E MÉDICOS (Agendamentos e Painel de Chamada)
Route::middleware(['auth:collaborator,doctor'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/{appointment}/call', [AttendanceCallController::class, 'callPatient'])->name('attendance.call');
    Route::get('/painel/control', [AttendanceCallController::class, 'panel'])->name('attendance.panel');
});

Route::middleware(['auth:collaborator'])->group(function () {
    Route::resource('appointments', AppointmentController::class)->except(['index']);
    Route::post('/appointments/{appointment}/checkin', [AppointmentController::class, 'checkIn'])->name('appointments.checkin');
});

// 4. ÁREA DO PACIENTE / CLIENTE
Route::middleware(['auth:client'])->group(function () {
    Route::get('/portal', [\App\Http\Controllers\ClientPortalController::class, 'index'])->name('portal.index');
    Route::get('/portal/prescriptions/{id}/download', [\App\Http\Controllers\ClientPortalController::class, 'downloadPrescription'])->name('portal.prescription.download');
    Route::post('/portal/appointments/{id}/cancel', [\App\Http\Controllers\ClientPortalController::class, 'cancelAppointment'])->name('portal.appointment.cancel');
    
    // API Interna do Motor de Escalas via JS FETCH
    Route::get('/api/portal/doctors', [\App\Http\Controllers\ClientPortalController::class, 'getDoctors']);
    Route::get('/api/portal/slots', [\App\Http\Controllers\ClientPortalController::class, 'getSlots']);
    Route::get('/api/portal/payment-methods', [\App\Http\Controllers\ClientPortalController::class, 'getPaymentMethods']);
    Route::post('/api/portal/book', [\App\Http\Controllers\ClientPortalController::class, 'book']);
});

// TV DA RECEPÇÃO (Stream passivo público ou acessível por display TV local)
Route::view('/attendance', 'attendance.panel')->name('attendance.tv');
Route::get('/attendance/stream', [AttendanceCallController::class, 'stream'])->name('attendance.stream');


