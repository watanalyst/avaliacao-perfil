<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhProfilerNivel extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_PROFILER_NIVEL';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = ['ORDEM', 'DESCRICAO'];
}
