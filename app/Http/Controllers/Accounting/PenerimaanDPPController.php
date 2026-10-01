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





class PenerimaanDPPController extends Controller


{
  // Nama href untuk DBHEADERTABLE. Dipatok (bukan dari $req->path()) supaya sama
  // persis dengan PDPP_HREF / OUT_HREF di penerimaandpp.blade.php. Dua tabel di
  // halaman ini menyimpan susunan kolomnya masing-masing, jadi href-nya dua.
  const HREF     = 'penerimaandpp';
  const HREF_OUT = 'penerimaandppoutstanding';

  // Kolom default tabel Penerimaan DPP (gabungan belum + sudah otorisasi). Nama key
  // HARUS sama persis dengan alias kolom di loadAll() karena dipakai juga sebagai
  // nama field data di JS. tipe: 0 = varchar, 1 = float, 2 = date.
  // IsOtorisasi1/OtoUser1/TglOto1 sengaja TIDAK dimasukkan - itu kolom teknis untuk
  // filter otorisasi dan kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS.
  private function kolomDefault () {
    return [
      'No Bukti'  => 0,
      'Tanggal'   => 2,
      'Kode Cust' => 0,
      'Nama Cust' => 0,
      'DPP'       => 0,
      'Dibayar'   => 1,
      'LB'        => 1,
      'KL'        => 1,
    ];
  }

  // Kolom default tabel Outstanding DPP. Aturannya sama dengan kolomDefault():
  // nama key = alias kolom di queryOutstanding() = nama field di JS.
  private function kolomDefaultOutstanding () {
    return [
      'No Bon'    => 0,
      'Tanggal'   => 2,
      'Kode Cust' => 0,
      'Nama Cust' => 0,
      'No Faktur' => 0,
      'Valas'     => 0,
      'Penagih'   => 0,
    ];
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user.
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

  /* Rentang tanggal tab Outstanding DPP. Aturannya sama persis dengan
     rentangTanggal() milik tab Penerimaan DPP dan dengan halaman purchasing:
     default = tanggal 1 sampai akhir bulan periode kerja user. Yang membedakan hanya
     nama parameternya (outtglawal/outtglakhir) karena tiap tab punya kotak sendiri. */
  private function rentangTanggalOutstanding (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('outtglawal')))  { $tglawal  = $req->input('outtglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('outtglakhir'))) { $tglakhir = $req->input('outtglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    return [$tglawal, $tglakhir];
  }

  /**
   * Susunan/tampil/desimal kolom milik user untuk tabel Penerimaan DPP.
   *
   * Bentuk hasilnya sengaja dibuat sama persis dengan
   * HeaderTableController@getHeaderTable supaya blade bisa memakai pola ReportTable
   * (geser & sembunyikan kolom) tanpa perlu menambah cabang baru di
   * HeaderTableController. Penyimpanannya tetap memakai tabel DBHEADERTABLE dan
   * route 'saveheadertable' yang sudah generik.
   *
   * $href + $kolomDefault dipakai supaya fungsi ini melayani dua tabel: tabel
   * Penerimaan DPP (self::HREF) dan tabel Outstanding DPP (self::HREF_OUT).
   */
  private function headerTable ($href, $kolomDefault, $reset = false) {
    $username = \Auth::user()->username;

    if ($reset) {
      DB::connection("SML")->update(
        "delete from DBHEADERTABLE where username = :username and href = :href",
        ["username" => $username, "href" => $href]
      );
    }

    $headertable = DB::connection("SML")->select(
      "select * from dbheadertable where href = :href and username = :username",
      ["username" => $username, "href" => $href]
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
      foreach ($kolomDefault as $key => $tipe) {
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

  /* Dipanggil tombol "Reset kolom" di bar kolom tersembunyi (ReportTable).
     Parameter `tabel` menentukan tabel mana yang di-reset: 'outstanding' untuk tabel
     Outstanding DPP, selain itu tabel Penerimaan DPP. */
  public function resetHeader (Request $req) {
    if ($req->input('tabel') === 'outstanding') {
      return $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding(), true);
    }
    return $this->headerTable(self::HREF, $this->kolomDefault(), true);
  }

  // Satu query gabungan untuk tabel Penerimaan DPP. Dulu dua query terpisah
  // (IsOtorisasi1 = 0 dan <> 0) yang mengisi dua tab; sekarang penyaringan otorisasi
  // dikerjakan di browser lewat modal Filter. Kolom dialiaskan supaya namanya sama
  // persis dengan kolomDefault(). Urutan terbaru di atas.
  private function queryPenerimaan ($tglawal, $tglakhir) {
    return DB::connection('SML')->select("
Select  A.NoBukti,
        A.NoBukti                             as [No Bukti],
        Convert(varchar(10), A.Tanggal, 23)   as [Tanggal],
        A.KODECUSTSUPP                        as [Kode Cust],
        A.NamaCustSupp                        as [Nama Cust],
        A.NoDPP                               as [DPP],
        Round(Sum(A.DIBAYAR),0)               as [Dibayar],
        Sum(A.LB)                             as [LB],
        Sum(A.KL)                             as [KL],
        A.NoUrut, A.NamaKota, A.Penagih,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
        A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
        A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
From vwTransTerimaDPP A
where A.Tanggal between :tglawal and :tglakhir and A.pPLD=0
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
  A.NoDPP, A.NamaKota,
  A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
  A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
  A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
order by A.Tanggal desc, A.NOBUKTI desc", [
      "tglawal" => $tglawal, "tglakhir" => $tglakhir
    ]);
  }

  /* Tabel Outstanding DPP. Kolomnya dialiaskan supaya namanya sama persis dengan
     kolomDefaultOutstanding() - nama itu dipakai juga sebagai nama field data di JS.
     NOBUKTI + Urut tetap dibawa apa adanya karena dipakai buttonAdd().

     Penyaringan tanggalnya `between` seperti queryPenerimaan(). */
  private function queryOutstanding ($tglawal, $tglakhir) {
    return DB::connection('SML')->select("
select A.NoBukti+A.KODECUSTSUPP KeyNOBUKTI, A.Urut,
       A.NOBUKTI,
       A.NOBUKTI                            as [No Bon],
       Convert(varchar(10), A.TANGGAL, 23)  as [Tanggal],
       A.KODECUSTSUPP                       as [Kode Cust],
       A.NAMACUSTSUPP                       as [Nama Cust],
       A.NOFAKTUR                           as [No Faktur],
       A.Valas                              as [Valas],
       A.Penagih                            as [Penagih],
       A.NoUrut
from vwBrowsOutDPP A
where A.TANGGAL between :tglawal and :tglakhir
group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
         A.Valas, A.Penagih, A.NOFAKTUR, A.Urut
order by A.NOBUKTI, A.KODECUSTSUPP", [
      "tglawal" => $tglawal, "tglakhir" => $tglakhir
    ]);
  }

  public function index(Request $req) {
    $kodemenu = '02010';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $username = \Auth::user()->username;


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    $listPerkiraanKLLB = DB::connection('SML')->select("
    select * from DBPERKIRAAN where Tipe = 1
    and perkiraan in (select perkiraan from DBpostHUTPIUT where KODE = 'SLS')
    and Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )" , [ "username" => $username]);

    $perkiraan = DB::connection("SML")->select("Select a.Perkiraan,b.Keterangan
    from dbPosthutpiut a
    left outer join dbperkiraan b on b.perkiraan=a.perkiraan
    where (a.Kode='KAS' or A.IsBeliJual=1)
    and A.perkiraan in (select perkiraan from dbaksesperkiraanBS where userID= :username )
    Order by a.Perkiraan" , ["username" => $username]);







$penagih =   DB::connection("SML")->select("select a.Penagih
from dbKaryawan a
Group by Penagih
                       order by a.Penagih");
    list($pdppTglAwal, $pdppTglAkhir) = $this->periodeRange($periode);

    // Periode awal tab Outstanding DPP: tanggal 1 s/d akhir bulan periode kerja,
    // sama seperti tab Penerimaan DPP dan halaman purchasing.
    list($outTglAwal, $outTglAkhir) = $this->rentangTanggalOutstanding($req, $periode);

    // Valas untuk dropdown (dulu input teks + tombol browsing).
    $listValas = DB::connection("SML")->select("select KodeVls, NamaVls, Kurs from dbValas order by KodeVls");

    // Kedua tabel (Outstanding DPP & Penerimaan DPP) digambar JS lewat loadAll(),
    // jadi tidak ada lagi data baris yang dikirim dari sini.
    return view('accounting.penerimaandpp' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "penagih" => $penagih,
      "listValas" => $listValas,
      "pdppTglAwal" => $pdppTglAwal,
      "pdppTglAkhir" => $pdppTglAkhir,
      "outTglAwal" => $outTglAwal,
      "outTglAkhir" => $outTglAkhir,
      "tempListPerkiraanKLLB" => $listPerkiraanKLLB
    ]);

  }
  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);
    $tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir);

    list($outAwal, $outAkhir) = $this->rentangTanggalOutstanding($req, $periode);
    $tempOutstanding = $this->queryOutstanding($outAwal, $outAkhir);

    // Dua tabel, dua susunan kolom tersimpan (lihat headerTable()).
    $header    = $this->headerTable(self::HREF, $this->kolomDefault());
    $headerOut = $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding());

    return [
      "tempOutstanding"   => $tempOutstanding,
      "tempPenerimaan"    => $tempPenerimaan,

      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "headertablevalue"  => $header['headertablevalue'],
      "isnumeric"         => $header['isnumeric'],
      "isshown"           => $header['isshown'],
      "isparsed"          => $header['isparsed'],
      "desimal"           => $header['desimal'],

      // Konfigurasi kolom tabel Outstanding DPP - bentuknya sama, diawali "out".
      "outaliasordered"      => $headerOut['aliasordered'],
      "outheadertableheader" => $headerOut['headertableheader'],
      "outheadertablevalue"  => $headerOut['headertablevalue'],
      "outisnumeric"         => $headerOut['isnumeric'],
      "outisshown"           => $headerOut['isshown'],
      "outisparsed"          => $headerOut['isparsed'],
      "outdesimal"           => $headerOut['desimal'],
    ];

  }

  public function detailOutstanding (Request $req) {
    $tempOutstanding = DB::connection('SML')->select("


select A.NoBukti+A.KODECUSTSUPP KeyNOBUKTI,
  A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
  A.Valas, A.Penagih,A.NOFAKTUR
from vwBrowsOutDPP A
where A.NOBUKTI = :nobukti
group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
  A.Valas, A.Penagih,A.NOFAKTUR
order by A.NOBUKTI, A.KODECUSTSUPP",[
  "nobukti" => $req->nodpp]);
  return $tempOutstanding;
  }


    public function detailKoreksi (Request $req) {
      $nobukti = $req->nobukti;

      $listInvoice = DB::connection("SML")->select("declare @NoBukti varchar(30)

      select 	@NoBukti= :nobukti

      Select 	A.*, P.Keterangan NamaPerkiraan
      From vwTransTerimaDPP A
      left outer join dbPerkiraan P on P.Perkiraan=A.Perkiraan
      where	A.NoBukti=@NoBukti
      order by A.KasBank, A.NoFaktur, A.Perkiraan, A.Urut" , ["nobukti" => $nobukti]);

      $listGiro = DB::connection("SML")->select("        declare @NoBukti varchar(30), @Kas varchar(20)

      select 	@NoBukti= :nobukti , @Kas=''

       Select 	A.NoGiro , A.Bank , a.Kodevls , a.Kurs , a.Keterangan , a.TglGiro  , case when A.KodeVls='IDR' then A.DebetRp else Debet end Jumlah
       From DBGIRO A
      where	A.BuktiBuka=@NoBukti
      order by A.NoGiro, A.Bank
      " , ["nobukti" => $nobukti]);

      $listRekap = DB::connection("SML")->select("declare @NoBukti varchar(30)

      select 	@NoBukti= :nobukti

      Select 	A.TipeKasBank, A.MyTipeKasBank, MIN(A.KasBank) MinKasBank, SUM(A.Dibayar) Dibayar, SUM(A.LB) LB, SUM(A.KL) KL
      From vwTransTerimaDPP A
      where	A.NoBukti=@NoBukti
      group by A.TipeKasBank, A.MyTipeKasBank
      order by MIN(A.KasBank)" , ["nobukti" => $nobukti]);




      return [
        "listInvoice" => $listInvoice ,
        "listGiro" => $listGiro,
        "listRekap" => $listRekap
      ];




    }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CETAKTERIMADPP ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

      public function listPerkiraanLBKL (Request $req) {
        $username = \Auth::user()->username;

        $listData = DB::connection('SML')->select("
        select * from DBPERKIRAAN where Tipe = 1
        and perkiraan in (select perkiraan from DBpostHUTPIUT where KODE = 'SLS')
        and Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )" , [ "username" => $username]);
        return $listData;
      }

      public function listPerkiraanAdd (Request $req) {

        $listData = DB::connection('SML')->select("Select A.Perkiraan, B.Keterangan, A.Kode from dbPostHutPiut A
               left outer join dbPerkiraan B on B.Perkiraan=A.Perkiraan
               where A.Kode in ('KAS','BANK','GTR')");
        return $listData;
      }

      public function checkGiro (Request $req) {

        $listData = DB::connection('SML')->select("select nogiro, bank from dbgiro where nogiro = :nogiro and bank = :bank" ,
        ["nogiro" => $req->nogiro , "bank" => $req->bank ]);
        return $listData;


      }

        public function spProses (Request $req) {

          $username = \Auth::user()->username;
          $listData = $req->tempData ?? [];
          $listDataKL = $req->tempDataKL ?? [] ;

          // return [
          //   'x' => $listData,
          //   'y' => $listDataKL
          //
          // ];
          $jmlrecord = $req->jmlrecord;

          if ($jmlrecord == 0 ) {
            $check = DB::connection('SML')->select('select * from DBTerimaDPP where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
              if ($check) {
                return 2;
            }
          }
          $indexAdd = 0;


            foreach ($listData as $d)  {


              DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                $req->choice,
                $req->nobukti,
                $req->nourut,
                $req->tanggal ,
                $d['NODPP'],
                $req->kodecustsupp,
                $d['Valas'],
                $d['Kurs'],
                '',
                'DPP' ,
                $d['inputPerkiraanDibayar'] ,
                $req->urutTrans,
                0,
                $d['NOFAKTUR'] ,
                $d['DIBAYAR'] ,
                $d['inputPerkiraanDibayar'],
                0 ,
                0,
                $username ,
                0 ,
                $d['KodeCustSupp']

              ]);

              $jmlrecord = 1;

              if ($d['inputLB'] > 0 ) {
                DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                  $req->choice,
                  $req->nobukti,
                  $req->nourut,
                  $req->tanggal ,
                  $d['NODPP'],
                  $req->kodecustsupp,
                  $d['Valas'],
                  $d['Kurs'],
                  '',
                  'DPP' ,
                  $d['inputPerkiraanLB'] ,
                  $req->urutTrans,
                  0,
                  $d['NOFAKTUR'] ,
                  0 ,
                  $d['inputPerkiraanLB'],
                  0 ,
                  $d['inputLB'],
                  $username ,
                  0 ,
                  $d['KodeCustSupp']

                ]);

              }

              if (!empty($listDataKL[$indexAdd]) && is_array($listDataKL[$indexAdd])) {
                foreach ($listDataKL[$indexAdd] as $kl ) {
                  DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
                    $req->choice,
                    $req->nobukti,
                    $req->nourut,
                    $req->tanggal ,
                    $d['NODPP'],
                    $req->kodecustsupp,
                    $d['Valas'],
                    $d['Kurs'],
                    '',
                    'DPP' ,
                    $kl['inputPerkiraanKL'] ,
                    $req->urutTrans,
                    0,
                    $d['NOFAKTUR'] ,
                    0 ,
                    $kl['inputPerkiraanKL'],
                    $kl['inputKL'] ,
                    0,
                    $username ,
                    0 ,
                    $d['KodeCustSupp']

                  ]);
                }
              }




              $indexAdd++;
            }



            return 1;

        }

      public function spAdd (Request $req) {


        $username = \Auth::user()->username;

        if ($req->choice == "I" && $req->tipeform == 'add') {
          $check = DB::connection('SML')->select("select * from dbterimadpp where nobukti = :nobukti" ,
          ["nobukti" => $req->nobukti ]);
          if ($check) {
            return 2;
          }

        }
        $listData = $req->tempData;


        foreach ($listData as $d)  {
          DB::connection("SML")->update(
            "update DBTempTerimaDPP set IsTerima = 1 where urut = :urut and NODPP = :nodpp and IDuser = :username" ,
            ["urut" => $d['Urut'] , "nodpp" => $d['NODPP'] , "username" => $username = $username]
          );

        }

        $tempProses = DB::connection("SML")->select("select * from dbTempTerimaDPP where IDuser = :username" , ["username" => $username]);

        foreach ($tempProses as $p) {
          $values =
          [
            $req->choice,
            $req->nobukti,
            $req->nourut,
            $req->tanggal ,
            $p->NODPP,
            $req->kodecustsupp,
            $p->Valas,
            $p->Kurs,
            $req->penagih,
            'DPP' ,
            $p->KasBank,
            $p->UrutDPP,
            0,
            $p->NOFAKTUR ,
            $p->DIBAYAR ,
            $p->Perkiraan,
            $p->KL ,
            $p->LB ,
            $username ,
            0 ,
            $p->KodeCustSupp

          ];

          // return $values;


          DB::connection('SML')->update('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

        }

        return 1;


      }

      public function spKoreksi (Request $req) {
        $username = \Auth::user()->username;
        $values =
        [
          $req->choice ,
          $req->nobukti ,
          '' ,
          '',
          '' ,
          '',
          '' ,
          0 ,
          '' ,
          'DPP' ,
          '' ,
          '' ,
          $req->urut ,
          '' ,
          $req->dibayar ,
          $req->perkiraan ,
          $req->kl ,
          $req->lb ,
          $username ,
          0 ,
          '',
        ];
        $res = DB::connection('SML')->update('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
        return 1;

      }

      public function spGiro (Request $req) {

        if ($req->choice == "I") {
          $check = DB::connection('SML')->select("select nogiro, bank from dbgiro where nogiro = :nogiro and bank = :bank" ,
          ["nogiro" => $req->nogiro , "bank" => $req->bank ]);
          if ($check) {
            return 2;
          }

        }


        $values =
        [
          $req->choice ,
          $req->bank ,
          $req->nogiro ,
          $req->tanggalgiro ,
          $req->valas ,
          $req->kurs ,
          $req->nilaigiro ,
          $req->tanggal ,
          $req->nobukti,
          $req->keterangan ? $req->keterangan : ''  ,
          ''
        ];
        $res = DB::connection('SML')->update('exec sp_TerimaDPPGiro ?,?,?,?,?,?,?,?,?,?,?',$values);
        return 1;
      }


      public function listProses (Request $req) {
        $username = \Auth::user()->username;
        $values = [
          $req->nodpp,
          $req->kodecust,
          $req->perkiraan,
          $username
        ];
        $res = DB::connection('SML')->update('exec sp_RefreshTempTerimaDPP ?,?,?,?',$values);

        $listData = DB::connection('SML')->select("Select * from dbTempTerimaDPP where IDUser = :username" , ["username" => $username]);
        return $listData;
      }


      public function spOtorisasi (Request $req) {
        $tanggal = date('Y-m-d H:i:s');
        $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal , tglbatal = NULL, userbatal = '' where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
        return $res;
      }
      public function spBatalOtorisasi (Request $req) {
        $tanggal = date('Y-m-d H:i:s');
        $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL , tglbatal = :tanggal, userbatal = :username where nobukti = :nobukti", [ "nobukti" => $req->nobukti , "username" => \Auth::user()->username , "tanggal" => $tanggal ]);
        return $res;
      }







}
