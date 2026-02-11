<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kesehatan;
use App\Models\Halaqah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class KesehatanController extends Controller
{
    /**
     * 🔹 Halaman utama kesehatan dengan auto detect device
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
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman kesehatan.');
        }
        
        // Filter tanggal (default hari ini)
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $halaqah_id = $request->halaqah_id;

        // Filter halaqah berdasarkan role
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $halaqah = Halaqah::where('musyrif_id', $user->id)
                ->orderBy('nama_halaqah')
                ->get();
        } else {
            $halaqah = Halaqah::orderBy('nama_halaqah')->get();
        }

        // Filter santri berdasarkan halaqah
        $santri = collect();
        $kesehatanData = [];

        if ($halaqah_id) {
            // Validasi: Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return redirect()->route('kesehatan.index')
                        ->with('error', 'Anda tidak berhak mengakses halaqah ini.');
                }
            }
            
            $santri = User::where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();
            
            // Ambil data kesehatan yang sudah ada untuk tanggal dan santri yang dipilih
            if ($santri->isNotEmpty()) {
                $existingData = Kesehatan::where('tanggal', $tanggal)
                    ->whereIn('santri_id', $santri->pluck('id'))
                    ->get()
                    ->keyBy('santri_id');
                
                foreach ($santri as $s) {
                    $kesehatanData[$s->id] = $existingData->get($s->id);
                }
            }
        }

        return view('kesehatan', [
            'halaqah' => $halaqah,
            'santri' => $santri,
            'tanggal' => $tanggal,
            'kesehatanData' => $kesehatanData,
            'halaqah_id' => $halaqah_id,
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
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman kesehatan.');
        }
        
        // Filter tanggal (default hari ini)
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $halaqah_id = $request->halaqah_id;

        // Filter halaqah berdasarkan role
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $halaqah = Halaqah::where('musyrif_id', $user->id)
                ->orderBy('nama_halaqah')
                ->get();
        } else {
            $halaqah = Halaqah::orderBy('nama_halaqah')->get();
        }

        // Filter santri berdasarkan halaqah
        $santri = collect();
        $kesehatanData = [];

        if ($halaqah_id) {
            // Validasi: Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return redirect()->route('kesehatan.index', ['mode' => 'mobile'])
                        ->with('error', 'Anda tidak berhak mengakses halaqah ini.');
                }
            }
            
            $santri = User::where('halaqah_id', $halaqah_id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();
            
            // Ambil data kesehatan yang sudah ada untuk tanggal dan santri yang dipilih
            if ($santri->isNotEmpty()) {
                $existingData = Kesehatan::where('tanggal', $tanggal)
                    ->whereIn('santri_id', $santri->pluck('id'))
                    ->get()
                    ->keyBy('santri_id');
                
                foreach ($santri as $s) {
                    $kesehatanData[$s->id] = $existingData->get($s->id);
                }
            }
        }

        return view('kesehatan_mobile', [
            'halaqah' => $halaqah,
            'santri' => $santri,
            'tanggal' => $tanggal,
            'kesehatanData' => $kesehatanData,
            'halaqah_id' => $halaqah_id,
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

            // Get existing kesehatan data
            $kesehatanData = Kesehatan::where('tanggal', $tanggal)
                ->whereIn('santri_id', $santri->pluck('id'))
                ->get()
                ->keyBy('santri_id');

            // Format data untuk mobile
            $data = $santri->map(function($santri) use ($kesehatanData) {
                $kesehatan = $kesehatanData->get($santri->id);
                
                return [
                    'id' => $santri->id,
                    'nama' => $santri->name,
                    'status' => $kesehatan ? $kesehatan->status : null,
                    'keterangan' => $kesehatan ? $kesehatan->keterangan : null,
                    'kode' => $kesehatan ? $kesehatan->kode : null,
                    'updated_at' => $kesehatan ? $kesehatan->updated_at : null
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
            \Log::error('Kesehatan Mobile Data Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    // App\Http\Controllers\KesehatanController.php

/**
 * 🔹 Update batch data kesehatan (untuk mobile)
 */
    public function updateBatch(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek apakah user berhak mengakses
            if (!$user->hasAnyRole(['admin', 'musyrif'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Hanya admin dan musyrif yang dapat menyimpan data kesehatan.'
                ], 403);
            }

            $tanggal = $request->tanggal;
            $halaqah_id = $request->halaqah_id;
            $data = $request->data ?? [];

            // Validasi request
            $request->validate([
                'tanggal' => 'required|date',
                'halaqah_id' => 'required|exists:halaqah,id',
                'data' => 'required|array',
            ]);
            
            // Validasi: Cek apakah musyrif berhak menyimpan data untuk halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
                $validHalaqah = Halaqah::where('id', $halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->exists();
                    
                if (!$validHalaqah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak menyimpan data kesehatan untuk halaqah ini.'
                    ], 403);
                }
            }
            
            $successCount = 0;
            $errorMessages = [];
            
            foreach ($data as $santri_id => $val) {
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
                    if (!empty($val['status'])) {
                        // Generate kode unik berdasarkan tanggal dan santri_id
                        $kode = 'KST-' . str_replace('-', '', $tanggal) . '-' . $santri_id . '-' . Str::random(4);
                        
                        Kesehatan::updateOrCreate(
                            [
                                'santri_id' => $santri_id,
                                'tanggal' => $tanggal
                            ],
                            [
                                'kode' => $kode,
                                'status' => $val['status'],
                                'keterangan' => $val['keterangan'] ?? null,
                                'updated_by' => $user->id
                            ]
                        );
                        $successCount++;
                    } else {
                        // Hapus data jika status kosong
                        Kesehatan::where('santri_id', $santri_id)
                            ->where('tanggal', $tanggal)
                            ->delete();
                    }
                } catch (\Exception $e) {
                    \Log::error('Error saving kesehatan data for santri ' . $santri_id . ': ' . $e->getMessage());
                    $errorMessages[] = "Gagal menyimpan data kesehatan untuk santri ID $santri_id";
                }
            }
            
            $message = "Data kesehatan berhasil disimpan untuk $successCount santri.";
            if (!empty($errorMessages)) {
                $message .= " Terdapat " . count($errorMessages) . " kesalahan.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'errors' => $errorMessages,
                'count' => $successCount
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Update Batch Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * 🔹 Simpan data kesehatan massal (desktop & mobile)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya admin dan musyrif yang dapat menyimpan data kesehatan.'
            ], 403);
        }

        $tanggal = $request->tanggal;
        $halaqah_id = $request->halaqah_id;
        $data = $request->kesehatan ?? [];

        // Validasi request
        $request->validate([
            'tanggal' => 'required|date',
            'halaqah_id' => 'required|exists:halaqah,id',
            'kesehatan' => 'required|array',
        ]);
        
        // Validasi: Cek apakah musyrif berhak menyimpan data untuk halaqah ini
        if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
            $validHalaqah = Halaqah::where('id', $halaqah_id)
                ->where('musyrif_id', $user->id)
                ->exists();
                
            if (!$validHalaqah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak menyimpan data kesehatan untuk halaqah ini.'
                ], 403);
            }
        }
        
        $successCount = 0;
        $errorMessages = [];
        
        foreach ($data as $santri_id => $val) {
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
                if (!empty($val['status'])) {
                    // Generate kode unik berdasarkan tanggal dan santri_id
                    $kode = 'KST-' . str_replace('-', '', $tanggal) . '-' . $santri_id . '-' . Str::random(4);
                    
                    Kesehatan::updateOrCreate(
                        [
                            'santri_id' => $santri_id,
                            'tanggal' => $tanggal
                        ],
                        [
                            'kode' => $kode,
                            'status' => $val['status'],
                            'keterangan' => $val['keterangan'] ?? null,
                            'updated_by' => $user->id
                        ]
                    );
                    $successCount++;
                } else {
                    // Hapus data jika status kosong
                    Kesehatan::where('santri_id', $santri_id)
                        ->where('tanggal', $tanggal)
                        ->delete();
                }
            } catch (\Exception $e) {
                \Log::error('Error saving kesehatan data for santri ' . $santri_id . ': ' . $e->getMessage());
                $errorMessages[] = "Gagal menyimpan data kesehatan untuk santri ID $santri_id";
            }
        }
        
        $message = "Data kesehatan berhasil disimpan untuk $successCount santri.";
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
     * 🔹 Get kesehatan data untuk API/AJAX (desktop)
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
        
        $kesehatanData = Kesehatan::where('tanggal', $tanggal)
            ->whereIn('santri_id', $santri->pluck('id'))
            ->get()
            ->keyBy('santri_id');
        
        $data = $santri->map(function($santri) use ($kesehatanData) {
            $kesehatan = $kesehatanData->get($santri->id);
            
            return [
                'id' => $santri->id,
                'nama' => $santri->name,
                'status' => $kesehatan ? $kesehatan->status : null,
                'keterangan' => $kesehatan ? $kesehatan->keterangan : null,
                'kode' => $kesehatan ? $kesehatan->kode : null,
                'updated_at' => $kesehatan ? $kesehatan->updated_at : null
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
     * 🔹 Get sub-kegiatan untuk kesehatan (jika ada relasi dengan kegiatan)
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