<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Models\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;

class MasterBarangJasaController extends Controller
{


  // select b.NAMA nNAMAGROUP, c.NAMAGRP nNAMAHDGROUP , d.NamaSubGrp nNAMASUBGROUP, d.NamaSubGrp nNAMASUBGROUP , a.* from dbbarang a
  // select b.NAMA nNAMAGROUP c.NAMAGRP , nNAMAHDGROUP, DNamaSubGrp nNAMASubgroub , d.NamaSubGrp nNAMASUBGROUP , a.* from dbbarang a




  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select("select b.NAMA nNAMAGROUP, c.NAMAHDGRP nNAMAHDGROUP , d.NamaSubGrp nNAMASUBGROUP, a.* from dbbarang a
join DBGROUP b on a.KODEGRP = b.KODEGRP
join DBHDGROUP c on a.KODEGRP = c.KODEGRP and a.KodeHdGrp = c.KODEHDGRP
join dbsubgroup d on a.KODEGRP = d.KodeGrp and a.KodeHdGrp = d.KodeHDGrp and a.KODESUBGRP = d.KodeSubGrp
where a.KODEGRP = 'JS'");


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterbarangjasa' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function spListSelect () {

    $listHeadGroup = DB::connection('SML')->select("select * from DBHDGROUP where KODEGRP = 'JS'");
    $listSubGroup =  DB::connection('SML')->select("select * from dbSubGroup where KodeGrp = 'JS'");
    // $listSubKategori = DB::connection('SML')->select("select * from DBSubGroupJnsTambah where KodeGrp = 'JS'");
    return [
      'listHeadGroup' => $listHeadGroup ,
      'listSubGroup' => $listSubGroup
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
    $detail = DB::connection('SML')->select("select b.NAMA nNAMAGROUP, c.NAMAHDGRP nNAMAHDGROUP , d.NamaSubGrp nNAMASUBGROUP, a.* from dbbarang a
join DBGROUP b on a.KODEGRP = b.KODEGRP
join DBHDGROUP c on a.KODEGRP = c.KODEGRP and a.KodeHdGrp = c.KODEHDGRP
join dbsubgroup d on a.KODEGRP = d.KodeGrp and a.KodeHdGrp = d.KodeHDGrp and a.KODESUBGRP = d.KodeSubGrp
where a.KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
    return $detail;
  }

  public function spAdd (Request $req) {

    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);

    if ($check) {
      return 'Kode Barang sudah ada di database';
    }


      $listData = DB::connection('SML')->update('insert into DBBARANG (KODEGRP, KodeHdGrp, KODESUBGRP, KODEBRG , NAMABRG , NamaBrg2 , SAT1 , ISI1 , ISAKTIF , Keterangan , Proses , IsTakeIn ) values ( :kodegroup , :kodeheadgroup , :kodesubgroup , :kodebarang , :namabarang , :namabarang2 , :satuan , :isi , :isaktif , :keterangan , 0 , 0  )' , [
        'kodegroup' => $req->kodegroup ,
        'kodeheadgroup' => $req->kodeheadgroup ,
        'kodesubgroup' => $req->kodesubgroup ,
        'kodebarang' => $req->kodebarang ,
        'namabarang' => $req->namabarang ,
        'namabarang2' => $req->namabarang2 ,
        'satuan' => $req->satuan ,
        'isi' => $req->isi ,
        'isaktif' => $req->isaktif ,
        'keterangan' => $req->keterangan,] );


      return 1;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBBARANG set NAMABRG = :namabarang , NamaBrg2 = :namabarang2 , SAT1 = :satuan , ISI1 = :isi , ISAKTIF = :isaktif , Keterangan = :keterangan where KODEBRG = :kodebarang' , [
      'namabarang' => $req->namabarang ,
      'namabarang2' => $req->namabarang2 ,
      'satuan' => $req->satuan,
      'isi' => $req->isi ,
      'isaktif' => $req->isaktif ,
      'keterangan' => $req->keterangan ,
      'kodebarang' => $req->kodebarang
    ]);

    return $edit;
  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('select * from DBPPLDET where kodebrg = :kodebarang', ['kodebarang' => $req->kodebarang] );

    if ($check) {
      return 'Barang digunakkan di permintaan pembelian';
    }

    $delete = DB::connection('SML')->update('delete from DBBARANG where KODEBRG = :kodebarang' , ['kodebarang' => $req->kodebarang]);
    return $delete;
  }

public function loadAll(Request $request)
{
    $draw   = $request->get('draw');
    $start  = $request->get("start");
    $length = $request->get("length");
    $search = $request->get('search')['value'];

    $query = DB::connection('SML')->table('DBBarang as a')
        ->join('DBGroup as b', 'a.KODEGRP', '=', 'b.KODEGRP')
        ->join('DBHDGroup as c', function ($join) {
            $join->on('a.KODEGRP', '=', 'c.KODEGRP')
                 ->on('a.KodeHdGrp', '=', 'c.KODEHDGRP');
        })
        ->join('DBSubGroup as d', function ($join) {
            $join->on('a.KODEGRP', '=', 'd.KodeGrp')
                 ->on('a.KodeHdGrp', '=', 'd.KodeHDGrp')
                 ->on('a.KODESUBGRP', '=', 'd.KodeSubGrp');
        })
        ->where('a.KODEGRP', '=', 'JS') // khusus Jasa
        ->select(
            'a.*',
            'b.NAMA as nNAMAGROUP',
            'c.NAMAHDGRP as nNAMAHDGROUP',
            'd.NamaSubGrp as nNAMASUBGROUP'
        );

    // Filtering
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('a.KODEBRG', 'like', "%{$search}%")
              ->orWhere('a.NAMABRG', 'like', "%{$search}%")
              ->orWhere('b.NAMA', 'like', "%{$search}%")
              ->orWhere('c.NAMAHDGRP', 'like', "%{$search}%")
              ->orWhere('d.NamaSubGrp', 'like', "%{$search}%");
        });
    }

    $start = (int) $start;
    $length = (int) $length;

    // DBSMLNEW's compatibility level doesn't support SQL Server's OFFSET/FETCH
    // syntax (Laravel's default ->offset()->limit() for sqlsrv), so page via
    // ROW_NUMBER() in a subquery instead. COUNT(*) OVER() rides along in the
    // same pass so the total doesn't need its own separate query.
    $dataQuery = (clone $query)->addSelect([
        DB::raw('ROW_NUMBER() OVER (ORDER BY a.KODEBRG) as RowNum'),
        DB::raw('COUNT(*) OVER () as TotalCount')
    ]);

    $paged = DB::connection('SML')->query()->fromSub($dataQuery, 'sub');

    // DataTables sends length = -1 for the "Semua" (All) option, meaning no limit
    if ($length >= 0) {
        $paged->whereBetween('RowNum', [$start + 1, $start + $length]);
    }

    $listBarang = $paged->get();

    $totalRecords = $listBarang->isNotEmpty() ? (int) $listBarang->first()->TotalCount : (clone $query)->count();
    $recordsFiltered = $totalRecords;

    return response()->json([
        "draw"            => intval($draw),
        "recordsTotal"    => $totalRecords,
        "recordsFiltered" => $recordsFiltered,
        "data"            => $listBarang
    ]);
}




}
