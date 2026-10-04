<?php

namespace Tests\Feature;

use App\Models\AktivitasProyek;
use App\Models\AnggotaTim;
use App\Models\Pengguna;
use App\Models\Proyek;
use App\Models\Role;
use App\Models\TimKerja;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * =========================================================================
 * PENGUJIAN OTOMATIS ATURAN BISNIS PROXIS
 * Mengulang, lewat kode, skenario test case black-box yang sudah dinyatakan "Sesuai",
 * ditambah perbaikan yang dibuat setelahnya. Tiap tes diberi kode kasus uji (mis. AUTH-05)
 * supaya mudah ditunjukkan saat sidang.
 *
 * PENTING: jalankan hanya pada basis data uji terpisah, bukan basis data asli, mis.
 *   DB_CONNECTION=mysql DB_DATABASE=proxis_test php artisan test --filter=AturanBisnisTest
 * =========================================================================
 */
class AturanBisnisTest extends TestCase
{
    use RefreshDatabase;

    private int $nipTerakhir = 200000000000000000;

    // ---------------------------------------------------------------------
    // PERALATAN: membuat data contoh (role, pengguna, tim, proyek, aktivitas)
    // ---------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        // Peran proyek tetap: 1 = Ketua Proyek, 2 = Anggota
        DB::table('peran_proyek')->insertOrIgnore([
            ['id_peran_proyek' => 1, 'nama_peran_proyek' => 'Ketua Proyek', 'created_at' => now(), 'updated_at' => now()],
            ['id_peran_proyek' => 2, 'nama_peran_proyek' => 'Anggota', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function role(string $nama): Role
    {
        return Role::firstOrCreate(['nama_role' => $nama]);
    }

    private function pengguna(string $nama, string $role = 'Anggota', string $status = 'aktif'): Pengguna
    {
        $this->nipTerakhir++;

        return Pengguna::create([
            'nama' => $nama,
            'nip' => (string) $this->nipTerakhir,
            'email' => strtolower(str_replace(' ', '', $nama)).'@bps.go.id',
            'password' => bcrypt('password123'),
            'id_role' => $this->role($role)->id_role,
            'status_akun' => $status,
        ]);
    }

    private function tim(Pengguna $ketua, string $nama = 'Tim A', string $status = 'aktif'): TimKerja
    {
        $tim = TimKerja::create(['nama_tim' => $nama, 'id_ketua_tim' => $ketua->id_pengguna, 'status_tim' => $status]);
        AnggotaTim::create(['id_tim' => $tim->id_tim, 'id_pengguna' => $ketua->id_pengguna, 'tanggal_bergabung' => now()]);

        return $tim;
    }

    private function masukTim(Pengguna $pengguna, TimKerja $tim, ?string $keluar = null): AnggotaTim
    {
        return AnggotaTim::create([
            'id_tim' => $tim->id_tim,
            'id_pengguna' => $pengguna->id_pengguna,
            'tanggal_bergabung' => now(),
            'tanggal_keluar' => $keluar,
        ]);
    }

    /** Proyek dengan rentang: mulai +5 hari, target selesai +30 hari (aktivitas baru harus di dalam rentang ini). */
    private function proyek(TimKerja $tim, Pengguna $ketuaProyek, array $atribut = []): Proyek
    {
        $proyek = Proyek::create(array_merge([
            'id_tim' => $tim->id_tim,
            'nama_proyek' => 'Proyek Uji',
            'id_ketua_proyek' => $ketuaProyek->id_pengguna,
            'tanggal_mulai' => Carbon::today()->addDays(5)->toDateString(),
            'tanggal_target_selesai' => Carbon::today()->addDays(30)->toDateString(),
        ], $atribut));

        DB::table('anggota_proyek')->insert([
            'id_proyek' => $proyek->id_proyek, 'id_pengguna' => $ketuaProyek->id_pengguna,
            'id_peran_proyek' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $proyek;
    }

    private function anggotaProyek(Proyek $proyek, Pengguna $pengguna): void
    {
        DB::table('anggota_proyek')->insert([
            'id_proyek' => $proyek->id_proyek, 'id_pengguna' => $pengguna->id_pengguna,
            'id_peran_proyek' => 2, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function aktivitas(Proyek $proyek, Pengguna $pj, array $atribut = []): AktivitasProyek
    {
        return AktivitasProyek::create(array_merge([
            'id_proyek' => $proyek->id_proyek,
            'nama_aktivitas' => 'Aktivitas Uji',
            'id_penanggung_jawab' => $pj->id_pengguna,
            'tanggal_mulai' => Carbon::today()->subDays(2)->toDateString(),
            'tanggal_target_selesai' => Carbon::today()->addDays(10)->toDateString(),
            'target' => 0,
        ], $atribut));
    }

    // =====================================================================
    // 1. REGISTRASI DAN LOGIN (AUTH)
    // =====================================================================

    public function test_auth01_registrasi_valid_membuat_akun_pending_dan_memberi_tahu_admin(): void
    {
        $admin = $this->pengguna('Admin Utama', 'Admin');

        $this->post(route('register.post'), [
            'nama' => 'Pegawai Baru', 'nip' => '199001012020011001', 'email' => 'baru@bps.go.id',
            'password' => 'rahasia88', 'password_confirmation' => 'rahasia88',
        ])->assertRedirect(route('login'));

        $baru = Pengguna::where('email', 'baru@bps.go.id')->first();
        $this->assertSame('pending', $baru->status_akun);
        $this->assertNull($baru->id_role);
        $this->assertSame('Registrasi Pengguna Baru', $admin->notifications()->first()->data['name']);
    }

    public function test_auth02_sampai_auth11_validasi_registrasi(): void
    {
        $this->pengguna('Sudah Ada');
        $dasar = ['nama' => 'X', 'nip' => '199001012020011002', 'email' => 'x@bps.go.id', 'password' => 'rahasia88', 'password_confirmation' => 'rahasia88'];

        $kasus = [
            ['nip' => '12345678901234567'] + ['_pesan' => ['nip' => 'NIP harus terdiri dari tepat 18 digit angka.']],   // AUTH-02
            ['nip' => '1234567890123456789'] + ['_pesan' => ['nip' => 'NIP harus terdiri dari tepat 18 digit angka.']],  // AUTH-03
            ['nip' => '19900101202001100A'] + ['_pesan' => ['nip' => 'NIP hanya boleh berupa angka (18 digit).']],       // AUTH-04
            ['nip' => '200000000000000001'] + ['_pesan' => ['nip' => 'NIP ini sudah terdaftar di sistem BPS.']],         // AUTH-05
            ['email' => 'nama@gmail.com'] + ['_pesan' => ['email' => 'Email wajib menggunakan domain resmi kantor @bps.go.id.']], // AUTH-06
            ['email' => 'nama.bps.go.id'] + ['_pesan' => ['email' => 'Format email tidak valid.']],                       // AUTH-07
            ['email' => 'sudahada@bps.go.id'] + ['_pesan' => ['email' => 'Email ini sudah terdaftar di sistem.']],        // AUTH-08
            ['password' => '1234567', 'password_confirmation' => '1234567'] + ['_pesan' => ['password' => 'Kata sandi minimal 8 karakter.']], // AUTH-09
            ['password_confirmation' => 'berbeda99'] + ['_pesan' => ['password' => 'Konfirmasi kata sandi tidak cocok.']], // AUTH-11
        ];

        foreach ($kasus as $k) {
            $pesan = $k['_pesan'];
            unset($k['_pesan']);
            $this->post(route('register.post'), array_merge($dasar, $k))->assertSessionHasErrors($pesan);
        }

        // AUTH-10: kata sandi tepat 8 karakter diterima
        $this->post(route('register.post'), array_merge($dasar, ['password' => '12345678', 'password_confirmation' => '12345678']))
            ->assertSessionHasNoErrors();
    }

    public function test_auth13_sampai_16_login_per_role_diarahkan_ke_halaman_masing_masing(): void
    {
        $admin = $this->pengguna('Admin A', 'Admin');
        $direktur = $this->pengguna('Direktur A', 'Direktur');
        $ketua = $this->pengguna('Ketua A', 'Ketua Tim');
        $this->tim($ketua);
        $anggota = $this->pengguna('Anggota A');
        $this->masukTim($anggota, TimKerja::first());

        foreach ([
            [$admin, 'admin.manajemenpengguna'], [$direktur, 'direktur.dashboard'],
            [$ketua, 'ketuatim.dashboard'], [$anggota, 'anggota.proyekaktivitas'],
        ] as [$pengguna, $rute]) {
            $this->post(route('login.post'), ['email' => $pengguna->email, 'password' => 'password123'])
                ->assertRedirect(route($rute));
            auth()->logout();
        }
    }

    public function test_auth17_sampai_21_login_ditolak_untuk_kredensial_dan_status_yang_tidak_sah(): void
    {
        $this->pengguna('Aktif', 'Admin');
        $this->pengguna('Pending Orang', 'Anggota', 'pending');
        $this->pengguna('Nonaktif Orang', 'Anggota', 'nonaktif');
        $this->pengguna('Tanpa Tim', 'Anggota');

        // AUTH-17 dan AUTH-18: pesan sama untuk kata sandi salah dan email tidak terdaftar
        $salah = 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.';
        $this->post(route('login.post'), ['email' => 'aktif@bps.go.id', 'password' => 'salah'])->assertSessionHas('error', $salah);
        $this->post(route('login.post'), ['email' => 'acak@bps.go.id', 'password' => 'salah'])->assertSessionHas('error', $salah);

        // AUTH-19: pending, AUTH-20: nonaktif, AUTH-21: anggota aktif tanpa tim
        $this->post(route('login.post'), ['email' => 'pendingorang@bps.go.id', 'password' => 'password123'])->assertSessionHas('account_pending');
        $this->post(route('login.post'), ['email' => 'nonaktiforang@bps.go.id', 'password' => 'password123'])->assertSessionHas('error', 'Akun Anda sudah dinonaktifkan.');
        $this->post(route('login.post'), ['email' => 'tanpatim@bps.go.id', 'password' => 'password123'])
            ->assertSessionHas('error', 'Akun Anda aktif, namun belum ditempatkan dalam Tim Kerja. Silakan hubungi Admin.');
        $this->assertGuest();
    }

    public function test_auth28_keanggotaan_lama_yang_sudah_ditutup_tidak_dihitung_saat_login(): void
    {
        $ketua = $this->pengguna('Ketua B', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $keluar = $this->pengguna('Sudah Keluar');
        $this->masukTim($keluar, $tim, now()->toDateString());   // tanggal keluar terisi

        $this->post(route('login.post'), ['email' => $keluar->email, 'password' => 'password123'])
            ->assertSessionHas('error', 'Akun Anda aktif, namun belum ditempatkan dalam Tim Kerja. Silakan hubungi Admin.');
        $this->assertGuest();
    }

    public function test_auth23_akun_yang_dinonaktifkan_saat_login_dikeluarkan_pada_permintaan_berikutnya(): void
    {
        $ketua = $this->pengguna('Ketua C', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $anggota = $this->pengguna('Anggota C');
        $this->masukTim($anggota, $tim);

        $this->actingAs($anggota)->get(route('anggota.proyekaktivitas'))->assertOk();

        $anggota->update(['status_akun' => 'nonaktif']);

        $this->get(route('anggota.proyekaktivitas'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.');
    }

    public function test_auth26_27_halaman_tidak_di_cache_dan_halaman_dalam_butuh_login(): void
    {
        // AUTH-27: tanpa login diarahkan ke login
        $this->get(route('anggota.proyekaktivitas'))->assertRedirect(route('login'));

        // AUTH-26: halaman dilarang disimpan peramban, sehingga tombol Back tidak menampilkan halaman lama
        $this->get(route('login'))->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $this->get(route('login'))->headers->get('Cache-Control'));
    }

    public function test_alamat_utama_tanpa_landing_page_diarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_pembatasan_role_pengguna_dialihkan_ke_halaman_perannya_sendiri(): void
    {
        $ketua = $this->pengguna('Ketua D', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $anggota = $this->pengguna('Anggota D');
        $this->masukTim($anggota, $tim);

        // Anggota membuka halaman Admin dan Direktur: dialihkan ke halamannya sendiri
        $this->actingAs($anggota)->get(route('admin.manajemenpengguna'))->assertRedirect(route('anggota.proyekaktivitas'));
        $this->actingAs($anggota)->get(route('direktur.dashboard'))->assertRedirect(route('anggota.proyekaktivitas'));
        // Ketua Tim membuka halaman Admin
        $this->actingAs($ketua)->get(route('admin.manajemenpengguna'))->assertRedirect(route('ketuatim.dashboard'));
    }

    // =====================================================================
    // 2. ADMIN: PENGGUNA (USR)
    // =====================================================================

    public function test_usr07_sampai_11_aktivasi_akun_pending(): void
    {
        $admin = $this->pengguna('Admin E', 'Admin');
        $ketua = $this->pengguna('Ketua E', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $kosong = TimKerja::create(['nama_tim' => 'Tim Kosong', 'id_ketua_tim' => $this->pengguna('Ketua Lama', 'Ketua Tim', 'nonaktif')->id_pengguna, 'status_tim' => 'aktif']);

        $anggotaBaru = $this->pengguna('Calon Anggota', 'Anggota', 'pending');
        $calonKetua = $this->pengguna('Calon Ketua', 'Anggota', 'pending');
        $calonKetua2 = $this->pengguna('Calon Ketua Dua', 'Anggota', 'pending');
        $calonDirektur = $this->pengguna('Calon Direktur', 'Anggota', 'pending');

        // USR-07: aktivasi sebagai Anggota -> aktif, masuk tim, Ketua Tim menerima notifikasi
        $this->actingAs($admin)->post(route('admin.aktivasi'), ['id_pengguna' => $anggotaBaru->id_pengguna, 'id_role' => $this->role('Anggota')->id_role, 'id_tim' => $tim->id_tim]);
        $this->assertSame('aktif', $anggotaBaru->fresh()->status_akun);
        $this->assertTrue(AnggotaTim::where('id_pengguna', $anggotaBaru->id_pengguna)->where('id_tim', $tim->id_tim)->whereNull('tanggal_keluar')->exists());
        $this->assertSame(1, $ketua->notifications()->count());

        // USR-08: Ketua Tim untuk tim yang ketuanya tidak aktif -> menjadi ketua tim itu
        $this->actingAs($admin)->post(route('admin.aktivasi'), ['id_pengguna' => $calonKetua->id_pengguna, 'id_role' => $this->role('Ketua Tim')->id_role, 'id_tim' => $kosong->id_tim]);
        $this->assertSame($calonKetua->id_pengguna, $kosong->fresh()->id_ketua_tim);

        // USR-09: Ketua Tim untuk tim yang ketuanya masih aktif -> ditolak
        $this->actingAs($admin)->post(route('admin.aktivasi'), ['id_pengguna' => $calonKetua2->id_pengguna, 'id_role' => $this->role('Ketua Tim')->id_role, 'id_tim' => $tim->id_tim])
            ->assertSessionHas('error');
        $this->assertSame('pending', $calonKetua2->fresh()->status_akun);

        // USR-10: Direktur tanpa tim
        $this->actingAs($admin)->post(route('admin.aktivasi'), ['id_pengguna' => $calonDirektur->id_pengguna, 'id_role' => $this->role('Direktur')->id_role]);
        $this->assertSame('aktif', $calonDirektur->fresh()->status_akun);

        // USR-11: tanpa role -> ditolak
        $this->actingAs($admin)->post(route('admin.aktivasi'), ['id_pengguna' => $calonKetua2->id_pengguna])
            ->assertSessionHasErrors(['id_role' => 'Silakan tentukan peran (role) untuk pengguna ini.']);
    }

    public function test_usr12_sampai_18_tambah_pengguna_oleh_admin(): void
    {
        $admin = $this->pengguna('Admin F', 'Admin');
        $ketua = $this->pengguna('Ketua F', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $this->pengguna('Pegawai Lama');

        $dasar = [
            'nama' => 'Pegawai Baru', 'nip' => '199001012020011003', 'nama_email_baru' => 'pegawaibaru@bps.go.id',
            'kata_sandi_baru' => 'rahasia88', 'status_akun' => 'aktif', 'id_role' => $this->role('Anggota')->id_role, 'id_tim' => $tim->id_tim,
        ];

        // USR-13 sampai USR-17: penolakan
        $this->actingAs($admin);
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['nip' => '200000000000000002']))->assertSessionHasErrors(['nip' => 'NIP ini sudah terdaftar di sistem BPS.']);
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['nama_email_baru' => 'pegawailama@bps.go.id']))->assertSessionHasErrors(['nama_email_baru' => 'Email ini sudah digunakan oleh pengguna lain.']);
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['nama_email_baru' => 'nama@yahoo.com']))->assertSessionHasErrors(['nama_email_baru' => 'Email wajib menggunakan domain resmi kantor @bps.go.id.']);
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['kata_sandi_baru' => '1234567']))->assertSessionHasErrors(['kata_sandi_baru' => 'Kata sandi minimal harus 8 karakter.']);
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['id_role' => '']))->assertSessionHasErrors(['id_role' => 'Kamu harus menentukan Peran (Role) terlebih dahulu.']);

        // USR-18: Ketua Tim untuk tim yang sudah punya ketua
        $this->post(route('admin.pengguna.store'), array_merge($dasar, ['id_role' => $this->role('Ketua Tim')->id_role]))->assertSessionHas('error');

        // USR-12: valid -> tersimpan, bisa langsung login
        $this->post(route('admin.pengguna.store'), $dasar)->assertSessionHas('success');
        $this->assertTrue(AnggotaTim::where('id_pengguna', Pengguna::where('nip', '199001012020011003')->value('id_pengguna'))->whereNull('tanggal_keluar')->exists());
        auth()->logout();
        $this->post(route('login.post'), ['email' => 'pegawaibaru@bps.go.id', 'password' => 'rahasia88'])->assertRedirect(route('anggota.proyekaktivitas'));
    }

    public function test_usr20_sampai_26_ubah_pengguna_menutup_dan_membuka_keanggotaan_tim(): void
    {
        $admin = $this->pengguna('Admin G', 'Admin');
        $ketuaA = $this->pengguna('Ketua A1', 'Ketua Tim');
        $timA = $this->tim($ketuaA, 'Tim A');
        $ketuaB = $this->pengguna('Ketua B1', 'Ketua Tim');
        $timB = $this->tim($ketuaB, 'Tim B');
        $anggota = $this->pengguna('Anggota G');
        $this->masukTim($anggota, $timA);
        $roleAnggota = $this->role('Anggota')->id_role;
        $this->actingAs($admin);

        // USR-22: dipindah dari tim A ke tim B -> tim A ditutup (tanggal keluar), tim B tercatat, Ketua Tim B diberi tahu
        $this->post(route('admin.pengguna.update'), ['id_pengguna' => $anggota->id_pengguna, 'nama' => 'Anggota G', 'status_akun' => 'aktif', 'id_role' => $roleAnggota, 'id_tim' => $timB->id_tim]);
        $this->assertNotNull(AnggotaTim::where('id_pengguna', $anggota->id_pengguna)->where('id_tim', $timA->id_tim)->value('tanggal_keluar'));
        $this->assertTrue(AnggotaTim::where('id_pengguna', $anggota->id_pengguna)->where('id_tim', $timB->id_tim)->whereNull('tanggal_keluar')->exists());
        $this->assertSame(1, $ketuaB->notifications()->count());

        // USR-26: Daftar Pengguna menampilkan tim yang AKTIF (tim B), bukan tim lama (tim A)
        $daftar = $this->get(route('admin.manajemenpengguna'))->viewData('users');
        $this->assertSame('Tim B', $daftar->firstWhere('id_pengguna', $anggota->id_pengguna)->nama_tim);

        // USR-20: dinonaktifkan -> keanggotaan ditutup
        $this->post(route('admin.pengguna.update'), ['id_pengguna' => $anggota->id_pengguna, 'nama' => 'Anggota G', 'status_akun' => 'nonaktif', 'id_role' => $roleAnggota]);
        $this->assertSame(0, AnggotaTim::where('id_pengguna', $anggota->id_pengguna)->whereNull('tanggal_keluar')->count());

        // USR-24: Ketua Tim yang masih menjabat tidak boleh diturunkan/dinonaktifkan langsung
        $this->post(route('admin.pengguna.update'), ['id_pengguna' => $ketuaA->id_pengguna, 'nama' => 'Ketua A1', 'status_akun' => 'nonaktif', 'id_role' => $this->role('Ketua Tim')->id_role, 'id_tim' => $timA->id_tim])
            ->assertSessionHas('error');
        $this->assertSame('aktif', $ketuaA->fresh()->status_akun);
    }

    // =====================================================================
    // 3. ADMIN: TIM KERJA (TIM)
    // =====================================================================

    public function test_tim04_sampai_10_tambah_tim_kerja(): void
    {
        $admin = $this->pengguna('Admin H', 'Admin');
        $calon = $this->pengguna('Calon Ketua H');
        $lain = $this->pengguna('Ketua Lain', 'Ketua Tim');
        $this->tim($lain, 'Tim Lain');
        $this->actingAs($admin);

        // TIM-05, 06, 08, 09, 10: penolakan
        $this->post(route('admin.timkerja.store'), ['nama_tim' => '', 'id_ketua_tim' => $calon->id_pengguna, 'status_tim' => 'aktif'])->assertSessionHasErrors(['nama_tim' => 'Nama tim kerja wajib diisi.']);
        $this->post(route('admin.timkerja.store'), ['nama_tim' => 'AB', 'id_ketua_tim' => $calon->id_pengguna, 'status_tim' => 'aktif'])->assertSessionHasErrors(['nama_tim' => 'Nama tim kerja minimal 3 karakter.']);
        $this->post(route('admin.timkerja.store'), ['nama_tim' => 'Tim Lain', 'id_ketua_tim' => $calon->id_pengguna, 'status_tim' => 'aktif'])->assertSessionHasErrors(['nama_tim' => 'Nama tim ini sudah digunakan. Silakan gunakan nama lain.']);
        $this->post(route('admin.timkerja.store'), ['nama_tim' => 'Tim Baru', 'status_tim' => 'aktif'])->assertSessionHasErrors(['id_ketua_tim' => 'Ketua tim wajib dipilih.']);
        $this->post(route('admin.timkerja.store'), ['nama_tim' => 'Tim Baru', 'id_ketua_tim' => $lain->id_pengguna, 'status_tim' => 'aktif'])->assertSessionHas('error');

        // TIM-07: tepat 3 karakter diterima; TIM-04: ketua otomatis jadi Ketua Tim dan menerima notifikasi
        $this->post(route('admin.timkerja.store'), ['nama_tim' => 'ABC', 'id_ketua_tim' => $calon->id_pengguna, 'status_tim' => 'aktif']);
        $this->assertSame($this->role('Ketua Tim')->id_role, $calon->fresh()->id_role);
        $this->assertSame('Penugasan Ketua Tim Baru', $calon->notifications()->first()->data['name']);
    }

    public function test_tim13_16_ganti_ketua_membuat_mantan_ketua_menjadi_anggota_tim_yang_sama(): void
    {
        $admin = $this->pengguna('Admin I', 'Admin');
        $ani = $this->pengguna('Ani', 'Ketua Tim');
        // Ani diangkat lewat jalur yang TIDAK membuat baris anggota_tim (seperti Tambah Pengguna/Aktivasi)
        $tim = TimKerja::create(['nama_tim' => 'Tim Ani', 'id_ketua_tim' => $ani->id_pengguna, 'status_tim' => 'aktif']);
        $dodi = $this->pengguna('Dodi');
        $this->masukTim($dodi, $tim);

        $this->actingAs($admin)->post(route('admin.timkerja.update'), [
            'id_tim' => $tim->id_tim, 'nama_tim' => 'Tim Ani', 'id_ketua_tim' => $dodi->id_pengguna, 'status_tim' => 'aktif',
        ])->assertSessionHas('success');

        // TIM-13: ketua lama turun jadi Anggota, ketua baru naik dan diberi notifikasi
        $this->assertSame($this->role('Anggota')->id_role, $ani->fresh()->id_role);
        $this->assertSame($this->role('Ketua Tim')->id_role, $dodi->fresh()->id_role);
        $this->assertSame('Penugasan Ketua Tim Baru', $dodi->notifications()->first()->data['name']);

        // TIM-16: Ani tetap tercatat sebagai anggota AKTIF tim itu, jadi bisa login dan tampil di daftar
        $this->assertTrue(AnggotaTim::where('id_pengguna', $ani->id_pengguna)->where('id_tim', $tim->id_tim)->whereNull('tanggal_keluar')->exists());
        auth()->logout();
        $this->post(route('login.post'), ['email' => $ani->email, 'password' => 'password123'])->assertRedirect(route('anggota.proyekaktivitas'));
    }

    public function test_tim15_tim_nonaktif_tidak_muncul_sebagai_pilihan(): void
    {
        $admin = $this->pengguna('Admin J', 'Admin');
        $this->tim($this->pengguna('Ketua J1', 'Ketua Tim'), 'Tim Aktif');
        $this->tim($this->pengguna('Ketua J2', 'Ketua Tim'), 'Tim Mati', 'nonaktif');

        $tims = $this->actingAs($admin)->get(route('admin.manajemenpengguna'))->viewData('tims');
        $this->assertSame(['Tim Aktif'], $tims->pluck('nama_tim')->all());
    }

    // =====================================================================
    // 4. KETUA TIM: PROYEK (PRY)
    // =====================================================================

    private function dasarProyek(Pengguna $ketuaProyek, Pengguna $anggota): array
    {
        return [
            'nama_proyek' => 'Proyek Baru', 'deskripsi' => 'Uraian', 'id_ketua_proyek' => $ketuaProyek->id_pengguna,
            'tanggal_mulai' => Carbon::today()->addDays(3)->toDateString(),
            'tenggat_waktu' => Carbon::today()->addDays(30)->toDateString(),
            'anggota_proyek' => [$anggota->id_pengguna],
        ];
    }

    public function test_pry06_sampai_13_dan_21_sampai_23_tambah_proyek(): void
    {
        $ketuaTim = $this->pengguna('Ketua K', 'Ketua Tim');
        $tim = $this->tim($ketuaTim);
        $kp = $this->pengguna('Ketua Proyek K');
        $ang = $this->pengguna('Anggota K');
        $this->masukTim($kp, $tim);
        $this->masukTim($ang, $tim);
        $this->actingAs($ketuaTim);
        $dasar = $this->dasarProyek($kp, $ang);

        // PRY-08, 09, 10: penolakan dasar
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['id_ketua_proyek' => '']))->assertSessionHasErrors(['id_ketua_proyek' => 'Pilih salah satu Ketua Proyek dari anggota tim.']);
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['nama_proyek' => '']))->assertSessionHasErrors(['nama_proyek' => 'Nama proyek wajib diisi.']);
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['tanggal_mulai' => Carbon::today()->addDays(10)->toDateString(), 'tenggat_waktu' => Carbon::today()->addDays(5)->toDateString()]))
            ->assertSessionHasErrors(['tenggat_waktu' => 'Target tanggal selesai tidak boleh mendahului tanggal mulai proyek.']);

        // PRY-21: tanggal mulai wajib; PRY-22: anggota wajib; PRY-23: ketua tidak dihitung sebagai anggota
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['tanggal_mulai' => '']))->assertSessionHasErrors(['tanggal_mulai' => 'Tanggal mulai proyek wajib diisi.']);
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['anggota_proyek' => []]))->assertSessionHasErrors(['anggota_proyek' => 'Pilih minimal satu anggota proyek.']);
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['anggota_proyek' => [$kp->id_pengguna]]))->assertSessionHas('error', 'Pilih minimal satu anggota proyek selain Ketua Proyek.');
        $this->assertSame(0, Proyek::count());

        // PRY-11: tanggal selesai sama dengan tanggal mulai diterima
        $sama = Carbon::today()->addDays(4)->toDateString();
        $this->post(route('ketuatim.manajemenproyek.store'), array_merge($dasar, ['nama_proyek' => 'Satu Hari', 'tanggal_mulai' => $sama, 'tenggat_waktu' => $sama]))->assertSessionHas('success');

        // PRY-06 dan PRY-13: tersimpan; Ketua Proyek tercatat peran 1, anggota peran 2
        $this->post(route('ketuatim.manajemenproyek.store'), $dasar)->assertSessionHas('success', 'Proyek baru berhasil ditambahkan!');
        $proyek = Proyek::where('nama_proyek', 'Proyek Baru')->first();
        $this->assertSame(1, (int) DB::table('anggota_proyek')->where('id_proyek', $proyek->id_proyek)->where('id_pengguna', $kp->id_pengguna)->value('id_peran_proyek'));
        $this->assertSame(2, (int) DB::table('anggota_proyek')->where('id_proyek', $proyek->id_proyek)->where('id_pengguna', $ang->id_pengguna)->value('id_peran_proyek'));
        $this->assertSame('belum_dimulai', $proyek->status_proyek);   // PRY-12: status dihitung otomatis
    }

    public function test_pry19_tim_nonaktif_tidak_dapat_menerima_proyek_baru(): void
    {
        $ketuaTim = $this->pengguna('Ketua L', 'Ketua Tim');
        $tim = $this->tim($ketuaTim, 'Tim L', 'nonaktif');
        $kp = $this->pengguna('Ketua Proyek L');
        $ang = $this->pengguna('Anggota L');

        $this->actingAs($ketuaTim)->post(route('ketuatim.manajemenproyek.store'), $this->dasarProyek($kp, $ang))->assertSessionHas('error');
        $this->assertSame(0, Proyek::count());
    }

    public function test_pry14_sampai_21_ubah_dan_hapus_proyek(): void
    {
        $ketuaTim = $this->pengguna('Ketua M', 'Ketua Tim');
        $tim = $this->tim($ketuaTim);
        $kp1 = $this->pengguna('Ketua Proyek M1');
        $kp2 = $this->pengguna('Ketua Proyek M2');
        $proyek = $this->proyek($tim, $kp1);
        $this->actingAs($ketuaTim);

        $berubah = ['nama_proyek' => 'Nama Baru', 'id_ketua_proyek' => $kp1->id_pengguna, 'tanggal_mulai' => Carbon::today()->addDays(5)->toDateString(), 'tenggat_waktu' => Carbon::today()->addDays(40)->toDateString()];

        // PRY-21: tanggal mulai wajib juga saat mengubah
        $this->put(route('ketuatim.proyek.update', $proyek->id_proyek), array_merge($berubah, ['tanggal_mulai' => '']))->assertSessionHasErrors(['tanggal_mulai']);
        // PRY-14: ubah data valid
        $this->put(route('ketuatim.proyek.update', $proyek->id_proyek), $berubah)->assertSessionHas('success', 'Data proyek berhasil diperbarui!');
        $this->assertSame('Nama Baru', $proyek->fresh()->nama_proyek);
        // PRY-15: ganti Ketua Proyek, ketua baru diberi notifikasi
        $this->put(route('ketuatim.proyek.update', $proyek->id_proyek), array_merge($berubah, ['id_ketua_proyek' => $kp2->id_pengguna]));
        $this->assertSame($kp2->id_pengguna, $proyek->fresh()->id_ketua_proyek);
        $this->assertSame('Penugasan Ketua Proyek', $kp2->notifications()->first()->data['name']);

        // PRY-18: proyek yang sudah punya aktivitas tidak boleh dihapus; PRY-16: tanpa aktivitas boleh
        $pj = $this->pengguna('PJ M');
        $this->aktivitas($proyek, $pj);
        $this->delete(route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek))->assertSessionHas('error');
        $this->assertNotNull(Proyek::find($proyek->id_proyek));
        AktivitasProyek::where('id_proyek', $proyek->id_proyek)->delete();
        $this->delete(route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek))->assertSessionHas('success');
        $this->assertNull(Proyek::find($proyek->id_proyek));
    }

    // =====================================================================
    // 5. KETUA PROYEK: AKTIVITAS (AKT)
    // =====================================================================

    /** Skenario umum: tim, Ketua Proyek (peran akun Anggota), satu anggota proyek, proyek dengan rentang +5 s.d. +30 hari. */
    private function skenarioAktivitas(): array
    {
        $ketuaTim = $this->pengguna('Ketua N', 'Ketua Tim');
        $tim = $this->tim($ketuaTim);
        $kp = $this->pengguna('Ketua Proyek N');
        $pj = $this->pengguna('PJ N');
        $this->masukTim($kp, $tim);
        $this->masukTim($pj, $tim);
        $proyek = $this->proyek($tim, $kp);
        $this->anggotaProyek($proyek, $pj);

        return [$ketuaTim, $kp, $pj, $proyek];
    }

    private function dataAktivitas(Pengguna $pj, array $ubah = []): array
    {
        return array_merge([
            'nama_aktivitas' => 'Aktivitas Baru', 'deskripsi_aktivitas' => 'Uraian',
            'id_penanggung_jawab' => $pj->id_pengguna,
            'tanggal_mulai' => Carbon::today()->addDays(6)->toDateString(),
            'tanggal_target_selesai' => Carbon::today()->addDays(20)->toDateString(),
        ], $ubah);
    }

    public function test_akt06_sampai_14_tambah_aktivitas(): void
    {
        [, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        $this->actingAs($kp);
        $url = route('anggota.aktivitas.store', $proyek->id_proyek);
        $batas = $proyek->tanggal_target_selesai;

        // AKT-07, 08: nama pendek dan penanggung jawab kosong
        $this->post($url, $this->dataAktivitas($pj, ['nama_aktivitas' => 'AB']))->assertSessionHasErrors(['nama_aktivitas' => 'Nama aktivitas minimal 3 karakter.']);
        $this->post($url, $this->dataAktivitas($pj, ['id_penanggung_jawab' => '']))->assertSessionHasErrors(['id_penanggung_jawab' => 'Penanggung jawab aktivitas wajib dipilih.']);
        // AKT-09, 10: tanggal mulai sebelum proyek atau di masa lalu
        $pesanMulai = 'Tanggal mulai aktivitas tidak boleh mendahului hari ini atau tanggal mulai proyek.';
        $this->post($url, $this->dataAktivitas($pj, ['tanggal_mulai' => Carbon::today()->addDays(2)->toDateString()]))->assertSessionHasErrors(['tanggal_mulai' => $pesanMulai]);
        $this->post($url, $this->dataAktivitas($pj, ['tanggal_mulai' => Carbon::yesterday()->toDateString()]))->assertSessionHasErrors(['tanggal_mulai' => $pesanMulai]);
        // AKT-11: target melewati batas proyek; AKT-13: target sebelum mulai
        $this->post($url, $this->dataAktivitas($pj, ['tanggal_target_selesai' => Carbon::parse($batas)->addDay()->toDateString()]))->assertSessionHasErrors(['tanggal_target_selesai' => 'Tanggal target selesai tidak boleh melebihi batas target selesai proyek.']);
        $this->post($url, $this->dataAktivitas($pj, ['tanggal_mulai' => Carbon::today()->addDays(15)->toDateString(), 'tanggal_target_selesai' => Carbon::today()->addDays(10)->toDateString()]))
            ->assertSessionHasErrors(['tanggal_target_selesai' => 'Tanggal target selesai tidak boleh lebih awal dari tanggal mulai aktivitas.']);
        $this->assertSame(0, AktivitasProyek::count());

        // AKT-12: target tepat di batas proyek diterima; AKT-06: progres awal 0%
        $this->post($url, $this->dataAktivitas($pj, ['tanggal_target_selesai' => $batas]))->assertSessionHas('success');
        $this->assertSame(0.0, AktivitasProyek::first()->target);
    }

    public function test_akt14_penanggung_jawab_otomatis_tercatat_sebagai_anggota_proyek(): void
    {
        [, $kp, , $proyek] = $this->skenarioAktivitas();
        $baru = $this->pengguna('Anggota Tim Baru');
        $this->masukTim($baru, TimKerja::first());

        $this->actingAs($kp)->post(route('anggota.aktivitas.store', $proyek->id_proyek), $this->dataAktivitas($baru))->assertSessionHas('success');
        $this->assertTrue(DB::table('anggota_proyek')->where('id_proyek', $proyek->id_proyek)->where('id_pengguna', $baru->id_pengguna)->exists());
    }

    public function test_akt15_16_ubah_aktivitas_yang_sudah_berjalan_tidak_memaksa_tanggal_mulai_diganti(): void
    {
        [, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        // Proyek yang sudah berjalan, aktivitas dengan tanggal mulai di masa lalu
        $proyek->update(['tanggal_mulai' => Carbon::today()->subDays(20)->toDateString()]);
        $akt = $this->aktivitas($proyek, $pj, ['tanggal_mulai' => Carbon::today()->subDays(7)->toDateString()]);
        $this->actingAs($kp);

        $data = [
            'nama_aktivitas' => 'Nama Diganti', 'deskripsi_aktivitas' => '', 'id_penanggung_jawab' => $pj->id_pengguna,
            'tanggal_mulai' => $akt->tanggal_mulai, 'tanggal_target_selesai' => Carbon::today()->addDays(15)->toDateString(),
        ];

        // Hanya nama yang diubah, tanggal mulai lama (sudah lewat) dipertahankan -> berhasil
        $this->put(route('anggota.aktivitas.update', $akt->id_aktivitas), $data)->assertSessionHasNoErrors();
        $this->assertSame('Nama Diganti', $akt->fresh()->nama_aktivitas);

        // Tanggal mulai BARU yang mendahului tanggal lama tetap ditolak
        $this->put(route('anggota.aktivitas.update', $akt->id_aktivitas), array_merge($data, ['tanggal_mulai' => Carbon::today()->subDays(10)->toDateString()]))
            ->assertSessionHasErrors(['tanggal_mulai']);
        // AKT-16: target melewati batas proyek ditolak
        $this->put(route('anggota.aktivitas.update', $akt->id_aktivitas), array_merge($data, ['tanggal_target_selesai' => Carbon::parse($proyek->tanggal_target_selesai)->addDay()->toDateString()]))
            ->assertSessionHasErrors(['tanggal_target_selesai']);
    }

    public function test_akt01_02_ketua_proyek_melihat_semua_aktivitas_anggota_biasa_hanya_miliknya(): void
    {
        [, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        $lain = $this->pengguna('PJ Lain');
        $this->masukTim($lain, TimKerja::first());
        $this->anggotaProyek($proyek, $lain);
        $this->aktivitas($proyek, $pj, ['nama_aktivitas' => 'Milik PJ']);
        $this->aktivitas($proyek, $lain, ['nama_aktivitas' => 'Milik Lain']);

        $semua = $this->actingAs($kp)->get(route('anggota.proyek.aktivitas', $proyek->id_proyek))->viewData('aktivitasProyek');
        $this->assertCount(2, $semua);

        $miliknya = $this->actingAs($pj)->get(route('anggota.proyek.aktivitas', $proyek->id_proyek))->viewData('aktivitasProyek');
        $this->assertSame(['Milik PJ'], $miliknya->pluck('nama_aktivitas')->all());
        $this->assertFalse($this->actingAs($pj)->get(route('anggota.proyek.aktivitas', $proyek->id_proyek))->viewData('isKetuaProyek'));

        // AKT-20 (Daftar Aktivitas saya): hanya yang menjadi tanggung jawabnya
        $saya = $this->actingAs($pj)->get(route('anggota.aktivitassaya'))->assertOk();
        $this->assertSame(1, $saya->viewData('counts')['semua']);
    }

    public function test_hak_akses_orang_yang_bukan_ketua_proyek_tidak_boleh_mengelola_aktivitas(): void
    {
        [, , $pj, $proyek] = $this->skenarioAktivitas();

        $this->actingAs($pj)->post(route('anggota.aktivitas.store', $proyek->id_proyek), $this->dataAktivitas($pj))->assertForbidden();
        $this->assertSame(0, AktivitasProyek::count());

        // AKT-03: proyek yang tidak diikuti dibuka lewat URL -> 403
        $asing = $this->pengguna('Orang Asing');
        $this->masukTim($asing, TimKerja::first());
        $this->actingAs($asing)->get(route('anggota.proyek.aktivitas', $proyek->id_proyek))->assertForbidden();
    }

    // =====================================================================
    // 6. ANGGOTA: LAPOR PROGRESS (PRG)
    // =====================================================================

    private function lapor(Pengguna $pelapor, AktivitasProyek $akt, array $data = [])
    {
        return $this->actingAs($pelapor)->post(route('anggota.aktivitas.progress', $akt->id_aktivitas), array_merge([
            'progress_minggu_berjalan' => 0, 'progress_minggu_berjalan_tambahan' => 30, 'uraian_progress' => 'Pekerjaan minggu ini',
        ], $data));
    }

    public function test_prg01_sampai_05_progress_bertambah_dan_menggerakkan_status_aktivitas_dan_proyek(): void
    {
        [$ketuaTim, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        $proyek->update(['tanggal_mulai' => Carbon::today()->subDays(10)->toDateString()]);
        $akt = $this->aktivitas($proyek, $pj);

        // PRG-01: laporan pertama +30 -> 30%, status Berjalan, riwayat mencatat +30
        $this->lapor($pj, $akt)->assertSessionHasNoErrors();
        $this->assertSame(30.0, $akt->fresh()->target);
        $this->assertSame('berjalan', $akt->fresh()->status_aktivitas);
        $this->assertSame(30.0, (float) DB::table('progress_aktivitas')->where('id_aktivitas', $akt->id_aktivitas)->value('progress_minggu_berjalan'));
        $this->assertSame(30.0, $proyek->fresh()->persen_progress);   // satu aktivitas: progres proyek = progres aktivitas

        // PRG-02: laporan kedua +30 -> 60%
        $this->lapor($pj, $akt->fresh(), ['progress_minggu_berjalan' => 30])->assertSessionHasNoErrors();
        $this->assertSame(60.0, $akt->fresh()->target);

        // PRG-05: tepat 100% -> Selesai, tanggal selesai aktual terisi, proyek ikut selesai
        $this->lapor($pj, $akt->fresh(), ['progress_minggu_berjalan' => 60, 'progress_minggu_berjalan_tambahan' => 40]);
        $this->assertSame(100.0, $akt->fresh()->target);
        $this->assertSame('selesai', $akt->fresh()->status_aktivitas);
        $this->assertNotNull($akt->fresh()->tanggal_selesai_aktual);
        $this->assertSame('selesai', $proyek->fresh()->status_proyek);
    }

    public function test_progres_proyek_adalah_rata_rata_progres_aktivitasnya(): void
    {
        [, , $pj, $proyek] = $this->skenarioAktivitas();
        $this->aktivitas($proyek, $pj, ['target' => 100]);
        $this->aktivitas($proyek, $pj, ['target' => 0]);

        $this->assertSame(50.0, $proyek->fresh()->persen_progress);
    }

    public function test_prg06_07_validasi_batas_progress_di_server(): void
    {
        [, , $pj, $proyek] = $this->skenarioAktivitas();
        $akt = $this->aktivitas($proyek, $pj);

        // PRG-06: 101 ditolak, PRG-07: negatif ditolak, PRG-09: kosong ditolak
        $this->lapor($pj, $akt, ['progress_minggu_berjalan' => 0, 'progress_minggu_berjalan_tambahan' => 101])->assertSessionHasErrors(['progress_minggu_berjalan_tambahan']);
        $this->lapor($pj, $akt, ['progress_minggu_berjalan' => 0, 'progress_minggu_berjalan_tambahan' => -1])->assertSessionHasErrors(['progress_minggu_berjalan_tambahan']);
        $this->lapor($pj, $akt, ['progress_minggu_berjalan' => null, 'progress_minggu_berjalan_tambahan' => null])->assertSessionHasErrors(['progress_minggu_berjalan']);
        $this->assertSame(0.0, $akt->fresh()->target);
    }

    public function test_prg_progress_tidak_pernah_melebihi_100_walau_dikirim_berlebih(): void
    {
        [, , $pj, $proyek] = $this->skenarioAktivitas();
        $akt = $this->aktivitas($proyek, $pj, ['target' => 90]);

        $this->lapor($pj, $akt, ['progress_minggu_berjalan' => 90, 'progress_minggu_berjalan_tambahan' => 20]);
        $this->assertSame(100.0, $akt->fresh()->target);
    }

    public function test_prg19_hanya_penanggung_jawab_yang_boleh_melapor(): void
    {
        [, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        $lain = $this->pengguna('Bukan PJ');
        $this->masukTim($lain, TimKerja::first());
        $this->anggotaProyek($proyek, $lain);
        $akt = $this->aktivitas($proyek, $pj);

        $this->lapor($lain, $akt)->assertForbidden();
        $this->lapor($kp, $akt)->assertForbidden();   // Ketua Proyek pun tidak boleh melapor atas nama PJ
        $this->assertSame(0.0, $akt->fresh()->target);
        $this->assertSame(0, DB::table('progress_aktivitas')->count());
    }

    public function test_prg_kendala_dokumen_dan_notifikasi_laporan(): void
    {
        Storage::fake('public');
        [$ketuaTim, $kp, $pj, $proyek] = $this->skenarioAktivitas();
        $akt = $this->aktivitas($proyek, $pj);

        // Dokumen dengan format tidak didukung dan ukuran lebih dari 5 MB ditolak
        $this->lapor($pj, $akt, ['dokumen_pendukung' => [UploadedFile::fake()->create('virus.exe', 10)]])->assertSessionHasErrors(['dokumen_pendukung.0']);
        $this->lapor($pj, $akt, ['dokumen_pendukung' => [UploadedFile::fake()->create('besar.pdf', 6000)]])->assertSessionHasErrors(['dokumen_pendukung.0']);
        $this->assertSame(0, DB::table('progress_aktivitas')->count());

        // Laporan lengkap: kendala + dokumen tersimpan, Ketua Proyek dan Ketua Tim diberi tahu, pelapor tidak
        $this->lapor($pj, $akt, ['kendala_internal' => 'Alat kurang', 'kendala_eksternal' => 'Data terlambat', 'dokumen_pendukung' => [UploadedFile::fake()->create('bukti.pdf', 100)]])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, DB::table('kendala_aktivitas')->where('id_aktivitas', $akt->id_aktivitas)->count());
        $this->assertSame(1, DB::table('dokumen_pendukung')->where('id_aktivitas', $akt->id_aktivitas)->count());
        $this->assertSame(1, $kp->notifications()->count());
        $this->assertSame(1, $ketuaTim->notifications()->count());
        $this->assertSame(0, $pj->notifications()->count());
    }

    public function test_status_aktivitas_otomatis_terlambat_bila_lewat_target_dan_belum_100(): void
    {
        [, , $pj, $proyek] = $this->skenarioAktivitas();
        $akt = $this->aktivitas($proyek, $pj, ['tanggal_mulai' => Carbon::today()->subDays(20)->toDateString(), 'tanggal_target_selesai' => Carbon::today()->subDay()->toDateString(), 'target' => 50]);

        $this->assertSame('terlambat', $akt->fresh()->status_aktivitas);
    }

    // =====================================================================
    // 7. DIREKTUR: DASHBOARD DAN GRAFIK
    // =====================================================================

    public function test_dashboard_direktur_menghitung_status_proyek_dan_terlambat_baru_setelah_tenggat_lewat(): void
    {
        $direktur = $this->pengguna('Direktur Z', 'Direktur');
        $ketua = $this->pengguna('Ketua Z', 'Ketua Tim');
        $tim = $this->tim($ketua);
        $kp = $this->pengguna('KP Z');

        // Tenggat HARI INI: belum terlambat (berjalan). Tenggat kemarin: terlambat. Satu lagi belum dimulai.
        $this->proyek($tim, $kp, ['nama_proyek' => 'Tenggat Hari Ini', 'tanggal_mulai' => Carbon::today()->subDays(10)->toDateString(), 'tanggal_target_selesai' => Carbon::today()->toDateString()]);
        $this->proyek($tim, $kp, ['nama_proyek' => 'Sudah Lewat', 'tanggal_mulai' => Carbon::today()->subDays(10)->toDateString(), 'tanggal_target_selesai' => Carbon::yesterday()->toDateString()]);
        $this->proyek($tim, $kp, ['nama_proyek' => 'Belum Mulai']);

        $stat = $this->actingAs($direktur)->get(route('direktur.dashboard'))->viewData('statsDirektorat');
        $this->assertSame(3, $stat['total']);
        $this->assertSame(1, $stat['berjalan']);
        $this->assertSame(1, $stat['terlambat']);
        $this->assertSame(1, $stat['belum_dimulai']);
    }

    public function test_grafik_progres_tim_memakai_rata_rata_proyek_dan_filter_bulan_lintas_tahun(): void
    {
        $direktur = $this->pengguna('Direktur Y', 'Direktur');
        $ketua = $this->pengguna('Ketua Y', 'Ketua Tim');
        $tim = $this->tim($ketua, 'Tim Y');
        $kp = $this->pengguna('KP Y');
        $pj = $this->pengguna('PJ Y');

        // Proyek 15 Nov 2026 s.d. 10 Jan 2027, progres 40%. Harus ikut dihitung pada filter Desember 2026.
        $p = $this->proyek($tim, $kp, ['tanggal_mulai' => '2026-11-15', 'tanggal_target_selesai' => '2027-01-10']);
        $this->aktivitas($p, $pj, ['tanggal_mulai' => '2026-11-15', 'tanggal_target_selesai' => '2027-01-10', 'target' => 40]);

        $json = $this->actingAs($direktur)->getJson(route('direktur.chart.data', ['tahun' => 2026, 'bulan' => 12]))->assertOk()->json();
        $this->assertSame(['Tim Y'], $json['namaTim']);
        $this->assertEquals([40.0], $json['rerataProgressTim']);
    }

    public function test_grafik_beban_kerja_tidak_memuat_anggota_yang_sudah_keluar_dari_tim(): void
    {
        $direktur = $this->pengguna('Direktur X', 'Direktur');
        $ketua = $this->pengguna('Ketua X', 'Ketua Tim');
        $tim = $this->tim($ketua, 'Tim X');
        $aktif = $this->pengguna('Anggota Aktif');
        $keluar = $this->pengguna('Anggota Keluar');
        $this->masukTim($aktif, $tim);
        $this->masukTim($keluar, $tim, now()->toDateString());

        $json = $this->actingAs($direktur)->getJson(route('direktur.chart.bebankerja', ['id_tim' => $tim->id_tim, 'tahun' => 'all', 'bulan' => 'all']))->assertOk()->json();
        $this->assertContains('Anggota Aktif', $json['labels']);
        $this->assertNotContains('Anggota Keluar', $json['labels']);
    }

    // =====================================================================
    // 8. NOTIFIKASI (lonceng)
    // =====================================================================

    public function test_notifikasi_lonceng_menghitung_yang_belum_dibaca_dan_menandai_terbaca(): void
    {
        $admin = $this->pengguna('Admin W', 'Admin');
        $this->post(route('register.post'), ['nama' => 'Baru W', 'nip' => '199001012020011009', 'email' => 'baruw@bps.go.id', 'password' => 'rahasia88', 'password_confirmation' => 'rahasia88']);

        $cek = $this->actingAs($admin)->getJson(route('notifications.check'))->assertOk()->json();
        $this->assertSame(1, $cek['unread_count']);
        $this->assertTrue($cek['has_unread']);

        $this->postJson(route('notifications.readAll'))->assertOk();
        $this->assertSame(0, $this->getJson(route('notifications.check'))->json('unread_count'));
    }
}
