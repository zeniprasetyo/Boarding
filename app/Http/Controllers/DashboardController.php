<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\User;
use App\Models\Halaqah;
use App\Models\Kesehatan;
use App\Models\CeklistKegiatan;
use App\Models\Absen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Jenssegers\Agent\Agent; // Tambahkan ini
use Illuminate\Support\Facades\Log; // Tambahkan untuk logging
use Illuminate\Support\Facades\DB; // Tambahkan untuk debugging

class DashboardController extends Controller
{
    /**
     * 🔹 Halaman utama dashboard dengan auto detect device
     */
    public function index(Request $request)
    {
        $agent = new Agent();
        
        // Manual override
        if ($request->mode == 'mobile') {
            return $this->viewMobile();
        }

        if ($request->mode == 'desktop') {
            return view('dashboard');
        }

        // Auto detect device
        if ($agent->isMobile() || $agent->isTablet()) {
            return $this->viewMobile();
        }

        return view('dashboard');
    }

    /**
     * 🔹 View Mobile Dashboard
     */
    private function viewMobile()
    {
        $user = auth()->user();
        
        return view('dashboard_mobile', [
            'user' => $user,
            'user_role' => $user->getRoleNames()->first() ?? 'Pengguna'
        ]);
    }

    /**
     * 🔹 Data statistik untuk mobile dashboard
     */
    public function mobileStats()
    {
        try {
            Log::info('DashboardController::mobileStats dipanggil');
            
            $totalSantri = Santri::count();
            $totalMusyrif = User::role('musyrif')->count();
            $totalHalaqah = Halaqah::count();
            
            // Hitung kegiatan hari ini
            $totalKegiatan = 0;
            if (class_exists(CeklistKegiatan::class)) {
                $totalKegiatan = CeklistKegiatan::whereDate('created_at', Carbon::today())
                    ->count();
            }

            Log::info('Statistik Mobile:', [
                'santri' => $totalSantri,
                'musyrif' => $totalMusyrif,
                'halaqah' => $totalHalaqah,
                'kegiatan' => $totalKegiatan
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'santri' => $totalSantri,
                    'musyrif' => $totalMusyrif,
                    'halaqah' => $totalHalaqah,
                    'kegiatan' => $totalKegiatan,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('DashboardController::mobileStats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [
                    'santri' => 0,
                    'musyrif' => 0,
                    'halaqah' => 0,
                    'kegiatan' => 0,
                ]
            ], 500);
        }
    }

    /**
     * 🔹 Data aktivitas untuk mobile dashboard
     */
    public function mobileActivities()
    {
        try {
            Log::info('DashboardController::mobileActivities dipanggil');
            
            $activities = collect();
            
            // 1. Ceklist kegiatan hari ini
            if (class_exists(CeklistKegiatan::class)) {
                $ceklistToday = CeklistKegiatan::with(['santri', 'kegiatan'])
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'title' => 'Ceklist Kegiatan',
                            'description' => ($item->santri->name ?? $item->santri->nama_lengkap ?? 'Santri') . ' - ' . ($item->kegiatan->nama ?? $item->kegiatan->nama_kegiatan ?? 'Kegiatan'),
                            'time' => $item->created_at->format('H:i'),
                            'type' => 'ceklist'
                        ];
                    });
                
                $activities = $activities->merge($ceklistToday);
            }
            
            // 2. Absensi hari ini
            if (class_exists(Absen::class)) {
                $absenToday = Absen::with(['santri'])
                    ->whereDate('tanggal', Carbon::today())
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'title' => 'Absensi',
                            'description' => ($item->santri->name ?? $item->santri->nama_lengkap ?? 'Santri') . ' - ' . ($item->status ?? 'Hadir'),
                            'time' => Carbon::parse($item->created_at)->format('H:i'),
                            'type' => 'absensi'
                        ];
                    });
                
                $activities = $activities->merge($absenToday);
            }
            
            // 3. Kesehatan hari ini
            if (class_exists(Kesehatan::class)) {
                $kesehatanToday = Kesehatan::with(['santri'])
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'title' => 'Kesehatan',
                            'description' => ($item->santri->name ?? $item->santri->nama_lengkap ?? 'Santri') . ' - ' . ($item->status ?? 'Sehat'),
                            'time' => $item->created_at->format('H:i'),
                            'type' => 'kesehatan'
                        ];
                    });
                
                $activities = $activities->merge($kesehatanToday);
            }
            
            // Jika tidak ada aktivitas
            if ($activities->isEmpty()) {
                $activities = collect([
                    [
                        'title' => 'Dashboard',
                        'description' => 'Sistem berjalan normal',
                        'time' => Carbon::now()->format('H:i'),
                        'type' => 'system'
                    ]
                ]);
            }
            
            // Urutkan berdasarkan waktu terbaru
            $activities = $activities->sortByDesc('time')->values();

            Log::info('Aktivitas Mobile ditemukan: ' . $activities->count() . ' items');

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('DashboardController::mobileActivities error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // METHOD YANG SUDAH ADA (tetap pertahankan)
    public function statistik()
    {
        try {
            Log::info('DashboardController::statistik dipanggil');
            
            $totalSantri = Santri::count();
            $totalMusyrif = User::role('musyrif')->count();
            $totalHalaqah = Halaqah::count();
            
            $totalPerawatan = 0;
            if (class_exists(Kesehatan::class)) {
                try {
                    $totalPerawatan = Kesehatan::whereDate('created_at', Carbon::today())
                        ->where('status', 'Dalam Perawatan')
                        ->count();
                } catch (\Exception $e) {
                    Log::warning('Gagal menghitung perawatan: ' . $e->getMessage());
                    $totalPerawatan = 0;
                }
            }

            Log::info('Statistik Desktop:', [
                'santri' => $totalSantri,
                'musyrif' => $totalMusyrif,
                'halaqah' => $totalHalaqah,
                'perawatan' => $totalPerawatan
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_santri' => $totalSantri,
                    'total_musyrif' => $totalMusyrif,
                    'total_halaqah' => $totalHalaqah,
                    'total_perawatan' => $totalPerawatan,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('DashboardController::statistik error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [
                    'total_santri' => 0,
                    'total_musyrif' => 0,
                    'total_halaqah' => 0,
                    'total_perawatan' => 0,
                ]
            ], 500);
        }
    }

    public function aktivitas()
    {
        try {
            Log::info('DashboardController::aktivitas dipanggil');
            
            $aktivitas = collect();
            
            // Cek apakah model-model ada dan bisa diakses
            $models = [
                'CeklistKegiatan' => class_exists(CeklistKegiatan::class),
                'Absen' => class_exists(Absen::class),
                'Kesehatan' => class_exists(Kesehatan::class)
            ];
            
            Log::info('Model check:', $models);
            
            // 1. Ceklist kegiatan hari ini
            if (class_exists(CeklistKegiatan::class)) {
                try {
                    $ceklistHariIni = CeklistKegiatan::with(['santri', 'kegiatan'])
                        ->whereDate('created_at', Carbon::today())
                        ->latest()
                        ->limit(10)
                        ->get()
                        ->map(function($item) {
                            // Cek nama field yang benar
                            $namaSantri = $item->santri->nama_lengkap ?? 
                                         $item->santri->name ?? 
                                         'Santri';
                            $namaKegiatan = $item->kegiatan->nama_kegiatan ?? 
                                           $item->kegiatan->nama ?? 
                                           'Kegiatan';
                            
                            return [
                                'waktu' => $item->created_at->format('H:i'),
                                'jenis' => 'Ceklist Kegiatan',
                                'detail' => $namaSantri . ' - ' . $namaKegiatan,
                            ];
                        });
                    
                    $aktivitas = $aktivitas->merge($ceklistHariIni);
                    Log::info('Ceklist ditemukan: ' . $ceklistHariIni->count() . ' items');
                } catch (\Exception $e) {
                    Log::warning('Error ceklist: ' . $e->getMessage());
                }
            }
            
            // 2. Absensi hari ini
            if (class_exists(Absen::class)) {
                try {
                    $absenHariIni = Absen::with(['santri'])
                        ->whereDate('tanggal', Carbon::today())
                        ->where('status', 'hadir')
                        ->latest()
                        ->limit(5)
                        ->get()
                        ->map(function($item) {
                            $namaSantri = $item->santri->nama_lengkap ?? 
                                         $item->santri->name ?? 
                                         'Santri';
                            
                            return [
                                'waktu' => Carbon::parse($item->created_at ?? now())->format('H:i'),
                                'jenis' => 'Absensi',
                                'detail' => $namaSantri . ' hadir',
                            ];
                        });
                    
                    $aktivitas = $aktivitas->merge($absenHariIni);
                    Log::info('Absen ditemukan: ' . $absenHariIni->count() . ' items');
                } catch (\Exception $e) {
                    Log::warning('Error absen: ' . $e->getMessage());
                }
            }
            
            // 3. Kesehatan hari ini
            if (class_exists(Kesehatan::class)) {
                try {
                    $kesehatanHariIni = Kesehatan::with(['santri'])
                        ->whereDate('created_at', Carbon::today())
                        ->latest()
                        ->limit(5)
                        ->get()
                        ->map(function($item) {
                            $namaSantri = $item->santri->nama_lengkap ?? 
                                         $item->santri->name ?? 
                                         'Santri';
                            
                            return [
                                'waktu' => $item->created_at->format('H:i'),
                                'jenis' => 'Kesehatan',
                                'detail' => $namaSantri . ' - ' . ($item->keluhan ?? $item->status ?? 'Check-up'),
                            ];
                        });
                    
                    $aktivitas = $aktivitas->merge($kesehatanHariIni);
                    Log::info('Kesehatan ditemukan: ' . $kesehatanHariIni->count() . ' items');
                } catch (\Exception $e) {
                    Log::warning('Error kesehatan: ' . $e->getMessage());
                }
            }
            
            // Jika tidak ada aktivitas
            if ($aktivitas->isEmpty()) {
                $aktivitas = collect([
                    [
                        'waktu' => Carbon::now()->format('H:i'),
                        'jenis' => 'Sistem',
                        'detail' => 'Belum ada aktivitas hari ini',
                    ]
                ]);
                Log::info('Tidak ada aktivitas ditemukan');
            }
            
            // Urutkan berdasarkan waktu terbaru
            $aktivitas = $aktivitas->sortByDesc(function($item) {
                return $item['waktu'];
            })->values()->take(10);

            Log::info('Total aktivitas: ' . $aktivitas->count() . ' items');

            return response()->json([
                'success' => true,
                'data' => $aktivitas
            ]);
            
        } catch (\Exception $e) {
            Log::error('DashboardController::aktivitas error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [
                    [
                        'waktu' => Carbon::now()->format('H:i'),
                        'jenis' => 'Error',
                        'detail' => 'Gagal memuat aktivitas',
                    ]
                ]
            ], 500);
        }
    }

    public function notifikasi()
    {
        try {
            Log::info('DashboardController::notifikasi dipanggil');
            
            $notifikasi = [];
            
            $totalSantri = Santri::count();
            
            if ($totalSantri > 0) {
                $jamSekarang = Carbon::now()->format('H');
                
                // Notifikasi ceklist pagi
                if ($jamSekarang >= 10 && class_exists(CeklistKegiatan::class)) {
                    try {
                        $sudahCeklist = CeklistKegiatan::whereDate('created_at', Carbon::today())
                            ->distinct('santri_id')
                            ->count();
                        
                        $belumCeklist = $totalSantri - $sudahCeklist;
                        
                        if ($belumCeklist > 0) {
                            $notifikasi[] = [
                                'judul' => 'Peringatan',
                                'pesan' => $belumCeklist . ' santri belum ceklist kegiatan pagi',
                                'tipe' => 'warning',
                                'icon' => 'exclamation-triangle',
                                'waktu' => 'Baru saja',
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error notifikasi ceklist: ' . $e->getMessage());
                    }
                }
                
                // Notifikasi kesehatan
                if (class_exists(Kesehatan::class)) {
                    try {
                        $perawatan = Kesehatan::where('status', 'Dalam Perawatan')
                            ->whereDate('created_at', Carbon::today())
                            ->count();
                        
                        if ($perawatan > 0) {
                            $notifikasi[] = [
                                'judul' => 'Kesehatan',
                                'pesan' => $perawatan . ' santri dalam perawatan',
                                'tipe' => 'danger',
                                'icon' => 'heartbeat',
                                'waktu' => 'Hari ini',
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error notifikasi kesehatan: ' . $e->getMessage());
                    }
                }
                
                // Notifikasi laporan sore
                if ($jamSekarang >= 15) {
                    $notifikasi[] = [
                        'judul' => 'Informasi',
                        'pesan' => 'Laporan harian sudah bisa diakses',
                        'tipe' => 'info',
                        'icon' => 'info-circle',
                        'waktu' => 'Sore ini',
                    ];
                }
            }
            
            if (empty($notifikasi)) {
                $notifikasi[] = [
                    'judul' => 'Semua Baik',
                    'pesan' => 'Sistem berjalan normal',
                    'tipe' => 'success',
                    'icon' => 'check-circle',
                    'waktu' => 'Hari ini',
                ];
            }

            Log::info('Notifikasi ditemukan: ' . count($notifikasi) . ' items');

            return response()->json([
                'success' => true,
                'data' => $notifikasi
            ]);
            
        } catch (\Exception $e) {
            Log::error('DashboardController::notifikasi error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [
                    [
                        'judul' => 'System Error',
                        'pesan' => 'Gagal memuat notifikasi',
                        'tipe' => 'danger',
                        'icon' => 'exclamation-circle',
                        'waktu' => 'Sekarang',
                    ]
                ]
            ], 500);
        }
    }

    /**
     * 🔹 Method debug untuk testing
     */
    public function debug()
    {
        try {
            Log::info('DashboardController::debug dipanggil');
            
            $debugInfo = [
                'auth_user' => auth()->check() ? auth()->user()->id : 'Not authenticated',
                'user_name' => auth()->check() ? auth()->user()->name : 'N/A',
                'user_role' => auth()->user()->getRoleNames()->first() ?? 'No role',
                'current_time' => Carbon::now()->toDateTimeString(),
                'models' => [
                    'Santri' => [
                        'exists' => class_exists(Santri::class),
                        'count' => class_exists(Santri::class) ? Santri::count() : 0
                    ],
                    'User' => [
                        'exists' => class_exists(User::class),
                        'count' => class_exists(User::class) ? User::count() : 0
                    ],
                    'Halaqah' => [
                        'exists' => class_exists(Halaqah::class),
                        'count' => class_exists(Halaqah::class) ? Halaqah::count() : 0
                    ],
                    'Kesehatan' => [
                        'exists' => class_exists(Kesehatan::class),
                        'count' => class_exists(Kesehatan::class) ? Kesehatan::count() : 0
                    ],
                    'CeklistKegiatan' => [
                        'exists' => class_exists(CeklistKegiatan::class),
                        'count' => class_exists(CeklistKegiatan::class) ? CeklistKegiatan::count() : 0
                    ],
                    'Absen' => [
                        'exists' => class_exists(Absen::class),
                        'count' => class_exists(Absen::class) ? Absen::count() : 0
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $debugInfo
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}