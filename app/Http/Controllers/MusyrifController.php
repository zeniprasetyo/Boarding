<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class MusyrifController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Unauthorized. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        if ($request->ajax()) {
            $data = User::role('musyrif')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('telephone', fn($row) => $row->telephone ?? '-')
                ->addColumn('halaqah', function($row) {
                    // Menampilkan halaqah yang dia pegang
                    $halaqah = $row->halaqah ?? null;
                    if ($halaqah) {
                        return $halaqah->kode . ' - ' . $halaqah->nama_halaqah;
                    }
                    return '-';
                })
                ->addColumn('status', function($row) {
                    return $row->trashed() 
                        ? '<span class="badge bg-danger">Terhapus</span>'
                        : '<span class="badge bg-success">Aktif</span>';
                })
                ->addColumn('action', function ($row) {
                    if ($row->trashed()) {
                        return '
                            <button class="btn btn-sm btn-success restore" data-id="'.$row->id.'" 
                                    title="Restore" data-bs-toggle="tooltip">
                                <i class="fas fa-undo"></i>
                            </button>
                        ';
                    }
                    
                    return '
                        <button class="btn btn-sm btn-warning edit" data-id="'.$row->id.'" 
                                title="Edit" data-bs-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'" 
                                title="Hapus" data-bs-toggle="tooltip">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('musyrif');
    }

    public function deleted(Request $request)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        if ($request->ajax()) {
            $data = User::onlyTrashed()->role('musyrif')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('telephone', fn($row) => $row->telephone ?? '-')
                ->addColumn('halaqah', function($row) {
                    $halaqah = $row->halaqah ?? null;
                    if ($halaqah) {
                        return $halaqah->kode . ' - ' . $halaqah->nama_halaqah;
                    }
                    return '-';
                })
                ->addColumn('deleted_at', function($row) {
                    return $row->deleted_at->format('d-m-Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-success restore" data-id="'.$row->id.'" 
                                title="Restore" data-bs-toggle="tooltip">
                            <i class="fas fa-undo"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengelola data musyrif'
            ], 403);
        }

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $request->id,
            'telephone' => 'nullable|string|max:20',
            'password'  => $request->id ? 'nullable|min:6' : 'required|min:6',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user = User::withTrashed()->updateOrCreate(['id' => $request->id], $data);
        
        // Hanya assign role jika user baru
        if (!$request->id) {
            $user->assignRole('musyrif');
        } else {
            // Pastikan role tetap musyrif saat update
            if (!$user->hasRole('musyrif')) {
                $user->syncRoles(['musyrif']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $request->id ? 'Musyrif berhasil diperbarui.' : 'Musyrif berhasil ditambahkan.'
        ]);
    }

    public function edit($id)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $musyrif = User::withTrashed()->findOrFail($id);
        
        // Pastikan yang diedit adalah musyrif
        if (!$musyrif->hasRole('musyrif')) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau bukan musyrif'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $musyrif
        ]);
    }

    public function destroy($id)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus musyrif'
            ], 403);
        }

        $musyrif = User::findOrFail($id);
        
        // Cek apakah musyrif memiliki halaqah
        if ($musyrif->halaqah()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Musyrif tidak dapat dihapus karena masih memiliki halaqah. Silahkan alihkan halaqah terlebih dahulu.'
            ], 400);
        }
        
        $musyrif->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Musyrif berhasil dihapus sementara.'
        ]);
    }

    public function restore($id)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat memulihkan musyrif'
            ], 403);
        }

        $musyrif = User::onlyTrashed()->findOrFail($id);
        $musyrif->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'Musyrif berhasil dipulihkan.'
        ]);
    }
    
    // Method untuk force delete (hapus permanen)
    public function forceDelete($id)
    {
        // Cek apakah user adalah admin
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus permanen musyrif'
            ], 403);
        }

        $musyrif = User::onlyTrashed()->findOrFail($id);
        
        // Cek apakah musyrif memiliki halaqah
        if ($musyrif->halaqah()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Musyrif tidak dapat dihapus permanen karena masih memiliki halaqah.'
            ], 400);
        }
        
        $musyrif->forceDelete();
        
        return response()->json([
            'success' => true,
            'message' => 'Musyrif berhasil dihapus permanen.'
        ]);
    }
}