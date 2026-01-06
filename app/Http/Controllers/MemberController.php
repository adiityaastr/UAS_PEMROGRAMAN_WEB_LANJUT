<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index()
    {
        $members = User::where('role', 'member')->latest()->get();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    private function generateKodeUnik()
    {
        // Format: YYMMDDXXX (9 digit angka)
        // YY = 2 digit tahun, MM = 2 digit bulan, DD = 2 digit hari, XXX = 3 digit nomor urut
        $datePrefix = now()->format('ymd'); // 6 digit: tahun(2) + bulan(2) + hari(2)
        
        // Cari member terakhir dengan prefix tanggal yang sama
        $lastMember = User::where('role', 'member')
            ->where('kode_unik', 'like', $datePrefix . '%')
            ->whereRaw('LENGTH(kode_unik) = 9')
            ->orderBy('kode_unik', 'desc')
            ->first();

        if ($lastMember && $lastMember->kode_unik) {
            // Ambil 3 digit terakhir (nomor urut)
            $lastNumber = (int) substr($lastMember->kode_unik, -3);
            $newNumber = $lastNumber + 1;
            
            // Jika nomor urut melebihi 999, gunakan nomor urut global
            if ($newNumber > 999) {
                $lastGlobalMember = User::where('role', 'member')
                    ->whereRaw('LENGTH(kode_unik) = 9')
                    ->orderBy('kode_unik', 'desc')
                    ->first();
                
                if ($lastGlobalMember && $lastGlobalMember->kode_unik) {
                    $lastGlobalNumber = (int) substr($lastGlobalMember->kode_unik, -3);
                    $newNumber = $lastGlobalNumber + 1;
                } else {
                    $newNumber = 1;
                }
            }
        } else {
            $newNumber = 1;
        }

        // Gabungkan: YYMMDD + XXX (3 digit dengan leading zero)
        return $datePrefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
        ]);

        // Generate unique email dengan timestamp + random string untuk menghindari duplikat
        $uniqueEmail = 'member' . time() . '_' . Str::random(6) . '@perpustakaan.local';
        
        // Pastikan email unik (jika masih duplikat, generate lagi)
        while (User::where('email', $uniqueEmail)->exists()) {
            $uniqueEmail = 'member' . time() . '_' . Str::random(6) . '@perpustakaan.local';
        }

        $data = [
            'name' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'kode_unik' => $this->generateKodeUnik(),
            'role' => 'member',
            'email' => $uniqueEmail,
            'password' => Hash::make('password123'),
        ];

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'member_' . time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
            // Simpan ke storage/app/public/members menggunakan disk 'public'
            $path = $foto->storeAs('members', $filename, 'public');
            // Simpan path relatif untuk akses via public/storage
            $data['foto'] = 'members/' . $filename;
        }

        User::create($data);

        return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
        ]);

        $data = [
            'name' => $request->name,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($member->foto && Storage::disk('public')->exists($member->foto)) {
                Storage::disk('public')->delete($member->foto);
            }

            $foto = $request->file('foto');
            $filename = 'member_' . time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
            // Simpan ke storage/app/public/members menggunakan disk 'public'
            $path = $foto->storeAs('members', $filename, 'public');
            $data['foto'] = 'members/' . $filename;
        }

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $member)
    {
        // Hapus foto jika ada
        if ($member->foto && Storage::disk('public')->exists($member->foto)) {
            Storage::disk('public')->delete($member->foto);
        }

        $member->delete();
        return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus.');
    }
}
