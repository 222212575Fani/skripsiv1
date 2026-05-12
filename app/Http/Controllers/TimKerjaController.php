<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimKerjaController extends Controller
{
    public function index()
    {
        // Ambil data tim dari tabel 'tim'
        $tims = DB::table('tim_kerja')->get();
        
        return view('admin.manajementimkerja', compact('tims'));
    }
}