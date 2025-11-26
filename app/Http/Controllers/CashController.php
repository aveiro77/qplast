<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\CashTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashController extends Controller
{
    public function index()
    {
        $items = CashTransaction::with('details')->orderBy('date', 'desc')->paginate(20);
        return view('cash.index', compact('items'));
    }

    public function create()
    {
        $categories = ['penjualan','modal','operasional'];
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
        $categories = ['penjualan','modal','operasional'];
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
}
