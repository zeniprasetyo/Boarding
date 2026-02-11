<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        h2, h3 {
            text-align: center;
            margin: 5px 0;
        }
        h2 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 18px;
        }
        h3 {
            color: #34495e;
            font-size: 16px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header-info p {
            margin: 5px 0;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            background: #e9ecef;
            padding: 8px 10px;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        .section-title.kesehatan {
            border-left-color: #2ecc71;
        }
        .section-title.absen {
            border-left-color: #e74c3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
            text-align: center;
        }
        .status-belum {
            background-color: #f8d7da;
            color: #721c24;
            text-align: center;
        }
        .status-sehat {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
            text-align: center;
        }
        .status-sakit {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
            text-align: center;
        }
        .status-hadir {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
            text-align: center;
        }
        .status-izin {
            background-color: #fff3cd;
            color: #856404;
            font-weight: bold;
            text-align: center;
        }
        .status-alpa {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
            text-align: center;
        }
        .status-sakit-absen {
            background-color: #d1ecf1;
            color: #0c5460;
            font-weight: bold;
            text-align: center;
        }
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .summary-box h4 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .summary-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        .summary-item h5 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .summary-item p {
            margin: 0;
            font-size: 14px;
        }
        .page-break {
            page-break-after: always;
        }
        .ttd-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
        }
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .ttd-box {
            text-align: center;
            width: 45%;
        }
        .ttd-space {
            height: 80px;
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
        }
        .ttd-label {
            font-weight: bold;
            margin-top: 10px;
        }
        .ttd-name {
            margin-top: 5px;
        }
        .santri-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .santri-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        @media print {
            .page-break {
                page-break-after: always;
            }
            .no-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- HEADER -->
    <div class="header-info">
        <h2>LAPORAN HARIAN BOARDING AL ABIDIN</h2>
        <h3>Halaqah: {{ $halaqah->nama_halaqah }}</h3>
        <p><strong>Tanggal:</strong> {{ $tanggal }}</p>
        @if(isset($periode) && $periode)
            <p><strong>Periode:</strong> {{ $periode }}</p>
        @endif
        <p><strong>Total Santri:</strong> {{ $total_santri ?? count($data) }} orang</p>
    </div>

    @foreach($santri_list ?? $data as $row)
    <div class="no-break" style="margin-bottom: 40px;">
        
        <!-- INFORMASI SANTRI -->
        <div class="santri-info">
            <div class="santri-info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Santri:</span> {{ $row['santri'] }}
                </div>
                <div class="info-item">
                    <span class="info-label">Kelas:</span> {{ $row['kelas'] ?? '-' }}
                </div>
                @if(isset($row['statistik']))
                <div class="info-item">
                    <span class="info-label">Persentase Kegiatan:</span> {{ $row['statistik']['persentase'] }}%
                </div>
                @endif
            </div>
        </div>

        <!-- DATA KESEHATAN -->
        <div class="section-title kesehatan">KONDISI KESEHATAN</div>
        <table>
            <tr>
                <th width="40%">Status Kesehatan</th>
                <th width="60%">Keterangan</th>
            </tr>
            <tr>
                <td class="status-{{ strtolower(str_replace(' ', '-', $row['kesehatan']['status'])) }}">
                    {{ $row['kesehatan']['status'] }}
                </td>
                <td>{{ $row['kesehatan']['keterangan'] }}</td>
            </tr>
        </table>

        <!-- DATA ABSENSI -->
        <div class="section-title absen">ABSENSI HARIAN</div>
        <table>
            <tr>
                <th width="40%">Waktu</th>
                <th width="60%">Status</th>
            </tr>
            <tr>
                <td><strong>Pagi</strong></td>
                <td class="status-{{ strtolower(str_replace(' ', '-', $row['absen']['pagi'])) }}">
                    {{ $row['absen']['pagi'] }}
                </td>
            </tr>
            <tr>
                <td><strong>Malam</strong></td>
                <td class="status-{{ strtolower(str_replace(' ', '-', $row['absen']['malam'])) }}">
                    {{ $row['absen']['malam'] }}
                </td>
            </tr>
        </table>

        <!-- KEGIATAN PAGI -->
        <div class="section-title">KEGIATAN PAGI</div>
        <table>
            <tr>
                <th width="8%">No</th>
                <th width="52%">Aktivitas</th>
                <th width="20%">Status</th>
                <th width="20%">Keterangan</th>
            </tr>
            @foreach($row['pagi'] as $i => $p)
            <tr>
                <td style="text-align: center;">{{ $i+1 }}</td>
                <td>{{ $p['nama'] }}</td>
                <td class="{{ $p['status'] == 'Selesai' ? 'status-selesai' : 'status-belum' }}">
                    {{ $p['status'] }}
                </td>
                <td style="text-align: center;">
                    @if($p['status'] == 'Selesai')
                    ✓
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
            @if(isset($row['statistik']))
            <tr style="background: #f8f9fa;">
                <td colspan="2" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $row['statistik']['selesai_pagi'] }}/{{ $row['statistik']['total_pagi'] }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $row['statistik']['total_pagi'] > 0 ? round(($row['statistik']['selesai_pagi'] / $row['statistik']['total_pagi']) * 100, 0) : 0 }}%
                </td>
            </tr>
            @endif
        </table>

        <!-- KEGIATAN MALAM -->
        <div class="section-title">KEGIATAN SORE & MALAM</div>
        <table>
            <tr>
                <th width="8%">No</th>
                <th width="52%">Aktivitas</th>
                <th width="20%">Status</th>
                <th width="20%">Keterangan</th>
            </tr>
            @foreach($row['malam'] as $i => $m)
            <tr>
                <td style="text-align: center;">{{ $i+1 }}</td>
                <td>{{ $m['nama'] }}</td>
                <td class="{{ $m['status'] == 'Selesai' ? 'status-selesai' : 'status-belum' }}">
                    {{ $m['status'] }}
                </td>
                <td style="text-align: center;">
                    @if($m['status'] == 'Selesai')
                    ✓
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
            @if(isset($row['statistik']))
            <tr style="background: #f8f9fa;">
                <td colspan="2" style="text-align: right; font-weight: bold;">Total:</td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $row['statistik']['selesai_malam'] }}/{{ $row['statistik']['total_malam'] }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $row['statistik']['total_malam'] > 0 ? round(($row['statistik']['selesai_malam'] / $row['statistik']['total_malam']) * 100, 0) : 0 }}%
                </td>
            </tr>
            @endif
        </table>

        <!-- RINGKASAN -->
        @if(isset($row['statistik']))
        <div class="summary-box">
            <h4>📊 RINGKASAN KEGIATAN</h4>
            <div class="summary-grid">
                <div class="summary-item">
                    <h5>Total Kegiatan</h5>
                    <p>{{ $row['statistik']['total_kegiatan'] }} kegiatan</p>
                </div>
                <div class="summary-item">
                    <h5>Selesai</h5>
                    <p>{{ $row['statistik']['total_selesai'] }} kegiatan</p>
                </div>
                <div class="summary-item">
                    <h5>Persentase</h5>
                    <p>{{ $row['statistik']['persentase'] }}%</p>
                </div>
                <div class="summary-item">
                    <h5>Status Kesehatan</h5>
                    <p>{{ $row['kesehatan']['status'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- TTD -->
        <div class="ttd-section">
            <div class="ttd-container">
                <div class="ttd-box">
                    <div class="ttd-space"></div>
                    <div class="ttd-label">Orang Tua/Wali</div>
                    <div class="ttd-name">(___________________)</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-space"></div>
                    <div class="ttd-label">Musyrif Halaqah</div>
                    <div class="ttd-name">{{ $halaqah->musyrif->name ?? 'Musyrif Halaqah' }}</div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Laporan ini dibuat secara otomatis oleh Sistem Boarding Al Abidin<br>
            Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}
        </div>

        <!-- PAGE BREAK -->
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
        
    </div>
    @endforeach

</div>

</body>
</html>