<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;



class NewPOController extends Controller
{


  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index (Request $req) {

    $kodemenu = '030401';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');

    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);
    list($npoTglAwal, $npoTglAkhir) = $this->periodeRange($periode);
    // return $periode;
    $pembelian = DB::connection("SML")->select("select * from dbo.fnc_masterbeli ( :bulan , :tahun, :pjasa)" , ["bulan" => $periode->bulan, "tahun" => $periode->tahun , "pjasa" => 0]);

    // $po = vwOUtPOWMS::all();
    $poGroup = vwOUtPOWMS::all()->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');

    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG);
    }

    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('purchasing.newpo' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "po" => [],
      "poGroup" => [],
      "tempPo" => $tempPo,
      "gudang" => $gudang,
      "tempPembelian1" => $pembelian,
      "npoTglAwal" => $npoTglAwal,
      "npoTglAkhir" => $npoTglAkhir,
      "akses" => $akses
    ]);


  }

  public function getAkses () {
    $kodemenu = '03004';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    return $akses;
  }

  public function getAllPO (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    $tglakhirPlus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));
    $poGroup = vwOUtPOWMS::where('TANGGAL', '>=', $tglawal)->where('TANGGAL', '<', $tglakhirPlus)->get()->sortBy('NoBukti')->sortBy('Urut')->groupBy('NoBukti');

    $tempPo = [];
    foreach ($poGroup as $pG) {
      // code...
      array_push($tempPo, $pG->values());
    }
    return $tempPo;
  }

  public function getAllPembelian (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    $tglakhirplus = date('Y-m-d', strtotime($tglakhir . ' +1 day'));

    // fnc_masterbeli hanya menerima bulan/tahun, jadi rentang bebas dilayani dengan
    // memanggilnya per bulan yang tersentuh rentang lalu disaring per tanggal (TANGGAL).
    $pembelian = [];
    $cursor = date('Y-m-01', strtotime($tglawal));
    $batas  = date('Y-m-01', strtotime($tglakhir));
    while ($cursor <= $batas) {
      $rows = DB::connection("SML")->select(
        // TANGGAL dari fnc_masterbeli untuk barang non-jasa ikut syarat QC (NULL kalau
        // belum di-QC) - modul ini tidak memakai QC, jadi fallback ke TglBeli (tanggal
        // dokumen apa adanya) supaya dokumen yang belum di-QC tidak hilang dari daftar.
        "select * from dbo.fnc_masterbeli ( :bulan , :tahun, :pjasa)
         where ISNULL(TANGGAL, TglBeli) >= :tglawal and ISNULL(TANGGAL, TglBeli) < :tglakhirplus",
        ["bulan" => (int) date('n', strtotime($cursor)), "tahun" => (int) date('Y', strtotime($cursor)),
         "pjasa" => 0, "tglawal" => $tglawal, "tglakhirplus" => $tglakhirplus]);
      foreach ($rows as $r) { if (empty($r->TANGGAL)) { $r->TANGGAL = $r->TglBeli; } }
      $pembelian = array_merge($pembelian, $rows);
      $cursor = date('Y-m-01', strtotime($cursor . ' +1 month'));
    }
    return $pembelian;
  }


public function getOutstandingPODetail(Request $req)
{
    $NoPO = $req->get('NoPO');

    $data = DB::connection('SML')->select("
        SELECT 
            d.Urut,
            d.KodeBrg,
            b.NamaBrg,
            d.QNT,
            d.OSPO,
            d.NOSAT,
            d.Satuan,
            d.ISI,
            d.ISI2
        FROM POD d
        JOIN BARANG b ON b.KodeBrg = d.KodeBrg
        WHERE d.NoBukti = ?
          AND d.OSPO > 0
        ORDER BY d.Urut
    ", [$NoPO]);

    return response()->json($data);
}


public function getDetailPembelian (Request $req) {
  $nobukti = $req->input('NoBukti');
  // Bulan/tahun HARUS diambil dari tanggal dokumennya sendiri, bukan periode kerja user
  // yang sedang aktif - kalau tidak, dokumen dari bulan lain selalu kembali kosong.
  $header = DB::connection('SML')->select('select TANGGAL from dbBeli where NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
  if ($header) {
    $bulan = (int) date('n', strtotime($header[0]->TANGGAL));
    $tahun = (int) date('Y', strtotime($header[0]->TANGGAL));
  } else {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $bulan = $periode->bulan;
    $tahun = $periode->tahun;
  }
  $pembelian = DB::connection("SML")->select("select * from dbo.fnc_tampilbeli ( :bulan , :tahun, :pjasa) where nobukti = :nobukti" , ["bulan" => $bulan, "tahun" => $tahun , "pjasa" => 0 , "nobukti" => $nobukti]);
  // Sama seperti getAllPembelian - TANGGAL bisa NULL kalau barang belum di-QC, fallback ke tanggal dbBeli.
  if ($header) { foreach ($pembelian as $p) { if (empty($p->TANGGAL)) { $p->TANGGAL = $header[0]->TANGGAL; } } }
 
  
  $tempPembelian1 = [];
  foreach ($pembelian as $p) {
    array_push($tempPembelian1, $p);
  }
  return $tempPembelian1;
}

public function getDetailCetak(Request $req)
{
    $noBukti = $req->input('NoBukti');

    $cetak = DB::connection("SML")->select(
        "EXEC dbo.CetakPenerimaangudang ?",
        [$noBukti]
    );

    $tempCetak1 = [];
    foreach ($cetak as $p) {
        array_push($tempCetak1, $p);
    }

    return $tempCetak1;
}

public function getDetailPO (Request $req) {
  $tempBeli = VWtampilbeli::where('NoBukti' , $req->input('NoBukti'))->select('KodeBrg')->get()->toArray();
  // $tempBeli1 = [];
  // foreach ($tempBeli as $t) {
  //   // code...
  //   array_push($tempBeli1, $t->KodeBrg);
  // }
  $tempDetail = vwOUtPOWMS::select()->where('NoBukti', $req->input('NoPO'))->whereNotIn('KodeBrg', $tempBeli)->get();
  $poDetail = [];
  foreach ($tempDetail as $t) {
    // code...
    array_push($poDetail,$t);
  }
  return $tempDetail;
}

public function spBeliGudang (Request $req) {
  // return $req;
  // $data = $req->input('data');
  // $dataLPB = $req->input('dataLPB');
  // $choice = $req->input('choice');
  // $qtyTerima = $req->input('qtyTerima');
  // $qntTerima1 = 0;
  // $qntTerima2 = 0;
  // if ($data['NOSAT'] == 1) {
  //   $qntTerima1 = $qtyTerima;
  //   $qntTerima2 = $qtyTerima / $data['ISI2'];
  // } else if ($data['NOSAT'] == 2) {
  //   $qntTerima1 = $qtyTerima * $data['ISI2'];
  //   $qntTerima2 = $qtyTerima;
  // }

  //=======================
  $values = [
    $req->input('choice'),
    $req->input('reqNoBukti'),
    $req->input('reqNoUrut'),
    $req->input('reqTANGGAL'),
    $req->input('reqKodeSupp'),
    $req->input('reqKodeGudang'),
    $req->input('reqNoPO'),
    $req->input('reqKeterangan'),
    $req->input('reqFakturSupp'),
    $req->input('reqUrut'),
    $req->input('reqKodeBarang'),
    $req->input('reqUrutPO'),
    $req->input('reqQtyTerima'),
    $req->input('reqNoSat'),
    $req->input('reqSatuan'),
    $req->input('reqIsi'),
    $req->input('reqQtyTerima1'),
    $req->input('reqQtyTerima2'),
    $req->input('reqNamaBarang'),
    "",
    $req->input('reqQtyReject'),
    $req->input('reqQtyReject1'),
    $req->input('reqQtyReject2'),
    $req->input('reqPBeliJasa'),
    $req->input('reqEd')
  ];

  DB::connection('SML')->statement('exec sp_BeliGudang ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

  return 1;

  //=============================

//   @Choice varchar(1),
// @NoBukti varchar(20),
// @NoUrut varchar(10),
// @Tanggal datetime,
// @KodeSupp varchar(15),
// @KodeGdg varchar(15),
// @NoPO varchar(20),
// @Keterangan varchar(200),
// @FakturSupp Varchar(20),
// @Urut int,
// @KodeBrg varchar(25),
// @UrutPO int,
// @QntTerima numeric(18,2),
// @NoSat tinyint,
// @Satuan varchar(5),
// @Isi numeric(18,2),
// @Qnt1Terima numeric(18,2),
// @Qnt2Terima numeric(18,2),
// @NamaBrg varchar(100),
// @NoBatch Varchar(50),
// @QntReject Numeric(18,2)=0,
// @QntReject1 Numeric(18,2)=0,
// @QntReject2 Numeric(18,2)=0,
// @pBeliJasa Bit=0,
// @Ed datetime=null
  // DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
}


  // ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK , ISEXPORT , IsOtorisasi1, IsOtorisasi2 , IsOtorisasi3 , IsOtorisasi4, IsOtorisasi5 ,
  // TIPE, IsBatal, pembatalan

public function getNoBukti (Request $req) {
  // $values = [
  //   'a'
  // ];
  // return 'tes';
  // $po = DB::connection("SML")->select('exec sp_outstanding_po ?',$values);
  // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
  // $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
  $username = \Auth::user()->username;
  // return [$periode->bulan,$inisial[0]->PBL,$username];
  $values = [
      $inisial[0]->PBL,
      $periode->bulan,
      $periode->tahun,
      $username
  ];
  $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  return $noBukti;
}

public function addDBBeli (Request $req) {
  // try {

        $data = $req->input('data');
        $suratJalan = $req->input('suratJalan');
        $noKend = $req->input('noKend');
        $noPO = $req->input('noPO');
        // $date = date("Y-m-d H:i:s");
        $date = $req->input('inputTanggal');
        $gudang = $req->input('gudang');
        $noBukti = $req->input('noBukti');
        $noUrut = $req->input('noUrut');
        $username = \Auth::user()->username;
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


        $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :nobukti',["nobukti" => $noBukti]);
        if ($check) {
          return 2;
        }
        // delete	TempOutstandingPO where IDUser=@IDUser
        DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);
        foreach ($data as $d) {
          $values = [$username,$noPO,$periode->tahun,$periode->bulan, 0,$d['inputQntTerima'], 1, $d['Urut']];
          DB::connection("SML")->statement('exec sp_RefreshTempOutstandingPOweb ?,?,?,?,?,?,?,?',$values);

        }
        // $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :id' , ['id' => $noBukti]);
        // if (count($check) == 0) {

          $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 0, $username];
        // } else {

          // $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 1, $username];
        // }
        DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
        return 1;
//         @NoBukti varchar(20),
// @NoUrut varchar(10),
// @Tanggal datetime,
// @KodeGdg varchar(20),
// @NoPO varchar(20),
// @FakturSupp varchar(200),
// @Keterangan Varchar(5000),
// @pjasa Bit=0,
// @JmlRecord Integer,
// @iduser varchar(20)
  // } catch (\Exception $e) {
  //
  // }
}

    public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC CetakPenerimaanGudang ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }


}
