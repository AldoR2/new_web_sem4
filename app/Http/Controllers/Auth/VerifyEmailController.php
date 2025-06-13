<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Mahasiswa;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */

    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        if (! URL::hasValidSignature($request)) {
            abort(403, 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        if (! hash_equals(sha1($mahasiswa->getEmailForVerification()), $hash)) {
            abort(403, 'Hash email tidak cocok.');
        }

        if ($mahasiswa->email_verified_at) {
            return redirect()->route('login')->with('status', 'Email sudah diverifikasi.');
        }

        $mahasiswa->email_verified_at = now();
        $mahasiswa->save();

        return redirect()->route('login')->with('status', 'Email berhasil diverifikasi. Silahkan login dengan nim anda');
    }
}
