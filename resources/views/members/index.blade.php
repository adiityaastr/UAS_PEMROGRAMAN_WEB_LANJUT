@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0;">Daftar Anggota</h1>
        <a href="{{ route('members.create') }}" class="btn btn-primary">Tambah Anggota</a>
    </div>

    <div class="glass fade-in">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-primary"
                                    style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">Edit</a>
                                <form action="{{ route('members.destroy', $member) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        style="padding: 0.25rem 0.75rem; font-size: 0.875rem;"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada
                                anggota yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection