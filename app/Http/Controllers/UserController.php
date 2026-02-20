<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::orderByDesc('id')
            ->select(['id', 'numcad', 'name', 'email', 'role', 'ativo', 'created_at'])
            ->paginate(20);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'numcad'   => ['required', 'integer', 'unique:RH_USERS,numcad'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:RH_USERS,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:RH,AVALIADOR'],
        ]);

        // Valida colaborador no Senior (mesmo padrão do RegisteredUserController)
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

        if (! $colab) {
            return back()->withErrors([
                'numcad' => 'Matrícula não encontrada no Senior ou colaborador inativo.',
            ]);
        }

        $numcad = $colab->NUMCAD ?? $colab->numcad;
        $nomfun = $colab->NOMFUN ?? $colab->nomfun;

        User::create([
            'numcad'   => (int) $numcad,
            'name'     => trim((string) $nomfun),
            'email'    => mb_strtolower(trim($data['email']), 'UTF-8'),
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'userEdit' => $user->only(['id', 'numcad', 'name', 'email', 'role']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role'     => ['required', 'in:RH,AVALIADOR'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('users.index');
    }

    public function toggleAtivo(User $user): RedirectResponse
    {
        $user->ativo = $user->ativo === 'S' ? 'N' : 'S';
        $user->save();

        return redirect()->route('users.index');
    }
}
