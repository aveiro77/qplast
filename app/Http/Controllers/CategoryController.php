<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // Show the form to create a new category
    public function create()
    {
        return view('categories.create');
    }

    // Store a newly created category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:255',
            'description' => 'nullable',
        ]);

        category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'category created successfully');
    }

    // Show the form for editing a category
    public function edit(category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Update the specified category
    public function update(Request $request, category $category)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'category updated successfully');
    }

    // Delete a category
    public function destroy(category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'category deleted successfully');
    }
}
