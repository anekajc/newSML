<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\User;
use App\Models\Periode;
use App\Model\AksesMenu;
use App\Traits\ReportTrait;

class ReportController extends Controller {
	use ReportTrait;

	public function cetakLaporan(Request $req) {
		$perk = "";
		if ($req->has('perkiraan')) { $perk = $req->input('perkiraan'); }
		return $this->cetak($req->input('title'), $req->input('table'), $req->input('periode'), $perk, $req->input('paper'), $req->input('orientation'));
	}

	public function PDF(Request $req) {
		$perk = "";
		if ($req->has('perkiraan')) { $perk = $req->input('perkiraan'); }
		return $this->exportPDF($req->input('title'), $req->input('table'), $req->input('periode'), $perk, $req->input('paper'), $req->input('orientation'));
	}

	public function Excel(Request $req) {
		$perk = "";
		if ($req->has('perkiraan')) { $perk = $req->input('perkiraan'); }
		return $this->exportExcel($req->input('title'), $req->input('table'), $req->input('periode'), $perk);
	}

}

?>
