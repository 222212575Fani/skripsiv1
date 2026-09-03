<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use App\Models\Pengguna;
use App\Models\TimKerja; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    // Menampilkan halaman utama manajemen pengguna disertai fungsionalitas pencarian dan filter tab data
    public function index(Request $request)
    {
        // Membuka kueri data dasar objek model Pengguna beserta pemanggilan relasi tabel role
        $query = Pengguna::with(['role']);

        // Menyaring data record pengguna berdasarkan klasifikasi status akun jika kategori filter dipilih
        if ($request->filled('status') && $request->status != 'semua') {
            $status = ($request->status == 'non-aktif') ? 'nonaktif' : $request->status;
            $query->where('status_akun', $status);
        }

        // Menyaring data record pengguna berdasarkan kata kunci Nama atau NIP jika kolom pencarian diisi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        // Mengeksekusi penarikan data koleksi terurut dengan pembatasan halaman (pagination) 10 baris data
        $users = $query->latest()->paginate(10)->withQueryString();

        // Melakukan transformasi koleksi data guna memetakan informasi alokasi tim kerja secara dinamis
        $users->getCollection()->transform(function ($user) {
            $timAsKetua = DB::table('tim_kerja')
                ->where('id_ketua_tim', $user->id_pengguna)
                ->first();

            if ($timAsKetua) {
                $user->nama_tim = $timAsKetua->nama_tim;
                $user->id_tim_aktif = $timAsKetua->id_tim;
            } else {
                $timAsAnggota = DB::table('anggota_tim')
                    ->join('tim_kerja', 'anggota_tim.id_tim', '=', 'tim_kerja.id_tim')
                    ->where('anggota_tim.id_pengguna', $user->id_pengguna)
                    ->select('tim_kerja.nama_tim', 'tim_kerja.id_tim')
                    ->first();

                $user->nama_tim = $timAsAnggota ? $timAsAnggota->nama_tim : '-';
                $user->id_tim_aktif = $timAsAnggota ? $timAsAnggota->id_tim : ''; 
            }
            return $user;
        });

        $roles = DB::table('role')->get(); 
        
        // Memuat data tim aktif sekaligus mendeteksi apakah tim tersebut sudah memiliki ketua yang valid & berstatus "Ketua Tim"
        $tims = DB::table('tim_kerja')->where('status_tim', 'aktif')->get()->map(function ($tim) {
            $sudahPunyaKetua = false;
            
            if (!empty($tim->id_ketua_tim)) {
                // Cek apakah pengguna yang memegang id_ketua_tim ini akunnya aktif DAN rolenya benar-benar memegang role Ketua
                $cekKetua = DB::table('pengguna')
                    ->join('role', 'pengguna.id_role', '=', 'role.id_role')
                    ->where('pengguna.id_pengguna', $tim->id_ketua_tim)
                    ->where('pengguna.status_akun', 'aktif')
                    ->where('role.nama_role', 'LIKE', '%ketua%')
                    ->exists();

                $sudahPunyaKetua = $cekKetua;
            }

            $tim->sudah_punya_ketua = $sudahPunyaKetua;
            return $tim;
        }); 

        $counts = [
            'semua'    => Pengguna::count(),
            'pending'  => Pengguna::where('status_akun', 'pending')->count(),
            'aktif'    => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.manajemenpengguna', compact('users', 'counts', 'roles', 'tims'));
    }

    public function aktivasi(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_role'     => 'required',
            'id_tim'      => 'nullable|exists:tim_kerja,id_tim'
        ]);

        try {
            DB::beginTransaction();

            $user = Pengguna::findOrFail($request->id_pengguna);
            
            $user->status_akun = 'aktif';
            $user->id_role = $request->id_role; 
            $user->disetujui_pada = now();
            $user->save();

            $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
            $namaRole = $roleData ? strtolower($roleData->nama_role) : '';
            
            $isRoleKetua = strpos($namaRole, 'ketua') !== false;
            $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

            // Jika bukan role global (Admin/Direktur) dan ada tim yang dipilih
            if (!$isRoleGlobal && $request->filled('id_tim')) {
                if ($isRoleKetua) {
                    DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                        'id_ketua_tim' => $user->id_pengguna,
                        'updated_at'   => now(),
                    ]);
                }

                DB::table('anggota_tim')->updateOrInsert(
                    ['id_pengguna' => $user->id_pengguna],
                    [
                        'id_tim'            => $request->id_tim,
                        'tanggal_bergabung' => now(), 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal aktivasi: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:100',
            'nip'               => 'required|string|size:18|unique:pengguna,nip',
            'nama_email_baru'   => [
                'required',
                'email',
                'unique:pengguna,email',
                'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/'
            ],
            'kata_sandi_baru'   => 'required|min:8', 
            'status_akun'       => 'required|in:aktif,pending,nonaktif',
            'id_role'           => 'required|exists:role,id_role',
            'id_tim'            => 'nullable|exists:tim_kerja,id_tim',
        ], [
            'nip.size'              => 'NIP harus 18 digit.',
            'nip.unique'            => 'NIP ini sudah terdaftar di sistem BPS.',
            'nama_email_baru.regex' => 'Email wajib menggunakan domain resmi @bps.go.id.',
            'nama_email_baru.unique'=> 'Email ini sudah digunakan.',
            'kata_sandi_baru.min'   => 'Password minimal harus 8 karakter.',
            'id_role.required'      => 'Kamu harus menentukan Peran (Role) terlebih dahulu.'
        ]);

        DB::beginTransaction();
        try {
            $disetujuiPada = ($request->status_akun === 'aktif') ? now() : null;

            $pengguna = Pengguna::create([
                'nama'           => $request->nama,
                'nip'            => $request->nip,
                'email'          => $request->nama_email_baru, 
                'password'       => Hash::make($request->kata_sandi_baru), 
                'status_akun'    => $request->status_akun,
                'id_role'        => $request->id_role,
                'disetujui_pada' => $disetujuiPada,
            ]);

            $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
            $namaRole = $roleData ? strtolower($roleData->nama_role) : '';
            
            $isRoleKetua = strpos($namaRole, 'ketua') !== false;
            $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

            if (!$isRoleGlobal && $request->filled('id_tim')) {
                if ($isRoleKetua) {
                    DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                        'id_ketua_tim' => $pengguna->id_pengguna,
                        'updated_at'   => now(),
                    ]);
                } else {
                    DB::table('anggota_tim')->insert([
                        'id_tim'            => $request->id_tim,
                        'id_pengguna'       => $pengguna->id_pengguna,
                        'tanggal_bergabung' => now(), 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah pengguna: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'nama'        => 'required|string|max:100',
            'nip'         => 'required|string|size:18|unique:pengguna,nip,' . $request->id_pengguna . ',id_pengguna',
            'status_akun' => 'required|in:aktif,pending,nonaktif',
            'id_role'     => 'nullable|exists:role,id_role',
            'id_tim'      => 'nullable|exists:tim_kerja,id_tim',
        ], [
            'nip.size'   => 'NIP wajib berisi tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah digunakan oleh pengguna lain.',
        ]);

        DB::beginTransaction();
        try {
            $user = Pengguna::findOrFail($request->id_pengguna);

            $disetujuiPada = $user->disetujui_pada;

            if ($request->status_akun === 'aktif' && $user->status_akun !== 'aktif') {
                $disetujuiPada = now();
            } elseif ($request->status_akun !== 'aktif') {
                $disetujuiPada = null;
            }

            // 1. Update data utama tabel pengguna
            $user->update([
                'nama'           => $request->nama,
                'nip'            => $request->nip,
                'status_akun'    => $request->status_akun,
                'id_role'        => $request->id_role,
                'disetujui_pada' => $disetujuiPada,
            ]);

            // Cek role pengguna
            $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
            $namaRole = $roleData ? strtolower($roleData->nama_role) : '';
            
            $isRoleKetua = strpos($namaRole, 'ketua') !== false;
            $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

            // Bersihkan dulu status ketua lama jika pengguna ini sebelumnya adalah ketua di tim manapun
            DB::table('tim_kerja')->where('id_ketua_tim', $user->id_pengguna)->update(['id_ketua_tim' => null]);

            if ($isRoleGlobal) {
                // Jika perannya Admin atau Direktur, pastikan bersih dari relasi tim manapun
                DB::table('tim_kerja')->where('id_ketua_tim', $user->id_pengguna)->update(['id_ketua_tim' => null]);
                DB::table('anggota_tim')->where('id_pengguna', $user->id_pengguna)->delete();
            } else {
                if ($request->filled('id_tim')) {
                    if ($isRoleKetua) {
                        // Jika dia menjadi ketua di tim yang dipilih
                        DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                            'id_ketua_tim' => $user->id_pengguna,
                            'updated_at'   => now(),
                        ]);
                    }

                    // Masukkan atau perbarui ke tabel anggota_tim
                    DB::table('anggota_tim')->updateOrInsert(
                        ['id_pengguna' => $user->id_pengguna],
                        [
                            'id_tim'            => $request->id_tim,
                            'tanggal_bergabung' => now(),
                            'updated_at'        => now(),
                            'created_at'        => now(),
                        ]
                    );
                } else {
                    // Jika pilihan tim dikosongkan, hapus dari anggota_tim
                    DB::table('anggota_tim')->where('id_pengguna', $user->id_pengguna)->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}