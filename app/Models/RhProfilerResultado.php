<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhProfilerResultado extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_PROFILER_RESULTADO';
    protected $primaryKey = 'AVALIACAO_ID';
    public $incrementing = false;
    protected $keyType = 'int';

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'AVALIACAO_ID',
        'EXECUTOR',
        'PLANEJADOR',
        'ANALISTA',
        'COMUNICADOR',
        'ENERGIA_NIVEL_ID',
        'EXIGENCIA_MEIO_NIVEL_ID',
        'APROVEITAMENTO_NIVEL_ID',
        'AUTOCONFIANCA_NIVEL_ID',
        'AUTOESTIMA_NIVEL_ID',
        'FLEXIBILIDADE_NIVEL_ID',
        'AMPLITUDE_NIVEL_ID',
        'AUTOMOTIVACAO_NIVEL_ID',
        'INCITABILIDADE_NIVEL_ID',
    ];
}
