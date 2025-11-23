<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleDetail;



class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view('pos.index', [
    //         'customers' => Customer::all(),
    //         'products'  => Product::all()
    //     ]);

    //     return view('pos.index', [
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

        return view('pos.proto', [
            'customers' => Customer::all(),
            'products'  => $products
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
        $request->validate([
            'customer_id' => 'required',
            'cart'        => 'required'
        ]);

        // 2. Decode cart JSON menjadi array
        $cart = json_decode($request->cart, true);

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Keranjang kosong!');
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

            return redirect()->route('pos.sukses')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: '.$e->getMessage());
        }
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
