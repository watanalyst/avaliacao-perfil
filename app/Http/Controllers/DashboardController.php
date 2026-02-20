<?php

namespace App\Http\Controllers;

use App\Models\RhAvaliacaoPerfil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $avaliacoesQuery = RhAvaliacaoPerfil::query();

        // Colaboradores cuja última avaliação finalizada tem mais de 6 meses
        $aRenovar = \Illuminate\Support\Facades\DB::connection('oracle_logix')->selectOne("
            SELECT COUNT(*) AS TOTAL FROM (
                SELECT NUMCAD
                FROM RH_AVALIACAO_PERFIL
                WHERE STATUS = 'FINALIZADA'
                GROUP BY NUMCAD
                HAVING MAX(DATA_AVALIACAO) <= ADD_MONTHS(SYSDATE, -6)
            )
        ");

        $stats = [
            'total_avaliacoes' => (clone $avaliacoesQuery)->count(),
            'rascunhos'        => (clone $avaliacoesQuery)->where('STATUS', 'RASCUNHO')->count(),
            'finalizadas'      => (clone $avaliacoesQuery)->where('STATUS', 'FINALIZADA')->count(),
            'a_renovar'        => $aRenovar->TOTAL ?? $aRenovar->total ?? 0,
        ];

        if ($user->role === 'RH') {
            $stats['total_usuarios'] = User::count();
        }

        $recentes = RhAvaliacaoPerfil::orderByDesc('ID')
            ->select(['ID', 'NUMCAD', 'NOMFUN', 'DATA_AVALIACAO', 'STATUS', 'AVALIADOR_USER_ID'])
            ->limit(5)
            ->get();

        // Lookup dos avaliadores via User::find (compatível com Oracle case)
        $cache = [];
        $recentes->transform(function ($a) use (&$cache) {
            $uid = $a->AVALIADOR_USER_ID ?? $a->avaliador_user_id;
            if ($uid) {
                if (!isset($cache[$uid])) {
                    $cache[$uid] = User::find($uid)?->name;
                }
                $a->avaliador_nome = $cache[$uid];
            }
            return $a;
        });

        return Inertia::render('Dashboard', [
            'stats'    => $stats,
            'recentes' => $recentes,
        ]);
    }
}
