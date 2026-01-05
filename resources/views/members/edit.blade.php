@extends('layouts.app')

@section('content')
    <h1 style="margin-bottom: 2rem;">Edit Anggota</h1>

    <div class="glass fade-in" style="max-width: 600px;">
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $member->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $member->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Kata Sandi Baru (Opsional)</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Biarkan kosong jika tidak ingin mengubah">
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Perbarui Anggota</button>
                <a href="{{ route('members.index') }}" class="btn" style="background: rgba(148, 163, 184, 0.1);">Batal</a>
            </div>
        </form>
    </div>
@endsection