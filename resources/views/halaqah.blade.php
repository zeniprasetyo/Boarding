@extends('layouts.app')

@section('title', 'Data Halaqah')

@push('datatable-css')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        /* Styling tambahan */
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
        /* Badge untuk status */
        .badge-deleted {
            background-color: #dc3545;
            color: white;
            font-size: 0.7em;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
@endpush

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-users me-2"></i>Data Halaqah</h4>
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                <small class="text-muted">Anda hanya dapat melihat dan mengedit halaqah yang Anda pegang</small>
            @endif
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    @if(auth()->user()->hasRole('admin'))
                        <button class="btn btn-primary px-4" id="btnTambah">
                            <i class="fas fa-plus me-2"></i>Tambah Halaqah
                        </button>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <label class="form-label fw-semibold me-3 mb-0">Tampilkan:</label>
                        <select id="filterStatus" class="form-select w-auto">
                            <option value="active" selected>Data Aktif</option>
                            @if(auth()->user()->hasRole('admin'))
                                <option value="deleted">Data Terhapus</option>
                                <option value="all">Semua Data</option>
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
                <table id="tabelHalaqah" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Kode</th>
                            <th>Nama Halaqah</th>
                            <th>Musyrif</th>
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
    {{-- MODAL FORM HALAQAH --}}
    <div class="modal fade" id="modalHalaqah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formHalaqah">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-users me-2"></i>Form Halaqah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode" class="form-label">Kode Halaqah</label>
                                <input type="text" class="form-control bg-light" id="kode" name="kode" 
                                       readonly placeholder="Kode akan digenerate otomatis">
                                <small class="text-muted">Format: H001, H002, dst.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama_halaqah" class="form-label">Nama Halaqah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_halaqah" name="nama_halaqah" required 
                                       placeholder="Masukkan nama halaqah">
                                <div class="invalid-feedback" id="nama_halaqah_error"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="musyrif_id" class="form-label">Musyrif <span class="text-danger">*</span></label>
                                <select class="form-select" id="musyrif_id" name="musyrif_id" required 
                                    {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Musyrif --</option>
                                    @foreach($musyrif as $m)
                                        <option value="{{ $m->id }}" 
                                            {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && auth()->user()->id == $m->id ? 'selected' : '' }}>
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                                    <input type="hidden" name="musyrif_id" value="{{ auth()->user()->id }}">
                                    <small class="text-muted">Anda otomatis menjadi musyrif untuk halaqah ini</small>
                                @endif
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

@push('scripts')
<script>
    $(document).ready(function() {
        let table;
        let showDeleted = false;
        let showAll = false;
        const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
        const isMusyrif = {{ auth()->user()->hasRole('musyrif') ? 'true' : 'false' }};
        const userId = {{ auth()->user()->id }};

        function loadTable(filter = 'active') {
            if (table) table.destroy();
            
            showDeleted = filter === 'deleted';
            showAll = filter === 'all';
            
            table = $('#tabelHalaqah').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url("halaqah/data") }}',
                    data: { filter: filter }
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
                        data: 'kode', 
                        name: 'kode',
                        render: function(data) {
                            return `<span class="badge bg-primary">${data}</span>`;
                        },
                        className: 'text-center'
                    },
                    { 
                        data: 'nama_halaqah', 
                        name: 'nama_halaqah',
                        render: function(data, type, row) {
                            let deletedBadge = '';
                            if (showAll && row.deleted_at) {
                                deletedBadge = '<span class="badge-deleted ms-2">Terhapus</span>';
                            }
                            return `
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle">
                                        ${data.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <strong>${data}</strong>
                                        <div class="small text-muted">Halaqah ${deletedBadge}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { 
                        data: 'musyrif_nama', 
                        name: 'musyrif_nama',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">Belum ditentukan</span>';
                            
                            // Tandai jika musyrif adalah user yang sedang login
                            let badge = '';
                            if (data === '{{ auth()->user()->name }}') {
                                badge = '<span class="badge bg-info ms-2">Anda</span>';
                            }
                            return `<strong>${data}</strong> ${badge}`;
                        }
                    },
                    { 
                        data: 'aksi', 
                        name: 'aksi',
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            let actions = '';
                            
                            if (showDeleted || (showAll && row.deleted_at)) {
                                // Untuk data terhapus
                                if (isAdmin) {
                                    actions = `
                                        <div class="aksi-buttons">
                                            <button class="btn btn-success btn-sm btnRestore" data-id="${row.id}" 
                                                    title="Restore" data-bs-toggle="tooltip">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm btnForceDelete" data-id="${row.id}" 
                                                    title="Hapus Permanen" data-bs-toggle="tooltip">
                                                <i class="fas fa-trash-alt"></i>
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
                                
                                // Cek apakah user bisa edit
                                if (isAdmin || (isMusyrif && row.musyrif_id == userId)) {
                                    editBtn = `
                                        <button class="btn btn-outline-primary btn-sm btnEdit" data-id="${row.id}" 
                                                title="Edit" data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    `;
                                }
                                
                                // Hanya admin yang bisa hapus
                                if (isAdmin) {
                                    deleteBtn = `
                                        <button class="btn btn-outline-danger btn-sm btnDelete" data-id="${row.id}" 
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
        $('#filterStatus').change(function() {
            loadTable($(this).val());
        });
        
        // Validasi nama halaqah unik
        $('#nama_halaqah').on('blur', function() {
            const nama = $(this).val();
            const id = $('#id').val();
            
            if (nama.length > 2) {
                $.post('{{ url("halaqah/check-name") }}', {
                    nama_halaqah: nama,
                    id: id
                }, function(response) {
                    if (response.exists) {
                        $('#nama_halaqah').addClass('is-invalid');
                        $('#nama_halaqah_error').text('Nama halaqah sudah digunakan');
                    } else {
                        $('#nama_halaqah').removeClass('is-invalid');
                        $('#nama_halaqah_error').text('');
                    }
                });
            }
        });
        
        // Tambah data (hanya untuk admin)
        $('#btnTambah').click(function() {
            $('#formHalaqah')[0].reset();
            $('#id').val('');
            $('#kode').val(''); // Kode akan digenerate otomatis
            $('.form-control').removeClass('is-invalid');
            
            // Set musyrif untuk musyrif non-admin
            if (isMusyrif && !isAdmin) {
                $('#musyrif_id').val(userId);
            }
            
            $('#modalHalaqah').modal('show');
        });
        
        // Submit form
        $('#formHalaqah').submit(function(e) {
            e.preventDefault();
            
            // Validasi manual
            if ($('#nama_halaqah').hasClass('is-invalid')) {
                showError('Nama halaqah sudah digunakan');
                return;
            }
            
            const submitBtn = $('#btnSimpan');
            const originalText = submitBtn.html();
            showLoading(submitBtn, 'Menyimpan...');
            
            const formData = $(this).serialize();
            const id = $('#id').val();
            const url = id ? '/halaqah/' + id : '/halaqah';
            const method = id ? 'PUT' : 'POST';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData + '&_method=' + method,
                success: function(res) {
                    $('#modalHalaqah').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccess(res.message || 'Data berhasil disimpan.');
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                            $(`#${key}`).addClass('is-invalid');
                            $(`#${key}_error`).text(value[0]);
                        });
                        showError(errorMsg);
                    } else {
                        let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        if (xhr.status === 403) {
                            showError(errorMessage);
                        } else {
                            showError(errorMessage);
                        }
                    }
                },
                complete: function() {
                    hideLoading(submitBtn, originalText);
                }
            });
        });
        
        // Edit data
        $(document).on('click', '.btnEdit', function() {
            const id = $(this).data('id');
            
            $.get('/halaqah/' + id)
                .done(function(data) {
                    $('#id').val(data.id);
                    $('#kode').val(data.kode);
                    $('#nama_halaqah').val(data.nama_halaqah);
                    $('#musyrif_id').val(data.musyrif_id);
                    
                    // Nonaktifkan dropdown musyrif untuk musyrif non-admin
                    if (isMusyrif && !isAdmin) {
                        $('#musyrif_id').attr('disabled', true);
                    }
                    
                    $('#modalHalaqah').modal('show');
                })
                .fail(function(xhr) {
                    if (xhr.status === 403) {
                        showError('Anda tidak berhak mengakses data ini');
                    } else {
                        showError('Gagal memuat data halaqah');
                    }
                });
        });
        
        // Hapus data (soft delete) - hanya admin
        $(document).on('click', '.btnDelete', function() {
            const id = $(this).data('id');
            
            confirmAction('Hapus Data?', 'Data akan dipindahkan ke sampah dan dapat direstore.', 'Ya, Hapus!')
                .then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/halaqah/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                table.ajax.reload(null, false);
                                showSuccess(res.message || 'Data berhasil dihapus.', 1500);
                            },
                            error: function(xhr) {
                                if (xhr.status === 403) {
                                    showError('Anda tidak memiliki izin untuk menghapus data');
                                } else {
                                    showError('Terjadi kesalahan saat menghapus data');
                                }
                            }
                        });
                    }
                });
        });
        
        // Restore data - hanya admin
        $(document).on('click', '.btnRestore', function() {
            const id = $(this).data('id');
            
            confirmAction('Restore Data?', 'Data akan dikembalikan ke daftar aktif.', 'Ya, Restore!', 'Batal', '#198754')
                .then((result) => {
                    if (result.isConfirmed) {
                        $.post('/halaqah/restore/' + id)
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
        
        // Hapus permanen - hanya admin
        $(document).on('click', '.btnForceDelete', function() {
            const id = $(this).data('id');
            
            confirmAction('Hapus Permanen?', 'Data akan dihapus selamanya dan tidak dapat dikembalikan!', 'Ya, Hapus!', 'Batal', '#dc3545')
                .then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/halaqah/force/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                table.ajax.reload(null, false);
                                showSuccess(res.message || 'Data berhasil dihapus permanen.', 1500);
                            },
                            error: function(xhr) {
                                if (xhr.status === 403) {
                                    showError('Anda tidak memiliki izin untuk menghapus permanen');
                                } else {
                                    showError('Terjadi kesalahan saat menghapus data');
                                }
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