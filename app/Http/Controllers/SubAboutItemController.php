<?php

namespace App\Http\Controllers;

use App\Models\SubAboutItem;
use Illuminate\Http\Request;

class SubAboutItemController extends Controller
{
    // Public - list all
    public function index()
    {
        return response()->json(SubAboutItem::with('aboutItem')->latest()->get());
    }

    // Public - single item
    public function show($id)
    {
        $item = SubAboutItem::with('aboutItem')->findOrFail($id);
        return response()->json($item);
    }

    // Admin - create
    public function store(Request $request)
    {
        $data = $request->validate([
            'about_item_id' => 'required|exists:about_items,id',
            'title' => 'required|string|max:255',
        ]);

        $item = SubAboutItem::create($data);
        return response()->json($item, 201);
    }

    // Admin - update
    public function update(Request $request, SubAboutItem $subAboutItem)
    {
        $data = $request->validate([
            'about_item_id' => 'sometimes|exists:about_items,id',
            'title' => 'sometimes|string|max:255',
        ]);

        $subAboutItem->update($data);
        return response()->json($subAboutItem);
    }

    // Admin - delete
    public function destroy(SubAboutItem $subAboutItem)
    {
        $subAboutItem->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
