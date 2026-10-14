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

class PerintahReturJualController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04105';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);


    [$tglawal, $tglakhir] = $this->rentangPeriode($periode);
    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, 0, 0);

    return view('marketing.perintahreturjual' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tglawalperiode" => $tglawal,
      "tglakhirperiode" => $tglakhir,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  // Awal & akhir bulan periode kerja aktif. Dulu Carbon::now()->month(bulan): tahunnya ikut
  // tahun berjalan (bukan tahun periode) dan pada tanggal 29-31 bulannya meluap ke bulan
  // berikutnya (mis. 31 Jan -> month(2) = 3 Maret).
  private function rentangPeriode ($periode) {
    $awal = \Carbon\Carbon::create((int) $periode->tahun, (int) $periode->bulan, 1);
    return [$awal->format('Y-m-d'), $awal->copy()->endOfMonth()->format('Y-m-d')];
  }

  // Satu query dipakai bareng oleh index() (initial load) dan loadAll() (periode/filter
  // berubah, atau refresh sehabis add/edit/delete) -- dulu ada 2 salinan query yang nyaris
  // identik (satu tanpa xstatus buat index(), satu dengan xstatus buat loadAll()), jadi
  // kolom baru/perubahan skema harus diedit di 2 tempat. filterstatus (status proses SPBR)
  // dan filteroto (status otorisasi) sekarang dua filter independen yang di-AND, bukan satu
  // dropdown gabungan yang saling eksklusif seperti sebelumnya -- user butuh bisa mis.
  // lihat "Belum Otorisasi" + "Selesai" sekaligus.
  //   filterstatus: 0 = Semua, 1 = Belum Diproses, 2 = Proses Sebagian, 3 = Selesai
  //   filteroto:    0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryOutstanding ($tglawal, $tglakhir, $filterstatus, $filteroto) {
    return DB::connection("SML")->select("
      select * from (
        select month(a.Tanggal) Bulan, YEAR(a.Tanggal) Tahun, a.Tanggal, a.Catatan, a.NOBUKTI,
          a.KodeCustSupp, d.NAMACUSTSUPP, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ, a.NOSO, a.IDUser,
          case when isnull(qntspbr,0) = 0 then 'Belum'
               when isnull(qntspbr,0) < qntprr then 'Sebagian'
               else 'Selesai' end xstatus
        from dbPRRJual a
        left outer join (select nobukti, sum(qnt) qntprr from dbPRRJualDet group by NoBukti) b on b.NoBukti = a.NoBukti
        left outer join (select NoPRRJUAL, sum(qnt) qntspbr from dbSPBRJualdet group by NoPRRJUAL) c on c.NoPRRJUAL = a.NoBukti
        left outer join DBCUSTSUPP D on a.KodeCustSupp = d.KODECUSTSUPP
        where a.TANGGAL between ? and ? and a.pmin = 0
      ) x
      where (
              (? = 0)
           or (? = 1 and x.xstatus = 'Belum')
           or (? = 2 and x.xstatus = 'Sebagian')
           or (? = 3 and x.xstatus = 'Selesai')
      )
      and (
              (? = 0)
           or (? = 1 and x.IsOtorisasi1 <> 1)
           or (? = 2 and x.IsOtorisasi1 = 1)
      )
    ", [
      $tglawal, $tglakhir,
      $filterstatus, $filterstatus, $filterstatus, $filterstatus,
      $filteroto, $filteroto, $filteroto,
    ]);
  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    [$defaultawal, $defaultakhir] = $this->rentangPeriode($periode);
    $tglawal = $this->tanggalFilter($req->tglawal) ?: $defaultawal;
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: $defaultakhir;
    $filterstatus = (int) $req->filterstatus;
    $filteroto = (int) $req->filteroto;

    $tempOutstanding = $this->queryOutstanding($tglawal, $tglakhir, $filterstatus, $filteroto);

    return ["tempOutstanding" => $tempOutstanding];
  }


  public function getDetail (Request $req ) {

        $tempOutstanding = DB::connection("SML")->select("select 	month(a.Tanggal) Bulan,YEAR(a.Tanggal) Tahun,a.Tanggal,a.Catatan,
    B.NOBUKTI,A.NOURUT, B.URUT, B.NoINV NoSC, B.UrutINV UrutSC, B.KODEBRG, C.NAMABRG,b.NAMABRG NAMAPRODUK,
            B.QNT,B.QNT1, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, b.ketdet KetDetail, a.KodeCustSupp, d.NAMACUSTSUPP,a.KodeCustSupp,d.ALAMAT1, a.IsOtorisasi1, a.OtoUser1, a.TglOto1, a.NoRPJ , a.NOSO, a.IDUser, case when b.nosat = 1 then b.SAT_1 else b.SAT_2 end SAT , b.SATX, b.KodeGdg, A.FlagTipe, A.TipePPN,
            b.NoSat, b.nobeli , b.urutbeli, v.qntsisa QntSisaView, v.isi IsiSisaView,  b.FlagKembali,  v.ISI1, v.ISI2
    from	dbPRRJualDet B
    left outer join dbBarang C on C.KodeBrg=B.KodeBrg
    left outer join dbPRRJual a on b.NoBukti=a.NoBukti

    left outer join DBCUSTSUPP D on a.KodeCustSupp=d.KODECUSTSUPP
    left outer join vwBrowsOutPLPR v on v.Nobukti = b.noinv and v.urut = b.urutinv
    where a.Nobukti = cast(:nobukti as varchar(100))" , ["nobukti" => $req->nobukti]);

    // IsProses = 1 bila PRJ sudah dipakai Retur Penjualan Gudang (dbSPBRJualDet), dipakai JS
    // untuk menolak Edit sebelum form dibuka.
    if ($tempOutstanding) {
      $isProses = $this->sudahDiprosesGudang($req->nobukti) ? 1 : 0;
      foreach ($tempOutstanding as $row) {
        $row->IsProses = $isProses;
      }
    }
    return $tempOutstanding;
  }

  // PRJ yang sudah dipakai Retur Penjualan Gudang tidak boleh diubah / dibatal otorisasi.
  private function sudahDiprosesGudang ($nobukti) {
    $cek = DB::connection('SML')->select("select top 1 1 ada from dbSPBRJualDet where NoPRRJUAL = cast(? as varchar(100))", [$nobukti]);
    return count($cek) > 0;
  }

  private function headerPRJ ($nobukti) {
    $cek = DB::connection('SML')->select("select NoBukti, isnull(IsOtorisasi1,0) IsOtorisasi1 from dbPRRJual where NoBukti = cast(? as varchar(100))", [$nobukti]);
    return $cek ? $cek[0] : null;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select PRJ from DBNOMOR');


      $values = [
          $inisial[0]->PRJ,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
 
    } else {
      $values = [
          'PRJN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    }


  }



  public function listCustomer (Request $req) {

    // Hanya kolom yang dipakai modal (dulu select * - semua kolom vwBrowsCustSupp ikut
    // terkirim, ~4x lebih lambat). distinct: view menggandakan customer yang punya lebih dari
    // satu perkiraan, isi kolom yang ditampilkan sama persis.
    $listData = DB::connection('SML')->select("select distinct KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1, NamaKota, PPN
                from vwBrowsCustSupp where IsCustomer=1");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    // Isi query = vwBrowsOutInvoicePL (-> VwOutInvoicePL) apa adanya, hanya filter customer
    // dipasang di dalam kedua cabang union. View aslinya meng-agregasi invoice SEMUA customer
    // baru kemudian difilter, sehingga customer besar butuh > 1 menit. Hasil sudah dicek sama
    // persis dengan view (except dua arah = 0 baris). Kolom customer (vwCUSTSUPP) tidak ikut
    // di-join karena tidak dipakai di sini dan tidak mengubah hasil distinct.
    $listData = DB::connection('SML')->select("select distinct V.NOBUKTI, V.TANGGAL, V.NoSO, V.KODEGDG, V.NAMAGDG, V.ppn, V.flagtipe
      from (
        select X.NOBUKTI, X.TANGGAL, X.NoSO, M2.KodeGdg KODEGDG, M3.NAMA NAMAGDG, m4.FlagTipe flagtipe, m4.PPN ppn,
          SUM(X.Qnt)-case when MAX(X.NoSat)=1 then sum(X.Qnt1SPBRJual) else sum(X.Qnt2SPBRJual) end QntSisa,
          X.NilaiOL, X.MAXOL
        from (
          select A.NOBUKTI, A.TANGGAL, A.KodeCustSupp KODECUSTSUPP, B.URUT, B.KODEBRG,
            B.Qnt, B.QNT2, B.NOSAT, 0 Qnt1SPBRJual, 0 Qnt2SPBRJual,
            (case when A.IsOtorisasi1=1 then 1 else 0 end + case when A.IsOtorisasi2=1 then 1 else 0 end +
             case when A.IsOtorisasi3=1 then 1 else 0 end + case when A.IsOtorisasi4=1 then 1 else 0 end +
             case when A.IsOtorisasi5=1 then 1 else 0 end) NilaiOL,
            A.MAXOL, B.NoSO, B.nobatch
          from DBInvoicePL A, dbInvoicePLDet B
          where B.NOBUKTI=A.NOBUKTI and A.KodeCustSupp = cast(? as varchar(50))
          union all
          select A.NOBUKTI, A.TANGGAL, A.KodeCustSupp, B.UrutInv, B.KODEBRG,
            0, 0, 0, B.Qnt, B.QNT2,
            (case when A.IsOtorisasi1=1 then 1 else 0 end + case when A.IsOtorisasi2=1 then 1 else 0 end +
             case when A.IsOtorisasi3=1 then 1 else 0 end + case when A.IsOtorisasi4=1 then 1 else 0 end +
             case when A.IsOtorisasi5=1 then 1 else 0 end),
            A.MAXOL, '', ''
          from DBInvoicePL A, dbSPBRJualDet B
          where B.Noinv=A.NOBUKTI and A.KodeCustSupp = cast(? as varchar(50))
        ) X
        left outer join DBBARANG Br on Br.KODEBRG=X.KodeBrg
        left outer join dbInvoicePLDet M1 on X.NoBukti=M1.NoBukti and M1.Urut=X.Urut
        left outer join dbSPBDet M2 on M1.NoSPB=M2.NoBukti and M1.UrutSPB=M2.Urut
        left outer join DBGUDANG M3 on M2.KodeGdg=M3.KODEGDG
        left outer join dbinvoicepl m4 on m1.NoBukti=m4.NoBukti
        group by X.NOBUKTI, X.TANGGAL, X.KODECUSTSUPP, X.Urut, X.KODEBRG, Br.NAMABRG, Br.SAT1, Br.SAT2, Br.NFix, Br.ISI1, Br.ISI2,
          X.NilaiOL, X.MAXOL, X.NoSO, X.Nobatch, M2.KodeGdg, M3.NAMA, m4.FlagTipe, m4.PPN
      ) V
      where V.QntSisa > 0 and V.NilaiOL = V.MAXOL", [$req->kodecustsupp, $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select A.Urut Urut, A.KodeBrg, A.NamaBrg, A.NoSat, A.Satuan,
                case when A.NOSAT=1 then 1 else A.ISI2 end Isi, A.Isi1, A.Isi2,
                A.QntSisa, A.Qnt1Sisa, A.Qnt2Sisa, A.NFix,a.SATX , b.NAMABRG NamaBrgx, a.SAT1 , a.SAT2

                from vwBrowsOutPLPR A
                left join dbbarang b on a.KODEBRG = b.KODEBRG
                where A.NoBukti= cast(:noinvoice as varchar(100))

                order by A.Urut" , ["noinvoice" => $req->noinvoice]);
    return $listData;
  }

  public function listNoBeli (Request $req) {

    $listData = DB::connection('SML')->select(" select A.NOBUKTI,A.urut
                       from DBBELIDET A
                       Left Outer join dbPRRJualdet b on a.NOBUKTI=b.nobeli and a.URUT=b.urutbeli
                      left outer join DBPO C on a.NoPO=c.NOBUKTI
                       where A.kodebrg= cast(:kodebrg as varchar(50)) and B.NoBukti is null
                      and C.Noso= cast(:noso as varchar(100))
                       group by A.NOBUKTI,A.Urut" , ["kodebrg" => $req->kodebrg , "noso" => $req->noso]);
    return $listData;
  }

  // Jawaban penolakan berbentuk {status: 0, pesan}; JS menampilkan pesannya lalu memuat
  // ulang data. Jawaban sukses tetap seperti semula (jumlah baris / 1).
  public function spOtorisasi (Request $req) {
    $header = $this->headerPRJ($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data PRJ " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diotorisasi"];
    }

    $tanggal = date('Y-m-d H:i:s');
    // isnull(isOtorisasi1,0) <> 1: penjaga terakhir kalau dua user menekan Otorisasi bersamaan.
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1,0) <> 1", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diotorisasi"];
    }
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');

    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    if (trim((string) $req->pket) === '') {
      return ["status" => 0, "pesan" => "Keterangan batal otorisasi harus diisi"];
    }
    $header = $this->headerPRJ($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "Data PRJ " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 !== 1) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " belum diotorisasi"];
    }
    if ($this->sudahDiprosesGudang($req->nobukti)) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " sudah diproses Retur Penjualan Gudang, tidak bisa batal otorisasi"];
    }

    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbprrjual set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1", [ "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "PRJ " . $req->nobukti . " belum diotorisasi"];
    }
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PRJ',$req->nobukti,$req->pket,0,'DBPRRJUAL');
    return $res;
  }

  public function spAdd (Request $req) {
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;
    $xurut=0;

    if (!in_array($choice, ['I', 'U', 'D'], true) || trim((string) $nobukti) === '') {
      return ["status" => 0, "pesan" => "Data tidak lengkap, silahkan ulangi"];
    }

    $tanggal = $req->tanggal;
    if ($choice == 'I' && $jmlrecord == 0) {
      // PRJ baru: tanggal selalu tanggal server (input tanggal di form disabled & diisi dari
      // gettanggalserver), dan harus masuk periode kerja aktif.
      $tglServer = DB::connection('SML')->select("select convert(varchar(10), getdate(), 23) tanggal")[0]->tanggal;
      $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      if ((int) substr($tglServer, 0, 4) !== (int) $periode->tahun || (int) substr($tglServer, 5, 2) !== (int) $periode->bulan) {
        return ["status" => 0, "pesan" => "Tanggal server (" . date('d/m/Y', strtotime($tglServer)) . ") tidak sesuai periode kerja aktif"];
      }
      $tanggal = $tglServer;
    } else {
      // Tambah/ubah/hapus item PRJ yang sudah ada: tolak bila PRJ sudah hilang, sudah
      // diotorisasi, atau sudah diproses gudang (mis. diotorisasi user lain saat form ini terbuka).
      $header = $this->headerPRJ($nobukti);
      if (!$header) {
        return ["status" => 0, "pesan" => "Data PRJ " . $nobukti . " tidak ditemukan, mungkin sudah dihapus"];
      }
      if ((int) $header->IsOtorisasi1 === 1) {
        return ["status" => 0, "pesan" => "PRJ " . $nobukti . " sudah diotorisasi, item tidak bisa diubah"];
      }
      if ($this->sudahDiprosesGudang($nobukti)) {
        return ["status" => 0, "pesan" => "PRJ " . $nobukti . " sudah diproses Retur Penjualan Gudang, item tidak bisa diubah"];
      }
    }

    if ($choice != 'D' && !((float) $req->qnt > 0)) {
      return ["status" => 0, "pesan" => "Qty harus lebih dari 0"];
    }


//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBPRRJUALDET where Nobukti = cast(:nobukti as varchar(100))', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBPRRJUALDET where Nobukti = cast(:nobukti as varchar(100))', ['nobukti' => $nobukti]);
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
    // return ["asd" => $xurut] ;



    if ($choice =='D'){
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }


    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbprrjual where Nobukti = cast(:nobukti as varchar(100))',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

    $values = [
      $choice,
      $nobukti,
      $req->nourut,
      $tanggal,
      $req->noinvoice,
      $req->kodecustsupp,
      '', // nopolkend
      '',
      '',
      '', // noseal 10
      $req->catatan,
      $req->urut,
      $req->urutinvoice,
      $req->kodebrg,
      $req->qnt,
      $req->qnt1,
      $req->qnt2,
      $req->sat1,
      $req->nosat,
      $req->isi, // 20
      0, // netw
      0,
      \Auth::User()->username,
      $req->jmlrecord,
      $req->namabrg, // 25
      '', // sopir
      0,
      $req->kodegdg,
      $req->flagtipe,
      $req->ppn, // 30
      $req->noso,
      '', // nobatch
      $req->retursupp,
      $req->sat2,
      '', //satx
      0, // pmin 50
      $req->ketdet,
      $req->nobeli,
      $req->urutbeli

    ];

    DB::connection('SML')->statement('exec sp_TransPRRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    
    if ($choice !='D'){
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'PRj',$nobukti,'',$xurut,'DBPRRJUALDET');
    }
    return 1;

  }



  public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC sp_CetakPRRJual ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }

}
