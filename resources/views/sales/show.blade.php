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
                <h3>Sale Details</h3>
                <p class="text-subtitle text-muted">View sale information and line items.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">

                <!-- Sale Header -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Sale Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <td style="width:40%"><strong>Sale ID:</strong></td>
                                <td>#{{ $sale->created_at->format('Ymd') }}{{ $sale->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ $sale->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>{{ $sale->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Note:</strong></td>
                                <td>{{ $sale->note ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Customer Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <td style="width:40%"><strong>Name:</strong></td>
                                <td>{{ $sale->customer->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $sale->customer->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $sale->customer->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $sale->customer->address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Sale Details Table -->
                <h5 class="mb-3">Line Items</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="width:12%">Unit</th>
                                <th style="width:12%">Quantity</th>
                                <th style="width:15%">Price</th>
                                <th style="width:15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sale->saleDetails as $detail)
                                <tr>
                                    <td>{{ $detail->product->name ?? '-' }}</td>
                                    <td>{{ $detail->unit ?? '-' }}</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No items in this sale.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales</a>
                </div>

            </div>
        </div>
    </section>
</div>

@endsection
