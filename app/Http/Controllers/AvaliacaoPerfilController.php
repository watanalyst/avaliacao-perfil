<?php

namespace App\Http\Controllers;

use App\Models\RhAncoraTipo;
use App\Models\RhAncoraResultado;
use App\Models\RhAvaliacaoForca;
use App\Models\RhAvaliacaoPerfil;
use App\Models\RhForca;
use App\Models\RhIfpResultado;
use App\Models\RhProfilerNivel;
use App\Models\RhProfilerResultado;
use App\Models\RhVirtude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AvaliacaoPerfilController extends Controller
{
    // ── Listas de campos reutilizáveis ──────────────────────────────

    private array $ifpFields = [
        'assistencia', 'intracepcao', 'afago', 'autonomia',
        'deferencia', 'afiliacao', 'dominancia', 'desempenho',
        'exibicao', 'agressao', 'ordem', 'persistencia', 'mudanca',
    ];

    private array $profilerPctFields = ['executor', 'planejador', 'analista', 'comunicador'];

    private array $profilerNivelFields = [
        'energia_nivel_id', 'exigencia_meio_nivel_id', 'aproveitamento_nivel_id',
        'moral_nivel_id', 'positividade_nivel_id', 'flexibilidade_nivel_id',
        'amplitude_nivel_id', 'automotivacao_nivel_id', 'incitabilidade_nivel_id',
    ];

    // ── Helpers privados ────────────────────────────────────────────

    private function buildValidationRules(): array
    {
        $rules = [
            'numemp'         => ['required', 'integer'],
            'tipcol'         => ['required', 'integer'],
            'numcad'         => ['required', 'integer'],
            'nomfun'         => ['required', 'string', 'max:40'],
            'data_avaliacao' => ['nullable', 'date'],
            'observacao'     => ['nullable', 'string', 'max:1000'],
            'status'         => ['required', 'in:RASCUNHO,FINALIZADA'],
        ];

        foreach ($this->ifpFields as $f) {
            $rules["ifp.$f"] = ['nullable', 'numeric', 'min:0', 'max:100'];
        }
        foreach ($this->profilerPctFields as $f) {
            $rules["profiler.$f"] = ['nullable', 'numeric', 'min:0', 'max:100'];
        }
        foreach ($this->profilerNivelFields as $f) {
            $rules["profiler.$f"] = ['nullable', 'integer'];
        }

        $rules['ancoras']            = ['nullable', 'array'];
        $rules['ancoras.*.tipo_id']  = ['required', 'integer'];
        $rules['ancoras.*.valor']    = ['nullable', 'numeric', 'min:0', 'max:100'];

        $rules['forcas']             = ['nullable', 'array'];
        $rules['forcas.*.forca_id']  = ['required', 'integer'];
        $rules['forcas.*.valor']     = ['nullable', 'numeric', 'min:0', 'max:100'];

        return $rules;
    }

    private function saveChildRecords(int $id, array $data): void
    {
        // IFP
        $ifpData = ['AVALIACAO_ID' => $id];
        foreach ($this->ifpFields as $f) {
            $ifpData[strtoupper($f)] = $data['ifp'][$f] ?? null;
        }
        RhIfpResultado::on('oracle_logix')->updateOrCreate(
            ['AVALIACAO_ID' => $id],
            $ifpData
        );

        // Profiler
        $profData = ['AVALIACAO_ID' => $id];
        foreach ($this->profilerPctFields as $f) {
            $profData[strtoupper($f)] = $data['profiler'][$f] ?? null;
        }
        foreach ($this->profilerNivelFields as $f) {
            $profData[strtoupper($f)] = $data['profiler'][$f] ?? null;
        }
        RhProfilerResultado::on('oracle_logix')->updateOrCreate(
            ['AVALIACAO_ID' => $id],
            $profData
        );

        // Âncoras (delete + recreate)
        RhAncoraResultado::on('oracle_logix')->where('AVALIACAO_ID', $id)->delete();
        foreach (($data['ancoras'] ?? []) as $ancora) {
            if (isset($ancora['valor']) && $ancora['valor'] !== null && $ancora['valor'] !== '') {
                RhAncoraResultado::on('oracle_logix')->create([
                    'AVALIACAO_ID'   => $id,
                    'ANCORA_TIPO_ID' => $ancora['tipo_id'],
                    'VALOR'          => $ancora['valor'],
                ]);
            }
        }

        // Forças (delete + recreate)
        RhAvaliacaoForca::on('oracle_logix')->where('AVALIACAO_ID', $id)->delete();
        foreach (($data['forcas'] ?? []) as $forca) {
            if (isset($forca['valor']) && $forca['valor'] !== null && $forca['valor'] !== '') {
                RhAvaliacaoForca::on('oracle_logix')->create([
                    'AVALIACAO_ID' => $id,
                    'FORCA_ID'     => $forca['forca_id'],
                    'VALOR'        => $forca['valor'],
                ]);
            }
        }
    }

    private function loadVirtudesComForcas()
    {
        $virtudes  = RhVirtude::orderBy('ORDEM')->get();
        $allForcas = RhForca::orderBy('ORDEM')->get();

        $virtudes->each(function ($virtude) use ($allForcas) {
            $vid = $virtude->ID ?? $virtude->id;
            $virtude->forcas = $allForcas->filter(function ($f) use ($vid) {
                return ($f->VIRTUDE_ID ?? $f->virtude_id) == $vid;
            })->values();
        });

        return $virtudes;
    }

    private function loadReferenceData(): array
    {
        return [
            'niveis'      => RhProfilerNivel::orderBy('ORDEM')->get(),
            'ancoraTipos' => RhAncoraTipo::orderBy('ORDEM')->get(),
            'virtudes'    => $this->loadVirtudesComForcas(),
        ];
    }

    private function loadAvaliacaoData(RhAvaliacaoPerfil $avaliacao): array
    {
        $id = $avaliacao->ID ?? $avaliacao->id;

        // Lookup manual do avaliador (evita eager load cross-database Oracle)
        $avaliadorId = $avaliacao->AVALIADOR_USER_ID ?? $avaliacao->avaliador_user_id;
        $avaliador = $avaliadorId ? \App\Models\User::find($avaliadorId) : null;
        $avaliacao->avaliador_nome = $avaliador?->name;

        return [
            'avaliacao' => $avaliacao,
            'ifp'       => RhIfpResultado::where('AVALIACAO_ID', $id)->first(),
            'profiler'  => RhProfilerResultado::where('AVALIACAO_ID', $id)->first(),
            'ancoras'   => RhAncoraResultado::where('AVALIACAO_ID', $id)->get(),
            'forcas'    => RhAvaliacaoForca::where('AVALIACAO_ID', $id)->get(),
        ];
    }

    // ── Actions ─────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = RhAvaliacaoPerfil::orderByDesc('ID')
            ->select([
                'ID', 'NUMCAD', 'NOMFUN', 'DATA_AVALIACAO',
                'STATUS', 'AVALIADOR_USER_ID',
            ]);

        // Filtro de busca (matrícula ou nome)
        $busca = trim((string) $request->get('busca', ''));
        if ($busca !== '') {
            $upper = mb_strtoupper($busca, 'UTF-8');
            $query->where(function ($q) use ($busca, $upper) {
                $q->whereRaw("NOMFUN LIKE ?", ["%{$upper}%"])
                  ->orWhereRaw("TO_CHAR(NUMCAD) LIKE ?", ["%{$busca}%"]);
            });
        }

        // Filtro de status (via query string, usado pelos cards do Dashboard)
        $status = $request->get('status', '');
        if (in_array($status, ['RASCUNHO', 'FINALIZADA'])) {
            $query->whereRaw("STATUS = ?", [$status]);
        } elseif ($status === 'RENOVAR') {
            // Colaboradores cuja última avaliação finalizada tem mais de 6 meses
            $query->where('STATUS', 'FINALIZADA')
                  ->whereRaw("DATA_AVALIACAO <= ADD_MONTHS(SYSDATE, -6)");
        }

        $avaliacoes = $query->paginate(20)->withQueryString();

        // Lookup dos avaliadores + flag de renovação
        $cache = [];
        $limiteRenovacao = now()->subMonths(6);
        $avaliacoes->getCollection()->transform(function ($a) use (&$cache, $limiteRenovacao) {
            $uid = $a->AVALIADOR_USER_ID ?? $a->avaliador_user_id;
            if ($uid) {
                if (!isset($cache[$uid])) {
                    $user = \App\Models\User::find($uid);
                    $cache[$uid] = $user?->name;
                }
                $a->avaliador_nome = $cache[$uid];
            }

            // Flag: avaliação finalizada há mais de 6 meses
            $dataAv = $a->DATA_AVALIACAO ?? $a->data_avaliacao;
            $statusAv = $a->STATUS ?? $a->status;
            $a->vencida = $statusAv === 'FINALIZADA'
                && $dataAv
                && \Carbon\Carbon::parse($dataAv)->lt($limiteRenovacao);

            return $a;
        });

        return Inertia::render('Avaliacoes/Index', [
            'avaliacoes' => $avaliacoes,
            'filters'    => [
                'busca'  => $busca,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Avaliacoes/Create', $this->loadReferenceData());
    }

    public function show(RhAvaliacaoPerfil $avaliacao)
    {
        return Inertia::render('Avaliacoes/Show', array_merge(
            $this->loadAvaliacaoData($avaliacao),
            $this->loadReferenceData(),
        ));
    }

    public function edit(RhAvaliacaoPerfil $avaliacao)
    {
        $status = $avaliacao->STATUS ?? $avaliacao->status;
        if ($status !== 'RASCUNHO' && Auth::user()->role !== 'RH') {
            abort(403, 'Apenas avaliações em rascunho podem ser editadas.');
        }

        return Inertia::render('Avaliacoes/Edit', array_merge(
            $this->loadAvaliacaoData($avaliacao),
            $this->loadReferenceData(),
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->buildValidationRules());

        // Bloquear se já existe avaliação finalizada com menos de 6 meses
        $recente = DB::connection('oracle_logix')->selectOne("
            SELECT MAX(DATA_AVALIACAO) AS ULTIMA
            FROM RH_AVALIACAO_PERFIL
            WHERE NUMCAD = :numcad
              AND STATUS = 'FINALIZADA'
              AND DATA_AVALIACAO > ADD_MONTHS(SYSDATE, -6)
        ", ['numcad' => (int) $data['numcad']]);

        if ($recente && ($recente->ULTIMA ?? $recente->ultima)) {
            return back()->withErrors([
                'numcad' => 'Este colaborador possui uma avaliação finalizada há menos de 6 meses. Aguarde o prazo para uma nova avaliação.',
            ]);
        }

        $avaliacao = null;

        DB::connection('oracle_logix')->transaction(function () use ($data, &$avaliacao) {
            $masterData = [
                'NUMEMP'            => (int) $data['numemp'],
                'TIPCOL'            => (int) $data['tipcol'],
                'NUMCAD'            => (int) $data['numcad'],
                'NOMFUN'            => mb_strtoupper(trim($data['nomfun']), 'UTF-8'),
                'DATA_AVALIACAO'    => $data['data_avaliacao'] ?? now(),
                'STATUS'            => $data['status'],
                'OBSERVACAO'        => $data['observacao'] ?? null,
                'AVALIADOR_USER_ID' => Auth::id(),
            ];

            if ($data['status'] === 'FINALIZADA') {
                $masterData['FINALIZADO_EM']  = now();
                $masterData['FINALIZADO_POR'] = Auth::id();
            }

            $avaliacao = RhAvaliacaoPerfil::on('oracle_logix')->create($masterData);

            $this->saveChildRecords($avaliacao->ID, $data);
        });

        return redirect()->route('avaliacoes.index');
    }

    public function update(Request $request, RhAvaliacaoPerfil $avaliacao)
    {
        $status = $avaliacao->STATUS ?? $avaliacao->status;
        if ($status !== 'RASCUNHO' && Auth::user()->role !== 'RH') {
            abort(403, 'Apenas avaliações em rascunho podem ser editadas.');
        }

        $data = $request->validate($this->buildValidationRules());

        DB::connection('oracle_logix')->transaction(function () use ($data, $avaliacao) {
            $id = $avaliacao->ID ?? $avaliacao->id;

            $masterData = [
                'NUMEMP'         => (int) $data['numemp'],
                'TIPCOL'         => (int) $data['tipcol'],
                'NUMCAD'         => (int) $data['numcad'],
                'NOMFUN'         => mb_strtoupper(trim($data['nomfun']), 'UTF-8'),
                'DATA_AVALIACAO' => $data['data_avaliacao'] ?? now(),
                'STATUS'         => $data['status'],
                'OBSERVACAO'     => $data['observacao'] ?? null,
            ];

            if ($data['status'] === 'FINALIZADA') {
                $masterData['FINALIZADO_EM']  = now();
                $masterData['FINALIZADO_POR'] = Auth::id();
            }

            $avaliacao->update($masterData);

            $this->saveChildRecords($id, $data);
        });

        return redirect()->route('avaliacoes.index');
    }

    public function finalizar(RhAvaliacaoPerfil $avaliacao)
    {
        if (($avaliacao->STATUS ?? $avaliacao->status) !== 'RASCUNHO') {
            abort(403, 'Esta avaliação já foi finalizada.');
        }

        $id = $avaliacao->ID ?? $avaliacao->id;

        DB::connection('oracle_logix')
            ->table('RH_AVALIACAO_PERFIL')
            ->where('ID', $id)
            ->update([
                'STATUS'         => 'FINALIZADA',
                'FINALIZADO_EM'  => now(),
                'FINALIZADO_POR' => Auth::id(),
            ]);

        return redirect()->route('avaliacoes.index');
    }

    public function checkColaborador(Request $request)
    {
        $numcad = (int) $request->get('numcad', 0);
        if (!$numcad) {
            return response()->json(['bloqueado' => false]);
        }

        $recente = DB::connection('oracle_logix')->selectOne("
            SELECT MAX(DATA_AVALIACAO) AS ULTIMA
            FROM RH_AVALIACAO_PERFIL
            WHERE NUMCAD = :numcad
              AND STATUS = 'FINALIZADA'
              AND DATA_AVALIACAO > ADD_MONTHS(SYSDATE, -6)
        ", ['numcad' => $numcad]);

        $bloqueado = (bool) ($recente->ULTIMA ?? $recente->ultima ?? null);

        return response()->json([
            'bloqueado' => $bloqueado,
            'ultima'    => $recente->ULTIMA ?? $recente->ultima ?? null,
        ]);
    }

    public function colaboradores(Request $request)
    {
        $q = mb_strtoupper(trim((string) $request->get('q', '')), 'UTF-8');

        if ($q !== '' && strlen($q) < 2 && !ctype_digit($q)) {
            return response()->json([]);
        }

        $where = "WHERE R.SITAFA <> 7";
        $bind  = [];

        if ($q !== '') {
            if (ctype_digit($q)) {
                $where .= " AND (TO_CHAR(R.NUMCAD) LIKE :numcad OR UPPER(R.NOMFUN) LIKE :nome)";
                $bind['numcad'] = "{$q}%";
                $bind['nome']   = "%{$q}%";
            } else {
                $where .= " AND UPPER(R.NOMFUN) LIKE :nome";
                $bind['nome']   = "%{$q}%";
            }
        }

        $sql = "
            SELECT *
            FROM (
                SELECT DISTINCT
                    R.NUMEMP,
                    R.TIPCOL,
                    R.NUMCAD,
                    R.NOMFUN
                FROM VETORH.R034FUN R
                LEFT JOIN VETORH.USU_TLOCFUN U
                    ON R.NUMCAD = U.MATRICULA
                $where
                ORDER BY R.NUMCAD
            )
            WHERE ROWNUM <= 20
        ";

        $rows = DB::connection('oracle_senior')->select($sql, $bind);

        return response()->json($rows);
    }
}
