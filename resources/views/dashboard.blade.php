@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .stat-title {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .card-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }
    .card-success {
        background: linear-gradient(135deg, #198754, #157347);
    }
    .card-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    .card-danger {
        background: linear-gradient(135deg, #dc3545, #b02a37);
    }
    .card-info {
        background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
    }
    .user-info-card {
        background: linear-gradient(135deg, #6f42c1, #59359e);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .user-avatar {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem auto;
    }
    .user-role-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .role-admin {
        background: linear-gradient(135deg, #dc3545, #b02a37);
    }
    .role-musyrif {
        background: linear-gradient(135deg, #198754, #157347);
    }
    .role-santri {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
    }
    .loading-spinner {
        width: 2rem;
        height: 2rem;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
    {{-- INFO PENGGUNA --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="user-info-card">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="text-center">
                    <h5 class="mb-2">{{ auth()->user()->name }}</h5>
                    @php
                        $role = auth()->user()->getRoleNames()->first() ?? 'Pengguna';
                        $roleClass = 'role-' . strtolower($role);
                        $roleIcon = '';
                        switch(strtolower($role)) {
                            case 'admin': $roleIcon = 'fas fa-user-shield'; break;
                            case 'musyrif': $roleIcon = 'fas fa-user-tie'; break;
                            case 'santri': $roleIcon = 'fas fa-user-graduate'; break;
                            default: $roleIcon = 'fas fa-user';
                        }
                    @endphp
                    <span class="user-role-badge {{ $roleClass }}">
                        <i class="{{ $roleIcon }} me-1"></i>{{ ucfirst($role) }}
                    </span>
                    <p class="mt-3 mb-0 small opacity-75">
                        <i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}
                    </p>
                    <p class="small opacity-75">
                        <i class="fas fa-calendar-alt me-1"></i> Login: {{ now()->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- PERBAIKAN: Tambahkan card untuk total perawatan --}}
        <div class="col-md-3">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number" id="total-santri">0</div>
                <div class="stat-title">Total Santri</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-success">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-number" id="total-musyrif">0</div>
                <div class="stat-title">Musyrif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-warning">
                <div class="stat-icon">
                    <i class="fas fa-school"></i>
                </div>
                <div class="stat-number" id="total-halaqah">0</div>
                <div class="stat-title">Halaqah</div>
            </div>
        </div>
    </div>

    {{-- PERBAIKAN: Baris baru untuk statistik tambahan --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card card-danger">
                <div class="stat-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="stat-number" id="total-perawatan">0</div>
                <div class="stat-title">Dalam Perawatan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-info">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-number" id="total-kegiatan">0</div>
                <div class="stat-title">Kegiatan Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-primary">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-number" id="total-absensi">0</div>
                <div class="stat-title">Absensi Hari Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number" id="percentage">0%</div>
                <div class="stat-title">Kehadiran</div>
            </div>
        </div>
    </div>

    {{-- INFO HAK AKSES BERDASARKAN ROLE --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Hak Akses 
                        <span class="badge {{ $roleClass }} ms-2">{{ ucfirst($role) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('admin'))
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Semua Halaman</strong>
                                    <p class="text-muted mb-0 small">Akses penuh ke semua fitur</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Manajemen User</strong>
                                    <p class="text-muted mb-0 small">Tambah/edit user dan role</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Master Data</strong>
                                    <p class="text-muted mb-0 small">Kelola data master sistem</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Laporan Lengkap</strong>
                                    <p class="text-muted mb-0 small">Akses semua laporan</p>
                                </div>
                            </div>
                        </div>
                        
                        @elseif(auth()->user()->hasRole('musyrif'))
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Ceklist Kegiatan</strong>
                                    <p class="text-muted mb-0 small">Input kegiatan harian santri</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Absensi Santri</strong>
                                    <p class="text-muted mb-0 small">Catat kehadiran pagi/malam</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Data Kesehatan</strong>
                                    <p class="text-muted mb-0 small">Monitor kesehatan santri</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Laporan</strong>
                                    <p class="text-muted mb-0 small">Lihat dan kirim laporan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle text-danger fa-lg me-3"></i>
                                <div>
                                    <strong>Master Data</strong>
                                    <p class="text-muted mb-0 small">Tidak bisa akses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle text-danger fa-lg me-3"></i>
                                <div>
                                    <strong>Manajemen User</strong>
                                    <p class="text-muted mb-0 small">Tidak bisa akses</p>
                                </div>
                            </div>
                        </div>
                        
                        @elseif(auth()->user()->hasRole('santri'))
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Profil Pribadi</strong>
                                    <p class="text-muted mb-0 small">Lihat dan edit profil sendiri</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success fa-lg me-3"></i>
                                <div>
                                    <strong>Laporan Pribadi</strong>
                                    <p class="text-muted mb-0 small">Lihat laporan kegiatan sendiri</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle text-danger fa-lg me-3"></i>
                                <div>
                                    <strong>Data Santri Lain</strong>
                                    <p class="text-muted mb-0 small">Tidak bisa akses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle text-danger fa-lg me-3"></i>
                                <div>
                                    <strong>Master Data</strong>
                                    <p class="text-muted mb-0 small">Tidak bisa akses</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terbaru Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="100">Waktu</th>
                                    <th width="150">Jenis</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody id="aktivitas-list">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <div class="loading-spinner mx-auto"></div>
                                        <p class="mt-2">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifikasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div id="notifikasi-list">
                        <div class="text-center">
                            <div class="loading-spinner mx-auto"></div>
                            <p class="mt-2">Memuat notifikasi...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="display-4 mb-3" id="current-date">--</h1>
                        <h2 class="mb-4" id="current-day">--</h2>
                        <div class="badge bg-primary fs-6 p-2" id="current-time">--:--:--</div>
                        <hr class="my-3">
                        <div class="mt-3">
                            <small class="text-muted d-block">Anda login sebagai</small>
                            <span class="badge {{ $roleClass }} fs-6 mt-2 px-3 py-2">
                                <i class="{{ $roleIcon }} me-1"></i>{{ ucfirst($role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Pastikan CSRF token ada untuk semua request AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Update tanggal dan waktu real-time
        function updateDateTime() {
            const now = new Date();
            $('#current-date').text(now.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            }));
            $('#current-day').text(now.toLocaleDateString('id-ID', { 
                weekday: 'long' 
            }));
            $('#current-time').text(now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            }));
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Load statistik dari database - PERBAIKI URL
        function loadStatistik() {
            console.log('Loading statistik...');
            $.ajax({
                url: '{{ route("dashboard.statistik") }}', // GUNAKAN NAMA ROUTE
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Statistik response:', response);
                    if (response.success) {
                        $('#total-santri').text(response.data.total_santri || 0);
                        $('#total-musyrif').text(response.data.total_musyrif || 0);
                        $('#total-halaqah').text(response.data.total_halaqah || 0);
                        $('#total-perawatan').text(response.data.total_perawatan || 0);
                        
                        // Hitung persentase kehadiran (jika ada data)
                        const totalSantri = response.data.total_santri || 0;
                        const totalPerawatan = response.data.total_perawatan || 0;
                        if (totalSantri > 0) {
                            const persentase = Math.round(((totalSantri - totalPerawatan) / totalSantri) * 100);
                            $('#percentage').text(persentase + '%');
                        }
                    } else {
                        console.error('API error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    // Set default values
                    $('#total-santri').text('?');
                    $('#total-musyrif').text('?');
                    $('#total-halaqah').text('?');
                    $('#total-perawatan').text('?');
                    $('#percentage').text('?%');
                }
            });
        }
        
        // Load aktivitas dari database
        function loadAktivitas() {
            $.ajax({
                url: '{{ route("dashboard.aktivitas") }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Aktivitas response:', response);
                    if (response.success) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                html += `
                                    <tr>
                                        <td><span class="badge bg-secondary">${item.waktu}</span></td>
                                        <td><span class="badge ${item.jenis === 'Ceklist Kegiatan' ? 'bg-success' : item.jenis === 'Absensi' ? 'bg-info' : item.jenis === 'Kesehatan' ? 'bg-danger' : 'bg-warning'}">${item.jenis}</span></td>
                                        <td>${item.detail}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = `
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <p>Belum ada aktivitas hari ini</p>
                                    </td>
                                </tr>
                            `;
                        }
                        $('#aktivitas-list').html(html);
                    } else {
                        $('#aktivitas-list').html(`
                            <tr>
                                <td colspan="3" class="text-center text-danger py-4">
                                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                    <p>API Error: ${response.message}</p>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal memuat aktivitas:', error);
                    $('#aktivitas-list').html(`
                        <tr>
                            <td colspan="3" class="text-center text-danger py-4">
                                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                <p>Gagal memuat aktivitas</p>
                                <small>Error: ${error}</small>
                            </td>
                        </tr>
                    `);
                }
            });
        }
        
        // Load notifikasi dari database
        function loadNotifikasi() {
            $.ajax({
                url: '{{ route("dashboard.notifikasi") }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Notifikasi response:', response);
                    if (response.success) {
                        let html = '';
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(item) {
                                let alertClass = '';
                                switch(item.tipe) {
                                    case 'danger': alertClass = 'alert-danger'; break;
                                    case 'warning': alertClass = 'alert-warning'; break;
                                    case 'success': alertClass = 'alert-success'; break;
                                    default: alertClass = 'alert-info';
                                }
                                
                                html += `
                                    <div class="alert ${alertClass} mb-2">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-${item.icon} fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>${item.judul}</strong>
                                                    <small class="text-muted">${item.waktu}</small>
                                                </div>
                                                <p class="mb-0 mt-1">${item.pesan}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = `
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-bell-slash fa-2x mb-3"></i>
                                    <p>Tidak ada notifikasi</p>
                                </div>
                            `;
                        }
                        $('#notifikasi-list').html(html);
                    } else {
                        $('#notifikasi-list').html(`
                            <div class="text-center text-danger py-3">
                                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                <p>API Error: ${response.message}</p>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal memuat notifikasi:', error);
                    $('#notifikasi-list').html(`
                        <div class="text-center text-danger py-3">
                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                            <p>Gagal memuat notifikasi</p>
                            <small>Error: ${error}</small>
                        </div>
                    `);
                }
            });
        }
        
        // Load semua data pertama kali
        loadStatistik();
        loadAktivitas();
        loadNotifikasi();
        
        // Refresh data setiap 60 detik
        setInterval(function() {
            loadStatistik();
            loadAktivitas();
            loadNotifikasi();
        }, 60000);
        
        // Tampilkan welcome message berdasarkan role
        setTimeout(function() {
            const role = "{{ strtolower(auth()->user()->getRoleNames()->first() ?? 'pengguna') }}";
            let message = '';
            
            switch(role) {
                case 'admin':
                    message = 'Selamat datang, Administrator! Anda memiliki akses penuh ke sistem.';
                    break;
                case 'musyrif':
                    message = 'Selamat datang, Musyrif! Silakan kelola kegiatan, absensi, dan kesehatan santri.';
                    break;
                case 'santri':
                    message = 'Selamat datang, Santri! Anda bisa melihat laporan dan profil pribadi.';
                    break;
                default:
                    message = 'Selamat datang di Sistem Boarding Management!';
            }
            
            if (message && typeof Swal !== 'undefined') {
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
            }
        }, 1000);
    });
</script>
@endpush