<?php

namespace App\Http\Controllers;

use App\Models\TermOfPayment;
use Illuminate\Http\Request;

class TermOfPaymentController extends Controller
{
    public function index()
    {
        $terms = TermOfPayment::all();
        return response()->json($terms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_due' => 'nullable|integer',
        ]);

        $term = TermOfPayment::create($validated);
        return response()->json($term, 201);
    }

    public function update(Request $request, TermOfPayment $termOfPayment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_due' => 'nullable|integer',
        ]);

        $termOfPayment->update($validated);
        return response()->json($termOfPayment);
    }

    public function destroy(TermOfPayment $termOfPayment)
    {
        $termOfPayment->delete();
        return response()->json(null, 204);
    }
}
