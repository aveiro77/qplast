<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Invoice #{{ $sale->id }}</title>

  <style>
    /* A4 print styling */
    @page { size: A4 portrait; margin: 10mm; }
    html,body { width: 210mm; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; color: #000; }
    body { padding: 10mm; box-sizing: border-box; }

    .container { width: 100%; max-width: 190mm; margin: 0 auto; }
    .header { text-align: left; margin-bottom: 8px; }
    .company { font-size: 20px; font-weight: 700; }
    .meta { margin-top: 6px; font-size: 12px; color:#333; }

    table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 13px; }
    th, td { padding: 6px 8px; border: 1px solid #ddd; text-align: left; }
    th { background: #f5f5f5; font-weight: 700; font-size: 13px; }
    tfoot td { font-weight: 700; }

    .right { text-align: right; }
    .center { text-align: center; }

    .notes { margin-top: 12px; font-size: 12px; color: #333; }
    .thankyou { margin-top: 18px; font-weight: 700; font-size: 14px; text-align: center; }

    /* Hide interactive elements when printing (if any) */
    @media print {
      a { color: #000; text-decoration: none; }
      .no-print { display: none !important; }
    }
  </style>

  <script>
    // Auto print on open, then close the window (closing may be blocked by browser)
    function doPrint() {
      window.print();
    }
    window.onload = function() {
      setTimeout(doPrint, 250);
    };
    window.onafterprint = function() {
      // try to close; user agents may block it
      try { window.close(); } catch(e) {}
    };
  </script>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="company">Qurnia Plastik</div>
      <div class="meta">Jl. Contoh No.123 — Telp: 0812-xxxx-xxxx</div>
    </div>

    <div style="display:flex; justify-content:space-between; margin-top:8px;">
      <div style="font-size:13px;">
        <div><strong>Invoice:</strong> #{{ $sale->created_at->format('Ymd') }}{{ $sale->id }}</div>
        <div><strong>Date:</strong> {{ $sale->created_at->format('Y-m-d H:i') }}</div>
        <div><strong>Type:</strong> {{ $sale->name }}</div>
      </div>
      <div style="font-size:13px; text-align:right;">
        <div><strong>Customer</strong></div>
        <div>{{ $sale->customer->name ?? '-' }}</div>
        <div style="font-size:12px; color:#555">{{ $sale->customer->phone ?? '' }}</div>
        <div style="font-size:12px; color:#555">{{ $sale->customer->address ?? '' }}</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:50%;">Product</th>
          <th style="width:10%;" class="center">Unit</th>
          <th style="width:10%;" class="center">Qty</th>
          <th style="width:15%;" class="right">Price</th>
          <th style="width:15%;" class="right">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($sale->saleDetails as $d)
        <tr>
          <td>{{ $d->product->name ?? '-' }}</td>
          <td class="center">{{ $d->unit ?? '-' }}</td>
          <td class="center">{{ $d->quantity }}</td>
          <td class="right">Rp {{ number_format($d->price,0,',','.') }}</td>
          <td class="right">Rp {{ number_format($d->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" class="right">Total</td>
          <td class="right">Rp {{ number_format($sale->total_price,0,',','.') }}</td>
        </tr>
      </tfoot>
    </table>

    <div class="notes">
      <div><strong>Note:</strong> {{ $sale->note ?? '-' }}</div>
    </div>

    <div class="thankyou">Terima kasih telah berbelanja — Qurnia Plastik</div>
  </div>
</body>
</html>
