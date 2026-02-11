<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ceklist Kegiatan Harian Santri | Boarding Admin</title>
    
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
        
        /* Header seperti desain pertama */
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Role badge di header */
        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 5px;
            display: inline-block;
            font-weight: 500;
        }
        
        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 15px;
        }
        
        /* Card seperti desain pertama */
        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
            border: none;
        }
        
        .card-content {
            padding: 20px;
        }
        
        /* Info Role Card */
        .role-info-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .role-info-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .role-info-text {
            flex: 1;
        }
        
        .role-info-text small {
            opacity: 0.9;
            font-size: 13px;
        }
        
        /* Filter Section seperti desain pertama */
        .filter-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .filter-group {
            flex: 1;
            min-width: 100%;
        }
        
        @media (min-width: 768px) {
            .filter-group {
                min-width: 250px;
            }
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        
        .filter-select, .filter-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
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
        
        /* Warning jika musyrif tanpa halaqah */
        .no-halaqah-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            display: none;
        }
        
        .no-halaqah-warning i {
            font-size: 18px;
        }
        
        /* Date Info Box seperti desain pertama */
        .date-info {
            background-color: var(--light-blue);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        @media (min-width: 768px) {
            .date-info {
                flex-direction: row;
                justify-content: space-between;
            }
        }
        
        .date-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .date-icon {
            background-color: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .date-text {
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }
        
        .date-value {
            font-size: 18px;
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* Table Container */
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            background-color: white;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Table seperti desain pertama */
        #ceklistTable {
            width: 100% !important;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        #ceklistTable thead th {
            background-color: var(--light-blue);
            color: var(--primary-color);
            font-weight: 700;
            padding: 15px 12px;
            border-bottom: 2px solid var(--primary-color);
            text-align: left;
            white-space: nowrap;
        }
        
        #ceklistTable tbody td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        #ceklistTable tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        #ceklistTable tbody tr:last-child td {
            border-bottom: none;
        }
        
        .no-column {
            width: 60px;
            text-align: center;
            font-weight: 600;
            color: #555;
        }
        
        .checkbox-cell {
            text-align: center;
        }
        
        .checkbox-cell input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        /* Action Buttons seperti desain pertama */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }
        
        .filter-btn {
            background-color: var(--light-blue);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            flex: 1;
        }
        
        .save-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            flex: 1;
        }
        
        .filter-btn:hover, .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .save-btn:hover {
            background-color: var(--primary-dark);
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
        
        /* Loader */
        .loader {
            display: none;
            text-align: center;
            padding: 30px;
        }
        
        .loader.active {
            display: block;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            display: none;
        }
        
        .empty-state.active {
            display: block;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ccc;
        }
        
        /* Responsive */
        @media (max-width: 767px) {
            .container {
                padding: 15px;
            }
            
            .card-content {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .header .subtitle {
                font-size: 13px;
            }
            
            .date-info {
                flex-direction: column;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .filter-btn, .save-btn {
                width: 100%;
            }
            
            .role-info-card {
                padding: 12px;
            }
            
            .role-info-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
        }
        
        @media (min-width: 768px) {
            .mobile-nav {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header seperti desain pertama -->
    <div class="header">
        <h1><i class="fas fa-tasks"></i> Ceklist Kegiatan Harian Santri</h1>
        <p class="subtitle">Pantau dan catat kegiatan harian santri dengan sistematis</p>
        <div class="role-badge">
            <i class="fas fa-user-tag me-1"></i>
            {{ auth()->user()->getRoleNames()->first() }}
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="container">
        <div class="card">
            <div class="card-content">
                <!-- Role Info -->
                <div class="role-info-card">
                    <div class="role-info-icon">
                        @if(auth()->user()->hasRole('admin'))
                            <i class="fas fa-user-shield"></i>
                        @elseif(auth()->user()->hasRole('musyrif'))
                            <i class="fas fa-user-tie"></i>
                        @endif
                    </div>
                    <div class="role-info-text">
                        <strong>
                            @if(auth()->user()->hasRole('admin'))
                                Administrator System
                            @elseif(auth()->user()->hasRole('musyrif'))
                                Musyrif - Pengampu Halaqah
                            @endif
                        </strong>
                        <br>
                        <small>
                            @if(auth()->user()->hasRole('admin'))
                                Anda dapat mengakses semua halaqah dan data santri.
                            @elseif(auth()->user()->hasRole('musyrif'))
                                Anda hanya dapat mengisi ceklist untuk halaqah yang Anda pegang.
                            @endif
                        </small>
                    </div>
                </div>
                
                <!-- Warning jika musyrif tanpa halaqah -->
                @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 0)
                <div class="no-halaqah-warning" id="noHalaqahWarning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Belum ada halaqah yang ditugaskan!</strong><br>
                        <small>Silahkan hubungi administrator untuk mendapatkan akses ke halaqah.</small>
                    </div>
                </div>
                @endif
                
                <!-- Filter Section seperti desain pertama -->
                <div class="filter-section">
                    <div class="filter-group">
                        <label for="halaqahFilter"><i class="fas fa-mosque"></i> Pilih Halaqah</label>
                        <select id="halaqahFilter" class="filter-select" {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 ? 'disabled' : '' }}>
                            <option value="">-- Pilih Halaqah --</option>
                            @foreach ($halaqah as $h)
                                <option value="{{ $h->id }}" 
                                    {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 ? 'selected' : '' }}>
                                    {{ $h->kode }} - {{ $h->nama_halaqah }}
                                    @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                                        (Halaqah Anda)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle"></i> Otomatis terpilih sebagai halaqah Anda
                        </small>
                        @endif
                    </div>
                    
                    <div class="filter-group">
                        <label for="kegiatanUtama"><i class="fas fa-list-check"></i> Pilih Kegiatan Utama</label>
                        <select id="kegiatanUtama" class="filter-select">
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach ($kegiatan as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="tanggal"><i class="fas fa-calendar-day"></i> Tanggal</label>
                        <input type="date" id="tanggal" class="filter-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <!-- Date Info seperti desain pertama -->
                <div class="date-info">
                    <div class="date-box">
                        <div class="date-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <div class="date-text">Tanggal</div>
                            <div class="date-value" id="dateDisplay">{{ date('d/m/Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="date-box">
                        <div class="date-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="date-text">Jumlah Santri</div>
                            <div class="date-value" id="santriCount">0</div>
                        </div>
                    </div>
                    
                    <div class="date-box">
                        <div class="date-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="date-text">Checklist Terisi</div>
                            <div class="date-value" id="checklistCount">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Loader -->
                <div class="loader" id="tableLoader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
                
                <!-- Empty State -->
                <div class="empty-state" id="emptyState">
                    <i class="fas fa-clipboard-list"></i>
                    <h5>Belum ada data</h5>
                    <p>Pilih halaqah dan kegiatan utama untuk menampilkan checklist</p>
                </div>
                
                <!-- Checklist Form -->
                <form id="ceklistForm">
                    @csrf
                    <input type="hidden" name="halaqah_id" id="halaqah_id">
                    <input type="hidden" name="kegiatan_root_id" id="kegiatan_root_id">
                    <input type="hidden" name="tanggal" id="tanggalForm" value="{{ date('Y-m-d') }}">

                    <div class="table-container" id="tableContainer">
                        <table class="table" id="ceklistTable">
                            <thead id="tableHead">
                                <tr>
                                    <th class="no-column">No</th>
                                    <th>Nama Santri</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data akan diisi via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="action-buttons">
                        <button type="button" id="filterBtn" class="filter-btn">
                            <i class="fas fa-filter"></i> Filter Data
                        </button>
                        
                        <button type="submit" class="save-btn" id="saveBtn">
                            <i class="fas fa-save"></i> Simpan Checklist
                        </button>
                    </div>
                </form>
            </div>
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
            <a href="/ceklist" class="mobile-nav-item active">
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
    
    <!-- Modal Menu Lainnya (Mobile) dengan pembatasan role -->
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
            // Cek apakah musyrif punya halaqah
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 0)
                $('#noHalaqahWarning').show();
                $('#halaqahFilter, #kegiatanUtama, #filterBtn').prop('disabled', true);
            @endif
            
            // Update date display
            $('#tanggal').change(function() {
                updateDateDisplay();
            });
            
            // Filter button click
            $('#filterBtn').click(function() {
                loadChecklistData();
            });
            
            // Auto load jika musyrif dan hanya punya 1 halaqah
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
            $(window).on('load', function() {
                if ($('#kegiatanUtama').val()) {
                    setTimeout(() => {
                        loadChecklistData();
                    }, 1000);
                }
            });
            @endif
            
            // Load checklist data
            function loadChecklistData() {
                const halaqahId = $('#halaqahFilter').val();
                const kegiatanId = $('#kegiatanUtama').val();
                const tanggal = $('#tanggal').val();
                
                if (!halaqahId || !kegiatanId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Pilih halaqah dan kegiatan utama terlebih dahulu!',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Set hidden form values
                $('#halaqah_id').val(halaqahId);
                $('#kegiatan_root_id').val(kegiatanId);
                $('#tanggalForm').val(tanggal);
                
                // Show loader
                showLoader();
                
                // Gunakan endpoint yang berhasil dari controller
                $.ajax({
                    url: "{{ route('ceklist.dataMobile') }}",
                    method: 'GET',
                    data: {
                        halaqah_id: halaqahId,
                        kegiatan_id: kegiatanId,
                        tanggal: tanggal
                    },
                    success: function(response) {
                        if (response.success) {
                            // Get sub-kegiatan
                            getSubKegiatan(kegiatanId, response.data);
                        } else {
                            hideLoader();
                            showEmptyState();
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#1a5fb4'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading data:', error);
                        
                        hideLoader();
                        showEmptyState();
                        
                        let errorMessage = 'Terjadi kesalahan saat memuat data checklist.';
                        if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki izin untuk mengakses data halaqah ini.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage,
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            }
            
            function getSubKegiatan(kegiatanId, checklistData) {
                $.ajax({
                    url: "{{ url('ceklist/sub-kegiatan') }}",
                    method: 'GET',
                    data: { kegiatan_id: kegiatanId },
                    success: function(response) {
                        if (response.success) {
                            renderTable(checklistData, response.data);
                        } else {
                            renderTable(checklistData, []);
                        }
                    },
                    error: function() {
                        console.log('Failed to get sub-kegiatan, using default');
                        renderTable(checklistData, []);
                    }
                });
            }
            
            function renderTable(data, subs) {
                hideLoader();
                
                if (!data || data.length === 0) {
                    showEmptyState();
                    $('#tableContainer').hide();
                    return;
                }
                
                // Sembunyikan empty state
                $('#emptyState').removeClass('active');
                
                // Build table header
                let headerHtml = '<tr><th class="no-column">No</th><th>Nama Santri</th>';
                
                if (subs && subs.length > 0) {
                    subs.forEach(function(sub) {
                        headerHtml += `<th>${sub.nama_kegiatan.toUpperCase()}</th>`;
                    });
                }
                
                headerHtml += '</tr>';
                $('#tableHead').html(headerHtml);
                
                // Build table body
                let tableBody = '';
                let santriCount = 0;
                let checklistCount = 0;
                
                if (data.length > 0) {
                    data.forEach(function(row, index) {
                        santriCount++;
                        
                        let rowHtml = `<tr>
                            <td class="no-column">${index + 1}</td>
                            <td><strong>${row.nama_user || 'N/A'}</strong></td>`;
                        
                        // Generate checkbox columns
                        if (subs && subs.length > 0) {
                            subs.forEach(function(sub) {
                                const key = 'keg_' + sub.id;
                                const isChecked = row[key] == 1;
                                if (isChecked) checklistCount++;
                                
                                const userId = row.user_id;
                                const kegiatanId = sub.id;
                                
                                rowHtml += `<td class="checkbox-cell">
                                    <input type="checkbox" 
                                           name="status[${userId}][${kegiatanId}]"
                                           data-user="${userId}"
                                           data-kegiatan="${kegiatanId}"
                                           value="1"
                                           ${isChecked ? 'checked' : ''}
                                           onchange="updateChecklistCount()">
                                </td>`;
                            });
                        }
                        
                        rowHtml += '</tr>';
                        tableBody += rowHtml;
                    });
                } else {
                    tableBody = `<tr>
                        <td colspan="${subs ? 2 + subs.length : 2}" class="text-center py-4">
                            <i class="fas fa-info-circle text-muted"></i>
                            <p class="mt-2">Tidak ada data santri untuk halaqah yang dipilih</p>
                        </td>
                    </tr>`;
                }
                
                $('#tableBody').html(tableBody);
                $('#tableContainer').show();
                
                // Update counts
                updateCounts(santriCount, checklistCount);
            }
            
            // Global function untuk update count
            window.updateChecklistCount = function() {
                const checkedCount = $('#tableBody input[type="checkbox"]:checked').length;
                $('#checklistCount').text(checkedCount);
            };
            
            // Update counts
            function updateCounts(santriCount, checklistCount) {
                $('#santriCount').text(santriCount);
                $('#checklistCount').text(checklistCount);
            }
            
            // Form submission
            $('#ceklistForm').submit(function(e) {
                e.preventDefault();
                
                const halaqahId = $('#halaqah_id').val();
                const kegiatanId = $('#kegiatan_root_id').val();
                
                if (!halaqahId || !kegiatanId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan filter data terlebih dahulu!',
                        confirmButtonColor: '#1a5fb4'
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Menyimpan',
                    text: 'Sedang menyimpan checklist...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit form
                $.ajax({
                    url: "{{ route('ceklist.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Checklist berhasil disimpan!',
                                confirmButtonColor: '#1a5fb4',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload data
                                loadChecklistData();
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
                    error: function(xhr, status, error) {
                        console.error('Error saving checklist:', error);
                        
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki izin untuk menyimpan data checklist.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage,
                            confirmButtonColor: '#1a5fb4'
                        });
                    }
                });
            });
            
            // Helper functions
            function updateDateDisplay() {
                const date = new Date($('#tanggal').val());
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                $('#dateDisplay').text(formattedDate);
            }
            
            function showLoader() {
                $('#tableLoader').addClass('active');
                $('#emptyState').removeClass('active');
                $('#tableContainer').hide();
            }
            
            function hideLoader() {
                $('#tableLoader').removeClass('active');
            }
            
            function showEmptyState() {
                $('#emptyState').addClass('active');
                $('#tableContainer').hide();
            }
            
            // Initial setup
            updateDateDisplay();
            
            // Auto filter jika ada parameter di URL
            const urlParams = new URLSearchParams(window.location.search);
            const halaqahParam = urlParams.get('halaqah');
            const kegiatanParam = urlParams.get('kegiatan');
            
            if (halaqahParam && kegiatanParam) {
                $('#halaqahFilter').val(halaqahParam);
                $('#kegiatanUtama').val(kegiatanParam);
                setTimeout(() => {
                    loadChecklistData();
                }, 500);
            }
        });
    </script>
</body>
</html>