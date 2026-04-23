<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::orderBy('name')->paginate(20);
        return view('admin.specialties.index', compact('specialties'));
    }

    public function create()
    {
        return view('admin.specialties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:specialties',
            'description' => 'nullable|string',
        ]);
        Specialty::create($data);
        return redirect()->route('specialties.index')->with('success', 'Especialidade cadastrada com sucesso!');
    }

    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,'.$specialty->id,
            'description' => 'nullable|string',
        ]);
        $specialty->update($data);
        return redirect()->route('specialties.index')->with('success', 'Especialidade atualizada com sucesso!');
    }

    public function destroy(Specialty $specialty)
    {
        $specialty->delete();
        return redirect()->route('specialties.index')->with('success', 'Especialidade removida!');
    }
}
