@extends('layouts.app')

@section('title', 'Kesehatan Harian Santri')

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-heartbeat text-danger me-2"></i>Kesehatan Harian Santri</h4>
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
                <small class="text-muted">Anda hanya dapat mengisi data kesehatan untuk halaqah yang Anda pegang</small>
            @endif
        </div>
        <div class="card-body p-4">
            {{-- FILTER FORM --}}
            <form method="GET" action="{{ route('kesehatan.index') }}" class="row gy-3" id="filterForm">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Pilih Halaqah</label>
                    <select name="halaqah_id" class="form-select" id="halaqahSelect"
                        {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 ? 'disabled' : '' }}>
                        <option value="">-- Pilih Halaqah --</option>
                        @foreach ($halaqah as $h)
                            <option value="{{ $h->id }}" 
                                {{ request('halaqah_id') == $h->id ? 'selected' : '' }}
                                {{ auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1 && !request('halaqah_id') ? 'selected' : '' }}>
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

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" 
                           value="{{ $tanggal }}" id="tanggalInput">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100 py-2" id="filterBtn">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('kesehatan.index') }}" class="btn btn-outline-secondary w-100 py-2">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </a>
                </div>
            </form>
            
            {{-- Warning jika musyrif tanpa halaqah --}}
            @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 0)
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Belum ada halaqah yang ditugaskan!</strong> Silahkan hubungi administrator untuk mendapatkan akses ke halaqah.
            </div>
            @endif
        </div>
    </div>

    {{-- ROLE INFO --}}
    @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Perhatian:</strong> Sebagai musyrif, Anda hanya dapat mengisi data kesehatan untuk halaqah yang Anda pegang.
        Jika ada kesalahan, silahkan hubungi admin.
    </div>
    @endif

    {{-- DATA KESEHATAN --}}
    @if($santri->isNotEmpty())
    <form method="POST" action="{{ route('kesehatan.store') }}" id="kesehatanForm">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="halaqah_id" value="{{ $halaqah_id }}">

        {{-- INFO CARD --}}
        <div class="alert alert-info mb-4 fade-in">
            <i class="fas fa-info-circle me-2"></i> 
            Menampilkan data kesehatan untuk tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>
            @if($halaqah_id)
                - Halaqah: <strong>{{ $halaqah->where('id', $halaqah_id)->first()->nama_halaqah ?? '' }}</strong>
            @endif
            <div class="mt-1">
                <small>
                    @if(auth()->user()->hasRole('admin'))
                        <i class="fas fa-user-shield me-1"></i> Anda login sebagai Administrator
                    @elseif(auth()->user()->hasRole('musyrif'))
                        <i class="fas fa-user-tie me-1"></i> Anda login sebagai Musyrif
                    @endif
                </small>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card fade-in">
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-striped table-bordered align-middle" width="100%">
                        <thead class="table-primary text-center">
                            <tr>
                                <th style="width:5%">No</th>
                                <th>Nama Santri</th>
                                <th style="width:20%">Status Kesehatan</th>
                                <th style="width:30%">Keterangan</th>
                                <th style="width:15%">Terakhir Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($santri as $s)
                                @php
                                    $existingData = $kesehatanData[$s->id] ?? null;
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $s->name }}</strong>
                                        @if($existingData)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-hashtag me-1"></i>{{ $existingData->kode ?? '-' }}
                                            </small>
                                        @endif
                                    </td>

                                    {{-- STATUS KESEHATAN --}}
                                    <td>
                                        <select name="kesehatan[{{ $s->id }}][status]" 
                                                class="form-select form-select-sm status-select" 
                                                data-santri="{{ $s->id }}">
                                            <option value="">-- Pilih Status --</option>
                                            <option value="sehat" {{ $existingData?->status == 'sehat' ? 'selected' : '' }}>Sehat</option>
                                            <option value="sakit" {{ $existingData?->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="izin" {{ $existingData?->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="rumah sakit" {{ $existingData?->status == 'rumah sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                                            <option value="rawat jalan" {{ $existingData?->status == 'rawat jalan' ? 'selected' : '' }}>Rawat Jalan</option>
                                        </select>
                                        
                                        @if($existingData?->status)
                                            <div class="mt-1" id="badge-{{ $s->id }}">
                                                @php
                                                    $badgeColors = [
                                                        'sehat' => 'success',
                                                        'sakit' => 'danger',
                                                        'izin' => 'warning',
                                                        'rumah sakit' => 'dark',
                                                        'rawat jalan' => 'info'
                                                    ];
                                                    $color = $badgeColors[$existingData->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} badge-status">
                                                    {{ ucwords($existingData->status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- KETERANGAN --}}
                                    <td>
                                        <input type="text" 
                                            class="form-control form-control-sm keterangan-input"
                                            name="kesehatan[{{ $s->id }}][keterangan]"
                                            value="{{ old('kesehatan.' . $s->id . '.keterangan', $existingData?->keterangan) }}"
                                            placeholder="Misal: Flu, Pusing, dll."
                                            data-santri="{{ $s->id }}">
                                    </td>

                                    <td class="text-center">
                                        @if($existingData?->updated_at)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($existingData->updated_at)->translatedFormat('d/m/Y H:i') }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                @if($existingData->updater)
                                                    by {{ $existingData->updater->name }}
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FOOTER ACTION --}}
        <div class="mt-4 text-end">
            <button type="button" class="btn btn-outline-secondary" onclick="selectAllStatus('sehat')">
                <i class="fas fa-check-circle me-2"></i>Set Semua Sehat
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="selectAllStatus('sakit')">
                <i class="fas fa-hospital me-2"></i>Set Semua Sakit
            </button>
            <button type="submit" class="btn btn-primary px-4 py-2" id="saveBtn">
                <i class="fas fa-save me-2"></i>Simpan Data
            </button>
        </div>
    </form>
    @else
        <div class="alert alert-warning fade-in">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @if($halaqah_id)
                Tidak ada santri ditemukan untuk halaqah yang dipilih.
            @else
                Silakan pilih halaqah terlebih dahulu untuk menampilkan data santri.
            @endif
        </div>
    @endif
@endsection

@push('styles')
<style>
    .badge-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .status-select {
        min-width: 120px;
    }
    
    .keterangan-input {
        min-width: 200px;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const isMusyrif = {{ auth()->user()->hasRole('musyrif') ? 'true' : 'false' }};
        const isAdmin = {{ auth()->user()->hasRole('admin') ? 'true' : 'false' }};
        let isAutoFiltering = false;
        
        // Filter button dengan AJAX
        $('#filterBtn').click(function(e) {
            e.preventDefault();
            filterData();
        });
        
        // Fungsi untuk memfilter data
        function filterData() {
            const halaqahId = $('#halaqahSelect').val();
            const tanggal = $('#tanggalInput').val();
            
            if (!halaqahId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih halaqah terlebih dahulu!',
                    confirmButtonColor: '#0d6efd'
                });
                return false;
            }
            
            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih tanggal terlebih dahulu!',
                    confirmButtonColor: '#0d6efd'
                });
                return false;
            }
            
            // Submit form filter
            $('#filterForm').submit();
            return true;
        }
        
        // Auto filter untuk musyrif dengan 1 halaqah
        @if(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin') && count($halaqah) == 1)
        if (!{{ $halaqah_id ? 'true' : 'false' }}) {
            // Set tanggal default jika kosong
            if (!$('#tanggalInput').val()) {
                $('#tanggalInput').val('{{ date("Y-m-d") }}');
            }
            
            // Delay sedikit sebelum auto-filter
            setTimeout(function() {
                if (!$('#halaqahSelect').val()) {
                    $('#halaqahSelect').val('{{ $halaqah->first()->id ?? "" }}');
                }
                
                // Cek apakah sudah ada data, jika belum baru filter
                if ($('#halaqahSelect').val() && $('#tanggalInput').val() && !isAutoFiltering) {
                    isAutoFiltering = true;
                    filterData();
                }
            }, 100);
        }
        @endif
        
        // Set semua status kesehatan
        function selectAllStatus(status) {
            document.querySelectorAll('.status-select').forEach(select => {
                select.value = status;
                // Trigger change event untuk update badge
                const event = new Event('change');
                select.dispatchEvent(event);
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Semua status kesehatan diubah menjadi ${status.charAt(0).toUpperCase() + status.slice(1)}`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Update badge real-time ketika status berubah
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const santriId = this.dataset.santri;
                const badgeContainer = document.getElementById(`badge-${santriId}`);
                
                if (this.value) {
                    const badgeColors = {
                        'sehat': 'success',
                        'sakit': 'danger',
                        'izin': 'warning',
                        'rumah sakit': 'dark',
                        'rawat jalan': 'info'
                    };
                    
                    if (!badgeContainer) {
                        const container = document.createElement('div');
                        container.className = 'mt-1';
                        container.id = `badge-${santriId}`;
                        container.innerHTML = `<span class="badge bg-${badgeColors[this.value] || 'secondary'} badge-status">${this.value.charAt(0).toUpperCase() + this.value.slice(1)}</span>`;
                        this.parentNode.appendChild(container);
                    } else {
                        const badge = badgeContainer.querySelector('.badge');
                        if (badge) {
                            badge.className = `badge bg-${badgeColors[this.value] || 'secondary'} badge-status`;
                            badge.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                        }
                    }
                } else if (badgeContainer) {
                    badgeContainer.remove();
                }
                
                // Auto-focus keterangan jika status sakit/rawat
                if (this.value === 'sakit' || this.value === 'rumah sakit' || this.value === 'rawat jalan') {
                    const row = this.closest('tr');
                    const keteranganInput = row.querySelector('.keterangan-input');
                    if (keteranganInput) {
                        keteranganInput.focus();
                    }
                }
            });
        });
        
        // Form submission dengan AJAX
        $('#kesehatanForm').submit(function(e) {
            e.preventDefault();
            
            const submitBtn = $('#saveBtn');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="loading-spinner me-2"></span>Menyimpan...');
            
            const formData = $(this).serialize();
            
            $.ajax({
                url: "{{ route('kesehatan.store') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data kesehatan berhasil disimpan!',
                            confirmButtonColor: '#0d6efd',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Refresh halaman untuk update data
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                    
                    if (xhr.status === 403) {
                        errorMessage = 'Anda tidak memiliki izin untuk menyimpan data kesehatan.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('\n');
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
        
        // Tanggal change auto filter (hanya jika halaqah sudah dipilih)
        $('#tanggalInput').change(function() {
            if ($('#halaqahSelect').val()) {
                filterData();
            }
        });
        
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                confirmButtonColor: '#0d6efd',
                timer: 2000,
                timerProgressBar: true
            });
        @endif
        
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session("error") }}',
                confirmButtonColor: '#0d6efd'
            });
        @endif
    });
</script>
@endpush