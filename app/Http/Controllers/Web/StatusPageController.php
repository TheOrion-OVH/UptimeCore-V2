<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StatusPage;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function index()
    {
        $statusPage = StatusPage::where('user_id', auth()->id())->first();
        return view('status-page.index', compact('statusPage'));
    }
}

