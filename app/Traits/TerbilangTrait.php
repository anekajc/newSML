<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use PDF;
use Excel;

trait TerbilangTrait
{
  public function terbilang($angka) {
     $angka=abs($angka);
     $baca =array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

     $terbilang="";
      if ($angka < 12){
          $terbilang= " " . $baca[$angka];
      }
      else if ($angka < 20){
          $terbilang= $this->terbilang($angka - 10) . " belas";
      }
      else if ($angka < 100){
          $terbilang= $this->terbilang($angka / 10) . " puluh" . $this->terbilang($angka % 10);
      }
      else if ($angka < 200){
          $terbilang= " seratus" . $this->terbilang($angka - 100);
      }
      else if ($angka < 1000){
          $terbilang= $this->terbilang($angka / 100) . " ratus" . $this->terbilang($angka % 100);
      }
      else if ($angka < 2000){
          $terbilang= " seribu" . $this->terbilang($angka - 1000);
      }
      else if ($angka < 1000000){
          $terbilang= $this->terbilang($angka / 1000) . " ribu" . $this->terbilang($angka % 1000);
      }
      else if ($angka < 1000000000){
         $terbilang= $this->terbilang($angka / 1000000) . " juta" . $this->terbilang($angka % 1000000);
      }
         return $terbilang;
  }
}
