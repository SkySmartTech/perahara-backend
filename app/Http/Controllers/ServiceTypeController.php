<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
    public function types()
    {
        $types = ServiceType::all();
        return response()->json($types);
    }
}
