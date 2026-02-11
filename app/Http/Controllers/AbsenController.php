<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absen;
use App\Models\Halaqah;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class AbsenController extends Controller
{
    /**
     * 🔹 Halaman utama absen dengan auto detect device
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
        // Cek apakah user berhak mengakses
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman absensi.');
        }
        
        $halaqah_id = $request->halaqah_id;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        // Filter halaqah berdasarkan role
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $halaqah = Halaqah::where('musyrif_id', $user->id)
                ->orderBy('nama_halaqah')
                ->get();
        } else {
            $halaqah = Halaqah::orderBy('nama_halaqah')->get();
        }

        $santri = collect();
        $absenData = [];

        if ($halaqah_id) {
            // Validasi: Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return redirect()->route('absen.index')
                        ->with('error', 'Anda tidak berhak mengakses halaqah ini.');
                }
            }
            
            $santri = User::where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();
            
            // Ambil data absen yang sudah ada
            if ($santri->isNotEmpty()) {
                $absenData = Absen::where('tanggal', $tanggal)
                    ->whereIn('santri_id', $santri->pluck('id'))
                    ->get()
                    ->keyBy('santri_id');
            }
        }

        return view('absen', [
            'halaqah' => $halaqah,
            'santri' => $santri,
            'tanggal' => $tanggal,
            'halaqah_id' => $halaqah_id,
            'absenData' => $absenData,
            'user' => $user,
            'user_role' => $user->getRoleNames()->first()
        ]);
    }

    /**
     * 🔹 View Mobile
     */
    private function viewMobile($request)
    {
        // Cek apakah user berhak mengakses
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman absensi.');
        }
        
        $halaqah_id = $request->halaqah_id;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        // Filter halaqah berdasarkan role
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $halaqah = Halaqah::where('musyrif_id', $user->id)
                ->orderBy('nama_halaqah')
                ->get();
        } else {
            $halaqah = Halaqah::orderBy('nama_halaqah')->get();
        }

        $santri = collect();
        $absenData = [];

        if ($halaqah_id) {
            // Validasi: Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return redirect()->route('absen.index', ['mode' => 'mobile'])
                        ->with('error', 'Anda tidak berhak mengakses halaqah ini.');
                }
            }
            
            $santri = User::where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();
            
            // Ambil data absen yang sudah ada
            if ($santri->isNotEmpty()) {
                $absenData = Absen::where('tanggal', $tanggal)
                    ->whereIn('santri_id', $santri->pluck('id'))
                    ->get()
                    ->keyBy('santri_id');
            }
        }

        return view('absen_mobile', [
            'halaqah' => $halaqah,
            'santri' => $santri,
            'tanggal' => $tanggal,
            'halaqah_id' => $halaqah_id,
            'absenData' => $absenData,
            'user' => $user,
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
            
            // Cek apakah user berhak mengakses
            if (!$user->hasAnyRole(['admin', 'musyrif'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $halaqah_id = $request->halaqah_id;
            $tanggal = $request->tanggal ?? date('Y-m-d');

            // Validasi input
            if (!$halaqah_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap pilih halaqah terlebih dahulu'
                ], 400);
            }

            // Cek hak akses halaqah
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak mengakses halaqah ini'
                    ], 403);
                }
            }

            // Get santri list
            $santri = User::where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();

            // Get existing absen data
            $absenData = Absen::where('tanggal', $tanggal)
                ->whereIn('santri_id', $santri->pluck('id'))
                ->get()
                ->keyBy('santri_id');

            // Format data untuk mobile
            $data = $santri->map(function($santri) use ($absenData) {
                $absen = $absenData->get($santri->id);
                
                return [
                    'id' => $santri->id,
                    'nama' => $santri->name,
                    'pagi' => $absen ? $absen->pagi : null,
                    'malam' => $absen ? $absen->malam : null,
                    'catatan' => $absen ? $absen->catatan : null,
                    'kode' => $absen ? $absen->kode : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $santri->count(),
                'tanggal' => $tanggal,
                'halaqah_id' => $halaqah_id
            ]);

        } catch (\Exception $e) {
            \Log::error('Absen Mobile Data Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔹 Simpan absen massal (desktop & mobile)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya admin dan musyrif yang dapat menyimpan absensi.'
            ], 403);
        }

        $tanggal = $request->tanggal;
        $halaqah_id = $request->halaqah_id;
        
        // Validasi: Cek apakah musyrif berhak menyimpan absensi untuk halaqah ini
        if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
            $validHalaqah = Halaqah::where('id', $halaqah_id)
                ->where('musyrif_id', $user->id)
                ->exists();
                
            if (!$validHalaqah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak menyimpan absensi untuk halaqah ini.'
                ], 403);
            }
        }
        
        // Validasi request
        $request->validate([
            'tanggal' => 'required|date',
            'halaqah_id' => 'required|exists:halaqah,id',
            'absen' => 'required|array',
        ]);
        
        $successCount = 0;
        $errorMessages = [];
        
        foreach ($request->absen as $santri_id => $data) {
            // Validasi: Cek apakah santri ada di halaqah yang dipilih
            $santri = User::where('id', $santri_id)
                ->where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->first();
                
            if (!$santri) {
                $errorMessages[] = "Santri dengan ID $santri_id tidak ditemukan atau tidak berada di halaqah yang dipilih.";
                continue;
            }
            
            try {
                // Generate kode unik
                $kode = 'ABS-' . str_replace('-', '', $tanggal) . '-' . $santri_id . '-' . Str::random(4);
                
                Absen::updateOrCreate(
                    [
                        'santri_id' => $santri_id,
                        'tanggal' => $tanggal
                    ],
                    [
                        'kode' => $kode,
                        'pagi' => $data['pagi'] ?? null,
                        'malam' => $data['malam'] ?? null,
                        'catatan' => $data['catatan'] ?? null,
                        'updated_by' => $user->id
                    ]
                );
                
                $successCount++;
            } catch (\Exception $e) {
                \Log::error('Error saving absensi for santri ' . $santri_id . ': ' . $e->getMessage());
                $errorMessages[] = "Gagal menyimpan absensi untuk santri ID $santri_id";
            }
        }
        
        $message = "Absensi berhasil disimpan untuk $successCount santri.";
        if (!empty($errorMessages)) {
            $message .= " Terdapat " . count($errorMessages) . " kesalahan.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'errors' => $errorMessages,
            'count' => $successCount
        ]);
    }
    
    /**
     * 🔹 Get absensi data untuk API/AJAX (desktop)
     */
    public function dataNested(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $halaqah_id = $request->halaqah_id;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        // Validasi: Cek apakah musyrif berhak mengakses halaqah ini
        if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
            $validHalaqah = Halaqah::where('id', $halaqah_id)
                ->where('musyrif_id', $user->id)
                ->exists();
                
            if (!$validHalaqah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak mengakses data halaqah ini'
                ], 403);
            }
        }
        
        $santri = User::where('halaqah_id', $halaqah_id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'santri');
            })
            ->orderBy('name')
            ->get();
        
        $absenData = Absen::where('tanggal', $tanggal)
            ->whereIn('santri_id', $santri->pluck('id'))
            ->get()
            ->keyBy('santri_id');
        
        $data = $santri->map(function($santri) use ($absenData) {
            $absen = $absenData->get($santri->id);
            
            return [
                'id' => $santri->id,
                'nama' => $santri->name,
                'absen_pagi' => $absen ? $absen->pagi : null,
                'absen_malam' => $absen ? $absen->malam : null,
                'catatan' => $absen ? $absen->catatan : null,
                'kode' => $absen ? $absen->kode : null
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $santri->count(),
            'tanggal' => $tanggal
        ]);
    }
    
    /**
     * 🔹 Get sub-kegiatan untuk absensi (jika ada relasi dengan kegiatan)
     */
    public function subKegiatan(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        // Return empty array jika tidak ada sub-kegiatan khusus
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}