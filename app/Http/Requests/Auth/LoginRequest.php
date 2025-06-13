<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function authenticate(): void
    // {
    //     $this->ensureIsNotRateLimited();

    //     if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
    //         RateLimiter::hit($this->throttleKey());

    //         throw ValidationException::withMessages([
    //             'username' => trans('auth.failed'),
    //         ]);
    //     }

    //     RateLimiter::clear($this->throttleKey());
    // }

//     public function authenticate(): void
// {
//     $this->ensureIsNotRateLimited();

//     $isEmail = filter_var($this->username, FILTER_VALIDATE_EMAIL);
//     $field = $isEmail ? 'email' : 'nim';

//     $user = \App\Models\User::where($field, $this->username)->first();

//     if (! $user || ! \Hash::check($this->password, $user->password)) {
//         RateLimiter::hit($this->throttleKey());

//         throw ValidationException::withMessages([
//             'username' => trans('auth.failed'),
//         ]);
//     }

//     // Role check based on data consistency
//     if ($isEmail && $user->role === 'mahasiswa') {
//         throw ValidationException::withMessages([
//             'username' => 'Mahasiswa harus login menggunakan NIM.',
//         ]);
//     }

//     if (! $isEmail && $user->role !== 'mahasiswa') {
//         throw ValidationException::withMessages([
//             'username' => 'Admin dan Dosen harus login menggunakan email.',
//         ]);
//     }

//     Auth::login($user, $this->boolean('remember'));

//     RateLimiter::clear($this->throttleKey());
// }

public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $isEmail = filter_var($this->username, FILTER_VALIDATE_EMAIL);
    $field = $isEmail ? 'email' : 'nim';

    // Ambil user berdasarkan email atau nim
    $user = \App\Models\User::where($field, $this->username)->first();

    // Cek apakah user ditemukan dan password cocok
    if (! $user || ! \Hash::check($this->password, $user->password)) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => 'Login gagal. Pastikan username dan password benar.',
            // 'username' => trans('auth.failed'),
        ]);
    }

        // Cek apakah user ditemukan dan password cocok
    // if (! $user) {
    //     RateLimiter::hit($this->throttleKey());

    //     throw ValidationException::withMessages([
    //         'username' => $isEmail ? 'Email tidak terdaftar' : 'NIM tidak ditemukan',
    //         // 'password' => 'Password yang Anda masukkan salah.',
    //     ]);
    // }

    //     if (! \Hash::check($this->password, $user->password)) {
    //     RateLimiter::hit($this->throttleKey());

    //     throw ValidationException::withMessages([
    //         'password' => 'Password yang Anda masukkan salah.',
    //     ]);
    // }

    // Validasi kombinasi field dan role
    if ($isEmail && $user->role === 'mahasiswa') {
        throw ValidationException::withMessages([
            'username' => 'Mahasiswa harus login menggunakan NIM.',
        ]);
    }

    if (! $isEmail && $user->role !== 'mahasiswa') {
        throw ValidationException::withMessages([
            'username' => 'Admin dan Dosen harus login menggunakan email.',
        ]);
    }

    // Khusus mahasiswa, cek verifikasi email di tabel mahasiswa
    if ($user->role === 'mahasiswa') {
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa || is_null($mahasiswa->email_verified_at)) {
            throw ValidationException::withMessages([
                'username' => 'Akun Anda belum diverifikasi. Silakan cek email Anda.',
            ]);
        }
    }

    Auth::login($user, false);

        // ✅ Simpan username/NIM ke cookie jika 'remember' dicentang
    if ($this->boolean('remember_me')) {
        cookie()->queue('cookie_username', $this->username, 60 * 24 * 30); // 30 hari
        cookie()->queue('cookie_ingat', true, 60 * 24 * 30);
    } else {
        cookie()->queue(cookie()->forget('cookie_username'));
        cookie()->queue(cookie()->forget('cookie_ingat'));
    }

    RateLimiter::clear($this->throttleKey());
}



    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
