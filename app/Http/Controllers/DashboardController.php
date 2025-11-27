<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Presence;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display stats.
     */
    public function index()
    {
        // Jumlah items (products)
        $totalItems = Product::count();

        // Jumlah categories
        $totalCategories = Category::count();

        // Jumlah customers
        $totalCustomers = Customer::count();

        // Total penjualan bulan ini
        $monthlySalesCount = Sale::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        // Best selling products (top 5 by quantity sold this month)
        $bestSellingProducts = DB::table('sale_details as sd')
            ->join('sales as s', 'sd.sale_id', 's.id')
            ->join('products as p', 'sd.product_id', 'p.id')
            ->whereBetween('s.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('p.id', 'p.name', DB::raw('SUM(sd.quantity) as total_qty'), DB::raw('SUM(sd.subtotal) as total_value'))
            ->groupBy('p.id', 'p.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        // Top customers (by total purchase value this month)
        $topCustomers = DB::table('sales as s')
            ->join('customers as c', 's.customer_id', 'c.id')
            ->whereBetween('s.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('c.id', 'c.name', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(s.total_price) as total_spent'))
            ->groupBy('c.id', 'c.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('totalItems', 'totalCategories', 'totalCustomers', 'monthlySalesCount', 'bestSellingProducts', 'topCustomers'));
    }

    public function presence()
    {
        $data = Presence::where('status', 'present')
                ->selectRaw('MONTH(date) as month, YEAR(date) as year, COUNT(*) as total_present')
                ->groupBy('year', 'month')
                ->orderBy('month', 'asc')
                ->get();

        $temp = [];
        $i = 0;

        foreach ($data as $item) {
            $temp[$i] = $item->total_present;
            $i++;
        }

        return response()->json($temp);
    }
}