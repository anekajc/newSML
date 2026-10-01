<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;
 
class MasterBarangController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select("select  b.NAMA nNAMAGROUP, c.NAMAHDGRP nNAMAHDGROUP , d.NamaSubGrp nNAMASUBGROUP, e.Keterangan nNAMASUBKATEGORI, f.NAMAMERK nNAMAMERK , a.*  from DBBARANG a
join DBGROUP b on a.KODEGRP = b.KODEGRP
join DBHDGROUP c on a.KODEGRP = c.KODEGRP and a.KodeHdGrp = c.KODEHDGRP
join dbsubgroup d on a.KODEGRP = d.KodeGrp and a.KodeHdGrp = d.KodeHDGrp and a.KODESUBGRP = d.KodeSubGrp
join DBSubGroupJnsTambah e on a.KODEGRP = e.KodeGrp and a.KodeHdGrp = e.HDGROUP and a.KODESUBGRP = e.KodeSubGrp and a.KODESUBKATEGORI = e.Urut
join DBMERK f on a.KodeMerk = f.KODEMERK where a.KODEGRP = 'BJ'");


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterbarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }


   public function spListSatuan () {
    $listData = DB::connection('SML')->select('SELECT A.kodesatuan,A.kodeSattax FROM dbsatuan A Left Outer join Dbsattax B on A.kodesattax=b.kodetax order by kodesatuan');
    return $listData;

  }


  public function spListSupplier () {
    $listSupplier = DB::connection('SML')->select("select * from DBJENISCUSTSUPP");

    return $listSupplier;
  }

  public function spDetailHarga (Request $req) {
    $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
    return $harga;
  }

  public function spDetailHargaDetail (Request $req) {
    $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier " , ['kodebarang' => $req->kodebarang , 'kodesupplier' => $req->kodesupplier]);
    return $harga;
  }



  public function spListSelect () {

    $listHeadGroup = DB::connection('SML')->select("select * from DBHDGROUP where KODEGRP = 'BJ'");
    $listSubGroup =  DB::connection('SML')->select("select * from dbSubGroup where KodeGrp = 'BJ'");
    $listSubKategori = DB::connection('SML')->select("select * from DBSubGroupJnsTambah where KodeGrp = 'BJ'");
    $listMerk = DB::connection('SML')->select("select * from DBMERK");

    return [
      'listHeadGroup' => $listHeadGroup ,
      'listSubGroup' => $listSubGroup ,
      'listSubKategori' => $listSubKategori ,
      'listMerk' => $listMerk
    ];
  }

  public function spCheckDBBarang (Request $req) {
    // return $req->kodebarang;
    $checkBarang = DB::connection('SML')->select("select * from dbbarang where KODEBRG like :kodebarang order by KODEBRG desc" , ['kodebarang' => $req->kodebarang]);
    return $checkBarang;
    // select * from DBSubGroupJnsTambah where KodeSubGrp like 'SU%'
    // $listSubKategori = DB::connection('SML')->select("select * from DBSubGroupJnsTambah where KodeSubGrp like :tes" , ['tes' => $req->kodebarang]);
    // return $listSubKategori;
  }




  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('select * from DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);
    return $detail;
  }

  public function loadAll(Request $request)
{
    $query = DB::connection('SML')
        ->table('DBBARANG as a')
        ->join('DBGROUP as b', 'a.KODEGRP', '=', 'b.KODEGRP')
        ->join('DBHDGROUP as c', function($join) {
            $join->on('a.KODEGRP', '=', 'c.KODEGRP')
                 ->on('a.KodeHdGrp', '=', 'c.KODEHDGRP');
        })
        ->join('dbsubgroup as d', function($join) {
            $join->on('a.KODEGRP', '=', 'd.KodeGrp')
                 ->on('a.KodeHdGrp', '=', 'd.KodeHDGrp')
                 ->on('a.KODESUBGRP', '=', 'd.KodeSubGrp');
        })
        ->join('DBSubGroupJnsTambah as e', function($join) {
            $join->on('a.KODEGRP', '=', 'e.KodeGrp')
                 ->on('a.KodeHdGrp', '=', 'e.HDGROUP')
                 ->on('a.KODESUBGRP', '=', 'e.KodeSubGrp')
                 ->on('a.KODESUBKATEGORI', '=', 'e.Urut');
        })
        ->join('DBMERK as f', 'a.KodeMerk', '=', 'f.KODEMERK')
        ->where('a.KODEGRP', 'BJ');

    // ? Apply search filter (same as master piutang)
    $search = $request->input('search.value');
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('a.KODEBRG', 'like', "%{$search}%")
              ->orWhere('a.NAMABRG', 'like', "%{$search}%")
              ->orWhere('b.NAMA', 'like', "%{$search}%")
              ->orWhere('c.NAMAHDGRP', 'like', "%{$search}%")
              ->orWhere('d.NamaSubGrp', 'like', "%{$search}%")
              ->orWhere('e.Keterangan', 'like', "%{$search}%")
              ->orWhere('f.NAMAMERK', 'like', "%{$search}%")
              ->orWhere('a.SKU', 'like', "%{$search}%")
              ->orWhere('a.PartNumber', 'like', "%{$search}%");
        });
    }

    // apply pagination
    $start = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 10);

    // DBSMLNEW's compatibility level doesn't support SQL Server's OFFSET/FETCH
    // syntax (Laravel's default ->offset()->limit() for sqlsrv), so page via
    // ROW_NUMBER() in a subquery instead. COUNT(*) OVER() rides along in the
    // same pass so we don't run the whole joined+filtered query twice just
    // to get the total.
    // b (DBGROUP) and c (DBHDGROUP) stay joined — the search filter above
    // matches against b.NAMA/c.NAMAHDGRP directly — but nothing in the blade
    // reads their columns (nNAMAGROUP/nNAMAHDGROUP are commented out of the
    // displayed columns), and a.* was pulling every DBBARANG column when the
    // table only ever uses 7 of them.
    $dataQuery = (clone $query)->select(
        'd.NamaSubGrp as nNAMASUBGROUP',
        'e.Keterangan as nNAMASUBKATEGORI',
        'f.NAMAMERK as nNAMAMERK',
        'a.KODEBRG',
        'a.NAMABRG',
        'a.pAgen',
        'a.SKU',
        'a.SAT1',
        'a.SAT2',
        'a.PartNumber',
        DB::raw('ROW_NUMBER() OVER (ORDER BY a.KODEBRG) as RowNum'),
        DB::raw('COUNT(*) OVER () as TotalCount')
    );

    $paged = DB::connection('SML')->query()->fromSub($dataQuery, 'sub');

    // DataTables sends length = -1 for the "Semua" (All) option, meaning no limit
    if ($length >= 0) {
        $paged->whereBetween('RowNum', [$start + 1, $start + $length]);
    }

    $data = $paged->get();

    // TotalCount rides on every row, but the page can come back empty (e.g.
    // stale pagination state after the underlying data changed) — fall back
    // to a real count only in that edge case.
    $total = $data->isNotEmpty() ? (int) $data->first()->TotalCount : (clone $query)->count();

    return response()->json([
        "draw" => intval($request->input('draw')),
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    ]);
}

  public function spEditHarga (Request $req) {
    $edit = DB::connection('SML')->update('update dbHARGAJUAL set  HARGA1 = :harga , HARGA2 = :harga2 where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier' , [
      'harga' => $req->harga,
      'harga2' => $req->harga2,
      'kodebarang' => $req->kodebarang,
      'kodesupplier' => $req->kodesupplier,
    ]);

    return $edit;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBBARANG set NAMABRG = :namabarang , NamaBrg2 = :namabarang2 , pAgen = :isagen , SAT1 = :satuan , SAT2 = :satuan2  , SAT3 = :satuan3, 
    ISI1 = :isi , ISI2 = :isi2 , ISI3 = :isi3 , QntMin = :qtymin , QntMax = :qtymax , Tolerate = :toleransi , pBerat = :isberat , ISAKTIF = :isaktif , Berat = :beratvolume , 
    PartNumber = :partnumber , Mlokasi = :lokasi , SKU = :kodesku , pKontrak = :iskontrak , Hrg1_1 = :harga , Hrg2_1 = :harga2 , Hrg3_1 = :harga3  where KODEBRG = :kodebarang' , [
      'namabarang' => $req->namabarang ,
      'namabarang2' => $req->namabarang2 ,
      'isagen' => $req->isagen ,
      'satuan' => $req->satuan ,
      'satuan2' => $req->satuan2 ,
      'satuan3' => $req->satuan3 ,
      'isi' => $req->isi ,
      'isi2' => $req->isi2 ,
      'isi3' => $req->isi3 ,
      'qtymin' => $req->qtymin ,
      'qtymax' => $req->qtymax ,
      'toleransi' => $req->toleransi ,
      'isberat' => $req->isberat ,
      'isaktif' => $req->isaktif ,
      'beratvolume' => $req->beratvolume ,
      'partnumber' => $req->partnumber ,
      'lokasi' => $req->lokasi ,
      'kodesku' => $req->kodesku ,
      'iskontrak' => $req->iskontrak,
      'harga' => $req->harga,
      'harga2' => $req->harga2,
      'harga3' => $req->harga3,
      'kodebarang' => $req->kodebarang
    ]);

    return $edit;
  }
  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('select * from DBPPLDET where kodebrg = :kodebarang', ['kodebarang' => $req->kodebarang] );

    if ($check) {
      return 'Barang digunakkan di permintaan pembelian';
    }

    // Dulu hanya permintaan pembelian yang dicek - barang yang sudah dipakai transaksi lain
    // tetap bisa terhapus.
    $pemakai = [
      ['DBPODET',        'Purchase Order'],
      ['DBBELIDET',      'Pembelian'],
      ['DBSODET',        'Sales Order'],
      ['DBJUALDET',      'Penjualan'],
      ['DBInvoiceDET',   'Invoice'],
      ['dbSPBDet',       'SPB'],
      ['DBTRANSFERDET',  'Transfer Barang'],
    ];
    foreach ($pemakai as $p) {
      $check = DB::connection('SML')->select("select top 1 1 as ada from {$p[0]} where KODEBRG = :kodebarang", ['kodebarang' => $req->kodebarang]);
      if ($check) {
        return 'Barang digunakan di ' . $p[1];
      }
    }

    // Baris stok hanya dihitung kalau ada kuantitasnya (baris kosong tidak menghalangi hapus).
    $check = DB::connection('SML')->select("select top 1 1 as ada from DBSTOCKBRG where KODEBRG = :kodebarang
      and (isnull(QNTAWAL,0) <> 0 or isnull(QNTIN,0) <> 0 or isnull(QNTOUT,0) <> 0 or isnull(SALDOQNT,0) <> 0)", ['kodebarang' => $req->kodebarang]);
    if ($check) {
      return 'Barang masih punya catatan stok';
    }
    // return 1;
    $delete1 = DB::connection('SML')->update('delete from dbHARGAJUAL where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang ]);
    $delete = DB::connection('SML')->update('delete from DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);
    return $delete;
  }

  public function spDeleteHarga (Request $req) {
    // delete from dbSubGroup where KodeSubGrp = 'SBGRP3'
    $delete = DB::connection('SML')->update('delete from dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier' , ['kodebarang' => $req->kodebarang, 'kodesupplier' => $req->kodesupplier ]);
    return $delete;
  }


  public function spAddHarga (Request $req) {
//     insert into dbHARGAJUAL (KODEBRG, KODEJENISCUSTSUPP, HARGA1, HARGA2)
// values ('20101030990001' , 'TES1' , 1000, 2000)

  $check = DB::connection('SML')->select('SELECT * FROM dbHARGAJUAL where KODEBRG = :kodebarang and KODEJENISCUSTSUPP = :kodesupplier'  , ['kodebarang' => $req->kodebarang , 'kodesupplier' => $req->kodesupplier]);
  if ($check) {
    return 'Harga sudah ada di database';
  }

  $add = DB::connection('SML')->update('insert into dbHARGAJUAL (KODEBRG, KODEJENISCUSTSUPP, HARGA1, HARGA2) values ( :kodebarang , :kodesupplier , :harga , :harga2)' , ['kodebarang' => $req->kodebarang ,'kodesupplier' => $req->kodesupplier , 'harga' => $req->harga , 'harga2' => $req->harga2 ] );


  return 1;



  }

  public function spAdd (Request $req) {
    // return [
    //   'kodegroup' => $req->kodegroup ,
    //   'kodeheadgroup' => $req->kodeheadgroup ,
    //   'kodesubgroup' => $req->kodesubgroup ,
    //   'kodesubkategori' => $req->kodesubkategori ,
    //   'kodemerk' => $req->kodemerk ,
    //   'kodebarang' => $req->kodebarang ,
    //   'namabarang' => $req->namabarang ,
    //   'namabarang2' => $req->namabarang2 ,
    //   'isagen' => (int)$req->isagen ,
    //   'satuan' => $req->satuan ,
    //   'satuan2' => $req->satuan2 ,
    //   'satuan3' => $req->satuan3 ,
    //   'isi' => (int)$req->isi ,
    //   'isi2' => (int)$req->isi2 ,
    //   'isi3' => (int)$req->isi3 ,
    //   'qtymin' => (int)$req->qtymin ,
    //   'qtymax' => (int)$req->qtymax ,
    //   'toleransi' => (int)$req->toleransi ,
    //   'isberat' => (int)$req->isberat ,
    //   'isaktif' => (int)$req->isaktif ,
    //   'beratvolume' => $req->beratvolume ,
    //   'partnumber' => $req->partnumber ,
    //   'lokasi' => $req->lokasi ,
    //   'kodesku' => $req->kodesku ,
    //   'proses' => (int)$req->proses ,
    //   'istakein' => (int)$req->istakein ];
    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);

    if ($check) {
      return 'Kode Barang sudah ada di database';
    }


      $listData = DB::connection('SML')->update('insert into DBBARANG (KODEGRP , KodeHdGrp ,  KODESUBGRP , KODESUBKATEGORI , KodeMerk, KODEBRG , NAMABRG , NamaBrg2 , pAgen , SAT1, SAT2 ,sat3, ISI1, ISI2,isi3, QntMin, QntMax , Tolerate, pBerat, ISAKTIF, Berat, PartNumber, Mlokasi, SKU , Proses, IsTakeIn , pKontrak, Hrg1_1 , Hrg2_1, Hrg3_1) 
      values ( :kodegroup , :kodeheadgroup ,  :kodesubgroup , :kodesubkategori , :kodemerk , :kodebarang , :namabarang , :namabarang2 , :isagen , :satuan , :satuan2, :satuan3 , :isi , :isi2, :isi3  , :qtymin , :qtymax , :toleransi , :isberat , :isaktif , :beratvolume , :partnumber , :lokasi , :kodesku , :proses , :istakein , :iskontrak , :harga , :harga2 , :harga3)' , [
        'kodegroup' => $req->kodegroup ,
        'kodeheadgroup' => $req->kodeheadgroup ,
        'kodesubgroup' => $req->kodesubgroup ,
        'kodesubkategori' => $req->kodesubkategori ,
        'kodemerk' => $req->kodemerk ,
        'kodebarang' => $req->kodebarang ,
        'namabarang' => $req->namabarang ,
        'namabarang2' => $req->namabarang2 ,
        'isagen' => $req->isagen ,
        'satuan' => $req->satuan ,
        'satuan2' => $req->satuan2 ,
        'satuan3' => $req->satuan3 ,
        'isi' => $req->isi ,
        'isi2' => $req->isi2 ,
        'isi3' => $req->isi3 ,
        'qtymin' => $req->qtymin ,
        'qtymax' => $req->qtymax ,
        'toleransi' => $req->toleransi ,
        'isberat' => $req->isberat ,
        'isaktif' => $req->isaktif ,
        'beratvolume' => $req->beratvolume ,
        'partnumber' => $req->partnumber ,
        'lokasi' => $req->lokasi ,
        'kodesku' => $req->kodesku ,
        'proses' => $req->proses ,
        'istakein' => $req->istakein,
        'iskontrak' => $req->iskontrak,
        'harga' => $req->harga,
        'harga2' => $req->harga2, 
        'harga3' => $req->harga3 
        ] );


      return 1;
       // : ,
      //  : , : , :kodebarang , :namabarang ,
       // :namabarang2 , :isagen ,
       // :satuan, :satuan2 , :satuan3, :isi , :isi2 , :isi3 , :qtymin , :qtymax ,
       // :toleransi , :isberat , isaktif , :beratvolume , :partnumber , :lokasi , :kodesku   ]);
    // insert into DBBARANG (KODEGRP , KODESUBGRP , KODESUBKATEGORI , KodeMerk)
    // values ()
  }



}
