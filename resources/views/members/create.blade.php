@extends('layouts.app')

@section('content')
    <h1>Tambah Anggota Baru</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}"
                    placeholder="Nama Lengkap">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Pengguna</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small style="color: var(--text-muted);">Format: JPG, PNG, GIF (Maks. 2MB)</small>
            </div>
            <div class="form-group">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" required value="{{ old('tempat_lahir') }}"
                    placeholder="Contoh: Jakarta">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required value="{{ old('tanggal_lahir') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Program Studi / Fakultas</label>
                <select name="program_studi" class="form-control" required>
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($programStudi as $prodi)
                        <option value="{{ $prodi }}" {{ old('program_studi') == $prodi ? 'selected' : '' }}>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <input type="number" name="semester" class="form-control" required value="{{ old('semester') }}"
                    min="1" max="14" placeholder="Contoh: 3">
                <small style="color: var(--text-muted);">Masukkan semester saat ini (1-14)</small>
            </div>
            <div class="form-group">
                <label class="form-label">Tahun Masuk</label>
                <input type="number" name="tahun_masuk" class="form-control" required value="{{ old('tahun_masuk') }}"
                    min="2000" max="{{ date('Y') }}" placeholder="Contoh: 2024">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}"
                    placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Kode Unik (NIM)</label>
                <input type="text" class="form-control" value="Akan dibuat otomatis (9 digit angka)" disabled
                    style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-family: monospace; letter-spacing: 0.1em;">
                <small style="color: var(--text-muted);">Kode unik 9 digit angka akan dibuat otomatis saat menyimpan (Format: YYMMDDXXX)</small>
            </div>
            <div class="form-actions">
                <a href="{{ route('members.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Anggota</button>
            </div>
        </form>
    </div>
@endsection