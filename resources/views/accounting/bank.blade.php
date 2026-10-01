@extends('newmasterTest')
{{-- @extends('accounting.newmaster') --}}
@section('page-title', 'Bank')
@section('buttons')

@endsection

@section('css')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

{{-- Gudang-style list view (#page1) plus .dph-tb skin on #page2/#page3 and the entity-picker
     modals — see docs/new-design-gudang-style-guide.md. Same treatment as
     accounting/kas.blade.php (this page is its Bank twin — identical dbTrans/dbTransaksi
     query shape, just filtered on BBM/BBK instead of BKM/BKK). newmasterTest doesn't load
     report-table.css/tableMaster2.css itself — added here, page-local, so no other page on
     this shared layout is affected. tableMaster2.css's .btn-action-* rules carry !important
     (fixed there, not here), so the pill/action buttons below render correctly on this
     layout without a page-local override. The ~19 tabel_add_list_* picker modals keep their
     existing DataTables-driven search/paging behavior untouched (per the style guide's §13
     ask-first note on existing picker patterns) — only their table skin (.dph-tb) and footer
     buttons were restyled.

     #page1 (toolbar + table markup, CSS, table id, and pagination) was made byte-for-byte
     identical to accounting/memorialkoreksi.blade.php / accounting/bonsementara.blade.php on
     request — same `po-*` class family, same `<table id="tabel">`, same DataTables-driven
     pagination (replacing the page's original hand-rolled #footerLabel1/#pagerBtns1/.pg
     pager — see public/js/bank.js's renderTabel()/dphtInitReportTableSekali-equivalent for the
     conversion). report-table.css/tableMaster2.css/newmaster.css/pengajuandphtunai.css stay
     loaded regardless (unlike the reference pages) because page2+ (the Add/Detail/Otorisasi
     forms) and the tabel_add_list_* entity-picker modals still depend on them — #page1 just
     neutralizes the few rules from those files that would otherwise leak onto #tabel (see the
     CSS block below). Bank now has the same Periode date-range filter as the DPH pages
     (#inputDate1/#inputDate2 in the toolbar) - see BankController::fetchList()/loadAll()
     and public/js/bank.js's ikatPeriode(). --}}
<link rel="stylesheet" href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/pengajuandphtunai.css') !!}?v={{ @filemtime(base_path('public/css/pengajuandphtunai.css')) ?: '1' }}">

{{-- Header tabel interaktif (geser kolom + roda gigi + bar kolom tersembunyi + kotak
 scroll bertajuk sticky) dalam skema po-* — lihat po-table-header.css untuk daftar id
 terdaftar (#tabel, dipakai halaman ini, sudah terdaftar di sana untuk halaman-halaman
 lain). Dimuat SETELAH report-table.css supaya .po-*/.rt-colmenu-nya menang saat
 spesifisitas seri. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

<style>
/* Jarak kartu ke bar atas — sama seperti pengajuandph/pengajuandpp/bonsementara. */
#content { padding-top: 12px; }

/* po-table-header.css tidak menulis .po-len-wrap/.po-len-inp — disalin apa adanya dari
   accounting/pengajuandph.blade.php, halaman lain yang sudah memakai skema po-* dan
   menulis dropdown Tampilkan ini page-local juga. */
.po-len-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--rt-card, #fff);
    border: 1.5px solid var(--rt-border, #E7E8F0);
    border-radius: 8px;
    padding: 5px 12px;
}
.po-len-wrap label {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft, #6B7180);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
}
.po-len-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink, #1D2130);
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
}

/* ==========================================================================
   Dari sini ke bawah: disalin verbatim dari accounting/memorialkoreksi.blade.php
   (dan bonsementara.blade.php) supaya #page1 (list Bank) memakai class & tampilan
   yang persis sama — lihat permintaan "samakan dengan bonsementara/memorialkoreksi"
   (sudah diterapkan lebih dulu di accounting/pengajuandph.blade.php dan
   accounting/pengajuandphtunai.blade.php).
   ========================================================================== */

/* Rule .card global di sebagian layout (flex + align-items:center + efek melayang
   saat hover) diperuntukkan kartu menu dashboard, bukan kartu berisi tabel. */
#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}

#page1 .card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: var(--border, #e5e7eb) !important;
}

/* Gaya dasar .data-table (tabel, thead th, tbody td, baris terakhir, hover) tidak ditulis
   di sini - sudah dideklarasikan layout newmasterTest di <style>-nya sendiri, yang dimuat
   SETELAH @yield('css') sehingga selalu menang (sama seperti marketing/so.blade.php). */

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai
   inline style pada <table>, yang mengalahkan `.data-table { width: 100% }`.
   Dipakai min-width, BUKAN width. */
#tabel { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabel td:first-child:not([colspan]) { vertical-align: middle; }

#tabel td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn {
  width: 30px;
  height: 30px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
  font-size: 13px;
  border: 1px solid transparent;
  box-shadow: none;
  transition: all .12s ease;
}

#tabel td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

/* Tombol di kolom Aksi baru muncul saat barisnya di-hover. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* ==========================================================================
   Penetral kebocoran gaya - halaman ini (beda dengan memorialkoreksi) masih memuat
   report-table.css/tableMaster2.css/newmaster.css karena page2+ (form tambah/detail/
   otorisasi) dan modal-modal tabel_add_list_* masih memakainya. Aturan di bawah ini
   HANYA menimpa balik nilai file-file itu supaya #page1 tetap identik dengan
   referensi, tanpa melepas file-nya (yang akan merusak halaman lain).

   font-size/color/padding SENGAJA TANPA !important - tableMaster2.css tidak menandai
   ketiganya !important pada #tabel tbody td (beda dengan border-color di bawah, yang
   DIPAKSA !important lewat aturan terpisah `#tabel, #tabel th, #tabel td`), jadi
   spesifisitas ekstra dari ID #page1 di sini sudah cukup menang tanpa !important. Kalau
   dipaksa !important, itu akan mengalahkan Bootstrap .text-success/.text-danger (yang
   !important) pada sel Oto - itulah yang membuat ikon centang/silang di kolom Oto
   tampak hitam alih-alih hijau/merah (bug yang sama sempat terjadi di pengajuandph/
   pengajuandphtunai, sudah diperbaiki di sana juga).
   ========================================================================== */

/* newmaster.css: `table tbody td { padding: 0 10px !important }` global.
   Sengaja TIDAK ditimpa lagi - baris dibuat rapat (0 10px) persis seperti memorialkoreksi,
   yang juga membiarkan aturan global itu menang. padding 13px 14px milik tableMaster2.css
   tidak !important, jadi ikut kalah. */
/* #page1 #tabel tbody td { padding: 12px 16px !important; } */

/* tableMaster2.css: skin khusus id #tabel (dipakai juga oleh halaman lain yang
   memuat file itu) - dikembalikan ke nilai .data-table di atas. */
#page1 #tabel thead th {
  background: #f9fafb !important;
  color: var(--text-muted, #6b7280) !important;
  font-size: 12px !important;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid var(--border, #e5e7eb) !important;
  border-top: none;
  white-space: nowrap; /* judul sebaris - kolom panjang digeser lewat scroll horizontal .po-table-wrap */
}
#page1 #tabel tbody td {
  font-size: 13px;
  color: var(--text-main, #1f2937);
  border-color: #f3f4f6 !important;
  border-left: none;
  border-right: none;
  white-space: nowrap; /* isi sebaris, sama seperti memorialkoreksi */
}
#page1 #tabel tbody tr:hover { background-color: transparent !important; }
#page1 #tabel td:last-child { font-weight: inherit !important; }

/* tableMaster2.css: chrome DataTables (panjang halaman/info/pagination) diwarnai ungu
   (--sp-primary) dan diberi padding tambahan - dikembalikan ke nilai bawaan
   jquery.dataTables.css 1.13.2 (dari public/css/jquery.dataTables.min.css, versi yang
   sama dimuat layout lewat CDN) supaya sama persis dengan memorialkoreksi, yang tidak
   memuat tableMaster2.css sama sekali. */
#page1 .dataTables_wrapper { padding: 0; }
#page1 .dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 2px !important;
  margin-left: 2px;
  border: 1px solid transparent !important;
  color: #333 !important;
}
#page1 .dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: #fff !important;
  border-color: #979797 !important;
  color: #333 !important;
}
#page1 .dataTables_wrapper .dataTables_info {
  color: inherit;
  font-size: inherit;
  padding-top: 0.755em !important;
}
</style>

{{-- Reopened below: kembali ke aturan .tb-report/#tabel_xxx_filter asli halaman ini (blok
     "search bar 1"), yang sebelumnya satu <style> saja dengan blok po-* di atas sebelum
     dipisah supaya jelas mana yang baru vs mana yang lama. --}}
<style>

#tabel_add_list_customer_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}

#tabel_add_list_customer_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_noinvoice_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}

#tabel_add_list_bon_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_bon_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_noinvoice_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_barang_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_barang_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_nobeli_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_nobeli_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }


#tabel_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

/* Picker gaya baru: baris tabel langsung diklik, tanpa kolom Actions/tombol +, sesuai
   docs/new-cust-supp-modal-guide.md. Discope ke #form (semua pane modalAddList*) dan hanya
   mengenai baris ber-class .pick-row, sehingga tiga tabel non-picker di modal ini
   (invoice/dphuhtbbm/tunai) tidak ikut berubah. */
#form table tbody tr.pick-row {
  cursor: pointer;
  transition: background .12s;
}

#form table tbody tr.pick-row:hover td {
  background: #F8F9FF;
}
</style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
<style>
#tabel2_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel2_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel2_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel2_filter input:focus {
    border-color: #007bff;
    outline: none;
  }

  #tabel_add_list_lawan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_lawan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

    #tabel_add_list_akumulasibiaya_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_akumulasibiaya_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

/* Pelunasan Hutang (#formTunai, _tunai-modal.blade.php): kolom Action, tombol +/hapus dan
   warna baris pelunasan disalin dari kartu hutang Memorial Koreksi (.btn-mk-hapus,
   .btn-mk-lunas, .mk-baris-lunas di memorialkoreksi.blade.php). Baris dibuat oleh
   tunaiRowHtml() di public/js/bank.js. */
#tabel_add_list_tunai th:first-child,
#tabel_add_list_tunai td.kolom-tunai-action {
  width: 52px;
  white-space: nowrap;
  text-align: center;
}
.btn-tunai-hapus {
  padding: 1px 6px;
  font-size: .72rem;
  line-height: 1.3;
  border-radius: 5px !important;
}
.btn-tunai-lunas {
  background-color: #eaf1ff;
  border: 1px solid #c7dbff;
  color: #1d4ed8;
  padding: 1px 6px;
  font-size: .72rem;
  line-height: 1.3;
  border-radius: 5px !important;
  box-shadow: none;
}
.btn-tunai-lunas:hover {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}
.btn-tunai-lunas:disabled {
  opacity: .4;
}
#tabel_add_list_tunai tr.tunai-baris-lunas td {
  color: #dc3545;
  background-color: #fffbea;
}
#tabel_add_list_tunai tr.tunai-bisa-dobel { cursor: pointer; }
</style>
@endsection


@section('content')
@include('accounting.bank._list')
@include('accounting.bank._form')
@include('accounting.bank._detail')
@include('accounting.bank._pickers')
@include('accounting.bank._tunai-modal')
@endsection

@section('js')
<script>
  window.BANK_ROUTES = {
    bankdetailCetak: "{!! url('bankdetailCetak') !!}",
    bankgetnourutaktiva: "{!! url('bankgetnourutaktiva') !!}",
    banklistaktiva: "{!! url('banklistaktiva') !!}",
    banklistakumulasi: "{!! url('banklistakumulasi') !!}",
    banklistakumulasiinput: "{!! url('banklistakumulasiinput') !!}",
    banklistbiayainput: "{!! url('banklistbiayainput') !!}",
    banklistcosting: "{!! url('banklistcosting') !!}",
    banklistcustsupp: "{!! url('banklistcustsupp') !!}",
    banklistcustsuppumb: "{!! url('banklistcustsuppumb') !!}",
    banklistdepartemen: "{!! url('banklistdepartemen') !!}",
    banklistdevisi: "{!! url('banklistdevisi') !!}",
    banklistdph: "{!! url('banklistdph') !!}",
    banklistdphuht: "{!! url('banklistdphuht') !!}",
    banklistdpp: "{!! url('banklistdpp') !!}",
    banklistkasheader: "{!! url('banklistkasheader') !!}",
    banklistlawan: "{!! url('banklistlawan') !!}",
    banklistsubcosting: "{!! url('banklistsubcosting') !!}",
    banklisttunai: "{!! url('banklisttunai') !!}",
    banklisttunaix: "{!! url('banklisttunaix') !!}",
    banklistvalas: "{!! url('banklistvalas') !!}",
    bankloadall: "{!! url('bankloadall') !!}",
    bankprosesumb: "{!! url('bankprosesumb') !!}",
    bankspadd: "{!! url('bankspadd') !!}",
    bankspadddppdph: "{!! url('bankspadddppdph') !!}",
    bankspaddnewaktiva: "{!! url('bankspaddnewaktiva') !!}",
    bankspaddtemprumjual: "{!! url('bankspaddtemprumjual') !!}",
    bankspbatalotorisasi: "{!! url('bankspbatalotorisasi') !!}",
    bankspdeletetemprumjual: "{!! url('bankspdeletetemprumjual') !!}",
    bankspdetail: "{!! url('bankspdetail') !!}",
    bankspotorisasi: "{!! url('bankspotorisasi') !!}",
    bankspupdatetemprumjual: "{!! url('bankspupdatetemprumjual') !!}",
    globalfunctions_doLoadHeader: "{!! url('globalfunctions_doLoadHeader') !!}",
    globalfunctions_doSimpanHeader: "{!! url('globalfunctions_doSimpanHeader') !!}",
    kaslistbon: "{!! url('kaslistbon') !!}",
    kaslistcustsupptunai: "{!! url('kaslistcustsupptunai') !!}",
    kassptemphutpiut: "{!! url('kassptemphutpiut') !!}",
    kreditnotelistinvoice: "{!! url('kreditnotelistinvoice') !!}",
    kreditnotespadd: "{!! url('kreditnotespadd') !!}",
    kreditnotespdetail: "{!! url('kreditnotespdetail') !!}",
    perintahreturjuallistnobeli: "{!! url('perintahreturjuallistnobeli') !!}",
    perintahreturjuallistnoinvoice: "{!! url('perintahreturjuallistnoinvoice') !!}",
    spnobuktisimbol: "{!! url('spnobuktisimbol') !!}",
  };
  window.BANK_INITIAL_ROWS = @json($tempOutstanding);
</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/bank.js') !!}?v={{ @filemtime(base_path('public/js/bank.js')) ?: '1' }}"></script>
@endsection
