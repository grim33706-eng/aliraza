<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * "Forgot password?" for the admin: emails a one-time link, then sets a new password.
 */
class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.forgot-password', [
            'mailIsLogged' => in_array(config('mail.default'), ['log', 'array'], true),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // The reply is the same whether or not the email matches, so the form
        // can't be used to find out which address is the admin's.
        Password::sendResetLink($request->only('email'));

        return to_route('admin.password.request')->with('reset_sent', $request->input('email'));
    }

    public function edit(Request $request, string $token): View
    {
        return view('admin.auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->onlyInput('email')->withErrors([
                'email' => $status === Password::INVALID_TOKEN
                    ? 'This reset link is invalid or has expired. Please request a new one.'
                    : 'We couldn\'t reset the password for that email.',
            ]);
        }

        return to_route('admin.login')->withInput($request->only('email'))->with('toast', [
            'type' => 'success', 'icon' => 'lock',
            'title' => 'Password updated',
            'message' => 'Sign in with your new password.',
        ]);
    }
}
