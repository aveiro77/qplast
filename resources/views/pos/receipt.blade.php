@extends('layouts.print')

@section('content')
  <div class="center">
    <div class="logo">Qurnia Plastik</div>
    <div class="small">Rumah botol plastik & kemasan</div>
    <div class="small">Jl. Seruni no.79 Kota Pekalongan • Telp: 0856-4026-0203</div>
    <hr>
  </div>

  <div class="small">
    <div><strong>Invoice #{{ $sale->created_at->format('Ymd') }}{{ $sale->id }}</strong></div>
    <div>{{ $sale->created_at->format('Y-m-d H:i') }}</div>
    <div>Customer: <strong>{{ optional($sale->customer)->name ?? 'Customer' }}</strong></div>
    <div>Cashier: <strong>{{ optional($sale->user)->name ?? '-' }}</strong></div>
    <div>Payment Method: <strong>{{ $sale->payment_method ?? '-' }}</strong></div>
  </div>

  <hr>

  <table class="items">
    <tbody>
      @forelse($sale->saleDetails as $d)
      <tr>
        <td class="item-name" style="width:60%;">{{ optional($d->product)->name ?? '(produk)' }}</td>
        <td class="price-col" style="width:40%;">{{ number_format($d->subtotal,0,',','.') }}</td>
      </tr>
      <tr>
        <td class="item-details" colspan="2">
          {{ rtrim($d->quantity, '.0') }} {{ $d->unit }} × Rp {{ number_format($d->price,0,',','.') }}
        </td>
      </tr>
      @empty
      <tr><td colspan="2" class="center small">Tidak ada item</td></tr>
      @endforelse
    </tbody>
  </table>

  <hr>
  <div class="totals">
    <div style="display:flex; justify-content:space-between;">
      <div>Total:</div>
      <div>Rp {{ number_format($sale->total_price,0,',','.') }}</div>
    </div>
    @if($sale->paid_amount > 0)
    <div style="display:flex; justify-content:space-between; margin-top:8px;">
      <div>Paid:</div>
      <div>Rp {{ number_format($sale->paid_amount,0,',','.') }}</div>
    </div>
    @endif
    @if($sale->change_amount > 0)
    <div style="display:flex; justify-content:space-between; margin-top:4px;">
      <div>Change:</div>
      <div>Rp {{ number_format($sale->change_amount,0,',','.') }}</div>
    </div>
    @endif
  </div>

  <hr>
  <div class="center small">
    <div style="margin-bottom:6px;">Terima kasih atas pembelian Anda!</div>
    <div style="font-size:11px;">* Barang yang sudah dibeli tidak dapat dikembalikan tanpa nota</div>
  </div>
@endsection
