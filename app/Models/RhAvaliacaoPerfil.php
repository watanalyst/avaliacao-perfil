<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RhAvaliacaoPerfil extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_AVALIACAO_PERFIL';
    protected $primaryKey = 'ID';

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'NUMEMP',
        'TIPCOL',
        'NUMCAD',
        'NOMFUN',
        'DATA_AVALIACAO',
        'STATUS',
        'OBSERVACAO',

        'AVALIADOR_USER_ID',
        'FINALIZADO_EM',
        'FINALIZADO_POR',
    ];

    public function ifp()
    {
        return $this->hasOne(RhIfpResultado::class, 'AVALIACAO_ID', 'ID');
    }

    public function profiler()
    {
        return $this->hasOne(RhProfilerResultado::class, 'AVALIACAO_ID', 'ID');
    }

    public function ancoras()
    {
        return $this->hasMany(RhAncoraResultado::class, 'AVALIACAO_ID', 'ID');
    }

    public function forcas()
    {
        return $this->hasMany(RhAvaliacaoForca::class, 'AVALIACAO_ID', 'ID');
    }

    // 🔥 relacionamento com o avaliador (usuário do sistema)
    public function avaliador()
    {
        return $this->belongsTo(User::class, 'AVALIADOR_USER_ID');
    }
}
