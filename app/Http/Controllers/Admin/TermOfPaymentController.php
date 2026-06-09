<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermOfPayment;
use App\Models\Order;
use App\Models\OrderPaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TermOfPaymentController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $terms = TermOfPayment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->get();
        $ordersWithTerm = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                            ->whereNotNull('payment_term_id')
                            ->get();

        // Calculate total revenue by term for the period
        $revenueByTerm = $ordersWithTerm->groupBy('payment_term_id')
                                       ->map(function ($orders, $termId) {
                                           $term = TermOfPayment::find($termId);
                                           return [
                                               'term_name' => $term ? $term->name : 'Unknown',
                                               'total_revenue' => $orders->sum('total_price'),
                                               'total_orders' => $orders->count()
                                           ];
                                       });

        return view('admin.terms.index', compact('terms', 'revenueByTerm', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('admin.terms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:term_of_payments,name',
            'description' => 'nullable|string',
            'days_due' => 'required|integer|min:0',
        ]);

        TermOfPayment::create($request->all());

        return redirect()->route('admin.terms.index')->with('success', 'Term of Payment created successfully.');
    }

    public function show(TermOfPayment $term)
    {
        return view('admin.terms.show', compact('term'));
    }

    public function edit(TermOfPayment $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    public function update(Request $request, TermOfPayment $term)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:term_of_payments,name,' . $term->id,
            'description' => 'nullable|string',
            'days_due' => 'required|integer|min:0',
        ]);

        $term->update($request->all());

        return redirect()->route('admin.terms.index')->with('success', 'Term of Payment updated successfully.');
    }

    public function destroy(TermOfPayment $term)
    {
        // Before deleting, check if any orders are using this term
        $ordersUsingTerm = Order::where('payment_term_id', $term->id)->exists();

        if ($ordersUsingTerm) {
            return redirect()->route('admin.terms.index')->with('error', 'Cannot delete this term of payment as it is used by existing orders. Please reassign or delete orders first.');
        }

        $term->delete();
        return redirect()->route('admin.terms.index')->with('success', 'Term of Payment deleted successfully.');
    }
}
