<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewUsers extends Model
{
		protected $connection = 'SML';
    protected $primaryKey = 'id';
		protected $table = 'new_users';
    protected $fillable = array(
        'name',
        'username'
    );
    public $timestamps = false;
}


?>
