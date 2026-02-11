@extends('layouts.app')

@section('title', 'Data Kegiatan')

@push('datatable-css')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Data Kegiatan</h4>
                <button class="btn btn-primary" id="btnTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Kegiatan
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row gy-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tampilkan:</label>
                    <select id="filterStatus" class="form-select">
                        <option value="active" selected>Data Aktif</option>
                        <option value="deleted">Data Terhapus</option>
                        <option value="all">Semua Data</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card fade-in">
        <div class="card-body p-0">
            <div class="table-container">
                <table id="tableKegiatan" class="table table-striped table-bordered align-middle" width="100%">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:5%;" class="text-center">No</th>
                            <th>Nama Kegiatan</th>
                            <th style="width:20%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

{{-- MODAL FORM --}}
<div class="modal fade" id="modalKegiatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formKegiatan">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Form Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label fw-semibold">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label fw-semibold">Parent (opsional)</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">-- Pilih Kegiatan Utama --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('datatable-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('scripts')
<script>
    $(function () {
        let table;

        // Inisialisasi DataTable
        function initTable() {
            table = $('#tableKegiatan').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('kegiatan.index') }}",
                    dataSrc: function(json) {
                        let raw = (json.data) ? json.data : json;
                        return buildTree(raw);
                    }
                },
                columns: [
                    { 
                        data: 'no', 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center'
                    },
                    { 
                        data: 'nama_kegiatan',
                        className: 'tree-column'
                    },
                    { 
                        data: 'action', 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                createdRow: function(row, data) {
                    if (data.parent_id) $(row).addClass('child-row parent-' + data.parent_id);
                    $(row).attr('data-id', data.id);
                    $(row).attr('data-parent', data.parent_id || '');
                },
                language: {
                    emptyTable: "Tidak ada data kegiatan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "→",
                        previous: "←"
                    }
                }
            });
        }

        /* ========== TREE BUILD FUNCTION ========== */
        function buildTree(data, parentId = null, level = 0, result = [], no = { count: 1 }) {
            data.filter(item => item.parent_id === parentId).forEach(item => {
                let hasChild = data.some(c => c.parent_id === item.id);

                let arrow = hasChild
                    ? `<span class="tree-arrow" data-id="${item.id}"></span>`
                    : `<span style="display:inline-block;width:12px;"></span>`;

                result.push({
                    no: no.count++,
                    id: item.id,
                    parent_id: item.parent_id,
                    nama_kegiatan: `
                        <div class="tree-wrapper">
                            <div class="tree-item tree-level-${level} tree-indent-${level}">
                                ${arrow}
                                <span>${item.nama_kegiatan}</span>
                            </div>
                        </div>
                    `,
                    action: item.action
                });

                buildTree(data, item.id, level + 1, result, no);
            });

            return result;
        }

        /* ========== TOGGLE CHILDREN ========== */
        $(document).on('click', '.tree-arrow', function() {
            let id = $(this).data('id');
            $(this).toggleClass('open');
            let show = $(this).hasClass('open');
            toggleChildren(id, show);
        });

        function toggleChildren(parentId, show) {
            let children = $(`.parent-${parentId}`);
            children.each(function() {
                if (show) {
                    $(this).fadeIn(150);
                } else {
                    $(this).fadeOut(150);
                    let id = $(this).data('id');
                    toggleChildren(id, false);
                    $(`.tree-arrow[data-id="${id}"]`).removeClass('open');
                }
            });
        }

        /* FILTER STATUS */
        $('#filterStatus').on('change', function() {
            table.ajax.url("{{ route('kegiatan.index') }}?status=" + $(this).val()).load();
        });

        /* LOAD PARENT OPTIONS */
        function loadParentOptions() {
            $.get("{{ route('kegiatan.index') }}", function(data) {
                let parents = (data.data) ? data.data : data;
                let options = '<option value="">-- Pilih Kegiatan Utama --</option>';
                parents.forEach(p => {
                    if (!p.parent_id) options += `<option value="${p.id}">${p.nama_kegiatan}</option>`;
                });
                $('#parent_id').html(options);
            });
        }

        /* TAMBAH */
        $('#btnTambah').on('click', function() {
            $('#modalTitle').text('Tambah Kegiatan');
            $('#formKegiatan')[0].reset();
            $('#id').val('');
            loadParentOptions();
            $('#modalKegiatan').modal('show');
        });

        /* SUBMIT FORM */
        $('#formKegiatan').on('submit', function(e) {
            e.preventDefault();
            const id = $('#id').val();
            const url = id ? `/kegiatan/${id}` : `{{ route('kegiatan.store') }}`;
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize() + '&_method=' + method,
                success: function(res) {
                    $('#modalKegiatan').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: (res.message || 'Data berhasil disimpan.'),
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: msg
                    });
                }
            });
        });

        /* EDIT */
        $(document).on('click', '.btnEdit', function() {
            const id = $(this).data('id');
            $.get(`/kegiatan/${id}`, function(data) {
                $('#modalTitle').text('Edit Kegiatan');
                $('#id').val(data.id);
                $('#nama_kegiatan').val(data.nama_kegiatan);
                $('#parent_id').val(data.parent_id);
                $('#modalKegiatan').modal('show');
            });
        });

        /* DELETE / RESTORE */
        $(document).on('click', '.btnDelete, .btnRestore', function() {
            const id = $(this).data('id');
            const isDelete = $(this).hasClass('btnDelete');
            const url = isDelete ? `/kegiatan/${id}` : `/kegiatan/restore/${id}`;
            const method = isDelete ? 'DELETE' : 'POST';

            Swal.fire({
                title: isDelete ? 'Yakin hapus data ini?' : 'Pulihkan data ini?',
                text: isDelete ? 'Data akan dipindahkan ke arsip!' : 'Data akan dikembalikan',
                icon: isDelete ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjut!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { _method: method, _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: (res.message || 'Operasi berhasil.'),
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan.'
                            });
                        }
                    });
                }
            });
        });

        // Inisialisasi tabel saat halaman dimuat
        initTable();
    });
</script>

<style>
    .tree-wrapper {
        padding-left: 0px;
        display: flex;
        align-items: center;
        position: relative;
    }

    /* Indent aman dan tidak menabrak kolom lain */
    .tree-indent-0 { padding-left: 0px; }
    .tree-indent-1 { padding-left: 20px; }
    .tree-indent-2 { padding-left: 40px; }
    .tree-indent-3 { padding-left: 60px; }
    .tree-indent-4 { padding-left: 80px; }

    /* Arrow */
    .tree-arrow {
        cursor: pointer;
        font-size: 14px;
        width: 12px;
        display: inline-block;
        margin-right: 6px;
        color: #0d6efd;
    }
    .tree-arrow::before {
        content: "▶";
        display: inline-block;
        transition: transform 0.2s ease;
    }
    .tree-arrow.open::before {
        transform: rotate(90deg);
    }

    /* Tree lines */
    .tree-item {
        position: relative;
        padding: 4px 0;
    }

    /* vertical line */
    .tree-item::before {
        content: "";
        position: absolute;
        left: 0px;
        top: -10px;
        height: 100%;
        border-left: 1px solid #c8c8c8;
    }

    /* horizontal connector */
    .tree-item::after {
        content: "";
        position: absolute;
        left: 0px;
        top: 12px;
        width: 12px;
        border-bottom: 1px solid #c8c8c8;
    }

    /* Level 0 (root) tidak perlu garis */
    .tree-level-0::before,
    .tree-level-0::after {
        display: none;
    }

    /* Table container responsive */
    .table-container {
        overflow-x: auto;
    }

    /* Styling untuk tombol aksi */
    .btn-action {
        padding: 4px 8px;
        font-size: 0.875rem;
        margin: 0 2px;
    }

    /* Styling untuk kolom tree */
    .tree-column {
        min-width: 300px;
    }
</style>
@endpush