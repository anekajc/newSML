<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class vw_outbeliqc extends Model
{
		protected $connection = 'SML';
    protected $table = 'vw_outbeliqc';
    protected $fillable = array(
         'NOBUKTI',
         'URUT',
         'KODEBRG',
         'KodeGdg',
         'NamaBrg',
         'PPN',
         'KURS',
         'DISC',
         'QNT',
         'NOSAT',
         'SATUAN',
         'ISI',
         'HARGA',
         'DISCP',
         'DISCTOT',
         'BYANGKUT',
         'NoPO',
         'UrutPO',
         'HPP',
         'QntTerima',
         'Qnt1Terima',
         'Qnt2Terima',
         'QntReject',
         'Qnt1Reject',
         'Qnt2Reject',
         'HRGNETTO',
         'NDISKON',
         'SUBTOTAL',
         'NDPP',
         'NPPN',
         'NNET',
         'SUBTOTALRp',
         'NDPPRp',
         'NPPNRp',
         'NNETRp',
         'UrutBeli',
         'KetReject',
         'DiscP2',
         'DiscP3',
         'DiscP4',
         'DiscP5',
         'Isjasa',
         'Sat_1',
         'Sat_2',
         'NOBATCH',
         'pFOC',
         'DBASAL',
         'PERKIRAAN',
         'COST',
         'SUBCOST',
         'ED',
         'NilaiPPN',
         'IsOtorisasi1',
         'pQC',
         'KODESUPP',
         'NAMACUSTSUPP',
         'TANGGAL',
         'FAKTURSUPP',
         'NAMAGDG',
         'Bulan',
         'Tahun',
				 'ISI1',
				 'ISI2',
    );
    public $timestamps = false;
}

?>
