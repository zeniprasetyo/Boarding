<?php

namespace App\Http\Controllers;

use App\Models\CeklistKegiatan;
use App\Models\User;
use App\Models\Halaqah;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Jenssegers\Agent\Agent;

class CeklistKegiatanController extends Controller
{
    /**
     * 🔹 Halaman utama ceklist kegiatan
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman ini.');
        }
        
        $agent = new Agent();

        // Manual override
        if ($request->mode == 'mobile') {
            return $this->viewMobile($user);
        }

        if ($request->mode == 'desktop') {
            return $this->viewDesktop($user);
        }

        // Auto detect device
        if ($agent->isMobile() || $agent->isTablet()) {
            return $this->viewMobile($user);
        }

        return $this->viewDesktop($user);
    }


    /**
     * 🔹 View Desktop
     */
    private function viewDesktop($user)
    {
        // Tentukan halaqah yang bisa diakses
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            // Musyrif hanya bisa melihat halaqah yang dia pegang
            $halaqah = Halaqah::where('musyrif_id', $user->id)->get();
        } else {
            // Admin bisa melihat semua halaqah
            $halaqah = Halaqah::all();
        }

        $kegiatan_utama = Kegiatan::whereNull('parent_id')
            ->orderBy('nama_kegiatan')
            ->get();

        return view('ceklist', [
            'halaqah' => $halaqah,
            'kegiatan' => $kegiatan_utama,
            'user_role' => $user->getRoleNames()->first()
        ]);
    }


    /**
     * 🔹 View Mobile
     */
    private function viewMobile($user)
    {
        // Tentukan halaqah yang bisa diakses
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            // Musyrif hanya bisa melihat halaqah yang dia pegang
            $halaqah = Halaqah::where('musyrif_id', $user->id)->get();
        } else {
            // Admin bisa melihat semua halaqah
            $halaqah = Halaqah::all();
        }

        $kegiatan_utama = Kegiatan::whereNull('parent_id')
            ->orderBy('nama_kegiatan')
            ->get();

        return view('ceklist_mobile', [
            'halaqah' => $halaqah,
            'kegiatan' => $kegiatan_utama,
            'user_role' => $user->getRoleNames()->first()
        ]);
    }


    /**
     * 🔹 Ambil daftar santri (user) berdasarkan halaqah
     */
    public function getUserByHalaqah($id)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        // Cek apakah musyrif berhak mengakses halaqah ini
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $halaqah = Halaqah::where('id', $id)->where('musyrif_id', $user->id)->first();
            if (!$halaqah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak mengakses halaqah ini'
                ], 403);
            }
        }

        $users = User::where('halaqah_id', $id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'santri');
            })
            ->get(['id', 'name']);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }


    /**
     * 🔹 Ambil sub-kegiatan berdasarkan kegiatan utama
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

        $subs = Kegiatan::where('parent_id', $request->kegiatan_id)
            ->orderBy('nama_kegiatan')
            ->get(['id', 'nama_kegiatan']);

        return response()->json([
            'success' => true,
            'data' => $subs
        ]);
    }


    /**
     * 🔹 DataTables (desktop)
     */
    public function getDataNested(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Cek apakah user berhak mengakses
            if (!$user->hasAnyRole(['admin', 'musyrif'])) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 403);
            }

            $halaqah_id = $request->halaqah_id;
            $root_id    = $request->kegiatan_id;
            $tanggal    = $request->tanggal ?? now()->toDateString();

            // Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
                $halaqah = Halaqah::where('id', $halaqah_id)->where('musyrif_id', $user->id)->first();
                if (!$halaqah) {
                    return response()->json([
                        'error' => 'Anda tidak berhak mengakses halaqah ini'
                    ], 403);
                }
            }

            $subKegiatan = Kegiatan::where('parent_id', $root_id)->get();
            $users = User::when($halaqah_id, fn($q) => $q->where('halaqah_id', $halaqah_id))
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->get();

            $dataTable = DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('nama_user', fn($row) => $row->name);

            foreach ($subKegiatan as $k) {
                $dataTable->addColumn('keg_' . $k->id, function ($row) use ($k, $tanggal) {
                    $cek = CeklistKegiatan::where('santri_id', $row->id)
                        ->where('kegiatan_id', $k->id)
                        ->whereDate('tanggal', $tanggal)
                        ->first();

                    $checked = $cek && $cek->status ? 'checked' : '';
                    return '<input type="checkbox" class="form-check-input ceklist-checkbox"
                            data-user="'.$row->id.'"
                            data-kegiatan="'.$k->id.'" '.$checked.'>';
                });
            }

            $dataTable->rawColumns(
                collect($subKegiatan)->map(fn($k) => 'keg_' . $k->id)->toArray()
            );

            return $dataTable->make(true);

        } catch (\Throwable $e) {
            \Log::error('Ceklist getDataNested error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * 🔹 Data khusus tampilan MOBILE
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
            $root_id    = $request->kegiatan_id;
            $tanggal    = $request->tanggal ?? now()->toDateString();

            // Cek apakah musyrif berhak mengakses halaqah ini
            if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah_id) {
                $halaqah = Halaqah::where('id', $halaqah_id)->where('musyrif_id', $user->id)->first();
                if (!$halaqah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak mengakses halaqah ini'
                    ], 403);
                }
            }

            $subKegiatan = Kegiatan::where('parent_id', $root_id)->get();

            $users = User::when($halaqah_id, fn($q) => $q->where('halaqah_id', $halaqah_id))
                ->whereHas('roles', function($q) {
                    $q->where('name', 'santri');
                })
                ->orderBy('name')
                ->get();

            $result = [];

            foreach ($users as $u) {
                $row = [
                    'user_id' => $u->id,
                    'nama_user' => $u->name,
                ];

                foreach ($subKegiatan as $k) {
                    $cek = CeklistKegiatan::where('santri_id', $u->id)
                        ->where('kegiatan_id', $k->id)
                        ->whereDate('tanggal', $tanggal)
                        ->first();

                    $row['keg_' . $k->id] = $cek && $cek->status == 1 ? 1 : 0;
                }

                $result[] = $row;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Throwable $e) {
            \Log::error("Ceklist dataMobile Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * 🔹 Simpan checklist massal (desktop & mobile)
     */
    public function store(Request $request)
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

            $tanggal = $request->tanggal;
            $statuses = $request->status ?? [];

            foreach ($statuses as $user_id => $kegiatans) {
                foreach ($kegiatans as $kegiatan_id => $isChecked) {
                    // Validasi: Cek apakah musyrif berhak menginput untuk santri ini
                    if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
                        $santri = User::find($user_id);
                        if ($santri && $santri->halaqah_id) {
                            $halaqah = Halaqah::where('id', $santri->halaqah_id)
                                ->where('musyrif_id', $user->id)
                                ->first();
                            if (!$halaqah) {
                                continue; // Skip jika tidak berhak
                            }
                        }
                    }
                    
                    CeklistKegiatan::updateOrCreate(
                        [
                            'santri_id' => $user_id,
                            'kegiatan_id' => $kegiatan_id,
                            'tanggal' => $tanggal,
                        ],
                        [
                            'status' => $isChecked ? 1 : 0,
                            'kode' => (string) Str::uuid(),
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Checklist berhasil disimpan.'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * 🔹 Update satu checklist
     */
    public function updateStatus(Request $request)
    {
        $user = auth()->user();
        
        // Cek apakah user berhak mengakses
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'kegiatan_id' => 'required|exists:kegiatan,id',
            'tanggal' => 'required|date',
        ]);

        // Validasi: Cek apakah musyrif berhak mengupdate untuk santri ini
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $santri = User::find($request->user_id);
            if ($santri && $santri->halaqah_id) {
                $halaqah = Halaqah::where('id', $santri->halaqah_id)
                    ->where('musyrif_id', $user->id)
                    ->first();
                if (!$halaqah) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berhak mengupdate checklist santri ini'
                    ], 403);
                }
            }
        }

        CeklistKegiatan::updateOrCreate(
            [
                'santri_id' => $request->user_id,
                'kegiatan_id' => $request->kegiatan_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'status' => $request->status ? 1 : 0,
                'kode' => (string) Str::uuid(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }
}