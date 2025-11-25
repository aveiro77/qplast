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
                <h3>Sales</h3>
                <p class="text-subtitle text-muted">Sales list.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                        <li class="breadcrumb-item">Sales</li>
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

                <!-- Export Modal Trigger -->
                <div class="d-flex mb-3">
                    <a href="{{ route('sales.create') }}" class="btn btn-primary ms-auto">Create New Sale</a>
                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-download"></i> Export Excel
                    </button>
                </div>

                <!-- Export Modal -->
                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportModalLabel">Export Sales Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('sales.export') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="export_start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="export_start_date" name="start_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="export_end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="export_end_date" name="end_date" required>
                                    </div>
                                    <p class="small text-muted">The selected date range will be used to export sales and their details into an Excel-compatible CSV.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Export</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>#{{ $sale->created_at->format('Ymd') }}{{ $sale->id }}</td>
                                <td>{{ $sale->name }}</td>
                                <td>{{ $sale->created_at }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>Rp {{ number_format($sale->total_price,0,',','.') }}</td>

                                <td>
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No sales available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection