<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $query = Service::with(['serviceType']);

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

        $services = $query->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => ServiceResource::collection($services)->resolve(),
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page'    => $services->lastPage(),
                'per_page'     => $services->perPage(),
                'total'        => $services->total(),
            ],
        ]);
    }

    // Get single service
    public function show($id)
    {
        $service = Service::with(['serviceType', 'user'])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json($service);
    }

    public function myServices(Request $request)
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
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $service = Service::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'location' => $data['location'],
            'phone' => $data['phone'],
            'service_type_id' => $user->service_type_id,
            'price' => $data['price'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'image' => $imagePath,
        ]);

        return response()->json($service, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $service = Service::findOrFail($id);

        // ✅ Ensure only the owner or admin can update
        if ($user->id !== $service->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // ✅ Validate only provided fields (partial updates allowed)
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'short_description' => ['sometimes', 'required', 'string'],
            'description' => ['sometimes', 'required', 'string'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,pending'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // ✅ Handle new image upload
        if ($request->hasFile('image')) {
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // ✅ Update and return fresh data
        $service->update($data);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service->fresh(), // ensures updated data is returned
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $service = Service::findOrFail($id);

        // Ensure only the owner or admin can delete
        if ($user->id !== $service->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Delete service image if exists
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
