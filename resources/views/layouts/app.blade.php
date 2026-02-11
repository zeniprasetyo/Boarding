<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Boarding Admin</title>

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- DataTables (optional) --}}
    @stack('datatable-css')
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #f8fafc;
            --card-shadow: 0 2px 10px rgba(0,0,0,0.08);
            --hover-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        body { 
            background-color: var(--light-bg); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }
        main { flex: 1; }
        
        .navbar { 
            background: linear-gradient(135deg, var(--primary-color), #0a58ca);
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand { 
            font-weight: 700;
            font-size: 1.4rem;
        }
        .navbar-brand, .nav-link { 
            color: white !important; 
            transition: all 0.3s ease;
        }
        .nav-link:hover { 
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .nav-link.active { 
            background: rgba(255,255,255,0.15); 
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
        }
        
        .card { 
            border-radius: 12px; 
            border: none;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #e9ecef;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        footer { 
            text-align: center; 
            color: #6c757d; 
            padding: 1.2rem 0; 
            font-size: 0.9rem;
            background: white;
            border-top: 1px solid #dee2e6;
            margin-top: auto;
        }
        
        /* Button Styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0a58ca);
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #157347);
            box-shadow: 0 2px 5px rgba(25, 135, 84, 0.2);
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #b02a37);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        /* Table Styling */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
            border-bottom: 2px solid #e2e8f0;
            padding: 0.85rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            padding: 0.85rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        .table tbody tr {
            transition: background 0.2s ease;
        }
        .table tbody tr:hover {
            background: #f8fafc;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fcfdfe;
        }
        
        /* Action Buttons */
        .aksi-buttons { 
            display: flex; 
            gap: 8px; 
            justify-content: center; 
        }
        .aksi-buttons .btn { 
            padding: 0.4rem 0.8rem; 
            font-size: 0.85rem;
            border-radius: 6px;
            min-width: 36px;
        }
        
        /* Badge Styling */
        .badge {
            border-radius: 20px;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
        }
        .badge-success {
            background: linear-gradient(135deg, var(--success-color), #157347);
        }
        .badge-danger {
            background: linear-gradient(135deg, var(--danger-color), #b02a37);
        }
        .badge-warning {
            background: linear-gradient(135deg, var(--warning-color), #e0a800);
        }
        
        /* Modal Styling */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .modal-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #dee2e6;
            padding: 1.2rem 1.5rem;
        }
        .modal-title {
            font-weight: 600;
            color: #212529;
        }
        .modal-body {
            padding: 1.5rem;
        }
        
        /* Form Styling */
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.65rem 0.95rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        .status-deleted {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Avatar */
        .avatar-circle {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), #0a58ca);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            margin-right: 12px;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Loading */
        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Custom Styles */
        @stack('styles')
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-school me-2"></i>Boarding Admin
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    {{-- Dashboard (Semua role) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    {{-- Halaqah (Admin & Musyrif) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('halaqah') ? 'active' : '' }}" href="/halaqah">
                            <i class="fas fa-users me-1"></i> Halaqah
                        </a>
                    </li>
                    @endif
                    
                    {{-- Musyrif (Admin only) --}}
                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('musyrif') ? 'active' : '' }}" href="/musyrif">
                            <i class="fas fa-user-tie me-1"></i> Musyrif
                        </a>
                    </li>
                    @endif
                    
                    {{-- Santri (Admin only) --}}
                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('santri') ? 'active' : '' }}" href="/santri">
                            <i class="fas fa-user-graduate me-1"></i> Santri
                        </a>
                    </li>
                    @endif
                    
                    {{-- Kegiatan (Admin only) --}}
                    @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('kegiatan') ? 'active' : '' }}" href="/kegiatan">
                            <i class="fas fa-tasks me-1"></i> Kegiatan
                        </a>
                    </li>
                    @endif
                    
                    {{-- Ceklist (Admin & Musyrif) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ceklist') ? 'active' : '' }}" href="/ceklist">
                            <i class="fas fa-check-circle me-1"></i> Ceklist
                        </a>
                    </li>
                    @endif
                    
                    {{-- Absensi (Admin & Musyrif) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('absen') ? 'active' : '' }}" href="/absen">
                            <i class="fas fa-calendar-check me-1"></i> Absensi
                        </a>
                    </li>
                    @endif
                    
                    {{-- Kesehatan (Admin & Musyrif) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('kesehatan') ? 'active' : '' }}" href="/kesehatan">
                            <i class="fas fa-heartbeat me-1"></i> Kesehatan
                        </a>
                    </li>
                    @endif
                    
                    {{-- Laporan (Admin & Musyrif) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('laporan') ? 'active' : '' }}" href="/laporan">
                            <i class="fas fa-file-alt me-1"></i> Laporan
                        </a>
                    </li>
                    @endif
                    
                    {{-- Santri Dashboard (Santri only) --}}
                    @if(auth()->user()->hasRole('santri'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('santri/dashboard') ? 'active' : '' }}" href="/santri/dashboard">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="d-flex align-items-center ms-3">
                {{-- User Info --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                        <span class="badge bg-light text-dark ms-1">
                            @if(auth()->user()->hasRole('admin'))
                                Admin
                            @elseif(auth()->user()->hasRole('musyrif'))
                                Musyrif
                            @elseif(auth()->user()->hasRole('santri'))
                                Santri
                            @endif
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/logout" method="POST" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none text-dark p-0 w-100 text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <main class="container mb-5 fade-in">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <i class="fas fa-heart text-danger me-1"></i> Yayasan Al Abidin
                </div>
                <div class="col-md-6 text-md-end">
                    © {{ date('Y') }} Boarding Management System
                </div>
            </div>
        </div>
    </footer>

    {{-- MODAL SECTION --}}
    @stack('modals')

    {{-- SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- DataTables (optional) --}}
    @stack('datatable-js')
    
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        });

        // Global utility functions
        function showLoading(button, text = 'Menyimpan...') {
            button.prop('disabled', true).html(`<span class="loading-spinner me-2"></span>${text}`);
        }

        function hideLoading(button, originalText) {
            button.prop('disabled', false).html(originalText);
        }

        function showSuccess(message, timer = 2000) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: timer,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message,
                confirmButtonColor: '#0d6efd'
            });
        }

        function confirmAction(title, text, confirmText = 'Ya', cancelText = 'Batal', confirmColor = '#dc3545') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6c757d'
            });
        }

        // Tooltip initialization
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    {{-- Page Specific Scripts --}}
    @stack('scripts')
</body>
</html>