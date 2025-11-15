<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Show the form for creating a new employee
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Store a newly created employee in storage
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric',
            'hpp' => 'required|numeric',
            'hrg_ball' => 'required|numeric',
            'hrg_grosir' => 'required|numeric',
            'hrg_ecer' => 'required|numeric',
            'status' => 'required|string|max:50',
        ]);

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'unit' => $request->unit,
            'stock' => $request->stock,
            'hpp' => $request->hpp,
            'hrg_ball' => $request->hrg_ball,
            'hrg_grosir' => $request->hrg_grosir,
            'hrg_ecer' => $request->hrg_ecer,
            'status' => $request->status,
            
        ]);

        return redirect()->route('products.index')->with('success', 'Products created successfully.');
    }


    // Show the form for editing the specified employee
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        
        return view('products.edit', compact('product', 'categories'));
    }

    // Update the specified employee in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'hpp' => 'required|numeric',
            'hrg_ecer' => 'required|numeric',
            'hrg_ball' => 'required|numeric',
            'hrg_grosir' => 'required|numeric',
            'status' => 'required|string|max:50',
            'stock' => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Remove the specified employee from storage
    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
