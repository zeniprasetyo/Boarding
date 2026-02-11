@extends('layouts.app')

@section('title', 'Data Musyrif')

@push('datatable-css')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Data Musyrif</h4>
            @if(auth()->user()->hasRole('admin'))
                <div class="mt-2">
                    <small class="text-muted">Hanya admin yang dapat mengelola data musyrif</small>
                </div>
            @endif
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    @if(auth()->user()->hasRole('admin'))
                        <button class="btn btn-primary px-4" id="btnAdd">
                            <i class="fas fa-plus me-2"></i>Tambah Musyrif
                        </button>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <label class="form-label fw-semibold me-3 mb-0">Tampilkan:</label>
                        <select id="filterData" class="form-select w-auto">
                            <option value="active" selected>Data Aktif</option>
                            @if(auth()->user()->hasRole('admin'))
                                <option value="deleted">Data Terhapus</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card fade-in">
        <div class="card-body p-0">
            <div class="table-container">
                <table id="musyrifTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Musyrif</th>
                            <th>Kontak</th>
                            <th>Halaqah</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data akan diisi oleh DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- MODAL FORM MUSYRIF --}}
    <div class="modal fade" id="modalMusyrif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formMusyrif">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-tie me-2"></i>Form Musyrif</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       placeholder="Masukkan nama lengkap musyrif">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="contoh@email.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Nomor Telepon</label>
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
                                <small class="text-muted" id="passwordHint">Wajib diisi untuk musyrif baru.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('datatable-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: bold;
    }
    .aksi-buttons .btn {
        margin: 0 2px;
    }
    .table-container {
        overflow-x: auto;
    }
    .badge-deleted {
        background-color: #dc3545;
        color: white;
        font-size: 0.7em;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let table;
        let showDeleted = false;
        const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};

        function loadTable(filter = 'active') {
            if (table) table.destroy();
            
            showDeleted = filter === 'deleted';
            
            table = $('#musyrifTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('musyrif.index') }}",
                    data: { 
                        filter: filter 
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
                                        <div class="small text-muted">Musyrif</div>
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
                                        <i class="fas fa-envelope me-1 text-muted"></i>
                                        <small>${email}</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-phone me-1 text-muted"></i>
                                        <small>${phone}</small>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'halaqah',
                        name: 'halaqah',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    { 
                        data: 'deleted_at', 
                        name: 'deleted_at',
                        render: function(data) {
                            if (showDeleted) {
                                return `<span class="badge bg-danger">Terhapus</span>`;
                            }
                            return `<span class="badge bg-success">Aktif</span>`;
                        },
                        className: 'text-center'
                    },
                    { 
                        data: 'action', 
                        name: 'action',
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = '';
                            
                            if (showDeleted) {
                                // Untuk data terhapus
                                if (isAdmin) {
                                    actions = `
                                        <div class="aksi-buttons">
                                            <button class="btn btn-success btn-sm restore" data-id="${row.id}" 
                                                    title="Restore" data-bs-toggle="tooltip">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    `;
                                } else {
                                    actions = '<span class="text-muted">-</span>';
                                }
                            } else {
                                // Untuk data aktif
                                let editBtn = '';
                                let deleteBtn = '';
                                
                                // Hanya admin yang bisa edit dan hapus
                                if (isAdmin) {
                                    editBtn = `
                                        <button class="btn btn-outline-primary btn-sm edit" data-id="${row.id}" 
                                                title="Edit" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    `;
                                    
                                    deleteBtn = `
                                        <button class="btn btn-outline-danger btn-sm delete" data-id="${row.id}" 
                                                title="Hapus" data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    `;
                                }
                                
                                actions = `
                                    <div class="aksi-buttons">
                                        ${editBtn}
                                        ${deleteBtn}
                                    </div>
                                `;
                            }
                            
                            return actions;
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

        // Inisialisasi tabel
        loadTable();
        
        // Filter data
        $('#filterData').change(function() {
            loadTable($(this).val());
        });
        
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
        
        // Tambah data (hanya untuk admin)
        $('#btnAdd').click(function() {
            $('#formMusyrif')[0].reset();
            $('#id').val('');
            $('#passwordRequired').show();
            $('#passwordHint').text('Wajib diisi untuk musyrif baru.');
            $('#password').prop('required', true);
            $('.form-control').removeClass('is-invalid');
            $('#modalMusyrif').modal('show');
        });
        
        // Submit form
        $('#formMusyrif').submit(function(e) {
            e.preventDefault();
            
            const submitBtn = $('#btnSimpan');
            const originalText = submitBtn.html();
            showLoading(submitBtn, 'Menyimpan...');
            
            const formData = $(this).serialize();
            
            $.post("{{ route('musyrif.store') }}", formData)
                .done(function(res) {
                    $('#modalMusyrif').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccess(res.message || 'Data berhasil disimpan.');
                })
                .fail(function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                            $(`#${key}`).addClass('is-invalid');
                        });
                        showError(errorMsg);
                    } else {
                        let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        if (xhr.status === 403) {
                            showError('Anda tidak memiliki izin untuk melakukan operasi ini');
                        } else {
                            showError(errorMessage);
                        }
                    }
                })
                .always(function() {
                    hideLoading(submitBtn, originalText);
                });
        });
        
        // Edit data
        $(document).on('click', '.edit', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: '/musyrif/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        $('#id').val(res.data.id);
                        $('#name').val(res.data.name);
                        $('#email').val(res.data.email);
                        $('#telephone').val(res.data.telephone || '');
                        $('#password').val('');
                        $('#passwordRequired').hide();
                        $('#passwordHint').text('Kosongkan jika tidak ingin mengubah password.');
                        $('#password').prop('required', false);
                        $('#modalMusyrif').modal('show');
                    } else {
                        showError(res.message || 'Gagal memuat data');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        showError('Anda tidak berhak mengakses data ini');
                    } else {
                        showError('Gagal memuat data musyrif');
                    }
                }
            });
        });
        
        // Hapus data
        $(document).on('click', '.delete', function() {
            const id = $(this).data('id');
            
            confirmAction('Hapus Data?', 'Data akan dipindahkan ke sampah dan dapat direstore.', 'Ya, Hapus!')
                .then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/musyrif/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                table.ajax.reload(null, false);
                                showSuccess(res.message || 'Data berhasil dihapus.', 1500);
                            },
                            error: function(xhr) {
                                if (xhr.status === 403) {
                                    showError('Anda tidak memiliki izin untuk menghapus data');
                                } else if (xhr.status === 400) {
                                    showError(xhr.responseJSON?.message || 'Data tidak dapat dihapus karena masih terkait dengan halaqah');
                                } else {
                                    showError('Terjadi kesalahan saat menghapus data');
                                }
                            }
                        });
                    }
                });
        });
        
        // Restore data
        $(document).on('click', '.restore', function() {
            const id = $(this).data('id');
            
            confirmAction('Restore Data?', 'Data akan dikembalikan ke daftar aktif.', 'Ya, Restore!', 'Batal', '#198754')
                .then((result) => {
                    if (result.isConfirmed) {
                        $.post('/musyrif/restore/' + id)
                            .done(function(res) {
                                table.ajax.reload(null, false);
                                showSuccess(res.message || 'Data berhasil direstore.', 1500);
                            })
                            .fail(function(xhr) {
                                if (xhr.status === 403) {
                                    showError('Anda tidak memiliki izin untuk merestore data');
                                } else {
                                    showError('Terjadi kesalahan saat merestore data');
                                }
                            });
                    }
                });
        });

        // Fungsi helper untuk sweetalert
        function confirmAction(title, text, confirmText, cancelText = 'Batal', confirmColor = '#d33') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                reverseButtons: true
            });
        }

        function showLoading(btn, text) {
            btn.html(`<i class="fas fa-spinner fa-spin me-2"></i>${text}`);
            btn.prop('disabled', true);
        }

        function hideLoading(btn, originalText) {
            btn.html(originalText);
            btn.prop('disabled', false);
        }

        function showSuccess(message, timer = 3000) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                timer: timer,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }
    });
</script>
@endpush