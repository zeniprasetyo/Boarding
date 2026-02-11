<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Halaqah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $trashed = $request->get('trashed') == 1;

            $query = User::with('halaqah')->role('santri')->select('users.*');
            if ($trashed) {
                $query = $query->onlyTrashed();
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('halaqah', fn(User $u) => $u->halaqah?->nama_halaqah ?? '-')
                ->addColumn('telephone', fn(User $u) => $u->telephone ?? '-') // Tambahkan kolom telephone
                ->addColumn('action', function (User $u) use ($trashed) {
                    if ($trashed) {
                        return '
                            <button class="btn btn-sm btn-success restore" data-id="'.$u->id.'">Restore</button>
                            <button class="btn btn-sm btn-danger force-delete" data-id="'.$u->id.'">Hapus Permanen</button>
                        ';
                    }
                    return '
                        <button class="btn btn-sm btn-warning edit" data-id="'.$u->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete" data-id="'.$u->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $halaqah = Halaqah::all();
        return view('santri', compact('halaqah'));
    }

    // 🔹 Simpan atau update
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'telephone' => 'nullable|string|max:20', // Validasi untuk telephone
            'password' => $request->id ? 'nullable|min:6' : 'required|min:6',
            'halaqah_id' => 'nullable|exists:halaqah,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone, // Tambahkan telephone ke data
            'halaqah_id' => $request->halaqah_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user = User::withTrashed()->updateOrCreate(['id' => $request->id], $data);
        
        // Hanya assign role jika user baru
        if (!$request->id) {
            $user->assignRole('santri');
        }

        return response()->json([
            'message' => $request->id ? 'Santri berhasil diperbarui.' : 'Santri berhasil ditambahkan.'
        ]);
    }

    // 🔹 Edit — tampilkan data (AJAX GET)
    public function edit($id)
    {
        return response()->json(User::withTrashed()->findOrFail($id));
    }

    // 🔹 Soft delete
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Santri berhasil dihapus sementara.']);
    }

    // 🔹 Restore dari soft delete
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['message' => 'Santri berhasil direstore.']);
    }

    // 🔹 Hapus permanen
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return response()->json(['message' => 'Santri dihapus permanen.']);
    }
}