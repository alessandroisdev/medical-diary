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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:collaborators,email',
            'password' => 'required|string|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);
        Collaborator::create($data);

        return response()->json(['message' => 'Atendente cadastrado com sucesso!']);
    }

    public function update(Request $request, Collaborator $collaborator)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('collaborators')->ignore($collaborator->id)],
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $collaborator->update($data);
        return response()->json(['message' => 'Acesso atualizado!']);
    }

    public function destroy(Collaborator $collaborator)
    {
        $collaborator->delete();
        return response()->json(['message' => 'Atendente destituído com sucesso.']);
    }
}
