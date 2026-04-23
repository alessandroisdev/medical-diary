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

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 1. ÁREA ADMINISTRATIVA (Financeiro e RH Corporativo)
Route::middleware(['auth:admin'])->group(function () {
    // Financeiro
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/metrics', [TransactionController::class, 'metrics'])->name('transactions.metrics');
    
    // Gestão de RH e Acessos
    Route::resource('doctors', DoctorController::class)->except(['show']);
    Route::resource('collaborators', CollaboratorController::class)->except(['show']);
    
    // Gestão Estrutural (Especialidades e Convênios)
    Route::resource('specialties', \App\Http\Controllers\Admin\SpecialtyController::class)->except(['show']);
    Route::resource('health-insurances', \App\Http\Controllers\Admin\HealthInsuranceController::class)->except(['show']);
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

// 3. ÁREA ATENDENTE (Agendamentos e Painel de Chamada)
Route::middleware(['auth:collaborator'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    
    Route::get('/painel/control', [AttendanceCallController::class, 'panel'])->name('attendance.panel');
    Route::post('/appointments/{appointment}/call', [AttendanceCallController::class, 'callPatient'])->name('attendance.call');
});

// 4. ÁREA DO PACIENTE / CLIENTE
Route::middleware(['auth:client'])->group(function () {
    Route::get('/portal', function() {
        return view('portal');
    })->name('portal.index');
});

// TV DA RECEPÇÃO (Stream passivo público ou acessível por display TV local)
Route::get('/painel', [AttendanceCallController::class, 'panel'])->name('attendance.tv');
Route::get('/attendance/stream', [AttendanceCallController::class, 'stream'])->name('attendance.stream');


