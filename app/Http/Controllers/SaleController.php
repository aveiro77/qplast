<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Do not apply date filter for table display. The date filter is used only for export.
        $sales = Sale::with(['customer', 'saleDetails.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        // Load products and customers for the create form
        $products = Product::all()->map(function ($p) {
            $file = $p->foto ?? $p->photo ?? $p->image ?? $p->foto_produk;
            $p->image = $file ? asset('storage/' . $file) : asset('storage/noimage.jpg');
            return $p;
        });

        $customers = Customer::all();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'nullable|string|max:191',
            'note' => 'nullable|string',
            'items' => 'required|string', // JSON string from client
        ]);

        $items = json_decode($validated['items'], true);
        if (!is_array($items) || count($items) === 0) {
            return back()->withInput()->withErrors(['items' => 'No items provided.']);
        }

        // Validate each item
        $lineErrors = [];
        foreach ($items as $i => $it) {
            if (empty($it['product_id']) || !is_numeric($it['product_id'])) {
                $lineErrors["items.$i.product_id"] = 'Product is required.';
                continue;
            }
            if (empty($it['quantity']) || !is_numeric($it['quantity']) || $it['quantity'] <= 0) {
                $lineErrors["items.$i.quantity"] = 'Quantity must be greater than zero.';
            }
            if (!isset($it['price']) || !is_numeric($it['price']) || $it['price'] < 0) {
                $lineErrors["items.$i.price"] = 'Price must be zero or greater.';
            }
        }

        if (!empty($lineErrors)) {
            return back()->withInput()->withErrors($lineErrors);
        }

        // Compute totals and check stock (no row-locking as requested)
        $total = 0;
        foreach ($items as $it) {
            $qty = (float) $it['quantity'];
            $price = (float) $it['price'];
            $subtotal = $qty * $price;
            $total += $subtotal;

            $product = Product::find($it['product_id']);
            if (!$product) {
                return back()->withInput()->withErrors(['items' => 'Product not found (ID: ' . $it['product_id'] . ').']);
            }
            if ($product->stock < $qty) {
                return back()->withInput()->withErrors(['items' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, requested: {$qty}"]);
            }
        }

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'name' => $validated['name'] ?? 'Manual Sale',
                'note' => $validated['note'] ?? null,
                'total_price' => $total,
            ]);

            foreach ($items as $it) {
                $qty = (float) $it['quantity'];
                $price = (float) $it['price'];
                $subtotal = $qty * $price;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $qty,
                    'unit' => $it['unit'] ?? null,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Decrement product stock
                $product = Product::find($it['product_id']);
                if ($product) {
                    $product->stock = $product->stock - $qty;
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error saving sale: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
        ]);

        $startDate = Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay();

        $sales = Sale::with(['customer', 'saleDetails.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // Create CSV file
        $filename = 'sales_' . $validated['start_date'] . '_' . $validated['end_date'] . '.csv';
        $handle = fopen('php://memory', 'w');

        // Write header
        fputcsv($handle, [
            'ID',
            'Type',
            'Date',
            'Customer',
            'Product',
            'Quantity',
            'Unit',
            'Price',
            'Subtotal',
            'Total Sale',
        ], ';');

        // Write data rows
        foreach ($sales as $sale) {
            if ($sale->saleDetails->isEmpty()) {
                // Write sale without details
                fputcsv($handle, [
                    '#' . $sale->created_at->format('Ymd') . $sale->id,
                    $sale->name,
                    $sale->created_at->format('Y-m-d H:i:s'),
                    $sale->customer->name ?? '-',
                    '-',
                    '-',
                    '-',
                    '-',
                    '-',
                    $sale->total_price,
                ], ';');
            } else {
                // Write first detail row with sale info
                $isFirst = true;
                foreach ($sale->saleDetails as $detail) {
                    fputcsv($handle, [
                        $isFirst ? '#' . $sale->created_at->format('Ymd') . $sale->id : '',
                        $isFirst ? $sale->name : '',
                        $isFirst ? $sale->created_at->format('Y-m-d H:i:s') : '',
                        $isFirst ? $sale->customer->name ?? '-' : '',
                        $detail->product->name ?? '-',
                        $detail->quantity,
                        $detail->unit ?? '-',
                        $detail->price,
                        $detail->subtotal,
                        $isFirst ? $sale->total_price : '',
                    ], ';');
                    $isFirst = false;
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

    public function edit($id)
    {
        $sale = Sale::with(['customer', 'saleDetails.product'])->findOrFail($id);
        $products = Product::all()->map(function ($p) {
            $file = $p->foto ?? $p->photo ?? $p->image ?? $p->foto_produk;
            $p->image = $file ? asset('storage/' . $file) : asset('storage/noimage.jpg');
            return $p;
        });
        $customers = Customer::all();

        return view('sales.edit', compact('sale', 'products', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'nullable|string|max:191',
            'note' => 'nullable|string',
            'items' => 'required|string',
        ]);

        $items = json_decode($validated['items'], true);
        if (!is_array($items) || count($items) === 0) {
            return back()->withInput()->withErrors(['items' => 'No items provided.']);
        }

        $lineErrors = [];
        foreach ($items as $i => $it) {
            if (empty($it['product_id']) || !is_numeric($it['product_id'])) {
                $lineErrors["items.$i.product_id"] = 'Product is required.';
                continue;
            }
            if (empty($it['quantity']) || !is_numeric($it['quantity']) || $it['quantity'] <= 0) {
                $lineErrors["items.$i.quantity"] = 'Quantity must be greater than zero.';
            }
            if (!isset($it['price']) || !is_numeric($it['price']) || $it['price'] < 0) {
                $lineErrors["items.$i.price"] = 'Price must be zero or greater.';
            }
        }

        if (!empty($lineErrors)) {
            return back()->withInput()->withErrors($lineErrors);
        }

        $total = 0;
        foreach ($items as $it) {
            $qty = (float) $it['quantity'];
            $price = (float) $it['price'];
            $subtotal = $qty * $price;
            $total += $subtotal;

            $product = Product::find($it['product_id']);
            if (!$product) {
                return back()->withInput()->withErrors(['items' => 'Product not found (ID: ' . $it['product_id'] . ').']);
            }

            $oldQtyInSale = 0;
            foreach ($sale->saleDetails as $oldDetail) {
                if ($oldDetail->product_id == $it['product_id']) {
                    $oldQtyInSale += $oldDetail->quantity;
                }
            }
            $availableStock = $product->stock + $oldQtyInSale;
            if ($availableStock < $qty) {
                return back()->withInput()->withErrors(['items' => "Insufficient stock for product: {$product->name}. Available: {$availableStock}, requested: {$qty}"]);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($sale->saleDetails as $oldDetail) {
                $product = Product::find($oldDetail->product_id);
                if ($product) {
                    $product->stock += $oldDetail->quantity;
                    $product->save();
                }
            }

            SaleDetail::where('sale_id', $sale->id)->delete();

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'name' => $validated['name'] ?? 'Manual Sale',
                'note' => $validated['note'] ?? null,
                'total_price' => $total,
            ]);

            foreach ($items as $it) {
                $qty = (float) $it['quantity'];
                $price = (float) $it['price'];
                $subtotal = $qty * $price;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $qty,
                    'unit' => $it['unit'] ?? null,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $product = Product::find($it['product_id']);
                if ($product) {
                    $product->stock -= $qty;
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating sale: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['customer', 'saleDetails.product'])->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    /**
     * Display printable view for a sale (A4 / quarto).
     */
    public function print($id)
    {
        $sale = Sale::with(['customer', 'saleDetails.product'])->findOrFail($id);
        return view('sales.print', compact('sale'));
    }

    /**
     * Delete a sale and its related details, restore stock.
     */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        DB::beginTransaction();
        try {
            // Restore product stock before deleting
            foreach ($sale->saleDetails as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->stock += $detail->quantity;
                    $product->save();
                }
            }

            // Delete sale details
            SaleDetail::where('sale_id', $sale->id)->delete();

            // Delete sale
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale deleted successfully and stock restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }
}
