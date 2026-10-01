<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;

class PemakaianBarangController extends Controller
{

    // Outstanding PPI (Permintaan Pemakaian Internal) yang belum sepenuhnya diserahkan,
    // untuk rentang tanggal tertentu. Dipakai bareng oleh index() dan loadAll() supaya
    // keduanya selalu memakai query yang sama persis (tidak ada lagi drift antar dua tempat).
    // TANGGAL membawa komponen waktu, jadi dipakai rentang setengah-terbuka [date1, date2+1hari)
    // bukan BETWEEN — sama seperti PermintaanPemakaianController::fetchList() — supaya baris
    // yang timestamp-nya di tanggal akhir tidak ikut terbuang.
    private function fetchOutstanding(string $date1, string $date2)
    {
        $date2plus1 = date('Y-m-d', strtotime($date2 . ' +1 day'));

        $temp = DB::connection("SML")->select("select YEAR(d.tanggal) Tahun, MONTH(d.tanggal) Bulan ,A.NOBUKTI,D.TANGGAL,A.KODEBRG,C.NAMABRG, D.Kodegdg ,  G.NAMA NamaGudangAsal,a.Sat Satuan , a.Nosat NOSAT, a.ISI ISI,
CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END QNT, Isnull(A.QntCLose,0) QntCLose
,B.QNT QNTPB,CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END- Isnull(A.QntCLose,0)-ISNULL(B.QNT,0) QntOS ,A.Urut
from DBPRPenyerahanBhnDET A
LEFT OUTER JOIN (SELECT NOPRPB,URUTPRPB
				,SUM (CASE WHEN NoSat=1 THEN Qnt WHEN NoSat=2 THEN QNT2 WHEN NoSat=3 THEN Qnt2 END) QNT
				FROM DBPenyerahanBhnDET GROUP BY NOPRPB,URUTPRPB) B ON A.Nobukti=B.NOPRPB AND A.URUT=B.URUTPRPB
LEFT OUTER JOIN DBBARANG C ON A.kodebrg=C.KODEBRG
LEFT OUTER JOIN DBPRPENYERAHANBHN D ON A.NOBUKTI=D.NOBUKTI
LEFT OUTER JOIN DBGUDANG G ON D.Kodegdg=G.KODEGDG

where  CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END - Isnull(A.QntCLose,0) -ISNULL(B.QNT,0) <>0 and d.IsOtorisasi1 = 1
and year(D.tanggal)>2022 and D.Tanggal >= :date1 and D.Tanggal < :date2plus1
    ", ["date1" => $date1, "date2plus1" => $date2plus1]);

        $grouped = collect($temp)->groupBy('NOBUKTI');
        $out = [];
        foreach ($grouped as $p) {
            array_push($out, $p);
        }
        return $out;
    }

    // Dokumen Pemakaian Barang (dbPenyerahanBhn) yang sudah dibuat pada rentang tanggal
    // tertentu, dikelompokkan per NOBUKTI. QntOS pada header dijumlah dari seluruh baris
    // detail (lihat catatan di bawah) supaya badge Status konsisten dengan
    // PermintaanPemakaianController::fetchList(). Rentang setengah-terbuka, lihat catatan
    // di fetchOutstanding().
    private function fetchPenerimaan(string $date1, string $date2)
    {
        $date2plus1 = date('Y-m-d', strtotime($date2 . ' +1 day'));

        $temp = DB::connection("SML")->select("

        Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL,A.NOBUKTI,  a.NOURUT	,
        	B.URUT, B.KODEBRG, H.NAMABRG, B.QNT, B.QNT2,B.NOSAT, C.SAT1, C.SAT2, B.ISI,C.ISI2, A.Kodegdg, G.Nama Namagdg,
        	A.IsOtorisasi1, A.OtoUser1, A.TglOto1,

                case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
                case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.QNT2 end Qntx , b.NOPRPB NooutBRg
                ,OS.QntOS

        From dbPenyerahanBhn A
        Left Outer join  dbPenyerahanBhnDet B on B.NoBukti=a.NoBukti
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join ( select
          b.NOPRPB,b.URUTPRPB,case when a.NoSat=1 then a.Qnt else a.qnt2 end -
          isnull(case when A.NoSat=1 then  b.Qnt else b.Qnt2 end,0 ) QntOS
          from DBPRPenyerahanBhnDET a
           left outer join (select NOPRPB,URUTPRPB,SUM(qnt) Qnt,SUM(qnt2) qnt2
                    from DBPenyerahanBhnDET
                  group by NOPRPB,URUTPRPB
                  ) b on a.Nobukti=b.NOPRPB and a.urut=b.URUTPRPB
                 ) OS ON B.NOPRPB=OS.NOPRPB AND B.URUTPRPB=OS.URUTPRPB

                 where a.Tanggal >= :date1 and a.Tanggal < :date2plus1
        order by A.NoBukti, B.Urut

", ["date1" => $date1, "date2plus1" => $date2plus1]);

        $grouped = collect($temp)->groupBy('NOBUKTI');
        $out = [];
        foreach ($grouped as $g) {
            $row = $g[0];

            // QntOS di atas berlevel detail (per baris B.URUT), sedangkan blade/JS hanya
            // memakai baris pertama tiap NOBUKTI ($g[0]) untuk badge Status. Jumlahkan dulu
            // seluruh baris supaya 0 = seluruh item sudah terkirim, >0 = masih ada sisa —
            // dicari case-insensitive karena casing kolom dari SQL Server tidak selalu sama.
            // $row adalah stdClass biasa (DB::select mentah, bukan Eloquent), jadi pakai
            // get_object_vars() — getAttributes() cuma ada di Eloquent Model.
            $key = null;
            foreach (array_keys(get_object_vars($row)) as $k) {
                if (strcasecmp($k, 'QntOS') === 0) {
                    $key = $k;
                    break;
                }
            }
            if ($key !== null) {
                $total = 0;
                foreach ($g as $d) {
                    $total += (float) $d->{$key};
                }
                $row->{$key} = $total;
            }

            array_push($out, $g);
        }
        return $out;
    }

    public function index(Request $req)
    {

        $kodemenu = '06012';
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu, $req->path());
        if (!$akses || !$akses->HASACCESS) {
            return redirect('/home');
        }

        $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);

        $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        $date2 = date('Y-m-t', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));

        return view('gudang.pemakaianbarang', [
            "periode" => $periode,
            "menul0" => $menul0,
            "date1" => $date1,
            "date2" => $date2,
            "outstandingArray" => $this->fetchOutstanding($date1, $date2),
            "penerimaanArray" => $this->fetchPenerimaan($date1, $date2),
            "akses" => $akses
        ]);
    }

    public function getDetailOutstanding(Request $req)
    {



        $tempOutstanding = DB::connection("SML")->select("select YEAR(d.tanggal) Tahun, MONTH(d.tanggal) Bulan ,A.NOBUKTI,D.TANGGAL,A.KODEBRG,C.NAMABRG, D.Kodegdg ,  G.NAMA NamaGudangAsal,a.Sat Satuan , a.Nosat NOSAT, a.ISI ISI,
CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END QNT, Isnull(A.QntCLose,0) QntCLose
,B.QNT QNTPB,CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END- Isnull(A.QntCLose,0)-ISNULL(B.QNT,0) QntOS ,A.Urut
from DBPRPenyerahanBhnDET A
LEFT OUTER JOIN (SELECT NOPRPB,URUTPRPB
				,SUM (CASE WHEN NoSat=1 THEN Qnt WHEN NoSat=2 THEN QNT2 WHEN NoSat=3 THEN Qnt2 END) QNT
				FROM DBPenyerahanBhnDET GROUP BY NOPRPB,URUTPRPB) B ON A.Nobukti=B.NOPRPB AND A.URUT=B.URUTPRPB
LEFT OUTER JOIN DBBARANG C ON A.kodebrg=C.KODEBRG
LEFT OUTER JOIN DBPRPENYERAHANBHN D ON A.NOBUKTI=D.NOBUKTI
LEFT OUTER JOIN DBGUDANG G ON D.Kodegdg=G.KODEGDG

where  CASE WHEN A.NOSAT=1 THEN A.Qnt WHEN A.Nosat=2 THEN A.Qnt2 WHEN A.NoSat=3 THEN A.Qnt2 END - Isnull(A.QntCLose,0) -ISNULL(B.QNT,0) <>0
and year(D.tanggal)>2022 and A.NOBUKTI = :nobukti
", ["nobukti" => $req->input('NOBUKTI')]);

        $collection1 = collect($tempOutstanding);
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
            array_push($tempOutstanding1, $p);
        }

        return $tempOutstanding1;
    }

    public function getDetailAdd(Request $req)
    {
        $date = date('Y-m-d');
        $month = date('m');
        $year = date('Y');
        $username = \Auth::user()->username;

        DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser', ['idUser' => $username]);

        $values = [$username, $req->noout, $year, (int)$month, 'PBG', $date];
        DB::connection('SML')->statement('exec sp_TempOutPRPB ?,?,?,?,?,?', $values);

        return DB::connection('SML')->select("select 2000 Tahun , 1 Bulan , a.NOBUKTI , a.Tanggal , a.KODEBRG , a.NAMABRG , a.Kodegdg , b.NAMA NamaGudangAsal , a.Satuan , a.NOSAT , A.ISI , a.QntSisa AS QNT, A.QntSisa QntOS , A.Urut  from tempoutstanding a
left outer join dbgudang b on a.Kodegdg = b.KODEGDG
where iduser = :username and TRANS = 'PBG'

", ['username' => $username]);
    }

    public function getDetailCetak(Request $req)
    {
        $noBukti = $req->input('NOBUKTI');

        $cetak = DB::connection("SML")->select(
            "EXEC dbo.CetakPemakaianBahan ?",
            [$noBukti]
        );

        $tempCetak1 = [];
        foreach ($cetak as $p) {
            array_push($tempCetak1, $p);
        }

        return $tempCetak1;
    }

    public function getDetailPenerimaan(Request $req)
    {
        $tempPenerimaan = DB::connection("SML")->select("

    Select MONTH(A.Tanggal) Bulan, YEAR(A.Tanggal) Tahun, A.TANGGAL,A.NOBUKTI,  a.NOURUT	,
          B.URUT, B.KODEBRG, H.NAMABRG,OS.QNTOS, B.QNT, B.QNT2,B.NOSAT, C.SAT1, C.SAT2, B.ISI,C.ISI2, A.Kodegdg, G.Nama Namagdg,





                case when b.NOSAT=1 then c.SAT1 when b.NOSAT=2 then C.SAT2 end Satuan,
                case when b.NOSAT=1 then B.QNT when b.NOSAT=2 then b.QNT2 end Qntx , b.NOPRPB NooutBRg
                ,os.QntOS,b.NOPRPB,b.URUTPRPB

        From dbPenyerahanBhn A
        Left Outer join  dbPenyerahanBhnDet B on B.NoBukti=a.NoBukti
        left outer join dbBarang C on C.KodeBrg=B.KodeBrg
        Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
        left outer join DBGUDANG G on A.Kodegdg = G.KODEGDG
        left outer join ( select
          b.NOPRPB,b.URUTPRPB,case when a.NoSat=1 then a.Qnt else a.qnt2 end -
          isnull(case when A.NoSat=1 then  b.Qnt else b.Qnt2 end,0 ) QntOS
          from DBPRPenyerahanBhnDET a
           left outer join (select NOPRPB,URUTPRPB,SUM(qnt) Qnt,SUM(qnt2) qnt2
                    from DBPenyerahanBhnDET
                  group by NOPRPB,URUTPRPB
                  ) b on a.Nobukti=b.NOPRPB and a.urut=b.URUTPRPB
                 ) OS ON B.NOPRPB=OS.NOPRPB AND B.URUTPRPB=OS.URUTPRPB

                 where a.nobukti = :nobukti
        order by A.NoBukti, B.Urut

", ["nobukti" => $req->NOBUKTI]);

        return $tempPenerimaan;
    }

    public function loadAll(Request $req)
    {
        $date1 = $req->input('date1');
        $date2 = $req->input('date2');
        if (!$date1 || !$date2) {
            $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
            $date1 = date('Y-m-01', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
            $date2 = date('Y-m-t', mktime(0, 0, 0, $periode->bulan, 1, $periode->tahun));
        }

        return [
            "outstandingArray" => $this->fetchOutstanding($date1, $date2),
            "penerimaanArray" => $this->fetchPenerimaan($date1, $date2)
        ];
    }

    public function getKoreksiAddList(Request $req)
    {
        return [];
    }

    public function addPenyerahanGudang(Request $req)
    {

        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        $date = $req->input('inputDate');
        $username = \Auth::user()->username;
        $data = $req->input('tempData');
        $nourut = $req->input('nourut');
        $noout = $req->input('noout');
        $nopbg = $req->input('nopbg');



        $check = DB::connection('SML')->select('select * from DBPRPenyerahanBhndet where NOBUKTI = :nobukti', ["nobukti" => $nopbg]);
        if ($check) {
            return 2;
        }

        foreach ($data as $d) {
            DB::connection("SML")->update(
                'update  TempOutstanding set QNTTerima = :qnt , isterima = 1 where IDUser= :username and NoBukti= :nobukti and URUT = :urut',
                ["qnt" => $d['inputQntTerima'], "username" => $username, "nobukti" => $noout, "urut" => $d['Urut']]
            );
        }

        $tempValues = [$nopbg, $nourut, $noout, $username, 1, $date];
        DB::connection('SML')->statement('exec SP_InsertPRPB ?,?,?,?,?,?', $tempValues);

        return 1;
    }

    public function spKoreksi(Request $req)
    {

        $xurut = 0;
        $purut = DB::connection('SML')->select('select * from dbPenyerahanBhndet where Nobukti = :nobukti', ['nobukti' => $req->input('nopbg')]);
        if ($purut) {

            if ($req->input('choice') == 'I') {

                $purut = DB::connection('SML')->select('select max(urut)+1 xurut from dbPenyerahanBhndet where Nobukti = :nobukti', ['nobukti' => $req->input('nopbg')]);
                $xurut = $purut[0]->xurut;
            } else {
                $xurut = $req->urut;
            }
        } else {
            $xurut = 1;
        }

        if ($req->input('choice') == 'D') {
            $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($req->input('choice'), 'PBG', $req->input('nopbg'), '', $xurut, 'dbPenyerahanBhndet');
        }




        $values = [
            $req->input('choice'),
            $req->input('nopbg'),
            $req->input('nourut'),
            $req->input('inputDate'),
            $req->input('kodegdg'),
            $req->input('urut'),
            $req->input('kodebrg'),
            $req->input('qntTerima'),
            $req->input('nosat'),
            $req->input('sat'),
            $req->input('isi'),
            $req->input('nobppb') ? $req->input('nobppb') : '',
            $req->input('qntTerima2'),
            $req->input('urutspk') ? $req->input('urutspk') : 0,
            $req->input('nosatspk') ? $req->input('nosatspk') : 0,
            $req->input('issample') ? $req->input('issample')  : 0,
            $req->input('isbarang') ? $req->input('isbarang') : 0,
            $req->input('keterangan') ? $req->input('keterangan') : '',
            $req->input('kddep') ? $req->input('kddep') : '',
            $req->input('nopr') ? $req->input('nopr') : '',
            $req->input('urutpr') ? $req->input('urutpr') : 0,
        ];

        DB::connection('SML')->statement('exec sp_PenyerahanBhnSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);


        if ($req->input('choice') != 'D') {
            $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($req->input('choice'), 'PBG', $req->input('nopbg'), '', $xurut, 'dbPenyerahanBhndet');
        }

        return 1;
    }
}
