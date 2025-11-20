<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AddressRequest;
use Auth;
use Inertia\Inertia;

class AddressController extends Controller
{
    /**
     * Show the user's addresses settings page.
     */
    public function index()
    {
        return Inertia::render('settings/addresses', [
            'addresses' => Auth::user()->addresses,
        ]);
    }

    /**
     * Store a new address for the user.
     */
    public function store(AddressRequest $request)
    {
        $request->all();
    }
}
