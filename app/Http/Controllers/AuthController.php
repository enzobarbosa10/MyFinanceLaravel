<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\LoginAttempt;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 6)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors(['email' => "Muitas tentativas. Tente novamente em {$seconds} segundos."])
                ->onlyInput('email');
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($throttleKey, 60);

        LoginAttempt::create([
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit($request->userAgent(), 500),
            'attempt_time' => now(),
        ]);

        return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        Category::seedDefaults($user->id);

        $user->sendEmailVerificationNotification();

        return redirect()->route('login')->with('success', 'Cadastro realizado! Verifique seu e-mail para ativar a conta.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
