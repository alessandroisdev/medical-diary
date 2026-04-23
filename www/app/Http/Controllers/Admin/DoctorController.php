<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\DataTables\Admin\DoctorDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function index(DoctorDataTable $dataTable)
    {
        return $dataTable->render('admin.doctors.index');
    }

    public function create()
    {
        $specialties = \App\Models\Specialty::orderBy('name')->get();
        return view('admin.doctors.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors',
            'crm' => 'required|string|max:50|unique:doctors',
            'consultation_duration_minutes' => 'required|integer|min:10|max:120',
            'specialties' => 'required|array',
            'specialties.*' => 'exists:specialties,id',
            'password' => 'required|string|min:6',
        ]);

        $doctor = Doctor::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'crm' => $data['crm'],
            'consultation_duration_minutes' => $data['consultation_duration_minutes'],
            'password' => Hash::make($data['password']),
        ]);

        $doctor->specialties()->sync($data['specialties']);

        return redirect()->route('doctors.index')->with('success', 'Médico criado! Lembre-se de configurar os Preços e Horários na Edição.');
    }

    public function edit(Doctor $doctor)
    {
        $specialties = \App\Models\Specialty::orderBy('name')->get();
        $insurances = \App\Models\HealthInsurance::where('is_active', true)->orderBy('name')->get();
        return view('admin.doctors.edit', compact('doctor', 'specialties', 'insurances'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors,email,'.$doctor->id,
            'crm' => 'required|string|max:50|unique:doctors,crm,'.$doctor->id,
            'consultation_duration_minutes' => 'required|integer|min:10|max:120',
            'specialties' => 'required|array',
            'specialties.*' => 'exists:specialties,id',
            'password' => 'nullable|string|min:6',
            
            // Validações dos Preços
            'price_particular' => 'nullable|numeric|min:0',
            'prices' => 'nullable|array',
            
            // Validações de Horários
            'availabilities' => 'nullable|array',
            'availabilities.*.day' => 'required|integer|min:0|max:6',
            'availabilities.*.specialty_id' => 'required|exists:specialties,id',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
        ]);

        $doctor->name = $data['name'];
        $doctor->email = $data['email'];
        $doctor->crm = $data['crm'];
        $doctor->consultation_duration_minutes = $data['consultation_duration_minutes'];

        if (!empty($data['password'])) {
            $doctor->password = Hash::make($data['password']);
        }

        $doctor->save();
        $doctor->specialties()->sync($data['specialties']);

        // Particular Price
        if (isset($data['price_particular']) && $data['price_particular'] !== '') {
            \App\Models\DoctorPrice::updateOrCreate(
                ['doctor_id' => $doctor->id, 'health_insurance_id' => null],
                ['price' => $data['price_particular']]
            );
        } else {
            \App\Models\DoctorPrice::where('doctor_id', $doctor->id)->whereNull('health_insurance_id')->delete();
        }

        // Insurances Price
        if (isset($data['prices']) && is_array($data['prices'])) {
            foreach ($data['prices'] as $insuranceId => $priceVal) {
                if ($priceVal !== null && $priceVal !== '') {
                    \App\Models\DoctorPrice::updateOrCreate(
                        ['doctor_id' => $doctor->id, 'health_insurance_id' => $insuranceId],
                        ['price' => $priceVal]
                    );
                } else {
                    \App\Models\DoctorPrice::where('doctor_id', $doctor->id)->where('health_insurance_id', $insuranceId)->delete();
                }
            }
        }

        // Availabilities (Recreation)
        $doctor->availabilities()->delete();
        if (isset($data['availabilities']) && is_array($data['availabilities'])) {
            foreach ($data['availabilities'] as $av) {
                if (isset($av['day']) && isset($av['specialty_id']) && isset($av['start_time']) && isset($av['end_time'])) {
                    $doctor->availabilities()->create([
                        'day_of_week' => $av['day'],
                        'specialty_id' => $av['specialty_id'],
                        'start_time' => $av['start_time'],
                        'end_time' => $av['end_time']
                    ]);
                }
            }
        }

        return redirect()->route('doctors.index')->with('success', 'Configurações de Agenda, Tabela de Preços e Perfil atualizados!');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Acesso clínico removido com sucesso.');
    }
}
