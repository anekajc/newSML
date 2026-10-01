<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Periode extends Model
{
		protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'periode';
    protected $fillable = array(
        'id_user',
        'bulan',
        'tahun'
    );
    public $timestamps = false;
}

?>
