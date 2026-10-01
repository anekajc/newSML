<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWPerkiraan;

// use App\Http\Controllers\NewMenuController;

class MasterHeadGroupController extends Controller
{

  public function index(Request $req) {


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBHDGROUP');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterheadgroup' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function listGroup () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBGROUP');
    return $listData;
  }

  public function listPerkiraan () {
    $listData = VWPerkiraan::all();
    return $listData;
  }



  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBHDGROUP');
    return [
      "hdgroup" => $listData
    ];
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBHDGROUP where KODEHDGRP = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Head Group sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBHDGROUP (KODEHDGRP, NAMAHDGRP, KODEGRP) values (:kode, :nama , :kodegroup )' , ['kode' => $req->kode , 'nama' => $req->nama , 'kodegroup' => $req->kodegroup]);
    return 1;

  }

  public function spDelete (Request $req) {

    // select * from dbbarang where KodeHdGrp = :kodeheadgroup
    // select * from dbSubGroup where KodeHDGrp = :kodeheadgroup

    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KodeHdGrp = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'HeadGroup digunakkan di Master Barang';
    }

    $check = DB::connection('SML')->select('SELECT * FROM dbSubGroup where KodeHDGrp = :kode' , ['kode' => $req->kode]);
    if ($check) {
      return 'HeadGroup digunakkan di SubGroup';
    }

    $delete = DB::connection('SML')->update('delete from DBHDGROUP where KODEHDGRP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBHDGROUP set NAMAHDGRP = :nama , KODEGRP = :kodegroup where KODEHDGRP = :kode' , ['kode' => $req->kode , 'nama' => $req->nama , 'kodegroup' => $req->kodegroup ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBHDGROUP where KODEHDGRP = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spListSubGroup (Request $req) {
    $list = DB::connection('SML')->select('select * from dbSubGroup where KodeHDGrp = :kode order by KodeSubGrp' , ['kode' => $req->kode]);

    return $list;
  }

  public function spDetailSubGroup (Request $req) {
    $detail = $list = DB::connection('SML')->select('select * from dbSubGroup where KodeSubGrp = :kode and KodeHDGrp = :kodehdgroup' , ['kode' => $req->kode , 'kodehdgroup' => $req->kodehdgroup]);
    return $detail;
  }

  public function spAddSubGroup (Request $req) {

    $check = DB::connection('SML')->select('SELECT * FROM dbSubGroup where KodeSubGrp = :kodesubgroup and KodeHDGrp = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup , 'kodehdgroup' => $req->kodehdgroup]);

    if ($check) {
      return 'Kode Sub Group sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into dbSubGroup (KodeGrp , KodeSubGrp , NamaSubGrp , KodeHDGrp , PerkPers, PerkH) values (:kodegroup, :kodesubgroup , :namasubgroup , :kodehdgroup , :perkpers , :perkjual )' , ['kodegroup' => $req->kodegroup , 'kodesubgroup' => $req->kodesubgroup , 'namasubgroup' => $req->namasubgroup , 'kodehdgroup' => $req->kodehdgroup , 'perkpers' => $req->perkpers , 'perkjual' => $req->perkjual]);
    return 1;
  }
  
  public function loadPerkiraan () {
    $listData = DB::connection('SML')->select("select Perkiraan, Keterangan, Kelompok, Tipe, Valas, DK, Neraca, FlagCashFlow, Simbol, IsPPN, GroupPerkiraan, Lokasi, iskirim, IsAktif from dbperkiraan");
    return $listData;
  }

  public function spEditSubGroup (Request $req) {
    $edit = DB::connection('SML')->update('update dbSubGroup set NamaSubGrp = :namasubgroup , PerkPers = :perkpers , PerkH = :perkjual where KodeSubGrp = :kodesubgroup and KodeHDGrp = :kodehdgroup' , [ 'namasubgroup' => $req->namasubgroup  , 'perkpers' => $req->perkpers  , 'perkjual' => $req->perkjual  , 'kodesubgroup' => $req->kodesubgroup , 'kodehdgroup' => $req->kodehdgroup]);
    return $edit;
  }

  public function spDeleteSubGroup (Request $req) {
    // delete from dbSubGroup where KodeSubGrp = 'SBGRP3'


    // select * from DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup
    // select * from DBBARANG where KODESUBGRP = :kodesubgroup

    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODESUBGRP = :kodesubgroup and KodeHdGrp = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup, 'kodehdgroup' => $req->kodehdgroup ]);

    if ($check) {
      return 'SubGroup digunakkan di Master Barang';
    }

    $check = DB::connection('SML')->select('SELECT * FROM DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup and HDGROUP = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup, 'kodehdgroup' => $req->kodehdgroup ]);
    if ($check) {
      return 'SubGroup digunakkan di SubKategori';
    }


    $delete = DB::connection('SML')->update('delete from dbSubGroup where KodeSubGrp = :kodesubgroup and KodeHDGrp = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup, 'kodehdgroup' => $req->kodehdgroup ]);
    return $delete;
  }



  public function spAddSubKategori (Request $req) {

    $check = DB::connection('SML')->select('select * from DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup and Urut = :kodesubkategori and HDGROUP = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup , 'kodesubkategori' => $req->kodesubkategori , 'kodehdgroup' => $req->kodehdgroup]);

    if ($check) {
      return 'Kode Sub Kategori sudah ada di database';
    }

    $listData = DB::connection('SML')->update('insert into DBSubGroupJnsTambah (KodeGrp , HDGROUP, KodeSubGrp, Urut, Keterangan) values (:kodegroup, :kodehdgroup , :kodesubgroup , :kodesubkategori , :namasubkategori )' , ['kodegroup' => $req->kodegroup, 'kodehdgroup' => $req->kodehdgroup , 'kodesubgroup' => $req->kodesubgroup , 'kodesubkategori' => $req->kodesubkategori , 'namasubkategori' => $req->namasubkategori ]);
    return 1;
  }

  public function spEditSubKategori (Request $req ) {
    $edit = DB::connection('SML')->update('update DBSubGroupJnsTambah set Keterangan = :namasubkategori where Urut = :kodesubkategori and KodeSubGrp = :kodesubgroup and HDGROUP = :kodehdgroup' , [ 'namasubkategori' => $req->namasubkategori  ,  'kodesubkategori' => $req->kodesubkategori,  'kodesubgroup' => $req->kodesubgroup , 'kodehdgroup' => $req->kodehdgroup]);
    return $edit;
  }


  public function spDeleteSubKategori (Request $req ) {

    // select * from DBBARANG where KODESUBKATEGORI = :kodesubkategori

    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODESUBGRP = :kodesubgroup and KODESUBKATEGORI = :kodesubkategori and KodeHdGrp = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup , 'kodesubkategori' => $req->kodesubkategori , 'kodehdgroup' => $req->kodehdgroup  ]);

    if ($check) {
      return 'SubKategori digunakkan di Master Barang';
    }

    $delete = DB::connection('SML')->update('delete from DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup and Urut =:kodesubkategori and HDGROUP = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup , 'kodesubkategori' => $req->kodesubkategori , 'kodehdgroup' => $req->kodehdgroup  ]);
    return $delete;
  }


  public function spDetailSubKategori (Request $req) {
    $detail = DB::connection('SML')->select('select * from DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup and Urut = :kodesubkategori and HDGROUP = :kodehdgroup' , ['kodesubgroup' => $req->kodesubgroup , 'kodesubkategori' => $req->kodesubkategori , 'kodehdgroup' => $req->kodehdgroup]);
    return $detail;
  }


  public function spListSubKategori (Request $req) {
    $list = DB::connection('SML')->select('select * from DBSubGroupJnsTambah where KodeSubGrp = :kodesubgroup and HDGROUP = :kodehdgroup order by Urut' , ['kodesubgroup' => $req->kodesubgroup, 'kodehdgroup' => $req->kodehdgroup]);

    return $list;
  }


}
