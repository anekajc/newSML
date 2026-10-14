<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



// Terima PO Customer -- port dari programajcSML (menu yang sudah dipakai user).
// Query, SP (Sp_POCustomer) dan aturan close disalin apa adanya; tambahan newSML:
// filter Periode (default = bulan periode aktif, sama dengan perilaku lama),
// validasi input di server, dan cek No PO ganda juga saat koreksi.
class TerimaPoCustomerController extends Controller
{

  public function index(Request $req) {
    // Akses dicek lewat href (DBMENUWEB/DBFLMENUWEB), kode menu di DBSMLBARU = 04015.
    $kodemenu = '04015';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    [$tglawal, $tglakhir] = $this->rangePeriode($req, $periode);

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $data = $this->ambilData($tglawal, $tglakhir);

    return view('marketing.terimapocustomer' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "tempOutstanding" => $data['tempOutstanding'],
      "tempOutstanding2" => $data['tempOutstanding2'],
      "tempOutstanding3" => $data['tempOutstanding3'],
      "akses" => $akses,
    ]);

  }

  // Filter Periode di toolbar mengirim tglawal/tglakhir; kalau tidak dikirim
  // (initial load) jatuh ke bulan/tahun periode aktif -- sama dengan programajcSML
  // yang memfilter MONTH/YEAR(TglInput) = periode.
  private function rangePeriode (Request $req, $periode) {
    $tglawal = $this->tanggalFilter($req->tglawal);
    $tglakhir = $this->tanggalFilter($req->tglakhir);
    if ($tglawal && $tglakhir) {
      return [$tglawal, $tglakhir];
    }
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->copy()->startOfMonth()->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  private function ambilData ($tglawal, $tglakhir) {

    // Tab 1: PO Customer yang belum dibuat SO dan belum di-close.
    $tempOutstanding = DB::connection("SML")->select("
      select A.ID,a.TglInput,a.TglTerima,a.POCustomer,a.KodeCustSupp,b.NAMACUSTSUPP,
      ltrim(B.ALAMAT1+case when ltrim(B.ALAMAT2)<>'' then char(13)+B.ALAMAT2 else '' end+
      	case when ltrim(isnull(B.KOTA,''))<>'' then char(13)+isnull(B.KOTA,'')+' '+B.KodePos else '' end) ALAMAT
        ,A.rppo,A.isclose,A.userclose,A.tglclose,A.ketclose
      from DBPOCUSTSUPP a
      left outer join DBCUSTSUPP b on a.KodeCustSupp=b.KODECUSTSUPP

      left outer join (select KODECUST,NoPesanan, isnull(IDPOCUST,0) IDPOCUST
      				from dbso) C on a.KodeCustSupp=c.KODECUST and a.POCustomer=c.NoPesanan
      where a.TglInput >= :tglawal and a.TglInput < dateadd(day, 1, cast(:tglakhir as date)) and isnull(A.Pganti,0)=0

      and C.nopesanan is null and isnull(IsCLose,0)=0
      order by a.TglInput, A.ID
      " ,["tglawal" => $tglawal , "tglakhir" => $tglakhir] );

    // Tab 2: Monitoring PO Customer - SO (lead time terima PO s/d otorisasi terakhir SO).
    $tempOutstanding2 = DB::connection("SML")->select("
          select A.ID,a.TglInput,a.TglTerima,a.POCustomer,a.KodeCustSupp,b.NAMACUSTSUPP,c.TglSO,
          c.NOSO,c.TotDPP,c.TglOto1,c.TglOto2,
          case when  C.PPO=1 then NULL else
          CASE WHEN

                  DATEDIFF (day,a.TglTerima,
          		  case when c.MAXOL=1 then C.TglOto1
          			   when c.MAXOL=2 then C.TglOto2
          			   when c.MAXOL=3 then C.TglOto3
          			   when c.MAXOL=4 then C.TglOto4
          			   when c.MAXOL=5 then C.TglOto5 End) < 0
             THEN NULL
            ELSE

          DATEDIFF (day,a.TglTerima,
          		  case when c.MAXOL=1 then C.TglOto1
          			   when c.MAXOL=2 then C.TglOto2
          			   when c.MAXOL=3 then C.TglOto3
          			   when c.MAXOL=4 then C.TglOto4
          			   when c.MAXOL=5 then C.TglOto5 End)
               eND
          End LeadTime
          ,A.rppo,A.isclose,A.userclose,A.tglclose,A.ketclose
          from DBPOCUSTSUPP a
          left outer join DBCUSTSUPP b on a.KodeCustSupp=b.KODECUSTSUPP
          left outer join (select b.NoPesanan,b.KODECUST,b.TANGGAL TglSO,b.TglOto1,b.TglOto2,b.TglOto3,b.TglOto4,b.TglOto5,b.MAXOL,
          				B.NOBUKTI NOSO,
          				SUM(a.NDPP) TotDPP,SUM(A.NNETRp) TotNNetRP ,isnull(B.PPO,0) PPO
          				 from DBSODET A
          				 left outer join DBSO b on a.NOBUKTI=b.NOBUKTI
          				 group by b.NoPesanan,b.KODECUST,b.TANGGAL ,b.TglOto1,b.TglOto2,B.NOBUKTI
          				 ,b.TglOto2,b.TglOto3,b.TglOto4,b.TglOto5,b.MAXOL ,isnull(B.PPO,0)
                          ) C on A.KodeCustSupp=C.KODECUST and A.POCustomer=C.NoPesanan
          where a.TglInput >= :tglawal and a.TglInput < dateadd(day, 1, cast(:tglakhir as date)) and isnull(A.Pganti,0)=0
          order by a.TglTerima, A.ID
      " ,["tglawal" => $tglawal , "tglakhir" => $tglakhir] );

    // Tab 3: PO Customer yang sudah di-close (belum ada SO).
    $tempOutstanding3 = DB::connection("SML")->select("
      select A.ID,a.TglInput,a.TglTerima,a.POCustomer,a.KodeCustSupp,b.NAMACUSTSUPP,
      ltrim(B.ALAMAT1+case when ltrim(B.ALAMAT2)<>'' then char(13)+B.ALAMAT2 else '' end+
      	case when ltrim(isnull(B.KOTA,''))<>'' then char(13)+isnull(B.KOTA,'')+' '+B.KodePos else '' end) ALAMAT
        ,A.rppo,A.isclose,A.userclose,A.tglclose,A.ketclose
      from DBPOCUSTSUPP a
      left outer join DBCUSTSUPP b on a.KodeCustSupp=b.KODECUSTSUPP

      left outer join (select KODECUST,NoPesanan, isnull(IDPOCUST,0) IDPOCUST
      				from dbso) C on a.KodeCustSupp=c.KODECUST and a.POCustomer=c.NoPesanan
      where a.TglInput >= :tglawal and a.TglInput < dateadd(day, 1, cast(:tglakhir as date)) and isnull(A.Pganti,0)=0

      and C.nopesanan is null and isnull(IsCLose,0)=1
      order by a.TglInput, A.ID
      " ,["tglawal" => $tglawal , "tglakhir" => $tglakhir] );

    return ["tempOutstanding" => $tempOutstanding,
            "tempOutstanding2" => $tempOutstanding2,
            "tempOutstanding3" => $tempOutstanding3
            ];
  }

  public function getCustomer(Request $req)
  {
      $search = $req->search;

      $data = DB::connection('SML')->select("
          SELECT TOP 20 KODECUSTSUPP, NAMACUSTSUPP
          FROM DBCUSTSUPP
          WHERE jenis = 1
            AND isaktif = 1
            AND (
                  KODECUSTSUPP LIKE :search1
                  OR NAMACUSTSUPP LIKE :search2
                )
          ORDER BY KODECUSTSUPP
      ", [
          "search1" => "%$search%",
          "search2" => "%$search%"
      ]);

      return response()->json($data);
  }

    // Hak akses menu ini (href terimapocustomer), dipakai spAdd/spClose.
    private function aksesMenu () {
      return app('App\Http\Controllers\GlobalController')->getAkses('04015', 'terimapocustomer');
    }

    public function spAdd (Request $req) {

      // Tambah butuh hak Tambah, koreksi butuh hak Koreksi.
      $akses = $this->aksesMenu();
      $hak = ($req->choice == "I") ? 'ISTAMBAH' : 'ISKOREKSI';
      if (!$akses || !$akses->HASACCESS || !$akses->$hak) {
        return 'No access';
      }

      $pocust = trim((string) $req->pocust);
      $kodecust = trim((string) $req->kodecust);

      if (!$req->tanggal) {
        return 'Tgl terima harus diisi';
      }
      if ($pocust === '') {
        return 'No PO Customer harus diisi';
      }
      if ($kodecust === '') {
        return 'Customer harus dipilih';
      }
      if (!is_numeric($req->totalpo ?: 0) || (float) $req->totalpo < 0) {
        return 'Total PO tidak boleh negatif';
      }

      // Kode customer harus ada di master (programajcSML membiarkan kode ketikan bebas
      // tersimpan). Tidak dicek isaktif supaya koreksi PO lama milik customer nonaktif tetap bisa.
      $cust = DB::connection('SML')->select('select KODECUSTSUPP from DBCUSTSUPP where KODECUSTSUPP = :kode and jenis = 1', ["kode" => $kodecust]);
      if (!$cust) {
        return 'Customer ' . $kodecust . ' tidak ditemukan, pilih dari daftar';
      }

      // No PO ganda: programajcSML hanya mengecek saat tambah (I); saat koreksi (U)
      // No PO bisa diganti menjadi No PO lain yang sudah ada. Sekarang dicek dua-duanya.
      if ($req->choice == "I" ) {
        $check = DB::connection('SML')->select('select ID from DBPOCUSTSUPP where pocustomer = :pocust',["pocust" => $pocust]);
      } else {
        $check = DB::connection('SML')->select('select ID from DBPOCUSTSUPP where pocustomer = :pocust and ID <> :id',["pocust" => $pocust, "id" => $req->id]);
      }
      if ($check) {
        return 'No PO Customer ' . $pocust . ' sudah ada';
      }

      $values = [
          $req->choice,
          $req->tanggal,
          $pocust,
          $kodecust,
          $req->id,
          $req->totalpo ?: 0,
      ];

      try {
        DB::connection('SML')->statement('exec Sp_POCustomer ?,?,?,?,?,?' ,$values);
      } catch (\Exception $e) {
        return 'Gagal menyimpan PO Customer: ' . $e->getMessage();
      }

      return 1;
    }


    public function loadAll (Request $req) {

      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      [$tglawal, $tglakhir] = $this->rangePeriode($req, $periode);

      return $this->ambilData($tglawal, $tglakhir);
    }


    public function spClose (Request $req) {
      // Close memakai hak Tambah (sama seperti programajcSML), keterangan wajib seperti Closing SO.
      $akses = $this->aksesMenu();
      if (!$akses || !$akses->HASACCESS || !$akses->ISTAMBAH) {
        return 'No access';
      }
      if (trim((string) $req->keterangan) === '') {
        return 'Keterangan closing harus diisi';
      }
      // Hanya PO outstanding (belum di-close, belum diganti, belum ada SO) yang bisa di-close --
      // mencegah close ganda dari dua layar yang terbuka bersamaan.
      $po = DB::connection('SML')->select("
        select A.ID from DBPOCUSTSUPP A
        where A.ID = :ID and isnull(A.IsCLose,0) = 0 and isnull(A.pGanti,0) = 0
          and not exists (select 1 from DBSO C where C.KODECUST = A.KodeCustSupp and C.NoPesanan = A.POCustomer)",
        ["ID" => $req->ID]);
      if (!$po) {
        return 'PO Customer sudah di-close atau sudah dibuat SO, silakan muat ulang halaman';
      }

      DB::connection('SML')->update("Update dbpocustsupp set isclose=1,userclose=:iduser ,
                  tglclose=getdate() ,ketclose =:keterangan where id= :ID
                  ", ["iduser" => \Auth::user()->username , "keterangan" => $req->keterangan ?? '' , "ID" => $req->ID ]);


    return 1;
  }

}
