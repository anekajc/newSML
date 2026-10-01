<?php


namespace App\Http\Controllers\Accounting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;

class PengajuanDPPController extends Controller


{

  // Nama href untuk DBHEADERTABLE. Dipatok (bukan dari $req->path()) supaya sama
  // persis dengan DPP_HREF di pengajuandpp.blade.php.
  const HREF = 'pengajuandpp';

  // Kolom default tabel daftar DPP. Nama key HARUS sama persis dengan alias kolom
  // di loadAll() karena dipakai juga sebagai nama field data di JS.
  // tipe: 0 = varchar, 1 = float, 2 = date.
  // IsOtorisasi1/OtoUser1/TglOto1 sengaja TIDAK dimasukkan - itu kolom teknis untuk
  // filter otorisasi dan kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS.
  private function kolomDefault () {
    return [
      'No Bukti' => 0,
      'Tanggal'  => 2,
      'Valas'    => 0,
      'Penagih'  => 0,
      'Debet'    => 1,
    ];
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user, sama seperti
  // PembelianPermintaanDebetNoteController@periodeRange.
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  // Rentang tanggal yang diminta browser, dijatuhkan ke periode kerja bila tidak valid.
  private function rentangTanggal (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    return [$tglawal, $tglakhir];
  }

  /**
   * Susunan/tampil/desimal kolom milik user untuk tabel daftar DPP.
   *
   * Bentuk hasilnya sengaja dibuat sama persis dengan
   * HeaderTableController@getHeaderTable supaya blade bisa memakai pola
   * ReportTable (geser & sembunyikan kolom) tanpa perlu menambah cabang baru di
   * HeaderTableController. Penyimpanannya tetap memakai tabel DBHEADERTABLE dan
   * route 'saveheadertable' yang sudah generik.
   */
  private function headerTable ($reset = false) {
    $username = \Auth::user()->username;

    if ($reset) {
      DB::connection("SML")->update(
        "delete from DBHEADERTABLE where username = :username and href = :href",
        ["username" => $username, "href" => self::HREF]
      );
    }

    $headertable = DB::connection("SML")->select(
      "select * from dbheadertable where href = :href and username = :username",
      ["username" => $username, "href" => self::HREF]
    );

    $headertableheader = [];
    $headertablevalue = [];
    $isnumberheadertable = [];
    $headerisshown = [];
    $isparsed = 0;

    if (count($headertable) > 0) {
      $isnumberheadertable = json_decode($headertable[0]->isnumber);
      $headertablevalue    = json_decode($headertable[0]->value);
      $headertableheader   = json_decode($headertable[0]->header);
      $headerisshown       = json_decode($headertable[0]->isshown);
    } else {
      $isparsed = 1;
      foreach ($this->kolomDefault() as $key => $tipe) {
        array_push($headertablevalue, $key);
        array_push($headertableheader, $key);
        array_push($headerisshown, 1);
        $isnumberheadertable[] = $tipe;
      }
    }

    $aliasOrdered = [];
    foreach ($headertablevalue as $header) {
      array_push($aliasOrdered, ["value" => $header, "alias" => $header]);
    }

    // Desimal dititipkan sebagai JSON array di kolom `tipe` DBHEADERTABLE - pola yang
    // sama dengan HeaderTableController@desimalHeaderTable.
    $tersimpan = [];
    if (count($headertable) > 0) {
      $decoded = json_decode($headertable[0]->tipe);
      if (is_array($decoded)) { $tersimpan = $decoded; }
    }

    $desimal = [];
    foreach ($isnumberheadertable as $i => $tipe) {
      if (isset($tersimpan[$i]) && is_numeric($tersimpan[$i])) {
        $nilai = (int) $tersimpan[$i];
        if ($nilai < 0) { $nilai = 0; }
        if ($nilai > 4) { $nilai = 4; }
        array_push($desimal, $nilai);
      } else {
        array_push($desimal, ((int) $tipe === 1) ? 2 : 0);
      }
    }

    return [
      "aliasordered"      => $aliasOrdered,
      "headertableheader" => $headertableheader,
      "headertablevalue"  => $headertablevalue,
      "isnumeric"         => $isnumberheadertable,
      "isshown"           => $headerisshown,
      "isparsed"          => $isparsed,
      "desimal"           => $desimal,
    ];
  }

  // Dipanggil tombol "Reset kolom" di bar kolom tersembunyi (ReportTable).
  public function resetHeader () {
    return $this->headerTable(true);
  }

  public function index(Request $req) {
    $kodemenu = '02009';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu,$req->path() );
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }




    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();





    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    list($dppTglAwal, $dppTglAkhir) = $this->periodeRange($periode);

$penagih =   DB::connection("SML")->select("select a.Penagih
from dbKaryawan a
Group by Penagih
                       order by a.Penagih");

    // Valas untuk dropdown di modal DPP (dulu tombol browsing yang tidak berfungsi).
    $listValas = DB::connection("SML")->select("select KodeVls, NamaVls, Kurs from dbValas order by KodeVls");

    // Baris tabel digambar JS lewat loadAll(), jadi index() tidak lagi menyiapkan
    // $tempOutstanding.
    return view('accounting.pengajuandpp' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "penagih" => $penagih,
      "listValas" => $listValas,
      "dppTglAwal" => $dppTglAwal,
      "dppTglAkhir" => $dppTglAkhir
    ]);

  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);

    // Kolom dialiaskan supaya namanya sama persis dengan kolomDefault() - alias itu
    // yang dipakai sebagai nama field data di JS (lihat dppBuatCart()/dppRenderNilai()).
    // Urutan terbaru di atas.
    $tempOutstanding = DB::connection("SML")->select("
Select  A.NoBukti,
        A.NoBukti                             as [No Bukti],
        Convert(varchar(10), A.Tanggal, 23)   as [Tanggal],
        a.Valas                               as [Valas],
        a.Penagih                             as [Penagih],
        Round(Sum(C.Debet),0)                 as [Debet],
        A.Nourut, a.IsOtorisasi1, a.OtoUser1, a.TglOto1
from dbDPH   a
Left Outer Join (select NoBukti,Min(Convert(Smallint,Isnull(IsClose,0)))IsClose from dbDPHdet Group By NoBukti)b on a.NoBukti=b.NoBukti
left outer join  (Select a.NoBukti,b.NAMACUSTSUPP,a.NoFaktur,a.KODECUSTSUPP,C.debet,C.jatuhtempo
                   From dbDPHDet a
                   Left Outer Join DBCUSTSUPP b on a.KODECUSTSUPP=b.KODECUSTSUPP
                   Left Outer join DbHutpiut C on A.NOfaktur=C.nofaktur  and C.Debet<>0
                   where  Isnull(A.IsClose,0)=0) C on A.nobukti=C.nobukti
where A.Tanggal between :tglawal and :tglakhir and a.Tipe='DPP' and Isnull(IsClose,0)=0
group by A.NoBukti, a.Tanggal, a.Valas, a.Penagih,A.Nourut , A.IsOtorisasi1, a.OtoUser1, a.TglOto1
order by a.Tanggal desc, A.NoBukti desc
" , ["tglawal" => $tglawal , "tglakhir" => $tglakhir ]);

    $header = $this->headerTable();

    return [
      "tempOutstanding"   => $tempOutstanding,
      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "headertablevalue"  => $header['headertablevalue'],
      "isnumeric"         => $header['isnumeric'],
      "isshown"           => $header['isshown'],
      "isparsed"          => $header['isparsed'],
      "desimal"           => $header['desimal'],
    ];
  }

  public function getListPengajuan (Request $req) {
    $username = \Auth::user()->username;
    $values = [
      $req->tglawal,
      $req->tglakhir,
      $req->valas,
      $req->tipe,
      $username,
      0
    ];

    $res = DB::connection('SML')->update('exec sp_CariHutangJT ?,?,?,?,?,?',$values);

      $tempOutstanding = DB::connection("SML")->select("
      Select * from dbTempHUTPIUTJt
      where IDUSER = :username" , ["username" => $username]);

      return $tempOutstanding;


  }


  public function getDetail (Request $req ) {
    $username = \Auth::user()->username;
      $x = DB::connection("SML")->statement("exec SP_TempKoreksiDPP ?,?" , [$req->nobukti, $username]);

        $tempOutstanding = DB::connection("SML")->select("

        select a.* , b.Penagih , b.Tanggal , b.IsOtorisasi1 from dbTempHutPiutJT a
        left outer join DBDPH b on a.Nobukti = b.NoBukti
         where IDUSER = :username

        " , ["username" => $username]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');

    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        $req->simbol
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?,?',$values);

    return $noBukti;
  }







  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbdph set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal  where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbdph set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti  ]);
    return $res;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $listData = $req->tempData;

    $jmlrecord = $req->jmlrecord;

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBDPH where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

      foreach ($listData as $d)  {

        // kalo 0 ambil dari kredit
        // kalo ada isi kredit - jmldibayar


        DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $req->valas,
          $req->penagih,
          $req->tipe ,
          0 ,
          $d['KodeCustSupp'],
          $d['NoFaktur'] ,
          $d['diBayar'] ,
          NULL,
          -1 ,
          $d['Perkiraan'],
          $d['KL'] ,
          $d['LB'] ,
          0 ,
          $jmlrecord ,
          $d['NOInvoice'] ? $d['NOInvoice'] : '',
          $d['TglInvoice'],
          $d['pCopy'],
          0,
          $username

        ]);

        $jmlrecord = 1;



      }

      return 1;

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CETAKPENGAJUANDPP ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function spkoreksi (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;


    DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal ,
      $req->valas,
      '',
      $req->tipe ,
      $req->urut,
      $req->kodecustsupp, //
      $req->nofaktur, //
      $req->dibayar, //
      NULL,
      -1 ,
      $req->perkiraan, //
      $req->kl, //
      $req->lb, //
      0 ,
      0 , //
      $req->noinvoice ? $req->noinvoice : '', //
      $req->tglinvoice, //
      $req->pcopy, //
      1,
      $username

    ]);

    return 1;


  }




}
