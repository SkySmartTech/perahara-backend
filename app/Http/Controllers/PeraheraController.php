<?php

namespace App\Http\Controllers;

use App\Models\Perahera;
use Illuminate\Http\Request;

class PeraheraController extends Controller
{
    public function index()
    {
        // anyone can view list of upcoming peraheras
        return Perahera::with('user')->latest()->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->user_type, ['admin', 'organizer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_date'  => ['required','date'],
            'end_date'    => ['required','date','after_or_equal:start_date'],
            'image'       => ['nullable','string'], // can switch to file upload later
            'location'    => ['required','string','max:255'],
            'status'      => ['required','in:active,inactive,cancelled'],
        ]);

        $perahera = Perahera::create([
            'user_id'     => $user->id,
            ...$data
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

        if (!in_array($user->user_type, ['admin', 'organizer'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // optional: ensure only the organizer or admin can edit
        if ($user->user_type === 'organizer' && $perahera->user_id !== $user->id) {
            return response()->json(['message' => 'You can only update your own events'], 403);
        }

        $data = $request->validate([
            'name'        => ['sometimes','string','max:255'],
            'description' => ['nullable','string'],
            'start_date'  => ['sometimes','date'],
            'end_date'    => ['sometimes','date','after_or_equal:start_date'],
            'image'       => ['nullable','string'],
            'location'    => ['sometimes','string','max:255'],
            'status'      => ['sometimes','in:active,inactive,cancelled'],
        ]);

        $perahera->update($data);

        return response()->json($perahera);
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
