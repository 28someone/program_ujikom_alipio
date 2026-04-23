<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = $credentials['username'];
        $password = $credentials['password'];

        $user = User::query()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower($username)])
            ->get()
            ->first(fn (User $candidate) => $candidate->username === $username);

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password tidak valid. Perhatikan huruf besar dan kecil pada username.',
            ])->onlyInput('username');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLogger::log($request->user(), 'auth.login', 'Pengguna login ke dalam sistem.');

        return redirect()->route('dashboard')->with('success', 'Login berhasil.');
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'classCategories' => ClassCategory::orderBy('name')->get(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'student_id' => ['required', 'string', 'max:50', 'unique:users,student_id'],
            'class_category_id' => ['required', 'exists:class_categories,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $classCategory = ClassCategory::findOrFail($validated['class_category_id']);

        $user = User::create([
            ...$validated,
            'class_name' => $classCategory->name,
            'role' => 'member',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log(
            $request->user(),
            'auth.register',
            'Pengguna baru melakukan registrasi akun.',
            $user,
            [
                'nama' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'kelas' => $classCategory->name,
            ],
        );

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil, selamat datang.');
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLogger::log($request->user(), 'auth.logout', 'Pengguna logout dari sistem.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
