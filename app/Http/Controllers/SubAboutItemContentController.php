<?php

namespace App\Http\Controllers;

use App\Models\SubAboutItemContent;
use Illuminate\Http\Request;

class SubAboutItemContentController extends Controller
{
    // Public - list all
    public function index()
    {
        return response()->json(SubAboutItemContent::with('subAboutItem')->latest()->get());
    }

    // Public - single
    public function show($id)
    {
        $content = SubAboutItemContent::with('subAboutItem')->findOrFail($id);
        return response()->json($content);
    }

    // Admin - create
    public function store(Request $request)
    {
        $data = $request->validate([
            'sub_about_item_id' => 'required|exists:sub_about_items,id',
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'image' => 'nullable|string|url|max:2048',
        ]);

        $content = SubAboutItemContent::create($data);
        return response()->json($content, 201);
    }

    // Admin - update
    public function update(Request $request, SubAboutItemContent $subAboutItemContent)
    {
        $data = $request->validate([
            'sub_about_item_id' => 'sometimes|exists:sub_about_items,id',
            'title' => 'sometimes|string|max:255',
            'short_description' => 'sometimes|nullable|string',
            'image' => 'sometimes|nullable|string|url|max:2048',
        ]);

        $subAboutItemContent->update($data);
        return response()->json($subAboutItemContent);
    }

    // Admin - delete
    public function destroy(SubAboutItemContent $subAboutItemContent)
    {
        $subAboutItemContent->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
