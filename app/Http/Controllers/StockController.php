<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Data;
use App\Models\Sparepart;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class StockController extends Controller
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
        return view('data.stock');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data dengan kolom optional
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'leadtime' => 'nullable|numeric|min:1',
            'lifetime' => 'nullable|numeric|min:1',
            'stock_wrhs' => 'nullable|numeric|min:0',
            'part_masuk' => 'nullable|numeric|min:0', // Menambahkan validasi untuk part_masuk
            'part_keluar' => 'nullable|numeric|min:0', // Menambahkan validasi untuk part_keluar
        ]);

        try {
            // Cari record yang ada
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                ->where('kode_barang', $validatedData['kode_barang'])
                ->first();

            if ($sparepart) {
                // Update existing record
                $sparepart->address = $validatedData['address'] ?? $sparepart->address;
                $sparepart->leadtime = $validatedData['leadtime'] ?? $sparepart->leadtime;
                $sparepart->lifetime = $validatedData['lifetime'] ?? $sparepart->lifetime;
                $sparepart->stock_wrhs = $validatedData['stock_wrhs'] ?? $sparepart->stock_wrhs;

                // Pastikan variabel-variabel ini ada sebelum perhitungan
                $leadtime = $sparepart->leadtime ?: 1; // Default 1 untuk menghindari pembagian dengan nol
                $lifetime = $sparepart->lifetime ?: 1; // Default 1 untuk menghindari pembagian dengan nol
                $total_qty = $sparepart->total_qty ?: 0; // Default 0 jika tidak ada total_qty
                $ms_ss = $sparepart->ms_ss ?: 0; // Default 0 jika tidak ada ms_ss

                // Perhitungan min_stock
                $sparepart->min_stock = ($leadtime / $lifetime) * $total_qty + $ms_ss;

                // Hitung stock akhir warehouse
                $part_masuk = $validatedData['part_masuk'] ?? $sparepart->part_masuk ?? 0;
                $part_keluar = $validatedData['part_keluar'] ?? $sparepart->part_keluar ?? 0;
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $part_masuk - $part_keluar;

                $sparepart->save();
            } else {
                // Hitung min_stock untuk record baru
                $leadtime = $validatedData['leadtime'] ?? 1; // Default 1 untuk menghindari pembagian dengan nol
                $lifetime = $validatedData['lifetime'] ?? 1; // Default 1 untuk menghindari pembagian dengan nol
                $total_qty = 0; // Default 0 jika tidak ada total_qty
                $ms_ss = 0; // Default 0 jika tidak ada ms_ss
                $min_stock = ($leadtime / $lifetime) * $total_qty + $ms_ss;

                // Hitung stock akhir warehouse
                $stock_wrhs = $validatedData['stock_wrhs'] ?? 0;
                $part_masuk = $validatedData['part_masuk'] ?? 0;
                $part_keluar = $validatedData['part_keluar'] ?? 0;
                $stock_akhir_wrhs = $stock_wrhs + $part_masuk - $part_keluar;

                // Buat record baru
                Sparepart::create([
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'address' => $validatedData['address'] ?? '',
                    'leadtime' => $validatedData['leadtime'] ?? null,
                    'lifetime' => $validatedData['lifetime'] ?? null,
                    'stock_wrhs' => $stock_wrhs,
                    'min_stock' => $min_stock,
                    'part_masuk' => $part_masuk,
                    'part_keluar' => $part_keluar,
                    'stock_akhir_wrhs' => $stock_akhir_wrhs,
                ]);
            }

            return redirect()->route('stock.create')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('stock.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
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
     * Update stock inline via AJAX.
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'id_sp' => 'required|integer',
            'address' => 'nullable|string|max:255',
            'lifetime' => 'nullable|numeric|min:0',
            'leadtime' => 'nullable|numeric|min:0',
            'stock_wrhs' => 'nullable|numeric|min:0',
        ]);

        try {
            $sparepart = Sparepart::where('id_sp', $request->id_sp)->firstOrFail();
            $sparepart->address = $request->address;
            $sparepart->lifetime = $request->lifetime;
            $sparepart->leadtime = $request->leadtime;
            $sparepart->stock_wrhs = $request->stock_wrhs;
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
        $spareparts = Sparepart::select(['id_sp', 'nama_barang', 'kode_barang', 'address', 'lifetime', 'leadtime', 'stock_wrhs']);

        return DataTables::of($spareparts)
            ->editColumn('address', function ($row) {
                return '<input type="text" class="form-control form-control-sm address-input" data-id="' . $row->id_sp . '" data-original="' . ($row->address ?? '') . '" value="' . ($row->address ?? '') . '" style="width:120px;">';
            })
            ->editColumn('lifetime', function ($row) {
                return '<input type="number" class="form-control form-control-sm lifetime-input" data-id="' . $row->id_sp . '" data-original="' . ($row->lifetime ?? '') . '" value="' . ($row->lifetime ?? '') . '" style="width:90px;">';
            })
            ->editColumn('leadtime', function ($row) {
                return '<input type="number" class="form-control form-control-sm leadtime-input" data-id="' . $row->id_sp . '" data-original="' . ($row->leadtime ?? '') . '" value="' . ($row->leadtime ?? '') . '" style="width:90px;">';
            })
            ->editColumn('stock_wrhs', function ($row) {
                return '<input type="number" class="form-control form-control-sm stock-wrhs-input" data-id="' . $row->id_sp . '" data-original="' . ($row->stock_wrhs ?? '') . '" value="' . ($row->stock_wrhs ?? '') . '" style="width:90px;">';
            })
            ->addColumn('aksi', function ($row) {
                return '<div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-success btn-simpan" data-id="' . $row->id_sp . '" title="Simpan"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-sm btn-secondary btn-reset-stock" data-id="' . $row->id_sp . '" title="Reset"><i class="fas fa-undo"></i> Reset</button>
                </div>';
            })
            ->rawColumns(['address', 'lifetime', 'leadtime', 'stock_wrhs', 'aksi'])
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
