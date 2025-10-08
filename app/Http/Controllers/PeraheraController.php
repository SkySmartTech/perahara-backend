<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Perahera;
use Illuminate\Http\Request;
use App\Http\Resources\PeraheraResource;

class PeraheraController extends Controller
{
    // 1️⃣ Display all peraheras (public) with pagination
public function index(Request $request)
{
    $perPage = $request->input('per_page', 20);
    $today = Carbon::today();

    $peraheras = Perahera::with('user')
        ->where(function ($query) use ($today) {
            $query->whereNull('end_date')
                  ->whereDate('start_date', '>=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereNotNull('end_date')
                        ->whereDate('end_date', '>=', $today);
                  });
        })
        ->orderBy('start_date', 'asc')
        ->paginate($perPage);

    return response()->json([
        'data' => PeraheraResource::collection($peraheras)->resolve(),
        'meta' => [
            'current_page' => $peraheras->currentPage(),
            'last_page'    => $peraheras->lastPage(),
            'per_page'     => $peraheras->perPage(),
            'total'        => $peraheras->total(),
        ],
    ]);
}

    // 2️⃣ Display logged-in organizer's peraheras (dashboard) without pagination
    public function indexUser(Request $request)
    {
        $user = $request->user();

        $query = Perahera::with('user')->orderBy('start_date', 'asc');

        if ($user && $user->user_type === 'organizer') {
            $query->where('user_id', $user->id);
        }

        $peraheras = $query->get(); // fetch all peraheras for this user

        return response()->json([
            'data' => PeraheraResource::collection($peraheras)->resolve(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->user_type, ['admin', 'organizer'])) {
            return response()->json(['message' => 'Only admin or organizer can add Peraheras'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'event_time' => ['required', 'date_format:H:i'],
            'status' => ['nullable', 'string', 'in:active,inactive,pending'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle image upload safely
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('perahera_images', 'public');
        }

        $perahera = Perahera::create([
            'user_id'           => $user->id,
            'name'              => $data['name'],
            'short_description' => $data['short_description'] ?? null,
            'description'       => $data['description'] ?? null,
            'location'          => $data['location'],
            'start_date'        => $data['start_date'],
            'end_date'          => $data['end_date'] ?? null,
            'event_time'        => $data['event_time'],
            'status'            => $data['status'] ?? 'pending',
            'image'             => $imagePath,
        ]);
        
        return response()->json($perahera, 201);
    }

    public function show(Perahera $perahera)
    {
        return $perahera->load('user');
        
    }

    public function update(Request $request, Perahera $perahera)
    {
        $user = $request->user();

        // Authorization: only admin or organizer
        if (!in_array($user->user_type, ['admin', 'organizer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Organizer can only update their own events
        if ($user->user_type === 'organizer' && $perahera->user_id !== $user->id) {
            return response()->json(['message' => 'You can only update your own events'], 403);
        }

        // Validate incoming data
        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'start_date'  => ['sometimes', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'location'    => ['sometimes', 'string', 'max:255'],
            'status'      => ['sometimes', 'in:active,inactive,pending'],
            'image'       => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $request->file('image')->store('perahera_images', 'public');
        }

        // Update the Perahera
        $perahera->update($data);

        // Return updated resource with user relationship
        return new PeraheraResource($perahera->loadMissing('user'));
    }

    public function destroy(Request $request, Perahera $perahera)
    {
        $user = $request->user();

        if (!in_array($user->user_type, ['admin', 'organizer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->user_type === 'organizer' && $perahera->user_id !== $user->id) {
            return response()->json(['message' => 'You can only delete your own events'], 403);
        }

        $perahera->delete();

        return response()->json(['message' => 'Perahera deleted']);
    }
}
