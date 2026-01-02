<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $campaignId = $request->query('campaign_id');
        if ($campaignId) {
            $donations = Donation::where('campaign_id', $campaignId)->with('donor')->get();
        } else {
            $donations = Donation::with('donor', 'campaign')->get();
        }
        return response()->json($donations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monetary,physical',
            'amount' => 'required_if:type,monetary|numeric|min:0',
            'currency' => 'required_if:type,monetary|string',
            'description' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'category' => 'nullable|in:food,essentials,clothing,other',
            'message' => 'nullable|string',
            'is_anonymous' => 'boolean',
            'donor_name' => 'nullable|string',
            'donor_email' => 'nullable|email',
            'campaign_id' => 'required|exists:campaigns,id',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_type' => 'nullable|in:mix_by_yas,moov_money,other',
            'deposit_number' => 'nullable|string|max:50',
            'location' => 'nullable|string',
        ]);

        $data = $request->only([
            'type', 'amount', 'currency', 'description', 'quantity', 'category',
            'message', 'is_anonymous', 'donor_name', 'donor_email', 'campaign_id',
            'first_name', 'last_name', 'phone', 'payment_type', 'deposit_number', 'location'
        ]);

        // Si connecté, lier au user
        if (auth()->check()) {
            $data['donor_id'] = auth()->id();
        }

        $donation = Donation::create($data);

        // Mettre à jour le montant collecté si monétaire
        if ($donation->type === 'monetary') {
            $campaign = Campaign::find($donation->campaign_id);
            $campaign->increment('current_amount', $donation->amount);
        }

        return response()->json($donation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $donation = Donation::with('donor', 'campaign')->findOrFail($id);
        return response()->json($donation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Pas d'update pour les dons (immuables)
        return response()->json(['error' => 'Donations cannot be updated'], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Pas de delete pour les dons (pour traçabilité)
        return response()->json(['error' => 'Donations cannot be deleted'], 405);
    }
}
