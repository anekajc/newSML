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

class NewMasterPerkiraanController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection("SML")->select("Select Perkiraan, Keterangan, Kelompok, Tipe, Valas, DK, Neraca, FlagCashFlow, Simbol,IsPPN, GroupPerkiraan, Lokasi, iskirim, isAktif,
    case when kelompok=0 then 'Aktiva'
	      when kelompok=1 then 'Kewajiban'
	      when kelompok=2 then 'Modal'
	      when kelompok=3 then 'Pendapatan'
	      when kelompok=4 then 'Biaya'
         end as mKelompok,
	 case when Tipe=0 then 'General'
	      when Tipe=1 then 'Detail'
         end as mTipe,
	 case when DK=0 then 'Debet'
	      when DK=1 then 'Kredit'
         end as mDK,
     Case when Lokasi=0 then 'Surabaya'
          when Lokasi=1 then 'Gempol'
          else ''
     end MyLokasi
     ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
from dbPerkiraan");
    $listDataValas = DB::connection('SML')->select('SELECT KODEVLS, Simbol FROM dbVALAS');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.newmasterperkiraan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData,
      "listDataValas" => $listDataValas
    ]);

  }

  public function loadAll () {
    // $listData = VWPerkiraan::all();

    $listData = DB::connection('SML')->select("select a.Perkiraan
      ,a.Keterangan
      ,a.Kelompok
      ,a.Tipe
      ,a.Valas
      ,a.DK
      ,a.Neraca
      ,a.FlagCashFlow
      ,a.Simbol
      ,a.IsPPN
      ,a.GroupPerkiraan
      ,a.Lokasi
      ,a.iskirim
      ,a.IsAktif,
                 Case when a.Tipe=0 then 'General'
                      when a.Tipe=1 then 'Detail'
                      else ''''
                 end mtipe,
                 Case when a.DK=0 then 'Debet'
                      when a.DK=1 then 'Kredit'
                      else ''''
                 end mDK ,
                 case when kelompok=0 then 'Aktiva'
                     when kelompok=1 then 'Kewajiban'
                     when kelompok=2 then 'Modal'
                     when kelompok=3 then 'Pendapatan'
                     when kelompok=4 then 'Biaya'
                      end as mKelompok
                      ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                      from dbPerkiraan a");


    return $listData;
  }

  public function loadValas() {
      $listData = DB::connection('SML')->select('SELECT KODEVLS FROM dbVALAS');
      return response()->json($listData);
  }



  public function spAdd (Request $req) {



    if ($req->input('choice') == "I") {
      $check = VWPerkiraan::all()->where('Perkiraan',$req->perkiraan )->first();
      // return $check;
      if ($check) {
        return 0;
      }
    }

    if ($req->input('choice') == "D") {
      $check = DB::connection('SML')->select('select * from dbSubGroup where PerkPers = :perk1 or PerkH = :perk2', ['perk1' => $req->perkiraan, 'perk2' => $req->perkiraan ] );
      // return $check;
      if ($check) {
        return 'Perkiraan digunakkan di sub group';
      }

      // SP_PERKIRAAN (choice D) langsung menghapus tanpa cek pemakaian - perkiraan yang sudah
      // dipakai transaksi / set posting / detail akun ditolak di sini.
      $pemakai = [
        ['dbTransaksi',    'Transaksi Accounting'],
        ['DBPOSTHUTPIUT',  'Set Posting'],
        ['DBPERKCUSTSUPP', 'Detail Akun Customer/Supplier'],
        ['dbPerkCost',     'Detail Akun Costing'],
      ];
      foreach ($pemakai as $p) {
        $check = DB::connection('SML')->select("select top 1 1 as ada from {$p[0]} where Perkiraan = :perk", ['perk' => $req->perkiraan]);
        if ($check) {
          return 'Perkiraan digunakan di ' . $p[1];
        }
      }
    }

    // $check = DB::connection('SML')->select('select * from DBPPLDET where kodebrg = :kodebarang', ['kodebarang' => $req->kodebarang] );
    //
    // if ($check) {
    //   return 'Barang digunakkan di permintaan pembelian';
    // }


    $values = [
        $req->input('choice'),
        $req->input('perkiraan'),
        $req->input('keterangan'),
        $req->input('kelompok'),
        $req->input('tipe'),
        $req->input('valas'),
        $req->input('debetkredit'),
        $req->input('neraca'),
        $req->input('simbol') ? $req->input('simbol') : '',
        $req->input('isppn'),
        $req->input('lokasi'),
        $req->input('isaktif')
    ];


    DB::connection('SML')->update('exec SP_PERKIRAAN ?,?,?,?,?,?,?,?,?,?,?,?',$values);

    return 1;

  }

  public function detail (Request $req) {
    // return 'tes';
    // $detail = VWPerkiraan::all()->where('Perkiraan',$req->perkiraan )->first();

    $detail = DB::connection('SML')->select("select a.Perkiraan
      ,a.Keterangan
      ,a.Kelompok
      ,a.Tipe
      ,a.Valas
      ,a.DK
      ,a.Neraca
      ,a.FlagCashFlow
      ,a.Simbol
      ,a.IsPPN
      ,a.GroupPerkiraan
      ,a.Lokasi
      ,a.iskirim
      ,a.IsAktif,
                 Case when a.Tipe=0 then 'General'
                      when a.Tipe=1 then 'Detail'
                      else ''''
                 end mytipe,
                 Case when a.DK=0 then 'Debet'
                      when a.DK=1 then 'Kredit'
                      else ''''
                 end myDK ,
                 case when kelompok=0 then 'Aktiva'
             	      when kelompok=1 then 'Kewajiban'
             	      when kelompok=2 then 'Modal'
             	      when kelompok=3 then 'Pendapatan'
             	      when kelompok=4 then 'Biaya'
                      end as myKelompok
                      , case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                      from dbPerkiraan a
                      where a.perkiraan = :perkiraan" , ["perkiraan" => $req->perkiraan]);

    return $detail;

  }

  public function getAllPerkiraan (Request $req) {
    $perk = $req->xperkiraan;
    $lengthx = strlen($perk) + 1;
    // return [
    //   "perk" => $perk ,
    //   "length" => $lengthx
    // ];

    for ($x = 0; $x <= $lengthx; $x++) {
      $string = substr($perk, 0, $lengthx - $x - 1);

      $list = DB::connection('SML')->select("select a.Perkiraan
        ,a.Keterangan
        ,a.Kelompok
        ,a.Tipe
        ,a.Valas
        ,a.DK
        ,a.Neraca
        ,a.FlagCashFlow
        ,a.Simbol
        ,a.IsPPN
        ,a.GroupPerkiraan
        ,a.Lokasi
        ,a.iskirim
        ,a.IsAktif,
                   Case when a.Tipe=0 then 'General'
                        when a.Tipe=1 then 'Detail'
                        else ''''
                   end mytipe,
                   Case when a.DK=0 then 'Debet'
                        when a.DK=1 then 'Kredit'
                        else ''''
                   end myDK ,
                   case when kelompok=0 then 'Aktiva'
                      when kelompok=1 then 'Kewajiban'
                      when kelompok=2 then 'Modal'
                      when kelompok=3 then 'Pendapatan'
                      when kelompok=4 then 'Biaya'
                        end as myKelompok
                        ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                         from dbPerkiraan a
                         where perkiraan like '" . $string . "%' and len(a.perkiraan) = :length order by perkiraan asc", ["length" => $lengthx - $x]);

              if (count($list)) {
                break;
              }

    }

    return $list;

  }

  public function getAllPerkiraanX (Request $req) {

    $list = DB::connection('SML')->select("select a.Perkiraan
      ,a.Keterangan
      ,a.Kelompok
      ,a.Tipe
      ,a.Valas
      ,a.DK
      ,a.Neraca
      ,a.FlagCashFlow
      ,a.Simbol
      ,a.IsPPN
      ,a.GroupPerkiraan
      ,a.Lokasi
      ,a.iskirim
      ,a.IsAktif,
                 Case when a.Tipe=0 then 'General'
                      when a.Tipe=1 then 'Detail'
                      else ''''
                 end mytipe,
                 Case when a.DK=0 then 'Debet'
                      when a.DK=1 then 'Kredit'
                      else ''''
                 end myDK ,
                 case when kelompok=0 then 'Aktiva'
             	      when kelompok=1 then 'Kewajiban'
             	      when kelompok=2 then 'Modal'
             	      when kelompok=3 then 'Pendapatan'
             	      when kelompok=4 then 'Biaya'
                      end as myKelompok
                      ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                       from dbPerkiraan a
                       where perkiraan like '" . $req->xperkiraan . "%' order by perkiraan asc");

    // $list = VWPerkiraan::all();
    $list2 = DB::connection('SML')->select("select a.Perkiraan
      ,a.Keterangan
      ,a.Kelompok
      ,a.Tipe
      ,a.Valas
      ,a.DK
      ,a.Neraca
      ,a.FlagCashFlow
      ,a.Simbol
      ,a.IsPPN
      ,a.GroupPerkiraan
      ,a.Lokasi
      ,a.iskirim
      ,a.IsAktif,
                 Case when a.Tipe=0 then 'General'
                      when a.Tipe=1 then 'Detail'
                      else ''''
                 end mytipe,
                 Case when a.DK=0 then 'Debet'
                      when a.DK=1 then 'Kredit'
                      else ''''
                 end myDK ,
                 case when kelompok=0 then 'Aktiva'
             	      when kelompok=1 then 'Kewajiban'
             	      when kelompok=2 then 'Modal'
             	      when kelompok=3 then 'Pendapatan'
             	      when kelompok=4 then 'Biaya'
                      end as myKelompok
                      ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                      from dbPerkiraan a
                       where perkiraan like '" . $req->xperkiraan2 . "%' order by perkiraan asc");

                 $list3 = DB::connection('SML')->select("select a.Perkiraan
                   ,a.Keterangan
                   ,a.Kelompok
                   ,a.Tipe
                   ,a.Valas
                   ,a.DK
                   ,a.Neraca
                   ,a.FlagCashFlow
                   ,a.Simbol
                   ,a.IsPPN
                   ,a.GroupPerkiraan
                   ,a.Lokasi
                   ,a.iskirim
                   ,a.IsAktif,
                              Case when a.Tipe=0 then 'General'
                                   when a.Tipe=1 then 'Detail'
                                   else ''''
                              end mytipe,
                              Case when a.DK=0 then 'Debet'
                                   when a.DK=1 then 'Kredit'
                                   else ''''
                              end myDK ,
                              case when kelompok=0 then 'Aktiva'
                          	      when kelompok=1 then 'Kewajiban'
                          	      when kelompok=2 then 'Modal'
                          	      when kelompok=3 then 'Pendapatan'
                          	      when kelompok=4 then 'Biaya'
                                   end as myKelompok
                                   ,case when isnull(isaktif,0)=0 then 'Aktif' Else 'Tidak Aktif' End Status
                                   from dbPerkiraan a
                                    where perkiraan like '" . $req->xperkiraan3 . "%' order by perkiraan asc");


    return ["list" => $list , "list2" => $list2 , "list3" => $list3];

  }




}
