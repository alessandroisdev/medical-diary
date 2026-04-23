<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Doctor;
use App\Support\DataTables\AppointmentDataTable;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(AppointmentDataTable $dataTable)
    {
        $clients = Client::all();
        $doctors = Doctor::all();
        return $dataTable->render('appointments.index', compact('clients', 'doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'consultation_type' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        Appointment::create($data);

        return response()->json([
            'message' => 'Agendamento criado com sucesso!',
        ]);
    }
}
