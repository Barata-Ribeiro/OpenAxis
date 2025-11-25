<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function show()
    {
        return Inertia::render('dashboard');
    }
}
