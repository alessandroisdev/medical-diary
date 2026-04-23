<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\DataTables\Admin\CollaboratorDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CollaboratorController extends Controller
{
    public function index(CollaboratorDataTable $dataTable)
    {
        return $dataTable->render('admin.collaborators.index');
    }

    public function create()
    {
        return view('admin.collaborators.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:collaborators',
            'password' => 'required|string|min:6',
        ]);

        Collaborator::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('collaborators.index')->with('success', 'Atendente cadastrado e habilitado ao sistema.');
    }

    public function edit(Collaborator $collaborator)
    {
        return view('admin.collaborators.edit', compact('collaborator'));
    }

    public function update(Request $request, Collaborator $collaborator)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:collaborators,email,'.$collaborator->id,
            'password' => 'nullable|string|min:6',
        ]);

        $collaborator->name = $data['name'];
        $collaborator->email = $data['email'];

        if (!empty($data['password'])) {
            $collaborator->password = Hash::make($data['password']);
        }

        $collaborator->save();

        return redirect()->route('collaborators.index')->with('success', 'Credenciais atualizadas com sucesso!');
    }

    public function destroy(Collaborator $collaborator)
    {
        $collaborator->delete();
        return redirect()->route('collaborators.index')->with('success', 'Atendente destituído e bloqueado do balcão.');
    }
}
