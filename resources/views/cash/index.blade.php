@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Transaksi Kas</h4>
        <a href="{{ route('cash.create') }}" class="btn btn-primary">Buat Transaksi Kas</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
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
