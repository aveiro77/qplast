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
                <h3>Manajemen Affal</h3>
                <p class="text-subtitle text-muted">Kelola barang rusak dan tidak standar.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Affal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row mb-3">
            <div class="col-12 col-md-8">
                <a href="{{ route('affals.create') }}" class="btn btn-primary">+ Keluarkan Stok ke Affal</a>
            </div>
            <!-- <div class="col-12 col-md-4 text-md-end">
                <div class="card">
                    <div class="card-body py-2">
                        <small class="text-muted">Stok Affal: <strong>{{ $affal->qty_stock }} unit</strong></small> | 
                        <small class="text-muted">Harga: <strong>Rp {{ number_format($affal->price, 0, ',', '.') }}</strong></small>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Jumlah Dikeluarkan</th>
                                <th>Catatan</th>
                                <th>Dicatat Oleh</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                            <tr>
                                <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                <td><strong>{{ $t->product->name }}</strong></td>
                                <td>{{ $t->qty_moved }} {{ $t->product->unit }}</td>
                                <td><small>{{ $t->notes ?? '-' }}</small></td>
                                <td>{{ $t->createdBy->name ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('affals.edit', $t->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                    <form action="{{ route('affals.destroy', $t->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Menghapus transaksi ini akan mengembalikan stok ke produk dan mengurangi stok affal. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada transaksi affal</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $transactions->links() }}
            </div>
        </div>
    </section>
</div>

@endsection