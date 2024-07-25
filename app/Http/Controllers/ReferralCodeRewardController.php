<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferralCodeReward;
use Illuminate\Support\Facades\Auth;

class ReferralCodeRewardController extends Controller
{
    public function index()
    {
        $rewards = ReferralCodeReward::all();
        return view('reward.index', compact('rewards'));
    }

    
    public function view(){
        $user_id = Auth::id();
        $rewards = ReferralCodeReward::all();

        foreach($rewards as $r){
            $r->available = $r->validateCondition($user_id);
            $r->desc = $r->desc . "\n\n" .$r->getConditionText(); 
           
        }

        return  view('reward.view', compact('rewards')) ;//$rewards;
    }

    public function create()
    {
        return view('reward.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            
            'quantity' => 'required|integer',
            'condition' => 'nullable|json',
            'requireAsset' => 'nullable|json',
            'additionInfo' => 'nullable|string',
            'external_link' => 'nullable|url',
            'payment' => 'required|boolean',
            'paymentAmount' => 'required|numeric|min:0',
        ]);
        $validated['status'] = 1;
        ReferralCodeReward::create($validated);

        return redirect()->route('reward.index')->with('success', 'Reward created successfully.');
    }

    public function edit(ReferralCodeReward $reward)
    {
        return view('reward.edit', compact('reward'));
    }

    public function update(Request $request, ReferralCodeReward $reward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'status' => 'required|boolean',
            'quantity' => 'required|integer',
            'condition' => 'nullable|json',
            'requireAsset' => 'nullable|json',
            'additionInfo' => 'nullable|string',
            'external_link' => 'nullable|url',
            'payment' => 'required|boolean',
            'paymentAmount' => 'required|numeric|min:0',
        ]);

        $reward->update($validated);

        return redirect()->route('reward.index')->with('success', 'Reward updated successfully.');
    }

    public function destroy(ReferralCodeReward $reward)
    {
        $reward->delete();

        return redirect()->route('reward.index')->with('success', 'Reward deleted successfully.');
    }
}
