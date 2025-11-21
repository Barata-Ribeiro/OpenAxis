<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AddressRequest;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

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
        $data = $request->validated();

        $addresses = Auth::user()->addresses();

        try {
            if ($addresses->count() >= 5) {
                return back()->withInput()->with('warning', 'You can only have up to 5 addresses.');
            }

            $isPrimary = isset($data['is_primary']) ? filter_var($data['is_primary'], FILTER_VALIDATE_BOOLEAN) : false;
            $data['is_primary'] = $isPrimary ? 1 : 0;

            if ($data['is_primary']) {
                $addresses->update(['is_primary' => 0]);
            }

            $addresses->create($data);

            return to_route('profile.addresses')->with('success', 'Address added successfully.');
        } catch (Exception $e) {
            Log::error('Address: Failed to add address', ['action_user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Failed to add address. Please try again.');
        }
    }
}
