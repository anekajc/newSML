<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VWtampilbeli extends Model
{
		protected $connection = 'SML';
    protected $table = 'VWtampilbeli';
    protected $fillable = array(
        'Bulan',
				'Tahun',
				'Tanggal',
				'KODESUPP',
				'NamaSupplier',
				'KETERANGAN',
				'NoBukti',
				'NoPO',
				'UrutPO',
				'KODEGDG',
				'NAMAGUDANG',
				'FAKTURSUPP',
				'Urut',
				'KodeBrg',
        'NamaBrg',
        'Qnt',
        'NoSat',
        'Isi',
        'Satuan',
        'Qnt2',
        'SatuanRoll',
        'Harga',
        'HrgNetto',
        'DiscP1',
        'DiscRp1',
        'DiscTot',
        'TotalUSD',
        'TotalIDR',
        'NDPP',
        'NPPN',
        'Beban',
        'Total',
        'PARTNUMBER',
        'QNTPO',
        'QNTOUT'

    );
    public $timestamps = false;
}

?>
