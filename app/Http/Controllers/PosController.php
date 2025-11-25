<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Category;



class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // public function index()
    // {
    //     $products = Product::all()->map(function ($p) {
    //         // Sesuaikan kolom foto di database Anda
    //         $file = $p->foto ?? $p->photo ?? $p->image ?? $p->foto_produk;

    //         // Jika foto tersimpan di storage
    //         $p->image = $file ? asset('storage/' . $file) : asset('storage/noimage.jpg');

    //         return $p;
    //     });

    //     return view('pos.proto', [
    //         'customers' => Customer::all(),
    //         'products'  => $products
    //     ]);
    // }

    public function index()
    {
        $products = Product::all()->map(function ($p) {
            // Sesuaikan kolom foto di database Anda
            $file = $p->foto ?? $p->photo ?? $p->image ?? $p->foto_produk;

            // Jika foto tersimpan di storage
            $p->image = $file ? asset('storage/' . $file) : asset('storage/noimage.jpg');

            return $p;
        });

        // Import categories untuk dropdown filter
        $categories = \App\Models\Category::all();

        return view('pos.proto', [
            'customers'  => Customer::all(),
            'products'   => $products,
            'categories' => $categories
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        // 1. Validasi input
        $validated = $request->validate([
            'customer_id' => 'required',
            'cart'        => 'required'
        ]);

        // 2. Decode cart JSON menjadi array
        $cart = json_decode($request->cart, true);

        if (!$cart || count($cart) == 0) {
            $errorMsg = 'Keranjang kosong!';
            if ($request->expectsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        // 3. Hitung total transaksi
        $total = array_sum(array_column($cart, 'subtotal'));

        DB::beginTransaction();
        try {

            // 4. Simpan header transaksi
            $sale = Sale::create([
                'customer_id'   => $request->customer_id,
                'name'          => 'Penjualan POS',
                'total_price'   => $total,
            ]);

            // 5. Simpan detail transaksi + update stok
            foreach ($cart as $item) {

                // Simpan detail
                SaleDetail::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['qty'],
                    'unit'       => $item['unit'],
                    'price'      => $item['harga'],
                    'subtotal'   => $item['subtotal'],
                ]);

                // Kurangi stok barang
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->stock -= $item['qty'];
                    $product->save();
                }
            }

            DB::commit();

            // Return JSON untuk AJAX, redirect untuk non-AJAX
            if ($request->expectsJson() || $request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan!',
                    'sale_id' => $sale->id,
                    'total_price' => $total,
                    'receipt_url' => route('pos.receipt', $sale->id)
                ], 200);
            }

            return redirect()->route('pos.index')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMsg = 'Error: ' . $e->getMessage();
            if ($request->expectsJson() || $request->isJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
    }

    /**
     * Display receipt for printing
     */
    public function receipt($id)
    {
        $sale = Sale::with('saleDetails.product', 'customer')->findOrFail($id);
        return view('pos.receipt', compact('sale'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
