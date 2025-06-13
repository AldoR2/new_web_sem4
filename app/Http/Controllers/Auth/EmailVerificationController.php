<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Notifications\CustomVerifyEmail;

class EmailVerificationController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.validasi_awal');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $mahasiswa = Mahasiswa::where('email', $request->email)->first();

        if (!$mahasiswa) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di Mahasiswa manapun..']);
        }

        if ($mahasiswa->email_verified_at) {
            return redirect()->route('login')->with('status', 'Email sudah diverifikasi, Silahkan Melanjutkan login');
        }

        // Kirim link verifikasi email (pakai notifikasi custom)
        $mahasiswa->notify(new CustomVerifyEmail());

        return back()->with('status', 'Link verifikasi telah dikirim ke email Anda.');
    }
}
