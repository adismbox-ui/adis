<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantDocumentsController extends Controller
{
    public function create()
    {
        return view('assistant.sessions.create');
    }

    public function show($id)
    {
        $document = \App\Models\Document::with(['auteur'])->findOrFail($id);
        return view('assistants.documents.show', compact('document'));
    }

    public function edit($id)
    {
        $document = \App\Models\Document::with(['auteur'])->findOrFail($id);
        return view('assistants.documents.edit', compact('document'));
    }

    public function destroy($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        $document->delete();
        return redirect()->route('assistant.documents')->with('success', 'Document supprimé avec succès.');
    }
}
