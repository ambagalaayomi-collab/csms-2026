<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:client,project_manager,engineer'],
        ]);

        User::create([...$data, 'password' => Hash::make($data['password'])]);

        return redirect()->route('home')->with([
            'register_success' => 'Registration successful. Please login.',
            'open_login_modal' => true,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:client,project_manager,engineer'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['login_error' => 'Invalid email, password, or role.'])
                ->withInput($request->only('email', 'role'))->with('open_login_modal', true);
        }

        $request->session()->regenerate();
        return redirect()->intended($this->dashboardRoute(Auth::user()->role))
            ->with('login_success', 'Login successful.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function dashboardRoute(string $role): string
    {
        return route(match ($role) {
            'client' => 'client.dashboard',
            'project_manager' => 'project.manager.dashboard',
            'engineer' => 'engineer.dashboard',
        });
    }
}
