<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PartKeluar;
use App\Models\Sparepart;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PartKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = PartKeluar::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%");
            });
        }

        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $partKeluar = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('data.partkeluar_index', compact('partKeluar'));
    }
    public function create()
    {
        return view('data.partkeluar');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'pic' => 'required|string|max:255',
            'keperluan' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kode_barang' => 'required|string|max:255',
            'qty' => 'required|numeric|min:1',
        ]);

        try {
            $sparepart = Sparepart::where('nama_barang', $validatedData['nama_barang'])
                ->where('kode_barang', $validatedData['kode_barang'])
                ->first();

            if ($sparepart) {
                // Jika nama_barang sudah ada, tambahkan quantity
                $sparepart->part_keluar += $validatedData['qty'];

                // Hitung stock akhir warehouse
                $part_masuk = $sparepart->part_masuk ?? 0;
                $sparepart->stock_akhir_wrhs = $sparepart->stock_wrhs + $part_masuk - $sparepart->part_keluar;

                $sparepart->save();
            } else {
                // Jika nama_barang belum ada, buat record baru
                Sparepart::create([
                    'part_keluar' => $validatedData['qty'],
                    'stock_wrhs' => -$validatedData['qty'], // Set stock_wrhs sesuai -qty
                    'nama_barang' => $validatedData['nama_barang'],
                    'kode_barang' => $validatedData['kode_barang'],
                    'stock_akhir_wrhs' => -$validatedData['qty'], // Set stock_akhir_wrhs sesuai -qty
                ]);
            }

            PartKeluar::create([
                'tanggal' => $validatedData['tanggal'],
                'pic' => $validatedData['pic'],
                'keperluan' => $validatedData['keperluan'],
                'nama_barang' => $validatedData['nama_barang'],
                'kode_barang' => $validatedData['kode_barang'],
                'qty' => $validatedData['qty'],
            ]);

            session()->flash('nama_barang', $request->input('nama_barang'));
            session()->flash('kode_barang', $request->input('kode_barang'));

            return redirect()->route('partkeluar.create')->with('success', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('partkeluar.create')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function show(PartKeluar $data)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PartKeluar $data)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartKeluar $data)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartKeluar $data)
    {
        //
    }

    /**
     * Approve the specified part keluar.
     */
    public function approve($id)
    {
        try {
            $partKeluar = PartKeluar::findOrFail($id);
            $partKeluar->flag = 1;
            $partKeluar->save();

            return redirect()->route('partkeluar.index')->with('success', 'Part keluar berhasil di-approve.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('partkeluar.index')->with('error', 'Terjadi kesalahan saat approve data.');
        }
    }

    /**
     * Export part keluar to Excel based on date range.
     */
    public function export(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->input('tanggal');

        $partKeluar = PartKeluar::whereDate('tanggal', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['No', 'Tanggal', 'PIC', 'Keperluan', 'Nama Barang', 'Kode Barang', 'Qty', 'Status'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        // Data rows
        $row = 2;
        $no = 1;
        foreach ($partKeluar as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->tanggal);
            $sheet->setCellValue('C' . $row, $item->pic);
            $sheet->setCellValue('D' . $row, $item->keperluan);
            $sheet->setCellValue('E' . $row, $item->nama_barang);
            $sheet->setCellValue('F' . $row, $item->kode_barang);
            $sheet->setCellValue('G' . $row, $item->qty);
            $sheet->setCellValue('H' . $row, $item->flag == 1 ? 'Approved' : 'Pending');
            $row++;
            $no++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'part_keluar_' . $tanggal . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
