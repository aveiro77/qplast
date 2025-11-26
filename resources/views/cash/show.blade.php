@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Detail Transaksi Kas #{{ $item->id }}</h4>
        <div>
            <a href="{{ route('cash.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="{{ route('cash.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Tanggal:</strong> {{ $item->date->format('Y-m-d') }}</p>
            <p><strong>Tipe:</strong> {{ $item->type == 'in' ? 'Masuk' : 'Keluar' }}</p>
            <p><strong>Kategori:</strong> {{ $item->category }}</p>
            <p><strong>Referensi:</strong> {{ $item->reference }}</p>
            <p><strong>Catatan:</strong> {{ $item->notes }}</p>
        </div>
    </div>

    <h5>Detail Item</h5>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Keterangan</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($item->details as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->description }}</td>
                <td class="text-end">{{ number_format($d->amount,2,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th class="text-end">{{ number_format($item->total,2,',','.') }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
