<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = Pengguna::with(['role', 'anggotaTim.tim'])->paginate(10);
        return view('admin.manajemenpengguna', compact('users'));
    }
}
