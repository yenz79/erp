<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Attendance;
use App\Models\StockMutation;
use App\Models\User;
use App\Models\Store;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Dashboard utama: ringkasan penjualan, stok, absensi, user, store
        $salesToday = Sale::whereDate('created_at', now()->toDateString())->count();
        $totalSales = Sale::sum('total');
        $productCount = Product::count();
        $attendanceToday = Attendance::whereDate('timestamp', now()->toDateString())->count();
        $stockMutationsToday = StockMutation::whereDate('created_at', now()->toDateString())->count();
        $userCount = User::count();
        $storeCount = Store::count();
        $summary = [
            'sales_today' => $salesToday,
            'total_sales' => $totalSales,
            'product_count' => $productCount,
            'attendance_today' => $attendanceToday,
            'stock_mutations_today' => $stockMutationsToday,
            'user_count' => $userCount,
            'store_count' => $storeCount,
        ];
        if ($request->wantsJson()) {
            return response()->json($summary);
        }
        return Inertia::render('Dashboard', ['summary' => $summary]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
