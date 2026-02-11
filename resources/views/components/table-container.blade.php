{{-- 
    Usage:
    @include('components.table-container', [
        'tableId' => 'musyrifTable',
        'columns' => ['No', 'Nama', 'Kontak', 'Status', 'Aksi']
    ])
--}}

<div class="card fade-in">
    <div class="card-body p-0">
        <div class="table-container">
            <table id="{{ $tableId }}" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            @if($loop->first)
                                <th width="60">{{ $column }}</th>
                            @elseif($loop->last)
                                <th width="150" class="text-center">{{ $column }}</th>
                            @else
                                <th>{{ $column }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Data akan diisi oleh DataTables atau server-side --}}
                </tbody>
            </table>
        </div>
    </div>
</div>