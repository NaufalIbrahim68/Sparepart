@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
        {{ __('User Management') }}
    </h2>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">User List</h4>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerUserModal">
                                Register
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="userTable" class="table table-striped table-bordered">
                                <thead class="table-header">
                                    <tr>
                                        <th>No.</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->roles == 'admin')
                                                    <span class="badge bg-primary">Admin</span>
                                                @else
                                                    <span class="badge bg-secondary">User</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal{{ $user->id }}" title="Edit User">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $user->id }})" title="Hapus User">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Update User -->
                                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                                            aria-labelledby="editUserModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('users.update', $user->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label for="name" class="form-label">Username</label>
                                                                <input type="text" class="form-control" id="name"
                                                                    name="name" value="{{ $user->name }}" required
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Password (kosongkan
                                                                    jika tidak ingin mengganti password)</label>
                                                                <input type="password" class="form-control" id="password"
                                                                    name="password">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="email" class="form-label">Email</label>
                                                                <input type="email" class="form-control" id="email"
                                                                    name="email" value="{{ $user->email }}" required
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="roles" class="form-label">Roles</label>
                                                                <select class="form-select" id="roles" name="roles"
                                                                    required>
                                                                    <option value="admin"
                                                                        {{ $user->roles == 'admin' ? 'selected' : '' }}>
                                                                        Admin</option>
                                                                    <option value="user"
                                                                        {{ $user->roles == 'user' ? 'selected' : '' }}>User
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>

    <!-- Modal Register-->
    <div class="modal fade" id="registerUserModal" tabindex="-1" aria-labelledby="registerUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerUserModalLabel">Register User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Username</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required
                                autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="roles" class="form-label">Roles</label>
                            <select class="form-select" id="roles" name="roles" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .table-header th {
            color: #fff;
            background-color: #2088ef;
        }

        .text-center {
            text-align: center;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                paging: true,
                searching: false,
                ordering: true,
                info: false,
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                lengthChange: false
            });
        });
    </script>

    <script>
        function togglePasswordVisibility(userId) {
            const passwordInput = document.getElementById(`password-${userId}`);
            const eyeIcon = document.getElementById(`eye-icon-${userId}`);
            const actualPassword = passwordInput.getAttribute('data-actual-password');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordInput.value = actualPassword;
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                passwordInput.value = "********";
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                timer: 3000
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true,
                timer: 3000
            });
        @endif
    </script>

    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            });
        }
    </script>
@endsection
