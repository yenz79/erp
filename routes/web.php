<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\SkinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Skin routes
Route::get('/skin-selector', [SkinController::class, 'index'])->name('skin.selector');
Route::post('/skin/set', [SkinController::class, 'setSkin'])->name('skin.set');

Route::get('/login', [SkinController::class, 'showLogin'])->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }
    
    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput($request->only('email'));
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_products' => \App\Models\Product::count(),
            'total_sales' => \App\Models\Sale::count(),
            'total_stores' => \App\Models\Store::count(),
            'low_stock_products' => \App\Models\Product::where('stock', '<=', 10)->count(),
            'today_mutations' => \App\Models\StockMutation::whereDate('created_at', now()->toDateString())->count(),
            'role_counts' => [
                'admin' => \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Admin'); })->count(),
                'manager' => \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Manager'); })->count(),
                'cashier' => \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Cashier'); })->count(),
            ]
        ];
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Dashboard</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-store me-2"></i>ERP System
                    </span>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i>' . e($user->name) . '
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="' . route('logout') . '" class="d-inline">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-4">
                <div class="row">
                    <!-- Welcome Card -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-wave-square me-2"></i>
                                    Selamat datang, ' . e($user->name) . '!
                                </h4>
                                <p class="card-text text-muted">
                                    Anda login sebagai <strong>' . e($user->role->name ?? 'User') . '</strong> 
                                    di toko <strong>' . e($user->store->name ?? 'Tidak ada toko') . '</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-3"></i>
                                <h3>' . $stats['total_users'] . '</h3>
                                <p class="mb-0">Total Pengguna</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <i class="fas fa-box fa-2x mb-3"></i>
                                <h3>' . $stats['total_products'] . '</h3>
                                <p class="mb-0">Total Produk</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                                <h3>' . $stats['total_sales'] . '</h3>
                                <p class="mb-0">Total Penjualan</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <i class="fas fa-store fa-2x mb-3"></i>
                                <h3>' . $stats['total_stores'] . '</h3>
                                <p class="mb-0">Total Toko</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Stats for Role-based Access -->
                    ' . ($user->hasRole('Admin') || $user->hasRole('Manager') ? '
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <h3>' . $stats['low_stock_products'] . '</h3>
                                <p class="mb-0">Stok Menipis</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-secondary">
                            <div class="card-body text-center">
                                <i class="fas fa-exchange-alt fa-2x mb-3"></i>
                                <h3>' . $stats['today_mutations'] . '</h3>
                                <p class="mb-0">Mutasi Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-dark">
                            <div class="card-body text-center">
                                <i class="fas fa-users-cog fa-2x mb-3"></i>
                                <h3>' . array_sum($stats['role_counts']) . '</h3>
                                <p class="mb-0">Total User Aktif</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card text-white bg-purple" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-center">
                                <i class="fas fa-warehouse fa-2x mb-3"></i>
                                <h3>' . \App\Models\Product::sum('stock') . '</h3>
                                <p class="mb-0">Total Stok Item</p>
                            </div>
                        </div>
                    </div>
                    ' : '') . '
                    
                    <!-- Quick Actions -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="/products" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Produk Baru
                                    </a>
                                    <a href="/pos" class="btn btn-success">
                                        <i class="fas fa-cash-register me-2"></i>Buka POS
                                    </a>
                                    <a href="/attendance" class="btn btn-warning">
                                        <i class="fas fa-clock me-2"></i>Absensi
                                    </a>
                                    ' . ($user->hasRole('Admin') || $user->hasRole('Manager') ? 
                                        '<a href="/warehouse" class="btn btn-secondary">
                                            <i class="fas fa-warehouse me-2"></i>Manajemen Gudang
                                        </a>' : '') . '
                                    ' . ($user->hasRole('Admin') ? 
                                        '<a href="/users" class="btn btn-dark">
                                            <i class="fas fa-users me-2"></i>Manajemen User
                                        </a>' : '') . '
                                    <a href="/reports" class="btn btn-info">
                                        <i class="fas fa-chart-line me-2"></i>Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi System
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><strong>Status Database:</strong> <span class="text-success">✓ Terhubung</span></li>
                                    <li><strong>Laravel Version:</strong> ' . app()->version() . '</li>
                                    <li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>
                                    <li><strong>Waktu Login:</strong> ' . now()->format('d/m/Y H:i:s') . '</li>
                                    <li><strong>Role Anda:</strong> <span class="badge bg-primary">' . e($user->role->name ?? 'No Role') . '</span></li>
                                    <li><strong>Akses Level:</strong> ' . 
                                        ($user->hasRole('Admin') ? '<span class="text-success">Full Access</span>' :
                                        ($user->hasRole('Manager') ? '<span class="text-warning">Management Access</span>' :
                                        '<span class="text-info">Basic Access</span>')) . '
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    ' . ($user->hasRole('Admin') ? '
                    <!-- Admin Only: Role Distribution -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users-cog me-2"></i>Distribusi Role
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="text-success">' . $stats['role_counts']['admin'] . '</h4>
                                        <small>Admin</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-warning">' . $stats['role_counts']['manager'] . '</h4>
                                        <small>Manager</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="text-info">' . $stats['role_counts']['cashier'] . '</h4>
                                        <small>Cashier</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="/users" class="btn btn-dark btn-sm">
                                        <i class="fas fa-users me-2"></i>Kelola User & Role
                                    </a>
                                    <a href="/stores" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-store me-2"></i>Kelola Toko & Gudang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ' : 
                    ($user->hasRole('Manager') ? '
                    <!-- Manager Only: Warehouse Summary -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-warehouse me-2"></i>Ringkasan Gudang
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <h4 class="text-danger">' . $stats['low_stock_products'] . '</h4>
                                        <small>Stok Menipis</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-secondary">' . $stats['today_mutations'] . '</h4>
                                        <small>Mutasi Hari Ini</small>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <a href="/warehouse" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-boxes me-2"></i>Kelola Gudang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ' : '
                    <!-- Cashier Only: POS Quick Access -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-cash-register me-2"></i>Akses Cepat Kasir
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <p class="text-muted">Sistem POS siap digunakan</p>
                                <div class="d-grid gap-2">
                                    <a href="/pos" class="btn btn-success">
                                        <i class="fas fa-cash-register me-2"></i>Buka POS
                                    </a>
                                    <a href="/attendance" class="btn btn-warning">
                                        <i class="fas fa-clock me-2"></i>Absensi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ')) . '
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        ');
    })->name('dashboard');
    
    // Route POS (Point of Sale)
    Route::get('/pos', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Point of Sale</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .barcode-scanner { 
                    border: 2px dashed #007bff; 
                    min-height: 60px; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center;
                    cursor: pointer;
                }
                .cart-item { border-bottom: 1px solid #eee; padding: 10px 0; }
                .total-display { font-size: 2rem; font-weight: bold; }
            </style>
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-success">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-cash-register me-2"></i>POS System
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <!-- Product Scanner & Search -->
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5><i class="fas fa-barcode me-2"></i>Scanner Barcode Produk</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Scan atau Ketik Barcode:</label>
                                        <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode disini..." autofocus>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cari Produk:</label>
                                        <input type="text" id="productSearch" class="form-control" placeholder="Cari nama produk...">
                                    </div>
                                </div>
                                
                                <div class="barcode-scanner mb-3" onclick="document.getElementById(\'barcodeInput\').focus()">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                                        Klik disini atau tekan input barcode untuk mulai scan
                                    </div>
                                </div>
                                
                                <!-- Product List -->
                                <div class="row" id="productList">
                                    <div class="col-md-3 mb-2">
                                        <div class="card h-100 product-card" onclick="addToCart(1, \'Kopi Hitam\', 15000)">
                                            <div class="card-body text-center">
                                                <i class="fas fa-coffee fa-2x mb-2 text-primary"></i>
                                                <h6>Kopi Hitam</h6>
                                                <p class="text-success fw-bold">Rp 15.000</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="card h-100 product-card" onclick="addToCart(2, \'Teh Manis\', 12000)">
                                            <div class="card-body text-center">
                                                <i class="fas fa-leaf fa-2x mb-2 text-success"></i>
                                                <h6>Teh Manis</h6>
                                                <p class="text-success fw-bold">Rp 12.000</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="card h-100 product-card" onclick="addToCart(3, \'Roti Bakar\', 25000)">
                                            <div class="card-body text-center">
                                                <i class="fas fa-bread-slice fa-2x mb-2 text-warning"></i>
                                                <h6>Roti Bakar</h6>
                                                <p class="text-success fw-bold">Rp 25.000</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="card h-100 product-card" onclick="addToCart(4, \'Nasi Goreng\', 35000)">
                                            <div class="card-body text-center">
                                                <i class="fas fa-utensils fa-2x mb-2 text-danger"></i>
                                                <h6>Nasi Goreng</h6>
                                                <p class="text-success fw-bold">Rp 35.000</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart & Checkout -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h5>
                            </div>
                            <div class="card-body">
                                <div id="cartItems" style="max-height: 300px; overflow-y: auto;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                        <p>Keranjang masih kosong</p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pajak (10%):</span>
                                    <span id="tax">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="total-display text-success" id="total">Rp 0</strong>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Uang Dibayar:</label>
                                    <input type="number" id="paymentAmount" class="form-control" placeholder="0" onchange="calculateChange()">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Kembalian:</label>
                                    <input type="text" id="changeAmount" class="form-control" readonly>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-lg" onclick="processPayment()">
                                        <i class="fas fa-credit-card me-2"></i>Proses Pembayaran
                                    </button>
                                    <button class="btn btn-warning" onclick="clearCart()">
                                        <i class="fas fa-trash me-2"></i>Kosongkan Keranjang
                                    </button>
                                    <button class="btn btn-info" onclick="printReceipt()">
                                        <i class="fas fa-print me-2"></i>Cetak Struk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                let cart = [];
                let cartTotal = 0;
                
                function addToCart(id, name, price) {
                    const existingItem = cart.find(item => item.id === id);
                    if (existingItem) {
                        existingItem.qty += 1;
                    } else {
                        cart.push({id, name, price, qty: 1});
                    }
                    updateCart();
                }
                
                function removeFromCart(id) {
                    cart = cart.filter(item => item.id !== id);
                    updateCart();
                }
                
                function updateQuantity(id, qty) {
                    const item = cart.find(item => item.id === id);
                    if (item) {
                        item.qty = parseInt(qty);
                        if (item.qty <= 0) {
                            removeFromCart(id);
                        }
                    }
                    updateCart();
                }
                
                function updateCart() {
                    const cartItems = document.getElementById("cartItems");
                    if (cart.length === 0) {
                        cartItems.innerHTML = `
                            <div class="text-center text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>Keranjang masih kosong</p>
                            </div>
                        `;
                        cartTotal = 0;
                    } else {
                        cartItems.innerHTML = cart.map(item => `
                            <div class="cart-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">${item.name}</h6>
                                        <small class="text-muted">Rp ${item.price.toLocaleString()}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="number" value="${item.qty}" min="1" 
                                               class="form-control form-control-sm me-2" 
                                               style="width: 60px"
                                               onchange="updateQuantity(${item.id}, this.value)">
                                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>Rp ${(item.price * item.qty).toLocaleString()}</strong>
                                </div>
                            </div>
                        `).join("");
                        
                        cartTotal = cart.reduce((total, item) => total + (item.price * item.qty), 0);
                    }
                    
                    const tax = cartTotal * 0.1;
                    const finalTotal = cartTotal + tax;
                    
                    document.getElementById("subtotal").textContent = "Rp " + cartTotal.toLocaleString();
                    document.getElementById("tax").textContent = "Rp " + tax.toLocaleString();
                    document.getElementById("total").textContent = "Rp " + finalTotal.toLocaleString();
                    
                    calculateChange();
                }
                
                function calculateChange() {
                    const paymentAmount = parseFloat(document.getElementById("paymentAmount").value) || 0;
                    const tax = cartTotal * 0.1;
                    const finalTotal = cartTotal + tax;
                    const change = paymentAmount - finalTotal;
                    
                    document.getElementById("changeAmount").value = change >= 0 ? "Rp " + change.toLocaleString() : "Rp 0";
                }
                
                function clearCart() {
                    cart = [];
                    updateCart();
                    document.getElementById("paymentAmount").value = "";
                    document.getElementById("changeAmount").value = "";
                }
                
                function processPayment() {
                    if (cart.length === 0) {
                        alert("Keranjang kosong!");
                        return;
                    }
                    
                    const paymentAmount = parseFloat(document.getElementById("paymentAmount").value) || 0;
                    const tax = cartTotal * 0.1;
                    const finalTotal = cartTotal + tax;
                    
                    if (paymentAmount < finalTotal) {
                        alert("Uang pembayaran kurang!");
                        return;
                    }
                    
                    alert("Pembayaran berhasil!\\nTotal: Rp " + finalTotal.toLocaleString() + "\\nBayar: Rp " + paymentAmount.toLocaleString() + "\\nKembalian: Rp " + (paymentAmount - finalTotal).toLocaleString());
                    
                    // Simpan transaksi (akan diintegrasikan dengan API)
                    clearCart();
                }
                
                function printReceipt() {
                    if (cart.length === 0) {
                        alert("Tidak ada transaksi untuk dicetak!");
                        return;
                    }
                    alert("Fitur cetak struk akan segera tersedia!");
                }
                
                // Barcode scanner simulation
                document.getElementById("barcodeInput").addEventListener("keyup", function(e) {
                    if (e.key === "Enter") {
                        const barcode = this.value;
                        // Simulasi pencarian produk berdasarkan barcode
                        if (barcode === "123456") {
                            addToCart(1, "Kopi Hitam", 15000);
                        } else if (barcode === "123457") {
                            addToCart(2, "Teh Manis", 12000);
                        } else {
                            alert("Produk dengan barcode " + barcode + " tidak ditemukan!");
                        }
                        this.value = "";
                    }
                });
                
                // Product search
                document.getElementById("productSearch").addEventListener("keyup", function() {
                    // Implementasi pencarian produk
                    console.log("Searching for: " + this.value);
                });
            </script>
        </body>
        </html>
        ');
    })->name('pos');
    
    // Route Absensi
    Route::get('/attendance', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        // Ambil data absensi hari ini
        $todayAttendance = \App\Models\Attendance::whereDate('created_at', now()->toDateString())
                                                 ->with(['user', 'store'])
                                                 ->latest()
                                                 ->get();
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Sistem Absensi</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .attendance-card { cursor: pointer; transition: all 0.3s; }
                .attendance-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .time-display { font-size: 2rem; font-weight: bold; }
                .camera-preview { 
                    width: 100%; 
                    height: 300px; 
                    background: #f8f9fa; 
                    border: 2px dashed #dee2e6;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-clock me-2"></i>Sistem Absensi
                    </span>
                    <div class="navbar-nav ms-auto">
                        <span class="nav-link text-dark">
                            <i class="fas fa-calendar me-2"></i>' . now()->format('d F Y') . '
                        </span>
                        <a href="/dashboard" class="nav-link text-dark">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <!-- Absensi Form -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5><i class="fas fa-user-check me-2"></i>Absensi Karyawan</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="time-display text-primary" id="currentTime">
                                        ' . now()->format('H:i:s') . '
                                    </div>
                                    <small class="text-muted">Waktu Saat Ini</small>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="card attendance-card text-center" onclick="recordAttendance(\'in\')">
                                            <div class="card-body">
                                                <i class="fas fa-sign-in-alt fa-3x text-success mb-2"></i>
                                                <h5 class="text-success">Check In</h5>
                                                <small>Absen Masuk</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card attendance-card text-center" onclick="recordAttendance(\'out\')">
                                            <div class="card-body">
                                                <i class="fas fa-sign-out-alt fa-3x text-danger mb-2"></i>
                                                <h5 class="text-danger">Check Out</h5>
                                                <small>Absen Keluar</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Keterangan (Opsional):</label>
                                    <textarea id="attendanceNote" class="form-control" rows="3" placeholder="Tulis keterangan absensi..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Absensi dengan Face Recognition:</label>
                                    <div class="camera-preview" id="cameraPreview">
                                        <div class="text-center">
                                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                            <br>
                                            <button class="btn btn-primary" onclick="startCamera()">
                                                <i class="fas fa-video me-2"></i>Aktifkan Kamera
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Absensi Hari Ini -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-list me-2"></i>Absensi Hari Ini</h5>
                            </div>
                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                ' . ($todayAttendance->count() > 0 ? 
                                    $todayAttendance->map(function($attendance) {
                                        $badgeClass = $attendance->type === 'in' ? 'bg-success' : 'bg-danger';
                                        $icon = $attendance->type === 'in' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
                                        return '
                                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                            <div>
                                                <h6 class="mb-1">' . e($attendance->user->name ?? 'Unknown') . '</h6>
                                                <small class="text-muted">' . e($attendance->store->name ?? 'No Store') . '</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge ' . $badgeClass . ' mb-2">
                                                    <i class="fas ' . $icon . ' me-1"></i>' . 
                                                    ($attendance->type === 'in' ? 'Masuk' : 'Keluar') . '
                                                </span>
                                                <br>
                                                <small>' . $attendance->created_at->format('H:i:s') . '</small>
                                            </div>
                                        </div>';
                                    })->join('') 
                                    : 
                                    '<div class="text-center text-muted">
                                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                        <p>Belum ada absensi hari ini</p>
                                    </div>'
                                ) . '
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-pie me-2"></i>Statistik Absensi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h3 class="text-success">' . $todayAttendance->where('type', 'in')->count() . '</h3>
                                        <small>Check In</small>
                                    </div>
                                    <div class="col-4">
                                        <h3 class="text-danger">' . $todayAttendance->where('type', 'out')->count() . '</h3>
                                        <small>Check Out</small>
                                    </div>
                                    <div class="col-4">
                                        <h3 class="text-info">' . $todayAttendance->count() . '</h3>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Update waktu real-time
                function updateTime() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString("id-ID");
                    document.getElementById("currentTime").textContent = timeString;
                }
                
                setInterval(updateTime, 1000);
                
                function recordAttendance(type) {
                    const note = document.getElementById("attendanceNote").value;
                    const currentTime = new Date().toLocaleTimeString("id-ID");
                    
                    // Simulasi API call
                    const attendanceData = {
                        user_id: ' . $user->id . ',
                        store_id: ' . ($user->store_id ?? 1) . ',
                        type: type,
                        note: note,
                        timestamp: currentTime
                    };
                    
                    // Konfirmasi
                    const typeText = type === "in" ? "Masuk" : "Keluar";
                    const confirmMsg = `Konfirmasi absen ${typeText}?\\nWaktu: ${currentTime}`;
                    
                    if (confirm(confirmMsg)) {
                        alert(`Absen ${typeText} berhasil dicatat!\\nWaktu: ${currentTime}`);
                        
                        // Reset form
                        document.getElementById("attendanceNote").value = "";
                        
                        // Reload halaman setelah 1 detik untuk update data
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                }
                
                function startCamera() {
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({ video: true })
                            .then(function(stream) {
                                const video = document.createElement("video");
                                video.style.width = "100%";
                                video.style.height = "300px";
                                video.style.objectFit = "cover";
                                video.srcObject = stream;
                                video.autoplay = true;
                                
                                const cameraPreview = document.getElementById("cameraPreview");
                                cameraPreview.innerHTML = "";
                                cameraPreview.appendChild(video);
                                
                                // Tambah tombol capture
                                const captureBtn = document.createElement("button");
                                captureBtn.className = "btn btn-success mt-2";
                                captureBtn.innerHTML = "<i class=\"fas fa-camera me-2\"></i>Ambil Foto";
                                captureBtn.onclick = function() {
                                    capturePhoto(video, stream);
                                };
                                cameraPreview.appendChild(captureBtn);
                            })
                            .catch(function(err) {
                                alert("Tidak dapat mengakses kamera: " + err.message);
                            });
                    } else {
                        alert("Browser tidak mendukung akses kamera!");
                    }
                }
                
                function capturePhoto(video, stream) {
                    const canvas = document.createElement("canvas");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext("2d");
                    ctx.drawImage(video, 0, 0);
                    
                    // Stop camera
                    stream.getTracks().forEach(track => track.stop());
                    
                    // Show captured image
                    const img = document.createElement("img");
                    img.src = canvas.toDataURL();
                    img.style.width = "100%";
                    img.style.height = "300px";
                    img.style.objectFit = "cover";
                    
                    const cameraPreview = document.getElementById("cameraPreview");
                    cameraPreview.innerHTML = "";
                    cameraPreview.appendChild(img);
                    
                    // Add retake button
                    const retakeBtn = document.createElement("button");
                    retakeBtn.className = "btn btn-warning mt-2 me-2";
                    retakeBtn.innerHTML = "<i class=\"fas fa-redo me-2\"></i>Foto Ulang";
                    retakeBtn.onclick = startCamera;
                    
                    const useBtn = document.createElement("button");
                    useBtn.className = "btn btn-success mt-2";
                    useBtn.innerHTML = "<i class=\"fas fa-check me-2\"></i>Gunakan Foto";
                    useBtn.onclick = function() {
                        alert("Foto berhasil disimpan untuk absensi!");
                    };
                    
                    cameraPreview.appendChild(retakeBtn);
                    cameraPreview.appendChild(useBtn);
                }
            </script>
        </body>
        </html>
        ');
    })->name('attendance');
    
    // Route Products Management
    Route::get('/products', function () {
        $products = \App\Models\Product::with(['category', 'store'])->paginate(20);
        $categories = \App\Models\Category::all();
        $stores = \App\Models\Store::all();
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Manajemen Produk</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-box me-2"></i>Manajemen Produk
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus me-2"></i>Tambah Produk Baru</h5>
                            </div>
                            <div class="card-body">
                                <form id="productForm">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Produk:</label>
                                        <input type="text" id="productName" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Barcode:</label>
                                        <div class="input-group">
                                            <input type="text" id="productBarcode" class="form-control">
                                            <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()">
                                                <i class="fas fa-random"></i> Generate
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kategori:</label>
                                        <select id="productCategory" class="form-select">
                                            ' . $categories->map(function($cat) {
                                                return '<option value="' . $cat->id . '">' . e($cat->name) . '</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Harga:</label>
                                        <input type="number" id="productPrice" class="form-control" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stok:</label>
                                        <input type="number" id="productStock" class="form-control" min="0">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Simpan Produk
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5><i class="fas fa-list me-2"></i>Daftar Produk</h5>
                                <input type="text" class="form-control w-50" placeholder="Cari produk..." id="searchProduct">
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Barcode</th>
                                                <th>Nama</th>
                                                <th>Kategori</th>
                                                <th>Harga</th>
                                                <th>Stok</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $products->map(function($product) {
                                                return '
                                                <tr>
                                                    <td><code>' . e($product->barcode ?? 'N/A') . '</code></td>
                                                    <td>' . e($product->name) . '</td>
                                                    <td>' . e($product->category->name ?? 'N/A') . '</td>
                                                    <td>Rp ' . number_format($product->price, 0, ',', '.') . '</td>
                                                    <td>' . $product->stock . '</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" onclick="editProduct(' . $product->id . ')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(' . $product->id . ')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>';
                                            })->join('') . '
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                function generateBarcode() {
                    const barcode = Date.now().toString() + Math.floor(Math.random() * 1000);
                    document.getElementById("productBarcode").value = barcode;
                }
                
                document.getElementById("productForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const productData = {
                        name: document.getElementById("productName").value,
                        barcode: document.getElementById("productBarcode").value,
                        category_id: document.getElementById("productCategory").value,
                        price: document.getElementById("productPrice").value,
                        stock: document.getElementById("productStock").value
                    };
                    
                    // Simulasi API call
                    alert("Produk berhasil ditambahkan!\\n" + JSON.stringify(productData, null, 2));
                    
                    // Reset form
                    this.reset();
                    
                    // Reload halaman
                    setTimeout(() => location.reload(), 1000);
                });
                
                function editProduct(id) {
                    alert("Edit produk ID: " + id + "\\nFitur akan segera tersedia!");
                }
                
                function deleteProduct(id) {
                    if (confirm("Yakin ingin menghapus produk ini?")) {
                        alert("Produk ID " + id + " berhasil dihapus!");
                        location.reload();
                    }
                }
                
                document.getElementById("searchProduct").addEventListener("keyup", function() {
                    // Implementasi search
                    console.log("Searching: " + this.value);
                });
            </script>
        </body>
        </html>
        ');
    })->name('products');
    
    // Route Reports
    Route::get('/reports', function () {
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Laporan</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-info">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-chart-line me-2"></i>Laporan Sistem
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                <h5>Laporan Penjualan</h5>
                                <button class="btn btn-primary" onclick="alert(\'Laporan penjualan akan segera tersedia!\')">
                                    Lihat Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-boxes fa-3x text-success mb-3"></i>
                                <h5>Laporan Stok</h5>
                                <button class="btn btn-success" onclick="alert(\'Laporan stok akan segera tersedia!\')">
                                    Lihat Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x text-warning mb-3"></i>
                                <h5>Laporan Absensi</h5>
                                <button class="btn btn-warning" onclick="alert(\'Laporan absensi akan segera tersedia!\')">
                                    Lihat Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill fa-3x text-danger mb-3"></i>
                                <h5>Laporan Keuangan</h5>
                                <button class="btn btn-danger" onclick="alert(\'Laporan keuangan akan segera tersedia!\')">
                                    Lihat Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        ');
    })->name('reports');
    
    // Route User Management
    Route::get('/users', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        // Check if user has permission
        if (!$user->hasRole('Admin') && !$user->hasRole('Manager')) {
            return redirect('/dashboard')->with('error', 'Akses ditolak!');
        }
        
        $users = \App\Models\User::with(['role', 'store'])->get();
        $roles = \App\Models\Role::all();
        $stores = \App\Models\Store::all();
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Manajemen User</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                .face-capture-area {
                    border: 2px dashed #dee2e6;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    background: #f8f9fa;
                }
                .camera-preview {
                    width: 100%;
                    max-width: 300px;
                    height: 300px;
                    border: 2px solid #dee2e6;
                    border-radius: 8px;
                    background: #000;
                    margin: 0 auto;
                    position: relative;
                    overflow: hidden;
                }
                #video {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .face-status {
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    right: 10px;
                    background: rgba(0,0,0,0.7);
                    color: white;
                    padding: 5px;
                    border-radius: 4px;
                    font-size: 12px;
                }
            </style>
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-users me-2"></i>Manajemen User & Role
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <!-- Form Add User -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h5>
                            </div>
                            <div class="card-body">
                                <form id="userForm">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap:</label>
                                        <input type="text" id="userName" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email:</label>
                                        <input type="email" id="userEmail" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password:</label>
                                        <input type="password" id="userPassword" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role:</label>
                                        <select id="userRole" class="form-select" required>
                                            ' . $roles->map(function($role) {
                                                return '<option value="' . $role->id . '">' . e($role->name) . ' - ' . e($role->description) . '</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Toko:</label>
                                        <select id="userStore" class="form-select" required>
                                            ' . $stores->map(function($store) {
                                                return '<option value="' . $store->id . '">' . e($store->name) . ' (' . e($store->type) . ')</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nomor HP:</label>
                                        <input type="text" id="userPhone" class="form-control">
                                    </div>
                                    
                                    <!-- Face Recognition Setup -->
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-camera me-2"></i>Perekaman Wajah (Opsional):
                                        </label>
                                        <div class="face-capture-area">
                                            <div class="camera-preview" id="facePreview">
                                                <div class="text-center p-4">
                                                    <i class="fas fa-user-circle fa-3x text-muted mb-2"></i>
                                                    <br>
                                                    <button type="button" class="btn btn-info btn-sm" onclick="startFaceCapture()">
                                                        <i class="fas fa-camera me-2"></i>Rekam Wajah
                                                    </button>
                                                </div>
                                                <video id="faceVideo" style="display: none;"></video>
                                                <canvas id="faceCanvas" style="display: none;"></canvas>
                                                <div class="face-status" id="faceStatus" style="display: none;">
                                                    <i class="fas fa-search me-1"></i>Mendeteksi wajah...
                                                </div>
                                            </div>
                                            <small class="text-muted mt-2 d-block">
                                                Face recognition untuk absensi otomatis. Pastikan wajah terlihat jelas dan pencahayaan cukup.
                                            </small>
                                        </div>
                                        <input type="hidden" id="faceData" name="face_data">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Simpan User
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Role Permissions -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-shield-alt me-2"></i>Role & Permissions</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="text-success">👑 Admin</h6>
                                    <small class="text-muted">Full access ke semua fitur</small>
                                    <ul class="list-unstyled ms-3">
                                        <li><i class="fas fa-check text-success"></i> Dashboard</li>
                                        <li><i class="fas fa-check text-success"></i> POS System</li>
                                        <li><i class="fas fa-check text-success"></i> Manajemen Produk</li>
                                        <li><i class="fas fa-check text-success"></i> Manajemen User</li>
                                        <li><i class="fas fa-check text-success"></i> Laporan</li>
                                        <li><i class="fas fa-check text-success"></i> Gudang</li>
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-warning">👨‍💼 Manager</h6>
                                    <small class="text-muted">Akses manajemen & laporan</small>
                                    <ul class="list-unstyled ms-3">
                                        <li><i class="fas fa-check text-success"></i> Dashboard</li>
                                        <li><i class="fas fa-times text-danger"></i> POS System</li>
                                        <li><i class="fas fa-check text-success"></i> Manajemen Produk</li>
                                        <li><i class="fas fa-times text-danger"></i> Manajemen User</li>
                                        <li><i class="fas fa-check text-success"></i> Laporan</li>
                                        <li><i class="fas fa-check text-success"></i> Gudang</li>
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-info">👨‍💻 Cashier</h6>
                                    <small class="text-muted">Akses POS & dashboard</small>
                                    <ul class="list-unstyled ms-3">
                                        <li><i class="fas fa-check text-success"></i> Dashboard</li>
                                        <li><i class="fas fa-check text-success"></i> POS System</li>
                                        <li><i class="fas fa-times text-danger"></i> Manajemen Produk</li>
                                        <li><i class="fas fa-times text-danger"></i> Manajemen User</li>
                                        <li><i class="fas fa-times text-danger"></i> Laporan</li>
                                        <li><i class="fas fa-times text-danger"></i> Gudang</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User List -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5><i class="fas fa-list me-2"></i>Daftar User</h5>
                                <input type="text" class="form-control w-50" placeholder="Cari user..." id="searchUser">
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Toko</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $users->map(function($usr) {
                                                $roleColors = [
                                                    'Admin' => 'success',
                                                    'Manager' => 'warning', 
                                                    'Cashier' => 'info'
                                                ];
                                                $roleColor = $roleColors[$usr->role->name ?? 'default'] ?? 'secondary';
                                                $statusColor = $usr->status ? 'success' : 'danger';
                                                $statusText = $usr->status ? 'Aktif' : 'Nonaktif';
                                                
                                                return '
                                                <tr>
                                                    <td>
                                                        <strong>' . e($usr->name) . '</strong><br>
                                                        <small class="text-muted">' . e($usr->phone ?? 'No Phone') . '</small>
                                                    </td>
                                                    <td>' . e($usr->email) . '</td>
                                                    <td>
                                                        <span class="badge bg-' . $roleColor . '">' . e($usr->role->name ?? 'No Role') . '</span>
                                                    </td>
                                                    <td>' . e($usr->store->name ?? 'No Store') . '</td>
                                                    <td>
                                                        <span class="badge bg-' . $statusColor . '">' . $statusText . '</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" onclick="editUser(' . $usr->id . ')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(' . $usr->id . ')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-info" onclick="resetPassword(' . $usr->id . ')">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    </td>
                                                </tr>';
                                            })->join('') . '
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                let faceVideo = null;
                let faceCanvas = null;
                let faceContext = null;
                let faceStream = null;
                let faceDetectionActive = false;
                
                // Initialize face recognition components
                function initializeFaceRecognition() {
                    faceVideo = document.getElementById("faceVideo");
                    faceCanvas = document.getElementById("faceCanvas");
                    faceContext = faceCanvas.getContext("2d");
                    
                    faceCanvas.width = 300;
                    faceCanvas.height = 300;
                }
                
                // Start face capture
                async function startFaceCapture() {
                    try {
                        initializeFaceRecognition();
                        
                        const constraints = {
                            video: {
                                width: 300,
                                height: 300,
                                facingMode: "user"
                            }
                        };
                        
                        faceStream = await navigator.mediaDevices.getUserMedia(constraints);
                        faceVideo.srcObject = faceStream;
                        faceVideo.style.display = "block";
                        
                        document.getElementById("faceStatus").style.display = "block";
                        document.getElementById("faceStatus").innerHTML = \'<i class="fas fa-camera me-1"></i>Kamera aktif - Posisikan wajah di tengah\';
                        
                        faceVideo.play();
                        
                        // Add capture button
                        const previewArea = document.getElementById("facePreview");
                        let captureBtn = document.getElementById("captureBtn");
                        if (!captureBtn) {
                            captureBtn = document.createElement("button");
                            captureBtn.id = "captureBtn";
                            captureBtn.type = "button";
                            captureBtn.className = "btn btn-success btn-sm mt-2";
                            captureBtn.innerHTML = \'<i class="fas fa-camera me-1"></i>Ambil Foto\';
                            captureBtn.onclick = captureFace;
                            previewArea.appendChild(captureBtn);
                        }
                        
                        let stopBtn = document.getElementById("stopBtn");
                        if (!stopBtn) {
                            stopBtn = document.createElement("button");
                            stopBtn.id = "stopBtn";
                            stopBtn.type = "button";
                            stopBtn.className = "btn btn-danger btn-sm mt-2 ms-2";
                            stopBtn.innerHTML = \'<i class="fas fa-stop me-1"></i>Stop\';
                            stopBtn.onclick = stopFaceCapture;
                            previewArea.appendChild(stopBtn);
                        }
                        
                    } catch (error) {
                        console.error("Error accessing camera:", error);
                        alert("Gagal mengakses kamera. Pastikan kamera tersedia dan izinkan akses kamera.");
                    }
                }
                
                // Capture face
                function captureFace() {
                    if (faceVideo && faceCanvas && faceContext) {
                        faceContext.drawImage(faceVideo, 0, 0, 300, 300);
                        
                        const faceDataURL = faceCanvas.toDataURL("image/jpeg", 0.8);
                        document.getElementById("faceData").value = faceDataURL;
                        
                        // Show success message
                        document.getElementById("faceStatus").innerHTML = \'<i class="fas fa-check me-1"></i>Wajah berhasil direkam!\';
                        document.getElementById("faceStatus").className = "face-status bg-success";
                        
                        // Hide video, show captured image
                        faceVideo.style.display = "none";
                        faceCanvas.style.display = "block";
                        
                        alert("Wajah berhasil direkam! Data wajah akan disimpan untuk sistem absensi.");
                    }
                }
                
                // Stop face capture
                function stopFaceCapture() {
                    if (faceStream) {
                        faceStream.getTracks().forEach(track => track.stop());
                        faceStream = null;
                    }
                    
                    if (faceVideo) {
                        faceVideo.style.display = "none";
                        faceVideo.srcObject = null;
                    }
                    
                    if (faceCanvas) {
                        faceCanvas.style.display = "none";
                    }
                    
                    document.getElementById("faceStatus").style.display = "none";
                    
                    // Remove buttons
                    const captureBtn = document.getElementById("captureBtn");
                    const stopBtn = document.getElementById("stopBtn");
                    if (captureBtn) captureBtn.remove();
                    if (stopBtn) stopBtn.remove();
                    
                    // Reset preview
                    const preview = document.getElementById("facePreview");
                    preview.innerHTML = `
                        <div class="text-center p-4">
                            <i class="fas fa-user-circle fa-3x text-muted mb-2"></i>
                            <br>
                            <button type="button" class="btn btn-info btn-sm" onclick="startFaceCapture()">
                                <i class="fas fa-camera me-2"></i>Rekam Wajah
                            </button>
                        </div>
                    `;
                }
                
                document.getElementById("userForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const userData = {
                        name: document.getElementById("userName").value,
                        email: document.getElementById("userEmail").value,
                        password: document.getElementById("userPassword").value,
                        role_id: document.getElementById("userRole").value,
                        store_id: document.getElementById("userStore").value,
                        phone: document.getElementById("userPhone").value,
                        face_data: document.getElementById("faceData").value
                    };
                    
                    console.log("User Data with Face:", userData);
                    
                    let message = "User berhasil ditambahkan!\\n\\n";
                    message += "Nama: " + userData.name + "\\n";
                    message += "Email: " + userData.email + "\\n";
                    message += "Role: " + document.getElementById("userRole").selectedOptions[0].text + "\\n";
                    message += "Store: " + document.getElementById("userStore").selectedOptions[0].text + "\\n";
                    if (userData.face_data) {
                        message += "Face Recognition: ✅ Tersimpan\\n";
                    }
                    
                    alert(message);
                    
                    // Stop face capture if active
                    stopFaceCapture();
                    
                    this.reset();
                    document.getElementById("faceData").value = "";
                    
                    setTimeout(() => location.reload(), 1000);
                });
                
                function editUser(id) {
                    alert("Edit user ID: " + id + "\\nFitur akan segera tersedia!");
                }
                
                function deleteUser(id) {
                    if (confirm("Yakin ingin menghapus user ini?")) {
                        alert("User ID " + id + " berhasil dihapus!");
                        location.reload();
                    }
                }
                
                function resetPassword(id) {
                    if (confirm("Reset password user ini ke default?")) {
                        alert("Password user ID " + id + " berhasil direset!");
                    }
                }
            </script>
        </body>
        </html>
        ');
    })->name('users');
    
    // Route Warehouse/Gudang Management
    Route::get('/warehouse', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        // Check permission
        if (!$user->hasRole('Admin') && !$user->hasRole('Manager')) {
            return redirect('/dashboard')->with('error', 'Akses ditolak!');
        }
        
        $stockMutations = \App\Models\StockMutation::with(['product', 'fromStore', 'toStore', 'user'])
                                                  ->latest()
                                                  ->take(50)
                                                  ->get();
        $products = \App\Models\Product::with('category')->get();
        $stores = \App\Models\Store::all();
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Manajemen Gudang</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-warehouse me-2"></i>Manajemen Gudang
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="container-fluid mt-3">
                <div class="row">
                    <!-- Stock Mutation Form -->
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5><i class="fas fa-exchange-alt me-2"></i>Mutasi Stok</h5>
                            </div>
                            <div class="card-body">
                                <form id="stockMutationForm">
                                    <div class="mb-3">
                                        <label class="form-label">Produk:</label>
                                        <select id="productId" class="form-select" required>
                                            <option value="">Pilih Produk...</option>
                                            ' . $products->map(function($product) {
                                                return '<option value="' . $product->id . '">' . e($product->name) . ' (Stok: ' . $product->stock . ')</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Mutasi:</label>
                                        <select id="mutationType" class="form-select" required onchange="toggleStoreFields()">
                                            <option value="">Pilih Tipe...</option>
                                            <option value="in">Stok Masuk</option>
                                            <option value="out">Stok Keluar</option>
                                            <option value="transfer">Transfer Antar Toko</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="fromStoreField" style="display: none;">
                                        <label class="form-label">Dari Toko:</label>
                                        <select id="fromStoreId" class="form-select">
                                            <option value="">Pilih Toko Asal...</option>
                                            ' . $stores->map(function($store) {
                                                return '<option value="' . $store->id . '">' . e($store->name) . '</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3" id="toStoreField" style="display: none;">
                                        <label class="form-label">Ke Toko:</label>
                                        <select id="toStoreId" class="form-select">
                                            <option value="">Pilih Toko Tujuan...</option>
                                            ' . $stores->map(function($store) {
                                                return '<option value="' . $store->id . '">' . e($store->name) . '</option>';
                                            })->join('') . '
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Quantity:</label>
                                        <input type="number" id="quantity" class="form-control" min="1" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan:</label>
                                        <textarea id="note" class="form-control" rows="3" placeholder="Keterangan mutasi..."></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Proses Mutasi
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Stock Alert -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Stok Menipis</h5>
                            </div>
                            <div class="card-body">
                                ' . $products->filter(function($product) {
                                    return $product->stock <= 10;
                                })->map(function($product) {
                                    return '
                                    <div class="alert alert-warning py-2 mb-2">
                                        <strong>' . e($product->name) . '</strong><br>
                                        <small>Stok tersisa: ' . $product->stock . '</small>
                                    </div>';
                                })->join('') . '
                                ' . ($products->filter(function($p) { return $p->stock <= 10; })->count() == 0 ? 
                                    '<div class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p>Semua stok aman</p>
                                    </div>' : '') . '
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock Mutation History -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5><i class="fas fa-history me-2"></i>Riwayat Mutasi Stok</h5>
                                <div>
                                    <button class="btn btn-success btn-sm" onclick="exportMutations()">
                                        <i class="fas fa-download me-1"></i>Export
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                    <table class="table table-striped table-sm">
                                        <thead class="sticky-top bg-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Produk</th>
                                                <th>Tipe</th>
                                                <th>Dari</th>
                                                <th>Ke</th>
                                                <th>Qty</th>
                                                <th>User</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ' . $stockMutations->map(function($mutation) {
                                                $typeColors = [
                                                    'in' => 'success',
                                                    'out' => 'danger',
                                                    'transfer' => 'warning'
                                                ];
                                                $typeColor = $typeColors[$mutation->type] ?? 'secondary';
                                                $typeText = [
                                                    'in' => 'Masuk',
                                                    'out' => 'Keluar', 
                                                    'transfer' => 'Transfer'
                                                ][$mutation->type] ?? $mutation->type;
                                                
                                                return '
                                                <tr>
                                                    <td>' . $mutation->created_at->format('d/m/Y H:i') . '</td>
                                                    <td>
                                                        <strong>' . e($mutation->product->name ?? 'N/A') . '</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-' . $typeColor . '">' . $typeText . '</span>
                                                    </td>
                                                    <td>' . e($mutation->fromStore->name ?? '-') . '</td>
                                                    <td>' . e($mutation->toStore->name ?? '-') . '</td>
                                                    <td><strong>' . $mutation->qty . '</strong></td>
                                                    <td>' . e($mutation->user->name ?? 'N/A') . '</td>
                                                    <td>
                                                        <small>' . e($mutation->note ?? '-') . '</small>
                                                    </td>
                                                </tr>';
                                            })->join('') . '
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                function toggleStoreFields() {
                    const type = document.getElementById("mutationType").value;
                    const fromField = document.getElementById("fromStoreField");
                    const toField = document.getElementById("toStoreField");
                    
                    fromField.style.display = "none";
                    toField.style.display = "none";
                    
                    if (type === "transfer") {
                        fromField.style.display = "block";
                        toField.style.display = "block";
                    } else if (type === "in") {
                        toField.style.display = "block";
                    } else if (type === "out") {
                        fromField.style.display = "block";
                    }
                }
                
                document.getElementById("stockMutationForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const mutationData = {
                        product_id: document.getElementById("productId").value,
                        type: document.getElementById("mutationType").value,
                        from_store_id: document.getElementById("fromStoreId").value,
                        to_store_id: document.getElementById("toStoreId").value,
                        qty: document.getElementById("quantity").value,
                        note: document.getElementById("note").value,
                        user_id: ' . $user->id . '
                    };
                    
                    alert("Mutasi stok berhasil diproses!\\n" + JSON.stringify(mutationData, null, 2));
                    this.reset();
                    toggleStoreFields();
                    setTimeout(() => location.reload(), 1000);
                });
                
                function exportMutations() {
                    alert("Export mutasi stok akan segera tersedia!");
                }
            </script>
        </body>
        </html>
        ');
    })->name('warehouse');

    // Route Store Management (Toko/Gudang)
    Route::get('/stores', function () {
        $user = Auth::user();
        $user->load('role', 'store');
        
        // Check if user has permission
        if (!$user->hasRole('Admin')) {
            return redirect('/dashboard')->with('error', 'Akses ditolak! Hanya Admin yang dapat mengelola toko/gudang.');
        }
        
        $stores = \App\Models\Store::all();
        
        return response('
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ERP System - Manajemen Toko & Gudang</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
            <style>
                #map { height: 300px; width: 100%; }
                .location-card { border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
                .current-location { background-color: #e8f5e8; border-color: #28a745; }
            </style>
        </head>
        <body class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <span class="navbar-brand">
                        <i class="fas fa-store me-2"></i>Manajemen Toko & Gudang
                    </span>
                    <div class="navbar-nav ms-auto">
                        <a href="/dashboard" class="nav-link text-white">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid py-4">
                <div class="row">
                    <!-- Form Add Store -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus me-2"></i>Tambah Toko/Gudang Baru</h5>
                            </div>
                            <div class="card-body">
                                <form id="storeForm">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Toko/Gudang:</label>
                                        <input type="text" id="storeName" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tipe:</label>
                                        <select id="storeType" class="form-select" required>
                                            <option value="store">Toko</option>
                                            <option value="warehouse">Gudang</option>
                                            <option value="office">Kantor</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alamat:</label>
                                        <textarea id="storeAddress" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nomor Telepon:</label>
                                        <input type="text" id="storePhone" class="form-control">
                                    </div>
                                    
                                    <!-- Location Picker -->
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Titik Lokasi:
                                        </label>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-info btn-sm w-100" onclick="getCurrentLocation()">
                                                <i class="fas fa-crosshairs me-2"></i>Gunakan Lokasi Saat Ini
                                            </button>
                                        </div>
                                        <div id="map" class="mb-2"></div>
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label">Latitude:</label>
                                                <input type="number" id="storeLatitude" class="form-control" step="any" readonly>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Longitude:</label>
                                                <input type="number" id="storeLongitude" class="form-control" step="any" readonly>
                                            </div>
                                        </div>
                                        <small class="text-muted">Klik pada peta untuk memilih lokasi atau gunakan lokasi saat ini</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Simpan Toko/Gudang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Store List -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-list me-2"></i>Daftar Toko & Gudang</h5>
                                <span class="badge bg-primary">' . $stores->count() . ' Lokasi</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    ' . $stores->map(function($store) {
                                        $typeIcons = [
                                            'store' => 'fa-store',
                                            'warehouse' => 'fa-warehouse', 
                                            'office' => 'fa-building'
                                        ];
                                        $typeColors = [
                                            'store' => 'text-success',
                                            'warehouse' => 'text-primary',
                                            'office' => 'text-warning'
                                        ];
                                        $icon = $typeIcons[$store->type] ?? 'fa-map-marker-alt';
                                        $color = $typeColors[$store->type] ?? 'text-secondary';
                                        
                                        return '
                                        <div class="col-md-6 mb-3">
                                            <div class="location-card">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">
                                                        <i class="fas ' . $icon . ' ' . $color . ' me-2"></i>
                                                        ' . e($store->name) . '
                                                    </h6>
                                                    <span class="badge bg-' . ($store->type === 'store' ? 'success' : ($store->type === 'warehouse' ? 'primary' : 'warning')) . '">' . ucfirst($store->type) . '</span>
                                                </div>
                                                <p class="text-muted small mb-2">' . e($store->address) . '</p>
                                                ' . ($store->phone ? '<p class="text-muted small mb-2"><i class="fas fa-phone me-1"></i>' . e($store->phone) . '</p>' : '') . '
                                                ' . ($store->latitude && $store->longitude ? '
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    ' . $store->latitude . ', ' . $store->longitude . '
                                                    <a href="https://maps.google.com/?q=' . $store->latitude . ',' . $store->longitude . '" target="_blank" class="ms-2">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </p>' : '') . '
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editStore(' . $store->id . ')">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewOnMap(' . ($store->latitude ?: 0) . ', ' . ($store->longitude ?: 0) . ')">
                                                        <i class="fas fa-map me-1"></i>Lihat
                                                    </button>
                                                </div>
                                            </div>
                                        </div>';
                                    })->join('') . '
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Initialize Map
                let map = L.map("map").setView([-6.2088, 106.8456], 10); // Default Jakarta
                let marker = null;
                
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "© OpenStreetMap contributors"
                }).addTo(map);
                
                // Map click event
                map.on("click", function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    
                    document.getElementById("storeLatitude").value = lat.toFixed(6);
                    document.getElementById("storeLongitude").value = lng.toFixed(6);
                    
                    if (marker) {
                        map.removeLayer(marker);
                    }
                    
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup("Lokasi Terpilih<br>Lat: " + lat.toFixed(6) + "<br>Lng: " + lng.toFixed(6))
                        .openPopup();
                });
                
                // Get current location
                function getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            document.getElementById("storeLatitude").value = lat.toFixed(6);
                            document.getElementById("storeLongitude").value = lng.toFixed(6);
                            
                            map.setView([lat, lng], 15);
                            
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            
                            marker = L.marker([lat, lng]).addTo(map)
                                .bindPopup("Lokasi Saat Ini<br>Lat: " + lat.toFixed(6) + "<br>Lng: " + lng.toFixed(6))
                                .openPopup();
                        }, function() {
                            alert("Gagal mendapatkan lokasi. Pastikan GPS aktif dan izinkan akses lokasi.");
                        });
                    } else {
                        alert("Browser tidak mendukung geolocation.");
                    }
                }
                
                // View location on map
                function viewOnMap(lat, lng) {
                    if (lat && lng) {
                        map.setView([lat, lng], 15);
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        marker = L.marker([lat, lng]).addTo(map)
                            .bindPopup("Lokasi Toko/Gudang")
                            .openPopup();
                    }
                }
                
                // Edit store
                function editStore(storeId) {
                    alert("Fitur edit akan segera tersedia! Store ID: " + storeId);
                }
                
                // Form submission
                document.getElementById("storeForm").addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const formData = {
                        name: document.getElementById("storeName").value,
                        type: document.getElementById("storeType").value,
                        address: document.getElementById("storeAddress").value,
                        phone: document.getElementById("storePhone").value,
                        latitude: document.getElementById("storeLatitude").value,
                        longitude: document.getElementById("storeLongitude").value
                    };
                    
                    console.log("Store Data:", formData);
                    alert("Toko/Gudang berhasil ditambahkan!\\n\\nNama: " + formData.name + "\\nTipe: " + formData.type + "\\nLokasi: " + formData.latitude + ", " + formData.longitude);
                    
                    // Reset form
                    this.reset();
                    document.getElementById("storeLatitude").value = "";
                    document.getElementById("storeLongitude").value = "";
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                });
            </script>
        </body>
        </html>
        ');
    })->name('stores');
});
