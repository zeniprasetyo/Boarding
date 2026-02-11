<?php

namespace App\Http\Controllers;

use App\Models\Halaqah;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HalaqahController extends Controller
{
    // Hapus constructor karena method middleware() tidak tersedia di Laravel 12
    
    public function index()
    {
        // Middleware auth sudah dihandle di routes
        // Cek apakah user berhak mengakses
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            abort(403, 'Unauthorized. Hanya admin dan musyrif yang dapat mengakses halaman ini.');
        }
        
        // Tambahan pengecekan untuk memastikan hanya musyrif yang terkait atau admin
        if ($user->hasRole('musyrif')) {
            // Musyrif hanya bisa melihat halaqah yang dia pegang
            $musyrif = User::role('musyrif')->where('id', $user->id)->get();
        } else {
            // Admin bisa melihat semua musyrif
            $musyrif = User::role('musyrif')->get();
        }
        
        return view('halaqah', compact('musyrif'));
    }

    public function data(Request $request)
    {
        // Cek apakah user berhak mengakses
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $filter = $request->get('filter', 'active');

        $query = Halaqah::with('musyrif');
        
        // Jika user adalah musyrif, hanya tampilkan halaqah yang dia pegang
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $query->where('musyrif_id', $user->id);
        }

        switch ($filter) {
            case 'deleted':
                $query->onlyTrashed();
                break;
            case 'all':
                $query->withTrashed();
                break;
            default:
                // Data aktif (default)
                break;
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('musyrif_nama', function ($row) {
                return $row->musyrif ? $row->musyrif->name : '-';
            })
            ->addColumn('aksi', function ($row) {
                // Kode aksi di-generate di client-side
                return '';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Hanya admin yang bisa membuat halaqah baru
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menambahkan halaqah'
            ], 403);
        }

        $request->validate([
            'nama_halaqah' => 'required|string|max:100|unique:halaqah,nama_halaqah',
            'musyrif_id' => 'required|exists:users,id',
        ]);

        // Generate kode halaqah
        $last = Halaqah::withTrashed()->latest('id')->first();
        $nextNumber = $last ? ((int) substr($last->kode, 1)) + 1 : 1;
        $kode = 'H' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $halaqah = Halaqah::create([
            'kode' => $kode,
            'nama_halaqah' => $request->nama_halaqah,
            'musyrif_id' => $request->musyrif_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Halaqah berhasil ditambahkan',
            'data' => $halaqah
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $halaqah = Halaqah::withTrashed()->findOrFail($id);
        
        // Cek apakah user berhak melihat
        if ($user->hasRole('musyrif') && !$user->hasRole('admin') && $halaqah->musyrif_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengakses data ini'
            ], 403);
        }
        
        return response()->json($halaqah);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $halaqah = Halaqah::withTrashed()->findOrFail($id);
        
        // Cek otorisasi
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            if ($halaqah->musyrif_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengedit halaqah yang Anda pegang'
                ], 403);
            }
            
            // Musyrif tidak bisa mengganti musyrif_id
            $request->merge(['musyrif_id' => $user->id]);
        }
        
        $request->validate([
            'nama_halaqah' => 'required|string|max:100|unique:halaqah,nama_halaqah,' . $id,
            'musyrif_id' => 'required|exists:users,id',
        ]);

        $halaqah->update([
            'nama_halaqah' => $request->nama_halaqah,
            'musyrif_id' => $request->musyrif_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Halaqah berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        // Hanya admin yang bisa menghapus
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus halaqah'
            ], 403);
        }

        $halaqah = Halaqah::findOrFail($id);
        $halaqah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Halaqah berhasil dipindahkan ke sampah'
        ]);
    }

    public function forceDelete($id)
    {
        // Hanya admin yang bisa menghapus permanen
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus permanen halaqah'
            ], 403);
        }

        $halaqah = Halaqah::onlyTrashed()->findOrFail($id);
        $halaqah->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Halaqah berhasil dihapus permanen'
        ]);
    }

    public function restore($id)
    {
        // Hanya admin yang bisa merestore
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat memulihkan halaqah'
            ], 403);
        }

        $halaqah = Halaqah::onlyTrashed()->findOrFail($id);
        $halaqah->restore();

        return response()->json([
            'success' => true,
            'message' => 'Halaqah berhasil dipulihkan'
        ]);
    }

    public function checkName(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'musyrif'])) {
            return response()->json(['exists' => false]);
        }
        
        $query = Halaqah::where('nama_halaqah', $request->nama_halaqah);
        
        // Jika musyrif, cek hanya halaqah miliknya
        if ($user->hasRole('musyrif') && !$user->hasRole('admin')) {
            $query->where('musyrif_id', $user->id);
        }
        
        $exists = $query->when($request->id, function($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}