<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi Santri | Boarding Admin</title>
    
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
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
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
        
        /* Santri Card */
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
        
        .santri-id {
            font-size: 13px;
            color: #666;
        }
        
        /* Absen Status Section */
        .absen-status {
            margin-bottom: 15px;
        }
        
        .absen-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .absen-column {
            display: flex;
            flex-direction: column;
        }
        
        .absen-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .absen-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            transition: all 0.3s;
        }
        
        .absen-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 180, 0.2);
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .status-hadir {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .status-izin {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        .status-alpa {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .status-sakit {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--info-color);
            border: 1px solid rgba(23, 162, 184, 0.2);
        }
        
        /* Catatan */
        .catatan-box {
            margin-top: 10px;
        }
        
        .catatan-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .catatan-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
            transition: all 0.3s;
        }
        
        .catatan-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 95, 180, 0.2);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
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
        
        .set-all-btn {
            background-color: #fff;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .set-all-btn:hover {
            background-color: var(--light-blue);
        }
        
        .save-btn {
            background-color: var(--primary-color);
            color: white;
        }
        
        .save-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
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
        <h1><i class="fas fa-list-check"></i> Absensi Harian Santri</h1>
        <p class="subtitle">Catat kehadiran santri dengan sistematis</p>
        <div class="role-badge">
            <i class="fas fa-user-tag me-1"></i>
            {{ $user_role }}
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="container">
        <!-- Role Alert -->
        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
            <div class="role-alert warning fade-in">
                <i class="fas fa-chalkboard-teacher"></i>
                <div>
                    <strong>Mode Musyrif</strong><br>
                    <small>Anda hanya dapat mengisi absensi untuk halaqah Anda</small>
                </div>
            </div>
        @endif
        
        <!-- Warning jika musyrif tanpa halaqah -->
        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 0)
            <div class="role-alert warning fade-in">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Belum ada halaqah yang ditugaskan!</strong><br>
                    <small>Silahkan hubungi administrator untuk mendapatkan akses ke halaqah.</small>
                </div>
            </div>
        @endif
        
        <!-- Filter Card -->
        <div class="card fade-in">
            <div class="card-content">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Absensi</h5>
                
                <form id="filterForm" class="filter-section">
                    <!-- Halaqah Filter -->
                    <div class="filter-group mb-3">
                        <label for="halaqahFilter"><i class="fas fa-mosque"></i> Halaqah</label>
                        <select id="halaqahFilter" class="filter-select" required
                            {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 ? 'disabled' : '' }}>
                            <option value="">-- Pilih Halaqah --</option>
                            @foreach ($halaqah as $h)
                                <option value="{{ $h->id }}" 
                                    {{ $halaqah_id == $h->id ? 'selected' : '' }}
                                    {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 && !$halaqah_id ? 'selected' : '' }}>
                                    {{ $h->kode }} - {{ $h->nama_halaqah }}
                                    @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                                        (Halaqah Anda)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
                            <input type="hidden" name="halaqah_id" value="{{ $halaqah->first()->id }}">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i> Otomatis terpilih sebagai halaqah Anda
                            </small>
                        @endif
                    </div>
                    
                    <!-- Tanggal Filter -->
                    <div class="filter-group mb-3">
                        <label for="tanggalFilter"><i class="fas fa-calendar-day"></i> Tanggal</label>
                        <input type="date" id="tanggalFilter" class="filter-input" 
                               value="{{ $tanggal }}" required>
                    </div>
                    
                    <!-- Filter Button -->
                    <button type="button" id="applyFilter" class="filter-button mt-2">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Info Box -->
        @if($halaqah_id)
        <div class="info-box fade-in" id="infoBox">
            <i class="fas fa-calendar-check"></i>
            <div class="info-box-content">
                <div class="info-box-title">TANGGAL ABSENSI</div>
                <div class="info-box-value">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</div>
                @if($halaqah_id)
                    <div class="info-box-title mt-1">HALAQAH: 
                        <span class="info-box-value" style="font-size: 14px;">
                            {{ $halaqah->where('id', $halaqah_id)->first()->nama_halaqah ?? '' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Santri Cards Container -->
        <div id="santriContainer"></div>
        
        <!-- Empty State -->
        <div class="empty-state fade-in" id="emptyState">
            <i class="fas fa-users-slash"></i>
            <h5>Belum ada data absensi</h5>
            <p>Pilih halaqah dan tanggal untuk menampilkan data santri</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons fade-in" id="actionButtons" style="display: none;">
            <button type="button" class="action-btn set-all-btn" onclick="setAllStatus('pagi', 'H')">
                <i class="fas fa-check-circle"></i> Semua Hadir Pagi
            </button>
            <button type="button" class="action-btn set-all-btn" onclick="setAllStatus('malam', 'H')">
                <i class="fas fa-check-circle"></i> Semua Hadir Malam
            </button>
            <button type="button" id="saveAbsenBtn" class="action-btn save-btn">
                <i class="fas fa-save"></i> Simpan Absensi
            </button>
        </div>
    </main>
    
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
            
            <a href="/absen" class="mobile-nav-item active">
                <i class="fas fa-list-check"></i>
                <span>Absensi</span>
            </a>
            
            <a href="/kesehatan" class="mobile-nav-item">
                <i class="fas fa-heartbeat"></i>
                <span>Kesehatan</span>
            </a>
            
            
            <a href="/laporan" class="mobile-nav-item">
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
    
    <!-- Modal Menu Lainnya -->
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
            // Auto-set untuk musyrif dengan 1 halaqah
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 && !$halaqah_id)
                $('#halaqahFilter').prop('disabled', true);
                @if(count($halaqah) == 1)
                    $('#halaqahFilter').val('{{ $halaqah->first()->id }}');
                    setTimeout(() => {
                        loadAbsenData();
                    }, 500);
                @endif
            @endif
            
            // Format tanggal
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            // Status mapping
            const statusMapping = {
                'H': { text: 'Hadir', class: 'status-hadir', icon: 'check-circle' },
                'I': { text: 'Izin', class: 'status-izin', icon: 'clipboard-check' },
                'A': { text: 'Alpa', class: 'status-alpa', icon: 'times-circle' },
                'S': { text: 'Sakit', class: 'status-sakit', icon: 'heartbeat' }
            };
            
            // Get status badge HTML
            function getStatusBadge(status, session) {
                if (!status) return '';
                const mapping = statusMapping[status] || { text: status, class: 'status-hadir', icon: 'question-circle' };
                return `
                    <div class="status-badge ${mapping.class}">
                        <i class="fas fa-${mapping.icon} me-1"></i>${mapping.text}
                    </div>
                `;
            }
            
            // Get status select options
            function getStatusOptions(selectedValue = '') {
                let options = '<option value="">-- Pilih --</option>';
                Object.entries(statusMapping).forEach(([value, data]) => {
                    const selected = value === selectedValue ? 'selected' : '';
                    options += `<option value="${value}" ${selected}>${data.text}</option>`;
                });
                return options;
            }
            
            // Load absen data
            $('#applyFilter').click(function() {
                loadAbsenData();
            });
            
            // Auto-load jika sudah ada filter
            @if($halaqah_id)
                setTimeout(() => {
                    loadAbsenData();
                }, 500);
            @endif
            
            function loadAbsenData() {
                const halaqahId = $('#halaqahFilter').val();
                const tanggal = $('#tanggalFilter').val();
                
                if (!halaqahId || !tanggal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Filter Belum Lengkap',
                        text: 'Harap pilih halaqah dan tanggal terlebih dahulu',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Show loading
                $('#santriContainer').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data absensi...</p>
                    </div>
                `);
                
                // AJAX request
                $.ajax({
                    url: "{{ route('absen.dataMobile') }}",
                    method: 'GET',
                    data: {
                        halaqah_id: halaqahId,
                        tanggal: tanggal
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            displayAbsenData(response.data, tanggal, halaqahId);
                        } else {
                            showEmptyState();
                            $('#infoBox').hide();
                            $('#actionButtons').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading absen data:', xhr);
                        
                        let errorMessage = 'Terjadi kesalahan saat memuat data absensi.';
                        if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki izin untuk mengakses data halaqah ini.';
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
            function displayAbsenData(data, tanggal, halaqahId) {
                if (!data || data.length === 0) {
                    showEmptyState();
                    return;
                }
                
                // Update info box
                const tanggalFormatted = formatDate(tanggal);
                $('#infoBox').show().find('.info-box-value').first().text(tanggalFormatted);
                
                // Generate santri cards
                const container = $('#santriContainer');
                container.empty();
                
                data.forEach((santri, index) => {
                    const cardHtml = generateSantriCard(santri, index + 1);
                    container.append(cardHtml);
                });
                
                // Tampilkan action buttons
                $('#actionButtons').show();
                $('#emptyState').hide();
                
                // Initialize event listeners
                initializeEventListeners();
            }
            
            // Generate card untuk setiap santri
            function generateSantriCard(santri, index) {
                const pagiBadge = getStatusBadge(santri.pagi, 'pagi');
                const malamBadge = getStatusBadge(santri.malam, 'malam');
                
                return `
                    <div class="santri-card fade-in" style="animation-delay: ${index * 0.05}s">
                        <div class="santri-header">
                            <div class="santri-avatar">
                                ${santri.nama.charAt(0).toUpperCase()}
                            </div>
                            <div class="santri-info">
                                <div class="santri-name">${santri.nama}</div>
                                <div class="santri-id">#${santri.id}</div>
                            </div>
                        </div>
                        
                        <div class="absen-status">
                            <div class="absen-row">
                                <div class="absen-column">
                                    <div class="absen-label">
                                        <i class="fas fa-sun text-warning"></i> Pagi
                                    </div>
                                    <select class="absen-select status-select" 
                                            data-santri-id="${santri.id}"
                                            data-session="pagi">
                                        ${getStatusOptions(santri.pagi)}
                                    </select>
                                    <div class="pagi-badge" id="badge-pagi-${santri.id}">
                                        ${pagiBadge}
                                    </div>
                                </div>
                                
                                <div class="absen-column">
                                    <div class="absen-label">
                                        <i class="fas fa-moon text-primary"></i> Malam
                                    </div>
                                    <select class="absen-select status-select" 
                                            data-santri-id="${santri.id}"
                                            data-session="malam">
                                        ${getStatusOptions(santri.malam)}
                                    </select>
                                    <div class="malam-badge" id="badge-malam-${santri.id}">
                                        ${malamBadge}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="catatan-box">
                            <div class="catatan-label">
                                <i class="fas fa-sticky-note text-muted"></i> Catatan
                            </div>
                            <textarea class="catatan-input" 
                                      data-santri-id="${santri.id}"
                                      placeholder="Tambahkan catatan jika diperlukan...">${santri.catatan || ''}</textarea>
                        </div>
                    </div>
                `;
            }
            
            // Initialize event listeners
            function initializeEventListeners() {
                // Status change
                $('.status-select').off('change').on('change', function() {
                    const santriId = $(this).data('santri-id');
                    const session = $(this).data('session');
                    const status = $(this).val();
                    const badgeId = `#badge-${session}-${santriId}`;
                    
                    if (status) {
                        const badge = getStatusBadge(status, session);
                        $(badgeId).html(badge);
                    } else {
                        $(badgeId).html('');
                    }
                });
                
                // Save button
                $('#saveAbsenBtn').off('click').on('click', function() {
                    saveAbsenData();
                });
            }
            
            // Set all status untuk sesi tertentu
            window.setAllStatus = function(session, status) {
                $(`.status-select[data-session="${session}"]`).val(status).trigger('change');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Semua status ${session === 'pagi' ? 'pagi' : 'malam'} diubah menjadi ${statusMapping[status]?.text || 'Hadir'}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            };
            
            // Save absen data
            function saveAbsenData() {
                const halaqahId = $('#halaqahFilter').val();
                const tanggal = $('#tanggalFilter').val();
                
                if (!halaqahId || !tanggal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Tidak Lengkap',
                        text: 'Harap lengkapi filter halaqah dan tanggal',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Prepare data
                const absenData = {};
                let hasData = false;
                
                $('.santri-card').each(function() {
                    const santriId = $(this).find('.status-select').first().data('santri-id');
                    const pagi = $(this).find(`.status-select[data-session="pagi"]`).val();
                    const malam = $(this).find(`.status-select[data-session="malam"]`).val();
                    const catatan = $(this).find('.catatan-input').val();
                    
                    if (pagi || malam) {
                        absenData[santriId] = {
                            pagi: pagi || null,
                            malam: malam || null,
                            catatan: catatan || null
                        };
                        hasData = true;
                    }
                });
                
                if (!hasData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Data',
                        text: 'Tidak ada status absensi yang diisi',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Show loading
                const saveBtn = $('#saveAbsenBtn');
                const originalText = saveBtn.html();
                saveBtn.html('<span class="loading-spinner me-2"></span>Menyimpan...').prop('disabled', true);
                
                // AJAX request
                $.ajax({
                    url: "{{ route('absen.store') }}",
                    method: 'POST',
                    data: {
                        tanggal: tanggal,
                        halaqah_id: halaqahId,
                        absen: absenData,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        saveBtn.html(originalText).prop('disabled', false);
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Absensi berhasil disimpan',
                                confirmButtonColor: '#1a5fb4',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Refresh data
                                loadAbsenData();
                            });
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
                        saveBtn.html(originalText).prop('disabled', false);
                        
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki izin untuk menyimpan absensi.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage,
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            }
            
            // Show empty state
            function showEmptyState() {
                $('#emptyState').show();
                $('#santriContainer').empty();
                $('#actionButtons').hide();
            }
            
            // Initial setup
            @if(!$halaqah_id)
                showEmptyState();
            @endif
            
            // Auto filter untuk musyrif dengan 1 halaqah
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 && !$halaqah_id)
                $(window).on('load', function() {
                    if (!$('#tanggalFilter').val()) {
                        $('#tanggalFilter').val('{{ date("Y-m-d") }}');
                    }
                    
                    if ($('#halaqahFilter').val() && $('#tanggalFilter').val()) {
                        setTimeout(() => {
                            loadAbsenData();
                        }, 1000);
                    }
                });
            @endif
        });
    </script>
</body>
</html>