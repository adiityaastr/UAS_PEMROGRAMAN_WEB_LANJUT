@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>Daftar Anggota</h1>
        <a href="{{ route('members.create') }}" class="btn btn-primary">Tambah Anggota</a>
    </div>

    <div class="glass fade-in">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Kode Unik</th>
                        <th>Nama Lengkap</th>
                        <th>Tempat & Tanggal Lahir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>
                                @if($member->foto)
                                    <img src="{{ asset('storage/' . $member->foto) }}" alt="Foto {{ $member->name }}"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem; border: var(--glass-border);">
                                @else
                                    <div style="width: 50px; height: 50px; background: var(--hover-bg); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.75rem; text-align: center; padding: 0.25rem;">
                                        No<br>Photo
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($member->kode_unik)
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color); font-family: monospace; letter-spacing: 0.05em; font-weight: 600;">
                                        {{ $member->kode_unik }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td><strong>{{ $member->name }}</strong></td>
                            <td>
                                {{ $member->tempat_lahir ?? '-' }}, 
                                {{ $member->tanggal_lahir ? $member->tanggal_lahir->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada
                                anggota yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection