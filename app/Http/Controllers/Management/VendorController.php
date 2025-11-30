<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Services\Management\VendorService;

class VendorController extends Controller
{
    public function __construct(private VendorService $vendorService) {}
}
