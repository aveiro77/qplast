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
                <h3>Detail Products</h3>
                <p class="text-subtitle text-muted">Manage tasks data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">Product Name</label>
                            <p>{{ $product->name }}</p>
                        </div>
                
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <p>{{ $product->category->name }}</p>
                        </div>
                
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <p>{{ $product->unit }}</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Current stock</label>
                            <p>{{ number_format($product->stock, 0, ',', '.') }}</p>
                        </div>
                
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <p>{{ ucfirst($product->status) }}</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label class="form-label">HPP</label>
                            <p>{{ 'Rp ' . number_format($product->hpp, 0, ',', '.') }}</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Grosir</label>
                            <p>{{ 'Rp ' . number_format($product->hrg_grosir, 0, ',', '.') }}</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Ball</label>
                            <p>{{ 'Rp ' . number_format($product->hrg_ball, 0, ',', '.') }}</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Ecer</label>
                            <p>{{ 'Rp ' . number_format($product->hrg_ecer, 0, ',', '.') }}</p>
                        </div>

                    </div>
                    <div>
                        <label class="form-label">Product Image</label>
                        <div>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" style="max-width: 200px; max-height: 200px;">
                            @else
                                <p>No image available.</p>
                            @endif
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back to Products</a>
                
            </div>
        </div>
    </section>
</div>

@endsection