@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Data Part Keluar') }}
    </h2>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Data Part Keluar</h4>
                                <a href="{{ route('partkeluar.create') }}" class="btn btn-primary">
                                    <i data-feather="plus"></i> Input Part Keluar
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <form method="GET" class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="tanggal" class="form-label mb-0">Tanggal:</label>
                                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                                            style="min-width: 200px;" value="{{ request('tanggal') }}" required>
                                    </div>
                                    <button type="submit" formaction="{{ route('partkeluar.index') }}"
                                        class="btn btn-info">
                                        <i data-feather="filter"></i> Filter
                                    </button>
                                    <button type="submit" formaction="{{ route('partkeluar.export') }}"
                                        class="btn btn-success">
                                        <i data-feather="download"></i> Export Excel
                                    </button>
                                </form>

                                <form action="{{ route('partkeluar.index') }}" method="GET"
                                    class="d-flex align-items-center gap-2">
                                    <label for="search" class="form-label mb-0">Cari:</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                        placeholder="Nama / Kode / PIC" value="{{ request('search') }}"
                                        style="min-width: 200px; width: 250px;">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Tanggal</th>
                                            <th>PIC</th>
                                            <th>Keperluan</th>
                                            <th>Nama Barang</th>
                                            <th>Kode Barang</th>
                                            <th>Qty</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($partKeluar as $index => $item)
                                            <tr>
                                                <td>{{ $partKeluar->firstItem() + $index }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                                <td>{{ $item->pic }}</td>
                                                <td>{{ $item->keperluan }}</td>
                                                <td>{{ $item->nama_barang }}</td>
                                                <td>{{ $item->kode_barang }}</td>
                                                <td>{{ $item->qty }}</td>
                                                <td class="text-center">
                                                    @if ($item->flag == 1)
                                                        <span class="badge bg-success">Approved</span>
                                                    @else
                                                        <form action="{{ route('partkeluar.approve', $item->id_pk) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Approve item ini?')">
                                                                <i data-feather="check"></i> Approve
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Tidak ada data part keluar
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Menampilkan {{ $partKeluar->firstItem() ?? 0 }} - {{ $partKeluar->lastItem() ?? 0 }}
                                    dari {{ $partKeluar->total() }} data
                                </div>
                                <div>
                                    {{ $partKeluar->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
