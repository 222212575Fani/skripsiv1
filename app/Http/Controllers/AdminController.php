<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        //Mengambil semua data pengguna 
        $users = Pengguna::with(['role', 'anggotaTim.tim'])->paginate(10);
        return view('admin.manajemenpengguna', compact('users'));
    }
}
