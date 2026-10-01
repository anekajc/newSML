<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class KreditNoteController extends Controller


// contoh desktop ce006
// sp_ProsesPostingHutPiut
// sp_ProsesPostingJurnalOto
{

  public function index(Request $req) {
    $kodemenu = '04110';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }





    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);



    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryKreditNote($tglawal, $tglakhir, 0);

    return view('marketing.kreditnote' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() -- dulu tabel (Belum,
  // tombol Koreksi/Otorisasi) dan tabel2 (Sudah, tombol Batal Otorisasi) adalah
  // 2 tab terpisah dengan query nyaris identik (cuma beda isOtorisasi1<>1/=1).
  // Digabung jadi satu dengan filterkn yang menyaring di server, port 1:1 dari
  // pola queryOutstanding() milik PerintahReturJualController.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryKreditNote ($tglawal, $tglakhir, $filterkn) {
    return DB::connection("SML")->select("
      select * from (
        select a.* , b.NAMACUSTSUPP from dbkreditnote a join DBCUSTSUPP b on a.KodeSupp = b.KODECUSTSUPP
        where a.Tanggal between ? and ?
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.isOtorisasi1,0) <> 1)
         or (? = 2 and isnull(x.isOtorisasi1,0) = 1)
    ", [$tglawal, $tglakhir, $filterkn, $filterkn, $filterkn]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tglawal = $req->tglawal ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $req->tglakhir ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterkn = $req->filterkn ?: 0;
    $tempOutstanding = $this->queryKreditNote($tglawal, $tglakhir, $filterkn);

    return ["tempOutstanding" => $tempOutstanding];
  }


  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("Select  c.KodeSupp,d.NamaCustSupp,d.Alamat1,Right(a.noBukti,4)NoUrut,a.Urut,a.NoBukti,c.tanggal,Keterangan,A.NoInv, b.Saldo, b.SaldoD,
        a.Nilai,
        Case when a.kodevls='IDR' then 0.00 else a.Nilai end NilaiValas,
        a.kodeVls, a.Kurs, a.NilaiRp , c.IsOtorisasi1
From  dbKreditNoteDet a
Left Outer Join dbKreditNote c On c.NoBukti=a.NoBukti
Left Outer Join (select a.NoFaktur,Sum(Saldo) Saldo, Sum(SaldoD) SaldoD
                 from dbo.vwHutPiut a
                 where a.Tipe='PT'
                 Group by a.NoFaktur) b On a.NoInv=B.Nofaktur
Left Outer Join dbCustSupp d On d.KodeCustSupp=c.KodeSupp
where a.NOBUKTI = :nobukti
order by a.urut" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select KN from DBNOMOR');


      $values = [
          $inisial[0]->KN,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    } else {
      $values = [
          'KNN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
    }

  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select b.NamaKota, a.KODECUSTSUPP, a.NAMACUSTSUPP , a.ALAMAT1 , a.PPN from dbcustsupp a
left outer join DBKOTA b on a.KOTA = b.KodeKota  where a.Jenis = 1");
    return $listData;
  }

  public function listInvoice (Request $req) {

    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= :kodecustsupp
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, a.Valas KodeVls, a.Kurs, a.NoFaktur,Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo,
           SUM(a.Saldo) Saldo, SUM(A.SaldoD) SaldoD
    from dbo.vwHutPiut a
         left outer join (select NoInv from dbkreditNoteDet) b on b.NoInv=a.NoFaktur
    where a.KodeCustSupp=@Awal and B.NoInv is null and a.Tipe='PT'
    Group by a.NoFaktur,a.Valas, a.Kurs
    Having SUM(a.Saldo)<>0 or SUM(A.SaldoD)<>0
    Order by a.Nofaktur
" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;

  }



  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $res = DB::connection('SML')->update("update dbkreditnote set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);

     $values = [
      '',
      'DBKREDITNOTE',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      1
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);


    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $res = DB::connection('SML')->update("update dbkreditnote set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);


     $values = [
      '',
      'DBKREDITNOTE',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);

    return $res;
  }

  public function spAdd (Request $req) {

      $tempData = $req->input('tempData');
      $username = \Auth::user()->username;


      if ($req->tipeform == 'add' ) {
        $check = DB::connection('SML')->select('select * from DBKREDITNOTE where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
      }

      foreach ($tempData as $d)  {
        // code...
        DB::connection('SML')->statement('exec Sp_KreditNote ?,?,?,?,?,?,?,?,?,?,?,?', [
          "I",
          $req->nobukti,
          $req->tanggal,
          $d['Keterangan'],
          0,
          $d['NoFaktur'],
          $req->kodecustsupp,
          $d['inputNilai'],
          $d['KodeVls'],
          $d['inputKurs'],
          $d['inputNilaiRp'],
          $req->nourut


        ]);
      }

      return 1;

  }

  public function spKoreksi (Request $req) {

    DB::connection('SML')->statement('exec Sp_KreditNote ?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->tanggal,
      $req->keterangan,
      $req->urut,
      $req->noinvoice,
      $req->kodecustsupp,
      $req->nilai,
      $req->kodevls,
      $req->kurs,
      $req->nilairp,

    ]);

    return 1;

  }











}
