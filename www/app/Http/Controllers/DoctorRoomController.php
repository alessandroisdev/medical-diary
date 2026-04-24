<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorRoomController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'room' => 'nullable|string|max:100'
        ]);

        $doctor = auth()->guard('doctor')->user();
        if (!$doctor) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $doctor->update(['current_room' => $request->room]);

        return response()->json(['message' => 'Localização de atendimento atualizada com sucesso!', 'room' => $doctor->current_room]);
    }
}
