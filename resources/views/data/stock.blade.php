@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Stock Part') }}
    </h2>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Data Stock Part</h4>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#inputStockModal">
                                    <i data-feather="plus"></i> Input Stock
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="stockTable" class="table table-bordered table-striped table-hover"
                                    style="width:100%">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Barang</th>
                                            <th>Kode Barang</th>
                                            <th>Address</th>
                                            <th>Lifetime</th>
                                            <th>Leadtime</th>
                                            <th>Stock WRHS</th>
                                            <th style="width: 170px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Input Stock --}}
    <div class="modal fade" id="inputStockModal" tabindex="-1" aria-labelledby="inputStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputStockModalLabel">Input Stock Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('stock.store') }}" method="POST" id="dataForm" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3" style="position: relative;">
                                    <label for="nama_barang" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control @error('nama_barang') is-invalid @enderror"
                                        id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}">
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="search-point" id="search_results"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="kode_barang" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control @error('kode_barang') is-invalid @enderror"
                                        id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" readonly>
                                    @error('kode_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address" value="{{ old('address') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="lifetime" class="form-label">Lifetime (week)</label>
                                    <input type="number" class="form-control @error('lifetime') is-invalid @enderror"
                                        id="lifetime" name="lifetime" value="{{ old('lifetime') }}">
                                    @error('lifetime')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="leadtime" class="form-label">Leadtime (week)</label>
                                    <input type="number" class="form-control @error('leadtime') is-invalid @enderror"
                                        id="leadtime" name="leadtime" value="{{ old('leadtime') }}">
                                    @error('leadtime')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stock_wrhs" class="form-label">Stock Warehouse</label>
                                    <input type="number" class="form-control @error('stock_wrhs') is-invalid @enderror"
                                        id="stock_wrhs" name="stock_wrhs" value="{{ old('stock_wrhs') }}">
                                    @error('stock_wrhs')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" onclick="resetForm()" class="btn btn-outline-danger">Reset</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .search-point {
            position: absolute;
            z-index: 1050;
            background: white;
            width: 100%;
            max-height: 230px;
            overflow-y: hidden;
            overflow-x: hidden;
            left: 0;
        }

        .search-point:hover {
            overflow-y: auto;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function resetForm() {
            var form = document.getElementById('dataForm');
            form.reset();
            document.getElementById('search_results').innerHTML = '';
        }
    </script>

    <script>
        document.getElementById('nama_barang').addEventListener('input', function() {
            var namaBarang = this.value;
            var searchResults = document.getElementById('search_results');

            if (namaBarang.length >= 2) {
                fetch(`/data/search-nama-barang/${encodeURIComponent(namaBarang)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            var list = document.createElement('ul');
                            list.classList.add('list-group');
                            data.forEach(item => {
                                var listItem = document.createElement('li');
                                listItem.classList.add('list-group-item');
                                listItem.style.cursor = 'pointer';
                                listItem.textContent = `${item.nama_barang} - ${item.kode_barang}`;
                                listItem.addEventListener('click', function() {
                                    document.getElementById('nama_barang').value = item
                                        .nama_barang;
                                    document.getElementById('kode_barang').value = item
                                        .kode_barang;
                                    document.getElementById('address').value = item.address ||
                                        '';
                                    document.getElementById('lifetime').value = item.lifetime ||
                                        '';
                                    document.getElementById('leadtime').value = item.leadtime ||
                                        '';
                                    document.getElementById('stock_wrhs').value = item
                                        .stock_wrhs || '';
                                    searchResults.innerHTML = '';
                                });
                                list.appendChild(listItem);
                            });
                            searchResults.appendChild(list);
                        } else {
                            searchResults.innerHTML = '<p>No results found.</p>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                searchResults.innerHTML = '';
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('stock.data') }}',
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'kode_barang',
                        name: 'kode_barang'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'lifetime',
                        name: 'lifetime'
                    },
                    {
                        data: 'leadtime',
                        name: 'leadtime'
                    },
                    {
                        data: 'stock_wrhs',
                        name: 'stock_wrhs'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Auto open modal if there are validation errors
            @if ($errors->any())
                var modal = new bootstrap.Modal(document.getElementById('inputStockModal'));
                modal.show();
            @endif

            // Tombol Simpan
            $('#stockTable').on('click', '.btn-simpan', function() {
                var $row = $(this).closest('tr');
                var id = $(this).data('id');
                var addressVal = $row.find('.address-input[data-id="' + id + '"]').val();
                var lifetimeVal = $row.find('.lifetime-input[data-id="' + id + '"]').val();
                var leadtimeVal = $row.find('.leadtime-input[data-id="' + id + '"]').val();
                var stockWrhsVal = $row.find('.stock-wrhs-input[data-id="' + id + '"]').val();

                $.ajax({
                    url: '{{ route('stock.updateStock') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_sp: id,
                        address: addressVal,
                        lifetime: lifetimeVal || null,
                        leadtime: leadtimeVal || null,
                        stock_wrhs: stockWrhsVal || null
                    },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memperbarui data.'
                        });
                    }
                });
            });

            // Tombol Reset
            $('#stockTable').on('click', '.btn-reset-stock', function() {
                var $row = $(this).closest('tr');
                var id = $(this).data('id');

                $row.find('.address-input[data-id="' + id + '"]').val($row.find('.address-input[data-id="' +
                    id + '"]').data('original'));
                $row.find('.lifetime-input[data-id="' + id + '"]').val($row.find(
                    '.lifetime-input[data-id="' + id + '"]').data('original'));
                $row.find('.leadtime-input[data-id="' + id + '"]').val($row.find(
                    '.leadtime-input[data-id="' + id + '"]').data('original'));
                $row.find('.stock-wrhs-input[data-id="' + id + '"]').val($row.find(
                    '.stock-wrhs-input[data-id="' + id + '"]').data('original'));
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    timer: 4000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    timer: 4000
                });
            @endif
        });
    </script>
@endsection
