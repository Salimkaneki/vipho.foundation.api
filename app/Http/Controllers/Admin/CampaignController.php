<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::with('creator')->where('status', 'active')->get();

        return response()->json($campaigns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'organization_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $campaign = Campaign::create([
            'title' => $request->title,
            'description' => $request->description,
            'goal_amount' => $request->goal_amount,
            'organization_name' => $request->organization_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::id(),
        ]);

        return response()->json($campaign, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $campaign = Campaign::with(['creator', 'donations'])->findOrFail($id);

        return response()->json($campaign);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $campaign = Campaign::findOrFail($id);

        // Vérifier si l'utilisateur est le créateur ou admin
        if ($campaign->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'goal_amount' => 'sometimes|required|numeric|min:0',
            'organization_name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|nullable|date|after:start_date',
            'status' => 'sometimes|in:active,completed,paused',
        ]);

        $campaign->update($request->only(['title', 'description', 'goal_amount', 'organization_name', 'start_date', 'end_date', 'status']));

        return response()->json($campaign);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->created_by !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted']);
    }
}
