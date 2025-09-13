<?php

namespace App\Http\Controllers;

use App\Models\AboutItem;
use Illuminate\Http\Request;

class AboutItemController extends Controller
{
    // Public: list all with nested children
    public function index()
    {
        // eager load nested relationships
        $items = AboutItem::with('subItems.contents.details')->latest()->get();
        return response()->json($items);
    }

    // Public: show single item with nested
    public function show(AboutItem $aboutItem)
    {
        $aboutItem->load('subItems.contents.details');
        return response()->json($aboutItem);
    }

    // Admin: create
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|url|max:2048',
        ]);

        $item = AboutItem::create($data);
        return response()->json($item, 201);
    }

    // Admin: update
    public function update(Request $request, AboutItem $aboutItem)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|url|max:2048',
        ]);

        $aboutItem->update($data);
        return response()->json($aboutItem);
    }

    // Admin: delete
    public function destroy(AboutItem $aboutItem)
    {
        $aboutItem->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
