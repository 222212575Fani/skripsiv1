<?php

namespace App\Http\Controllers;

// Mengimpor kelas pengendali utama Laravel guna menghubungkan struktur inheritance
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
        // PERBAIKAN: Menghapus 'anggotaTim.tim' karena relasi belum didefinisikan di Model dan kita sudah menggunakan DB Query Builder di bawah
        $query = Pengguna::with(['role']);

        // Menyaring data record pengguna berdasarkan klasifikasi status akun jika kategori filter dipilih
        if ($request->filled('status') && $request->status != 'semua') {
            // Menyelaraskan string parameter 'non-aktif' dari URL menjadi format database asli yaitu 'nonaktif'
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
            // Memeriksa eksistensi ID pengguna sebagai penanggung jawab utama pada tabel master tim_kerja
            $timAsKetua = DB::table('tim_kerja')
                ->where('id_ketua_tim', $user->id_pengguna)
                ->first();

            if ($timAsKetua) {
                $user->nama_tim = $timAsKetua->nama_tim;
                $user->id_tim_aktif = $timAsKetua->id_tim; // Diperlukan untuk form Modal Edit
            } else {
                // Memeriksa keterlibatan data pengguna sebagai staf anggota aktif pada tabel relasi anggota_tim
                $timAsAnggota = DB::table('anggota_tim')
                    ->join('tim_kerja', 'anggota_tim.id_tim', '=', 'tim_kerja.id_tim')
                    ->where('anggota_tim.id_pengguna', $user->id_pengguna)
                    ->select('tim_kerja.nama_tim', 'tim_kerja.id_tim')
                    ->first();

                $user->nama_tim = $timAsAnggota ? $timAsAnggota->nama_tim : '-';
                $user->id_tim_aktif = $timAsAnggota ? $timAsAnggota->id_tim : ''; // Diperlukan untuk form Modal Edit
            }
            return $user;
        });

        // Menarik data master dari tabel peran dan tabel tim kerja aktif untuk kebutuhan dropdown modal
        $roles = DB::table('role')->get(); 
        $tims = DB::table('tim_kerja')->where('status_tim', 'aktif')->get(); 

        // Menghitung ringkasan jumlah akumulatif per status guna mengisi data komponen badge angka tab filter
        $counts = [
            'semua'    => Pengguna::count(),
            'pending'  => Pengguna::where('status_akun', 'pending')->count(),
            'aktif'    => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        // Mengirimkan seluruh kompilasi variabel penunjang menuju berkas tampilan antarmuka manajemen pengguna
        return view('admin.manajemenpengguna', compact('users', 'counts', 'roles', 'tims'));
    }

    // Memproses otorisasi persetujuan pembukaan hak akses (aktivasi cepat) bagi akun pendaftar pending
    public function aktivasi(Request $request)
    {
        // 1. Validasi Identitas Sasaran: Memastikan record kunci ID pengguna terdaftar secara sahih
        // 2. Validasi Penetapan Peran: Memastikan parameter role terisi oleh sistem
        // 3. Validasi Alokasi Tim Kerja: Memastikan referensi ID tim kerja penempatan terdaftar nyata di database
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_role'     => 'required',
            'id_tim'      => 'nullable|exists:tim_kerja,id_tim'
        ]);

        try {
            // Membuka gerbang transaksi database guna menjaga konsistensi integritas relasi multi-tabel
            DB::beginTransaction();

            $user = Pengguna::findOrFail($request->id_pengguna);
            
            // Memperbarui parameter utama akun pengguna menjadi status aktif
            $user->status_akun = 'aktif';
            $user->id_role = $request->id_role; 
            $user->save();

            // Menyisipkan record penempatan tim kerja baru secara otomatis jika form tim diisi oleh admin
            if ($request->filled('id_tim')) {
                DB::table('anggota_tim')->updateOrInsert(
                    ['id_pengguna' => $user->id_pengguna],
                    [
                        'id_tim'            => $request->id_tim,
                        'tanggal_bergabung' => now(), // Sesuai dengan rancangan basis data logis
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }

            // Meresmikan seluruh rangkaian perubahan secara permanen ke dalam database apabila sukses
            DB::commit();
            return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan!');
            
        } catch (\Exception $e) {
            // Membatalkan seluruh runtunan modifikasi data (rollback) jika mendeteksi adanya kegagalan eksekusi
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal aktivasi: ' . $e->getMessage());
        }
    }

    // Memproses penyimpanan record data pengguna baru yang ditambahkan manual oleh pihak administrator
    public function store(Request $request)
    {
        // 1. Validasi Biodata Pokok: Memastikan keabsahan nama serta kesesuaian struktur NIP 18 angka unik
        // 2. Validasi Akun Surat Elektronik: Memverifikasi alamat e-mail dengan domain wajib @bps.go.id
        // 3. Validasi Komponen Otorisasi: Memastikan parameter sandi, status akun, role, dan tim terpilih valid
        $request->validate([
            'nama'               => 'required|string|max:100',
            'nip'                => 'required|string|size:18|unique:pengguna,nip',
            'nama_email_baru'    => [
                'required',
                'email',
                'unique:pengguna,email',
                'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/'
            ],
            'kata_sandi_baru'    => 'required|min:8', 
            'status_akun'        => 'required|in:aktif,pending,nonaktif',
            'id_role'            => 'required|exists:role,id_role',
            'id_tim'             => 'nullable|exists:tim_kerja,id_tim',
        ], [
            // Menyediakan komponen notifikasi pesan kesalahan kustom berbahasa Indonesia resmi
            'nip.size'              => 'NIP harus 18 digit.',
            'nip.unique'            => 'NIP ini sudah terdaftar di sistem BPS.',
            'nama_email_baru.regex' => 'Email wajib menggunakan domain resmi @bps.go.id.',
            'nama_email_baru.unique'=> 'Email ini sudah digunakan.',
            'kata_sandi_baru.min'   => 'Password minimal harus 8 karakter.',
            'id_role.required'      => 'Kamu harus menentukan Peran (Role) terlebih dahulu.'
        ]);

        // Membuka sesi jaminan transaksi data guna mengantisipasi kegagalan parsial kueri manipulasi data
        DB::beginTransaction();
        try {
            // Mendaftarkan entitas pengguna baru ke tabel pengguna disertai enkripsi password
            $pengguna = Pengguna::create([
                'nama'        => $request->nama,
                'nip'         => $request->nip,
                'email'       => $request->nama_email_baru, 
                'password'    => Hash::make($request->kata_sandi_baru), 
                'status_akun' => $request->status_akun,
                'id_role'     => $request->id_role,
            ]);

            // Menyisipkan entitas pemetaan kelompok ke tabel anggota_tim jika dialokasikan ke suatu kelompok kerja
            if ($request->filled('id_tim')) {
                DB::table('anggota_tim')->insert([
                    'id_tim'            => $request->id_tim,
                    'id_pengguna'       => $pengguna->id_pengguna,
                    'tanggal_bergabung' => now(), // Sesuai dengan rancangan basis data logis
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            // Mengunci penyimpanan berkas jika seluruh kueri berhasil tereksekusi tanpa kendala
            DB::commit();
            return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Mengembalikan kondisi awal basis data apabila terjadi kendala teknis
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah pengguna: ' . $e->getMessage());
        }
    }

    // Memproses pembaruan data pengguna secara menyeluruh via kiriman data form modal edit
    public function update(Request $request)
    {
        // 1. Validasi Target Primer: Memvalidasi ketersediaan target identitas kunci ID pengguna
        // 2. Validasi Mutasi Data NIP: Memeriksa validitas struktur 18 angka NIP serta menjaga keunikan data
        // 3. Validasi Otorisasi Kelompok: Memvalidasi parameter tingkatan hak akses peran, status, beserta referensi ID tim kerja
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'nama'        => 'required|string|max:100',
            'nip'         => 'required|string|size:18|unique:pengguna,nip,' . $request->id_pengguna . ',id_pengguna',
            'status_akun' => 'required|in:aktif,pending,nonaktif',
            'id_role'     => 'nullable|exists:role,id_role',
            'id_tim'      => 'nullable|exists:tim_kerja,id_tim',
        ], [
            // Menyediakan luaran umpan balik pesan peringatan berbahasa Indonesia
            'nip.size'   => 'NIP wajib berisi tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah digunakan oleh pengguna lain.',
        ]);

        // Membuka gerbang transaksi database guna menjaga konsistensi integritas pembaruan multi-tabel
        DB::beginTransaction();
        try {
            $user = Pengguna::findOrFail($request->id_pengguna);

            // Memperbarui atribut sekumpulan kolom primer pada tabel pengguna
            $user->update([
                'nama'        => $request->nama,
                'nip'         => $request->nip,
                'status_akun' => $request->status_akun,
                'id_role'     => $request->id_role,
            ]);

            // Memeriksa status eksistensi pengguna sebagai Ketua Tim untuk mencegah tumpang tindih data
            $isKetua = DB::table('tim_kerja')->where('id_ketua_tim', $user->id_pengguna)->exists();

            if (!$isKetua) {
                if ($request->filled('id_tim')) {
                    // Mensinkronisasikan penambahan atau pembaruan asosiasi keanggotaan tim baru
                    DB::table('anggota_tim')->updateOrInsert(
                        ['id_pengguna' => $user->id_pengguna],
                        [
                            'id_tim'     => $request->id_tim,
                            'updated_at' => now(),
                        ]
                    );
                } else {
                    // Menghapus rekaman keanggotaan kelompok jika admin memilih opsi pengosongan tim
                    DB::table('anggota_tim')->where('id_pengguna', $user->id_pengguna)->delete();
                }
            }

            // Menyimpan seluruh rangkaian perubahan secara permanen ke dalam database
            DB::commit();
            return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
        } catch (\Exception $e) {
            // Membatalkan seluruh runtunan modifikasi data jika mendeteksi adanya kegagalan eksekusi kueri
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}