<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhVirtude extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_VIRTUDE';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = ['ORDEM', 'NOME'];

    public function forcas()
    {
        return $this->hasMany(RhForca::class, 'VIRTUDE_ID', 'ID')->orderBy('ORDEM');
    }
}
