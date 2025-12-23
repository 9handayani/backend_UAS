<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menangani registrasi user baru (Hanya Nama, Email, Password)
     */
    public function register(Request $request)
    {
        // 1. Validasi: Hapus phone dan address dari aturan karena tidak ada di form
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' untuk mencocokkan konfirmasi password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Data pendaftaran tidak valid'
            ], 422);
        }

        // 2. Simpan user: Hapus phone dan address dari create agar tidak error di database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Gunakan 'customer' agar tidak 'Data truncated'
        ]);

        return response()->json([
            'message' => 'Registrasi Berhasil',
            'user' => $user
        ], 201);
    }

    /**
     * Menangani login untuk Admin maupun Customer
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek kecocokan kredensial
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau Password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login Berhasil',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }
}