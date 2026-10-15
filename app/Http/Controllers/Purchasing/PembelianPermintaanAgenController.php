<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\VwPPL;
use App\Traits\PenjagaTransaksi;


// use App\Http\Controllers\NewMenuController;

class PembelianPermintaanAgenController extends Controller
{
  use PenjagaTransaksi;


    public function index(Request $req)
    {
        $kodemenu = '030102';

        $akses = app('App\Http\Controllers\GlobalController')
            ->getAkses($kodemenu, $req->path());

        if (!$akses || !$akses->HASACCESS) {
            return redirect('/home');
        }

        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $menul0  = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

        // Data tabel (listData1/listData2, header kolom, dsb) TIDAK disiapkan di sini karena
        // blade memuatnya sendiri lewat AJAX loadAll() saat halaman ready - menyiapkannya di
        // sini dobel kerja dan bikin buka halaman lemot.
        return view('purchasing.pembelianpermintaanagen', [
            "menul0"  => $menul0,
            "periode" => $periode,
            "akses"   => $akses,
        ]);
    }


    // Tanggal filter dari browser hanya dipakai kalau formatnya Y-m-d, tanggalnya ada di kalender,
    // dan tahunnya 1900-9999. Selain itu (mis. "0202-01-01" waktu user masih mengetik tahun)
    // SQL Server datetime menolak dengan error out-of-range, jadi dipakai satu bulan periode kerja.
    private function rentangTanggal (Request $req) {
      $sah = function ($tgl) {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', (string) $tgl, $m)) { return false; }
        return (int) $m[1] >= 1900 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
      };
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
      $awal  = $sah($req->tglawal)  ? $req->tglawal  : date('Y-m-01', $stamp);
      $akhir = $sah($req->tglakhir) ? $req->tglakhir : date('Y-m-t', $stamp);
      return [$awal, $akhir];
    }

    public function loadAll(Request $req)
    {
        list($tglawalSah, $tglakhirSah) = $this->rentangTanggal($req);

        $queryOtorisasi = '';

        if (in_array((string) $req->isoto, ['0', '1'], true)) {
            $queryOtorisasi = ' AND IsOtorisasi1 = ' . (int) $req->isoto;
        }

        // Qnt/QntBatal/QntPO ikut diambil (dengan alias eksplisit, supaya key JSON-nya pasti
        // "Qnt"/"QntBatal"/"QntPO") untuk menghitung badge Status "Sudah/Belum/Batal" di
        // pembelianpermintaanagen.blade.php (prStatusPR()). Ketiganya dikecualikan dari
        // penurunan kolom default di bawah supaya tidak ikut jadi kolom tabel.
        $outstanding = DB::connection("SML")->select(
            "SELECT NoBukti, CONVERT(varchar(10), Tanggal, 23) AS Tanggal, IsOtorisasi1, TglOto1, OtoUser1,
                    Qnt AS Qnt, QntBatal AS QntBatal, QntPO AS QntPO
             FROM vwPPL
             WHERE Tanggal BETWEEN :tglawal AND :tglakhir
               AND IsJasa = 0
               AND pAgen = 1
               $queryOtorisasi
             ORDER BY Tanggal DESC, NoBukti DESC",
            [
                "tglawal"  => $tglawalSah,
                "tglakhir" => $tglakhirSah
            ]
        );

        $collection = collect($outstanding)->groupBy('NoBukti');

        $tempOutstanding = [];
        foreach ($collection as $groupedData) {
            $tempOutstanding[] = $groupedData;
        }

        $tempOtorisasi = [];

        $username = \Auth::user()->username;

        $headertable = DB::connection("SML")->select("select * from dbheadertable where href= :href and username = :username", ["username" => $username, "href" => $req->href]);
        $isnumberheadertable = [];
        $headertablevalue = [];
        $headertableheader = [];
        $headerisshown = [];
        $aliasOrdered = [];

        $isparsed = 0;
        $headertablealias = [];

        $xxx = DB::connection("SML")->select("select * from DBHEADERTABLEALIAS where href = :href", ["href" => $req->href]);
        if ($xxx) {
            $headertablealias = json_decode($xxx[0]->value);
        } else {
            $headertablealias = [];
        }
        if (count($headertable) > 0) {
            $isnumberheadertable = json_decode($headertable[0]->isnumber);
            $headertablevalue = json_decode($headertable[0]->value);
            $headertableheader = json_decode($headertable[0]->header);
            $isparsed = 0;
            $headerisshown = json_decode($headertable[0]->isshown);
        } else {
            $isparsed = 1;
            // Qnt/QntBatal/QntPO dipakai hanya untuk menghitung badge Status di JS, bukan kolom
            // tabel - lihat komentar di query vwppl di atas.
            $kolomInternal = ['Qnt', 'QntBatal', 'QntPO'];
            if (!$tempOutstanding) {
            } else {
                foreach ($tempOutstanding[0][0] as $key => $value) {
                    if (str_contains($key, "Oto") || in_array($key, $kolomInternal, true)) {
                    } else {
                        array_push($headertablevalue, $key);
                        array_push($headertableheader, $key);
                        array_push($headerisshown, 1);
                        if (strtotime($value)) {
                            array_push($isnumberheadertable, 2);
                        } else if (is_numeric($value)) {
                            array_push($isnumberheadertable, 1);
                        } else {
                            array_push($isnumberheadertable, 0);
                        }
                    }
                }
            }
        }

        $aliasOrdered = [];
        if ($headertablealias) {
            foreach ($headertablevalue as $header) {
                foreach ($headertablealias as $item) {
                    if ($item->value === $header) {
                        array_push($aliasOrdered, $item);
                        break;
                    }
                }
            }
        } else {
            foreach ($headertablevalue as $header) {
                array_push($aliasOrdered, ["value" => $header, "alias" => $header]);
            }
        }

        return [
            "aliasordered"      => $aliasOrdered,
            "headertableheader" => $headertableheader,
            "isnumeric"         => $isnumberheadertable,
            "headertablevalue"  => $headertablevalue,
            "isparsed"          => $isparsed,
            "isshown"           => $headerisshown,
            "listData1"         => $tempOutstanding,   // Belum Otorisasi
            "listData2"         => $tempOtorisasi      // Sudah Otorisasi
        ];
    }

  public function updateOtorisasi(Request $req) {
    $pr = $this->headerDokumen('DBPPL', $req->nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $pr->isotorisasi1 === 1) {
      return $this->tolak('PR ' . $req->nobukti . ' sudah diotorisasi oleh ' . $pr->otouser1 . '.');
    }
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 1, maxol = 1, OtoUser1 = :username, TglOto1 = :tanggal WHERE NoBukti = :nobukti AND isnull(IsOtorisasi1, 0) = 0",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PR',$req->nobukti,'',0,'DBPPL');
    return $res;
}
  public function updateBatalOtorisasi(Request $req) {
    $pr = $this->headerDokumen('DBPPL', $req->nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $pr->isotorisasi1 !== 1) {
      return $this->tolak('PR ' . $req->nobukti . ' belum diotorisasi.');
    }
    // PR yang sudah dibuat PO tidak boleh dibatalkan: sesudah batal otorisasi itemnya bisa
    // diedit/dihapus, padahal DBPODET.NoPPL/UrutPPL masih menunjuk item itu.
    $po = $this->dipakaiOleh('DBPODET', 'NoPPL', $req->nobukti);
    if ($po) {
      return $this->tolak('PR ' . $req->nobukti . ' sudah dibuat PO ' . $po . ', otorisasi tidak bisa dibatalkan.');
    }
    $res = DB::connection('SML')->update(
        "UPDATE DBPPL SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]


    );

     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PR',$req->nobukti,$req->pket,0,'DBPPL');
    return $res;
}
  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from DBPPL where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function spDetail (Request $req) {
    return DB::connection('SML')->select(
      "select * from VwPPL where NoBukti = :nobukti order by Urut",
      ["nobukti" => $req->nobukti]
    );
  }


  public function listDepartemen (Request $req) {
    $listDepartemen = DB::connection("SML")->select('select * from DBDEPART');
    return $listDepartemen;
  }

  public function getNoBukti (Request $req) {
    // $values = [
    //   'a'
    // ];
    // return 'tes';
    // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $inisial = DB::connection("SML")->select('select PRA from DBNOMOR');
    // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->PRA,
        $periode->bulan,
        $periode->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
    //     select b.NAMAMERK ,  a.* from dbbarang a
    // join DBMERK b on a.KodeMerk = b.KODEMERK
    //  where a.KODEGRP = 'BJ' and a.pAgen = 1

    if (!$req->filled('search')) {
        return response()->json([]);
    }

    $search = "%" . $req->input('search') . "%";

    $listData = DB::connection('SML')->select("
        SELECT
            b.NAMAMERK,
            a.KODEBRG,
            a.NAMABRG,
            a.KODEMERK,
            a.PartNumber,
            a.SAT1,
            a.SAT2,
            a.SAT3,
            a.ISI1,
            a.ISI2,
            a.ISI3
        FROM dbbarang a
        JOIN DBMERK b ON a.KodeMerk = b.KODEMERK
        WHERE a.KODEGRP = 'BJ'
	AND a.IsAktif = 1
        AND a.pAgen = ?
        AND (a.KODEBRG LIKE ? OR a.NAMABRG LIKE ?)
        ORDER BY a.KODEBRG ASC
    ", [
        $req->isagen,
        $search,
        $search
    ]);

    return response()->json($listData);
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;


     $purut = DB::connection('SML')->select('select * from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPPLdet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else {
            // return 'mmm';
            $xurut = $req->urut;
        }

    }else{
        // return 'ttt';
        $xurut=1;
    }

    // Semua item dihapus di form edit (sp_PPL ikut menghapus header) lalu user menambah item
    // lagi: JS masih mengirim jmlrecord 1, jadi header dibuat ulang di sini, bukan ditolak.
    if ($choice == "I" && $jmlrecord != 0 && !$this->headerDokumen('DBPPL', $nobukti)) {
      $jmlrecord = 0;
    }
    if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('select * from DBPPL where Nobukti = :nobukti', ['nobukti' => $nobukti]);
        if ($check) {
            return 2;
        }
    } else {
      $tolak = $this->cekUbahPR($nobukti, $choice == 'U' ? $req->urut : null);
      if ($tolak) {
        return $tolak;
      }
    }
    $jmlSebelum = $this->jumlahBaris('DBPPLDET', $nobukti);
    $values = [
        $choice,
        $nobukti,
        strval($req->nourut),
        date('Y-m-d H:i:s', strtotime($req->tanggal)),
        (int) $req->urut,
        $req->kodebarang,
         $req->qnt,
        (int) $req->nosat,
        $req->satuan,
         $req->isi,
        $req->keterangan ?? '',
        (int) $req->isclose,
        (int) $req->isclosed,
        $req->kodedepartemen,
        $req->keterangannama ?? '',
        (int) $req->isjasa,
        $req->noso ?? '',
        (int) $req->urutso,
        (int) $req->pagen,
        $req->nopocust ?? '',
        (int) $jmlrecord,
        (int) $req->pjasa
    ];
    DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    if ($choice == 'I' && $this->jumlahBaris('DBPPLDET', $nobukti) <= $jmlSebelum) {
      return $this->tolak('Item gagal disimpan. Silakan coba lagi.');
    }

    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$nobukti,'',$xurut,'DBPPLDET');

    return 1;
  }


  public function spDelete (Request $req) {
    $tolak = $this->cekUbahPR($req->nobukti, $req->urut);
    if ($tolak) {
      return $tolak;
    }

    // $listData = $req->listData;
    // foreach ($listData as $d) {
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PR',$req->nobukti,'',$req->urut,'DBPPLDET');
      // code...
      $values = [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->urut,
        $req->kodebarang,
        $req->qnt,
        $req->nosat,
        $req->satuan,
        $req->isi,
        $req->keterangan,
        $req->isclose,
        $req->isclosed,
        $req->kddep,
        $req->keterangannama,
        $req->isjasa,
        $req->noso,
        $req->urutso,
        $req->pagen,
        $req->nopocust,
        $req->jmlrecord,
        $req->pjasa

      ];
      DB::connection('SML')->statement('exec sp_PPL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    // }
    return 1;
    // foreach ($penerimaan as $p) {
    //   // code...
    //   array_push($tempPenerimaan, $p);
    // }
    //
    // DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    // return 1;
  }


  // PR yang sudah diotorisasi tidak boleh diubah, dan item yang sudah dibuat PO tidak boleh
  // diedit/dihapus. Mengembalikan jawaban penolakan, atau null kalau boleh.
  private function cekUbahPR($nobukti, $urut = null) {
    $pr = $this->headerDokumen('DBPPL', $nobukti);
    if (!$pr) {
      return $this->tolak('PR ' . $nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $pr->isotorisasi1 === 1) {
      return $this->tolak('PR ' . $nobukti . ' sudah diotorisasi, tidak bisa diubah.');
    }
    if ($urut !== null) {
      $po = $this->dipakaiOleh('DBPODET', 'NoPPL', $nobukti, 'UrutPPL', $urut);
      if ($po) {
        return $this->tolak('Item ini sudah dibuat PO ' . $po . ', tidak bisa diubah atau dihapus.');
      }
    }
    return null;
  }

}
