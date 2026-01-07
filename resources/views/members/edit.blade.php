@extends('layouts.app')

@section('content')
    <h1>Edit Anggota</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Kode Unik (NIM)</label>
                <input type="text" class="form-control" value="{{ $member->kode_unik ?? '-' }}" disabled
                    style="background: rgba(148, 163, 184, 0.1); cursor: not-allowed; font-family: monospace; letter-spacing: 0.1em; font-size: 1.1rem; font-weight: 600;">
                <small style="color: var(--text-muted);">Kode unik 9 digit angka tidak dapat diubah</small>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $member->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Foto Pengguna</label>
                @if($member->foto)
                    <div style="margin-bottom: 0.75rem;">
                        <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto {{ $member->name }}"
                            class="member-photo">
                    </div>
                @endif
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small style="color: var(--text-muted);">Format: JPG, PNG, GIF (Maks. 2MB). Kosongkan jika tidak ingin mengubah.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" required
                    value="{{ old('tempat_lahir', $member->tempat_lahir) }}" placeholder="Contoh: Jakarta">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" required
                    value="{{ old('tanggal_lahir', $member->tanggal_lahir ? $member->tanggal_lahir->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Program Studi / Fakultas</label>
                <select name="program_studi" class="form-control" required>
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($programStudi as $prodi)
                        <option value="{{ $prodi }}" {{ old('program_studi', $member->program_studi) == $prodi ? 'selected' : '' }}>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <input type="number" name="semester" class="form-control" required
                    value="{{ old('semester', $member->semester) }}" min="1" max="14" placeholder="Contoh: 3">
                <small style="color: var(--text-muted);">Masukkan semester saat ini (1-14)</small>
            </div>
            <div class="form-group">
                <label class="form-label">Tahun Masuk</label>
                <input type="number" name="tahun_masuk" class="form-control" required
                    value="{{ old('tahun_masuk', $member->tahun_masuk) }}" min="2000" max="{{ date('Y') }}" placeholder="Contoh: 2024">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="aktif" {{ old('status', $member->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ old('status', $member->status) == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control"
                    value="{{ old('nomor_telepon', $member->nomor_telepon) }}" placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap">{{ old('alamat', $member->alamat) }}</textarea>
            </div>
            <div class="form-actions">
                <a href="{{ route('members.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Anggota</button>
            </div>
        </form>
    </div>
@endsection