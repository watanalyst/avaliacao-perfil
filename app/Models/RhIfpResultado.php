<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhIfpResultado extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_IFP_RESULTADO';
    protected $primaryKey = 'AVALIACAO_ID';
    public $incrementing = false;
    protected $keyType = 'int';

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'AVALIACAO_ID',
        'ASSISTENCIA',
        'INTRACEPCAO',
        'AFAGO',
        'AUTONOMIA',
        'DEFERENCIA',
        'AFILIACAO',
        'DOMINANCIA',
        'DESEMPENHO',
        'EXIBICAO',
        'AGRESSAO',
        'ORDEM',
        'PERSISTENCIA',
        'MUDANCA',
    ];
}
