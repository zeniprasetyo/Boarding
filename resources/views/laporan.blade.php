@extends('layouts.app')

@section('title', 'Laporan Kegiatan Santri')

@push('datatable-css')
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@section('content')
    {{-- HEADER CARD --}}
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Laporan Kegiatan Santri</h4>
        </div>
        <div class="card-body p-4">
            {{-- FILTER FORM --}}
            <form action="{{ route('laporan.index') }}" method="GET" class="row gy-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Halaqah</label>
                    <select name="halaqah_id" id="halaqah_id" class="form-select" required>
                        <option value="">-- Pilih Halaqah --</option>
                        @foreach ($halaqahs as $h)
                            <option value="{{ $h->id }}" {{ request('halaqah_id') == $h->id ? 'selected' : '' }}>
                                {{ $h->nama_halaqah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kegiatan Utama</label>
                    <select name="kegiatan_utama" class="form-select">
                        <option value="">-- Semua Kegiatan --</option>
                        @foreach($kegiatanUtama as $k)
                            <option value="{{ $k->id }}" {{ request('kegiatan_utama') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kegiatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" 
                           value="{{ request('start_date') }}" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sampai</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" 
                           value="{{ request('end_date') }}" required>
                </div>
                
                @if(auth()->user()->hasRole('santri') && auth()->user()->halaqah_id)
                    <input type="hidden" name="halaqah_id" value="{{ auth()->user()->halaqah_id }}">
                    <input type="hidden" name="santri_id" value="{{ auth()->user()->id }}">
                @endif
                
                <div class="col-md-12 text-center mt-2">
                    <button class="btn btn-primary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- INFO ROLE --}}
    @if(auth()->user()->hasRole('santri'))
        <div class="alert alert-warning mb-4 fade-in">
            <i class="fas fa-user-graduate me-2"></i>
            Anda login sebagai <strong>Santri</strong>. Hanya dapat melihat laporan Anda sendiri.
        </div>
    @elseif(auth()->user()->hasRole('musyrif') && !auth()->user()->hasRole('admin'))
        <div class="alert alert-info mb-4 fade-in">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Anda login sebagai <strong>Musyrif</strong>. Hanya dapat mengelola halaqah Anda sendiri.
        </div>
    @endif

    {{-- FORM HIDDEN UNTUK DOWNLOAD PDF --}}
    <form action="{{ route('laporan.pdf') }}" method="POST" id="pdf-form" target="_blank" style="display: none;">
        @csrf
        <input type="hidden" name="halaqah_id" value="{{ request('halaqah_id') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        @if(request()->filled('santri_id'))
            <input type="hidden" name="santri_id" value="{{ request('santri_id') }}">
        @endif
        @if(request()->filled('kegiatan_utama'))
            <input type="hidden" name="kegiatan_utama" value="{{ request('kegiatan_utama') }}">
        @endif
    </form>

    {{-- INFO HEADER --}}
    @if(request()->filled(['halaqah_id', 'start_date', 'end_date']))
        @php
            $selectedHalaqah = $halaqahs->where('id', request('halaqah_id'))->first();
            $startDate = \Carbon\Carbon::parse(request('start_date'));
            $endDate = \Carbon\Carbon::parse(request('end_date'));
            $daysCount = $startDate->diffInDays($endDate) + 1;
        @endphp
        
        <div class="alert alert-info mb-4 fade-in">
            <i class="fas fa-info-circle me-2"></i>
            Menampilkan laporan untuk:
            <strong>{{ $selectedHalaqah->nama_halaqah ?? '-' }}</strong> |
            Periode: <strong>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</strong> 
            ({{ $daysCount }} hari) |
            Total: <strong>{{ count($laporan) }} Santri</strong>
        </div>
    @endif

    {{-- ACTION BUTTONS --}}
    @if(request()->filled(['halaqah_id', 'start_date', 'end_date']) && count($laporan) > 0)
        @php
            $user = auth()->user();
            $showWhatsAppBtn = $user->hasAnyRole(['admin', 'musyrif']);
            $showDownloadBtn = $user->hasAnyRole(['admin', 'musyrif', 'santri']);
        @endphp
        
        @if($showWhatsAppBtn || $showDownloadBtn)
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        @if($showWhatsAppBtn)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" id="selectAllCheckbox">
                                    <label class="form-check-label fw-semibold" for="selectAllCheckbox">Pilih Semua</label>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6"></div>
                        @endif
                        
                        <div class="col-md-6 text-end">
                            @if($showWhatsAppBtn)
                                <button type="button" class="btn btn-success px-4 py-2" id="btn-broadcast-selected">
                                    <i class="fab fa-whatsapp me-2"></i>Kirim WhatsApp
                                </button>
                            @endif
                            
                            @if($showDownloadBtn)
                                <button type="submit" form="pdf-form" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- DATA LAPORAN --}}
    @if(count($laporan) > 0)
        <div class="card fade-in">
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-striped table-bordered align-middle" width="100%">
                        <thead class="table-primary">
                            @if(isset($laporan[0]['data']))
                                <tr>
                                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                                        <th class="text-center" style="width: 50px;">Pilih</th>
                                    @endif
                                    <th>Santri</th>
                                    @foreach($dates as $d)
                                        <th class="text-center">{{ \Carbon\Carbon::parse($d)->format('d M') }}</th>
                                    @endforeach
                                    <th class="text-center">Total</th>
                                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif', 'santri']))
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    @endif
                                </tr>
                            @elseif(isset($laporan[0]['pagi']))
                                <tr>
                                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                                        <th class="text-center" style="width: 50px;" rowspan="2">Pilih</th>
                                    @endif
                                    <th rowspan="2">Santri</th>
                                    @foreach ($dates as $d)
                                        <th colspan="2" class="text-center">{{ \Carbon\Carbon::parse($d)->format('d M Y') }}</th>
                                    @endforeach
                                    <th colspan="2" class="text-center">Total</th>
                                    @if(auth()->user()->hasAnyRole(['admin', 'musyrif', 'santri']))
                                        <th rowspan="2" class="text-center" style="width: 120px;">Aksi</th>
                                    @endif
                                </tr>
                                <tr>
                                    @foreach ($dates as $d)
                                        <th class="text-center bg-light"><i class="fas fa-sun text-warning"></i> Pagi</th>
                                        <th class="text-center bg-light"><i class="fas fa-moon text-primary"></i> Malam</th>
                                    @endforeach
                                    <th class="text-center">Pagi</th>
                                    <th class="text-center">Malam</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($laporan as $row)
                            <tr>
                                @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input santri-checkbox" 
                                               name="selected_santri[]"
                                               data-santri-id="{{ $row['id'] }}" 
                                               data-santri-name="{{ $row['santri'] }}"
                                               data-santri-phone="{{ $row['telepon'] ?? '' }}">
                                    </td>
                                @endif
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 36px; height: 36px;">
                                            {{ substr($row['santri'], 0, 1) }}
                                        </div>
                                        <div>
                                            <strong>{{ $row['santri'] }}</strong>
                                            @if($row['telepon'])
                                                <div class="small text-muted">{{ $row['telepon'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                @if(isset($row['data']))
                                    @foreach($dates as $d)
                                        <td class="text-center">
                                            @php
                                                $value = $row['data'][$d] ?? '0/0';
                                                list($done, $total) = explode('/', $value);
                                                $percentage = $total > 0 ? ($done / $total) * 100 : 0;
                                            @endphp
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="fw-semibold">{{ $done }}/{{ $total }}</span>
                                                <div class="progress w-100" style="height: 5px;">
                                                    <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $row['total'] }}</span>
                                    </td>
                                @elseif(isset($row['pagi']))
                                    @foreach ($dates as $d)
                                        <td class="text-center">
                                            @php
                                                $pagiValue = $row['pagi'][$d] ?? '0/0';
                                                list($pagiDone, $pagiTotal) = explode('/', $pagiValue);
                                                $pagiPercentage = $pagiTotal > 0 ? ($pagiDone / $pagiTotal) * 100 : 0;
                                            @endphp
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="fw-semibold">{{ $pagiDone }}/{{ $pagiTotal }}</span>
                                                <div class="progress w-100" style="height: 5px;">
                                                    <div class="progress-bar bg-warning" style="width: {{ $pagiPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $malamValue = $row['malam'][$d] ?? '0/0';
                                                list($malamDone, $malamTotal) = explode('/', $malamValue);
                                                $malamPercentage = $malamTotal > 0 ? ($malamDone / $malamTotal) * 100 : 0;
                                            @endphp
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="fw-semibold">{{ $malamDone }}/{{ $malamTotal }}</span>
                                                <div class="progress w-100" style="height: 5px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $malamPercentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <span class="badge bg-warning rounded-pill px-3 py-2">{{ $row['total_pagi'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $row['total_malam'] }}</span>
                                    </td>
                                @endif
                                
                                @if(auth()->user()->hasAnyRole(['admin', 'musyrif', 'santri']))
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary btn-download" 
                                                    data-santri-id="{{ $row['id'] }}"
                                                    data-santri-name="{{ $row['santri'] }}"
                                                    title="Download Laporan">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            
                                            @if(auth()->user()->hasAnyRole(['admin', 'musyrif']))
                                                <button class="btn btn-sm btn-success btn-whatsapp-individual"
                                                        data-santri-id="{{ $row['id'] }}"
                                                        data-santri-name="{{ $row['santri'] }}"
                                                        data-santri-phone="{{ $row['telepon'] ?? '' }}"
                                                        title="Kirim WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif(request()->filled(['halaqah_id', 'start_date', 'end_date']))
        <div class="card text-center p-5 fade-in">
            <div class="card-body">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data laporan</h5>
                <p class="text-muted">Tidak ada data laporan untuk filter yang dipilih.</p>
            </div>
        </div>
    @else
        <div class="card text-center p-5 fade-in">
            <div class="card-body">
                <i class="fas fa-filter fa-3x text-primary mb-3"></i>
                <h5 class="text-primary">Pilih Filter Laporan</h5>
                <p class="text-muted">Silakan pilih halaqah dan tanggal untuk menampilkan laporan.</p>
            </div>
        </div>
    @endif
@endsection

@push('datatable-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('styles')
<style>
    .progress {
        height: 5px;
        border-radius: 3px;
    }
    
    .progress-bar {
        border-radius: 3px;
    }
    
    .badge {
        font-size: 0.875rem;
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
    
    .btn-whatsapp-individual {
        background-color: #25D366;
        border-color: #25D366;
        color: white;
    }
    
    .btn-whatsapp-individual:hover {
        background-color: #128C7E;
        border-color: #128C7E;
    }
    
    #btn-broadcast-selected {
        background-color: #25D366;
        border-color: #25D366;
        color: white;
    }
    
    #btn-broadcast-selected:hover {
        background-color: #128C7E;
        border-color: #128C7E;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // ========== HIDE/SHOW ELEMENTS BERDASARKAN ROLE ==========
        const userRole = '{{ auth()->user()->roles->pluck("name")->first() }}';
        const isAdmin = userRole === 'admin';
        const isMusyrif = userRole === 'musyrif';
        const isSantri = userRole === 'santri';
        
        // ========== FUNGSI CHECKBOX ==========
        $('#selectAllCheckbox').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.santri-checkbox').prop('checked', isChecked);
            updateBroadcastButton();
        });

        $(document).on('change', '.santri-checkbox', function() {
            const total = $('.santri-checkbox').length;
            const checked = $('.santri-checkbox:checked').length;
            const selectAll = $('#selectAllCheckbox');
            
            if (checked === 0) {
                selectAll.prop('checked', false);
                selectAll.prop('indeterminate', false);
            } else if (checked === total) {
                selectAll.prop('checked', true);
                selectAll.prop('indeterminate', false);
            } else {
                selectAll.prop('checked', false);
                selectAll.prop('indeterminate', true);
            }
            
            updateBroadcastButton();
        });

        function updateBroadcastButton() {
            const selected = $('.santri-checkbox:checked').length;
            const btn = $('#btn-broadcast-selected');
            if (selected > 0) {
                btn.html(`<i class="fab fa-whatsapp me-2"></i>Kirim ke ${selected} Santri`);
            } else {
                btn.html(`<i class="fab fa-whatsapp me-2"></i>Kirim WhatsApp`);
            }
        }

        // ========== FUNGSI WHATSAPP INDIVIDUAL ==========
        $(document).on('click', '.btn-whatsapp-individual', function(e) {
            e.preventDefault();
            
            const santriId = $(this).data('santri-id');
            const santriName = $(this).data('santri-name');
            const santriPhone = $(this).data('santri-phone') || '';
            
            // Format nomor telepon
            function formatPhone(phone) {
                if (!phone) return null;
                let cleaned = phone.toString().replace(/\D/g, '');
                if (!cleaned) return null;
                if (cleaned.startsWith('0')) cleaned = '62' + cleaned.substring(1);
                if (!cleaned.startsWith('62') && cleaned.length >= 10) cleaned = '62' + cleaned;
                return cleaned.length >= 10 ? cleaned : null;
            }
            
            const formattedPhone = formatPhone(santriPhone);
            
            // Tampilkan modal konfirmasi
            Swal.fire({
                title: 'Kirim Laporan via WhatsApp',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Kirim laporan kegiatan <strong>${santriName}</strong> via WhatsApp?
                        </div>
                        <p class="mb-3">Nomor tujuan: <code>${formattedPhone || santriPhone || 'Tidak ada nomor'}</code></p>
                        <div class="mb-3">
                            <label class="form-label">Pesan Tambahan (opsional):</label>
                            <textarea class="form-control" id="whatsapp-message" rows="3" 
                                      placeholder="Contoh: Ananda hari ini aktif dalam tahfidz..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim WhatsApp',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#25D366',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendIndividualWhatsApp(santriId, santriName, result.value.message);
                }
            });
        });

        // ========== FUNGSI KIRIM WHATSAPP INDIVIDUAL ==========
        function sendIndividualWhatsApp(santriId, santriName, additionalMessage = '') {
            // Validasi form
            const halaqahId = $('#halaqah_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            if (!halaqahId || !startDate || !endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Filter Tidak Lengkap',
                    text: 'Harap lengkapi filter halaqah dan tanggal terlebih dahulu',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menyiapkan Laporan...',
                html: '<div class="loading-spinner my-3"></div><p>Sedang mengambil data kegiatan santri</p>',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Prepare data
            const requestData = {
                santri_id: santriId,
                additional_message: additionalMessage,
                halaqah_id: halaqahId,
                start_date: startDate,
                end_date: endDate,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Kirim request AJAX
            $.ajax({
                url: '{{ route("laporan.send-whatsapp") }}',
                type: 'POST',
                data: requestData,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    
                    if (response.success && response.whatsapp_url) {
                        // Buka WhatsApp langsung
                        window.open(response.whatsapp_url, '_blank');
                        
                        // Tampilkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'WhatsApp Dibuka!',
                            html: `
                                <div class="text-center">
                                    <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                                    <p>Pesan telah dibuka di WhatsApp untuk:</p>
                                    <p><strong>${response.santri_name}</strong></p>
                                    <div class="alert alert-info text-start small mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Jika WhatsApp tidak terbuka, klik link di bawah:
                                    </div>
                                    <div class="mt-3">
                                        <a href="${response.whatsapp_url}" target="_blank" 
                                           class="btn btn-success">
                                            <i class="fab fa-whatsapp me-2"></i> Buka WhatsApp Lagi
                                        </a>
                                    </div>
                                </div>
                            `,
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#25D366',
                            width: '450px'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim',
                            text: response.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    
                    let errorMessage = 'Terjadi kesalahan saat mengirim WhatsApp';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#0d6efd'
                    });
                }
            });
        }

        // ========== FUNGSI BROADCAST SELECTED ==========
        $('#btn-broadcast-selected').on('click', function() {
            const selectedSantris = [];
            const selectedNames = [];
            
            $('.santri-checkbox:checked').each(function() {
                const santriId = $(this).data('santri-id');
                const santriName = $(this).data('santri-name');
                
                if (santriId) {
                    selectedSantris.push({
                        id: santriId,
                        name: santriName
                    });
                    selectedNames.push(santriName);
                }
            });
            
            if (selectedSantris.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Santri Terpilih',
                    text: 'Silakan pilih minimal satu santri',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }
            
            // Validasi filter
            const halaqahId = $('#halaqah_id').val();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            if (!halaqahId || !startDate || !endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Filter Tidak Lengkap',
                    text: 'Harap lengkapi filter halaqah dan tanggal terlebih dahulu',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }
            
            // Tampilkan modal konfirmasi untuk batch
            Swal.fire({
                title: 'Broadcast WhatsApp',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Kirim laporan ke <strong>${selectedSantris.length}</strong> santri via WhatsApp?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Santri yang terpilih:</label>
                            <div class="border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                <ul class="mb-0">
                                    ${selectedNames.map(name => `<li>${name}</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan Tambahan (opsional):</label>
                            <textarea class="form-control" id="batch-whatsapp-message" rows="3" 
                                      placeholder="Pesan yang sama akan dikirim ke semua santri..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim WhatsApp',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#25D366',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendBatchWhatsApp(selectedSantris, result.value.message);
                }
            });
        });

        // ========== FUNGSI KIRIM WHATSAPP BATCH ==========
        function sendBatchWhatsApp(santriList, additionalMessage = '') {
            // Tampilkan loading
            Swal.fire({
                title: 'Mengirim WhatsApp...',
                html: `<div class="loading-spinner my-3"></div><p>Menyiapkan pesan untuk ${santriList.length} santri</p>`,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            // Prepare data untuk batch
            const requestData = {
                santri_ids: santriList.map(s => s.id),
                additional_message: additionalMessage,
                halaqah_id: $('#halaqah_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Kirim request AJAX batch
            $.ajax({
                url: '{{ route("laporan.send-whatsapp-selected") }}',
                type: 'POST',
                data: requestData,
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    
                    if (response.success) {
                        const successCount = response.data.success_count || 0;
                        const totalCount = response.data.total || 0;
                        const failedCount = response.data.failed_count || 0;
                        
                        let message = `<div class="text-start">`;
                        message += `<p><strong>✅ Broadcast WhatsApp Selesai!</strong></p>`;
                        message += `<p>Berhasil: <span class="text-success">${successCount} santri</span></p>`;
                        message += `<p>Gagal: <span class="text-danger">${failedCount} santri</span></p>`;
                        message += `<p>Total: ${totalCount} santri</p>`;
                        
                        // Tampilkan detail hasil jika ada
                        if (response.data.results && response.data.results.length > 0) {
                            message += `<div class="mt-3" style="max-height: 200px; overflow-y: auto;">`;
                            message += `<table class="table table-sm table-bordered">`;
                            message += `<thead><tr><th>Santri</th><th>Status</th><th>Aksi</th></tr></thead>`;
                            message += `<tbody>`;
                            
                            response.data.results.forEach(result => {
                                const statusClass = result.success ? 'text-success' : 'text-danger';
                                const statusIcon = result.success ? '✓' : '✗';
                                const statusText = result.success ? 'Berhasil' : (result.error || 'Gagal');
                                
                                message += `<tr>`;
                                message += `<td>${result.santri_name}</td>`;
                                message += `<td><span class="${statusClass}">${statusIcon} ${statusText}</span></td>`;
                                if (result.success && result.whatsapp_url) {
                                    message += `<td><a href="${result.whatsapp_url}" target="_blank" class="btn btn-sm btn-outline-success">Buka WA</a></td>`;
                                } else {
                                    message += `<td>-</td>`;
                                }
                                message += `</tr>`;
                            });
                            
                            message += `</tbody></table></div>`;
                        }
                        
                        message += `</div>`;
                        
                        Swal.fire({
                            title: 'Broadcast Selesai!',
                            html: message,
                            showCancelButton: true,
                            cancelButtonText: 'Tutup',
                            showConfirmButton: false,
                            width: '650px'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengirim',
                            text: response.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim WhatsApp batch.',
                        confirmButtonColor: '#0d6efd'
                    });
                }
            });
        }

        // ========== FUNGSI DOWNLOAD INDIVIDUAL ==========
        $(document).on('click', '.btn-download', function() {
            const santriId = $(this).data('santri-id');
            const santriName = $(this).data('santri-name');
            
            Swal.fire({
                title: 'Download Laporan',
                text: `Download laporan untuk ${santriName}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Download',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    const currentForm = $('#pdf-form');
                    currentForm.append(`<input type="hidden" name="santri_id" value="${santriId}">`);
                    currentForm.submit();
                    setTimeout(() => {
                        currentForm.find('input[name="santri_id"]').last().remove();
                    }, 100);
                }
            });
        });

        // ========== AUTO-SET FILTER UNTUK SANTRI ==========
        @if(auth()->user()->hasRole('santri'))
            @if(auth()->user()->halaqah_id)
                $('#halaqah_id').val('{{ auth()->user()->halaqah_id }}');
            @endif
        @endif

        // Inisialisasi awal
        updateBroadcastButton();
    });
</script>
@endpush