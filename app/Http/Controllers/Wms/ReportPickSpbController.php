<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportPickSpbController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    // Dinonaktifkan 2026-10-23: kode menu 054 adalah Report SO-SPB. Menu ini 056 (new_menureport).
    // $kodemenu = '054';
    $kodemenu = '210606';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $bln = $periode->bulan;
    $thn = $periode->tahun;


    // Dinonaktifkan 2026-10-23: SP memanggil fungsi cursor dbo.DataSPBPL per baris (±48 dtk).
    // $outstanding = DB::connection("SML")->select('exec Sp_ReportPickSpb ?,? ',[$bln,$thn]);;
    $outstanding = $this->dataPickSpb();





    return view('wms.reportpickspb' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  $bln = $periode->bulan;
  $thn = $periode->tahun;
  $values = [
		$bln,
        $thn
	];
    //return DB::connection("SML")->select("exec Sp_ReportSoSpb :bulan,:tahun ", ["bulan" => $bulan,"tahun" => $tahun] );
    // Dinonaktifkan 2026-10-23: lihat index().
    // return DB::connection("SML")->select('exec Sp_ReportPickSpb ?,? ',$values);
    return $this->dataPickSpb();
  }


  // Ditambahkan 2026-10-23: isi sama dengan "exec Sp_ReportPickSpb" (SP tidak diubah; parameter
  // bulan/tahun memang tidak dipakai SP - filter periodenya dikomentari, yang tampil semua outstanding).
  // Bedanya hanya cara mengisi kolom nospb: SP memanggil fungsi cursor dbo.DataSPBPL untuk tiap baris
  // (±44 dtk untuk ±5 ribu baris). Di sini dirangkai sekaligus dengan FOR XML PATH, mengikuti aturan
  // fungsi itu: urutan cursor, pemisah koma, ada elemen NULL (picking belum SPB / belum ada picking)
  // -> NULL, maksimal 300 karakter. Hasil dibandingkan dengan SP (data asli 23-10-2026): identik.
  // Kalau Sp_ReportPickSpb atau dbo.DataSPBPL di database diubah, query di bawah perlu disamakan.
  private function dataPickSpb () {
    return DB::connection("SML")->select("select x.nopicking, x.urut, x.kodebrg, x.namabrg, x.tglpicking,
      x.qntpickinglist, x.qntpicking, x.qntrejectpicking, x.qntspb, x.qntrspb,
      case when not exists (select 1 from dboutbrgdet2 b where b.nobeli=x.nopicking and b.urutbeli=x.urut)
             or exists (select 1 from dboutbrgdet2 b
                  left outer join dbspbdet c on b.nobukti=c.nooutbrg and b.urut=c.urutoutbrg
                  where b.nobeli=x.nopicking and b.urutbeli=x.urut and c.NoBukti is null)
        then null
        else left(stuff((select ',' + left('SPB : ' + c.nobukti+'-'+cast(c.urut as varchar(2) ) + isnull('Rspb :'+D.nobukti , ''), 300)
                from dboutbrgdet2 b
                join dbspbdet c on b.nobukti=c.nooutbrg and b.urut=c.urutoutbrg
                left outer join dbrspbdet d on c.nobukti=d.nospb and c.urut=d.urutspb
                where b.nobeli=x.nopicking and b.urutbeli=x.urut
                order by b.NOBUKTI, b.URUT, c.NOBUKTI, c.URUT, d.NOBUKTI, d.URUT
                for xml path(''), type).value('.', 'varchar(max)'), 1, 1, ''), 300)
      end nospb
      from (
        select A.NoBukti nopicking, B.urut,B.KODEBRG kodebrg,c.NAMABRG namabrg, cast(A.TANGGAL as DATE) tglpicking,
        b.QNT qntpickinglist,m19.QNTPicking qntpicking,M19.QNTRejectPL qntrejectpicking,
        CASE WHEN   B.NOSAT=1 THEN m19.qnt1spb ELSE m19.qnt2spb END qntspb,
        m19.qntrspb, A.TANGGAL urut_tanggal
        from DBOutBrg A
        left outer join DBOutBrgDET B on B.NoBukti=A.NoBukti
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        Left outer join (SELECT a.NoBeli,a.UrutBeli,SUM(A.QNT) QNTPicking,
            SUM(isnull(A.QNTreject,0)) QNTRejectPL,SUM((A.QNT) -isnull(A.QNTreject,0)) qntplreject,
            SUM(ISNULL(b.Qnt1,0)) qnt1spb,SUM(ISNULL(b.Qnt2,0)) qnt2spb,SUM(ISNULL(b.QntRSPB,0)) qntrspb
            FROM DBOutBrgDET2 A
            left outer join (select A.NoOutBrg,A.UrutOutBrg,SUM(A.Qnt) Qnt1,SUM(A.Qnt2) Qnt2,
                SUM(case when a.NOSAT=1 then b.qnt1rspb else b.Qnt2rspb End) QntRSPB
                from dbSPBDet A
                left outer join (select NoSPB,UrutSPB,SUM(QNT) qnt1rspb,SUM(QNT2) Qnt2rspb
                    from DBRSPBDet
                    group by NoSPB,UrutSPB
                    ) B on a.NoBukti=b.NoSPB and a.Urut=b.UrutSPB
                group by A.NoOutBrg,A.UrutOutBrg
                ) B on A.NOBUKTI=B.NoOutBrg and a.urut=b.UrutOutBrg
            GROUP BY a.NoBeli,a.UrutBeli
          ) m19 on b.NOBUKTI=m19.NoBeli and b.urut=m19.UrutBeli
        WHERE b.QNT - ISNULL(m19.QNTRejectPL,0) - ISNULL(CASE WHEN   B.NOSAT=1 THEN m19.qnt1spb ELSE m19.qnt2spb END,0) >0
        and b.JenisTRansaksi='so'
      ) x
      ORDER BY x.urut_tanggal, x.nopicking, x.urut");
  }




}
