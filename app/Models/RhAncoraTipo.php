<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhAncoraTipo extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_ANCORA_TIPO';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = ['ORDEM', 'NOME'];
}
