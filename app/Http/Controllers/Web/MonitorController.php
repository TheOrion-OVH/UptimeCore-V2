<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        $monitors = Monitor::where('user_id', auth()->id())->get();
        return view('monitors.index', compact('monitors'));
    }

    public function create()
    {
        return view('monitors.create');
    }

    public function store(Request $request)
    {
        // TODO: Implémenter la création
        return redirect()->route('monitors.index');
    }

    public function show(Monitor $monitor)
    {
        return view('monitors.show', compact('monitor'));
    }

    public function edit(Monitor $monitor)
    {
        return view('monitors.edit', compact('monitor'));
    }

    public function update(Request $request, Monitor $monitor)
    {
        // TODO: Implémenter la mise à jour
        return redirect()->route('monitors.index');
    }

    public function destroy(Monitor $monitor)
    {
        $monitor->delete();
        return redirect()->route('monitors.index');
    }
}

