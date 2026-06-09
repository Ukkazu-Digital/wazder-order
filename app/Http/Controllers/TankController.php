<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\TankLog;
use Illuminate\Http\Request;

class TankController extends Controller
{
    public function index()
    {
        $tanks = Tank::all();
        return response()->json($tanks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric',
            'current_volume' => 'required|numeric',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $tank = Tank::create($validated);
        
        TankLog::create([
            'tank_id' => $tank->id,
            'water_level' => $tank->current_volume,
            'notes' => 'Initial creation'
        ]);

        return response()->json($tank, 201);
    }

    public function update(Request $request, Tank $tank)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|numeric',
            'current_volume' => 'sometimes|numeric',
            'type' => 'sometimes|string',
            'location' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($request->has('current_volume')) {
            $old_volume = $tank->current_volume;
            $new_volume = $request->current_volume;
            
            if ($old_volume != $new_volume) {
                TankLog::create([
                    'tank_id' => $tank->id,
                    'water_level' => $new_volume,
                    'notes' => "Volume changed from $old_volume to $new_volume"
                ]);
            }
        }

        $tank->update($validated);
        return response()->json($tank);
    }

    public function destroy(Tank $tank)
    {
        $tank->delete();
        return response()->json(null, 204);
    }

    public function updateVolume(Request $request, Tank $tank)
    {
        $validated = $request->validate([
            'current_volume' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $old_volume = $tank->current_volume;
        $new_volume = $validated['current_volume'];

        $tank->update(['current_volume' => $new_volume]);

        TankLog::create([
            'tank_id' => $tank->id,
            'water_level' => $new_volume,
            'notes' => $validated['notes'] ?? "Volume updated from $old_volume to $new_volume"
        ]);

        return response()->json($tank);
    }
}
