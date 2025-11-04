<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::where('user_id', auth()->id())->latest()->get();
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        return view('maintenances.create');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la création
        return redirect()->route('maintenances.index');
    }

    public function show(Maintenance $maintenance)
    {
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        return view('maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        // TODO: Implémenter la mise à jour
        return redirect()->route('maintenances.index');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenances.index');
    }
}

