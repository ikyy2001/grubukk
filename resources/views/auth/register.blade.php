@extends('layouts.app')

@section('title', 'Daftar Akun Baru')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12">
            <div class="card o-hidden border-0 shadow-lg my-3">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center mb-4">
                            <h1 class="h2 text-gray-900 font-weight-bold">Buat Akun Baru SPPD</h1>
                            <p class="text-muted">Isi formulir di bawah ini untuk mendaftarkan akun sesuai peran (role)</p>
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Terdapat kesalahan input:
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="user">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="nip" class="font-weight-bold">NIP / Nomor Identitas <span class="text-danger">*</span></label>
                                    <input id="nip" type="text"
                                        class="form-control form-control-user @error('nip') is-invalid @enderror"
                                        name="nip" value="{{ old('nip') }}" required autofocus
                                        placeholder="Contoh: 198501012010011001 atau 12345">
                                    @error('nip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="nama" class="font-weight-bold">Nama Lengkap + Gelar <span class="text-danger">*</span></label>
                                    <input id="nama" type="text"
                                        class="form-control form-control-user @error('nama') is-invalid @enderror"
                                        name="nama" value="{{ old('nama') }}" required
                                        placeholder="Contoh: Budi Santoso, S.T., M.M.">
                                    @error('nama')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="password" class="font-weight-bold">Password <span class="text-danger">*</span></label>
                                    <input id="password" type="password"
                                        class="form-control form-control-user @error('password') is-invalid @enderror"
                                        name="password" required placeholder="Minimal 4 karakter">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="role" class="font-weight-bold">Pilih Role / Peran <span class="text-danger">*</span></label>
                                    <select id="role" name="role"
                                        class="custom-select custom-select-lg @error('role') is-invalid @enderror"
                                        style="height: calc(1.5em + 1.25rem + 2px); font-size: 0.95rem; border-radius: 10rem;" required>
                                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="Kepala Bidang" {{ old('role') == 'Kepala Bidang' ? 'selected' : '' }}>Kepala Bidang</option>
                                        <option value="Kepala Seksi" {{ old('role') == 'Kepala Seksi' ? 'selected' : '' }}>Kepala Seksi</option>
                                        <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                    </select>
                                    @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="jabatan" class="font-weight-bold">Jabatan</label>
                                    <input id="jabatan" type="text"
                                        class="form-control form-control-user"
                                        name="jabatan" value="{{ old('jabatan') }}"
                                        placeholder="Contoh: Pengelola Data / Analis">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="tanggal_lahir" class="font-weight-bold">Tanggal Lahir</label>
                                    <input id="tanggal_lahir" type="date"
                                        class="form-control form-control-user"
                                        name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="golongan" class="font-weight-bold">Golongan</label>
                                    <select class="custom-select custom-select-lg" id="golongan" name="golongan"
                                        style="height: calc(1.5em + 1.25rem + 2px); font-size: 0.95rem; border-radius: 10rem;">
                                        <option value="" selected>-- Pilih Golongan (Opsional) --</option>
                                        <?php $no = 1; ?>
                                        @foreach ($golongan as $gol)
                                        <option value="{{ $no }}" {{ old('golongan') == $no ? 'selected' : '' }}>{{ $gol }}</option>
                                        <?php $no++; ?>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="eselon" class="font-weight-bold">Eselon</label>
                                    <select class="custom-select custom-select-lg" id="eselon" name="eselon"
                                        style="height: calc(1.5em + 1.25rem + 2px); font-size: 0.95rem; border-radius: 10rem;">
                                        <option value="" selected>-- Pilih Eselon (Opsional) --</option>
                                        <?php $no = 1; ?>
                                        @foreach ($eselon as $eselo)
                                        <option value="{{ $no }}" {{ old('eselon') == $no ? 'selected' : '' }}>{{ $eselo }}</option>
                                        <?php $no++; ?>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alamat" class="font-weight-bold">Alamat Lengkap</label>
                                <textarea id="alamat" class="form-control" rows="2" name="alamat"
                                    placeholder="Alamat domisili..." style="border-radius: 15px;">{{ old('alamat') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block py-3 font-weight-bold shadow-sm" style="font-size: 1.1rem; border-radius: 10rem;">
                                <i class="fa fa-user-plus mr-1"></i> Daftar & Buat Akun
                            </button>
                        </form>

                        <hr>
                        <div class="text-center">
                            <span>Sudah memiliki akun? </span>
                            <a class="font-weight-bold text-primary" href="{{ route('login') }}">Masuk / Login di sini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
