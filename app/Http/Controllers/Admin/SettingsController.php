<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\MediaStore;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['maxUpload' => MediaStore::maxUploadBytes()]);
    }

    public function updateProfile(Request $request, MediaStore $media): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);

        if ($request->hasFile('avatar')) {
            try {
                $stored = $media->storeImage($request->file('avatar'), maxWidth: 400, square: true);
            } catch (RuntimeException $e) {
                return back()->withErrors(['avatar' => $e->getMessage()], 'profile')->withInput();
            }
            $media->delete($user->avatar);
            $user->avatar = $stored['path'];
        } elseif ($request->boolean('remove_avatar')) {
            $media->delete($user->avatar);
            $user->avatar = null;
        }

        $user->save();

        return to_route('admin.settings')->with('toast', [
            'type' => 'success', 'icon' => 'user',
            'title' => 'Profile updated',
            'message' => 'Your name, email and photo are saved.',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'Your current password is incorrect.',
        ]);

        $request->user()->update(['password' => $data['password']]);
        $request->session()->regenerate();

        return to_route('admin.settings')->with('toast', [
            'type' => 'success', 'icon' => 'lock',
            'title' => 'Password changed',
            'message' => 'Use the new one next time you sign in.',
        ]);
    }
}
