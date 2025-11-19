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
                <h3>Categories</h3>
                <p class="text-subtitle text-muted">Manage categories data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Categories</li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body">

                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
        
                    <div class="mb-3">
                        <label for="name" class="form-label">Customer Name</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $customer->name) }}" required>
                    </div>
        
                    <div class="mb-3">

                        <label for="address" class="form-label">Customer Address</label>
                        <input type="text" name="address" class="form-control" id="address" value="{{ old('address', $customer->address) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Customer Phone</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $customer->phone) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" class="form-control" id="type" required>
                            <option value="Regular" {{ old('type', $customer->type) == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Premium" {{ old('type', $customer->type) == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                    </div>
        
                    <button type="submit" class="btn btn-success">Update Categories</button>
                </form>
                
            </div>
        </div>
    </section>
</div>

@endsection