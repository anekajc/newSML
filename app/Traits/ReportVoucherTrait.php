<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared report endpoints used by the accounting "styled table" reports
 * (Neraca Lajur, Trial Balance, Buku Besar, â€¦): the drill-down ledger and the
 * bottom voucher panels (Bukti Kas/Bank, Invoice, Faktur Pembelian), plus a
 * division-list helper.
 *
 * Each report controller pulls these in with `use ReportVoucherTrait;` so the
 * identical bodies live in one place. `doReport` stays per-controller because
 * each report calls a different stored procedure.
 *
 * IMPORTANT: pulling this trait in does NOT wire up routes. doLedger/doKasharian/
 * doInvoice/doLpb/loadDivisi still need `Route::get(...)` entries per controller
 * for that controller's page to actually reach them - add whichever ones the
 * controller's Blade view calls (grep the view for kasUrl/invoiceUrl/lpbUrl/
 * loadDivisiDropdown/etc. rather than assuming). Confirmed empty (as of 2026-08)
 * for every one of the 24 controllers using this trait except where a route was
 * deliberately added.
 */
trait ReportVoucherTrait {

  /**
   * Per-account ledger detail for the styled table's drill-down panel, from
   * Sp_ReportBukuTambahan for the report month, scoped to one account.
   *   - date1/date2 are the first/last day of the period month.
   *   - awal/akhir bound the account-code range; one account = same code twice
   *     (the proc also returns rows where the account is the contra/lawan entry,
   *     so a post-filter on Perkiraan would wrongly drop those).
   *   - '-' (or empty) division means "all"; the proc filters with LIKE, so
   *     translate that to the '%' wildcard.
   */
  public function doLedger(Request $req) {
    $tahun  = (int) $req->inputTahun;
    $bulan  = (int) $req->inputBulan;
    $date1  = sprintf('%04d-%02d-01', $tahun, $bulan);
    $date2  = date('Y-m-t', strtotime($date1));
    $perkiraan = trim($req->perkiraan);
    $divisi = ($req->divisi === null || $req->divisi === '' || $req->divisi === '-') ? '%' : $req->divisi;

    $rows = DB::connection('SML')->select("exec Sp_ReportBukuTambahan :awal, :akhir, :date1, :date2, :divisi, 'sa', 'y', 0",
      ['awal' => $perkiraan, 'akhir' => $perkiraan, 'date1' => $date1, 'date2' => $date2, 'divisi' => $divisi]);

    // The proc emits a synthetic 'SALDO AWAL' opening row; the drill panel builds
    // its own opening-balance row, so drop it to avoid duplication.
    return array_values(array_filter($rows, function ($r) {
      return !(isset($r->Nobukti) && trim($r->Nobukti) === 'SALDO AWAL');
    }));
  }

  /**
   * Bukti Kas/Bank/Memorial/Jurnal voucher detail for the bottom panel (clicked
   * from a ledger row). All B* Jenis (BBK/BBM/BKK/BKM/BMM/BJK) share this proc;
   * only the voucher title differs (set on the client per Jenis). SET NOCOUNT ON
   * so the print proc's internal counts don't become the first PDO result set.
   */
  public function doKasharian(Request $req) {
    $res = DB::connection('SML')->select("SET NOCOUNT ON; EXEC dbo.CetakKasharian :nobukti",
      ['nobukti' => trim($req->nobukti)]);
    return $res;
  }

  /**
   * Sales-invoice voucher detail, used when the clicked row's Jenis is INVC
   * (different proc + layout from the B* vouchers).
   */
  public function doInvoice(Request $req) {
    $res = DB::connection('SML')->select("SET NOCOUNT ON; EXEC dbo.CetakInvoicePenjualan :nobukti",
      ['nobukti' => trim($req->nobukti)]);
    return $res;
  }

  /**
   * Faktur Pembelian (purchase receipt) voucher detail, used when the clicked
   * row's Jenis is BPL.
   */
  public function doLpb(Request $req) {
    $res = DB::connection('SML')->select("SET NOCOUNT ON; EXEC dbo.CetakPenerimaanACC :nobukti",
      ['nobukti' => trim($req->nobukti)]);
    return $res;
  }

  public function loadDivisi () {
    $listData = DB::connection('SML')->select('select Devisi, NamaDevisi from DBDEVISI');
    return $listData;
  }
}
