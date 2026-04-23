<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Client;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Support\DataTables\MedicalRecordDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicalRecordController extends Controller
{
    public function index(MedicalRecordDataTable $dataTable)
    {
        return $dataTable->render('records.index');
    }

    public function create()
    {
        $clients = Client::all();
        $doctors = Doctor::all();
        $appointments = Appointment::where('status', 'in_consultation')->get();
        return view('records.create', compact('clients', 'doctors', 'appointments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'notes' => 'nullable|string',
            
            // Prescription parts via in-form toggle
            'has_prescription' => 'boolean',
            'medicines' => 'nullable|array',
            'instructions' => 'nullable|string',
        ]);

        \DB::transaction(function() use ($data) {
            $record = MedicalRecord::create([
                'client_id' => $data['client_id'],
                'doctor_id' => $data['doctor_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
                'symptoms' => $data['symptoms'],
                'diagnosis' => $data['diagnosis'],
                'treatment_plan' => $data['treatment_plan'],
                'notes' => $data['notes'],
            ]);

            if (!empty($data['has_prescription']) && $data['has_prescription']) {
                Prescription::create([
                    'client_id' => $data['client_id'],
                    'doctor_id' => $data['doctor_id'],
                    'medical_record_id' => $record->id,
                    'medicines' => $data['medicines'],
                    'instructions' => $data['instructions'],
                    'valid_until' => now()->addDays(30),
                    // Hash simplificado de assinatura para validador impresso
                    'signature_hash' => Str::upper(Str::random(12)), 
                ]);
            }
            
            // Marca agendamento como finalizado se existir
            if (!empty($data['appointment_id'])) {
                Appointment::find($data['appointment_id'])->update(['status' => 'finished']);
            }
        });

        return response()->json([
            'message' => 'Prontuário Médico assinado e salvo com sucesso!',
            'redirect' => route('records.index')
        ]);
    }

    public function edit(MedicalRecord $record)
    {
        $clients = Client::all();
        $doctors = Doctor::all();
        $appointments = Appointment::where('status', 'in_consultation')->get();
        return view('records.edit', compact('record', 'clients', 'doctors', 'appointments'));
    }

    public function update(Request $request, MedicalRecord $record)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'required|string',
            'treatment_plan' => 'required|string',
            'notes' => 'nullable|string',
            
            // Prescription não é atualizada neste endpoint direto pra n gerar fraude
        ]);

        $record->update([
            'client_id' => $data['client_id'],
            'doctor_id' => $data['doctor_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'symptoms' => $data['symptoms'],
            'diagnosis' => $data['diagnosis'],
            'treatment_plan' => $data['treatment_plan'],
            'notes' => $data['notes'],
        ]);

        return response()->json([
            'message' => 'Evolução clínica do prontuário atualizada com sucesso!',
            'redirect' => route('records.index')
        ]);
    }
}
