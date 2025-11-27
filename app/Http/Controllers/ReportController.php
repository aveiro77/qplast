<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Halaman utama laporan neraca
    public function index(Request $request)

    {
        // Ambil filter tanggal dari request, default bulan ini
        $start = $request->input('start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end', now()->endOfMonth()->format('Y-m-d'));

        // Total penjualan
        $totalSales = \App\Models\Sale::whereBetween('created_at', [$start, $end])->sum('total_price');

        // Total HPP (COGS)
        $totalHpp = \DB::table('sale_details as sd')
            ->join('sales as s','sd.sale_id','s.id')
            ->join('products as p','sd.product_id','p.id')
            ->whereBetween('s.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(sd.quantity * p.hpp),0) as total_hpp')
            ->value('total_hpp');

        // Omzet
        $omzet = $totalSales - $totalHpp;

        // Kas masuk
        $cashIn = \App\Models\CashTransaction::where('type','in')->whereBetween('date', [$start, $end])->sum('total');
        // Kas keluar
        $cashOut = \App\Models\CashTransaction::where('type','out')->whereBetween('date', [$start, $end])->sum('total');
        $netCash = $cashIn - $cashOut;

        return view('reports.neraca', compact('start', 'end', 'totalSales', 'totalHpp', 'omzet', 'cashIn', 'cashOut', 'netCash'));
    }

    // Export CSV (skeleton, implementasi menyusul)
    public function export(Request $request)
    {
        $start = $request->input('start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end', now()->endOfMonth()->format('Y-m-d'));

        $totalSales = \App\Models\Sale::whereBetween('created_at', [$start, $end])->sum('total_price');
        $totalHpp = \DB::table('sale_details as sd')
            ->join('sales as s','sd.sale_id','s.id')
            ->join('products as p','sd.product_id','p.id')
            ->whereBetween('s.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(sd.quantity * p.hpp),0) as total_hpp')
            ->value('total_hpp');
        $omzet = $totalSales - $totalHpp;
        $cashIn = \App\Models\CashTransaction::where('type','in')->whereBetween('date', [$start, $end])->sum('total');
        $cashOut = \App\Models\CashTransaction::where('type','out')->whereBetween('date', [$start, $end])->sum('total');
        $netCash = $cashIn - $cashOut;

        $rows = [
            ['Item', 'Nilai'],
            ['Total Penjualan', $totalSales],
            ['Total HPP', $totalHpp],
            ['Omzet', $omzet],
            ['Kas Masuk', $cashIn],
            ['Kas Keluar', $cashOut],
            ['Net Cash Flow', $netCash],
        ];

        $filename = 'neraca_' . $start . '_to_' . $end . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function() use ($rows) {
            $f = fopen('php://output', 'w');
            foreach ($rows as $row) {
                // Format angka sebagai string
                if (is_numeric($row[1])) {
                    $row[1] = number_format($row[1], 0, ',', '.');
                }
                fputcsv($f, $row);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}