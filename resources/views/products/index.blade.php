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

                <div class="d-flex">
                    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3 ms-auto">New Product</a>
                </div>

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

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Stok</th>
                            <th>HPP</th>
                            <th>Ecer</th>
                            <th>Ball</th>
                            <th>Grosir</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->hpp }}</td>
                                <td>{{ $product->hrg_ecer }}</td>
                                <td>{{ $product->hrg_ball }}</td>
                                <td>{{ $product->hrg_grosir }}</td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="{{ route('products.show', $product->id) }}">View</a>
                                   <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection