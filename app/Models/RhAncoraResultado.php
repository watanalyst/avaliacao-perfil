<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhAncoraResultado extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_ANCORA_RESULTADO';
    protected $primaryKey = 'ID';

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = [
        'AVALIACAO_ID',
        'ANCORA_TIPO_ID',
        'VALOR',
    ];

    public function tipo()
    {
        return $this->belongsTo(RhAncoraTipo::class, 'ANCORA_TIPO_ID', 'ID');
    }
}
