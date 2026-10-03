<?php

namespace Tests\Feature;

use App\Models\AktivitasProyek;
use App\Models\Pengguna;
use App\Models\Proyek;
use App\Models\Role;
use App\Models\TimKerja;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pengujian perintah proxis:ingatkan-progress (pengingat laporan progress).
 * Harus dijalankan pada basis data uji terpisah, bukan basis data asli.
 */
class IngatkanProgressTest extends TestCase
{
    use RefreshDatabase;

    private int $nip = 100000000000000000;

    private function buatPengguna(string $nama, string $status = 'aktif'): Pengguna
    {
        $role = Role::firstOrCreate(['nama_role' => 'Anggota']);
        $this->nip++;

        return Pengguna::create([
            'nama' => $nama,
            'nip' => (string) $this->nip,
            'email' => strtolower($nama).'@bps.go.id',
            'password' => bcrypt('password123'),
            'id_role' => $role->id_role,
            'status_akun' => $status,
        ]);
    }

    private function buatAktivitas(Proyek $proyek, Pengguna $pj, array $atribut = []): AktivitasProyek
    {
        return AktivitasProyek::create(array_merge([
            'id_proyek' => $proyek->id_proyek,
            'nama_aktivitas' => 'Aktivitas Uji',
            'id_penanggung_jawab' => $pj->id_pengguna,
            'tanggal_mulai' => Carbon::today()->subDays(20)->toDateString(),
            'tanggal_target_selesai' => Carbon::today()->addDays(30)->toDateString(),
            'target' => 10,
        ], $atribut));
    }

    private function siapkanProyek(): Proyek
    {
        $ketua = $this->buatPengguna('Ketua');
        $tim = TimKerja::create(['nama_tim' => 'Tim Uji', 'id_ketua_tim' => $ketua->id_pengguna, 'status_tim' => 'aktif']);

        return Proyek::create([
            'id_tim' => $tim->id_tim,
            'nama_proyek' => 'Proyek Uji',
            'id_ketua_proyek' => $ketua->id_pengguna,
            'tanggal_mulai' => Carbon::today()->subDays(30)->toDateString(),
            'tanggal_target_selesai' => Carbon::today()->addDays(60)->toDateString(),
        ]);
    }

    public function test_mengingatkan_penanggung_jawab_yang_sudah_7_hari_tidak_melapor(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $this->buatAktivitas($proyek, $pj);

        $this->artisan('proxis:ingatkan-progress')->assertSuccessful();

        $this->assertSame(1, $pj->notifications()->count());
        $data = $pj->notifications()->first()->data;
        $this->assertSame('Pengingat Laporan Progress', $data['name']);
        $this->assertSame('Pengingat', $data['category']);
    }

    public function test_tidak_mengirim_ganda_pada_rentang_yang_sama(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $this->buatAktivitas($proyek, $pj);

        $this->artisan('proxis:ingatkan-progress');
        $this->artisan('proxis:ingatkan-progress');

        $this->assertSame(1, $pj->notifications()->count());
    }

    public function test_tidak_mengingatkan_bila_baru_saja_melapor(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $akt = $this->buatAktivitas($proyek, $pj);
        DB::table('progress_aktivitas')->insert([
            'id_aktivitas' => $akt->id_aktivitas,
            'id_pengguna' => $pj->id_pengguna,
            'progress_minggu_berjalan' => 10,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->artisan('proxis:ingatkan-progress');

        $this->assertSame(0, $pj->notifications()->count());
    }

    public function test_mengingatkan_bila_laporan_terakhir_sudah_lama(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $akt = $this->buatAktivitas($proyek, $pj);
        DB::table('progress_aktivitas')->insert([
            'id_aktivitas' => $akt->id_aktivitas,
            'id_pengguna' => $pj->id_pengguna,
            'progress_minggu_berjalan' => 10,
            'created_at' => now()->subDays(9),
            'updated_at' => now()->subDays(9),
        ]);

        $this->artisan('proxis:ingatkan-progress');

        $this->assertSame(1, $pj->notifications()->count());
    }

    public function test_tidak_mengingatkan_aktivitas_selesai_belum_dimulai_atau_pj_nonaktif(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $nonaktif = $this->buatPengguna('Nonaktif', 'nonaktif');

        $this->buatAktivitas($proyek, $pj, ['target' => 100]); // sudah selesai
        $this->buatAktivitas($proyek, $pj, [ // belum dimulai
            'tanggal_mulai' => Carbon::today()->addDays(5)->toDateString(),
            'target' => 0,
        ]);
        $this->buatAktivitas($proyek, $nonaktif); // penanggung jawab nonaktif

        $this->artisan('proxis:ingatkan-progress');

        $this->assertSame(0, $pj->notifications()->count());
        $this->assertSame(0, $nonaktif->notifications()->count());
    }

    public function test_opsi_hari_mengubah_batas(): void
    {
        $proyek = $this->siapkanProyek();
        $pj = $this->buatPengguna('Anggota');
        $akt = $this->buatAktivitas($proyek, $pj);
        DB::table('progress_aktivitas')->insert([
            'id_aktivitas' => $akt->id_aktivitas,
            'id_pengguna' => $pj->id_pengguna,
            'progress_minggu_berjalan' => 10,
            'created_at' => now()->subDays(4),
            'updated_at' => now()->subDays(4),
        ]);

        $this->artisan('proxis:ingatkan-progress --hari=3');

        $this->assertSame(1, $pj->notifications()->count());
    }
}
