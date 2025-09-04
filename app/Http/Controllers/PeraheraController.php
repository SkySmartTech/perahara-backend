<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Perahera;
use Illuminate\Http\Request;
use App\Http\Resources\PeraheraResource;

class PeraheraController extends Controller
{
    public function index()
    {
        // anyone can view list of upcoming peraheras
        $peraheras = Perahera::with('user')
            ->where('status', 'active')
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->paginate(20);

        return PeraheraResource::collection($peraheras);
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
            'start_date' => [
                'required','date',
                function ($attribute, $value, $fail) use ($request) {
                    if (Carbon::parse($value)->lt(Carbon::today())) {
                        $fail("The $attribute must be today or a future date.");
                    }
                    $endDate = $request->input('end_date');
                    if ($endDate && Carbon::parse($value)->gt(Carbon::parse($endDate))) {
                        $fail("The $attribute must be before or equal to the end date.");
                    }
                },
            ],
            'end_date' => [
                'required','date',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = $request->input('start_date');
                    if ($startDate && Carbon::parse($value)->lt(Carbon::parse($startDate))) {
                        $fail("The $attribute must be after or equal to the start date.");
                    }
                },
            ],
            'image'       => ['nullable','string'], // can switch to file upload later
            'location'    => ['required','string','max:255'],
            'status'      => ['sometimes','in:active,inactive,cancelled'],
        ]);

        $perahera = Perahera::create(array_merge([
            'user_id' => $user->id,
        ], $data));

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
            'description' => ['sometimes', 'nullable','string'],
            'start_date' => [
                'sometimes','date',
                function ($attribute, $value, $fail) use ($request, $perahera) {
                    $today = Carbon::today();
                    $startDate = Carbon::parse($value);

                    if ($startDate->lt($today)) {
                        $fail("The $attribute must be today or a future date.");
                    }

                    $endDate = $request->input('end_date', $perahera->end_date);
                    if ($endDate && $startDate->gt(Carbon::parse($endDate))) {
                        $fail("The $attribute must be before or equal to the end date.");
                    }
                },
            ],

            'end_date' => [
                'sometimes','date',
                function ($attribute, $value, $fail) use ($request, $perahera) {
                    $endDate = Carbon::parse($value);
                    $startDate = $request->input('start_date', $perahera->start_date);

                    if ($startDate && $endDate->lt(Carbon::parse($startDate))) {
                        $fail("The $attribute must be after or equal to the start date.");
                    }
                },
            ],
            'image'       => ['sometimes', 'nullable','string'],
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
