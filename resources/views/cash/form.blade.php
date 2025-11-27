@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h4>{{ $item->exists ? 'Edit Transaction' : 'New Transaction' }}</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $item->exists ? route('cash.update', $item->id) : route('cash.store') }}">
        @csrf
        @if($item->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-3">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $item->date ? $item->date->format('Y-m-d') : date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label>Tipe</label>
                <select name="type" class="form-control">
                    <option value="in" {{ old('type', $item->type) == 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ old('type', $item->type) == 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Kategori</label>
                <select name="category" class="form-control">
                    <option value="">— Pilih —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ old('category', $item->category) == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Referensi</label>
                <input type="text" name="reference" class="form-control" value="{{ old('reference', $item->reference) }}">
            </div>
        </div>

        <hr>

        <h5>Detail</h5>
        <table class="table" id="details-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $details = old('details', $item->details->toArray() ?? []);
                @endphp
                @if(empty($details))
                    <tr>
                        <td><input name="details[0][description]" class="form-control"></td>
                        <td><input name="details[0][amount]" class="form-control" type="number" step="0.01"></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">-</button></td>
                    </tr>
                @else
                    @foreach($details as $i => $d)
                    <tr>
                        <td><input name="details[{{ $i }}][description]" class="form-control" value="{{ $d['description'] ?? '' }}"></td>
                        <td><input name="details[{{ $i }}][amount]" class="form-control" type="number" step="0.01" value="{{ $d['amount'] ?? '' }}"></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">-</button></td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addRow()">Tambah Item</button>

        <div class="mb-3">
            <label>Catatan</label>
            <textarea name="notes" class="form-control">{{ old('notes', $item->notes) }}</textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('cash.index') }}" class="btn btn-light">Batal</a>
    </form>
</div>

<script>
function addRow(){
    const tbody = document.querySelector('#details-table tbody');
    const index = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input name="details[${index}][description]" class="form-control"></td>
        <td><input name="details[${index}][amount]" class="form-control" type="number" step="0.01"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">-</button></td>
    `;
    tbody.appendChild(tr);
}
function removeRow(btn){
    const tr = btn.closest('tr');
    tr.remove();
}
</script>

@endsection
