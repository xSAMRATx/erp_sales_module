<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('customer', 'products')->paginate();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        try {
            $rules = [
                'customer_id' => 'required|exists:customers,id',
                'product_id' => 'required|array',
                'quantity' => 'nullable|array',
                'discount' => 'nullable|array',
                'total_amount' => 'nullable',
                'sale_date' => 'required|after_or_equal:today',
            ];

            $messages = [];

            $attributes = [
                'customer_id' => 'customer',
                'product_id' => 'product',
            ];

            $request->validate($rules, $messages, $attributes);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        }

        $sale = new Sale();
        $sale->customer_id = $request->customer_id;
        $sale->sale_date = $request->sale_date;
        $sale->total_amount = $request->total_amount;

        $sale->save();


        $productIds = $request->product_id;

        if ($productIds) {
            foreach ($productIds as $key => $id) {
                $saleProduct = new SaleProduct();
                $saleProduct->sale_id = $sale->id;
                $saleProduct->product_id = $id;
                $saleProduct->quantity = $request->quantity[$id];
                $saleProduct->discount_percentage = $request->discount[$id];
                $saleProduct->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale has been created successfully!!!',
            'redirect' => route('sales.index')
        ]);
    }

    public function show(Sale $sale)
    {
        //
    }

    public function edit(Sale $sale)
    {
        //
    }

    public function update(Request $request, Sale $sale)
    {
        //
    }

    public function destroy(Sale $sale)
    {
        return response()->json(array('success' => true));

        if ($sale) {
            $sale->products()->detach();

            $sale->delete();
        }

        session(['success_message' => 'Sale has been deleted successfully!!!']);

        return response()->json(array('response_type' => 1));
    }

    public function fetchSolutions(Request $request)
    {
        $solutionIds = $request->input('solution_ids', []);

        $solutions = Product::whereIn('id', $solutionIds)->get();

        return response()->json($solutions);
    }
}
