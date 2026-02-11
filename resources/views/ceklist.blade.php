@extends('layouts.app')

@section('title', 'Ceklist Kegiatan Harian')

@push('datatable-css')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-check-square me-2"></i>Ceklist Kegiatan Harian Santri</h4>
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                <small class="text-muted">Anda hanya dapat mengisi ceklist untuk halaqah yang Anda pegang</small>
            @endif
        </div>
        <div class="card-body p-4">
            <div class="row gy-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Pilih Halaqah</label>
                    <select id="halaqahFilter" class="form-select">
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
                    
                    @if(count($halaqah) == 0)
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            @if(auth()->user()->hasRole('musyrif'))
                                Anda belum memiliki halaqah yang ditugaskan. Silahkan hubungi admin.
                            @else
                                Belum ada halaqah yang tersedia.
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Pilih Kegiatan Utama</label>
                    <select id="kegiatanUtama" class="form-select">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach ($kegiatan as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button id="filterBtn" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO ROLE --}}
    @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Perhatian:</strong> Sebagai musyrif, Anda hanya dapat mengisi ceklist untuk halaqah yang Anda pegang.
            Jika ada kesalahan, silahkan hubungi admin.
        </div>
    @endif

    {{-- FORM CEKLIST --}}
    <form id="ceklistForm">
        @csrf
        <input type="hidden" name="halaqah_id" id="halaqah_id">
        <input type="hidden" name="kegiatan_root_id" id="kegiatan_root_id">
        <input type="hidden" name="tanggal" id="tanggalForm" value="{{ date('Y-m-d') }}">

        <div class="card fade-in" id="ceklistContainer" style="display: none;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Daftar Checklist</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-striped table-bordered align-middle" id="ceklistTable" width="100%">
                        <thead class="table-primary text-center">
                            <tr id="tableHead">
                                <th>No</th>
                                <th>Nama Santri</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            {{-- FOOTER ACTION --}}
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i>Simpan Checklist
                </button>
            </div>
        </div>
    </form>
    
    {{-- EMPTY STATE --}}
    <div class="text-center mt-5" id="emptyState">
        <div class="empty-state">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Data Belum Dipilih</h4>
            <p class="text-muted">Pilih halaqah dan kegiatan utama untuk menampilkan data checklist.</p>
        </div>
    </div>
@endsection

@push('datatable-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('styles')
<style>
    .checkbox-cell {
        text-align: center;
        vertical-align: middle;
    }
    
    .checkbox-cell .form-check-input {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }
    
    .checkbox-cell .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .empty-state {
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Styling untuk tabel checklist */
    #ceklistTable th.checkbox-cell {
        min-width: 120px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        let table;
        const isMusyrif = {{ auth()->user()->hasRole('musyrif') ? 'true' : 'false' }};
        const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
        const userId = {{ auth()->user()->id }};

        // Jika musyrif dan hanya punya 1 halaqah, otomatis pilih
        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
            $('#halaqahFilter').trigger('change');
        @endif

        function loadTable() {
            let halaqah_id = $('#halaqahFilter').val();
            let kegiatan_id = $('#kegiatanUtama').val();
            let tanggal = $('#tanggal').val();

            if (!halaqah_id || !kegiatan_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih halaqah dan kegiatan utama terlebih dahulu.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            $('#halaqah_id').val(halaqah_id);
            $('#kegiatan_root_id').val(kegiatan_id);
            $('#tanggalForm').val(tanggal);

            if (table) table.destroy();

            // Ambil sub-kegiatan
            $.ajax({
                url: "{{ url('ceklist/sub-kegiatan') }}",
                type: 'GET',
                data: { kegiatan_id: kegiatan_id },
                success: function (response) {
                    if (response.success) {
                        const subs = response.data;
                        let headHtml = '<th>No</th><th>Nama Santri</th>';
                        subs.forEach(sub => {
                            headHtml += `<th class="checkbox-cell">${sub.nama_kegiatan.toUpperCase()}</th>`;
                        });
                        $('#tableHead').html(headHtml);

                        // Tampilkan container
                        $('#ceklistContainer').show();
                        $('#emptyState').hide();

                        table = $('#ceklistTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "{{ route('ceklist.dataNested') }}",
                                data: { 
                                    halaqah_id: halaqah_id, 
                                    kegiatan_id: kegiatan_id, 
                                    tanggal: tanggal 
                                }
                            },
                            columns: [
                                { 
                                    data: 'DT_RowIndex', 
                                    orderable: false, 
                                    searchable: false, 
                                    className: 'text-center',
                                    width: '50px'
                                },
                                { 
                                    data: 'nama_user', 
                                    name: 'nama_user',
                                    render: function(data, type, row) {
                                        return `<strong>${data}</strong>`;
                                    }
                                },
                                ...subs.map(sub => ({
                                    data: 'keg_' + sub.id,
                                    orderable: false,
                                    searchable: false,
                                    className: 'checkbox-cell',
                                    render: function(data, type, row) {
                                        const checked = data ? 'checked' : '';
                                        return `
                                            <input type="checkbox" class="form-check-input ceklist-checkbox"
                                                data-user="${row.user_id || row.id}"
                                                data-kegiatan="${sub.id}" 
                                                ${checked}
                                                name="status[${row.user_id || row.id}][${sub.id}]">
                                        `;
                                    }
                                })),
                            ],
                            language: {
                                search: "Cari:",
                                lengthMenu: "Tampilkan _MENU_ data",
                                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ santri",
                                infoEmpty: "Tidak ada data santri",
                                zeroRecords: "Tidak ada santri yang cocok",
                                paginate: {
                                    first: "Pertama",
                                    last: "Terakhir",
                                    next: "→",
                                    previous: "←"
                                }
                            },
                            drawCallback: function () {
                                // Attach event listener untuk real-time update
                                $('.ceklist-checkbox').on('change', function() {
                                    const checkbox = $(this);
                                    const user_id = checkbox.data('user');
                                    const kegiatan_id = checkbox.data('kegiatan');
                                    const isChecked = checkbox.is(':checked');
                                    const tanggal = $('#tanggal').val();

                                    // Real-time update via AJAX
                                    $.ajax({
                                        url: "{{ route('ceklist.updateStatus') }}",
                                        type: 'POST',
                                        data: {
                                            user_id: user_id,
                                            kegiatan_id: kegiatan_id,
                                            tanggal: tanggal,
                                            status: isChecked
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                console.log('Status updated');
                                            }
                                        },
                                        error: function(xhr) {
                                            checkbox.prop('checked', !isChecked);
                                            if (xhr.status === 403) {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Akses Ditolak',
                                                    text: 'Anda tidak berhak mengupdate checklist ini'
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'Terjadi kesalahan saat mengupdate status'
                                                });
                                            }
                                        }
                                    });
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal memuat sub-kegiatan',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data sub-kegiatan',
                        confirmButtonColor: '#0d6efd'
                    });
                }
            });
        }

        $('#filterBtn').on('click', loadTable);

        // Submit form untuk save massal
        $('#ceklistForm').on('submit', function (e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Menyimpan...');
            
            $.ajax({
                url: "{{ route('ceklist.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            // Refresh table
                            if (table) table.ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                    
                    if (xhr.status === 403) {
                        errorMessage = 'Anda tidak memiliki izin untuk menyimpan data checklist.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage,
                        confirmButtonColor: '#0d6efd'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Function untuk loading spinner
        function createLoadingSpinner() {
            return '<span class="loading-spinner me-2"></span>';
        }
        
        // Event untuk tanggal
        $('#tanggal').on('change', function() {
            if ($('#halaqahFilter').val() && $('#kegiatanUtama').val()) {
                loadTable();
            }
        });

        // Auto-load jika semua data sudah terisi
        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
        $(document).ready(function() {
            setTimeout(function() {
                if ($('#halaqahFilter').val() && $('#kegiatanUtama').val()) {
                    loadTable();
                }
            }, 500);
        });
        @endif
    });
</script>
@endpush