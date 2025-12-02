<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Category;
use App\Models\CashTransaction;
use App\Models\CashTransactionDetail;
use Illuminate\Support\Facades\Auth;



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
        // 1. Validasi input dasar
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string',
            'cart' => 'required',
            'paid_amount' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric',
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

        // Normalize paid and change
        $paidAmount = (float) $request->input('paid_amount', 0);
        $changeAmount = (float) $request->input('change_amount', 0);

        // Payment method logic
        if (strtolower($request->payment_method) === 'cash') {
            if ($paidAmount < $total) {
                $errorMsg = 'Pembayaran kurang: dibutuhkan ' . number_format($total, 2) . ', diterima ' . number_format($paidAmount, 2);
                if ($request->expectsJson() || $request->isJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return back()->with('error', $errorMsg);
            }
            $changeAmount = max(0, $paidAmount - $total);
        } else {
            // For transfer/card: assume paid equals total (no cash drawer)
            // If front-end sent a paidAmount, we accept it but don't require it.
            if ($paidAmount && abs($paidAmount - $total) > 0.01) {
                // It's acceptable to allow paidAmount for non-cash (e.g., transfer), but we won't error here.
            }
        }

        DB::beginTransaction();
        try {

            // 4. Simpan header transaksi dengan paid/change
            $sale = Sale::create([
                'customer_id'   => $request->customer_id,
                'payment_method'   => $request->payment_method,
                'name'          => 'Penjualan POS',
                'total_price'   => $total,
                'paid_amount'   => $paidAmount,
                'change_amount' => $changeAmount,
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

            // 6. Buat CashTransaction - catat pendapatan (total)
            if (strtolower($request->payment_method) === 'cash') {
                $cashTx = CashTransaction::create([
                    'date' => now()->toDateString(),
                    'type' => 'in',
                    'category' => 'penjualan',
                    'reference' => 'Sale #' . $sale->id,
                    'total' => $total,
                    'notes' => 'Penjualan POS #' . $sale->id,
                    'created_by' => Auth::id() ?: null,
                ]);

                CashTransactionDetail::create([
                    'cash_transaction_id' => $cashTx->id,
                    'description' => 'Penjualan (Sale #' . $sale->id . ')',
                    'amount' => $total,
                ]);

                // Optionally record the received amount and change separately (not implemented by default)
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
