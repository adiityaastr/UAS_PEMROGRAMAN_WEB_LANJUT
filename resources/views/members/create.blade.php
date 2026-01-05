@extends('layouts.app')

@section('content')
    <h1 style="margin-bottom: 2rem;">Tambah Anggota Baru</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('members.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}"
                    placeholder="Nama Lengkap">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}"
                    placeholder="email@contoh.com">
            </div>
            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Simpan Anggota</button>
                <a href="{{ route('members.index') }}" class="btn" style="background: rgba(148, 163, 184, 0.1);">Batal</a>
            </div>
        </form>
    </div>
@endsection