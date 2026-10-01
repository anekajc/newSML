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

class MasterDaftarKaryawanController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM dbKaryawan');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterdaftarkaryawan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM dbKaryawan');
    return $listData;
  }

  public function getNewKeyNIK () {
    $listData = DB::connection('SML')->select('select max(KeyNIK)KeyNIK from dbKaryawan');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbKaryawan where NIK = :Nik' , ['Nik' => $req->Nik]);

    if ($check) {
      return 'NIK sudah ada di database';
    }
    $listData = DB::connection('SML')->update("insert into dbKaryawan (
      KeyNIK,
      NIK,
      Nama,
      AlamatKTP,
      TeleponHP,
      KodePosRmh,
      Kelamin,
      NomorKTP,
      TmpLahir,
      Email,
      TglLahir,
      Agama,
      KetPendAkhir,
      Tinggi,
      Berat,
      TglMasuk,
      NPWP,
      IsSales,
      Aktif,
      Produksi,
      KodeGdg,
      KodeCost,
      Penagih)
      values (
    :keyNIK,
    :Nik,
    :NamaLengkap,
    :Alamat,
    :HP,
    :KodePos,
    :Kelamin,
    :KTP,
    :TempatLahir,
    :Email,
    :TanggalLahir,
    :Agama,
    :PendidikanAkhir,
    :Tinggi,
    :Berat,
    :TglMasuk,
    :NPWP,
    :Status,
    :Aktif,
    :Produksi,
    '', '',
    :Penagih)" ,
  [
    'keyNIK' => $req->keyNIK,
    'Nik' => $req->Nik,
    'NamaLengkap' => $req->NamaLengkap,
    'Alamat' => $req->Alamat,
    'HP' => $req->HP,
    'KodePos' => $req->KodePos,
    'Kelamin' => $req->Kelamin,
    'KTP' => $req->KTP,
    'TempatLahir' => $req->TempatLahir,
    'Email' => $req->Email,
    'TanggalLahir' => $req->TanggalLahir,
    'Agama' => $req->Agama,
    'PendidikanAkhir' => $req->PendidikanAkhir,
    'Tinggi' => $req->Tinggi,
    'Berat' => $req->Berat,
    'TglMasuk' => $req->TglMasuk,
    'NPWP' => $req->NPWP,
    'Status' => $req->Status,
    'Aktif' => $req->Aktif,
    'Produksi' => $req->Produksi,
    'Penagih' => $req->Penagih]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }

    // Karyawan yang masih punya data Sales Cust / Target Sales (master Sales) tidak boleh dihapus -
    // keduanya terikat lewat KeyNIK, dulu tidak dicek.
    $karyawan = DB::connection('SML')->select('select KeyNIK from dbKaryawan where NIK = :Nik', ['Nik' => $req->Nik]);
    if ($karyawan) {
      $keynik = $karyawan[0]->KeyNIK;
      if (DB::connection('SML')->select('select top 1 1 as ada from DBSalesCustomer where KeyNik = :keynik', ['keynik' => $keynik])) {
        return 'Karyawan masih punya data Sales Customer';
      }
      if (DB::connection('SML')->select('select top 1 1 as ada from DbTargetsales where KeyNik = :keynik', ['keynik' => $keynik])) {
        return 'Karyawan masih punya data Target Sales';
      }
    }

    $delete = DB::connection('SML')->update('delete from dbKaryawan where NIK = :Nik', ['Nik'=>$req->Nik]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update dbKaryawan set

    Nama = :NamaLengkap,
    AlamatKTP = :Alamat,
    TeleponHP = :HP,
    KodePosRmh = :KodePos,
    Kelamin = :Kelamin,
    NomorKTP = :KTP,
    TmpLahir = :TempatLahir,
    Email = :Email,
    TglLahir = :TanggalLahir,
    Agama = :Agama,
    KetPendAkhir = :PendidikanAkhir,
    Tinggi = :Tinggi,
    Berat = :Berat,
    TglMasuk = :TglMasuk,
    NPWP = :NPWP,
    IsSales = :Status,
    Aktif = :Aktif,
    Produksi = :Produksi,
    Penagih = :Penagih

    where NIK = :Nik' ,
    [
      'Nik' => $req->Nik,
      'NamaLengkap' => $req->NamaLengkap,
      'Alamat' => $req->Alamat,
      'HP' => $req->HP,
      'KodePos' => $req->KodePos,
      'Kelamin' => $req->Kelamin,
      'KTP' => $req->KTP,
      'TempatLahir' => $req->TempatLahir,
      'Email' => $req->Email,
      'TanggalLahir' => $req->TanggalLahir,
      'Agama' => $req->Agama,
      'PendidikanAkhir' => $req->PendidikanAkhir,
      'Tinggi' => $req->Tinggi,
      'Berat' => $req->Berat,
      'TglMasuk' => $req->TglMasuk,
      'NPWP' => $req->NPWP,
      'Status' => $req->Status,
      'Aktif' => $req->Aktif,
      'Produksi' => $req->Produksi,
      'Penagih' => $req->Penagih]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM dbKaryawan where NIK = :Nik' , ['Nik' => $req->Nik]);
    return $detail;
  }



}
