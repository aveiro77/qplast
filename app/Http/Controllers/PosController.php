<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;

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
            $p->image = $file ? asset('storage/' . $file) : asset('no-image.png');

            return $p;
        });

        return view('pos.index', [
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
        //
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
