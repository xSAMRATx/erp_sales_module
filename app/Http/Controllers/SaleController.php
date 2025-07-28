<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function edit($id)
    {
        $sale = Sale::with('customer', 'products')->find($id);

        // dd($sale);
        $customers = Customer::pluck('name', 'id')->toArray();
        $products = Product::pluck('name', 'id')->toArray();
        return view('sales.edit', compact('customers', 'products', 'sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        dd($request->all());
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
        dd($request->all());

        $solutionIds = $request->input('solution_ids', []);

        $solutions = SaleProduct::whereIn('id', $solutionIds)->get();

        return response()->json($solutions);
    }

    public function fetchSaleProducts(Request $request, $id)
    {
        $productIds = $request->input('productIds', []);

        $products = DB::table('products')
            ->leftJoin('sale_product', function ($join) use ($id) {
                $join->on('products.id', '=', 'sale_product.product_id')
                    ->where('sale_product.sale_id', '=', $id);
            })
            ->whereIn('products.id', $productIds)
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'sale_product.quantity',
                'sale_product.discount_percentage'
            )
            ->get();

        return response()->json($products);
    }
}
