<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sparepart;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class HargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data.harga');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data dengan kolom optional, termasuk mata_uang
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:1',
            'mata_uang' => 'required|string|max:10',
            'uom' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
        ]);

        try {
            // Cari record yang ada
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                ->where('kode_barang', $validatedData['kode_barang'])
                ->first();

            if ($sparepart) {
                // Jika record ada, perbarui data
                $sparepart->harga = $validatedData['harga'] ?? $sparepart->harga;
                $sparepart->mata_uang = $validatedData['mata_uang'] ?? $sparepart->mata_uang;
                $sparepart->uom = $validatedData['uom'] ?? $sparepart->uom;
                $sparepart->vendor = $validatedData['vendor'] ?? $sparepart->vendor;
                $sparepart->save();
            } else {
                Sparepart::create([
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'harga' => $validatedData['harga'] ?? 0,
                    'mata_uang' => $validatedData['mata_uang'] ?? null,
                    'uom' => $validatedData['uom'] ?? null,
                    'vendor' => $validatedData['vendor'] ?? null,
                ]);
            }

            return redirect()->route('harga.create')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            return redirect()->route('harga.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Update harga inline via AJAX.
     */
    public function updateHarga(Request $request)
    {
        $request->validate([
            'id_sp' => 'required|integer',
            'harga' => 'nullable|numeric|min:0',
            'mata_uang' => 'nullable|string|max:10',
            'uom' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
        ]);

        try {
            $sparepart = Sparepart::where('id_sp', $request->id_sp)->firstOrFail();
            $sparepart->harga = $request->harga;
            $sparepart->mata_uang = $request->mata_uang;
            $sparepart->uom = $request->uom;
            $sparepart->vendor = $request->vendor;
            $sparepart->save();

            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.'], 500);
        }
    }

    /**
     * Get data for DataTable.
     */
    public function getData()
    {
        $spareparts = Sparepart::select(['id_sp', 'nama_barang', 'kode_barang', 'harga', 'mata_uang', 'uom', 'vendor']);

        return DataTables::of($spareparts)
            ->editColumn('harga', function ($row) {
                $value = $row->harga ?? 0;
                $formatted = $value ? number_format($value, 0, ',', '.') : '';
                return '<input type="text" class="form-control form-control-sm harga-input" data-id="' . $row->id_sp . '" data-original="' . $value . '" value="' . $formatted . '" style="width:130px;">';
            })
            ->editColumn('mata_uang', function ($row) {
                return '<input type="text" class="form-control form-control-sm mata-uang-input" data-id="' . $row->id_sp . '" data-original="' . ($row->mata_uang ?? '') . '" value="' . ($row->mata_uang ?? '') . '" style="width:80px;">';
            })
            ->editColumn('uom', function ($row) {
                return '<input type="text" class="form-control form-control-sm uom-input" data-id="' . $row->id_sp . '" data-original="' . ($row->uom ?? '') . '" value="' . ($row->uom ?? '') . '" style="width:80px;">';
            })
            ->editColumn('vendor', function ($row) {
                return '<input type="text" class="form-control form-control-sm vendor-input" data-id="' . $row->id_sp . '" data-original="' . ($row->vendor ?? '') . '" value="' . ($row->vendor ?? '') . '" style="width:130px;">';
            })
            ->addColumn('aksi', function ($row) {
                return '<div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-success btn-simpan" data-id="' . $row->id_sp . '" title="Simpan"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-secondary btn-reset-harga" data-id="' . $row->id_sp . '" title="Reset"><i class="fas fa-undo"></i> Reset</button>
                </div>';
            })
            ->rawColumns(['harga', 'mata_uang', 'uom', 'vendor', 'aksi'])
            ->make(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
