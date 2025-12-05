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
                <h3>Keluarkan Stok ke Affal</h3>
                <p class="text-subtitle text-muted">Catat barang rusak/tidak standar yang dikeluarkan dari stok.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('affals.index') }}">Affal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Keluarkan Stok</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('affals.store') }}">
                            @csrf

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Produk</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} (Stok: {{ $p->stock }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Jumlah Dikeluarkan</label>
                                <input type="number" name="qty_moved" class="form-control" min="1" value="{{ old('qty_moved') }}" required>
                                @error('qty_moved')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Alasan barang rusak/tidak standar">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('affals.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Keluarkan Stok</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Info Affal</h5>
                        <div class="mb-2">
                            <small class="text-muted">Stok Affal Saat Ini</small>
                            <p class="fs-4 fw-bold">{{ $affal->qty_stock }} unit</p>
                        </div>
                        <div>
                            <small class="text-muted">Harga Jual Affal</small>
                            <p class="fs-4 fw-bold">Rp {{ number_format($affal->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection