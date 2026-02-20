<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhForca extends Model
{
    protected $connection = 'oracle_logix';
    protected $table = 'RH_FORCA';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = ['VIRTUDE_ID', 'ORDEM', 'NOME'];

    public function virtude()
    {
        return $this->belongsTo(RhVirtude::class, 'VIRTUDE_ID', 'ID');
    }
}
