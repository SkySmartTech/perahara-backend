<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'service_provider') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $user->services()->with('serviceType')->latest()->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'service_provider') {
            return response()->json(['message' => 'Only service providers can add services'], 403);
        }

        $data = $request->validate([
            'service_type_id' => ['required','exists:service_types,id'],
            'service_name'    => ['required','string','max:255'],
            'description'     => ['nullable','string'],
            'price'           => ['nullable','numeric','min:0'],
        ]);

        $service = Service::create([
            'user_id'        => $user->id,
            'service_type_id'=> $data['service_type_id'],
            'service_name'   => $data['service_name'],
            'description'    => $data['description'] ?? null,
            'price'          => $data['price'] ?? null,
        ]);

        return response()->json($service, 201);
    }
}
