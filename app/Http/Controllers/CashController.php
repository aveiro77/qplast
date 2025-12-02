<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\CashTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with('details')->orderBy('date', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qbb) use ($q) {
                $qbb->where('category', 'like', "%{$q}%")
                    ->orWhere('reference', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('date', [$start, $end]);
            } catch (\Exception $e) {
                // ignore invalid dates
            }
        }

        $items = $query->paginate(20)->appends($request->except('page'));

        return view('cash.index', compact('items'));
    }

    public function create()
    {
        $categories = ['penjualan affal','modal','operasional', 'pengeluaran lain', 'pemasukan lain'];
        return view('cash.form', ['item' => new CashTransaction(), 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'category' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.description' => 'nullable|string',
            'details.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data) {
            $total = collect($data['details'])->sum(function ($d) { return $d['amount']; });

            $tx = CashTransaction::create([
                'date' => $data['date'],
                'type' => $data['type'],
                'category' => $data['category'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total' => $total,
                'created_by' => auth()->id() ?? null,
            ]);

            foreach ($data['details'] as $d) {
                $tx->details()->create([
                    'description' => $d['description'] ?? null,
                    'amount' => $d['amount'],
                ]);
            }
        });

        return redirect()->route('cash.index')->with('success', 'Transaksi kas tersimpan.');
    }

    public function show(CashTransaction $cash)
    {
        $cash->load('details');
        return view('cash.show', ['item' => $cash]);
    }

    public function edit(CashTransaction $cash)
    {
        $categories = ['penjualan affal','modal','operasional', 'pengeluaran lain', 'pemasukan lain'];
        $cash->load('details');
        return view('cash.form', ['item' => $cash, 'categories' => $categories]);
    }

    public function update(Request $request, CashTransaction $cash)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'category' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.description' => 'nullable|string',
            'details.*.amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data, $cash) {
            $total = collect($data['details'])->sum(function ($d) { return $d['amount']; });

            $cash->update([
                'date' => $data['date'],
                'type' => $data['type'],
                'category' => $data['category'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total' => $total,
            ]);

            // replace details
            $cash->details()->delete();
            foreach ($data['details'] as $d) {
                $cash->details()->create([
                    'description' => $d['description'] ?? null,
                    'amount' => $d['amount'],
                ]);
            }
        });

        return redirect()->route('cash.index')->with('success', 'Transaksi kas diperbarui.');
    }

    public function destroy(CashTransaction $cash)
    {
        $cash->delete();
        return redirect()->route('cash.index')->with('success', 'Transaksi kas dihapus.');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
        ]);

        $start = Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay();

        $items = CashTransaction::with('details')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'asc')
            ->get();

        $filename = 'cash_' . $validated['start_date'] . '_' . $validated['end_date'] . '.csv';
        $handle = fopen('php://memory', 'w');

        // Set UTF-8 BOM for Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");

        // header
        fputcsv($handle, [
            'ID', 'Tanggal', 'Tipe', 'Kategori', 'Referensi', 'Catatan', 'Deskripsi Detail', 'Jumlah', 'Total Transaksi'
        ], ';');

        foreach ($items as $item) {
            if ($item->details->isEmpty()) {
                fputcsv($handle, [
                    $item->id,
                    $item->date->format('Y-m-d H:i:s'),
                    ($item->type === 'in' ? 'Masuk' : 'Keluar'),
                    $item->category ?? '-',
                    $item->reference ?? '-',
                    $item->notes ?? '-',
                    '-',
                    (int)$item->total,
                    (int)$item->total,
                ], ';');
            } else {
                $first = true;
                foreach ($item->details as $d) {
                    fputcsv($handle, [
                        $first ? $item->id : '',
                        $first ? $item->date->format('Y-m-d H:i:s') : '',
                        $first ? ($item->type === 'in' ? 'Masuk' : 'Keluar') : '',
                        $first ? $item->category ?? '-' : '',
                        $first ? $item->reference ?? '-' : '',
                        $first ? $item->notes ?? '-' : '',
                        $d->description ?? '-',
                        (int)$d->amount,
                        $first ? (int)$item->total : '',
                    ], ';');
                    $first = false;
                }
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
