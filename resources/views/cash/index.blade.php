@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Finance</h4>
    </div>

    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Export Modal Trigger -->
                <div class="d-flex mb-3">
                    <a href="{{ route('cash.create') }}" class="btn btn-primary ms-auto">New Transaction</a>
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
                            <form action="{{ route('cash.export') }}" method="POST">
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
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->date->format('Y-m-d') }}</td>
                <td>{{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}</td>
                <td>{{ $item->category }}</td>
                <td class="text-end">{{ number_format($item->total,2,',','.') }}</td>
                <td>
                    <a href="{{ route('cash.show', $item->id) }}" class="btn btn-sm btn-info">Lihat</a>
                    <a href="{{ route('cash.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('cash.destroy', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus transaksi kas?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $items->links() }}
</div>
@endsection
