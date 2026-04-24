<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ContactMessage;

class InboxController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.inbox.index', compact('messages'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        if ($message->status === 'pending') {
            $message->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return view('admin.inbox.show', compact('message'));
    }

    public function markReplied($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => 'replied']);
        return redirect()->route('inbox.index')->with('success', 'Mensagem arquivada como Respondida!');
    }

    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();
        return redirect()->route('inbox.index')->with('success', 'Mensagem deletada (Lixeira).');
    }
}
