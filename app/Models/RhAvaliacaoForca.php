<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhAvaliacaoForca extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_AVALIACAO_FORCA';
    protected $primaryKey = 'ID';

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'AVALIACAO_ID',
        'FORCA_ID',
        'VALOR',
    ];

    public function forca()
    {
        return $this->belongsTo(RhForca::class, 'FORCA_ID', 'ID');
    }
}
