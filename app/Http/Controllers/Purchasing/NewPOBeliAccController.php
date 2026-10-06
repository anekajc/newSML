<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\vwMasterBeli;
use App\Models\NEWDBBELI;

class NewPOBeliAccController extends Controller
{
 

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index (Request $req) {
    $kodemenu = '0304';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');

    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    list($pbaTglAwal, $pbaTglAkhir) = $this->periodeRange($periode);

    // Baris tabel digambar JS lewat loadAll() (lihat newpobeliacc.blade.php), jadi
    // index() tidak lagi perlu menyiapkan poGroup/tempPembelian1 - sama seperti
    // UangMukaBeliController@index.
    return view('purchasing.newpobeliacc' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "gudang" => $gudang,
      "pbaTglAwal" => $pbaTglAwal,
      "pbaTglAkhir" => $pbaTglAkhir,
      "akses" => $akses
    ]);


  }

  public function getAkses (Request $req) {
    $kodemenu = '0304';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, 'newpobeliacc');
    return $akses;
  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    // Konfigurasi kolom (susunan/lebar/desimal tersimpan per user) - sama seperti
    // UangMukaBeliController@loadAll.
    $reqHeader = new Request(['href' => 'newpobeliacc']);
    $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

    // Gabungan 4 menu lama - dibedakan lewat TipeBayar (0 Tunai/1 Kredit) dan pJasa
    // (0 Barang/1 Jasa). Satu tabel utuh (belum maupun sudah diotorisasi); penyaringan
    // Jenis & Otorisasi dikerjakan di browser lewat modal Filter, sama seperti Uang Muka Beli.
    //
    // fnc_masterbeli hanya menerima bulan/tahun, jadi rentang tanggal bebas (boleh lintas
    // bulan/tahun) dilayani dengan memanggilnya per bulan yang tersentuh rentang, lalu
    // menyaring baris persis ke rentang tanggal lewat TglBeli di SQL.
    $aliasRow = function ($r, $pjasa) {
      $r->pJasa = $pjasa;
      $r->{'Jenis'} = ($r->TipeBayar == 1)
        ? ($pjasa == 1 ? 'Jasa Kredit' : 'Kredit')
        : ($pjasa == 1 ? 'Jasa Tunai'  : 'Tunai');
      $r->{'No Bukti'}      = $r->NoBukti;
      $r->{'Tanggal'}       = $r->TglBeli ? date('Y-m-d', strtotime($r->TglBeli)) : null;
      $r->{'Nama Supplier'} = $r->NAMACUSTSUPP;
      $r->{'Keterangan'}    = $r->KETERANGAN;
      $r->{'No PO'}         = $r->NoPO;
      $r->{'Gudang'}        = $r->NAMAGUDANG ?? null;
      $r->{'Faktur Supp'}   = $r->FAKTURSUPP ?? null;
      $r->{'DPP'}           = $r->TotDPP;
      $r->{'PPN'}           = $r->TotPPN;
      $r->{'Total'}         = $r->TotNet;
      return $r;
    };

    $tempData = [];
    $cursor = date('Y-m-01', strtotime($tglawal));
    $batas  = date('Y-m-01', strtotime($tglakhir));
    while ($cursor <= $batas) {
      $bulan = (int) date('n', strtotime($cursor));
      $tahun = (int) date('Y', strtotime($cursor));
      foreach ([0, 1] as $pjasa) {
        $rows = DB::connection("SML")->select(
          "select * from dbo.fnc_masterbeli ( :bulan , :tahun, :pjasa)
           where TglBeli between :tglawal and :tglakhir",
          ["bulan" => $bulan, "tahun" => $tahun, "pjasa" => $pjasa, "tglawal" => $tglawal, "tglakhir" => $tglakhir]);
        foreach ($rows as $r) {
          array_push($tempData, $aliasRow($r, $pjasa));
        }
      }
      $cursor = date('Y-m-01', strtotime($cursor . ' +1 month'));
    }

    // Terbaru di atas - digabung dari beberapa pemanggilan fungsi jadi diurutkan di PHP.
    usort($tempData, function ($a, $b) {
      $cmp = strcmp((string) $b->TglBeli, (string) $a->TglBeli);
      return $cmp !== 0 ? $cmp : strcmp((string) $b->NoBukti, (string) $a->NoBukti);
    });

    return [
      "listData1"         => $tempData,
      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "isnumeric"         => $header['isnumeric'],
      "headertablevalue"  => $header['headertablevalue'],
      "isparsed"          => $header['isparsed'],
      "isshown"           => $header['isshown'],
      "desimal"           => $header['desimal'],
    ];
  }

public function onChangeHeader (Request $req) {
    $query = 'update dbbeli set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    // exec sp_UpdateBeli '+QuotedStr(NoBukti.Text)+',:0 
    $values = [ $req->nobukti,11];
    DB::connection('SML')->statement('exec sp_UpdateBeli ?,?', $values);
    return $res;

  }

public function spUpdateSO (Request $req) {
    $res = DB::connection('SML')->update('exec sp_UpdateBeli ?,11', [$req->nobukti]);



    return $res;
  }


  public function spOtorisasi1 (Request $req) {
      $username = \Auth::user()->username;
      $nobukti =  $req->nobukti;
      $otorisasi = $req->otorisasi;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


      if ($otorisasi == 0 ) {
        $username = '';
        $tanggal = null;
      }


      $update = DB::connection('SML')->update("update dbbeli set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = getDate() , MaxOL = 1 where nobukti = :nobukti", ['otorisasi' => $otorisasi, 'username' => $username,  'nobukti' => $nobukti ] );
    
     $values = [
      '',
      'dbbeli',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      1
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);

    
    
    
      return $update;

    }


    public function spUnOtorisasi1 (Request $req) {
        $username = \Auth::user()->username;
        $nobukti =  $req->nobukti;
        $otorisasi = $req->otorisasi;
         $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        if ($otorisasi == 0 ) {
          $username = '';
          $tanggal = null;
        }


        $update = DB::connection('SML')->update("update dbbeli set IsOtorisasi1 = 0 , OtoUser1 = '' , TglOto1 = NULL, MaxOL = -1 where nobukti = :nobukti", [  'nobukti' => $nobukti ] );
       
         $values = [
      '',
      'dbbeli',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
       
        return $update;

      }

public function getDetailPembelian (Request $req) {
  $nobukti = $req->NoBukti;

  // Bulan/tahun HARUS diambil dari tanggal dokumennya sendiri, bukan periode kerja user
  // yang sedang aktif - kalau tidak, dokumen dari bulan lain (filter tanggal di luar
  // periode berjalan, seperti di grid newpobeliacc yang boleh lintas bulan) selalu
  // kembali kosong. Sama seperti NewPOController::getDetailPembelian.
  $header = DB::connection('SML')->select('select TANGGAL from dbBeli where NOBUKTI = :nobukti', ['nobukti' => $nobukti]);
  if ($header) {
    $bulan = (int) date('n', strtotime($header[0]->TANGGAL));
    $tahun = (int) date('Y', strtotime($header[0]->TANGGAL));
  } else {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $bulan = $periode->bulan;
    $tahun = $periode->tahun;
  }

  // Halaman gabungan melayani baris jasa maupun non-jasa dalam satu tabel, jadi
  // pjasa dikirim dari baris yang dipilih di JS (lihat renderTabelPBA() di
  // newpobeliacc.blade.php), bukan lagi dipatok 0/1 per controller.
  $pembelian = DB::connection("SML")->select("select * from dbo.fnc_Tampilbeli ( :bulan , :tahun, :pjasa) where NoBukti = :NoBukti" , ["bulan" => $bulan, "tahun" => $tahun , "pjasa" => (int) ($req->pjasa ?? 0), "NoBukti" => $nobukti]);

  // Sama seperti getAllPembelian - TANGGAL bisa NULL kalau barang belum di-QC, fallback ke tanggal dbBeli.
  if ($header) { foreach ($pembelian as $p) { if (empty($p->TANGGAL)) { $p->TANGGAL = $header[0]->TANGGAL; } } }

  $tempPembelian1 = [];
  foreach ($pembelian as $p) {
    array_push($tempPembelian1, $p);
  }
  return $tempPembelian1;
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
    $req->input('reqEd'),
    $req->input('reqDisc'),
    $req->input('reqDiscRp'),
    $req->input('reqHarga')

  ];

  DB::connection('SML')->statement('exec sp_BeliGudangACC ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

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

public function spCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      // Hasil SP
      $cetak = DB::connection("SML")->select(
          "EXEC CetakPenerimaanACC ?",
          [$noBukti]
      );

      $dbumjual = collect();

      if(count($cetak) > 0){

          $dbumjual = DB::connection("SML")
              ->table("DBUMJUAL")
              ->select("NOBUKTI","NOSO","DPP")
              ->where("NOSO", $cetak[0]->NoPO)
              ->get();

      }

      return response()->json([
          'detail'    => $cetak,
          'dbumjual'  => $dbumjual
      ]);
  }

// ADD LAMA


  // public function addDBBeli (Request $req) {
  //   DB::beginTransaction();
  //   try {
  //     $tesMaxNumber = NEWDBBELI::all()->count();
  //     if ($tesMaxNumber) {
  //       $tesMaxNumber = NEWDBBELI::max('NoBukti');
  //       $tesMaxNumber = $tesMaxNumber + 1;
  //     } else {
  //       $tesMaxNumber = 1;
  //       // return 0;
  //
  //     }
  //     // $tesMax = vwOUtPOWMS::max('NoBukti');
  //     // if ($tesMaxNumber) {
  //     //   $tesMaxNumber = $tesMaxNumber + 1;
  //     // } else {
  //     //   $tesMaxNumber = 1;
  //     // }
  //     //
  //     $data = $req->input('data');
  //     $suratJalan = $req->input('suratJalan');
  //     $noKend = $req->input('noKend');
  //     $noPO = $req->input('noPO');
  //     $date = date("Y-m-d H:i:s");
  //     // $dateStr = strtotime($date);
  //     // $monthName = date('F',$dateStr);
  //     // $monthNo = date('m',$dateStr);
  //     // $year = date('Y',$dateStr);
  //     // $noBukti = "LPB/" . $monthNo . substr($year , -2) . "/" . "0002";
  //     foreach($data as $d) {
  //       NEWDBBELI::create([
  //         'Tanggal' => $date,
  //         'NamaSupplier' => $d['NAMACUSTSUPP'],
  //         'NoPo'=> $noPO,
  //         'Gudang' => "-",
  // 				'FakturSupp' => "-",
  //         'KodeBarang' => $d['KodeBrg'] ,
  //         'NamaBarang' => $d['namaBrg'],
  //         'Qty' => $d['QNT'],
  //         'QtyPO'=> $d['QntPO'],
  //         'QtyOS' =>$d['QNTOS'],
  //         'Satuan' => $d['Satuan'],
  //         'NoSO'=> "-",
  //         'NoPOCust' => "-",
  //         'Customer' => "-",
  //         'SuratJalan' => $suratJalan,
  //         'NoKendSopir' => $noKend,
  // 				'NoBukti' => $tesMaxNumber
  //       ]);
  //     }
  //     DB::commit();
  //
  //     return 1;
  //     // $check = NewMenu::where('KODEMENU', $req->input('KODEMENU'))->count();
  //     // if($check == 0) {
  //     //   NewMenu::create([
  //     //     'KODEMENU'=> $req->input('KODEMENU'),
  //     //     'Keterangan'=> $req->input('Keterangan'),
  //     //     'L0'=> $req->input('L0') ,
  //     //     'ACCESS' => $req->input('ACCESS'),
  //     //     'href' => $req->input('href')
  //     //   ]);
  //     //   $users = NewUsers::all();
  //     //   foreach ($users as $u) {
  //     //     $access = False;
  //     //     if ($u['username'] == 'admin') {
  //     //       $access = True;
  //     //     }
  //     //     NewAksesMenu::create([
  //     //       'USERID' => $u->id,
  //     //       'L1' => $req->input('KODEMENU'),
  //     //       'HASACCESS' => $access
  //     //     ]);
  //     //   }
  //     //   DB::commit();
  //     //   return 1;
  //     // } else {
  //     //   return 0;
  //     // }
  //   } catch (\Exception $e) {
  //     DB::rollback();
  //   }
  // }


// END ADD LAMA



}
