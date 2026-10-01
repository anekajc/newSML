<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use PDF;
use Excel;

trait ReportTrait
{
  public function loadQuery($table, $select, $where, $order, $groupby, $col, $len, $count, $header) {
    $row = NULL; $counter = array(); $subcounter = array(); $numeric = array(); $numericType = array('integer', 'bigint', 'float');

    // query
    $row =  DB::table($table)->whereRaw($where)->selectRaw($select)->orderByRaw($order)->get();
    $hidden = DB::table('report_'.$table)->where('user', \Auth::id())->first();

    // header
    $res = "<table class='table table-bordered' style='table-layout:fixed; width: 100%;' id='tabel'><thead><tr>";
    for ($j = 0; $j < count($header); $j++) {
      if ($hidden == NULL) { $res .= "<th style='width: ".$len[$j]."px; vertical-align: middle;'>".$header[$j]."</th>"; }
      else if ($hidden->{$col[$j]} == 1) { $res .= "<th style='width: ".$len[$j]."px; vertical-align: middle;'>".$header[$j]."</th>"; }
    }

    // count, counter & numeric column
    for ($i = 0; $i < count($len); $i++) { array_push($counter, 0.0); array_push($subcounter, 0.0); array_push($numeric, DB::connection()->getDoctrineColumn($table, $col[$i])->getType()->getName()); }
    if ($hidden != NULL) {
      for ($i = 0; $i < count($count); $i++) { if ($hidden->{$col[$count[$i]]} == 0) { array_splice($count, $i, 1); $i--; } }
    }

    // body
    $res .= "</tr></thead><tbody>";
    for ($i = 0; $i < count($row); $i++) {
      $res .= "<tr>";
      for ($j = 0; $j < count($len); $j++) {
        if ($hidden == NULL) {
          if (in_array($numeric[$j], $numericType)) {
            $res .= "<td style='text-align: right;'>".number_format($row[$i]->{$col[$j]}, 2)."</td>";
          } else if ($numeric[$j] == "date") {
            $res .= "<td>".date("d/m/Y", strtotime($row[$i]->{$col[$j]}))."</td>";
          } else {
            $res .= "<td>".$row[$i]->{$col[$j]}."</td>";
          }
          if (in_array($j, $count)) { $counter[$j] += $row[$i]->{$col[$j]}; $subcounter[$j] += $row[$i]->{$col[$j]}; }
        } else if ($hidden->{$col[$j]} == 1) {
          if (in_array($numeric[$j], $numericType)) {
            $res .= "<td style='text-align: right;'>".number_format($row[$i]->{$col[$j]}, 2)."</td>";
          } else if ($numeric[$j] == "date") {
            $res .= "<td>".date("d/m/Y", strtotime($row[$i]->{$col[$j]}))."</td>";
          } else {
            $res .= "<td>".$row[$i]->{$col[$j]}."</td>";
          }
          if (in_array($j, $count)) { $counter[$j] += $row[$i]->{$col[$j]}; $subcounter[$j] += $row[$i]->{$col[$j]}; }
        }
      }
      // sub total
      if ($groupby != "-") {
        if (($i == count($row) - 1 || $row[$i]->{$groupby} != $row[$i + 1]->{$groupby}) && array_sum($subcounter) > 0 && ($hidden == NULL || $hidden->total == 1)) {
          $res .= "</tr><tr>";
          if ($hidden == NULL) {
            for ($a = 0; $a < count($count); $a++) {
              if ($a == 0 && $count[$a] > 0) {
                $res .= "<td colspan='".$count[$a]."'><b>Subtotal</b></td><td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>";
              } else if ($a == 0 && $count[$a] == 0) {
                $res .= "<td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>";
              } else if ($a != 0 && $count[$a] - $count[$a - 1] > 1) {
                $res .= "<td colspan='".($count[$a] - $count[$a - 1] - 1)."'></td><td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>";
              } else if ($a != 0 && $count[$a] - $count[$a - 1] == 1) {
                $res .= "<td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>";
              }
            }
            if ((count($col) - 1) - ($count[count($count) - 1]) > 0) {
              $res .= "<td colspan='".((count($col) - 1) - ($count[count($count) - 1]))."'></td>";
            }
          } else {
            for ($a = 0; $a < count($count); $a++) {
              if ($a == 0) {
                $offset = 0;
                for ($b = 0; $b < $count[0]; $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
                if ($offset > 0) { $res .= "<td colspan='".$offset."'><b>Subtotal</b></td><td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>"; }
                else { $res .= "<td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>"; }
              } else {
                $offset = 0;
                for ($b = $count[$a - 1] + 1; $b < $count[$a]; $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
                if ($offset > 0) { $res .= "<td colspan='".$offset."'></td><td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>"; }
                else { $res .= "<td style='text-align: right;'><b>".number_format($subcounter[$count[$a]], 2)."</b></td>"; }
              }
              if ($a == count($count) - 1) {
                $offset = 0;
                for ($b = $count[$a] + 1; $b < count($col); $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
                if ($offset > 0) { $res .= "<td colspan='".$offset."'></td>"; }
              }
            }
          }
          for ($a = 0; $a < count($subcounter); $a++) { $subcounter[$a] = 0.0; }
        }
      }
      $res .= "</tr>";
    }

    // kalo tidak ada data
    if (count($row) == 0) {
      if ($hidden == NULL) {
        $res .= "<td colspan='".count($len)."'>Tidak ada data</td>";
      } else {
        $offset = 0;
        for ($a = 0; $a < count($len); $a++) { if ($hidden->{$col[$a]} == 1) { $offset++; } }
        $res .= "<td colspan='".$offset."'>Tidak ada data</td>";
      }
    }

    // total
    if (array_sum($counter) > 0 && ($hidden == NULL || $hidden->total == 1)) {
      $res .= "<tr>";
      if ($hidden == NULL) {
        for ($a = 0; $a < count($count); $a++) {
          if ($a == 0 && $count[$a] > 0) {
            $res .= "<td colspan='".$count[$a]."'><b>Total</b></td><td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>";
          } else if ($a == 0 && $count[$a] == 0) {
            $res .= "<td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>";
          } else if ($a != 0 && $count[$a] - $count[$a - 1] > 1) {
            $res .= "<td colspan='".($count[$a] - $count[$a - 1] - 1)."'></td><td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>";
          } else if ($a != 0 && $count[$a] - $count[$a - 1] == 1) {
            $res .= "<td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>";
          }
        }
        if (count($count) > 0) {
          if ((count($col) - 1) - ($count[count($count) - 1]) > 0) {
            $res .= "<td colspan='".((count($col) - 1) - ($count[count($count) - 1]))."'></td>";
          }
        }
      } else {
        for ($a = 0; $a < count($count); $a++) {
          if ($a == 0) {
            $offset = 0;
            for ($b = 0; $b < $count[0]; $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
            if ($offset > 0) { $res .= "<td colspan='".$offset."'><b>Total</b></td><td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>"; }
            else { $res .= "<td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>"; }
          } else {
            $offset = 0;
            for ($b = $count[$a - 1] + 1; $b < $count[$a]; $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
            if ($offset > 0) { $res .= "<td colspan='".$offset."'></td><td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>"; }
            else { $res .= "<td style='text-align: right;'><b>".number_format($counter[$count[$a]], 2)."</b></td>"; }
          }
          if ($a == count($count) - 1) {
            $offset = 0;
            for ($b = $count[$a] + 1; $b < count($col); $b++) { if ($hidden->{$col[$b]} == 1) { $offset++; } }
            if ($offset > 0) { $res .= "<td colspan='".$offset."'></td>"; }
          }
        }
      }
      $res .= "</tr>";
    }
    $res .= "</tbody></table>";
    return $res;
  }

  public function columnReport($table) {
    $val = array(); $col = DB::getSchemaBuilder()->getColumnListing($table);
    for ($i = 2; $i < count($col); $i++) { array_push($val, ucwords(str_replace('_', ' ', $col[$i]))); }
    return $val;
  }

  public function setColumnReport($table, $check) {
    $val = array(); $col = DB::getSchemaBuilder()->getColumnListing($table);
    for ($i = 2; $i < count($col); $i++) { $val[$col[$i]] = $check[$i - 2]; }
    DB::table($table)->updateOrInsert(['user' => \Auth::id()], $val);
  }

  public function getColumnReport($table) {
    $count = DB::table($table)->where('user', \Auth::id())->count();
    $val = array(); $col = DB::getSchemaBuilder()->getColumnListing($table);
    if ($count == 0) {
      for ($i = 2; $i < count($col); $i++) { array_push($val, 1); }
    } else {
      $getVal = DB::table($table)->where('user', \Auth::id())->first();
      for ($i = 2; $i < count($col); $i++) { array_push($val, $getVal->{$col[$i]}); }
    }
    return $val;
  }

  public function exportPDF($title, $table, $periode, $perkiraan, $paper, $orientation) {
    $table = '<table class="table table-bordered" style="width: 100%;">'.$table.'</table>';
		$pdf = PDF::loadView("/templatepdf", compact('table'))->setPaper($paper)->setOrientation($orientation);
    $perusahaan = DB::table('perusahaan')->where('id', 1)->first();
		$pdf->setOption('title', $title)
        ->setOption('header-html', URL::asset('header_laporan.html'))
        ->setOption('replace', array('perusahaan' => $perusahaan->nama, 'judul' => $title, 'periode' => $periode, 'perkiraan' => $perkiraan))
        ->setOption('header-spacing', 4)
        ->setOption('margin-top', 22)
        ->setOption('footer-left', 'Waktu cetak [date] [time] oleh '.\Auth::user()->name)
        ->setOption('footer-right', 'Halaman [page] dari [topage]')
        ->setOption('footer-font-size', 8)
        ->setOption('footer-spacing', 2)
        ->setOption('footer-line', true);
	 	return $pdf->download($title.'.pdf');
  }

  public function cetak($title, $table, $periode, $perkiraan, $paper, $orientation) {
    $table = '<table class="table table-bordered" style="width: 100%;">'.$table.'</table>';
		$pdf = PDF::loadView("/templatepdf", compact('table'))->setPaper($paper)->setOrientation($orientation);
    $perusahaan = DB::table('perusahaan')->where('id', 1)->first();
    if ($perkiraan == "") { $pdf->setOption('header-html', URL::asset('header_laporan.html'))->setOption('margin-top', 20); }
    else { $pdf->setOption('header-html', URL::asset('header_laporan_akun.html'))->setOption('margin-top', 26); }
		$pdf->setOption('title', $title)
        ->setOption('replace', array('perusahaan' => $perusahaan->nama, 'judul' => $title, 'periode' => $periode, 'perkiraan' => $perkiraan))
        ->setOption('header-spacing', 4)
        ->setOption('footer-left', 'Waktu cetak [date] [time] oleh '.\Auth::user()->name)
        ->setOption('footer-right', 'Halaman [page] dari [topage]')
        ->setOption('footer-font-size', 8)
        ->setOption('footer-spacing', 2)
        ->setOption('footer-line', true);
	 	return $pdf->inline($title.'.pdf');
  }

  public function exportExcel($title, $table, $periode, $perkiraan) {
    $fixed = preg_replace('/\<th (.*?)>/', "<th>", $table);
    $fixed = preg_replace('/\<table (.*?)>/', "<table>", $fixed);
    $perusahaan = DB::table('perusahaan')->where('id', 1)->first();
    $table = '<table><tr><th>'.$perusahaan->nama.'</th><th>'.$title.'</th></tr><tr><th></th><th>'.$periode.'</th></tr>'.$fixed.'</table>';
		Excel::create($title, function($excel) use ($table, $title) {
			$excel->sheet($title, function($sheet) use ($table) {
				$sheet->loadView('templateexcel', compact('table'));
			});
		})->download('xlsx');
  }
}
