<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use App\Models\TankLog;
use App\Models\RefillSchedule;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class TankController extends Controller
{
    public function index()
    {
        $tanks = Tank::with('customer')->get();
        return view('admin.tanks.index', compact('tanks'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('admin.tanks.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'current_volume' => 'nullable|numeric|min:0|lte:capacity',
            'type' => 'required|string|in:water,gas,other',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,maintenance,inactive',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $tank = Tank::create($request->all());

        // Record initial log if current_volume is provided
        if ($request->filled('current_volume')) {
            TankLog::create([
                'tank_id' => $tank->id,
                'water_level' => $request->current_volume,
                'notes' => 'Initial tank volume setting',
            ]);
        }

        return redirect()->route('admin.tanks.index')->with('success', 'Tank created successfully.');
    }

    public function show(Tank $tank)
    {
        $tank->load('logs', 'refillSchedules', 'customer');
        $logs = $tank->logs()->orderByDesc('created_at')->paginate(10); // Paginate logs
        $refills = $tank->refillSchedules()->orderByDesc('scheduled_date')->paginate(10); // Paginate refills

        return view('admin.tanks.show', compact('tank', 'logs', 'refills'));
    }

    public function edit(Tank $tank)
    {
        $customers = Customer::all();
        return view('admin.tanks.edit', compact('tank', 'customers'));
    }

    public function update(Request $request, Tank $tank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'current_volume' => 'nullable|numeric|min:0|lte:capacity',
            'type' => 'required|string|in:water,gas,other',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,maintenance,inactive',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $tank->update($request->all());

        // Record log if current_volume changed or updated
        if ($request->filled('current_volume') && $tank->getOriginal('current_volume') != $request->current_volume) {
            TankLog::create([
                'tank_id' => $tank->id,
                'water_level' => $request->current_volume,
                'notes' => 'Tank volume manually updated',
            ]);
        }

        return redirect()->route('admin.tanks.index')->with('success', 'Tank updated successfully.');
    }

    public function destroy(Tank $tank)
    {
        $tank->delete();
        return redirect()->route('admin.tanks.index')->with('success', 'Tank deleted successfully.');
    }

    // Custom methods for tank operations
    public function recordLog(Request $request, Tank $tank)
    {
        $request->validate([
            'water_level' => 'required|numeric|min:0|lte:' . $tank->capacity,
            'notes' => 'nullable|string|max:255',
        ]);

        TankLog::create([
            'tank_id' => $tank->id,
            'water_level' => $request->water_level,
            'notes' => $request->notes,
        ]);

        // Update current volume of the tank
        $tank->update(['current_volume' => $request->water_level]);

        return back()->with('success', 'Tank log recorded and volume updated.');
    }

    public function storeRefill(Request $request, Tank $tank)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'target_volume' => 'nullable|numeric|min:0|lte:' . $tank->capacity,
            'notes' => 'nullable|string|max:255',
        ]);

        RefillSchedule::create([
            'tank_id' => $tank->id,
            'scheduled_date' => $request->scheduled_date,
            'target_volume' => $request->target_volume,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Refill schedule added successfully.');
    }

    public function lowStockCheck()
    {
        // This method can be called via cron or manually to get low stock tanks
        $lowStockTanks = Tank::whereColumn('current_volume', '<=', DB::raw('capacity * 0.3'))
                             ->where('status', 'active')
                             ->get();

        if ($lowStockTanks->isNotEmpty()) {
            Log::warning('Low Stock Alert:', $lowStockTanks->pluck('name', 'id')->toArray());
            // Implement notification (e.g., email, WhatsApp) here if needed
        }

        return response()->json(['low_stock_tanks' => $lowStockTanks]);
    }
}
