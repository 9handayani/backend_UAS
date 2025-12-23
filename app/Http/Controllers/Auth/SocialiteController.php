<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Cari user berdasarkan email, jika tidak ada maka buat baru
            $user = User::updateOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'name' => $socialUser->getName(),
                'password' => bcrypt(str()->random(16)), // Password random karena login via social
                'email_verified_at' => now(),
            ]);

            // Buat token (Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect kembali ke Next.js sambil membawa token di URL
            // Pastikan URL ini sesuai dengan alamat frontend Next.js Anda
            return redirect("http://localhost:3000/login?token={$token}");

        } catch (\Exception $e) {
            return redirect("http://localhost:3000/login?error=Authentication failed");
        }
    }
}