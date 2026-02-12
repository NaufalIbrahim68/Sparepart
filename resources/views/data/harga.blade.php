@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Harga Part') }}
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
                                <h4 class="card-title mb-0">Data Harga Part</h4>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#inputHargaModal">
                                    <i data-feather="plus"></i> Input Harga
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="hargaTable" class="table table-bordered table-striped table-hover"
                                    style="width:100%">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Barang</th>
                                            <th>Kode Barang</th>
                                            <th>Harga</th>
                                            <th>Mata Uang</th>
                                            <th>UOM</th>
                                            <th>Vendor</th>
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

    {{-- Modal Input Harga --}}
    <div class="modal fade" id="inputHargaModal" tabindex="-1" aria-labelledby="inputHargaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputHargaModalLabel">Input Harga Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('harga.store') }}" method="POST" id="dataForm" autocomplete="off">
                        @csrf
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

                        <div class="mb-3 row">
                            <div class="col-md-8">
                                <label for="harga_display" class="form-label">Harga</label>
                                <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                    id="harga_display"
                                    value="{{ old('harga') ? number_format(old('harga'), 0, ',', '.') : '' }}">
                                <input type="hidden" id="harga" name="harga" value="{{ old('harga') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="mata_uang" class="form-label">Tipe</label>
                                <input type="text" class="form-control @error('mata_uang') is-invalid @enderror"
                                    id="mata_uang" name="mata_uang" value="{{ old('mata_uang') }}" readonly>
                                @error('mata_uang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="uom" class="form-label">Unit Of Measure</label>
                            <input type="text" class="form-control @error('uom') is-invalid @enderror" id="uom"
                                name="uom" value="{{ old('uom') }}" readonly>
                            @error('uom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="vendor" class="form-label">Vendor</label>
                            <input type="text" class="form-control @error('vendor') is-invalid @enderror" id="vendor"
                                name="vendor" value="{{ old('vendor') }}">
                            @error('vendor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        function resetForm() {
            var form = document.getElementById('dataForm');
            form.reset();
            document.getElementById('harga').value = '';
            document.getElementById('harga_display').value = '';
            document.getElementById('search_results').innerHTML = '';
        }

        document.getElementById('harga_display').addEventListener('input', function() {
            var rawValue = this.value.replace(/\./g, '');
            document.getElementById('harga').value = rawValue;
            this.value = formatRupiah(this.value);
        });
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
                                listItem.dataset.nama = item.nama_barang;
                                listItem.dataset.kode = item.kode_barang;
                                listItem.addEventListener('click', function() {
                                    document.getElementById('nama_barang').value = item
                                        .nama_barang;
                                    document.getElementById('kode_barang').value = item
                                        .kode_barang;
                                    var rawHarga = item.harga ? parseFloat(item.harga) : '';
                                    document.getElementById('harga').value = rawHarga;
                                    document.getElementById('harga_display').value = rawHarga ?
                                        formatRupiah(rawHarga.toString()) : '';
                                    document.getElementById('vendor').value = item.vendor || '';
                                    if (item.mata_uang) {
                                        document.getElementById('mata_uang').value = item
                                            .mata_uang;
                                    }
                                    if (item.uom) {
                                        document.getElementById('uom').value = item.uom;
                                    }
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
            var table = $('#hargaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('harga.data') }}',
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
                        data: 'harga',
                        name: 'harga'
                    },
                    {
                        data: 'mata_uang',
                        name: 'mata_uang'
                    },
                    {
                        data: 'uom',
                        name: 'uom'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
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
                var modal = new bootstrap.Modal(document.getElementById('inputHargaModal'));
                modal.show();
            @endif

            // Format harga input saat mengetik
            $('#hargaTable').on('input', '.harga-input', function() {
                var val = this.value.replace(/[^\d]/g, '');
                if (val) {
                    this.value = parseInt(val).toLocaleString('id-ID');
                }
            });

            // Tombol Simpan
            $('#hargaTable').on('click', '.btn-simpan', function() {
                var $row = $(this).closest('tr');
                var id = $(this).data('id');
                var hargaVal = $row.find('.harga-input[data-id="' + id + '"]').val().replace(/\./g, '')
                    .replace(/,/g, '');
                var mataUangVal = $row.find('.mata-uang-input[data-id="' + id + '"]').val();
                var uomVal = $row.find('.uom-input[data-id="' + id + '"]').val();
                var vendorVal = $row.find('.vendor-input[data-id="' + id + '"]').val();

                $.ajax({
                    url: '{{ route('harga.updateHarga') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_sp: id,
                        harga: hargaVal || 0,
                        mata_uang: mataUangVal,
                        uom: uomVal,
                        vendor: vendorVal
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
            $('#hargaTable').on('click', '.btn-reset-harga', function() {
                var $row = $(this).closest('tr');
                var id = $(this).data('id');

                var $harga = $row.find('.harga-input[data-id="' + id + '"]');
                var origHarga = $harga.data('original');
                $harga.val(origHarga ? parseInt(origHarga).toLocaleString('id-ID') : '');

                $row.find('.mata-uang-input[data-id="' + id + '"]').val($row.find(
                    '.mata-uang-input[data-id="' + id + '"]').data('original'));
                $row.find('.uom-input[data-id="' + id + '"]').val($row.find('.uom-input[data-id="' + id +
                    '"]').data('original'));
                $row.find('.vendor-input[data-id="' + id + '"]').val($row.find('.vendor-input[data-id="' +
                    id + '"]').data('original'));
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
