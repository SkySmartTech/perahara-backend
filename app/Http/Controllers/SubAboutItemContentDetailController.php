<?php

namespace App\Http\Controllers;

use App\Models\SubAboutItemContentDetail;
use Illuminate\Http\Request;

class SubAboutItemContentDetailController extends Controller
{
    // Public - list all
    public function index()
    {
        return response()->json(SubAboutItemContentDetail::with('subAboutItemContent')->latest()->get());
    }

    // Public - single
    public function show($id)
    {
        $detail = SubAboutItemContentDetail::with('subAboutItemContent')->findOrFail($id);
        return response()->json($detail);
    }

    // Admin - create
    public function store(Request $request)
    {
        $data = $request->validate([
            'sub_about_item_content_id' => 'required|exists:sub_about_item_contents,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $detail = SubAboutItemContentDetail::create($data);
        return response()->json($detail, 201);
    }

    // Admin - update
    public function update(Request $request, SubAboutItemContentDetail $subAboutItemContentDetail)
    {
        $data = $request->validate([
            'sub_about_item_content_id' => 'sometimes|exists:sub_about_item_contents,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $subAboutItemContentDetail->update($data);
        return response()->json($subAboutItemContentDetail);
    }

    // Admin - delete
    public function destroy(SubAboutItemContentDetail $subAboutItemContentDetail)
    {
        $subAboutItemContentDetail->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
