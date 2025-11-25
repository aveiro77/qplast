<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Struk - Qplast</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Print layout minimal, cocok untuk thermal 58mm (~300px) */
    body { font-family: Arial, Helvetica, sans-serif; color:#000; margin:0; padding:0; background:#fff; }
    .receipt { width: 300px; padding: 8px 10px; margin:0 auto; }
    h1,h2,h3 { margin:0; padding:0; font-weight:700; }
    .center { text-align:center; }
    .logo { font-size:18px; font-weight:700; color:#000; margin-bottom:6px; }
    .small { font-size:12px; color:#333; }
    .items { width:100%; margin-top:8px; border-collapse:collapse; }
    .items td { padding:4px 0; font-size:12px; vertical-align:top; }
    .totals { margin-top:8px; font-size:13px; font-weight:700; }
    hr { border:none; border-top:1px dashed #333; margin:8px 0; }
    .item-name { font-weight:600; }
    .item-details { font-size:11px; color:#555; }
    .price-col { text-align:right; }
    @media print {
      @page { margin: 0; size: auto; }
      body { margin: 0; padding:0; }
    }
  </style>
</head>
<body onload="window.print();">
  <div class="receipt">
    @yield('content')
    <div style="height:20px;"></div>
  </div>
  <script>
    // optional: close window after print (some browsers restrict)
    if (window.matchMedia) {
      window.onafterprint = function(){ setTimeout(()=>{ window.close(); }, 500); }
    }
  </script>
</body>
</html>
