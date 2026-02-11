{{-- 
    Usage:
    @include('components.header-card', [
        'title' => 'Judul Halaman',
        'icon' => 'fa-user-tie',
        'addButton' => true,
        'addButtonText' => 'Tambah Data',
        'addButtonId' => 'btnAdd',
        'filter' => true,
        'filterOptions' => [
            ['value' => 'live', 'text' => 'Data Aktif'],
            ['value' => 'deleted', 'text' => 'Data Terhapus']
        ]
    ])
--}}

<div class="card mb-4">
    <div class="card-header bg-white">
        <h4 class="mb-0">
            @if(isset($icon))
                <i class="fas {{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h4>
    </div>
    <div class="card-body p-4">
        <div class="row align-items-center">
            @if(isset($addButton) && $addButton)
                <div class="col-md-6 mb-3 mb-md-0">
                    <button class="btn btn-primary px-4" id="{{ $addButtonId ?? 'btnAdd' }}">
                        <i class="fas fa-plus me-2"></i>{{ $addButtonText ?? 'Tambah Data' }}
                    </button>
                </div>
            @endif
            
            @if(isset($filter) && $filter)
                <div class="{{ isset($addButton) && $addButton ? 'col-md-6' : 'col-12' }}">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <label class="form-label fw-semibold me-3 mb-0">Tampilkan:</label>
                        <select id="filterData" class="form-select w-auto">
                            @foreach($filterOptions ?? [] as $option)
                                <option value="{{ $option['value'] }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $option['text'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>