<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Those details don\'t match an admin account.',
            ]);
        }

        $request->session()->regenerate();

        $firstName = strtok($request->user()->name, ' ');

        return redirect()->intended(route('admin.dashboard'))->with('toast', [
            'type' => 'success', 'icon' => 'sparkles',
            'title' => "Welcome back, {$firstName}!",
            'message' => 'You\'re signed in to the admin panel.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login')->with('toast', [
            'type' => 'info', 'icon' => 'logout',
            'title' => 'Signed out',
            'message' => 'You have been signed out safely. See you soon!',
        ]);
    }
}
