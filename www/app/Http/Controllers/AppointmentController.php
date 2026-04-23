<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Support\DataTables\AppointmentDataTable;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(AppointmentDataTable $dataTable)
    {
        return $dataTable->render('appointments.index');
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        // Carrega também a especialidade ligada para o drop
        $doctors = Doctor::with('specialties')->orderBy('name')->get();
        $specialties = Specialty::orderBy('name')->get();
        
        return view('appointments.create', compact('clients', 'doctors', 'specialties'));
    }

    public function store(Request $request)
    {
        // Aceita Overbooking e datas em qualquer formato
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'consultation_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $datetimeStr = $data['date'] . ' ' . $data['time'] . ':00';

        Appointment::create([
            'client_id' => $data['client_id'],
            'doctor_id' => $data['doctor_id'],
            'scheduled_at' => $datetimeStr,
            'status' => 'scheduled',
            'consultation_type' => $data['consultation_type'],
            'notes' => $data['notes']
        ]);

        return redirect()->route('appointments.index')->with('success', 'Agendamento/Encaixe criado com sucesso!');
    }

    public function edit(Appointment $appointment)
    {
        $clients = Client::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'clients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled,confirmed,arrived,in_consultation,finished,canceled,no_show',
            'consultation_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $datetimeStr = $data['date'] . ' ' . $data['time'] . ':00';

        $appointment->update([
            'scheduled_at' => $datetimeStr,
            'status' => $data['status'],
            'consultation_type' => $data['consultation_type'],
            'notes' => $data['notes']
        ]);

        return redirect()->route('appointments.index')->with('success', 'Dados da Consulta atualizados com sucesso!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Agendamento deletado.');
    }

    /**
     * Ação Rápida de Balcão (Recepção)
     */
    public function checkIn(Appointment $appointment)
    {
        $appointment->update(['status' => 'arrived']);
        return redirect()->route('appointments.index')->with('success', 'Check-in realizado! Status atualizado para: Aguardando em Sala.');
    }
}
