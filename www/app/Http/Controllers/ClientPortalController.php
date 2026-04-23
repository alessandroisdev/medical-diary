<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\HealthInsurance;
use App\Models\DoctorSchedule;
use App\Models\DoctorAvailability;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use OpenApi\Attributes as OA;

class ClientPortalController extends Controller
{
    public function index()
    {
        $specialties = Specialty::orderBy('name')->get();
        return view('portal', compact('specialties'));
    }

    public function getDoctors(Request $request)
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id'
        ]);

        $specialtyId = $request->specialty_id;

        $doctors = Doctor::whereHas('specialties', function ($q) use ($specialtyId) {
            $q->where('specialties.id', $specialtyId);
        })->get(['id', 'name']);

        return response()->json($doctors);
    }

    public function getPaymentMethods(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id'
        ]);

        $doctorId = $request->doctor_id;
        
        $doctor = Doctor::with('prices.healthInsurance')->findOrFail($doctorId);
        $methods = [];

        foreach ($doctor->prices as $price) {
            if ($price->health_insurance_id === null) {
                $methods[] = [
                    'id' => 'particular',
                    'name' => 'Particular',
                    'price' => number_format($price->price, 2, ',', '.')
                ];
            } else {
                if ($price->healthInsurance && $price->healthInsurance->is_active) {
                    $methods[] = [
                        'id' => $price->health_insurance_id,
                        'name' => $price->healthInsurance->name,
                        'price' => $price->price > 0 ? number_format($price->price, 2, ',', '.') : 'Coberto pelo Plano'
                    ];
                }
            }
        }

        return response()->json($methods);
    }

    #[OA\Get(
        path: '/api/portal/slots',
        summary: 'Obtém Horários Disponíveis para um Médico via Motor Matemático',
        description: 'Exclui automaticamente bloqueios administrativos e conflitos.',
        responses: [
            new OA\Response(response: 200, description: 'Successful operation')
        ]
    )]
    public function getSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today'
        ]);

        $doctorId = $request->doctor_id;
        $specialtyId = $request->specialty_id;
        $dateStr = $request->date;
        $carbonDate = Carbon::parse($dateStr);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Sun) to 6 (Sat)

        // 1. Verificar bloqueios globais administrativos para o dia
        $block = DoctorSchedule::where('doctor_id', $doctorId)
                    ->where('date', $dateStr)
                    ->whereIn('status', ['cancelled', 'vacation'])
                    ->first();
        if ($block) {
            return response()->json(['slots' => []]);
        }

        // 2. Verificar regras de horário base cadastrado
        $availabilities = DoctorAvailability::where('doctor_id', $doctorId)
                            ->where('specialty_id', $specialtyId)
                            ->where('day_of_week', $dayOfWeek)
                            ->get();

        if ($availabilities->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        $doctor = Doctor::findOrFail($doctorId);
        $duration = $doctor->consultation_duration_minutes ?? 30;

        // 3. Buscar appointments desse médico no dia para remover do Grid
        $appointments = Appointment::where('doctor_id', $doctorId)
                            ->whereDate('scheduled_at', $dateStr)
                            ->whereNotIn('status', ['canceled', 'no_show'])
                            ->get()
                            ->map(function ($app) {
                                return Carbon::parse($app->scheduled_at)->format('H:i');
                            })->toArray();

        // 4. Matemática de Slots Magnéticos
        $availableSlots = [];
        $now = Carbon::now();

        foreach ($availabilities as $av) {
            $start = Carbon::parse($dateStr . ' ' . $av->start_time);
            $end = Carbon::parse($dateStr . ' ' . $av->end_time);

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $timeFormatted = $start->format('H:i');
                
                // Se a data solicitada for hoje, ignora horários que já passaram
                $isValidFutureTime = true;
                if ($carbonDate->isToday()) {
                    if ($start->lt($now)) {
                        $isValidFutureTime = false;
                    }
                }

                if ($isValidFutureTime && !in_array($timeFormatted, $appointments)) {
                    $availableSlots[] = $timeFormatted;
                }

                $start->addMinutes($duration);
            }
        }

        // Remover duplicatas e ordenar cronologicamente apenas por segurança técnica
        $availableSlots = array_values(array_unique($availableSlots));
        sort($availableSlots);

        return response()->json(['slots' => $availableSlots]);
    }

    public function book(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'specialty_id' => 'required|exists:specialties,id',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'payment_method' => 'required|string'
        ]);

        $clientId = Auth::guard('client')->id();
        $datetimeStr = $data['date'] . ' ' . $data['time'] . ':00';

        // Evita double-booking racial condition
        $exists = Appointment::where('doctor_id', $data['doctor_id'])
                             ->where('scheduled_at', $datetimeStr)
                             ->whereNotIn('status', ['canceled', 'no_show'])
                             ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Desculpe, esse horário acabou de ser reservado por outro paciente.'], 409);
        }

        // Criar appointment
        Appointment::create([
            'client_id' => $clientId,
            'doctor_id' => $data['doctor_id'],
            'scheduled_at' => $datetimeStr,
            'status' => 'scheduled',
            'consultation_type' => 'routine',
            // Podemos mapear 'payment_method' nas notas do sistema pro faturamento extrair
            'notes' => 'Via Self-Booking Portal. Pagamento: ' . $data['payment_method'],
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Sua consulta foi confirmada com sucesso! O sistema te aguarda no dia ' . Carbon::parse($data['date'])->format('d/m/Y') . ' às ' . $data['time'],
            'redirect' => route('portal.index')
        ]);
    }
}
