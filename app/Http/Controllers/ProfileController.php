<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Update the user's profile photo.
     */
    public function updateFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Remove foto anterior se existir
        $fotoAnterior = $user->FOTO ?? $user->foto ?? null;
        if ($fotoAnterior) {
            Storage::disk('public')->delete($fotoAnterior);
        }

        $path = $request->file('foto')->store('avatars', 'public');
        $user->foto = $path;
        $user->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Remove the user's profile photo.
     */
    public function destroyFoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $foto = $user->FOTO ?? $user->foto ?? null;

        if ($foto) {
            Storage::disk('public')->delete($foto);
            $user->foto = null;
            $user->save();
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Display the password change form.
     */
    public function editPassword(): Response
    {
        return Inertia::render('Profile/Password');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
