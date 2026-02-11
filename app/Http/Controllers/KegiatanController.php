<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KegiatanController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');

    }

    /** 🔹 GET /kegiatan */
    public function index(Request $request)
    {
        // ✅ Kalau AJAX → return JSON untuk DataTables
        if ($request->ajax()) {
            $status = $request->get('status', 'active');

            $query = Kegiatan::query();

            if ($status === 'deleted') {
                $query->onlyTrashed();
            } elseif ($status === 'all') {
                $query->withTrashed();
            }

            return DataTables::of($query->orderBy('id', 'asc'))
                ->addIndexColumn() // 🔹 otomatis DT_RowIndex
                ->addColumn('action', function ($row) {
                    if ($row->deleted_at) {
                        return '
                            <button class="btn btn-sm btn-success btnRestore" data-id="' . $row->id . '">Restore</button>
                        ';
                    } else {
                        return '
                            <button class="btn btn-sm btn-warning btnEdit" data-id="' . $row->id . '">Edit</button>
                            <button class="btn btn-sm btn-danger btnDelete" data-id="' . $row->id . '">Hapus</button>
                        ';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // ✅ Kalau bukan AJAX → tampilkan blade
        return view('kegiatan');
    }
    public function data()
    {
        $kegiatan = Kegiatan::select('id', 'nama_kegiatan', 'parent_id')->get();

        return datatables()->of($kegiatan)->make(true);
    }


    /** 🔹 POST /kegiatan */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:kegiatan,id',
        ]);

        $kode = Kegiatan::generateKode();

        Kegiatan::create([
            'kode' => $kode,
            'nama_kegiatan' => $request->nama_kegiatan,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['message' => 'Kegiatan berhasil disimpan']);
    }

    /** 🔹 GET /kegiatan/{id} */
    public function show($id)
    {
        $kegiatan = Kegiatan::withTrashed()->findOrFail($id);
        return response()->json($kegiatan);
    }

    /** 🔹 PUT /kegiatan/{id} */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:kegiatan,id',
        ]);

        $kegiatan = Kegiatan::withTrashed()->findOrFail($id);
        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['message' => 'Kegiatan berhasil diperbarui']);
    }

    /** 🔹 DELETE /kegiatan/{id} (soft delete) */
    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return response()->json(['message' => 'Kegiatan berhasil dihapus (soft delete)']);
    }

    /** 🔹 POST /kegiatan/restore/{id} */
    public function restore($id)
    {
        $kegiatan = Kegiatan::withTrashed()->findOrFail($id);
        $kegiatan->restore();

        return response()->json(['message' => 'Kegiatan berhasil direstore']);
    }
}
