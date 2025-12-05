<?php

namespace App\Http\Controllers;

use App\Models\Affal;
use App\Models\AffalsTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffalsController extends Controller
{
    public function index()
    {
        $transactions = AffalsTransaction::with(['product', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $affal = Affal::getInstance();

        return view('affals.index', compact('transactions', 'affal'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        $affal = Affal::getInstance();

        return view('affals.create', compact('products', 'affal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_moved' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['qty_moved']) {
            return back()->withInput()->with('error', "Stok produk {$product->name} tidak cukup. Stok tersedia: {$product->stock}");
        }

        DB::beginTransaction();
        try {
            // Kurangi stok produk
            $product->update(['stock' => $product->stock - $validated['qty_moved']]);

            // Tambah stok affal
            $affal = Affal::getInstance();
            $affal->update(['qty_stock' => $affal->qty_stock + $validated['qty_moved']]);

            // Catat transaksi
            AffalsTransaction::create([
                'product_id' => $validated['product_id'],
                'qty_moved' => $validated['qty_moved'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('affals.index')->with('success', "Berhasil mengeluarkan {$validated['qty_moved']} unit produk {$product->name} ke affal.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // SHOW edit form
    public function edit($id)
    {
        $transaction = AffalsTransaction::findOrFail($id);
        $products = Product::where('stock', '>=', 0)->get(); // show all products so user can change product if needed
        $affal = Affal::getInstance();

        return view('affals.edit', compact('transaction', 'products', 'affal'));
    }

    // UPDATE transaction
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty_moved' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $transaction = AffalsTransaction::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldQty = (int) $transaction->qty_moved;
            $oldProductId = $transaction->product_id;
            $newQty = (int) $validated['qty_moved'];
            $newProductId = (int) $validated['product_id'];

            // If same product: restore old stock then check availability for new qty
            if ($oldProductId === $newProductId) {
                $product = Product::findOrFail($newProductId);
                // Put back oldQty first
                $product->stock += $oldQty;
                // Then deduct newQty
                if ($product->stock < $newQty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$product->name} tidak cukup untuk update. Tersedia setelah restore: {$product->stock}");
                }
                $product->stock -= $newQty;
                $product->save();
            } else {
                // Different products: restore to old product and deduct from new product
                $oldProduct = Product::findOrFail($oldProductId);
                $oldProduct->stock += $oldQty;
                $oldProduct->save();

                $newProduct = Product::findOrFail($newProductId);
                if ($newProduct->stock < $newQty) {
                    DB::rollBack();
                    return back()->withInput()->with('error', "Stok produk {$newProduct->name} tidak cukup. Stok tersedia: {$newProduct->stock}");
                }
                $newProduct->stock -= $newQty;
                $newProduct->save();
            }

            // Adjust affal stock: add (newQty - oldQty)
            $affal = Affal::getInstance();
            $delta = $newQty - $oldQty;
            $newAffalQty = $affal->qty_stock + $delta;
            if ($newAffalQty < 0) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Tidak dapat mengurangi stok affal di bawah 0. Periksa kembali transaksi.');
            }
            $affal->update(['qty_stock' => $newAffalQty]);

            // Update transaction record
            $transaction->update([
                'product_id' => $newProductId,
                'qty_moved' => $newQty,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('affals.index')->with('success', 'Transaksi affal berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // DELETE transaction
    public function destroy($id)
    {
        $transaction = AffalsTransaction::findOrFail($id);

        DB::beginTransaction();
        try {
            $qty = (int) $transaction->qty_moved;
            $product = Product::findOrFail($transaction->product_id);
            $affal = Affal::getInstance();

            // Check affal has enough stock to remove
            if ($affal->qty_stock < $qty) {
                DB::rollBack();
                return redirect()->route('affals.index')->with('error', "Stok affal ({$affal->qty_stock}) kurang untuk menghapus transaksi ({$qty}). Sesuaikan stok affal terlebih dahulu.");
            }

            // Restore product stock
            $product->stock += $qty;
            $product->save();

            // Decrease affal stock
            $affal->update(['qty_stock' => $affal->qty_stock - $qty]);

            // Delete transaction
            $transaction->delete();

            DB::commit();
            return redirect()->route('affals.index')->with('success', 'Transaksi affal berhasil dihapus dan stok dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('affals.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}