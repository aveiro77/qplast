@extends('layouts.dashboard')

@section('content')

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Products</h3>
                <p class="text-subtitle text-muted">Manage products data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select name="category_id" class="form-control" id="category_id" required>
                            @foreach($categories as $category)
                                @if($category->id == $product->category_id)
                                    <option value="{{ $category->id }}" selected>{{ $category->name }}</option> 
                                @else
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
        
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <select name="unit" id="unit" class="form-control" required>
                            <option value="Ball" {{ $product->unit == 'Ball' ? 'selected' : '' }}>Ball</option>
                            <option value="Pcs" {{ $product->unit == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                        </select>
                    </div>
        
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" id="stock" value="{{ old('stock', $product->stock) }}" required>
                    </div> 

                    <div class="mb-3">
                        <label for="hpp" class="form-label">HPP</label>
                        <input type="number" name="hpp" class="form-control" id="hpp" value="{{ old('hpp', $product->hpp) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="hrg_grosir" class="form-label">Grosir</label>
                        <input type="number" name="hrg_grosir" class="form-control" id="hrg_grosir" value="{{ old('hrg_grosir', $product->hrg_grosir) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="hrg_ball" class="form-label">Ball</label>
                        <input type="number" name="hrg_ball" class="form-control" id="hrg_ball" value="{{ old('hrg_ball', $product->hrg_ball) }}" required>
                    </div>      

                    <div class="mb-3">
                        <label for="hrg_ecer" class="form-label">Ecer</label>
                        <input type="number" name="hrg_ecer" class="form-control" id="hrg_ecer" value="{{ old('hrg_ecer', $product->hrg_ecer) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Active" {{ $product->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $product->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" class="image-preview-filepond" name="image" id="image">
                    </div>
        
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
              
            </div>
        </div>
    </section>
</div>

@endsection