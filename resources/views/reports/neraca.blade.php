@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h4>Laporan Neraca Sederhana</h4>
    <form method="GET" action="{{ route('reports.neraca') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start">Tanggal Awal</label>
            <input type="date" name="start" id="start" class="form-control" value="{{ request('start', $start) }}">
        </div>
        <div class="col-md-3">
            <label for="end">Tanggal Akhir</label>
            <input type="date" name="end" id="end" class="form-control" value="{{ request('end', $end) }}">
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary">Tampilkan</button>
        </div>
        <div class="col-md-3 align-self-end text-end">
            <a href="{{ route('reports.neraca.export', ['start'=>request('start', $start), 'end'=>request('end', $end)]) }}" class="btn btn-success">Export CSV</a>
        </div>
    </form>
    <table class="table table-bordered w-50">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th class="text-end">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Penjualan</td>
                <td class="text-end">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total HPP</td>
                <td class="text-end">Rp {{ number_format($totalHpp ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Omzet</td>
                <td class="text-end">Rp {{ number_format($omzet ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kas Masuk</td>
                <td class="text-end">Rp {{ number_format($cashIn ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kas Keluar</td>
                <td class="text-end">Rp {{ number_format($cashOut ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Net Cash Flow</td>
                <td class="text-end">Rp {{ number_format($netCash ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
