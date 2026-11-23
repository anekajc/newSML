<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportSoSpbController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $kodemenu = '210604';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $bln = $periode->bulan;
    $thn = $periode->tahun;



    // Dinonaktifkan 2026-10-23: SP memanggil fungsi cursor dbo.dataplso & dbo.Datarspbspb per baris (±13 dtk).
    // $outstanding = DB::connection("SML")->select('exec Sp_ReportSoSpb ?,? ',[$bln,$thn]);;
    $outstanding = $this->dataSoSpb($bln, $thn);





    return view('wms.reportsospb' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  $bln = $req->input('bln');
  $thn = $req->input('thn');
  $values = [
		$bln,
        $thn
	];
    //return DB::connection("SML")->select("exec Sp_ReportSoSpb :bulan,:tahun ", ["bulan" => $bulan,"tahun" => $tahun] );
    // Dinonaktifkan 2026-10-23: lihat index().
    // return DB::connection("SML")->select('exec Sp_ReportSoSpb ?,? ',$values);
    return $this->dataSoSpb($bln, $thn);
  }

  // Ditambahkan 2026-10-23: isi sama dengan "exec Sp_ReportSoSpb @bulan,@tahun" (SP tidak diubah).
  // Bedanya hanya cara mengisi kolom nopl dan norspb: SP memanggil fungsi cursor dbo.dataplso dan
  // dbo.Datarspbspb untuk tiap baris. Di sini dirangkai sekaligus dengan FOR XML PATH, mengikuti aturan
  // fungsi itu: urutan cursor, pemisah koma, kosong -> '', maksimal 300 karakter.
  // Hasil dibandingkan dengan SP (data asli 23-10-2026, beberapa periode): isi identik. Urutan baris
  // kembar (satu item SO dengan beberapa SPB) di SP acak; di sini diurutkan nomor SPB.
  // Kalau Sp_ReportSoSpb atau fungsi-fungsi itu di database diubah, query di bawah perlu disamakan.
  private function dataSoSpb ($bln, $thn) {
    return DB::connection("SML")->select("select A.NoBukti noso, B.urut,B.KODEBRG kodebrg,c.NAMABRG namabrg, cast(A.TANGGAL as DATE) tglso,
      cast(b.QNT as Numeric(18,2)) qntso,m19.qntpckinglist,M19.QNTP qntp,D.NoBukti nospb
      ,cast(CASE WHEN   D.NOSAT=1 THEN D.QNT ELSE D.QNT2 END as NUmeric(18,2)) qntspb,
      isnull(left(stuff((select ',' + left(p.nobukti+'-'+cast(p.URUT as varchar(3) ), 300)
              from DBOUTBRGDET p where p.nobeli=b.NOBUKTI and p.urutbeli=b.urut
              order by p.NOBUKTI, p.URUT
              for xml path(''), type).value('.', 'varchar(max)'), 1, 1, ''), 300), '') nopl
      ,m19.QNTRejectPL qntplreject
      ,cast(CASE WHEN   D.NOSAT=1 THEN e.QNT ELSE e.QNT2 END as NUmeric(18,2)) qntrspb,
      isnull(left(stuff((select ',' + left(r.nobukti+'-'+cast(r.urut as varchar(3) ), 300)
              from dbrspbdet r where r.nospb=d.NoBukti and r.urutspb=d.Urut
              order by r.NOBUKTI, r.URUT
              for xml path(''), type).value('.', 'varchar(max)'), 1, 1, ''), 300), '') norspb
      from dbSO A
      left outer join dBSODet B on B.NoBukti=A.NoBukti
      left outer join dbBarang C on C.KodeBrg=B.KodeBrg
      LEFT OUTER JOIN dbSPBDet D ON B.NOBUKTI=D.NoSo AND B.URUT=D.UrutSo
      left outer join DBRSPBDet E on d.NoBukti=e.NoSPB and D.Urut=e.UrutSPB
      Left outer join (select A.NoBeli,A.UrutBeli,SUM(A.QNT) qntpckinglisT,SUM(B.QNTP) QNTP,sum(b.qntplreject) qntplreject,SUM(isnull(B.QNTRejectPL,0)) QNTRejectPL
          from DBOutBrgDET A
          LEFT OUTER JOIN (SELECT a.NoBeli,a.UrutBeli,SUM(isnull(A.QNTreject,0)) QNTRejectPL,SUM(A.QNT) QNTP,SUM((A.QNT) -isnull(A.QNTreject,0)) qntplreject
              FROM DBOutBrgDET2 A
              GROUP BY a.NoBeli,a.UrutBeli
              ) B ON A.NOBUKTI=B.NoBeli AND A.Urut=B.UrutBeli
          group by A.NoBeli,A.UrutBeli
        ) m19 on b.NOBUKTI=m19.NoBeli and b.urut=m19.UrutBeli
      WHERE MONTH(a.TANGGAL)=? and YEAR(a.TANGGAL)=?
      ORDER BY B.NOBUKTI,B.URUT, D.NoBukti, D.Urut, E.NoBukti, E.Urut", [(int) $bln, (int) $thn]);
  }




}
