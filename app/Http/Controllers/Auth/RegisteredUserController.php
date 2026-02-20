<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'numcad'   => ['required', 'integer', 'unique:RH_USERS,numcad'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:RH_USERS,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 🔍 busca no Senior
        $colab = DB::connection('oracle_senior')->selectOne("
            SELECT DISTINCT
                R.NUMCAD AS NUMCAD,
                R.NOMFUN AS NOMFUN
            FROM VETORH.R034FUN R
            WHERE R.SITAFA <> 7
              AND R.NUMCAD = :numcad
        ", [
            'numcad' => (int) $data['numcad'],
        ]);

        if (!$colab) {
            return back()->withErrors([
                'numcad' => 'Matrícula não encontrada no Senior ou colaborador inativo.',
            ])->onlyInput('email');
        }

        // ⚠️ Oracle pode retornar em minúsculo dependendo do driver
        $numcad = $colab->NUMCAD ?? $colab->numcad;
        $nomfun = $colab->NOMFUN ?? $colab->nomfun;

        $user = User::create([
            'numcad'   => (int) $numcad,
            'name'     => trim((string) $nomfun),
            'email'    => mb_strtolower(trim($data['email']), 'UTF-8'),
            'password' => Hash::make($data['password']),
            'role'     => 'RH',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }

}
