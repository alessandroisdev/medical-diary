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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'password' => 'required|string|min:6',
            'crm' => 'required|string|unique:doctors,crm',
            'specialty' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        Doctor::create($data);

        return response()->json(['message' => 'Médico cadastrado com sucesso!']);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('doctors')->ignore($doctor->id)],
            'crm' => ['required','string', Rule::unique('doctors')->ignore($doctor->id)],
            'specialty' => 'nullable|string',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $doctor->update($data);
        return response()->json(['message' => 'Ficha médica atualizada!']);
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return response()->json(['message' => 'Acesso clínico removido com sucesso.']);
    }
}
