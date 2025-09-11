<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // List all active services with optional filters
    public function index(Request $request)
    {
        $query = Service::with(['serviceType'])
            ->where('status', 'active');

        // Filter by service type
        if ($request->has('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $services = $query->latest()->get();
        return response()->json($services);
    }

    // Get single service
    public function show($id)
    {
        $service = Service::with(['serviceType', 'user'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json($service);
    }

    // List logged-in service provider's services
    public function myServices(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'service_provider') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $user->services()->with('serviceType')->latest()->get();
    }

    // Create new service
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->user_type !== 'service_provider') {
            return response()->json(['message' => 'Only service providers can add services'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'image' => ['nullable', 'string', 'url', 'max:2048'], // Accept image URL
        ]);

        $service = Service::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'phone' => $data['phone'],
            'service_type_id' => $user->service_type_id,
            'price' => $data['price'] ?? null,
            'status' => $data['status'] ?? 'active',
            'image' => $data['image'] ?? null,
        ]);

        return response()->json($service, 201);
    }

    // Update service
    public function update(Request $request, Service $service)
    {
        $user = $request->user();

        if ($user->id !== $service->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:active,inactive'],
            'image' => ['sometimes', 'nullable', 'string', 'url', 'active_url', 'max:2048'], // image URL for update
        ]);

        $service->update($data);
        $service->loadMissing('serviceType');

        return response()->json($service);
    }

    // Delete service
    public function destroy(Request $request, Service $service)
    {
        $user = $request->user();

        if ($user->id !== $service->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted']);
    }
}
