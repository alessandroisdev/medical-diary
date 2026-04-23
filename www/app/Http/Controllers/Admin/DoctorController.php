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
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors',
            'crm' => 'required|string|max:50|unique:doctors',
            'specialty' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        Doctor::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'crm' => $data['crm'],
            'specialty' => $data['specialty'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('doctors.index')->with('success', 'Acesso Médico criado e ativado com sucesso!');
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:doctors,email,'.$doctor->id,
            'crm' => 'required|string|max:50|unique:doctors,crm,'.$doctor->id,
            'specialty' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $doctor->name = $data['name'];
        $doctor->email = $data['email'];
        $doctor->crm = $data['crm'];
        $doctor->specialty = $data['specialty'];

        if (!empty($data['password'])) {
            $doctor->password = Hash::make($data['password']);
        }

        $doctor->save();

        return redirect()->route('doctors.index')->with('success', 'Credenciais cadastrais atualizadas!');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Acesso clínico removido com sucesso.');
    }
}
