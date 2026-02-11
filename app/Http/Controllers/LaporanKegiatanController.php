<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CeklistKegiatan as Ceklist;
use App\Models\Kegiatan;
use App\Models\Halaqah;
use App\Models\Kesehatan;
use App\Models\Absen;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class LaporanKegiatanController extends Controller
{
    // Constructor tanpa middleware
    public function __construct()
    {
        // Tidak ada middleware di sini, karena sudah diatur di route
    }

    /**
     * 🔹 Halaman utama laporan kegiatan dengan auto detect device
     */
    public function index(Request $request)
    {
        $agent = new Agent();
        
        // Manual override
        if ($request->mode == 'mobile') {
            return $this->viewMobile($request);
        }

        if ($request->mode == 'desktop') {
            return $this->viewDesktop($request);
        }

        // Auto detect device
        if ($agent->isMobile() || $agent->isTablet()) {
            return $this->viewMobile($request);
        }

        return $this->viewDesktop($request);
    }

    /**
     * 🔹 View Desktop
     */
    private function viewDesktop($request)
    {
        // Cek authorization - semua role bisa akses
        $this->checkAuthorization(['admin', 'musyrif', 'santri']);
        
        $user = auth()->user();

        // Santri hanya bisa melihat laporan mereka sendiri
        if ($user->hasRole('santri')) {
            $santriId = $user->id;
            $halaqahId = $user->halaqah_id;
            
            // Jika santri mencoba akses halaqah lain, batalkan
            if ($request->filled('halaqah_id') && $request->halaqah_id != $halaqahId) {
                abort(403, 'Anda hanya dapat melihat data halaqah Anda sendiri.');
            }
            
            // Set default untuk santri
            if (!$request->filled('halaqah_id')) {
                $request->merge(['halaqah_id' => $halaqahId]);
            }
            
            // Santri hanya bisa melihat data mereka sendiri
            if (!$request->filled('santri_id')) {
                $request->merge(['santri_id' => $santriId]);
            } elseif ($request->santri_id != $santriId) {
                abort(403, 'Anda hanya dapat melihat data Anda sendiri.');
            }
        }

        $halaqahs = Halaqah::all();
        
        // Musyrif hanya bisa melihat halaqah mereka sendiri
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah();
            
            $halaqahs = Halaqah::where('id', $musyrifHalaqah->id)->get();
            
            // Jika musyrif mencoba akses halaqah lain, batalkan
            if ($request->filled('halaqah_id') && $request->halaqah_id != $musyrifHalaqah->id) {
                abort(403, 'Anda hanya dapat mengakses data halaqah Anda sendiri.');
            }
            
            // Set default untuk musyrif
            if (!$request->filled('halaqah_id')) {
                $request->merge(['halaqah_id' => $musyrifHalaqah->id]);
            }
        }

        $kegiatanUtama = Kegiatan::whereNull('parent_id')->get();

        $laporan = [];
        $dates   = [];

        if (!$request->filled(['halaqah_id', 'start_date', 'end_date'])) {
            return view('laporan', compact(
                'halaqahs',
                'kegiatanUtama',
                'laporan',
                'dates'
            ));
        }

        $dates = $this->generateDateRange(
            $request->start_date,
            $request->end_date
        );

        $santriList = User::where('halaqah_id', $request->halaqah_id);
        
        // Santri hanya bisa melihat data mereka sendiri
        if ($user->hasRole('santri')) {
            $santriList->where('id', $user->id);
        }
        
        $santriList = $santriList->get();

        if ($request->filled('kegiatan_utama')) {
            $childKegiatan = Kegiatan::where(
                'parent_id',
                $request->kegiatan_utama
            )->pluck('id')->toArray();

            $totalKegiatan = count($childKegiatan);

            foreach ($santriList as $santri) {
                $row = [
                    'santri' => $santri->name,
                    'id' => $santri->id,
                    'telepon' => $santri->telephone ?? null,
                    'data'   => [],
                    'total'  => 0,
                ];

                foreach ($dates as $date) {
                    $done = Ceklist::where('santri_id', $santri->id)
                        ->whereIn('kegiatan_id', $childKegiatan)
                        ->whereDate('tanggal', $date)
                        ->count();

                    $row['data'][$date] = "$done/$totalKegiatan";
                    $row['total'] += $done;
                }

                $laporan[] = $row;
            }

            return view('laporan', compact(
                'halaqahs',
                'kegiatanUtama',
                'laporan',
                'dates'
            ));
        }

        $PAGI_PARENT  = 1;
        $MALAM_PARENT = 3;

        $kegiatanPagi  = Kegiatan::where('parent_id', $PAGI_PARENT)->pluck('id')->toArray();
        $kegiatanMalam = Kegiatan::where('parent_id', $MALAM_PARENT)->pluck('id')->toArray();

        foreach ($santriList as $santri) {
            $row = [
                'santri'      => $santri->name,
                'id'          => $santri->id,
                'telepon'     => $santri->telephone ?? null,
                'pagi'        => [],
                'malam'       => [],
                'total_pagi'  => '0/' . count($kegiatanPagi),
                'total_malam' => '0/' . count($kegiatanMalam),
            ];

            $sumPagi = 0;
            $sumMalam = 0;

            foreach ($dates as $date) {
                $pagiDone = Ceklist::where('santri_id', $santri->id)
                    ->whereIn('kegiatan_id', $kegiatanPagi)
                    ->whereDate('tanggal', $date)
                    ->count();

                $malamDone = Ceklist::where('santri_id', $santri->id)
                    ->whereIn('kegiatan_id', $kegiatanMalam)
                    ->whereDate('tanggal', $date)
                    ->count();

                $row['pagi'][$date]  = "$pagiDone/" . count($kegiatanPagi);
                $row['malam'][$date] = "$malamDone/" . count($kegiatanMalam);

                $sumPagi += $pagiDone;
                $sumMalam += $malamDone;
            }

            $row['total_pagi']  = "$sumPagi/" . (count($kegiatanPagi) * count($dates));
            $row['total_malam'] = "$sumMalam/" . (count($kegiatanMalam) * count($dates));

            $laporan[] = $row;
        }

        return view('laporan', compact(
            'halaqahs',
            'kegiatanUtama',
            'laporan',
            'dates'
        ));
    }

    /**
     * 🔹 View Mobile
     */
    private function viewMobile($request)
    {
        // Cek authorization - semua role bisa akses
        $this->checkAuthorization(['admin', 'musyrif', 'santri']);
        
        $user = auth()->user();

        // Santri hanya bisa melihat laporan mereka sendiri
        if ($user->hasRole('santri')) {
            $santriId = $user->id;
            $halaqahId = $user->halaqah_id;
            
            // Jika santri mencoba akses halaqah lain, batalkan
            if ($request->filled('halaqah_id') && $request->halaqah_id != $halaqahId) {
                abort(403, 'Anda hanya dapat melihat data halaqah Anda sendiri.');
            }
            
            // Set default untuk santri
            if (!$request->filled('halaqah_id')) {
                $request->merge(['halaqah_id' => $halaqahId]);
            }
            
            // Santri hanya bisa melihat data mereka sendiri
            if (!$request->filled('santri_id')) {
                $request->merge(['santri_id' => $santriId]);
            } elseif ($request->santri_id != $santriId) {
                abort(403, 'Anda hanya dapat melihat data Anda sendiri.');
            }
        }

        $halaqahs = Halaqah::all();
        
        // Musyrif hanya bisa melihat halaqah mereka sendiri
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah();
            
            $halaqahs = Halaqah::where('id', $musyrifHalaqah->id)->get();
            
            // Jika musyrif mencoba akses halaqah lain, batalkan
            if ($request->filled('halaqah_id') && $request->halaqah_id != $musyrifHalaqah->id) {
                abort(403, 'Anda hanya dapat mengakses data halaqah Anda sendiri.');
            }
            
            // Set default untuk musyrif
            if (!$request->filled('halaqah_id')) {
                $request->merge(['halaqah_id' => $musyrifHalaqah->id]);
            }
        }

        $kegiatanUtama = Kegiatan::whereNull('parent_id')->get();

        $laporan = [];
        $dates   = [];

        if (!$request->filled(['halaqah_id', 'start_date', 'end_date'])) {
            return view('laporan_mobile', [
                'halaqahs' => $halaqahs,
                'kegiatanUtama' => $kegiatanUtama,
                'laporan' => $laporan,
                'dates' => $dates,
                'user_role' => $user->getRoleNames()->first()
            ]);
        }

        $dates = $this->generateDateRange(
            $request->start_date,
            $request->end_date
        );

        $santriList = User::where('halaqah_id', $request->halaqah_id);
        
        // Santri hanya bisa melihat data mereka sendiri
        if ($user->hasRole('santri')) {
            $santriList->where('id', $user->id);
        }
        
        $santriList = $santriList->get();

        if ($request->filled('kegiatan_utama')) {
            $childKegiatan = Kegiatan::where(
                'parent_id',
                $request->kegiatan_utama
            )->pluck('id')->toArray();

            $totalKegiatan = count($childKegiatan);

            foreach ($santriList as $santri) {
                $row = [
                    'santri' => $santri->name,
                    'id' => $santri->id,
                    'telepon' => $santri->telephone ?? null,
                    'data'   => [],
                    'total'  => 0,
                ];

                foreach ($dates as $date) {
                    $done = Ceklist::where('santri_id', $santri->id)
                        ->whereIn('kegiatan_id', $childKegiatan)
                        ->whereDate('tanggal', $date)
                        ->count();

                    $row['data'][$date] = "$done/$totalKegiatan";
                    $row['total'] += $done;
                }

                $laporan[] = $row;
            }

            return view('laporan_mobile', [
                'halaqahs' => $halaqahs,
                'kegiatanUtama' => $kegiatanUtama,
                'laporan' => $laporan,
                'dates' => $dates,
                'user_role' => $user->getRoleNames()->first()
            ]);
        }

        $PAGI_PARENT  = 1;
        $MALAM_PARENT = 3;

        $kegiatanPagi  = Kegiatan::where('parent_id', $PAGI_PARENT)->pluck('id')->toArray();
        $kegiatanMalam = Kegiatan::where('parent_id', $MALAM_PARENT)->pluck('id')->toArray();

        foreach ($santriList as $santri) {
            $row = [
                'santri'      => $santri->name,
                'id'          => $santri->id,
                'telepon'     => $santri->telephone ?? null,
                'pagi'        => [],
                'malam'       => [],
                'total_pagi'  => '0/' . count($kegiatanPagi),
                'total_malam' => '0/' . count($kegiatanMalam),
            ];

            $sumPagi = 0;
            $sumMalam = 0;

            foreach ($dates as $date) {
                $pagiDone = Ceklist::where('santri_id', $santri->id)
                    ->whereIn('kegiatan_id', $kegiatanPagi)
                    ->whereDate('tanggal', $date)
                    ->count();

                $malamDone = Ceklist::where('santri_id', $santri->id)
                    ->whereIn('kegiatan_id', $kegiatanMalam)
                    ->whereDate('tanggal', $date)
                    ->count();

                $row['pagi'][$date]  = "$pagiDone/" . count($kegiatanPagi);
                $row['malam'][$date] = "$malamDone/" . count($kegiatanMalam);

                $sumPagi += $pagiDone;
                $sumMalam += $malamDone;
            }

            $row['total_pagi']  = "$sumPagi/" . (count($kegiatanPagi) * count($dates));
            $row['total_malam'] = "$sumMalam/" . (count($kegiatanMalam) * count($dates));

            $laporan[] = $row;
        }

        return view('laporan_mobile', [
            'halaqahs' => $halaqahs,
            'kegiatanUtama' => $kegiatanUtama,
            'laporan' => $laporan,
            'dates' => $dates,
            'user_role' => $user->getRoleNames()->first()
        ]);
    }

    /**
     * 🔹 Data khusus untuk mobile (API)
     */
    public function dataMobile(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek authorization - semua role bisa akses
            $this->checkAuthorization(['admin', 'musyrif', 'santri']);

            $halaqah_id = $request->halaqah_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $kegiatan_utama = $request->kegiatan_utama;
            $santri_id = $request->santri_id;

            // Validasi input
            if (!$halaqah_id || !$start_date || !$end_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap lengkapi filter halaqah dan tanggal'
                ], 400);
            }

            // Validasi tanggal
            if (new \DateTime($start_date) > new \DateTime($end_date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir'
                ], 400);
            }

            // Cek hak akses halaqah
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $musyrifHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->first();
                
                if (!$musyrifHalaqah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak mengakses halaqah ini'
                    ], 403);
                }
            }

            // Jika santri, pastikan hanya mengakses data sendiri
            if ($user->hasRole('santri')) {
                $santri_id = $user->id;
                
                // Cek apakah santri ada di halaqah yang diminta
                $santri = User::where('id', $user->id)
                    ->where('halaqah_id', $halaqah_id)
                    ->first();
                
                if (!$santri) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak mengakses data halaqah ini'
                    ], 403);
                }
            }

            // Generate dates
            $dates = $this->generateDateRange($start_date, $end_date);

            // Get santri list
            $santriQuery = User::where('halaqah_id', $halaqah_id);
            
            if ($santri_id) {
                $santriQuery->where('id', $santri_id);
            }
            
            $santriList = $santriQuery->get();

            $laporan = [];

            if ($kegiatan_utama) {
                $childKegiatan = Kegiatan::where('parent_id', $kegiatan_utama)
                    ->pluck('id')
                    ->toArray();

                $totalKegiatan = count($childKegiatan);

                foreach ($santriList as $santri) {
                    $row = [
                        'santri' => $santri->name,
                        'id' => $santri->id,
                        'telepon' => $santri->telephone ?? null,
                        'data' => [],
                        'total' => 0,
                    ];

                    foreach ($dates as $date) {
                        $done = Ceklist::where('santri_id', $santri->id)
                            ->whereIn('kegiatan_id', $childKegiatan)
                            ->whereDate('tanggal', $date)
                            ->count();

                        $row['data'][$date] = "$done/$totalKegiatan";
                        $row['total'] += $done;
                    }

                    $laporan[] = $row;
                }
            } else {
                // Default: pagi dan malam
                $PAGI_PARENT = 1;
                $MALAM_PARENT = 3;

                $kegiatanPagi = Kegiatan::where('parent_id', $PAGI_PARENT)
                    ->pluck('id')
                    ->toArray();
                $kegiatanMalam = Kegiatan::where('parent_id', $MALAM_PARENT)
                    ->pluck('id')
                    ->toArray();

                foreach ($santriList as $santri) {
                    $row = [
                        'santri' => $santri->name,
                        'id' => $santri->id,
                        'telepon' => $santri->telephone ?? null,
                        'pagi' => [],
                        'malam' => [],
                        'total_pagi' => 0,
                        'total_malam' => 0,
                    ];

                    $sumPagi = 0;
                    $sumMalam = 0;

                    foreach ($dates as $date) {
                        $pagiDone = Ceklist::where('santri_id', $santri->id)
                            ->whereIn('kegiatan_id', $kegiatanPagi)
                            ->whereDate('tanggal', $date)
                            ->count();

                        $malamDone = Ceklist::where('santri_id', $santri->id)
                            ->whereIn('kegiatan_id', $kegiatanMalam)
                            ->whereDate('tanggal', $date)
                            ->count();

                        $row['pagi'][$date] = "$pagiDone/" . count($kegiatanPagi);
                        $row['malam'][$date] = "$malamDone/" . count($kegiatanMalam);

                        $sumPagi += $pagiDone;
                        $sumMalam += $malamDone;
                    }

                    $row['total_pagi'] = $sumPagi;
                    $row['total_malam'] = $sumMalam;

                    $laporan[] = $row;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $laporan,
                'dates' => $dates,
                'total' => count($laporan)
            ]);

        } catch (\Exception $e) {
            Log::error('Laporan Mobile Data Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Simpan PDF ke server (API untuk download)
     */
    public function generatePdf(Request $request)
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);
        
        // Musyrif hanya bisa generate untuk halaqah mereka
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah($request->halaqah_id);
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda hanya dapat membuat PDF untuk halaqah Anda sendiri.');
            }
        }

        $request->validate([
            'halaqah_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $halaqah = Halaqah::findOrFail($request->halaqah_id);

        $santriQuery = User::where('halaqah_id', $halaqah->id);

        if ($request->filled('santri_id')) {
            $santriQuery->where('id', $request->santri_id);
        }

        $santriList = $santriQuery->get();

        if ($santriList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada santri ditemukan'
            ], 404);
        }

        $PAGI_PARENT = 1;
        $MALAM_PARENT = 3;

        $kegiatanPagi = Kegiatan::where('parent_id', $PAGI_PARENT)->get();
        $kegiatanMalam = Kegiatan::where('parent_id', $MALAM_PARENT)->get();

        $data = [];

        foreach ($santriList as $santri) {
            $pagi = [];
            foreach ($kegiatanPagi as $k) {
                $ceklist = Ceklist::where([
                    'santri_id'   => $santri->id,
                    'kegiatan_id' => $k->id,
                    'tanggal'     => $request->start_date,
                ])->first();

                $status = ($ceklist && $ceklist->status == 1) ? 'Selesai' : 'Belum';

                $pagi[] = [
                    'nama'   => $k->nama_kegiatan,
                    'status' => $status,
                ];
            }

            $malam = [];
            foreach ($kegiatanMalam as $k) {
                $ceklist = Ceklist::where([
                    'santri_id'   => $santri->id,
                    'kegiatan_id' => $k->id,
                    'tanggal'     => $request->start_date,
                ])->first();

                $status = ($ceklist && $ceklist->status == 1) ? 'Selesai' : 'Belum';

                $malam[] = [
                    'nama'   => $k->nama_kegiatan,
                    'status' => $status,
                ];
            }

            $dataKesehatan = Kesehatan::where('santri_id', $santri->id)
                ->whereDate('tanggal', $request->start_date)
                ->first();

            $kesehatanStatus = 'Belum diisi';
            $kesehatanKeterangan = '-';

            if ($dataKesehatan) {
                $statusMapping = [
                    'sehat' => 'Sehat',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'rumah sakit' => 'Rumah Sakit',
                    'rawat jalan' => 'Rawat Jalan'
                ];

                $kesehatanStatus = $statusMapping[$dataKesehatan->status] ?? ucfirst($dataKesehatan->status);
                $kesehatanKeterangan = $dataKesehatan->keterangan ?: '-';
            }

            $dataAbsen = Absen::where('santri_id', $santri->id)
                ->whereDate('tanggal', $request->start_date)
                ->first();

            $absenPagi = 'Belum diisi';
            $absenMalam = 'Belum diisi';

            if ($dataAbsen) {
                $absenMapping = [
                    'H' => 'Hadir',
                    'I' => 'Izin',
                    'A' => 'Alpa',
                    'S' => 'Sakit'
                ];

                $absenPagi = $dataAbsen->pagi ? ($absenMapping[$dataAbsen->pagi] ?? $dataAbsen->pagi) : 'Belum diisi';
                $absenMalam = $dataAbsen->malam ? ($absenMapping[$dataAbsen->malam] ?? $dataAbsen->malam) : 'Belum diisi';
            }

            $totalPagi = count($pagi);
            $selesaiPagi = count(array_filter($pagi, function ($item) {
                return $item['status'] == 'Selesai';
            }));

            $totalMalam = count($malam);
            $selesaiMalam = count(array_filter($malam, function ($item) {
                return $item['status'] == 'Selesai';
            }));

            $totalKegiatan = $totalPagi + $totalMalam;
            $totalSelesai = $selesaiPagi + $selesaiMalam;
            $persentase = $totalKegiatan > 0 ? round(($totalSelesai / $totalKegiatan) * 100, 1) : 0;

            $data[] = [
                'santri' => $santri->name,
                'kelas'  => $santri->kelas ?? '-',
                'pagi'   => $pagi,
                'malam'  => $malam,
                'kesehatan' => [
                    'status' => $kesehatanStatus,
                    'keterangan' => $kesehatanKeterangan
                ],
                'absen' => [
                    'pagi' => $absenPagi,
                    'malam' => $absenMalam
                ],
                'statistik' => [
                    'total_pagi' => $totalPagi,
                    'selesai_pagi' => $selesaiPagi,
                    'total_malam' => $totalMalam,
                    'selesai_malam' => $selesaiMalam,
                    'total_kegiatan' => $totalKegiatan,
                    'total_selesai' => $totalSelesai,
                    'persentase' => $persentase
                ]
            ];
        }

        $filename = 'laporan_' . Str::slug($halaqah->nama_halaqah) . '_' .
            Carbon::parse($request->start_date)->format('Y-m-d') . '_' .
            Str::random(10) . '.pdf';

        $folder = 'report/';
        $filepath = $folder . $filename;
        $fullPath = public_path($filepath);

        try {
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0755, true);
            }

            $periode = Carbon::parse($request->start_date)->translatedFormat('d F Y');
            if ($request->filled('end_date') && $request->end_date != $request->start_date) {
                $periode = Carbon::parse($request->start_date)->translatedFormat('d F Y') . ' s/d ' .
                    Carbon::parse($request->end_date)->translatedFormat('d F Y');
            }

            $pdf = Pdf::loadView('laporan_pdf', [
                'halaqah' => $halaqah,
                'tanggal' => Carbon::parse($request->start_date)->translatedFormat('d F Y'),
                'periode' => $periode,
                'data'    => $data,
                'total_santri' => count($data),
                'kegiatan_pagi' => $kegiatanPagi,
                'kegiatan_malam' => $kegiatanMalam
            ])->setPaper('A4', 'portrait');

            $pdf->save($fullPath);
            $url = url($filepath);

            $validPhones = [];
            $santriWithPhone = User::where('halaqah_id', $request->halaqah_id)
                ->whereNotNull('telephone')
                ->where('telephone', '!=', '')
                ->get(['id', 'name', 'telephone']);

            foreach ($santriWithPhone as $santri) {
                $formattedPhone = $this->formatPhoneNumber($santri->telephone);
                if ($formattedPhone) {
                    $validPhones[] = [
                        'id' => $santri->id,
                        'name' => $santri->name,
                        'phone' => $formattedPhone,
                        'original' => $santri->telephone
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
                'data' => [
                    'filename' => $filename,
                    'url' => $url,
                    'view_url' => route('laporan.view', ['filename' => $filename]),
                    'download_url' => route('laporan.download', ['filename' => $filename]),
                    'expires_at' => now()->addHours(24)->format('d F Y H:i'),
                    'halaqah' => $halaqah->nama_halaqah,
                    'tanggal' => Carbon::parse($request->start_date)->translatedFormat('d F Y'),
                    'total_santri' => count($data),
                    'whatsapp_data' => [
                        'total_santri_with_phone' => count($validPhones),
                        'phones' => $validPhones,
                        'pdf_url' => $url,
                        'halaqah_name' => $halaqah->nama_halaqah,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date ?? $request->start_date
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Download PDF langsung tanpa simpan ke server
     */
    public function exportPdf(Request $request)
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);
        
        // Musyrif hanya bisa export untuk halaqah mereka
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah($request->halaqah_id);
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda hanya dapat mengekspor PDF untuk halaqah Anda sendiri.');
            }
        }

        Log::info('PDF Export Request:', $request->all());

        $request->validate([
            'halaqah_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $halaqah = Halaqah::findOrFail($request->halaqah_id);

        $santriQuery = User::where('halaqah_id', $halaqah->id);

        if ($request->filled('santri_id')) {
            $santriQuery->where('id', $request->santri_id);
        }

        $santriList = $santriQuery->get();

        if ($santriList->isEmpty()) {
            return back()->with('error', 'Tidak ada santri ditemukan untuk halaqah ini.');
        }

        $PAGI_PARENT = 1;
        $MALAM_PARENT = 3;

        $kegiatanPagi = Kegiatan::where('parent_id', $PAGI_PARENT)->get();
        $kegiatanMalam = Kegiatan::where('parent_id', $MALAM_PARENT)->get();

        $data = [];

        foreach ($santriList as $santri) {
            $pagi = [];
            foreach ($kegiatanPagi as $k) {
                $ceklist = Ceklist::where([
                    'santri_id'   => $santri->id,
                    'kegiatan_id' => $k->id,
                    'tanggal'     => $request->start_date,
                ])->first();

                $status = ($ceklist && $ceklist->status == 1) ? 'Selesai' : 'Belum';

                $pagi[] = [
                    'nama'   => $k->nama_kegiatan,
                    'status' => $status,
                ];
            }

            $malam = [];
            foreach ($kegiatanMalam as $k) {
                $ceklist = Ceklist::where([
                    'santri_id'   => $santri->id,
                    'kegiatan_id' => $k->id,
                    'tanggal'     => $request->start_date,
                ])->first();

                $status = ($ceklist && $ceklist->status == 1) ? 'Selesai' : 'Belum';

                $malam[] = [
                    'nama'   => $k->nama_kegiatan,
                    'status' => $status,
                ];
            }

            $dataKesehatan = Kesehatan::where('santri_id', $santri->id)
                ->whereDate('tanggal', $request->start_date)
                ->first();

            $kesehatanStatus = 'Belum diisi';
            $kesehatanKeterangan = '-';

            if ($dataKesehatan) {
                $statusMapping = [
                    'sehat' => 'Sehat',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                    'rumah sakit' => 'Rumah Sakit',
                    'rawat jalan' => 'Rawat Jalan'
                ];

                $kesehatanStatus = $statusMapping[$dataKesehatan->status] ?? ucfirst($dataKesehatan->status);
                $kesehatanKeterangan = $dataKesehatan->keterangan ?: '-';
            }

            $dataAbsen = Absen::where('santri_id', $santri->id)
                ->whereDate('tanggal', $request->start_date)
                ->first();

            $absenPagi = 'Belum diisi';
            $absenMalam = 'Belum diisi';

            if ($dataAbsen) {
                $absenMapping = [
                    'H' => 'Hadir',
                    'I' => 'Izin',
                    'A' => 'Alpa',
                    'S' => 'Sakit'
                ];

                $absenPagi = $dataAbsen->pagi ? ($absenMapping[$dataAbsen->pagi] ?? $dataAbsen->pagi) : 'Belum diisi';
                $absenMalam = $dataAbsen->malam ? ($absenMapping[$dataAbsen->malam] ?? $dataAbsen->malam) : 'Belum diisi';
            }

            $totalPagi = count($pagi);
            $selesaiPagi = count(array_filter($pagi, function ($item) {
                return $item['status'] == 'Selesai';
            }));

            $totalMalam = count($malam);
            $selesaiMalam = count(array_filter($malam, function ($item) {
                return $item['status'] == 'Selesai';
            }));

            $totalKegiatan = $totalPagi + $totalMalam;
            $totalSelesai = $selesaiPagi + $selesaiMalam;
            $persentase = $totalKegiatan > 0 ? round(($totalSelesai / $totalKegiatan) * 100, 1) : 0;

            $data[] = [
                'santri' => $santri->name,
                'kelas'  => $santri->kelas ?? '-',
                'pagi'   => $pagi,
                'malam'  => $malam,
                'kesehatan' => [
                    'status' => $kesehatanStatus,
                    'keterangan' => $kesehatanKeterangan
                ],
                'absen' => [
                    'pagi' => $absenPagi,
                    'malam' => $absenMalam
                ],
                'statistik' => [
                    'total_pagi' => $totalPagi,
                    'selesai_pagi' => $selesaiPagi,
                    'total_malam' => $totalMalam,
                    'selesai_malam' => $selesaiMalam,
                    'total_kegiatan' => $totalKegiatan,
                    'total_selesai' => $totalSelesai,
                    'persentase' => $persentase
                ]
            ];
        }

        try {
            $periode = Carbon::parse($request->start_date)->translatedFormat('d F Y');
            if ($request->filled('end_date') && $request->end_date != $request->start_date) {
                $periode = Carbon::parse($request->start_date)->translatedFormat('d F Y') . ' s/d ' .
                    Carbon::parse($request->end_date)->translatedFormat('d F Y');
            }

            $pdf = Pdf::loadView('laporan_pdf', [
                'halaqah' => $halaqah,
                'tanggal' => Carbon::parse($request->start_date)->translatedFormat('d F Y'),
                'periode' => $periode,
                'data'    => $data,
                'total_santri' => count($data),
                'kegiatan_pagi' => $kegiatanPagi,
                'kegiatan_malam' => $kegiatanMalam
            ])->setPaper('A4', 'portrait');

            $filename = $request->filled('santri_id')
                ? 'laporan-' . str_replace(' ', '-', strtolower($santriList->first()->name)) . '.pdf'
                : 'laporan-harian-' . $halaqah->nama_halaqah . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * 🔹 Kirim WhatsApp untuk satu santri
     */
    public function sendWhatsApp(Request $request)
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);
        
        // Musyrif hanya bisa kirim untuk halaqah mereka
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah($request->halaqah_id);
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda hanya dapat mengirim pesan untuk halaqah Anda sendiri.');
            }
        }

        try {
            Log::info('WhatsApp Request Received:', $request->all());
            
            $request->validate([
                'santri_id' => 'required|exists:users,id',
                'halaqah_id' => 'required|exists:halaqah,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'additional_message' => 'nullable|string',
            ]);
            
            $santri = User::findOrFail($request->santri_id);
            $halaqah = Halaqah::findOrFail($request->halaqah_id);
            $startDate = Carbon::parse($request->start_date);
            
            $laporanData = $this->getLaporanDataHarian($santri->id, $startDate);
            $message = $this->formatWhatsAppMessageHarian($santri, $halaqah, $startDate, $laporanData, $request->additional_message);
            $phone = $this->formatPhoneNumber($santri->telephone);
            
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp tidak valid: ' . ($santri->telephone ?? 'Kosong')
                ], 400);
            }
            
            $whatsappUrl = $this->generateWhatsAppUrl($phone, $message);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan WhatsApp siap dikirim',
                'whatsapp_url' => $whatsappUrl,
                'phone' => $phone,
                'santri_name' => $santri->name,
                'santri_id' => $santri->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('WhatsApp Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Kirim WhatsApp untuk multiple santri (broadcast)
     */
    public function sendWhatsAppSelected(Request $request)
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);
        
        // Musyrif hanya bisa kirim untuk halaqah mereka
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah($request->halaqah_id);
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda hanya dapat mengirim pesan untuk halaqah Anda sendiri.');
            }
        }

        try {
            $request->validate([
                'santri_ids' => 'required|array',
                'santri_ids.*' => 'exists:users,id',
                'halaqah_id' => 'required|exists:halaqah,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'additional_message' => 'nullable|string',
            ]);
            
            $results = [];
            $halaqah = Halaqah::findOrFail($request->halaqah_id);
            
            // Ambil nama musyrif dari database
            $musyrifName = 'Musyrif Halaqah';
            if (isset($halaqah->musyrif_id) && $halaqah->musyrif_id) {
                $musyrif = User::find($halaqah->musyrif_id);
                if ($musyrif) {
                    $musyrifName = $musyrif->name;
                }
            } elseif (isset($halaqah->musrif_id) && $halaqah->musrif_id) {
                // Backup jika kolomnya musrif_id
                $musyrif = User::find($halaqah->musrif_id);
                if ($musyrif) {
                    $musyrifName = $musyrif->name;
                }
            }
            
            $startDate = Carbon::parse($request->start_date);
            $date = $startDate;
            
            foreach ($request->santri_ids as $santriId) {
                $santri = User::find($santriId);
                if (!$santri) {
                    $results[] = [
                        'santri_id' => $santriId,
                        'santri_name' => 'Tidak ditemukan',
                        'success' => false,
                        'error' => 'Santri tidak ditemukan'
                    ];
                    continue;
                }
                
                $phone = $this->formatPhoneNumber($santri->telephone);
                
                if (!$phone) {
                    $results[] = [
                        'santri_id' => $santri->id,
                        'santri_name' => $santri->name,
                        'phone' => $santri->telephone,
                        'success' => false,
                        'error' => 'Nomor tidak valid: ' . ($santri->telephone ?? 'Kosong')
                    ];
                    continue;
                }
                
                $laporanData = $this->getLaporanDataHarian($santri->id, $date);
                $message = $this->formatWhatsAppMessageHarian($santri, $halaqah, $date, $laporanData, $request->additional_message);
                $whatsappUrl = $this->generateWhatsAppUrl($phone, $message);
                
                $results[] = [
                    'santri_id' => $santri->id,
                    'santri_name' => $santri->name,
                    'phone' => $phone,
                    'original_phone' => $santri->telephone,
                    'whatsapp_url' => $whatsappUrl,
                    'success' => true,
                    'kesehatan_sehat' => $laporanData['kesehatan_sehat'],
                    'kesehatan_sakit' => $laporanData['kesehatan_sakit'],
                    'absen_pagi' => $laporanData['absen_pagi'],
                    'absen_malam' => $laporanData['absen_malam']
                ];
            }
            
            $successCount = count(array_filter($results, function($r) { return $r['success']; }));
            $totalCount = count($results);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil disiapkan',
                'data' => [
                    'results' => $results,
                    'total' => $totalCount,
                    'success_count' => $successCount,
                    'failed_count' => $totalCount - $successCount,
                    'summary' => [
                        'halaqah' => $halaqah->nama_halaqah,
                        'musyrif' => $musyrifName,
                        'tanggal' => $date->format('d-m-Y'),
                        'hari' => $date->translatedFormat('l')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Batch WhatsApp Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Download PDF yang sudah tersimpan
     */
    public function downloadPdf($filename)
    {
        // Cek authorization - semua role bisa download
        $this->checkAuthorization(['admin', 'musyrif', 'santri']);

        $filepath = 'report/' . $filename;
        
        if (!file_exists(public_path($filepath))) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(public_path($filepath), $filename);
    }

    /**
     * 🔹 View PDF di browser
     */
    public function viewPdf($filename)
    {
        // Cek authorization - semua role bisa view
        $this->checkAuthorization(['admin', 'musyrif', 'santri']);

        $filepath = 'report/' . $filename;
        
        if (!file_exists(public_path($filepath))) {
            abort(404, 'File tidak ditemukan');
        }

        $fullPath = public_path($filepath);
        $mimeType = mime_content_type($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    /**
     * 🔹 Kirim WhatsApp ke semua santri di halaqah
     */
    public function sendWhatsAppToAll(Request $request)
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);
        
        // Musyrif hanya bisa kirim untuk halaqah mereka
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = $this->checkMusyrifHalaqah($request->halaqah_id);
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda hanya dapat mengirim pesan untuk halaqah Anda sendiri.');
            }
        }

        $request->validate([
            'halaqah_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'pdf_url' => 'required',
            'message' => 'sometimes|string'
        ]);

        try {
            $halaqah = Halaqah::find($request->halaqah_id);
            
            // Ambil nama musyrif dari database
            $musyrifName = 'Musyrif Halaqah';
            if (isset($halaqah->musyrif_id) && $halaqah->musyrif_id) {
                $musyrif = User::find($halaqah->musyrif_id);
                if ($musyrif) {
                    $musyrifName = $musyrif->name;
                }
            } elseif (isset($halaqah->musrif_id) && $halaqah->musrif_id) {
                // Backup jika kolomnya musrif_id
                $musyrif = User::find($halaqah->musrif_id);
                if ($musyrif) {
                    $musyrifName = $musyrif->name;
                }
            }
            
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            
            $tanggalFormat = $startDate->translatedFormat('d F Y');
            if ($startDate->notEqualTo($endDate)) {
                $tanggalFormat = $startDate->translatedFormat('d F Y') . ' s/d ' . $endDate->translatedFormat('d F Y');
            }
            
            $santriList = User::where('halaqah_id', $request->halaqah_id)
                ->whereNotNull('telephone')
                ->where('telephone', '!=', '')
                ->get(['id', 'name', 'telephone', 'kelas']);
            
            $results = [];
            $successCount = 0;
            $failedCount = 0;
            
            foreach ($santriList as $santri) {
                $phone = $this->formatPhoneNumber($santri->telephone);
                
                if (!$phone) {
                    $results[] = [
                        'santri' => $santri->name,
                        'phone' => $santri->telephone,
                        'status' => 'invalid',
                        'message' => 'Nomor tidak valid'
                    ];
                    $failedCount++;
                    continue;
                }
                
                $laporanData = $this->getLaporanDataHarian($santri->id, $startDate);
                $message = $this->formatWhatsAppMessageHarian($santri, $halaqah, $startDate, $laporanData, $request->message);
                $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
                
                $results[] = [
                    'santri' => $santri->name,
                    'phone' => $phone,
                    'whatsapp_url' => $whatsappUrl,
                    'status' => 'success',
                    'kesehatan_sehat' => $laporanData['kesehatan_sehat'],
                    'kesehatan_sakit' => $laporanData['kesehatan_sakit']
                ];
                $successCount++;
            }
            
            Log::info('WhatsApp broadcast sent', [
                'halaqah' => $halaqah->nama_halaqah,
                'musyrif' => $musyrifName,
                'total' => count($santriList),
                'success' => $successCount,
                'failed' => $failedCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dibuat untuk ' . $successCount . ' santri',
                'data' => [
                    'total_santri' => count($santriList),
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'results' => $results,
                    'summary' => [
                        'halaqah' => $halaqah->nama_halaqah,
                        'musyrif' => $musyrifName,
                        'periode' => $tanggalFormat,
                        'pdf_url' => $request->pdf_url
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('WhatsApp Broadcast Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim WhatsApp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Helper functions
     */
    private function checkAuthorization($allowedRoles)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }
    }

    private function checkMusyrifHalaqah($halaqahId = null)
    {
        $user = auth()->user();
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $musyrifHalaqah = Halaqah::where('musyrif_id', $user->id)
                ->orWhere('musyrif_id', $user->id)
                ->first();
            
            if (!$musyrifHalaqah) {
                abort(403, 'Anda belum memiliki halaqah yang ditugaskan.');
            }
            
            if ($halaqahId && $halaqahId != $musyrifHalaqah->id) {
                abort(403, 'Anda hanya dapat mengakses data halaqah Anda sendiri.');
            }
            
            return $musyrifHalaqah;
        }
        return null;
    }

    private function generateDateRange($start, $end)
    {
        $dates = [];
        $current = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        while ($current <= $endDate) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    private function getLaporanDataHarian($santriId, $date)
    {
        $tanggal = $date->format('Y-m-d');
        $PAGI_PARENT = 1;
        $MALAM_PARENT = 3;
        
        $kegiatanPagiAll = Kegiatan::where('parent_id', $PAGI_PARENT)->get();
        $kegiatanMalamAll = Kegiatan::where('parent_id', $MALAM_PARENT)->get();
        
        $dataPagi = [];
        $dataMalam = [];
        
        foreach ($kegiatanPagiAll as $kegiatan) {
            $ceklist = Ceklist::where('santri_id', $santriId)
                ->where('kegiatan_id', $kegiatan->id)
                ->whereDate('tanggal', $tanggal)
                ->first();
            
            $selesai = $ceklist && $ceklist->status == 1;
            
            $dataPagi[] = [
                'nama' => $kegiatan->nama_kegiatan,
                'selesai' => $selesai
            ];
        }
        
        foreach ($kegiatanMalamAll as $kegiatan) {
            $ceklist = Ceklist::where('santri_id', $santriId)
                ->where('kegiatan_id', $kegiatan->id)
                ->whereDate('tanggal', $tanggal)
                ->first();
            
            $selesai = $ceklist && $ceklist->status == 1;
            
            $dataMalam[] = [
                'nama' => $kegiatan->nama_kegiatan,
                'selesai' => $selesai
            ];
        }
        
        $dataKesehatan = Kesehatan::where('santri_id', $santriId)
            ->whereDate('tanggal', $tanggal)
            ->first();
        
        $kesehatanSehat = false;
        $kesehatanSakit = false;
        
        if ($dataKesehatan) {
            $kesehatanSehat = ($dataKesehatan->status == 'sehat');
            $kesehatanSakit = ($dataKesehatan->status == 'sakit' || $dataKesehatan->status == 'rumah sakit' || $dataKesehatan->status == 'rawat jalan');
        }
        
        $dataAbsen = Absen::where('santri_id', $santriId)
            ->whereDate('tanggal', $tanggal)
            ->first();
        
        $absenPagi = null;
        $absenMalam = null;
        
        if ($dataAbsen) {
            $absenMapping = [
                'H' => 'Hadir',
                'I' => 'Izin',
                'A' => 'Alpa',
                'S' => 'Sakit'
            ];
            
            $absenPagi = $dataAbsen->pagi ? ($absenMapping[$dataAbsen->pagi] ?? $dataAbsen->pagi) : null;
            $absenMalam = $dataAbsen->malam ? ($absenMapping[$dataAbsen->malam] ?? $dataAbsen->malam) : null;
        }
        
        return [
            'data_pagi' => $dataPagi,
            'data_malam' => $dataMalam,
            'kesehatan_sehat' => $kesehatanSehat,
            'kesehatan_sakit' => $kesehatanSakit,
            'absen_pagi' => $absenPagi,
            'absen_malam' => $absenMalam,
            'tanggal' => $date,
            'total_selesai' => count(array_filter($dataPagi, function($item) { return $item['selesai']; }))
                             + count(array_filter($dataMalam, function($item) { return $item['selesai']; })),
            'total_kegiatan' => count($dataPagi) + count($dataMalam)
        ];
    }

    private function formatWhatsAppMessageHarian($santri, $halaqah, $date, $laporanData, $additionalMessage = '')
    {
        $hari = $date->translatedFormat('l');
        $tanggal = $date->format('d-m-Y');
        
        // Ambil nama musyrif dari database
        $musyrifName = 'Musyrif Halaqah';
        if (isset($halaqah->musyrif_id) && $halaqah->musyrif_id) {
            $musyrif = User::find($halaqah->musyrif_id);
            if ($musyrif) {
                $musyrifName = $musyrif->name;
            }
        } elseif (isset($halaqah->musrif_id) && $halaqah->musrif_id) {
            // Backup jika kolomnya musrif_id
            $musyrif = User::find($halaqah->musrif_id);
            if ($musyrif) {
                $musyrifName = $musyrif->name;
            }
        }
        
        $message = "Assalamualaikum warohmatullahi wabarokatuh\n";
        $message .= "Ayah Bunda berikut kami sampaikan kegiatan boarding Ananda : " . $santri->name . " pada hari " . $hari . " tanggal " . $tanggal . "\n\n";
        
        $message .= "KONDISI HARI INI\n";
        $message .= "1. Sehat ( " . ($laporanData['kesehatan_sehat'] ? "v" : "-") . " )\n";
        $message .= "2. Sakit ( " . ($laporanData['kesehatan_sakit'] ? "v" : "-") . " )\n";
        
        if ($laporanData['absen_pagi'] || $laporanData['absen_malam']) {
            $message .= "\nABSENSI HARIAN\n";
            if ($laporanData['absen_pagi']) {
                $message .= "Pagi: " . $laporanData['absen_pagi'] . "\n";
            }
            if ($laporanData['absen_malam']) {
                $message .= "Malam: " . $laporanData['absen_malam'] . "\n";
            }
        }
        
        $message .= "\nKEGIATAN PAGI\n";
        $message .= "Kegiatan Pagi\n";
        
        $counter = 1;
        foreach ($laporanData['data_pagi'] as $kegiatan) {
            $checkmark = $kegiatan['selesai'] ? "v" : "-";
            $message .= $counter . ". " . $kegiatan['nama'] . " \t( " . $checkmark . " )\n";
            $counter++;
        }
        
        $message .= "\nKEGIATAN SORE & MALAM\n";
        $message .= "Kegiatan Sore - Malam\n";
        
        $counter = 1;
        foreach ($laporanData['data_malam'] as $kegiatan) {
            $checkmark = $kegiatan['selesai'] ? "v" : "-";
            $message .= $counter . ". " . $kegiatan['nama'] . " \t( " . $checkmark . " )\n";
            $counter++;
        }
        
        $message .= "\n📊 *RINGKASAN:*\n";
        $message .= "✅ Selesai: " . $laporanData['total_selesai'] . " dari " . $laporanData['total_kegiatan'] . " kegiatan\n";
        
        if (!empty($additionalMessage)) {
            $message .= "\n" . $additionalMessage . "\n";
        } else {
            $message .= "\nTeriring doa semoga Allah selalu melindungi Ananda dari segala keburukan dan kemaksiatan serta melembutkan hatinya untuk menerima setiap kebaikan. Aamiin\n";
        }
        
        $message .= "\nWassalamualaikum warohmatullahi wabarokatuh\n\n";
        $message .= "Ttd\n";
        $message .= $musyrifName;
        
        return urlencode($message);
    }

    private function generateWhatsAppUrl($phone, $message)
    {
        return "https://wa.me/{$phone}?text={$message}";
    }

    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        if (empty($cleaned)) {
            return null;
        }
        
        if (str_starts_with($cleaned, '0')) {
            $formatted = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '62')) {
            $formatted = $cleaned;
        } else {
            $formatted = '62' . $cleaned;
        }
        
        if (strlen($formatted) < 10 || strlen($formatted) > 15) {
            return null;
        }
        
        return $formatted;
    }

    /**
     * 🔹 Test PDF
     */
    public function testPdf()
    {
        // Cek authorization - hanya admin dan musyrif
        $this->checkAuthorization(['admin', 'musyrif']);

        $halaqah = Halaqah::first();
        $santri = User::where('halaqah_id', $halaqah->id)->first();
        
        $data = [
            [
                'santri' => $santri->name,
                'kelas'  => $santri->kelas ?? '-',
                'pagi'   => [
                    ['nama' => 'Sholat Subuh', 'status' => 'Hadir'],
                    ['nama' => 'Tahfidz', 'status' => 'Hadir'],
                ],
                'malam'  => [
                    ['nama' => 'Sholat Isya', 'status' => 'Hadir'],
                    ['nama' => 'Murojaah', 'status' => 'Tidak'],
                ],
            ]
        ];
        
        $pdf = Pdf::loadView('laporan_pdf', [
            'halaqah' => $halaqah,
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            'data'    => $data,
        ])->setPaper('A4');
        
        return $pdf->stream('test-laporan.pdf');
    }

    /**
     * 🔹 Cleanup old PDF files
     */
    public function cleanupOldFiles()
    {
        // Cek authorization - hanya admin
        $this->checkAuthorization(['admin']);

        $folder = 'report/';
        $path = public_path($folder);
        
        if (!file_exists($path)) {
            return 'Folder tidak ada';
        }
        
        $files = glob($path . '*.pdf');
        $expiredTime = now()->subHours(24);
        $deletedCount = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $expiredTime->timestamp) {
                unlink($file);
                $deletedCount++;
            }
        }
        
        return 'Deleted ' . $deletedCount . ' old files';
    }

    /**
     * 🔹 Alias untuk sendWhatsAppSelected (backward compatibility)
     */
    public function sendWhatsAppToSelectedOld(Request $request)
    {
        return $this->sendWhatsAppSelected($request);
    }
}