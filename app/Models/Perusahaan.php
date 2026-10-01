<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Perusahaan extends Model
{
		protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $table = 'perusahaan';
    protected $fillable = array(
        'nama',
				'alamat',
				'kota',
				'telepon',
				'fax',
				'email',
				'nama_pajak',
				'alamat_pajak',
				'kota_pajak',
				'npwp',
				'tanggal_pengukuhan',
				'penandatanganan_fpj',
				'jabatan'
    );
    public $timestamps = false;
}

?>
