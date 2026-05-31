<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi akun baru
     */
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
            'id_role' => null, 
            'status_akun' => 'pending', 
            'disetujui_pada' => null,
            'disetujui_oleh' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Akun berhasil didaftarkan. Silakan menunggu proses aktivasi dan penempatan tim oleh admin.');
    }

    /**
     * Menampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses login dengan pengecekan status, role, dan penempatan tim
     */
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

        // 1. Validasi Kredensial (Email & Password)
        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withInput()
                ->with('error', 'Email atau password salah');
        }

        // 2. Validasi Status Akun
        if ($pengguna->status_akun === 'pending') {
            return back()
                ->withInput()
                ->with('error', 'Akun Anda belum diaktivasi oleh admin.');
        }

        if ($pengguna->status_akun === 'nonaktif') {
            return back()
                ->withInput()
                ->with('error', 'Akun Anda sudah dinonaktifkan.');
        }

        // 3. Validasi Penetapan Role
        if (!$pengguna->id_role) {
            return back()
                ->withInput()
                ->with('error', 'Peran (Role) akun Anda belum ditetapkan oleh Admin.');
        }

        // 4. Validasi Penempatan Tim (Kecuali Admin)
        if ($pengguna->role?->nama_role !== 'Admin') {
            $isKetua = DB::table('tim_kerja')
                ->where('id_ketua_tim', $pengguna->id_pengguna)
                ->exists();
            
            $isAnggota = DB::table('anggota_tim')
                ->where('id_pengguna', $pengguna->id_pengguna)
                ->exists();

            if (!$isKetua && !$isAnggota) {
                return back()
                    ->withInput()
                    ->with('error', 'Akun Anda aktif, namun belum ditempatkan dalam Tim Kerja. Silakan hubungi Admin.');
            }
        }

        // 5. LOGIN (Menggunakan $request->boolean('remember') untuk fitur "Ingat Saya")
        // Laravel secara otomatis menggunakan kolom "remember_token" di database
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole($pengguna);
        }

        return back()->withInput()->with('error', 'Gagal masuk ke sistem.');
    }

    /**
     * Redirect berdasarkan Role
     */
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
            ->with('error', 'Role tidak dikenali. Silakan hubungi admin.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}