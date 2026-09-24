<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cookie;

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
            'nip' => 'required|string|size:18|regex:/^[0-9]{18}$/|unique:pengguna,nip',
            'email' => [
                'required',
                'email',
                'max:100',
                'unique:pengguna,email',
                'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/'
            ],
            'password' => 'required|string|min:8|max:100|confirmed',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.size' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.regex' => 'NIP hanya boleh berupa angka (18 digit).',
            'nip.unique' => 'NIP ini sudah terdaftar di sistem BPS.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Alamat email maksimal 100 karakter.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'email.regex' => 'Email wajib menggunakan domain resmi kantor @bps.go.id.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 100 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = Pengguna::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => null, 
            'status_akun' => 'pending', 
            'disetujui_pada' => null,
            'disetujui_oleh' => null,
        ]);

        // Kirim Notifikasi ke Admin menggunakan GeneralNotification (2 parameter: title & message)
        $admins = Pengguna::whereHas('role', function($query) {
            $query->where('nama_role', 'Admin');
        })->get();

        if ($admins->isNotEmpty()) {
            $title = 'Registrasi Pengguna Baru';
            $message = 'Pengguna baru atas nama ' . $user->nama . ' telah mendaftar dan menunggu aktivasi.';
            Notification::send($admins, new GeneralNotification($title, $message));
        }

        return redirect()
            ->route('login')
            ->with('register_success', true)
            ->with('registered_name', $user->nama)
            ->with('registered_email', $user->email);
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
            'email'    => 'required|email|max:100',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'email.max'         => 'Alamat email maksimal 100 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $pengguna = Pengguna::with('role')->where('email', $request->email)->first();

        // 1. Validasi Kredensial (Email & Password)
        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()
                ->withInput()
                ->with('error', 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.');
        }

        // 2. Validasi Status Akun
        if ($pengguna->status_akun === 'pending') {
            return back()
                ->withInput()
                ->with('account_pending', true)
                ->with('pending_name', $pengguna->nama);
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

        // 4. Validasi Penempatan Tim (Kecuali untuk Admin dan Direktur)
        $namaRole = $pengguna->role?->nama_role;
        if ($namaRole !== 'Admin' && $namaRole !== 'Direktur') {
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

        // 5. LOGIN
        $remember = $request->boolean('remember');
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            if ($remember) {
                Cookie::queue('remember_email', $request->email, 60 * 24 * 30); // Simpan email selama 30 hari
            } else {
                Cookie::queue(Cookie::forget('remember_email'));
            }

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