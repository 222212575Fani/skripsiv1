<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses registrasi akun baru
    public function register(Request $request)
    {
    $request->validate([
        'nama' => 'required|string|max:100',
        'nip' => 'required|string|size:18|unique:pengguna,nip',
        'email' => [
            'required',
            'email',
            'max:100',
            'unique:pengguna,email',
            'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/'
        ],
        'password' => 'required|string|min:8|confirmed',
    ], [
        'nama.required' => 'Nama lengkap wajib diisi',
        'nip.required' => 'NIP wajib diisi',
        'nip.size' => 'NIP harus terdiri dari 18 digit',
        'nip.unique' => 'NIP sudah terdaftar',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.max' => 'Email maksimal 100 karakter',
        'email.unique' => 'Email sudah terdaftar',
        'email.regex' => 'Email harus menggunakan domain @bps.go.id',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password.confirmed' => 'Konfirmasi password tidak sesuai',
    ]);

    Pengguna::create([
        'nama' => $request->nama,
        'nip' => $request->nip,
        'email' => $request->email,
        'password' => Hash::make($request->password),

        // Role belum ditentukan saat registrasi.
        // Role ditentukan admin saat aktivasi akun.
        'id_role' => null,

        // Akun baru otomatis pending.
        'status_akun' => 'pending',

        // Belum disetujui admin.
        'disetujui_pada' => null,
        'disetujui_oleh' => null,
    ]);

    return redirect()
        ->route('register')
        ->with('success', 'Akun Anda telah berhasil didaftarkan dan sedang menunggu proses aktivasi oleh admin. Silakan cek status akun secara berkala dengan mencoba login menggunakan email dan password yang telah didaftarkan.');
}

    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah'])
                ->withInput();
        }

        if ($pengguna->status_akun === 'pending') {
            return back()
                ->withErrors(['email' => 'Akun Anda belum diaktivasi oleh admin'])
                ->withInput();
        }

        if ($pengguna->status_akun === 'nonaktif') {
            return back()
                ->withErrors(['email' => 'Akun Anda sudah dinonaktifkan'])
                ->withInput();
        }

        if (!$pengguna->id_role) {
            return back()
                ->withErrors(['email' => 'Role akun belum ditetapkan, silakan hubungi Admin'])
                ->withInput();
        }

        Auth::login($pengguna);

        $request->session()->regenerate();

        return $this->redirectByRole($pengguna);
    }

    // Mengarahkan pengguna berdasarkan role sistem
    private function redirectByRole(Pengguna $pengguna)
    {
        $role = $pengguna->role?->nama_role;

        if ($role === 'Admin') {
            return redirect()->route('admin.manajemenpengguna');
        }

        if ($role === 'Direktur') {
            return redirect()->route('direktur.dashboard');
        }

        if ($role === 'Ketua Tim') {
            return redirect()->route('ketuatim.dashboard');
        }

        if ($role === 'Anggota') {
            return redirect()->route('anggota.proyekaktivitas');
        }

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Role pengguna tidak dikenali. Silakan hubungi admin.']);
    }

    // Logout pengguna
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}