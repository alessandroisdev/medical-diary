<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrintRecordController extends Controller
{
    /**
     * Imprimir um Prontuário Específico.
     * Pode ou não trazer junto os dados da receita gerada no mesmo ato.
     */
    public function printDocument($record_id)
    {
        $record = MedicalRecord::with(['client', 'doctor'])->findOrFail($record_id);
        
        $prescription = Prescription::where('medical_record_id', $record->id)->first();

        return view('print.record', compact('record', 'prescription'));
    }
}
