<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::all();
        $date = $request->get('date', now()->format('Y-m-d'));
        
        // Carrega bloqueios de dias
        $schedules = DoctorSchedule::with('doctor')
                        ->where('date', $date)
                        ->get()
                        ->keyBy('doctor_id');

        return view('schedules.index', compact('doctors', 'date', 'schedules'));
    }

    public function toggle(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:active,cancelled,vacation',
            'reason' => 'nullable|string'
        ]);

        DoctorSchedule::updateOrCreate(
            ['doctor_id' => $doctor->id, 'date' => $data['date']],
            ['status' => $data['status'], 'reason' => $data['reason']]
        );

        return response()->json(['message' => 'Status da agenda atualizado para a data informada!']);
    }
}
