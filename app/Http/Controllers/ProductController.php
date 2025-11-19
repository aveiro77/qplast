<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        // 1. Validasi Data
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric',
            'hpp' => 'required|numeric',
            'hrg_ball' => 'required|numeric',
            'hrg_grosir' => 'required|numeric',
            'hrg_ecer' => 'required|numeric',
            'status' => 'required|string|max:50',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
        ]);
        
        // 2. Proses Unggahan Gambar (Pisahkan logika file dari data input lain)
        if ($request->hasFile('image')) {
            // Menyimpan file ke storage/app/public/product_images dan mendapatkan path-nya
            $imagePath = $request->file('image')->store('product_images', 'public');
        } else {
            $imagePath = null; // Atau tetapkan nilai default jika diperlukan
        }

        // 3. Gabungkan Path Gambar ke Data Tervalidasi
        // Catatan: Pastikan 'image' ada dalam $fillable di Model Product
        $validatedData['image'] = $imagePath;

        // 4. Simpan Data ke Database
        Product::create($validatedData);

        // 5. Redirect dengan pesan sukses
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
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'unit' => 'required|string|max:50',
            'hpp' => 'required|numeric',
            'hrg_ecer' => 'required|numeric',
            'hrg_ball' => 'required|numeric',
            'hrg_grosir' => 'required|numeric',
            'status' => 'required|string|max:50',
            'stock' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
        
            // A. Hapus gambar lama jika ada
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // B. Simpan gambar baru
            $imagePath = $request->file('image')->store('product_images', 'public');
            
            // C. Masukkan path baru ke array data
            $validatedData['image'] = $imagePath;
        } else {
            // Jika tidak ada gambar baru, HAPUS key 'image' dari validatedData
            // agar Laravel tidak menimpa kolom image di DB menjadi NULL
            unset($validatedData['image']);
        }

        $product->update($validatedData);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Display the specified employee
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    // Remove the specified employee from storage
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();


        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
