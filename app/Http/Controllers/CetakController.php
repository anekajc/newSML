<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\Nomor;
use App\Model\AksesMenu;
use App\Model\User;
use App\Model\Periode;
use App\Model\Transaksi;
use App\Model\Trans;
use PDF;

class CetakController extends Controller {

	public function cetakKasBank(Request $req) {
		$no_bukti = $req->input('no_bukti');  $tanggal_oto = $req->input('tanggal_oto'); $nomor = Nomor::where('id', 1)->first();
		$getTransaksi = Transaksi::join('perkiraan', 'perkiraan.kode', '=', 'transaksi.kode_perkiraan')->where('transaksi.no_bukti', $no_bukti)->selectRaw('transaksi.*, perkiraan.keterangan AS ket_perk')->first(); $head = "";
		$getDetTransaksi = Trans::join('perkiraan as p1', 'trans.perkiraan', '=', 'p1.kode')->join('perkiraan as p2', 'trans.lawan', '=', 'p2.kode')->where('trans.no_bukti', $no_bukti)->selectRaw('trans.*, p1.keterangan AS perk_ket, p2.keterangan AS lawan_ket')->orderBy('trans.urut', 'ASC')->get();
		if ($getTransaksi->tipe_transaksi == $nomor->kas_keluar) { $head = "BUKTI KAS KELUAR"; } else if ($getTransaksi->tipe_transaksi == $nomor->kas_masuk) { $head = "BUKTI KAS MASUK"; } else if ($getTransaksi->tipe_transaksi == $nomor->bank_keluar) { $head = "BUKTI BANK KELUAR"; } else { $head = "BUKTI BANK MASUK"; }
		$table = "<table class='table table-bordered' border='1' style='width: 100%; height: 100%; border-top: 1px solid black;'><colgroup><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'></colgroup>";
		for ($i = 0; $i < count($getDetTransaksi); $i++) {
			if ($getTransaksi->tipe_transaksi == $nomor->kas_keluar || $getTransaksi->tipe_transaksi == $nomor->bank_keluar) { $table .= "<tr ><td>".$getDetTransaksi[$i]->perkiraan."</td><td>".$getDetTransaksi[$i]->perk_ket."</td><td>".$getDetTransaksi[$i]->lawan."</td><td colspan='4'>".$getDetTransaksi[$i]->keterangan."</td><td style='text-align: right;'>".number_format($getDetTransaksi[$i]->debet_rp, 2)."</td></tr>"; }
			else { $table .= "<tr ><td>".$getDetTransaksi[$i]->lawan."</td><td>".$getDetTransaksi[$i]->lawan_ket."</td><td>".$getDetTransaksi[$i]->perkiraan."</td><td colspan='4'>".$getDetTransaksi[$i]->keterangan."</td><td style='text-align: right;'>".number_format($getDetTransaksi[$i]->debet_rp, 2)."</td></tr>"; }
		}
		$table .= "</table>";
		$pdf = PDF::loadView("/templatepdf", compact('table'))->setPaper('A5')->setOrientation('landscape');
		$pdf->setOption('replace', array('total' => number_format($getTransaksi->jumlah, 2), 'user' => \Auth::user()->name, 'head' => $head, 'no_bukti' => $getTransaksi->no_bukti, 'tanggal' => date("d/m/Y", strtotime($getTransaksi->tanggal)), 'note' => $getTransaksi->note, 'ket_perk' => $getTransaksi->ket_perk, 'no_bon' => $getTransaksi->no_bon, 'tanggal_oto' => $tanggal_oto))
				->setOption('footer-html', URL::asset('public/footer_kasbank.html'));
		if ($getTransaksi->tipe_transaksi == $nomor->kas_keluar || $getTransaksi->tipe_transaksi == $nomor->kas_masuk) { $pdf->setOption('header-html', URL::asset('public/header_kas.html'))->setOption('title', 'Kas'); return $pdf->inline("Kas.pdf"); }
		else { $pdf->setOption('header-html', URL::asset('public/header_bank.html'))->setOption('title', 'Bank'); return $pdf->inline("Bank.pdf"); }
	}

	public function cetakMemorialKoreksi(Request $req) {
		$no_bukti = $req->input('no_bukti');  $tanggal_oto = $req->input('tanggal_oto'); $nomor = Nomor::where('id', 1)->first();
		$getTransaksi = Transaksi::where('no_bukti', $no_bukti)->first(); $head = "";
		$getDetTransaksi = Trans::join('perkiraan as p1', 'trans.perkiraan', '=', 'p1.kode')->join('perkiraan as p2', 'trans.lawan', '=', 'p2.kode')->where('trans.no_bukti', $no_bukti)->selectRaw('trans.*, p1.keterangan AS perk_ket, p2.keterangan AS lawan_ket')->orderBy('trans.urut', 'ASC')->get();
		if ($getTransaksi->tipe_transaksi == $nomor->bukti_memorial) { $head = "BUKTI MEMORIAL"; } else { $head = "BUKTI JURNAL KOREKSI"; }
		$table = "<table class='table table-bordered' border='1' style='width: 100%; height: 100%; border-top: 1px solid black;'><colgroup><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'></colgroup>";
		for ($i = 0; $i < count($getDetTransaksi); $i++) {
			$table .= "<tr ><td>".$getDetTransaksi[$i]->perkiraan."</td><td>".$getDetTransaksi[$i]->perk_ket."</td><td>".$getDetTransaksi[$i]->lawan."</td><td>".$getDetTransaksi[$i]->lawan_ket."</td><td colspan='3'>".$getDetTransaksi[$i]->keterangan."</td><td style='text-align: right;'>".number_format($getDetTransaksi[$i]->debet_rp, 2)."</td></tr>";
		}
		$table .= "</table>";
		$pdf = PDF::loadView("/templatepdf", compact('table'))->setPaper('A5')->setOrientation('landscape');
		$pdf->setOption('replace', array('total' => number_format($getTransaksi->jumlah, 2), 'user' => \Auth::user()->name, 'head' => $head, 'no_bukti' => $getTransaksi->no_bukti, 'tanggal' => date("d/m/Y", strtotime($getTransaksi->tanggal)), 'note' => $getTransaksi->note, 'tanggal_oto' => $tanggal_oto))
				->setOption('footer-html', URL::asset('public/footer_kasbank.html'))
				->setOption('header-html', URL::asset('public/header_memorialkoreksi.html'))->setOption('title', 'Memorial / Koreksi');
		return $pdf->inline("Memorial / Koreksi.pdf");
	}

	public function cetakGiro(Request $req) {
		$no_bukti = $req->input('no_bukti');  $tanggal_oto = $req->input('tanggal_oto'); $nomor = Nomor::where('id', 1)->first();
		$getTransaksi = Transaksi::join('perkiraan', 'perkiraan.kode', '=', 'transaksi.kode_perkiraan')->where('transaksi.no_bukti', $no_bukti)->selectRaw('transaksi.*, perkiraan.keterangan AS ket_perk')->first(); $head = "";
		$getDetTransaksi = Trans::join('perkiraan as p1', 'trans.perkiraan', '=', 'p1.kode')->join('perkiraan as p2', 'trans.lawan', '=', 'p2.kode')->where('trans.no_bukti', $no_bukti)->selectRaw('trans.*, p1.keterangan AS perk_ket, p2.keterangan AS lawan_ket')->orderBy('trans.urut', 'ASC')->get();
		if ($getTransaksi->tipe_transaksi == $nomor->bukti_giro_terima) { $head = "BUKTI GIRO TERIMA"; } else if ($getTransaksi->tipe_transaksi == $nomor->bukti_giro_cair) { $head = "BUKTI GIRO CAIR"; } else if ($getTransaksi->tipe_transaksi == $nomor->bukti_buka_giro) { $head = "BUKTI BUKA GIRO"; } else { $head = "BUKTI CAIR GIRO"; }
		$table = "<table class='table table-bordered' border='1' style='width: 100%; height: 100%; border-top: 1px solid black;'><colgroup><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'><col width='8%'><col width='17%'></colgroup>";
		for ($i = 0; $i < count($getDetTransaksi); $i++) {
			if ($getTransaksi->tipe_transaksi == $nomor->bukti_giro_cair || $getTransaksi->tipe_transaksi == $nomor->bukti_buka_giro) { $table .= "<tr ><td>".$getDetTransaksi[$i]->perkiraan."</td><td>".$getDetTransaksi[$i]->perk_ket."</td><td>".$getDetTransaksi[$i]->lawan."</td><td colspan='4'>".$getDetTransaksi[$i]->keterangan."</td><td style='text-align: right;'>".number_format($getDetTransaksi[$i]->debet_rp, 2)."</td></tr>"; }
			else { $table .= "<tr ><td>".$getDetTransaksi[$i]->lawan."</td><td>".$getDetTransaksi[$i]->lawan_ket."</td><td>".$getDetTransaksi[$i]->perkiraan."</td><td colspan='4'>".$getDetTransaksi[$i]->keterangan."</td><td style='text-align: right;'>".number_format($getDetTransaksi[$i]->debet_rp, 2)."</td></tr>"; }
		}
		$table .= "</table>";
		$pdf = PDF::loadView("/templatepdf", compact('table'))->setPaper('A5')->setOrientation('landscape');
		$pdf->setOption('replace', array('total' => number_format($getTransaksi->jumlah, 2), 'user' => \Auth::user()->name, 'head' => $head, 'no_bukti' => $getTransaksi->no_bukti, 'tanggal' => date("d/m/Y", strtotime($getTransaksi->tanggal)), 'note' => $getTransaksi->note, 'ket_perk' => $getTransaksi->ket_perk, 'no_bon' => $getTransaksi->no_bon, 'tanggal_oto' => $tanggal_oto))
				->setOption('footer-html', URL::asset('public/footer_kasbank.html'))
				->setOption('header-html', URL::asset('public/header_bank.html'))->setOption('title', 'Giro');
		return $pdf->inline("Giro.pdf");
	}

}

?>
