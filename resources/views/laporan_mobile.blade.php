<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Santri | Boarding Admin</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #1a5fb4;
            --primary-dark: #155399;
            --light-blue: #f0f9ff;
            --light-gray: #f0f7ff;
            --whatsapp-green: #25D366;
            --whatsapp-dark: #128C7E;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            min-height: 100vh;
            padding-bottom: 70px;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header .subtitle {
            font-size: 13px;
            opacity: 0.9;
        }
        
        /* Role badge */
        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 8px;
            display: inline-block;
            font-weight: 500;
        }
        
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
        }
        
        /* Card */
        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 15px;
            border: none;
        }
        
        .card-content {
            padding: 15px;
        }
        
        /* Alert Role Info */
        .role-alert {
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .role-alert i {
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .role-alert.warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .role-alert.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        /* Filter Section */
        .filter-section {
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .filter-group {
            flex: 1 1 100%;
        }
        
        @media (min-width: 480px) {
            .filter-group {
                flex: 1 1 calc(50% - 12px);
            }
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
            font-size: 13px;
        }
        
        .filter-select, .filter-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            transition: border 0.3s;
        }
        
        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 180, 0.2);
        }
        
        /* Filter Button */
        .filter-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 5px;
        }
        
        .filter-button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Info Box */
        .info-box {
            background-color: var(--light-blue);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-box i {
            color: var(--primary-color);
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .info-box-content {
            flex: 1;
        }
        
        .info-box-title {
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }
        
        .info-box-value {
            font-size: 16px;
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        
        .action-btn {
            flex: 1;
            min-width: 120px;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            text-align: center;
            border: none;
        }
        
        .whatsapp-btn {
            background-color: var(--whatsapp-green);
            color: white;
        }
        
        .whatsapp-btn:hover {
            background-color: var(--whatsapp-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .pdf-btn {
            background-color: var(--primary-color);
            color: white;
        }
        
        .pdf-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Checklist Selection */
        .checklist-selection {
            background-color: #fff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #eee;
        }
        
        .checklist-selection input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        .checklist-selection label {
            font-weight: 600;
            font-size: 14px;
            color: #444;
        }
        
        /* Santri Cards */
        .santri-card {
            background-color: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
            transition: all 0.3s;
        }
        
        .santri-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .santri-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .santri-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .santri-info {
            flex: 1;
        }
        
        .santri-name {
            font-weight: 700;
            font-size: 16px;
            color: #333;
            margin-bottom: 2px;
        }
        
        .santri-phone {
            font-size: 13px;
            color: #666;
        }
        
        .santri-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        /* Progress Grid */
        .progress-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .progress-day {
            text-align: center;
            padding: 8px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }
        
        .day-header {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .day-value {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .progress-bar-mini {
            height: 4px;
            background-color: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 2px;
        }
        
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
        .bg-danger { background-color: #dc3545; }
        .bg-primary { background-color: var(--primary-color); }
        
        /* Total Badges */
        .total-badges {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .total-badge {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-weight: 700;
        }
        
        .total-badge.pagi {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .total-badge.malam {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .total-badge.keseluruhan {
            background-color: var(--light-blue);
            color: var(--primary-color);
        }
        
        /* Action Buttons Row */
        .action-row {
            display: flex;
            gap: 8px;
        }
        
        .action-row .btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .btn-download {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-download:hover {
            background-color: var(--light-blue);
        }
        
        .btn-whatsapp {
            background-color: var(--whatsapp-green);
            color: white;
            border: none;
        }
        
        .btn-whatsapp:hover {
            background-color: var(--whatsapp-dark);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ccc;
        }
        
        .empty-state h5 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #555;
        }
        
        .empty-state p {
            font-size: 14px;
        }
        
        /* Mobile Bottom Navigation */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 8px 0;
        }
        
        .mobile-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #666;
            flex: 1;
            padding: 5px 0;
            transition: all 0.2s;
            font-size: 12px;
        }
        
        .mobile-nav-item.active {
            color: var(--primary-color);
        }
        
        .mobile-nav-item i {
            font-size: 18px;
            margin-bottom: 3px;
        }
        
        /* Modal untuk menu lainnya */
        .mobile-modal .modal-dialog {
            margin: 0;
            max-width: 100%;
        }
        
        .mobile-modal .modal-content {
            border-radius: 20px 20px 0 0;
            border: none;
            min-height: 60vh;
        }
        
        .mobile-modal .modal-header {
            border-bottom: 1px solid #eee;
            padding: 20px;
        }
        
        .mobile-modal .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        
        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (min-width: 768px) {
            .mobile-nav {
                display: none;
            }
            
            .container {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
        
        @media (max-width: 479px) {
            .progress-grid {
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-file-alt"></i> Laporan Kegiatan Santri</h1>
        <p class="subtitle">Pantau perkembangan kegiatan harian santri</p>
        <div class="role-badge">
            <i class="fas fa-user-tag me-1"></i>
            {{ auth()->user()->getRoleNames()->first() }}
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="container">
        <!-- Role Alert -->
        @if(auth()->user()->hasRole('santri'))
            <div class="role-alert warning fade-in">
                <i class="fas fa-user-graduate"></i>
                <div>
                    <strong>Mode Santri</strong><br>
                    <small>Anda hanya dapat melihat laporan Anda sendiri</small>
                </div>
            </div>
        @elseif(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
            <div class="role-alert info fade-in">
                <i class="fas fa-chalkboard-teacher"></i>
                <div>
                    <strong>Mode Musyrif</strong><br>
                    <small>Hanya dapat mengelola halaqah Anda</small>
                </div>
            </div>
        @endif
        
        <!-- Filter Card -->
        <div class="card fade-in">
            <div class="card-content">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Laporan</h5>
                
                <form id="filterForm" class="filter-section">
                    <!-- Halaqah Filter -->
                    <div class="filter-group mb-3">
                        <label for="halaqahFilter"><i class="fas fa-mosque"></i> Halaqah</label>
                        <select id="halaqahFilter" class="filter-select" required>
                            <option value="">-- Pilih Halaqah --</option>
                            @foreach ($halaqahs as $h)
                                <option value="{{ $h->id }}" 
                                    {{ request('halaqah_id') == $h->id ? 'selected' : '' }}
                                    {{ auth()->user()->hasRole('santri') && auth()->user()->halaqah_id == $h->id ? 'selected disabled' : '' }}>
                                    {{ $h->nama_halaqah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Kegiatan Filter -->
                    <div class="filter-group mb-3">
                        <label for="kegiatanFilter"><i class="fas fa-tasks"></i> Kegiatan Utama</label>
                        <select id="kegiatanFilter" class="filter-select">
                            <option value="">-- Semua Kegiatan --</option>
                            @foreach($kegiatanUtama as $k)
                                <option value="{{ $k->id }}" 
                                    {{ request('kegiatan_utama') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kegiatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="startDate"><i class="fas fa-calendar-alt"></i> Dari</label>
                            <input type="date" id="startDate" class="filter-input" 
                                   value="{{ request('start_date') ?: date('Y-m-d', strtotime('-7 days')) }}" required>
                        </div>
                        
                        <div class="filter-group">
                            <label for="endDate"><i class="fas fa-calendar-alt"></i> Sampai</label>
                            <input type="date" id="endDate" class="filter-input" 
                                   value="{{ request('end_date') ?: date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <!-- Hidden Fields for Santri -->
                    @if(auth()->user()->hasRole('santri'))
                        <input type="hidden" id="santriId" value="{{ auth()->user()->id }}">
                    @endif
                    
                    <!-- Filter Button -->
                    <button type="button" id="applyFilter" class="filter-button mt-2">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="info-box fade-in" id="infoBox" style="display: none;">
            <i class="fas fa-chart-line"></i>
            <div class="info-box-content">
                <div class="info-box-title">PERIODE LAPORAN</div>
                <div class="info-box-value" id="periodDisplay"></div>
                <div class="info-box-title mt-1">TOTAL SANTRI: <span id="totalSantri">0</span></div>
            </div>
        </div>
        
        <!-- Checklist Selection (for WhatsApp Broadcast) -->
        @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
        <div class="checklist-selection fade-in" id="checklistSelection" style="display: none;">
            <input type="checkbox" id="selectAll" class="form-check-input">
            <label for="selectAll">Pilih Semua Santri</label>
        </div>
        @endif
        
        <!-- Santri Cards Container -->
        <div id="santriContainer"></div>
        
        <!-- Empty State -->
        <div class="empty-state fade-in" id="emptyState">
            <i class="fas fa-chart-bar"></i>
            <h5>Belum ada data laporan</h5>
            <p>Pilih filter untuk menampilkan laporan santri</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons fade-in" id="actionButtons" style="display: none;">
            @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
            <button type="button" id="broadcastBtn" class="action-btn whatsapp-btn">
                <i class="fab fa-whatsapp"></i> Broadcast
            </button>
            @endif
            
            <button type="button" id="downloadBtn" class="action-btn pdf-btn">
                <i class="fas fa-download"></i> Download PDF
            </button>
        </div>
    </main>
    
    <!-- Mobile Bottom Navigation -->
        <!-- Mobile Bottom Navigation -->
    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
    <nav class="mobile-nav">
        <div class="mobile-nav-container">
            <a href="/dashboard" class="mobile-nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="/ceklist" class="mobile-nav-item">
                <i class="fas fa-check-square"></i>
                <span>Ceklist</span>
            </a>
            
            <a href="/absen" class="mobile-nav-item">
                <i class="fas fa-list-check"></i>
                <span>Absensi</span>
            </a>
            
            <a href="/kesehatan" class="mobile-nav-item">
                <i class="fas fa-heartbeat"></i>
                <span>Kesehatan</span>
            </a>
            
            
            <a href="/laporan" class="mobile-nav-item active">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            
            
            <a href="#" class="mobile-nav-item" data-bs-toggle="modal" data-bs-target="#moreMenuModal">
                <i class="fas fa-bars"></i>
                <span>Menu</span>
            </a>
        </div>
    </nav>
    @endif
    
    <div class="modal fade mobile-modal" id="moreMenuModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Menu Lainnya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                        <a href="/halaqah" class="list-group-item list-group-item-action">
                            <i class="fa fa-mosque me-2"></i> Halaqah
                        </a>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <form action="/logout" method="POST" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fa fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Auto-set filter untuk santri
            @if(auth()->user()->hasRole('santri'))
                $('#halaqahFilter').prop('disabled', true);
                @if(auth()->user()->halaqah_id)
                    $('#halaqahFilter').val('{{ auth()->user()->halaqah_id }}');
                @endif
            @endif
            
            // Format tanggal untuk display
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }
            
            // Hitung selisih hari
            function getDaysBetween(startDate, endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            }
            
            // Generate array tanggal
            function getDatesArray(startDate, endDate) {
                const dates = [];
                const start = new Date(startDate);
                const end = new Date(endDate);
                
                for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                    dates.push(new Date(d).toISOString().split('T')[0]);
                }
                
                return dates;
            }
            
            // Hitung persentase progress
            function calculateProgress(value) {
                if (!value || value === '0/0') return { done: 0, total: 0, percentage: 0 };
                
                const [done, total] = value.split('/').map(Number);
                const percentage = total > 0 ? Math.round((done / total) * 100) : 0;
                return { done, total, percentage };
            }
            
            // Tentukan warna progress
            function getProgressColor(percentage) {
                if (percentage >= 80) return 'success';
                if (percentage >= 50) return 'warning';
                return 'danger';
            }
            
            // Load laporan data
            $('#applyFilter').click(function() {
                loadLaporanData();
            });
            
            // Auto-load jika ada parameter URL
            @if(request()->filled(['halaqah_id', 'start_date', 'end_date']))
                setTimeout(() => {
                    loadLaporanData();
                }, 500);
            @endif
            
            function loadLaporanData() {
                const halaqahId = $('#halaqahFilter').val();
                const kegiatanId = $('#kegiatanFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                if (!halaqahId || !startDate || !endDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Belum Lengkap',
                        text: 'Harap pilih halaqah dan tanggal terlebih dahulu',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Validasi tanggal
                if (new Date(startDate) > new Date(endDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tanggal Tidak Valid',
                        text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Memuat Laporan',
                    text: 'Sedang mengambil data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Prepare request data
                const requestData = {
                    halaqah_id: halaqahId,
                    start_date: startDate,
                    end_date: endDate,
                    kegiatan_utama: kegiatanId || '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                @if(auth()->user()->hasRole('santri'))
                    requestData.santri_id = $('#santriId').val();
                @endif
                
                // AJAX request
                $.ajax({
                    url: '{{ route("laporan.mobileData") }}',
                    method: 'GET',
                    data: requestData,
                    success: function(response) {
                        Swal.close();
                        
                        if (response.success) {
                            displayLaporanData(response.data, startDate, endDate);
                        } else {
                            showEmptyState();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#1a5fb4'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        
                        let errorMessage = 'Terjadi kesalahan saat memuat data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        showEmptyState();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            }
            
            // Display data ke UI
            function displayLaporanData(data, startDate, endDate) {
                if (!data || data.length === 0) {
                    showEmptyState();
                    return;
                }
                
                // Update info box
                const period = `${formatDate(startDate)} - ${formatDate(endDate)}`;
                const daysCount = getDaysBetween(startDate, endDate);
                $('#periodDisplay').html(`${period} <span class="small">(${daysCount} hari)</span>`);
                $('#totalSantri').text(data.length);
                $('#infoBox').show();
                
                // Tampilkan checklist selection untuk admin/musyrif
                @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                    $('#checklistSelection').show();
                    $('#selectAll').prop('checked', false);
                @endif
                
                // Generate dates array
                const dates = getDatesArray(startDate, endDate);
                
                // Clear container
                const container = $('#santriContainer');
                container.empty();
                
                // Process each santri
                data.forEach((santri, index) => {
                    const cardHtml = generateSantriCard(santri, index + 1, dates);
                    container.append(cardHtml);
                });
                
                // Tampilkan action buttons
                $('#actionButtons').show();
                $('#emptyState').hide();
                
                // Initialize checkbox events
                initializeCheckboxEvents();
            }
            
            // Generate card untuk setiap santri
            function generateSantriCard(santri, index, dates) {
                let cardHtml = `
                    <div class="santri-card fade-in" style="animation-delay: ${index * 0.05}s">
                        <div class="santri-header">
                            <div class="santri-avatar">
                                ${santri.santri.charAt(0).toUpperCase()}
                            </div>
                            <div class="santri-info">
                                <div class="santri-name">${santri.santri}</div>
                                ${santri.telepon ? `<div class="santri-phone"><i class="fas fa-phone-alt"></i> ${santri.telepon}</div>` : ''}
                            </div>
                            @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                            <input type="checkbox" 
                                   class="santri-checkbox" 
                                   data-santri-id="${santri.id}"
                                   data-santri-name="${santri.santri}"
                                   data-santri-phone="${santri.telepon || ''}">
                            @endif
                        </div>`;
                
                // Progress grid berdasarkan tipe data
                if (santri.data) {
                    // Tipe 1: Data keseluruhan
                    cardHtml += `<div class="progress-grid">`;
                    
                    dates.forEach(date => {
                        const value = santri.data[date] || '0/0';
                        const progress = calculateProgress(value);
                        const color = getProgressColor(progress.percentage);
                        const dateShort = new Date(date).toLocaleDateString('id-ID', { 
                            day: '2-digit', 
                            month: 'short' 
                        });
                        
                        cardHtml += `
                            <div class="progress-day">
                                <div class="day-header">${dateShort}</div>
                                <div class="day-value">${progress.done}/${progress.total}</div>
                                <div class="progress-bar-mini">
                                    <div class="progress-fill bg-${color}" style="width: ${progress.percentage}%"></div>
                                </div>
                            </div>`;
                    });
                    
                    cardHtml += `</div>`;
                    
                    // Total badge
                    if (santri.total) {
                        cardHtml += `
                            <div class="total-badges">
                                <div class="total-badge keseluruhan">
                                    <div style="font-size: 12px;">TOTAL</div>
                                    <div>${santri.total}</div>
                                </div>
                            </div>`;
                    }
                } else if (santri.pagi && santri.malam) {
                    // Tipe 2: Pagi dan Malam terpisah
                    cardHtml += `<div style="margin-bottom: 10px;"><strong>Pagi:</strong></div>`;
                    cardHtml += `<div class="progress-grid">`;
                    
                    dates.forEach(date => {
                        const value = santri.pagi[date] || '0/0';
                        const progress = calculateProgress(value);
                        const dateShort = new Date(date).toLocaleDateString('id-ID', { 
                            day: '2-digit', 
                            month: 'short' 
                        });
                        
                        cardHtml += `
                            <div class="progress-day">
                                <div class="day-header">${dateShort}</div>
                                <div class="day-value">${progress.done}/${progress.total}</div>
                                <div class="progress-bar-mini">
                                    <div class="progress-fill bg-warning" style="width: ${progress.percentage}%"></div>
                                </div>
                            </div>`;
                    });
                    
                    cardHtml += `</div>`;
                    cardHtml += `<div style="margin: 10px 0;"><strong>Malam:</strong></div>`;
                    cardHtml += `<div class="progress-grid">`;
                    
                    dates.forEach(date => {
                        const value = santri.malam[date] || '0/0';
                        const progress = calculateProgress(value);
                        const dateShort = new Date(date).toLocaleDateString('id-ID', { 
                            day: '2-digit', 
                            month: 'short' 
                        });
                        
                        cardHtml += `
                            <div class="progress-day">
                                <div class="day-header">${dateShort}</div>
                                <div class="day-value">${progress.done}/${progress.total}</div>
                                <div class="progress-bar-mini">
                                    <div class="progress-fill bg-primary" style="width: ${progress.percentage}%"></div>
                                </div>
                            </div>`;
                    });
                    
                    cardHtml += `</div>`;
                    
                    // Total badges pagi dan malam
                    cardHtml += `
                        <div class="total-badges">
                            <div class="total-badge pagi">
                                <div style="font-size: 12px;">TOTAL PAGI</div>
                                <div>${santri.total_pagi || 0}</div>
                            </div>
                            <div class="total-badge malam">
                                <div style="font-size: 12px;">TOTAL MALAM</div>
                                <div>${santri.total_malam || 0}</div>
                            </div>
                        </div>`;
                }
                
                // Action buttons
                cardHtml += `
                    <div class="action-row">
                        <button class="btn btn-download btn-download-single" 
                                data-santri-id="${santri.id}"
                                data-santri-name="${santri.santri}">
                            <i class="fas fa-download"></i> PDF
                        </button>
                        
                        @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                        <button class="btn btn-whatsapp btn-whatsapp-single"
                                data-santri-id="${santri.id}"
                                data-santri-name="${santri.santri}"
                                data-santri-phone="${santri.telepon || ''}">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                        @endif
                    </div>
                </div>`;
                
                return cardHtml;
            }
            
            // Initialize checkbox events
            function initializeCheckboxEvents() {
                // Select all checkbox
                $('#selectAll').off('change').on('change', function() {
                    const isChecked = $(this).prop('checked');
                    $('.santri-checkbox').prop('checked', isChecked);
                    updateBroadcastButton();
                });
                
                // Individual checkbox
                $(document).off('change', '.santri-checkbox').on('change', '.santri-checkbox', function() {
                    const total = $('.santri-checkbox').length;
                    const checked = $('.santri-checkbox:checked').length;
                    const selectAll = $('#selectAll');
                    
                    if (checked === 0) {
                        selectAll.prop('checked', false);
                    } else if (checked === total) {
                        selectAll.prop('checked', true);
                    } else {
                        selectAll.prop('checked', false);
                    }
                    
                    updateBroadcastButton();
                });
            }
            
            // Update broadcast button text
            function updateBroadcastButton() {
                const selected = $('.santri-checkbox:checked').length;
                const btn = $('#broadcastBtn');
                if (selected > 0) {
                    btn.html(`<i class="fab fa-whatsapp"></i> Kirim ke ${selected} Santri`);
                } else {
                    btn.html(`<i class="fab fa-whatsapp"></i> Broadcast`);
                }
            }
            
            // Show empty state
            function showEmptyState() {
                $('#emptyState').show();
                $('#infoBox').hide();
                $('#checklistSelection').hide();
                $('#actionButtons').hide();
                $('#santriContainer').empty();
            }
            
            // Download single PDF
            $(document).on('click', '.btn-download-single', function() {
                const santriId = $(this).data('santri-id');
                const santriName = $(this).data('santri-name');
                
                Swal.fire({
                    title: 'Download PDF',
                    text: `Download laporan untuk ${santriName}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#1a5fb4'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>').attr({
                            method: 'POST',
                            action: '{{ route("laporan.pdf") }}',
                            target: '_blank'
                        }).css('display', 'none');
                        
                        const csrf = $('<input>').attr({
                            type: 'hidden',
                            name: '_token',
                            value: $('meta[name="csrf-token"]').attr('content')
                        });
                        
                        const halaqah = $('<input>').attr({
                            type: 'hidden',
                            name: 'halaqah_id',
                            value: $('#halaqahFilter').val()
                        });
                        
                        const start = $('<input>').attr({
                            type: 'hidden',
                            name: 'start_date',
                            value: $('#startDate').val()
                        });
                        
                        const end = $('<input>').attr({
                            type: 'hidden',
                            name: 'end_date',
                            value: $('#endDate').val()
                        });
                        
                        const santri = $('<input>').attr({
                            type: 'hidden',
                            name: 'santri_id',
                            value: santriId
                        });
                        
                        const kegiatan = $('<input>').attr({
                            type: 'hidden',
                            name: 'kegiatan_utama',
                            value: $('#kegiatanFilter').val()
                        });
                        
                        form.append(csrf, halaqah, start, end, santri, kegiatan);
                        $('body').append(form);
                        form.submit();
                        setTimeout(() => form.remove(), 100);
                    }
                });
            });
            
            // Download all PDF
            $('#downloadBtn').click(function() {
                const halaqahId = $('#halaqahFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                if (!halaqahId || !startDate || !endDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Belum Lengkap',
                        text: 'Harap terapkan filter terlebih dahulu',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Download PDF',
                    text: 'Download laporan untuk semua santri?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#1a5fb4'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = $('<form>').attr({
                            method: 'POST',
                            action: '{{ route("laporan.pdf") }}',
                            target: '_blank'
                        }).css('display', 'none');
                        
                        const csrf = $('<input>').attr({
                            type: 'hidden',
                            name: '_token',
                            value: $('meta[name="csrf-token"]').attr('content')
                        });
                        
                        const halaqah = $('<input>').attr({
                            type: 'hidden',
                            name: 'halaqah_id',
                            value: halaqahId
                        });
                        
                        const start = $('<input>').attr({
                            type: 'hidden',
                            name: 'start_date',
                            value: startDate
                        });
                        
                        const end = $('<input>').attr({
                            type: 'hidden',
                            name: 'end_date',
                            value: endDate
                        });
                        
                        const kegiatan = $('<input>').attr({
                            type: 'hidden',
                            name: 'kegiatan_utama',
                            value: $('#kegiatanFilter').val()
                        });
                        
                        form.append(csrf, halaqah, start, end, kegiatan);
                        $('body').append(form);
                        form.submit();
                        setTimeout(() => form.remove(), 100);
                    }
                });
            });
            
            // WhatsApp single
            $(document).on('click', '.btn-whatsapp-single', function() {
                const santriId = $(this).data('santri-id');
                const santriName = $(this).data('santri-name');
                const santriPhone = $(this).data('santri-phone');
                
                sendWhatsAppSingle(santriId, santriName, santriPhone);
            });
            
            // WhatsApp broadcast
            $('#broadcastBtn').click(function() {
                const selectedSantris = [];
                const selectedNames = [];
                
                $('.santri-checkbox:checked').each(function() {
                    selectedSantris.push({
                        id: $(this).data('santri-id'),
                        name: $(this).data('santri-name'),
                        phone: $(this).data('santri-phone')
                    });
                    selectedNames.push($(this).data('santri-name'));
                });
                
                if (selectedSantris.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Santri Terpilih',
                        text: 'Pilih minimal satu santri untuk broadcast',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                sendWhatsAppBroadcast(selectedSantris, selectedNames);
            });
            
            // Function: Send WhatsApp single
            function sendWhatsAppSingle(santriId, santriName, santriPhone) {
                // Format phone number
                function formatPhone(phone) {
                    if (!phone) return null;
                    let cleaned = phone.toString().replace(/\D/g, '');
                    if (!cleaned) return null;
                    if (cleaned.startsWith('0')) cleaned = '62' + cleaned.substring(1);
                    if (!cleaned.startsWith('62') && cleaned.length >= 10) cleaned = '62' + cleaned;
                    return cleaned.length >= 10 ? cleaned : null;
                }
                
                const formattedPhone = formatPhone(santriPhone);
                
                // Show confirmation modal
                Swal.fire({
                    title: 'Kirim WhatsApp',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Kirim laporan untuk <strong>${santriName}</strong>?
                            </div>
                            ${formattedPhone ? 
                                `<p>Nomor tujuan: <code>${formattedPhone}</code></p>` : 
                                `<div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Nomor WhatsApp tidak tersedia untuk santri ini.
                                </div>`
                            }
                            <div class="mt-3">
                                <label class="form-label">Pesan Tambahan:</label>
                                <textarea class="form-control" id="whatsappMessage" rows="3" placeholder="Tambahkan pesan jika diperlukan..."></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Kirim WhatsApp',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#25D366',
                    width: '90%',
                    maxWidth: '500px',
                    preConfirm: () => {
                        const message = $('#whatsappMessage').val();
                        return { message: message };
                    }
                }).then((result) => {
                    if (result.isConfirmed && formattedPhone) {
                        processWhatsAppRequest([{ id: santriId }], result.value.message, true);
                    } else if (!formattedPhone) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Dapat Mengirim',
                            text: 'Nomor WhatsApp tidak tersedia untuk santri ini',
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            }
            
            // Function: Send WhatsApp broadcast
            function sendWhatsAppBroadcast(santriList, santriNames) {
                // Filter santri yang punya nomor
                const santriWithPhone = santriList.filter(s => {
                    const phone = s.phone || '';
                    const cleaned = phone.toString().replace(/\D/g, '');
                    return cleaned.length >= 10;
                });
                
                const noPhoneCount = santriList.length - santriWithPhone.length;
                
                Swal.fire({
                    title: 'Broadcast WhatsApp',
                    html: `
                        <div class="text-start">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Kirim laporan ke <strong>${santriList.length}</strong> santri?
                            </div>
                            ${noPhoneCount > 0 ? 
                                `<div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${noPhoneCount} santri tidak memiliki nomor WhatsApp.
                                </div>` : ''
                            }
                            <div class="mb-3">
                                <label class="form-label">Santri yang terpilih:</label>
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto; font-size: 14px;">
                                    ${santriNames.map(name => `<div>• ${name}</div>`).join('')}
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Pesan Tambahan:</label>
                                <textarea class="form-control" id="broadcastMessage" rows="3" placeholder="Pesan yang sama akan dikirim ke semua santri..."></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Kirim Broadcast',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#25D366',
                    width: '90%',
                    maxWidth: '500px',
                    preConfirm: () => {
                        const message = $('#broadcastMessage').val();
                        return { message: message };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        processWhatsAppRequest(santriWithPhone, result.value.message, false);
                    }
                });
            }
            
            // Function: Process WhatsApp request
            function processWhatsAppRequest(santriList, additionalMessage, isSingle) {
                if (santriList.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Nomor WhatsApp',
                        text: 'Tidak ada santri yang memiliki nomor WhatsApp',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                const halaqahId = $('#halaqahFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                
                if (!halaqahId || !startDate || !endDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Tidak Lengkap',
                        text: 'Harap terapkan filter terlebih dahulu',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: isSingle ? 'Menyiapkan Pesan...' : 'Mengirim Broadcast...',
                    html: `<div class="loading-spinner my-3"></div>
                          <p>${isSingle ? 'Menyiapkan pesan WhatsApp' : `Mengirim ke ${santriList.length} santri`}</p>`,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                
                // Prepare request
                const requestData = {
                    santri_ids: santriList.map(s => s.id),
                    additional_message: additionalMessage || '',
                    halaqah_id: halaqahId,
                    start_date: startDate,
                    end_date: endDate,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                const url = isSingle ? 
                    '{{ route("laporan.send-whatsapp") }}' : 
                    '{{ route("laporan.send-whatsapp-selected") }}';
                
                // Send AJAX request
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: requestData,
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        
                        if (response.success) {
                            if (isSingle && response.whatsapp_url) {
                                // Buka WhatsApp untuk single
                                window.open(response.whatsapp_url, '_blank');
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'WhatsApp Dibuka!',
                                    html: `
                                        <div class="text-center">
                                            <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                                            <p>Pesan telah dibuka di WhatsApp.</p>
                                            <div class="mt-3">
                                                <a href="${response.whatsapp_url}" target="_blank" 
                                                   class="btn btn-success">
                                                    <i class="fab fa-whatsapp me-2"></i> Buka WhatsApp Lagi
                                                </a>
                                            </div>
                                        </div>
                                    `,
                                    showCancelButton: true,
                                    cancelButtonText: 'Tutup',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#25D366'
                                });
                            } else if (!isSingle) {
                                // Tampilkan hasil batch
                                const successCount = response.data?.success_count || 0;
                                const failedCount = response.data?.failed_count || 0;
                                
                                let message = `<div class="text-start">`;
                                message += `<p><strong>✅ Broadcast Selesai!</strong></p>`;
                                message += `<p>Berhasil dikirim: <span class="text-success">${successCount}</span></p>`;
                                message += `<p>Gagal: <span class="text-danger">${failedCount}</span></p>`;
                                
                                if (response.data?.results) {
                                    message += `<div class="mt-3" style="max-height: 200px; overflow-y: auto;">`;
                                    message += `<table class="table table-sm table-bordered">`;
                                    message += `<tr><th>Santri</th><th>Status</th></tr>`;
                                    
                                    response.data.results.forEach(result => {
                                        const statusClass = result.success ? 'text-success' : 'text-danger';
                                        const statusIcon = result.success ? '✓' : '✗';
                                        message += `<tr>
                                            <td>${result.santri_name}</td>
                                            <td class="${statusClass}">${statusIcon} ${result.success ? 'Berhasil' : 'Gagal'}</td>
                                        </tr>`;
                                    });
                                    
                                    message += `</table></div>`;
                                }
                                
                                message += `</div>`;
                                
                                Swal.fire({
                                    title: 'Hasil Broadcast',
                                    html: message,
                                    showCancelButton: true,
                                    cancelButtonText: 'Tutup',
                                    showConfirmButton: false,
                                    width: '90%',
                                    maxWidth: '500px'
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#1a5fb4'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengirim WhatsApp',
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            }
            
            // Initialize
            updateBroadcastButton();
        });
    </script>
</body>
</html>