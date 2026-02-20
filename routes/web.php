<?php

use App\Http\Controllers\AvaliacaoPerfilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('avaliacoes.index');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => false,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Dashboard (logado + verificado)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rotas protegidas (usuário logado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Autocomplete de colaboradores (Senior)
    Route::get('/colaboradores', [AvaliacaoPerfilController::class, 'colaboradores'])
        ->name('colaboradores.index');

    // Verificar se colaborador pode ser avaliado (regra dos 6 meses)
    Route::get('/colaboradores/check', [AvaliacaoPerfilController::class, 'checkColaborador'])
        ->name('colaboradores.check');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/foto', [ProfileController::class, 'updateFoto'])->name('profile.updateFoto');
    Route::delete('/profile/foto', [ProfileController::class, 'destroyFoto'])->name('profile.destroyFoto');
    Route::get('/profile/senha', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avaliações de perfil
    Route::get('/avaliacoes', [AvaliacaoPerfilController::class, 'index'])
        ->name('avaliacoes.index');

    Route::get('/avaliacoes/nova', [AvaliacaoPerfilController::class, 'create'])
        ->name('avaliacoes.create');

    Route::post('/avaliacoes', [AvaliacaoPerfilController::class, 'store'])
        ->name('avaliacoes.store');

    Route::get('/avaliacoes/{avaliacao}', [AvaliacaoPerfilController::class, 'show'])
        ->name('avaliacoes.show');

    Route::get('/avaliacoes/{avaliacao}/editar', [AvaliacaoPerfilController::class, 'edit'])
        ->name('avaliacoes.edit');

    Route::put('/avaliacoes/{avaliacao}', [AvaliacaoPerfilController::class, 'update'])
        ->name('avaliacoes.update');

    Route::patch('/avaliacoes/{avaliacao}/finalizar', [AvaliacaoPerfilController::class, 'finalizar'])
        ->name('avaliacoes.finalizar');

    // Gerenciamento de usuários (somente RH)
    Route::middleware('role.rh')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/usuarios/novo', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/usuarios', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/usuarios/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::patch('/usuarios/{user}/toggle-ativo', [UserController::class, 'toggleAtivo'])
            ->name('users.toggleAtivo');
    });
});

/*
|--------------------------------------------------------------------------
| Autenticação (login / senha)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
