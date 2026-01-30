@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800">
        {{ __('Form Input') }}
    </h2>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <h1>Input Part Keluar</h1>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('partkeluar.store') }}" method="POST" id="dataForm" autocomplete="off">
                                @csrf
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                        id="tanggal" name="tanggal" value="{{ old('tanggal') }}">
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="pic" class="form-label">PIC (Scan Barcode)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('pic') is-invalid @enderror"
                                            id="pic" name="pic" value="{{ old('pic', session('pic')) }}"
                                            placeholder="Scan Barcode PIC...">
                                        <button type="button" class="btn btn-primary" id="scanBarcodeBtn">
                                            <i class="bi bi-camera"></i> Scan
                                        </button>
                                    </div>
                                    @error('pic')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="keperluan" class="form-label">Keperluan Part</label>
                                    <input type="text" class="form-control @error('keperluan') is-invalid @enderror"
                                        id="keperluan" name="keperluan" value="{{ old('keperluan') }}">
                                    @error('keperluan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Barang</label>
                                    <input type="text" class="form-control @error('nama_barang') is-invalid @enderror"
                                        id="nama_barang" name="nama_barang"
                                        value="{{ request()->query('nama_barang', old('nama_barang', session('nama_barang'))) }}">
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class ="search-point" id="search_results"></div>

                                <div class="mb-3">
                                    <label for="kode_barang" class="form-label">Kode Barang</label>
                                    <input type="text" class="form-control @error('kode_barang') is-invalid @enderror"
                                        id="kode_barang" name="kode_barang"
                                        value="{{ request()->query('kode_barang', old('kode_barang', session('kode_barang'))) }}"
                                        readonly>
                                    @error('kode_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="qty" class="form-label">Quantity</label>
                                    <input type="number" class="form-control @error('qty') is-invalid @enderror"
                                        id="qty" name="qty" value="{{ old('qty') }}">
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-actions">
                                    <button type="button" onclick="resetForm()"
                                        class="btn btn-outline-danger">Reset</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                            <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert"
                                aria-live="assertive" aria-atomic="true"
                                style="position: fixed; top: 10px; right: 10px; display: none;">
                                <div class="d-flex">
                                    <div class="toast-body" id="toastMessage"></div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode PIC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reader" style="width: 100%;"></div>
                    <div id="scanResult" class="mt-3 alert alert-success" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .search-point {
            position: absolute;
            z-index: 1000;
            background: white;
            width: 96.5%;
            max-height: 230px;
            overflow-y: hidden;
            overflow-x: hidden;
        }

        .search-point:hover {
            overflow-y: auto;
        }

        /* Remove mirror effect from barcode scanner */
        #reader video {
            transform: scaleX(-1);
        }
    </style>

    <script>
        function resetForm() {
            var form = document.getElementById('dataForm');
            form.reset();
            document.getElementById('nama_barang').value = '';
            document.getElementById('kode_barang').value = '';
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
                                listItem.textContent = `${item.nama_barang} - ${item.kode_barang}`;
                                listItem.dataset.nama = item.nama_barang;
                                listItem.dataset.kode = item.kode_barang;
                                listItem.addEventListener('click', function() {
                                    document.getElementById('nama_barang').value = item
                                        .nama_barang;
                                    document.getElementById('kode_barang').value = item
                                        .kode_barang;
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

    <!-- html5-qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode = null;
        const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');
        const barcodeScannerModal = new bootstrap.Modal(document.getElementById('barcodeScannerModal'));

        scanBarcodeBtn.addEventListener('click', function() {
            barcodeScannerModal.show();
            startScanner();
        });

        // Clean up when modal is closed
        document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
            stopScanner();
        });

        function startScanner() {
            if (html5QrCode) {
                return; // Scanner already running
            }

            html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: {
                    width: 400,
                    height: 400
                },
                aspectRatio: 1.0
            };

            html5QrCode.start({
                    facingMode: "environment"
                }, // Use back camera if available
                config,
                (decodedText, decodedResult) => {
                    // Success callback - barcode detected
                    document.getElementById('pic').value = decodedText;
                    document.getElementById('scanResult').innerHTML =
                        `<strong>Berhasil!</strong> Barcode terdeteksi: ${decodedText}`;
                    document.getElementById('scanResult').style.display = 'block';

                    // Stop scanner and close modal after 1 second
                    setTimeout(() => {
                        stopScanner();
                        barcodeScannerModal.hide();
                        document.getElementById('scanResult').style.display = 'none';
                    }, 1000);
                },
                (errorMessage) => {
                    // Error callback - can be ignored, happens when no barcode is in view
                }
            ).catch((err) => {
                console.error('Unable to start scanner:', err);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    html5QrCode = null;
                }).catch((err) => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }
    </script>
@endsection
