<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;

class AnggotaProyekController extends Controller
{
    public function index()
{
    $userId = auth()->id();

    $proyekKetua = Proyek::whereHas('anggotaProyek', function($query) use ($userId) {
        $query->where('id_pengguna', $userId)->where('id_peran_proyek', 1); 
    })->get();

    $proyekAnggota = Proyek::whereHas('anggotaProyek', function($query) use ($userId) {
        $query->where('id_pengguna', $userId)->where('id_peran_proyek', 2); 
    })->get();

    // Kirim variabel ke view
    return view('anggota.proyek', compact('proyekKetua', 'proyekAnggota'));
}
}