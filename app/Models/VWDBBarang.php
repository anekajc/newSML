<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWDBBarang extends Model
{
		protected $connection = 'sqlsrv';
    protected $table = 'vwDBBarang';
    protected $fillable = array(
        'KODEBRG',
				'NAMABRG',
				'KODEGRP',
				'KODESUBGRP',
				'KODESUPP',
				'SAT1',
				'ISI1',
				'SAT2',
				'ISI2',
				'SAT3',
				'ISI3',
				'Hrg1_1',
				'Hrg2_1',
				'Hrg3_1',
				'Hrg1_2',
				'Hrg2_2',
				'Hrg3_2',
				'Hrg1_3',
				'Hrg2_3',
				'Hrg3_3',
				'QntMin',
				'QntMax',
				'ISAKTIF',
				'Keterangan',
				'NFix',
				'NamaBrg2',
				'Tolerate',
				'Proses',
				'IsTakeIn',
				'IsBarang',
				'IsJasa',
				'KodeMerk',
				'KodeHdGrp',
				'pAgen',
				'PartNumber',
				'KODESUBKATEGORI',
				'pPPN',
				'Lokasi',
				'KodeBarcode',
				'CBarcode',
				'Mlokasi',
				'Berat',
				'pBerat',
				'pKontrak',
				'SKU'

    );
    public $timestamps = false;
}

?>
