<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | Boarding Admin</title>
    
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
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --purple-color: #6f42c1;
            --orange-color: #fd7e14;
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
            font-size: 20px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header .subtitle {
            font-size: 12px;
            opacity: 0.9;
        }
        
        /* Role badge */
        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
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
        
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--purple-color), #59359e);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 3px;
        }
        
        .user-email {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
            text-align: center;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
            font-size: 18px;
            color: white;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
        }
        
        /* Quick Actions */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: var(--primary-color);
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .action-btn {
            background-color: white;
            border-radius: 12px;
            padding: 15px 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: var(--primary-color);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px auto;
            font-size: 16px;
            color: white;
        }
        
        .action-label {
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Recent Activity */
        .activity-card {
            background-color: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 3px;
        }
        
        .activity-desc {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .activity-time {
            font-size: 11px;
            color: #999;
        }
        
        /* Time Card */
        .time-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .current-date {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .current-day {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .current-time {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-family: 'Courier New', monospace;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #ccc;
        }
        
        .empty-state h5 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
        }
        
        .empty-state p {
            font-size: 13px;
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
                font-size: 22px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
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
        <h1><i class="fas fa-home"></i> Dashboard Boarding</h1>
        <p class="subtitle">Sistem Manajemen Pondok Pesantren</p>
        <div class="role-badge">
            <i class="fas fa-user-tag me-1"></i>
            {{ auth()->user()->getRoleNames()->first() ?? 'Pengguna' }}
        </div>
    </div>
    
    <!-- Main Content -->
    <main class="container">
        <!-- Welcome Card -->
        <div class="welcome-card fade-in">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-email">{{ auth()->user()->email }}</div>
                    <div class="role-badge" style="background: rgba(255,255,255,0.3);">
                        <i class="fas fa-shield-alt me-1"></i>
                        {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Pengguna') }}
                    </div>
                </div>
            </div>
            <p style="font-size: 13px; opacity: 0.9; margin: 0;">
                <i class="fas fa-calendar-alt me-1"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid fade-in">
            <!-- Santri -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value" id="stat-santri">0</div>
                <div class="stat-label">Total Santri</div>
            </div>
            
            <!-- Musyrif -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #157347);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-value" id="stat-musyrif">0</div>
                <div class="stat-label">Musyrif</div>
            </div>
            
            <!-- Halaqah -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #e0a800);">
                    <i class="fas fa-mosque"></i>
                </div>
                <div class="stat-value" id="stat-halaqah">0</div>
                <div class="stat-label">Halaqah</div>
            </div>
            
            <!-- Kegiatan Hari Ini -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--info-color), #0aa2c0);">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-value" id="stat-kegiatan">0</div>
                <div class="stat-label">Kegiatan Hari Ini</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <h5 class="section-title fade-in">
            <i class="fas fa-bolt"></i> Akses Cepat
        </h5>
        
        <div class="actions-grid fade-in">
            @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
            <!-- Ceklist -->
            <a href="/ceklist" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, var(--success-color), #157347);">
                    <i class="fas fa-check-square"></i>
                </div>
                <div class="action-label">Ceklist</div>
            </a>
            
            <!-- Absensi -->
            <a href="/absen" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, var(--info-color), #0aa2c0);">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="action-label">Absensi</div>
            </a>
            
            <!-- Kesehatan -->
            <a href="/kesehatan" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, var(--danger-color), #b02a37);">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="action-label">Kesehatan</div>
            </a>
            
            <!-- Laporan -->
            <a href="/laporan" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, var(--purple-color), #59359e);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-label">Laporan</div>
            </a>
            
            <!-- Halaqah -->
            <a href="/halaqah" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #fd7e14, #e96c0d);">
                    <i class="fas fa-school"></i>
                </div>
                <div class="action-label">Halaqah</div>
            </a>
            @endif
            
            <!-- Profil -->
            <a href="/profile" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #6c757d, #545b62);">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="action-label">Profil</div>
            </a>
        </div>
        
        <!-- Recent Activity -->
        <h5 class="section-title fade-in">
            <i class="fas fa-history"></i> Aktivitas Terbaru
        </h5>
        
        <div class="activity-card fade-in">
            <div id="activityList">
                <!-- Loading state -->
                <div class="activity-item">
                    <div class="activity-icon" style="background-color: #f0f0f0;">
                        <i class="fas fa-spinner fa-spin text-muted"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Memuat aktivitas...</div>
                        <div class="activity-desc">Sedang mengambil data terbaru</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Time Card -->
        <div class="time-card fade-in">
            <div class="current-date" id="currentDate">--</div>
            <div class="current-day" id="currentDay">--</div>
            <div class="current-time" id="currentTime">--:--:--</div>
            <div style="font-size: 12px; opacity: 0.9;">
                <i class="fas fa-clock me-1"></i> Waktu Server
            </div>
        </div>
    </main>
    
    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-nav">
        <div class="mobile-nav-container">
            <a href="/dashboard" class="mobile-nav-item active">
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
            <a href="#" class="mobile-nav-item" data-bs-toggle="modal" data-bs-target="#moreMenuModal">
                <i class="fas fa-bars"></i>
                <span>Menu</span>
            </a>
        </div>
    </nav>
    
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
                        <a href="/laporan" class="list-group-item list-group-item-action">
                            <i class="fa fa-chart-bar me-2"></i> Laporan
                        </a>
                        @endif
                        
                        @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                        <a href="/halaqah" class="list-group-item list-group-item-action">
                            <i class="fa fa-mosque me-2"></i> Halaqah
                        </a>
                        @endif
                        
                        @if(auth()->user()->hasRole('admin'))
                        <a href="/musyrif" class="list-group-item list-group-item-action">
                            <i class="fa fa-user-tie me-2"></i> Musyrif
                        </a>
                        @endif
                        
                        @if(auth()->user()->hasRole('admin'))
                        <a href="/kegiatan" class="list-group-item list-group-item-action">
                            <i class="fa fa-tasks me-2"></i> Kegiatan
                        </a>
                        @endif
                        
                        <a href="/profile" class="list-group-item list-group-item-action">
                            <i class="fa fa-user-circle me-2"></i> Profil
                        </a>
                        
                        <a href="/pengaturan" class="list-group-item list-group-item-action">
                            <i class="fa fa-cog me-2"></i> Pengaturan
                        </a>
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
            // Real-time clock
            function updateClock() {
                const now = new Date();
                
                // Format date: Senin, 1 Jan 2024
                const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                $('#currentDate').text(now.toLocaleDateString('id-ID', optionsDate));
                
                // Format day: Senin
                $('#currentDay').text(now.toLocaleDateString('id-ID', { weekday: 'long' }));
                
                // Format time: 14:30:45
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                $('#currentTime').text(`${hours}:${minutes}:${seconds}`);
            }
            
            updateClock();
            setInterval(updateClock, 1000);
            
            // Load statistics
            function loadStatistics() {
                $.ajax({
                    url: '/api/dashboard/activities',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#stat-santri').text(response.data.santri || 0);
                            $('#stat-musyrif').text(response.data.musyrif || 0);
                            $('#stat-halaqah').text(response.data.halaqah || 0);
                            $('#stat-kegiatan').text(response.data.kegiatan || 0);
                        }
                    },
                    error: function() {
                        // Fallback values
                        $('#stat-santri').text('12');
                        $('#stat-musyrif').text('3');
                        $('#stat-halaqah').text('5');
                        $('#stat-kegiatan').text('8');
                    }
                });
            }
            
            // Load recent activities
            function loadActivities() {
                $.ajax({
                    url: '/api/dashboard/activities',
                    method: 'GET',
                    success: function(response) {
                        const container = $('#activityList');
                        container.empty();
                        
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(function(activity, index) {
                                const colors = [
                                    'linear-gradient(135deg, #0d6efd, #0a58ca)',
                                    'linear-gradient(135deg, #198754, #157347)',
                                    'linear-gradient(135deg, #ffc107, #e0a800)',
                                    'linear-gradient(135deg, #dc3545, #b02a37)',
                                    'linear-gradient(135deg, #6f42c1, #59359e)'
                                ];
                                
                                const icons = {
                                    'ceklist': 'check-square',
                                    'absensi': 'list-check',
                                    'kesehatan': 'heartbeat',
                                    'laporan': 'chart-bar',
                                    'halaqah': 'mosque',
                                    'default': 'circle'
                                };
                                
                                const colorIndex = index % colors.length;
                                const icon = icons[activity.type] || icons.default;
                                
                                const html = `
                                    <div class="activity-item">
                                        <div class="activity-icon" style="background: ${colors[colorIndex]}">
                                            <i class="fas fa-${icon}"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">${activity.title}</div>
                                            <div class="activity-desc">${activity.description}</div>
                                            <div class="activity-time">${activity.time}</div>
                                        </div>
                                    </div>
                                `;
                                
                                container.append(html);
                            });
                        } else {
                            container.html(`
                                <div class="empty-state">
                                    <i class="fas fa-history"></i>
                                    <h5>Belum ada aktivitas</h5>
                                    <p>Mulai catat kegiatan pertama Anda</p>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        const container = $('#activityList');
                        container.html(`
                            <div class="empty-state">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                                <h5>Gagal memuat</h5>
                                <p>Coba refresh halaman</p>
                            </div>
                        `);
                    }
                });
            }
            
            // Initial load
            loadStatistics();
            loadActivities();
            
            // Auto-refresh every 30 seconds
            setInterval(function() {
                loadStatistics();
                loadActivities();
            }, 30000);
            
            // Welcome message based on role
            setTimeout(function() {
                const role = "{{ strtolower(auth()->user()->getRoleNames()->first() ?? 'pengguna') }}";
                let message = '';
                let icon = 'info';
                
                switch(role) {
                    case 'admin':
                        message = 'Selamat datang Administrator! Anda memiliki akses penuh ke sistem.';
                        icon = 'user-shield';
                        break;
                    case 'musyrif':
                        message = 'Halo Musyrif! Siap memantau kegiatan santri hari ini?';
                        icon = 'user-tie';
                        break;
                    case 'santri':
                        message = 'Selamat datang Santri! Periksa jadwal kegiatan Anda.';
                        icon = 'user-graduate';
                        break;
                    default:
                        message = 'Selamat datang di Boarding Management System!';
                        icon = 'home';
                }
                
                // Show toast notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                
                Toast.fire({
                    icon: 'info',
                    title: message
                });
            }, 1500);
            
            // Pull to refresh simulation
            let startY = 0;
            let pullDistance = 0;
            
            $(document).on('touchstart', function(e) {
                if ($(window).scrollTop() === 0) {
                    startY = e.originalEvent.touches[0].pageY;
                }
            });
            
            $(document).on('touchmove', function(e) {
                if ($(window).scrollTop() === 0) {
                    pullDistance = e.originalEvent.touches[0].pageY - startY;
                    
                    if (pullDistance > 50) {
                        // Show refresh indicator
                        console.log('Pull to refresh');
                    }
                }
            });
            
            $(document).on('touchend', function(e) {
                if (pullDistance > 100) {
                    // Refresh data
                    loadStatistics();
                    loadActivities();
                    
                    // Show refresh animation
                    Swal.fire({
                        icon: 'success',
                        title: 'Data diperbarui',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
                
                startY = 0;
                pullDistance = 0;
            });
        });
    </script>
</body>
</html>