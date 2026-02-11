<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Santri | Boarding Admin</title>

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- DataTables --}}
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
        
        /* Button Styling - SAMA PERSIS dengan Musyrif */
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
        
        /* Table Styling - SAMA PERSIS dengan Musyrif */
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
        
        /* Action Buttons - SAMA PERSIS dengan Musyrif */
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
        
        /* Badge Styling - SAMA PERSIS dengan Musyrif */
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
        
        /* Modal Styling - SAMA PERSIS dengan Musyrif */
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
        
        /* Form Styling - SAMA PERSIS dengan Musyrif */
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
        
        /* Status Badges - SAMA PERSIS dengan Musyrif */
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
        
        /* Avatar - SAMA PERSIS dengan Musyrif */
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
        
        /* Animation - SAMA PERSIS dengan Musyrif */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Loading - SAMA PERSIS dengan Musyrif */
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
        
        /* Control Panel - SAMA PERSIS dengan Musyrif */
        .control-panel {
            background: white;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            box-shadow: var(--card-shadow);
        }
        
        /* Empty State - SAMA PERSIS dengan Musyrif */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        /* Info Badge - SAMA PERSIS dengan Musyrif */
        .info-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-right: 10px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    {{-- NAVBAR - SAMA PERSIS dengan Musyrif --}}
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-school me-2"></i>Boarding Admin
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/halaqah"><i class="fas fa-users me-1"></i> Halaqah</a></li>
                    <li class="nav-item"><a class="nav-link" href="/musyrif"><i class="fas fa-user-tie me-1"></i> Musyrif</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/santri"><i class="fas fa-user-graduate me-1"></i> Santri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kegiatan"><i class="fas fa-tasks me-1"></i> Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ceklist"><i class="fas fa-check-circle me-1"></i> Ceklist</a></li>
                    <li class="nav-item"><a class="nav-link" href="/absen"><i class="fas fa-calendar-check me-1"></i> Absensi</a></li>
                    <li class="nav-item"><a class="nav-link" href="/kesehatan"><i class="fas fa-heartbeat me-1"></i> Kesehatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/laporan"><i class="fas fa-file-alt me-1"></i> Laporan</a></li>
                </ul>
            </div>

            <form action="/logout" method="POST" class="d-flex ms-3">
                @csrf
                <button type="submit" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- CONTENT - SAMA PERSIS dengan Musyrif --}}
    <main class="container mb-5 fade-in">
        
        {{-- HEADER CARD - SAMA PERSIS dengan Musyrif --}}
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Data Santri</h4>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <button class="btn btn-primary px-4" id="btnAdd">
                            <i class="fas fa-plus me-2"></i>Tambah Santri
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <label class="form-label fw-semibold me-3 mb-0">Tampilkan:</label>
                            <select id="filterData" class="form-select w-auto">
                                <option value="0" selected>Data Aktif</option>
                                <option value="1">Data Terhapus</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATA TABLE - SAMA PERSIS dengan Musyrif --}}
        <div class="card fade-in">
            <div class="card-body p-0">
                <div class="table-container">
                    <table id="santriTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Santri</th>
                                <th>Kontak</th>
                                <th>Halaqah</th>
                                <th width="100">Status</th>
                                <th width="180" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diisi oleh DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER - SAMA PERSIS dengan Musyrif --}}
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <i class="fas fa-heart text-danger me-1"></i> Yayasan Al Abidin
                </div>
                <div class="col-md-6 text-md-end">
                    © 2025 Boarding Management System
                </div>
            </div>
        </div>
    </footer>

    
    <div class="modal fade" id="modalSantri" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formSantri">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-graduate me-2"></i>Form Santri</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       placeholder="Masukkan nama lengkap santri">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="contoh@email.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Nomor Telepon (WhatsApp)</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" 
                                       placeholder="081234567890">
                                <small class="text-muted">Format: 08xx xxxx xxxx</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password <span id="passwordRequired" class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="Minimal 6 karakter">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted" id="passwordHint">Wajib diisi untuk santri baru.</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            
                            
                            <div class="col-md-6 mb-3">
                                <label for="halaqah_id" class="form-label">Halaqah</label>
                                <select class="form-select" id="halaqah_id" name="halaqah_id">
                                    <option value="">-- Pilih Halaqah --</option>
                                    @foreach($halaqah as $h)
                                        <option value="{{ $h->id }}">{{ $h->nama_halaqah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS - SAMA PERSIS dengan Musyrif --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            }
        });

        let showDeleted = 0;
        let table;

        function loadTable() {
            if (table) table.destroy();
            
            table = $('#santriTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('santri.index') }}",
                    data: function(d) { 
                        d.trashed = showDeleted; 
                    }
                },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex',
                        orderable: false, 
                        searchable: false,
                        className: 'text-center'
                    },
                    { 
                        data: 'name', 
                        name: 'name',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">
                                        ${data.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <strong>${data}</strong>
                                        
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            const email = row.email || '-';
                            const phone = row.telephone || '-';
                            
                            return `
                                <div>
                                    <div class="mb-1">
                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                        <a href="mailto:${email}" class="text-decoration-none">${email}</a>
                                    </div>
                                    <div>
                                        <i class="fab fa-whatsapp me-2 text-muted"></i>
                                        <a href="tel:${phone}" class="text-decoration-none">${phone}</a>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'halaqah', 
                        name: 'halaqah',
                        render: function(data) {
                            if (!data) return '<span class="text-muted fst-italic">Belum ada halaqah</span>';
        return `<span class="badge bg-warning text-dark fw-bold px-3 py-2">
                    <i class="fas fa-users me-2"></i>${data}
                </span>`;
                        }
                    },
                    { 
                        data: 'deleted_at', 
                        name: 'deleted_at',
                        render: function(data) {
                            if (showDeleted == 1) {
                                return `<span class="badge badge-danger">Terhapus</span>`;
                            }
                            return `<span class="badge badge-success">Aktif</span>`;
                        },
                        className: 'text-center'
                    },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            if (showDeleted == 1) {
                                return `
                                    <div class="aksi-buttons">
                                        <button class="btn btn-success btn-sm restore" data-id="${row.id}" 
                                                title="Restore" data-bs-toggle="tooltip">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm force-delete" data-id="${row.id}" 
                                                title="Hapus Permanen" data-bs-toggle="tooltip">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                `;
                            }
                            return `
                                <div class="aksi-buttons">
                                    <button class="btn btn-outline-primary btn-sm edit" data-id="${row.id}" 
                                            title="Edit" data-bs-toggle="tooltip">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm delete" data-id="${row.id}" 
                                            title="Hapus" data-bs-toggle="tooltip">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        },
                        className: 'text-center'
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                drawCallback: function(settings) {
                    // Initialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });
        }

        // Inisialisasi
        $(document).ready(function() {
            loadTable();
            
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Filter data
            $('#filterData').change(function() {
                showDeleted = $(this).val();
                loadTable();
            });
            
            // Tambah data
            $('#btnAdd').click(function() {
                $('#formSantri')[0].reset();
                $('#id').val('');
                $('#passwordRequired').show();
                $('#passwordHint').text('Wajib diisi untuk santri baru.');
                $('#password').prop('required', true);
                $('.form-control').removeClass('is-invalid');
                $('#modalSantri').modal('show');
            });
            
            // Submit form
            $('#formSantri').submit(function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Menyimpan...');
                
                // Validasi client-side untuk password saat tambah baru
                if (!$('#id').val() && !$('#password').val()) {
                    submitBtn.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Password harus diisi untuk santri baru.',
                        confirmButtonColor: '#0d6efd'
                    });
                    return false;
                }
                
                const formData = $(this).serialize();
                
                $.post("{{ route('santri.store') }}", formData)
                    .done(function(res) {
                        $('#modalSantri').modal('hide');
                        table.ajax.reload(null, false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .fail(function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            let errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMsg,
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#0d6efd'
                            });
                        }
                    })
                    .always(function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    });
            });
            
            // Edit data
            $(document).on('click', '.edit', function() {
                const id = $(this).data('id');
                
                $.get('/santri/edit/' + id)
                    .done(function(res) {
                        $('#id').val(res.id);
                        $('#name').val(res.name);
                        $('#email').val(res.email);
                        $('#telephone').val(res.telephone || '');
                        $('#kelas').val(res.kelas || '');
                        $('#halaqah_id').val(res.halaqah_id);
                        $('#password').val('');
                        $('#passwordRequired').hide();
                        $('#passwordHint').text('Kosongkan jika tidak ingin mengubah password.');
                        $('#password').prop('required', false);
                        $('.form-control').removeClass('is-invalid');
                        $('#modalSantri').modal('show');
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data santri',
                            confirmButtonColor: '#0d6efd'
                        });
                    });
            });
            
            // Hapus data (soft delete)
            $(document).on('click', '.delete', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data akan dipindahkan ke sampah dan dapat direstore.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/santri/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data',
                                    confirmButtonColor: '#0d6efd'
                                });
                            }
                        });
                    }
                });
            });
            
            // Restore data
            $(document).on('click', '.restore', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Restore Data?',
                    text: 'Data akan dikembalikan ke daftar aktif.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Restore!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/santri/restore/' + id)
                            .done(function(res) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            })
                            .fail(function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat merestore data',
                                    confirmButtonColor: '#0d6efd'
                                });
                            });
                    }
                });
            });
            
            // Hapus permanen
            $(document).on('click', '.force-delete', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Hapus Permanen?',
                    text: 'Data akan dihapus secara permanen dan tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/santri/force/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus permanen',
                                    confirmButtonColor: '#0d6efd'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>