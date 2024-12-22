<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Location::all());
    }
}
