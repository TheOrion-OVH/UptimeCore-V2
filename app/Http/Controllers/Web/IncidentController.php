<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::where('user_id', auth()->id())->latest()->get();
        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        return view('incidents.create');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la création
        return redirect()->route('incidents.index');
    }

    public function show(Incident $incident)
    {
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        // TODO: Implémenter la mise à jour
        return redirect()->route('incidents.index');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('incidents.index');
    }
}

