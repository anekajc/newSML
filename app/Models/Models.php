<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Models extends Model
{
		protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'model';
    protected $fillable = array(
        'kode',
				'nama',
				'deleted'
    );
    public $timestamps = false;
}

?>
