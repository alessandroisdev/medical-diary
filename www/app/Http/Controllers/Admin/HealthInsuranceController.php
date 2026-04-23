<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthInsurance;
use Illuminate\Http\Request;

class HealthInsuranceController extends Controller
{
    public function index()
    {
        $insurances = HealthInsurance::orderBy('name')->paginate(20);
        return view('admin.health-insurances.index', compact('insurances'));
    }

    public function create()
    {
        return view('admin.health-insurances.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:health_insurances',
            'ans_code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        HealthInsurance::create($data);
        return redirect()->route('health-insurances.index')->with('success', 'Convênio cadastrado com sucesso!');
    }

    public function edit(HealthInsurance $healthInsurance)
    {
        return view('admin.health-insurances.edit', compact('healthInsurance'));
    }

    public function update(Request $request, HealthInsurance $healthInsurance)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:health_insurances,name,'.$healthInsurance->id,
            'ans_code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        $healthInsurance->update($data);
        return redirect()->route('health-insurances.index')->with('success', 'Convênio atualizado com sucesso!');
    }

    public function destroy(HealthInsurance $healthInsurance)
    {
        $healthInsurance->delete();
        return redirect()->route('health-insurances.index')->with('success', 'Convênio removido do sistema!');
    }
}
