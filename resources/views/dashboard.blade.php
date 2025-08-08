<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: #bdc3c7;
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s;
            padding: 12px 15px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #3498db;
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #ff9068 0%, #fd746c 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #2196F3 0%, #00BCD4 100%);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50 !important;
        }

        /* Mobile Responsive Styles */
        .sidebar-toggle {
            display: none;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                z-index: 1050;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .col-md-3,
            .col-lg-2 {
                display: none;
            }

            .col-md-9,
            .col-lg-10 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .card-body {
                padding: 1rem;
            }

            .navbar {
                padding: 0.5rem 1rem;
            }

            .p-4 {
                padding: 1rem !important;
            }

            .card-title {
                font-size: 1.1rem;
            }

            .stat-card .card-body {
                padding: 1rem;
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                left: -100%;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .row.mb-4 {
                margin-bottom: 1rem !important;
            }

            .col-md-6,
            .col-lg-3 {
                margin-bottom: 1rem;
            }
        }

        /* Touch-friendly buttons */
        @media (max-width: 768px) {
            .nav-link,
            .btn,
            .dropdown-toggle {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3" id="sidebar">
                    <div class="text-center mb-4">
                        <i class="fas fa-store fa-2x mb-2"></i>
                        <h5>ERP System</h5>
                        <small class="text-muted">{{ $user->role->name ?? 'User' }}</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-cash-register me-2"></i>POS
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-box me-2"></i>Produk
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-shopping-cart me-2"></i>Penjualan
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-chart-bar me-2"></i>Laporan
                        </a>
                        @if($user->role->name === 'Admin')
                        <hr>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-users me-2"></i>Pengguna
                        </a>
                        <a class="nav-link" href="#" onclick="alert('Fitur dalam pengembangan')">
                            <i class="fas fa-cog me-2"></i>Pengaturan
                        </a>
                        @endif
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Top Navigation -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                        <div class="container-fluid">
                            <!-- Mobile Menu Toggle -->
                            <button class="btn btn-link sidebar-toggle me-2" id="sidebarToggle">
                                <i class="fas fa-bars"></i>
                            </button>

                            <span class="navbar-brand mb-0 h1">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </span>
                            
                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle fa-lg me-2"></i>
                                        <span class="d-none d-md-inline">{{ $user->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" onclick="alert('Fitur dalam pengembangan')">
                                            <i class="fas fa-user me-2"></i>Profil
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('skin.selector') }}">
                                            <i class="fas fa-palette me-2"></i>Ubah Tampilan
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
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
                    
                    <!-- Dashboard Content -->
                    <div class="p-4">
                        <!-- Welcome Card -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <i class="fas fa-wave-square me-2"></i>
                                            Selamat datang, {{ $user->name }}!
                                        </h4>
                                        <p class="card-text text-muted">
                                            Anda login sebagai <strong>{{ $user->role->name }}</strong> 
                                            di toko <strong>{{ $user->store->name ?? 'Tidak ada toko' }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistics Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-3"></i>
                                        <h3>{{ $stats['total_users'] }}</h3>
                                        <p class="mb-0">Total Pengguna</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card stat-card success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box fa-2x mb-3"></i>
                                        <h3>{{ $stats['total_products'] }}</h3>
                                        <p class="mb-0">Total Produk</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card stat-card warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                                        <h3>{{ $stats['total_sales'] }}</h3>
                                        <p class="mb-0">Total Penjualan</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="card stat-card info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-store fa-2x mb-3"></i>
                                        <h3>{{ $stats['total_stores'] }}</h3>
                                        <p class="mb-0">Total Toko</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-bolt me-2"></i>Aksi Cepat
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" onclick="alert('Fitur dalam pengembangan')">
                                                <i class="fas fa-plus me-2"></i>Tambah Produk Baru
                                            </button>
                                            <button class="btn btn-success" onclick="alert('Fitur dalam pengembangan')">
                                                <i class="fas fa-cash-register me-2"></i>Buka POS
                                            </button>
                                            <button class="btn btn-info" onclick="alert('Fitur dalam pengembangan')">
                                                <i class="fas fa-chart-line me-2"></i>Lihat Laporan
                                            </button>
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
                                            <li><strong>Laravel Version:</strong> {{ app()->version() }}</li>
                                            <li><strong>PHP Version:</strong> {{ PHP_VERSION }}</li>
                                            <li><strong>Waktu Login:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on nav links on mobile
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });

        // Auto-refresh stats every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
