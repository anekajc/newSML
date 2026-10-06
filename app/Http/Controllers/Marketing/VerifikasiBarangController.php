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


// Verifikasi Barang - item penawaran yang sudah diverifikasi (IsVerf=1) tetapi kode barangnya
// masih '-' diberi kode barang dari master DBBARANG. Logika dan query disalin dari
// programajcSML (menu yang sudah dipakai user); perbaikan bug ditandai komentar "Perbaikan".
class VerifikasiBarangController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '041040';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    $tempOutstanding = $this->queryOutstanding($periode);

    return view('marketing.verifikasibarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses,
    ]);

  }

  // Query daftar sama persis dengan programajcSML (filter periode memang dikomentari di sana:
  // semua item '-' yang sudah diverifikasi tampil, tidak dibatasi bulan).
  private function queryOutstanding ($periode) {
    return DB::connection("SML")->select("
    declare @Tahun int, @Bulan int,@pAgen Bit,@pJasa bit

    select @Tahun= :tahun , @Bulan= :bulan

    Select 	B.NOBUKTI,A.TANGGAL,B.KODEBRG,A.KODECUST,C.NAMACUSTSUPP,B.NAMABRG,B.QNT,B.HARGA,a.NAMAPIC,
    FRANCO,A.DELIVERY,A.VALIDITAS,B.ID,B.Nosat,B.Urut,D.tipe,D.NamaMerk,B.ketdet


    From DBPENAWARANSODet B
    Left Outer Join DBPENAWARANSO A on A.id =B.IDMASTER
    left outer join DBCUSTSUPP C on A.KODECUST=C.KODECUSTSUPP
    left outer join dbrefprdet d on b.norpr=d.nobukti and d.urut=b.urutRPR
    where /*year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and  */
     B.KODEBRG='-' and isnull(B.isverf,0)=1    --and year(a.tanggal)>=2025
    order by B.NoBukti
    " , ["tahun" => $periode->tahun , "bulan" => $periode->bulan] );
  }


  public function loadAllInfo () {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

$tempOutstanding2 = DB::connection("SML")->select("
    declare @bulan integer,@tahun integer,@periode varchar(6)

select @Tahun= :tahun , @Bulan= :bulan
Set @Periode=Cast(@tahun as varchar(4))+Case when @bulan<10 then '0' else '' end+Cast(@bulan as varchar(2))

select Z.KODEBRG
,Z.NamaBrg NamaBrgx,B.* ,Z.Lokasi
from DbBarang Z
left outer join (
				select H.Bulan,H.Tahun,B.KODEBRG,B.NamaBrg,B.qntmin,
				isnull(H.SALDOQNT,0) SALDOQNT,isnull(H.SALDO2QNT,0) SALDO2QNT,
			    isnull(F.QNTOS,0) Qnt1outPO,isnull(F.QNTOS2,0) Qnt2outPO,
			    G.Qnt1OutSo Qnt1OutSO,
				isnull(H.SALDOQNT,0) +isnull(F.QNTOS,0)-ISnull(G.Qnt1OutSo,0) Qnt1Fiktif,
				isnull(H.SALDO2QNT,0) +isnull(F.QNTOS2,0) -ISnull(G.Qnt2OutSo,0) Qnt2Fiktif

				from  DBBARANG B
				left outer join (Select  A.KodeBrg, A.NamaBrg,Sum(QNTOS) QntOS,Sum(QNTOS2) QNTOS2
								From vwOutstandingPO A   group by A.KodeBrg, A.NamaBrg
								) F on B.kodebrg = F.kodebrg

				left outer join (Select  A.kodebrg, B.NamaBrg,Sum(ISnull(A.QntSisa,0)) Qnt1OutSo,Sum(ISnull(A.Qnt2Sisa,0)) Qnt2OutSo
								From    vwBrowsOutSO_SPP A
								Left Outer Join dbBarang B on B.KodeBrg=A.KodeBrg
                                                                Left Outer Join DBSO P on P.NoBukti=A.NoBukti
                                                                where
                                                                          Cast(Case when Case when P.IsOtorisasi1=1 then 1 else 0 end+
                                                                       Case when P.IsOtorisasi2=1 then 1 else 0 end+
                                                                       Case when P.IsOtorisasi3=1 then 1 else 0 end+
                                                                       Case when P.IsOtorisasi4=1 then 1 else 0 end+
                                                                       Case when P.IsOtorisasi5=1 then 1 else 0 end=P.MaxOL then 0
                                                                  else 1
                                                             end As Bit)=0
								group by A.KodeBrg,B.NAMABRG
                                                                ) G on B.KODEBRG=G.KodeBrg

				left outer join (select kodebrg,bulan,tahun,Sum(ISnull(SALDOQNT,0)) SaldoQnt,Sum(isnull(SALDO2QNT,0)) Saldo2Qnt
								from DBSTOCKBRG where Bulan=@Bulan and Tahun=@Tahun and kodegdg<>'GTC' group by kodebrg,bulan,tahun) H on B.KODEBRG=h.KODEBRG

				group by  H.Bulan,H.Tahun,B.KODEBRG,B.NamaBrg,B.qntmin,H.SaldoQnt,H.Saldo2Qnt,
				F.QntOS,F.QNTOS2,G.Qnt1OutSo,G.Qnt2OutSo

) B on Z.KODEBRG=B.kodebrg
where Z.isaktif=1
order by  Z.KODEBRG ,Z.NamaBrg

" , ["tahun" => $periode->tahun , "bulan" => $periode->bulan]);

return $tempOutstanding2;
  }

  public function detailBarangx (Request $req) {

$tempOutstanding2 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int,@pAgen Bit,@pJasa bit


Select 	B.NOBUKTI,A.TANGGAL,B.KODEBRG,A.KODECUST,C.NAMACUSTSUPP,B.NAMABRG,B.QNT,B.HARGA,a.NAMAPIC,
FRANCO,A.DELIVERY,A.VALIDITAS,B.ID,B.Nosat


From DBPENAWARANSODet B
Left Outer Join DBPENAWARANSO A on A.id =B.IDMASTER
left outer join DBCUSTSUPP C on A.KODECUST=C.KODECUSTSUPP
where B.nobukti= :nobukti  and B.urut= :urut
order by B.NoBukti

" , ["nobukti" => $req->nobukti , "urut" => $req->urut ]);

return $tempOutstanding2;
  }


    public function loadAll () {

      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

      return ["tempOutstanding" => $this->queryOutstanding($periode) ];

    }

    // Perbaikan: di programajcSML teks pencarian ditempel langsung ke SQL (rawan error kalau
    // mengandung tanda kutip) dan kondisinya "IsAktif = 1 and (kode like) or (nama like)",
    // sehingga barang NON-AKTIF yang namanya cocok ikut muncul. Sekarang memakai binding dan
    // kurung, jadi hanya barang aktif yang tampil (sesuai maksud IsAktif = 1).
    public function listBarang (Request  $req) {
      $search = '%' . $req->input('search') . '%';

      $listBarangAll = DB::connection('SML')->select("
    SELECT
      Kodebrg,
       REPLACE( REPLACE(Namabrg, '\"', '') ,'''','') Namabrg,
      sat1,
      isi1,b.namamerk
  FROM dbbarang A
  left outer join DBMERK b on a.KodeMerk=b.KODEMERK
  WHERE IsAktif = 1
   and ((A.KodeBrg like :search1) or (a.NamaBrg like :search2))
", ["search1" => $search, "search2" => $search]);
      return $listBarangAll;
    }


    // Hak akses user untuk menu ini (DBFLMENUWEB lewat href, sama seperti index()).
    private function aksesMenu () {
      return app('App\Http\Controllers\GlobalController')->getAkses('041040', 'verifikasibarang');
    }

    public function spOtorisasi ( Request $req) {
      // Update kode barang = koreksi item penawaran -> butuh hak Koreksi.
      $akses = $this->aksesMenu();
      if (!$akses || !$akses->HASACCESS || !$akses->ISKOREKSI) {
        return 'No access';
      }

      // Perbaikan: programajcSML langsung menyimpan apa pun yang diketik di kolom Kode Barang,
      // termasuk kode yang tidak ada di master. Sekarang kode harus ada dan aktif di DBBARANG.
      $kodebrg = trim((string) $req->kodebrg);
      if ($kodebrg === '' || $kodebrg === '-') {
        return 'Kode Barang harus diisi';
      }

      $barang = DB::connection('SML')->select("select KODEBRG from DBBARANG where KODEBRG = :kodebrg and IsAktif = 1", ["kodebrg" => $kodebrg]);
      if (!$barang) {
        return 'Kode Barang ' . $kodebrg . ' tidak ditemukan / tidak aktif di master barang';
      }

      // Perbaikan: hanya item yang masih tampil di daftar ini (kode '-' dan sudah diverifikasi).
      // Kalau user lain sudah mengisi kodenya / membatalkan verifikasi sejak layar dimuat,
      // tidak ada baris yang berubah -> kembalikan 2 supaya layar memuat ulang data.
      $res = DB::connection('SML')->update("
      Update DbpenawaranSodet set Kodebrg= :kodebrg

     Where ID= :idrow and KODEBRG = '-' and isnull(isVerf,0) = 1

",
      [ "kodebrg" => $barang[0]->KODEBRG , "idrow" => $req->idrow  ]);

      if (!$res) {
        return 2;
      }

      return 1;
    }

    public function spBatalOtorisasi ( Request $req) {
      $akses = $this->aksesMenu();
      if (!$akses || !$akses->HASACCESS || !$akses->IsBatal) {
        return 'No access';
      }

      $res = DB::connection('SML')->update("
      Update DbPenawaranSodet set IsVerf= NULL ,TglVerf= NULL ,UserVerf= '' where NObukti= :nobukti and Urut= :urut
        and KODEBRG = '-' and isnull(isVerf,0) = 1
",
      [ "nobukti" => $req->nobukti , "urut" => $req->urut  ]);

      // Perbaikan: item yang sudah diisi kodenya / sudah dibatalkan user lain tidak ikut berubah.
      if (!$res) {
        return 2;
      }

      return 1;
    }




}
