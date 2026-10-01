@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Surat Jalan')
@section('title', 'SML - Surat Jalan')

@endsection

@section('css')

{{-- report-table.css/report-table.js + public/js/headerEngine.js power #tabel/#tabel2/
     #tabel6's draggable-column/gear-menu headers and "Tampilkan"
     (page-length) control, same pattern as so.blade.php's #tabel/#tabel7. Linked here
     page-locally (not in newmaster.blade.php) so only this page gets it. #tabel4 ("Out SO
     Booking") is left on the old static header -- its nav-tab link is already commented
     out elsewhere in this file, so it's unreachable in the UI. #tabel3 ("Surat Jalan Belum
     Otorisasi", no actions) was merged into #tabel6 ("Surat Jalan Otorisasi") with a
     Semua/Belum/Sudah Otorisasi filter, port 1:1 dari pola PRJ/RPG. --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

{{-- Page1 rebuilt 1:1 against so.blade.php's own page1 (itself rebuilt from
     purchaseOrder.blade.php): same card-wrapped pill tab bar, shared toolbar
     (search + Tampilkan) in its own card, po-table-wrap sticky-header scroll box, and
     Reset-kolom bar -- copied verbatim, rescoped from SO's #tabel/#tabel7 to this
     page's three live tables (#tabel/#tabel2/#tabel6). Data/business
     logic (loadAll, HeaderEngine persistence, tabelXActionsCell) is untouched -- this
     only changes how the page is built and how it looks. --}}
<link rel="stylesheet" href="{{ asset('css/sj-table-header.css') }}?v={{ filemtime(public_path('css/sj-table-header.css')) }}">

<style>
  .custom-tabs {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    gap: 2px;
    background-color: #f1f3f5;
    border-radius: 20px;
    padding: 3px;
  }

  .custom-tabs .nav-link {
    display: inline-block !important;
    padding: 5px 16px !important;
    font-size: 0.75rem !important;
    border: none;
    border-radius: 17px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  }

  .custom-tabs .nav-link:hover {
    background: transparent;
    color: #007bff;
  }

  .custom-tabs .nav-link.active {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }

  .tab-card .card-body {
    padding: 5px 10px !important;
  }

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
    border-color: var(--border) !important;
  }

  .po-len-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--rt-card);
    border: 1.5px solid var(--rt-border);
    border-radius: 8px;
    padding: 5px 12px;
  }

  .po-len-wrap label {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .po-len-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink);
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right center;
  }
</style>

<style>
  .tb-pagination-outside {
    overflow: hidden;
    margin-top: 8px;
    padding: 0 14px 10px;
  }
  .tb-pagination-outside .dataTables_paginate {
    float: right;
  }
  .tb-pagination-outside .dataTables_info {
    float: left;
    padding-top: 0.5em;
    font-size: 13px;
    color: var(--sp-text-soft, #6b7280);
  }
  .tb-pagination-outside .paginate_button.disabled.ellipsis {
    border: none;
    background: transparent;
    padding: 0.4em 0.3em;
  }
  .tb-pagination-outside .paginate_button {
    box-sizing: border-box;
    display: inline-block;
    padding: 0.4em 0.9em;
    margin-left: 4px;
    border-radius: 6px;
    border: 1px solid var(--sp-border, #e7e9ee);
    color: var(--sp-text, #1f2430);
    text-decoration: none !important;
    cursor: pointer;
  }
  .tb-pagination-outside .paginate_button.current {
    background: #0d9488;
    border-color: #0d9488;
    color: #fff;
  }
  .tb-pagination-outside .paginate_button.disabled {
    cursor: default;
    color: var(--sp-text-soft, #6b7280);
    opacity: .6;
  }
  .tb-pagination-outside .paginate_button:hover:not(.disabled):not(.current) {
    background: var(--sp-bg, #f4f5f7);
  }

  /* "Surat Jalan Otorisasi" -- data selalu satu baris, tidak boleh terbungkus
     ke baris baru (beda dari kolom header yang sudah nowrap sejak awal). */
  #tabel6 tbody td {
    white-space: nowrap;
  }

  .tb-report .toolbar {
    margin-bottom: 10px;
  }
  .rt-bar-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 10px;
  }
  .rt-bar-row .rt-bar { margin-bottom: 0; }
  .rt-reset-btn {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 12px;
    border: 1px solid var(--rt-border);
    border-radius: var(--rt-radius);
    background: var(--rt-card);
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    line-height: 1.2;
    cursor: pointer;
    white-space: nowrap;
    color: var(--rt-ink-soft);
  }
  .rt-reset-btn:hover {
    color: #D64550;
    border-color: #D64550;
    background: #FEF2F2;
  }
  /* Pager server-side milik tabel/tabel2 (SO Belum Siap Kirim/SO Siap
     Kirim) -- keduanya tidak lagi dipaginate DataTables di
     browser (satu halaman per request lewat suratjalanpaginate), tapi tetap
     dibikin pakai markup+class .paginate_button/.dataTables_paginate yang
     sama dengan pager DataTables asli (lihat .tb-pagination-outside di atas)
     supaya tampilannya identik dengan tabel6 (yang masih pager DataTables
     asli, periode-scoped). */
</style>

  <style>
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
  </style>

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
  </style>

  <style>
  #tabel3_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel3_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabel3_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabel3_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
  </style>


<style>
#tabel6_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel6_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel6_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel6_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>

<style>
#tabel4_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel4_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel4_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel4_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>


<style>
  #tabel_add_list_pelanggan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_pelanggan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>
  #tabel_add_list_sales_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_sales_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>
  #tabel_add_list_ekspedisi_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_ekspedisi_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>
  /* Search-icon button appended flush to an input, ported from so.blade.php. */
  .btn-icon-search {
    height: 32px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-chip-biru {
    background-color: #e8edff;
    border-color: #cfdcff;
    color: #2563eb;
  }

  .btn-chip-biru:hover,
  .btn-chip-biru:focus {
    background-color: #dce6ff;
    border-color: #b9c9ff;
    color: #1d4ed8;
  }

  .btn-chip-biru:active {
    background-color: #cfdcff !important;
    border-color: #a8bdff !important;
    color: #1d4ed8 !important;
  }
</style>

<style>
  #input_search_barang_all {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
    display: flex;
    align-items: flex-end;
    margin-left: 95px;
  }
  .search-label {
  font-weight: bold;
  font-size: 0.75rem;
  margin-right: 155px;
  margin-top : -45px;
  display: inline-block;
  vertical-align: middle;
  }

  /* Hide action buttons until the row is hovered */
  #tabel6 tbody .action-buttons-wrap {
    opacity: 0;
    visibility: hidden;
    transform: translateX(-6px);
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
  }
  /* Show them when hovering the table row */
  #tabel6 tbody tr:hover .action-buttons-wrap,
  #tabel6 tbody tr:focus-within .action-buttons-wrap {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
  }

  /* Pastel round-button treatment for the Otorisasi tab's action buttons, copied
     verbatim from so.blade.php/penawaranso.blade.php's own #tabelN td:first-child .btn-*
     pattern -- same palette, just scoped to #tabel6's .action-buttons-wrap instead. */
  #tabel6 .action-buttons-wrap .btn {
    width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
    justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
    box-shadow: none; transition: all .12s ease;
  }
  #tabel6 .action-buttons-wrap .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
  #tabel6 .action-buttons-wrap .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel6 .action-buttons-wrap .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel6 .action-buttons-wrap .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel6 .action-buttons-wrap .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }

  /* Same pastel round-button treatment for #tabel2's ("SO Siap Kirim") own "+"
     action, copied 1:1 from returpenjualangudang.blade.php's #tabel/#tabel2/#tabel3
     td:first-child .btn-primary pastel-blue pattern + its .btn .bi-plus rule
     (bigger glyph, line-height:0 so it doesn't grow the button's height). */
  #tabel2 .action-buttons-wrap .btn {
    width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
    justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
    box-shadow: none; transition: all .12s ease;
  }
  #tabel2 .action-buttons-wrap .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
  #tabel2 .action-buttons-wrap .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel2 .action-buttons-wrap .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }

  /* Modal pickers converted to click-anywhere-on-row (per marketing-full-menu-guide.md),
     same convention as so.blade.php's own picker modals. */
  .pick-row { cursor: pointer; }
  .pick-row:hover { background-color: #f5f3ff !important; }

  /* Pastel round-button treatment for #koreksiTable's own Edit/Delete actions
     (last column, not wrapped in .action-buttons-wrap like the other tables). */
  #koreksiTable td:last-child .btn {
    width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
    justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
    box-shadow: none; transition: all .12s ease;
  }
  #koreksiTable td:last-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
  #koreksiTable td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #koreksiTable td:last-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

  .data-table th,
  .data-table td {
    white-space: nowrap;
  }
</style>

@endsection

@section('content')
<div id="page1" class="container-fluid">

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="container-fluid" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  {{-- Tab bar: so.blade.php's own card.tab-card + custom-tabs pattern, copied 1:1.
       Same five tabs/targets as before (data-toggle, not so.blade.php's data-bs-toggle --
       this page's tabs still run on the Bootstrap 4 tab plugin, matching the existing
       markup's own convention; changing it isn't part of this restyle). The commented-out
       "Out SO Booking" tab stays commented -- unreachable before this rebuild, unreachable
       after it. --}} 
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">

        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          SO Siap Kirim
        </a>

        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          SO Belum Siap Kirim
        </a>

        <a class="nav-item nav-link" id="nav-profile4-tab" data-toggle="tab" href="#profile4" role="tab" aria-controls="nav-profile4" aria-selected="false">
          Surat Jalan Otorisasi
        </a>

        <!--   <a class="nav-item nav-link" id="nav-profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="nav-profile2" aria-selected="false">
          Out SO Booking
        </a> -->

      </div>
    </div>
  </div>

  {{-- Setiap tab sekarang punya toolbar (search+Tampilkan) sendiri-sendiri, tidak lagi
       berbagi satu toolbar seperti sebelumnya (dulu satu #tabel_filter_visual/
       #tabel_length_visual menyaring KEEMPAT tabel sekaligus lewat page1Tables).
       "SO Belum Siap Kirim"/"SO Siap Kirim" (tabel/tabel2)
       juga sekarang TIDAK dibatasi periode sama sekali (dulu ikut ke-filter rentang
       tanggal yang harusnya cuma milik "Surat Jalan Otorisasi") dan datanya diambil
       per-halaman dari server (server-side paging, lihat sjLoadPage() / endpoint
       suratjalanpaginate) supaya tidak berat saat datanya banyak -- bukan lagi
       "ambil semua lalu paginate di browser" seperti tabel6. Periode + Filter status
       cuma relevan buat "Surat Jalan Otorisasi" (tabel6), jadi sekarang jadi bagian
       toolbar tab itu sendiri saja, bukan dibagikan ke tab lain. --}}
  <div class="card">
    <div class="card-body" style="padding:0;">
  <div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      {{-- Filter modal: sama persis polanya dengan modalFilterSPB milik tabel6,
           cuma isinya "Status Prioritas" (Urgent/Not Urgent) bukan status
           otorisasi. --}}
      <div class="modal fade rt-filter" id="modalFilterTabel1">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bi bi-funnel"></i>
                Filter Data
                <span class="rt-active-badge" id="tabel1FilterBadge">0 aktif</span>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterTabel1').modal('hide')">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">
              <div class="rt-section">
                <div class="rt-group-label">Status</div>
                <div>
                  <label class="rt-field-label" for="input_filterprioritas1">Status Prioritas</label>
                  <select class="rt-native" id="input_filterprioritas1">
                    <option value="" selected>Semua</option>
                    <option value="1">Urgent</option>
                    <option value="0">Not Urgent</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="rt-reset-link" onclick="sjResetFilterFieldsTabel('tabel')">Reset semua</button>
              <div class="rt-footer-buttons">
                <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                  onclick="$('#modalFilterTabel1').modal('hide')">Batal</button>
                <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterTabel('tabel'); $('#modalFilterTabel1').modal('hide');">Terapkan</button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="po-toolbar">
        <input type="search" id="tabelSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="tabelLen1">Tampilkan</label>
          <select id="tabelLen1" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <button class="po-btn-filter" id="sjFilterBtn1" type="button" onclick="$('#modalFilterTabel1').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>
      <div class="rt-bar-row">
        <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel')">
          <i class="bi bi-arrow-clockwise"></i> Reset kolom
        </button>
        <div id="rtBarTabel"></div>
      </div>
      <div class="po-table-wrap">
        <table id="tabel" class="tb data-table">

          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel_data" class="text-left"></tbody>
        </table>
      </div>
      <div class="tb-pagination-outside">
        <div class="dataTables_info" id="tabelPagerInfo1"></div>
        <div class="dataTables_paginate paging_simple_numbers">
          <a class="paginate_button previous" id="tabelPagerPrev1" onclick="sjGotoPage('tabel', sjPageState.tabel.page - 1)">Previous</a>
          <span id="tabelPagerNumbers1"></span>
          <a class="paginate_button next" id="tabelPagerNext1" onclick="sjGotoPage('tabel', sjPageState.tabel.page + 1)">Next</a>
        </div>
      </div>
      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
  </div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="modal fade rt-filter" id="modalFilterTabel2">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bi bi-funnel"></i>
                Filter Data
                <span class="rt-active-badge" id="tabel2FilterBadge">0 aktif</span>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterTabel2').modal('hide')">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">
              <div class="rt-section">
                <div class="rt-group-label">Status</div>
                <div>
                  <label class="rt-field-label" for="input_filterprioritas2">Status Prioritas</label>
                  <select class="rt-native" id="input_filterprioritas2">
                    <option value="" selected>Semua</option>
                    <option value="1">Urgent</option>
                    <option value="0">Not Urgent</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="rt-reset-link" onclick="sjResetFilterFieldsTabel('tabel2')">Reset semua</button>
              <div class="rt-footer-buttons">
                <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                  onclick="$('#modalFilterTabel2').modal('hide')">Batal</button>
                <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterTabel('tabel2'); $('#modalFilterTabel2').modal('hide');">Terapkan</button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="po-toolbar">
        <input type="search" id="tabelSearch2" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="tabelLen2">Tampilkan</label>
          <select id="tabelLen2" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <button class="po-btn-filter" id="sjFilterBtn2" type="button" onclick="$('#modalFilterTabel2').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>
      <div class="rt-bar-row">
        <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel2')">
          <i class="bi bi-arrow-clockwise"></i> Reset kolom
        </button>
        <div id="rtBarTabel2"></div>
      </div>
      <div class="po-table-wrap">
        <table id="tabel2" class="tb data-table">
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel2_data" class="text-left"></tbody>
        </table>
      </div>
      <div class="tb-pagination-outside">
        <div class="dataTables_info" id="tabelPagerInfo2"></div>
        <div class="dataTables_paginate paging_simple_numbers">
          <a class="paginate_button previous" id="tabelPagerPrev2" onclick="sjGotoPage('tabel2', sjPageState.tabel2.page - 1)">Previous</a>
          <span id="tabelPagerNumbers2"></span>
          <a class="paginate_button next" id="tabelPagerNext2" onclick="sjGotoPage('tabel2', sjPageState.tabel2.page + 1)">Next</a>
        </div>
      </div>
      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
  </div>
  <div class="tab-pane fade" id="profile4" role="tabpanel" aria-labelledby="profile-tab">
      {{-- Filter modal: port 1:1 dari modalFilter milik perintahreturjual.blade.php.
           tabel3 (Belum Otorisasi, dulu tanpa actions) + tabel6 (Sudah Otorisasi,
           actions Kirim/Terima Acc) digabung jadi satu tabel di sini dengan Status
           dropdown, sama seperti PRJ/RPG. --}}
      <div class="modal fade rt-filter" id="modalFilterSPB">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bi bi-funnel"></i>
                Filter Data
                <span class="rt-active-badge" id="spbFilterBadge">0 aktif</span>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterSPB').modal('hide')">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">
              <div class="rt-section">
                <div class="rt-group-label">Status</div>
                <div>
                  <label class="rt-field-label" for="input_filterspb">Status Otorisasi</label>
                  <select class="rt-native" id="input_filterspb">
                    <option value=0 selected>Semua</option>
                    <option value=1>Belum Otorisasi</option>
                    <option value=2>Sudah Otorisasi</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="rt-reset-link" onclick="sjResetFilterFieldsSPB()">Reset semua</button>
              <div class="rt-footer-buttons">
                <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                  onclick="$('#modalFilterSPB').modal('hide')">Batal</button>
                <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterSPB(); $('#modalFilterSPB').modal('hide');">Terapkan</button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="onChangePeriodeSPB()" class="po-filter-inp" id="input_tanggalawal_spb" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodeSPB()" class="po-filter-inp" id="input_tanggalakhir_spb" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="tabelSearch6" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="tabelLen6">Tampilkan</label>
          <select id="tabelLen6" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" id="sjFilterBtn" type="button" onclick="$('#modalFilterSPB').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>

      <div class="rt-bar-row">
        <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable('tabel6')">
          <i class="bi bi-arrow-clockwise"></i> Reset kolom
        </button>
        <div id="rtBarTabel6"></div>
      </div>
        <table id="tabel6" class="tb data-table">
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel6_data" class="text-left"></tbody>
        </table>
      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
  </div>
  <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab">
    <div class="po-table-wrap">

              <table id="tabel4" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                    <th style="padding: 4px 12px;" scope="col">Ref PR</th>
                    <th style="padding: 4px 12px;" scope="col">NoPesanan</th>
                    <th style="padding: 4px 12px;" scope="col">Qnt</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">SaldoQnt</th>
                    <th style="padding: 4px 12px;" scope="col">Catatan</th>
                    <th style="padding: 4px 12px;" scope="col">DueDate</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Kebun</th>
                    <th style="padding: 4px 12px;" scope="col">PartNumber</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Merk</th>
                    <th style="padding: 4px 12px;" scope="col">UserID</th>

                  </tr>
                </thead>


                <tbody id="tabel4_data" class="text-left" >
                    @for ($i = 0; $i < count($tempOutstanding4); $i++)
                      <tr>
                        <td>{{ $tempOutstanding4[$i]->NOBUKTI }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding4[$i]->Tanggal)) !!}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaCustSupp }}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaBrg }}</td>
                        <td>{{ $tempOutstanding4[$i]->RefPR }}</td>
                        <td>{{ $tempOutstanding4[$i]->Nopesanan }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->Qnt, 2 ,'.' , '') }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->QntOut, 2 ,'.' , '') }}</td>
                        <td>{{ $tempOutstanding4[$i]->SATUAN }}</td>
                        <td class="text-right">{{ number_format($tempOutstanding4[$i]->SaldoQnt, 2 ,'.' , '') }}</td>
                        <td>{{ $tempOutstanding4[$i]->catatan }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding4[$i]->DUEDATE)) !!}</td>
                        <td>{{ $tempOutstanding4[$i]->namakebun }}</td>
                        <td>{{ $tempOutstanding4[$i]->PartNumber }}</td>
                        <td>{{ $tempOutstanding4[$i]->NamaMerk }}</td>
                        <td>{{ $tempOutstanding4[$i]->UserID }}</td>

                      </tr>
                    @endfor
                </tbody>
              </table>
    </div>
  </div>
</div>
</div>
</div>


</div>
</div>


<div id="page2" class="container-fluid" style="display: none">

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>
    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Out" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_noso" placeholder="No SO" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
                </div>
              </div>
            </div>
          </div>




        </div>

        <hr/ style="margin-top: -5px">
        <div class="row">
          <div class="col-md-3">
            <div class="row">

          <div class="col-md-4">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>
          <!-- <div class="col-8 text-right">
            <div class="form-group input-group">

          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group">
              {{-- Native <select>, sama seperti Gudang milik purchasing/newpo.blade.php
                   (id="input_add_gudang" -> @foreach ($gudang as $g)) -- id tetap
                   input_add_kodegdg supaya submitAdd()/dst yang sudah baca field ini
                   tidak perlu diubah; value select LANGSUNG kode gudangnya, jadi
                   input_add_gdg (nama tampilan terpisah) dan modal picker
                   buttonAddListGudang()/buttonAddPickGudang() sudah tidak dipakai lagi. --}}
              <select class="form-control" id="input_add_kodegdg">
                <option value="">- Pilih Gudang -</option>
                @foreach ($listGudang as $g)
                  <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} - {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top: -10px">
          <div class="col-md-4">
            <div class="form-group">
              <label>Ekspedisi</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">

          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="hidden" class="form-control" id="input_add_kodeekspedisi" placeholder="" >
              <input type="text" class="form-control" id="input_add_ekspedisi" placeholder=""  disabled>
              <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListEkspedisi" onclick="buttonAddListEkspedisi()"><i class="bi bi-search"></i></button>
            </div>
          </div>
        </div>

        <div class="row" style="margin-top: -10px">
          <div class="col-md-4">
            <div class="form-group">
              <label>No Pol Kend.</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_nopol" placeholder="" required >
            </div>
          </div>
        </div>



          <div class="row" style="margin-top: -10px">

          <div class="col-md-4">
            <div class="form-group">
              <label>Sopir</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_sopir" placeholder="" required >
            </div>
          </div>

        </div>

      </div>


      <div class="col-md-3">
        <div class="row">

      <div class="col-md-4">
        <div class="form-group">
          <label>Customer</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_customer" placeholder="" required disabled>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: -10px">
      <div class="col-md-4">
        <div class="form-group">
          <label>Lok. Penerima</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_lokasipenerima" placeholder="" required disabled>
        </div>
      </div>
    </div>



      <div class="row" style="margin-top: -10px">


      <div class="col-md-4">
        <div class="form-group">
          <label>Ref UKM</label>
        </div>
      </div>
      <div class="col-md-8">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_refukm" placeholder="" required >
        </div>
      </div>

    </div>

  </div>

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
          <label>Alamat Kirim</label>
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group">
          <textarea type="text" style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
          <!-- <input type="text" class="form-control" id="input_add_alamatkirim" placeholder="" required disabled> -->
        </div>
      </div>

    </div>
      <div class="row" style="margin-top: -10px">


      <div class="col-md-2">
        <div class="form-group">
          <label>Catatan SO</label>
        </div>
      </div>
      <div class="col-md-10">
        <div class="form-group">
          <input type="text" class="form-control" id="input_add_catatanso" placeholder="" required >
        </div>
      </div>
    </div>

  </div>

      </div>

      <div class="row">


      </div>
      </div>



    </div>
      <div class="container-fluid" style="overflow-x: auto;">
        <hr/>

            <table id="addTable" class="data-table" >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kirim</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty OS</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Sisa</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Book</th>
                  <th style="padding: 4px 12px;" scope="col">Qty Kirim</th>

                </tr>
              </thead>


              <tbody id="addTableData" class="text-right" >
                <tr >

                    <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>

              </tr>

              </tbody>


            </table>
      </div>
    </div>
    <!-- <div class="container fluid"> -->
      <div class="row">

        <div class="col-12">
          <!-- <hr/> -->

          <div class="container-fluid">
            <hr/>
            <div class="row">
              <div id="" class="text-right col-12">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
                <button type="button" id="submitAddAdd" class="btn btn-chip-biru btn-lg" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="submitAdd()">Simpan</button>

              </div>

            </div>

          </div>
        </div>

      </div>

    <!-- </div> -->




</div>

<!-- page 2 end -->

<div id="page3" class="container-fluid" style="display: none">
  <div class="row">
    <div class="col-6 text-left">
      <h1 style=""></h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>



  <div id="" class="">
  <div class="modal-body">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" />
      <div class="row">

        <div class="col-md-3">
          <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>No Bukti</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No Out" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noso" placeholder="No SO" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>
        </div>




      </div>

      <hr/ style="margin-top:-10px">
      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Ekspedisi</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_koreksi_kodeekspedisi" placeholder="" >
                <input type="text" class="form-control" id="input_koreksi_ekspedisi" placeholder=""  disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Pol Kend.</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nopol" placeholder="" required disabled>
              </div>
            </div>


          </div>
            <div class="row" style="margin-top: -10px">


            <div class="col-md-4">
              <div class="form-group">
                <label>Sopir</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_sopir" placeholder="" required disabled>
              </div>
            </div>







          </div>

          <!-- <div class="row">

        <div class="col-md-4" style="display: none">
          <div class="form-group">
            <label>Gudang</label>
          </div>
        </div>

      </div> -->
        <!-- <div class="col-md-8" style="display: none">
          <div class="form-group"> -->
            <input type="hidden" class="form-control" id="input_koreksi_kodegdg" placeholder="" >
            <input type="hidden" class="form-control" id="input_koreksi_gdg" placeholder="" disabled>
          <!-- </div>
        </div> -->


      </div>

      <div class="col-md-3">
        <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_customer" placeholder="" required disabled>
            </div>
          </div>

        </div>
          <div class="row" style="margin-top: -10px">



          <div class="col-md-4">
            <div class="form-group">
              <label>Lok. Penerima</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_lokasipenerima" placeholder="" required disabled>
            </div>
          </div>


        </div>

          <div class="row" style="margin-top: -10px">



          <div class="col-md-4">
            <div class="form-group">
              <label>Ref UKM</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_refukm" placeholder="" required disabled >
            </div>
          </div>


        </div>



      </div>

      <div class="col-md-6">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label>Alamat Kirim</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_koreksi_alamatkirim"  disabled></textarea>
              <!-- <input type="text" class="form-control" id="input_koreksi_alamatkirim" placeholder="" required disabled> -->
            </div>
          </div>

        </div>
          <div class="row" style="margin-top: -10px">




          <div class="col-md-2">
            <div class="form-group">
              <label>Catatan SO</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_catatanso" placeholder="" required disabled>
            </div>
          </div>





        </div>

      </div>







        <!-- <div class="col-2">
          <div class="form-group">
            <label>Nama Cust</label>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
          </div>
        </div> -->


      </div>

    </div>


    <div class="col-8">
      <div class="row">







    <!-- <div class="col-2">
      <div class="form-group">
        <label>Nama Cust</label>
      </div>
    </div>
    <div class="col-4">
      <div class="form-group">
        <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
      </div>
    </div> -->


  </div>

</div>

    </div>

    <div class="row">


    </div>
    </div>







  <div class="container-fluid mt-4" style="overflow-x: auto; ">
    <div class="mt-4">

      <hr/ style="margin-top: -20px">
    </div>

        <table id="koreksiTable" class="data-table" >
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Gudang</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="koreksiTableData" class="text-right" >
            <tr >

                <!-- <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td> -->
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="container-fluid">
    <div class="row">
      {{-- id dipakai buttonDetail() buat menyembunyikan tombol Tambah -- halaman ini
           dipakai bersama oleh Edit (buttonKoreksiSPB) dan Detail (read-only). --}}
      <div class="col-12  text-right" id="koreksiTambahWrap">
        <button type="button" class="btn btn-chip-biru btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonKoreksiAdd()" class="btn btn-secondary"><b>Tambah</b></button>
        <!-- <button type="button" onclick="buttonKoreksiAdd()" class="btn btn-primary">+ Tambah Item</button> -->
      </div>

    </div>

    <!-- koreksi add -->
    <div id="formAddAdd" class="container-fluid showhide">
      <!-- <div class="line"></div> -->
      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Add Item</h4>
        </div>
      </div>
      <div class="row">

        <div class="col-md-3">



          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Kode Barang</label>
              </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">
                <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
                <button type="button" onclick="buttonKoreksiListBarang()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>

              </div>

            </div>

          </div>

        </div>

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Nama Barang</label>
              </div>
            </div>
            <div class="col-md-6">
              <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
            </div>
          </div>
        </div>
        <div class="col-md-3">

        </div>

      </div>
        <div class="row" style="margin-top: -10px">

        <div class="col-md-3 ">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <!-- <div class="col-4 text-right">

            </div> -->
            <div class="col-md-8">
              <div class="form-group">
                {{-- Native <select>, sama seperti Gudang header (input_add_kodegdg)
                     dan purchasing/newpo.blade.php -- AddAddNamaGdg (nama tampilan
                     terpisah) dan modal picker buttonKoreksiListGudang()/
                     buttonKoreksiPickGudang() sudah tidak dipakai lagi; nama gudang
                     dibaca dari data-nama option terpilih (lihat submitAddAdd()). --}}
                <select class="form-control" id="AddAddKodeGdg">
                  <option value="">- Pilih Gudang -</option>
                  @foreach ($listGudang as $g)
                    <option value="{{ $g->KODEGDG }}" data-nama="{{ $g->NAMA }}">{{ $g->KODEGDG }} - {{ $g->NAMA }}</option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Qty</label>
              </div>
            </div>
            <div class="col-4">
              <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
            </div>
            <div class="col-2">
              <input id="AddAddInputSatuan" type="text"  class="form-control  " disabled>
              <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
              <input id="AddAddInputSatuan1" type="hidden"  class="form-control  " disabled>
            </div>


          </div>
        </div>
      </div>


      <div class="row mt-2">
        <div class="col-md-12 text-right mt-4">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonBatalAdd()">Batal</button>

          <button type="button" id="buttonSubmitAddAdd" class="btn btn-chip-biru btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="submitAddAdd()">Simpan</button>

          <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
        </div>

      </div>
      <!-- <div class="line"></div> -->
      <hr/>
    </div>

    <div id="formAddEdit" class="container-fluid showhide">
      <!-- <div class="line"></div> -->
      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Edit Item</h4>
        </div>
      </div>
      <div class="row">

        <div class="col-3">



          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Kode Barang</label>
              </div>
            </div>
            <!-- <div class="col-4 text-right">

            <button type="button" onclick="buttonAddListBarang('edit')" class="btn btn-primary" >+</button>
          </div> -->
          <div class="col-8">
            <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
          </div>

        </div>

      </div>

      <div class="col-6">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Nama Barang</label>
            </div>
          </div>
          <div class="col-6">
            <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
          </div>
        </div>
      </div>
      <div class="col-3">

      </div>

    </div>
      <div class="row" style="margin-top: -10px">

      <div class="col-3 ">
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>
          <!-- <div class="col-8 text-right">

          </div> -->
          <div class="col-8">
            <div class="form-group">
              {{-- Native <select>, sama seperti pasangannya di Tambah Item (AddAddKodeGdg
                   di atas) -- AddEditNamaGdg dan modal picker sudah tidak dipakai lagi. --}}
              <select class="form-control" id="AddEditKodeGdg">
                <option value="">- Pilih Gudang -</option>
                @foreach ($listGudang as $g)
                  <option value="{{ $g->KODEGDG }}" data-nama="{{ $g->NAMA }}">{{ $g->KODEGDG }} - {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
          </div>

        </div>
      </div>
      <div class="col-6">
        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Qty</label>
            </div>
          </div>
          <div class="col-4">
            <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
          </div>
          <div class="col-2">
            <input id="AddEditInputSatuan" type="text"  class="form-control " disabled>
            <input id="AddEditInputSatuan1" type="hidden"  class="form-control " disabled>
          </div>


        </div>
      </div>
    </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">

        <button type="button" class="btn btn-danger btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonBatalAdd()" class="btn btn-secondary">Batal</button>


        <button type="button" id="buttomSubmitAddEdit" class="btn btn-chip-biru btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="submitAddEdit()">Simpan Edit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <hr/>
  </div>

</div>


  </div>


  <div id="page4" class="container-fluid" style="display: none">
    <div class="row" style="margin-top: -65px">
      <div class="col-6 text-left">
        <h1 style="">Form Detail Surat Jalan</h1>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>



    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Out" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="No SO" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>
          </div>




        </div>

        <hr/ style="margin-top:-10px">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Ekspedisi</label>
                </div>
              </div>
              <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div> -->
              <div class="col-md-8">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_detail_kodeekspedisi" placeholder="" >
                  <input type="text" class="form-control" id="input_detail_ekspedisi" placeholder=""  disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>No Pol Kend.</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nopol" placeholder="" required disabled>
                </div>
              </div>


            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Sopir</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_sopir" placeholder="" required disabled>
                </div>
              </div>







            </div>

            <!-- <div class="row">

          <div class="col-md-4" style="display: none">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>

        </div> -->
          <!-- <div class="col-md-8" style="display: none">
            <div class="form-group"> -->
              <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" >
              <input type="hidden" class="form-control" id="input_detail_gdg" placeholder="" disabled>
            <!-- </div>
          </div> -->


        </div>

        <div class="col-md-3">
          <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_customer" placeholder="" required disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <div class="col-md-4">
              <div class="form-group">
                <label>Lok. Penerima</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_lokasipenerima" placeholder="" required disabled>
              </div>
            </div>


          </div>

            <div class="row" style="margin-top: -10px">



            <div class="col-md-4">
              <div class="form-group">
                <label>Ref UKM</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_refukm" placeholder="" required disabled >
              </div>
            </div>


          </div>



        </div>

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Alamat Kirim</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
                <!-- <input type="text" class="form-control" id="input_detail_alamatkirim" placeholder="" required disabled> -->
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">




            <div class="col-md-2">
              <div class="form-group">
                <label>Catatan SO</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_catatanso" placeholder="" required disabled>
              </div>
            </div>





          </div>

        </div>







          <!-- <div class="col-2">
            <div class="form-group">
              <label>Nama Cust</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
            </div>
          </div> -->


        </div>

      </div>


      <div class="col-8">
        <div class="row">







      <!-- <div class="col-2">
        <div class="form-group">
          <label>Nama Cust</label>
        </div>
      </div>
      <div class="col-4">
        <div class="form-group">
          <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Gdg Tujuan" disabled>
        </div>
      </div> -->


    </div>

  </div>

      </div>

      <div class="row">


      </div>
      </div>







    <div class="container-fluid mt-4" style="overflow-x: auto; ">
      <div class="mt-4">

        <hr/ style="margin-top: -20px">
      </div>

          <table id="detailTable" class="table table-bordered table-hover table-striped table-responsive-lg" >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Qty2</th>
                <th style="padding: 4px 12px;" scope="col">Sat2</th>
                <th style="padding: 4px 12px;" scope="col">Gudang</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="text-right" >
              <tr >

                  <!-- <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td> -->
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>

            </tr>

            </tbody>


          </table>
    </div>





    </div>






  </div>




</div>



<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <div id="modalBodyAddListEkspedisi" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid mt-4" >
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto;">
          <!-- <div class="container-fluid"> -->


          <table id="tabel_add_list_ekspedisi" class="data-table">
            <thead class="text-center">
              <tr>

                <th style="padding: 4px 12px;" scope="col">Kode</th>
                <th style="padding: 4px 12px;" scope="col">Nama</th>

              </tr>
            </thead>


            <tbody id="tabel_data_add_list_ekspedisi" class="text-left" >

              <tr >

                <td>-</td>
                <td>-</td>
            </tr>
            </tbody>


          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>

  </div>

    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal detail -->
<div class="modal fade"  id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>




        <div id="modalBodyDetailMain" class="showhidemodalbodyadd">
          <div class="modal-body" >

        <div class="container-fluid">
          <div class="row">

            <input type="hidden" class="form-control" id="input_detail_nourut" >
            <div class="col-md-8">



              <div class="row">
                <div class="col-md-6">
                  <div class="row">


                <div class="col-9">
                  <div class="form-group">
                    <label>Pelanggan</label>
                  </div>
                </div>
                <div class="col-3 text-right">
                  <div class="form-group">
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-plus"></i></button> -->
                </div>

              </div>
              </div>
            </div>
            <div class="col-md-6">
            </div>


              <div class="col-md-6">
                <div class="row">


                <div class="col-md-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_kodepelanggan"  disabled>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_namapelanggan"  disabled>
                  </div>
                </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">


                <div class="col-md-12">
                  <div class="form-group">
                    <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatpelanggan"  disabled></textarea>
                  </div>
                </div>
                </div>
              </div>

              </div>
            </div>
            <div class="col-md-4">

              <div class="row">


              <div class="col-md-12">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nobukti" placeholder="" disabled>
                </div>
              </div>


              <div class="col-md-12">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>


            </div>















          </div>
          <!-- <hr/> -->
          <div class="row ">
            <div class="col-md-12 text-left">
            <button type="button" class="btn btn-primary" onclick="buttonDetailMainHeader()" class="btn btn-secondary"  >Header</button>
            <button type="button" class="btn btn-primary" onclick="buttonDetailMainItems()" class="btn btn-secondary"  >Items</button>
        </div>
      </div>
      <hr/>
      <div class="showhidemodalbodydetailmain" id="modalBodyDetailMainHeader">

      <div class="row">
        <div class="col-md-4">
          <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>


                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="hidden" class="form-control" id="input_detail_kodesales" >
                  <input type="text" class="form-control" id="input_detail_namasales"  disabled>
                </div>
              </div>

            </div>

          </div>

          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Alamat Kirim</label>
              </div>
            </div>

          <div class="col-md-12">
            <div class="form-group">
              <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
            </div>
          </div>
          </div>

          <div class="row">
            <div class="col-md-6">

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>



          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_valas"  disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>



            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_kurs"  disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-12">


          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Draft PO</label>
              </div>
            </div>



          <div class="col-md-6">
            <select disabled id="input_detail_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
          </div>
          </div>
          </div>



          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-6">
              <div class="row">


              <div class="col-12">
                <div class="form-group">
                  <label>PIC</label>
                </div>
              </div>

            </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodepic"  >
                    <input type="text" class="form-control" id="input_detail_namapic"  disabled>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">




          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Lokasi Penerima</label>
              </div>
            </div>

          <div class="col-md-12">
            <div class="form-group">
              <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
              <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  disabled></textarea>
            </div>
          </div>

          <div class="col-md-12">
          <div class="row">
            <div class="col-6">

              <div class="form-group">
                <label>No PO</label>
              </div>
            </div>





          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control" id="input_detail_nopo"  disabled>
            </div>
          </div>
          </div>
          </div>



          <div class="col-md-12">


          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tgl PO</label>
              </div>
            </div>


          <div class="col-md-6">
            <div class="form-group">
              <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
          </div>
          </div>
          <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Tgl Kirim</label>
              </div>
            </div>


          <div class="col-md-6">
            <div class="form-group">
              <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
          </div>
        </div>
          </div>









        </div>

        <div class="col-md-4">
          <div class="row">
            <div class="col-md-6">
              <div class="row">


              <div class="col-12">
                <div class="form-group">
                  <label>Back Office</label>
                </div>
              </div>

            </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">



                  <div class="col-12">


                  <div class="form-group">
                    <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
                    <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
                  </div>

                  </div>
                  </div>

                </div>
              </div>

            </div>





              <div class="col-md-12">
                <label>Keterangan</label>
              </div>



            <div class="col-md-12">

              <div class="form-group" style="margin-top: 14px">
                <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>


              </div>
            </div>



          </div>


          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>



          <div class="col-md-6">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_dp" value='0.00'  disabled>
            </div>
          </div>
          </div>

          <div class="row">



          <div class="col-md-12">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Pembayaran</label>
              </div>
            </div>



          <div class="col-md-6">
            <select  id="input_detail_pembayaran"  class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
              <option value=0 selected >Tunai</option>
              <option value=1  >Kredit</option>
            </select>
          </div>
          </div>
          </div>

          <div class="col-md-12">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TOP</label>
              </div>
            </div>



          <div class="col-md-6">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_hari"  value=0 min=0 disabled>
            </div>
          </div>
          </div>
          </div>

          <div class="col-md-12">

          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>



          <div class="col-md-6">
            <select  id="input_detail_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
              <option value=0 selected>None</option>
              <option value=1 >Exclude</option>
              <option value=2 >Include</option>
            </select>
          </div>
          </div>
          </div>









          </div>
















          <div class="row ">

      </div>

        </div>
        <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
        <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
    </div> -->


      </div>
      <hr/>
    </div>

    </div>
    <div id="modalBodyFooterList" class="modal-footer showhidemodalfooteradd">
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button>
    </div>
    <div class="showhidemodalbodydetailmain container-fluid" id="modalBodyDetailMainItems">
    <div class="row ">
      <div class="col-md-12 mt-2 text-right">

  </div>
</div>




        <div class="container-fluid mt-4" style="overflow:auto;">
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>

                  <th scope="col">Sat</th>
                  <th scope="col">Harga</th>
                  <th scope="col">Diskon</th>
                  <th scope="col">NDPP</th>


                </tr>
              </thead>


              <tbody id="tabel_data_detail" class="text-left" >

                <tr >

                  <td></td>
                  <td></td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>



    </div>

    </div>
    </div>
      <hr/ style="margin-top: -20px">
    <div class="container-fluid" style="margin-top: -10px;">


          <div class="row">


        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Disc %</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_disc" onchange="onChangeInputAddDisc()" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>DiscRp</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="number" class="form-control text-right" id="input_detail_discrp" onchange="onChangeInputAddDiscRp()" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>DPP</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>
        <div class="col" style="width:20%">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Grand Total</label>
              </div>
            </div>


          <div class="col-12" style="margin-top: -20px">
            <div class="form-group">
              <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
            </div>
          </div>
          </div>
        </div>

      </div>

      <div class="row justify-content-end">




  </div>



    </div>


  </div>


</div>
</div>
</div>
<!-- End modal detail-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi SPB</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalKoreksiListBarang" class="showhidemodalkoreksi">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_koreksi_list_barang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                  <th style="padding: 4px 12px;" scope="col">Qnt Out</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>


                </tr>
              </thead>


              <tbody id="tabel_data_koreksi_list_barang" class="text-left" >
                @for ($i = 0; $i < count($listBarang); $i++)
                <tr class="pick-row" onclick="buttonKoreksiPickBarang('{{ $listBarang[$i]->Kodebrg }}' , '{{ $listBarang[$i]->NamaBrg }}' , '{{ $listBarang[$i]->Sat1 }}' , '{{ $listBarang[$i]->ISI1 }}')">

                  <td>{{ $listBarang[$i]->Kodebrg }}</td>
                  <td>{{ $listBarang[$i]->NamaBrg }}</td>

                  <td>{{ $listBarang[$i]->SATUAN }}</td>
                  <td></td>
              </tr>
              @endfor
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>

    </div>
      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitKoreksi()">Koreksi</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal koreksi-->

<!-- start modal add -->
<div class="modal fade"  id="formKirimTerima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tgl Kirim / Terima</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <h4 id='titleInvoice'>No Invoice</h4>
              </div>
            </div>

          </div>

          
          <div class="row">
            <div class="col-8">
              <div class="form-group">
                <h4 id='titleSupplier'>Supplier</h4>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Kirim</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalKirim">
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Terima Barang</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalTerimaBarang">
              </div>
            </div>

          </div>

            
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Terima</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalTerima">
              </div>
            </div>

          </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitKirimTerima()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade"  id="formKirimTerimaAcc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tgl Terima ACC</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          
          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <h4 id='titleInvoice2'>No Invoice</h4>
              </div>
            </div>

          </div>

          
          <div class="row">
            <div class="col-8">
              <div class="form-group">
                <h4 id='titleSupplier2'>Supplier</h4>
              </div>
            </div>

          </div>

          <div class="row">
            <div class="col-4 text-left">
              <div class="form-group text-left">
                <label class="text-left">Tanggal Kirim</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggalKirimAcc">
              </div>
            </div>

          </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitKirimTerimaAcc()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

@endsection

@section('js')
<script src="{{ asset('js/report-table.js') }}"></script>
<script type="text/javascript">

let dataTableAdd = []
let dataTableAddHeader = []
let dataTableEdit = []
let dataKoreksiEdit = {}
let dataKoreksiAdd = {}

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

let dataRefreshPenerimaan = []

let dataTableKoreksiHeader = []
let dataTableKoreksi = []
let listAddBarangKoreksi = []
let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let tipeform = ''
let tipeformitem = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php (sama
 * seperti so.blade.php/invoicepenjualan.blade.php), MENGGANTIKAN HeaderEngine
 * -- tapi endpoint penyimpanannya TETAP loadHeader/simpanHeader milik
 * SuratJalanController (bukan pindah ke saveheadertable/getheadertable
 * milik PO): itu "core function" yang sudah ada, dan layout kolom yang
 * sudah tersimpan user di endpoint lama harus tetap kebaca. hrefnya juga
 * dibiarkan sama persis (suratjalan_tabel, suratjalan_tabel2, dst) supaya
 * baris yang sudah tersimpan tetap cocok.
 *
 * report-table.js membaca window.gcart_header/g_href/g_modeReport/
 * gsum_issubtotal/gsum_isgrandtotal langsung dan memanggil
 * window.doMoveHeader/doButtonVisibility/doSetDesimal/doButtonTotal/
 * doSimpanHeader BY THESE EXACT NAMES -- kontrak itu tidak berubah,
 * sjCart{} di bawah cuma checkpoint tempat gcart_header disimpan sebelum
 * pindah ke tabel lain, sama seperti poCart/soCart/ipCart.
 */
var g_href = '';
var g_modeReport = 1;
var gcart_header = [];
var gsum_issubtotal = 0, gsum_isgrandtotal = 0;

var sjCart = { tabel: [], tabel2: [], tabel6: [] };
var sjActiveKey = null;
var SJ_TABLE_INFO = {
  tabel:  { href: 'suratjalan_tabel',  setDefault: setDefaultHeaderTabel  },
  tabel2: { href: 'suratjalan_tabel2', setDefault: setDefaultHeaderTabel2 },
  tabel6: { href: 'suratjalan_tabel6', setDefault: setDefaultHeaderTabel6 }
};

var lastTabelRows = [];
var lastTabel2Rows = [];
var lastTabel6Rows = [];

// Dua sumber data di halaman ini kadang mengembalikan field logis yang sama
// dengan casing berbeda -- cari case-insensitive. Port dari HeaderEngine.pickCI.
function sjPickCI(row, key) {
  if (row[key] !== undefined) { return row[key]; }
  var lower = key.toLowerCase();
  for (var k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

// Checkpoint gcart_header/g_href/dst milik tabel yang sedang aktif keluar,
// lalu arahkan ke tabel `key`. Data-only, aman dipanggil sebelum DataTables
// destroy()/rebuild. Port dari HeaderEngine.activateEngineData().
function sjAktifkanTabel(key) {
  if (sjActiveKey && sjCart[sjActiveKey]) {
    sjCart[sjActiveKey] = gcart_header;
  }
  sjActiveKey = key;
  g_href = SJ_TABLE_INFO[key].href;
  gcart_header = sjCart[key];
}

function sjDoGetHeader(_str) {
  var arr = [];
  _str.split('||').forEach(function (part) {
    var f = part.split(';;');
    arr.push([f[0], f[1], Number(f[2]), f[3], Number(f[4]), Number(f[5])]);
  });
  return arr;
}

// Bare global -- report-table.js's saveHeader() fallback checks for this exact name.
window.doSimpanHeader = function (_hrefArg, _mode, _cart, _issubtotal, _isgrandtotal) {
  var _strHeader = "";
  _cart.forEach(function (item, i) {
    if (i != 0) { _strHeader += '||'; }
    _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' + item[5];
  });
  $.ajax({
    url: "{!! url('suratjalansimpanheader') !!}",
    type: "get",
    async: false,
    data: {
      href: _hrefArg,
      mode: _mode,
      header: _strHeader,
      issubtotal: _issubtotal,
      isgrandtotal: _isgrandtotal
    },
    success: function (res) {}
  });
};

function sjDoSetHeader(key, isReset) {
  isReset = isReset || false;
  var _strHeader = "";
  if (!isReset) {
    $.ajax({
      url: "{!! url('suratjalanloadheader') !!}",
      type: "get",
      async: false,
      data: { href: SJ_TABLE_INFO[key].href, mode: 1 },
      success: function (res) {
        if (res && res[0]) {
          _strHeader = res[0].header;
          gsum_issubtotal = Number(res[0].issubtotal) || 0;
          gsum_isgrandtotal = Number(res[0].isgrandtotal) || 0;
        }
      }
    });
  }
  if (_strHeader != "") {
    gcart_header = sjDoGetHeader(_strHeader);
  } else {
    SJ_TABLE_INFO[key].setDefault();
    window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  }
  sjCart[key] = gcart_header;
}

// Bare globals -- report-table.js's moveColumn()/menuAction() fallbacks check
// for these exact names.
window.doMoveHeader = function (_from, _to) {
  var moved = gcart_header.splice(_from, 1)[0];
  gcart_header.splice(_to, 0, moved);
  window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
};
window.doButtonVisibility = function (_id) {
  gcart_header[_id][2] = gcart_header[_id][2] ? 0 : 1;
  window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
};
window.doSetDesimal = function (_index, _step) {
  var v = (gcart_header[_index][5] || 0) + _step;
  if (v < 0) { v = 0; }
  if (v > 4) { v = 4; }
  gcart_header[_index][5] = v;
  window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
};
window.doButtonTotal = function (_index) {
  gcart_header[_index][4] = gcart_header[_index][4] ? 0 : 1;
  window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
};

function setDefaultHeaderTabel() {
  gcart_header = [
    ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
    ['URGENT',     'Prioritas',       1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
    ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
    ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
    ['Nopesanan',  'PO Cust',         1, 'varchar', 0, 0],
    ['QntOut',     'Qty Sisa',        1, 'float',   0, 2],
    ['Qnt',        'Qty SO',          1, 'float',   0, 2],
    ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
    ['catatan',    'Catatan',         1, 'varchar', 0, 0],
    ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
    ['namakebun',  'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
    ['NamaMerk',   'Nama Merk',       1, 'varchar', 0, 0],
    ['UserID',     'UserID',          1, 'varchar', 0, 0]
  ];
}

// #tabel2 ("SO Siap Kirim") shares dbSuratJalan's same outstanding-SO query shape as
// #tabel ("SO Belum Siap Kirim"), just pre-filtered to rows ready to ship. Also absorbs
// what used to be the separate "Out SO Prioritas" tab (#tabel5/queryOutstanding5SJ,
// removed) -- that tab's query was just this same one filtered to B.PUrgent=1, so
// instead of a separate tab the priority flag is now its own URGENT/NOTURGENT column
// here and every row shows regardless of priority.
function setDefaultHeaderTabel2() {
  gcart_header = [
    ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
    ['URGENT',     'Prioritas',       1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
    ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
    ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
    ['Nopesanan',  'PO Cust',         1, 'varchar', 0, 0],
    ['QntOut',     'Qty Sisa',        1, 'float',   0, 2],
    ['Qnt',        'Qty SO',          1, 'float',   0, 2],
    ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
    ['catatan',    'Catatan',         1, 'varchar', 0, 0],
    ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
    ['namakebun',  'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
    ['NamaMerk',   'Nama Merk',       1, 'varchar', 0, 0],
    ['UserID',     'UserID',          1, 'varchar', 0, 0]
  ];
}

function setDefaultHeaderTabel6() {
  gcart_header = [
    ['NOBUKTI',        'Nobukti',         1, 'varchar', 0, 0],
    ['TANGGAL',        'Tanggal',         1, 'date',    0, 0],
    ['NamaCustSupp',   'Customer',        1, 'varchar', 0, 0],
    ['NoPesanan',      'NoPesanan',       1, 'varchar', 0, 0],
    ['IDUser',         'User',            1, 'varchar', 0, 0],
    ['TGLKIRIM',       'Tgl Kirim',       1, 'date',    0, 0],
    ['TGLTERIMA',      'Tgl Terima',      1, 'date',    0, 0],
    ['OtoUser1',       'User Oto1',       1, 'varchar', 0, 0],
    ['TglOto1',        'Tgl Oto1',        1, 'date',    0, 0],
    ['LokasiPenerima', 'Lokasi Penerima', 1, 'varchar', 0, 0],
    ['RefUKM',         'No UKM',          1, 'varchar', 0, 0],
    ['Isbatal',        'Batal',           1, 'bool',    0, 0],
    ['Userbatal',      'User Batal',      1, 'varchar', 0, 0],
    ['Tglbatal',       'Tgl Batal',       1, 'date',    0, 0]
  ];
}

// Fixed, non-draggable Actions cell for #tabel6 -- same buttons/onclick args the old
// static markup and loadAll() row-builder both used, just read via pickCI() now.
function tabel6ActionsCell(row) {
  var nobukti = sjPickCI(row, 'NOBUKTI');
  var namaCustSupp = sjPickCI(row, 'NamaCustSupp');
  var tglKirim = sjPickCI(row, 'TGLKIRIM');
  var tglTerimaBrg = sjPickCI(row, 'TglTerimaBRG');
  var tglTerima = sjPickCI(row, 'TGLTERIMA');
  var tglSpbInvc = sjPickCI(row, 'TglSPBINVC');
  var isOto = Number(sjPickCI(row, 'IsOtorisasi1'));
  var html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Detail" type="button" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button> ';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" title="Otorisasi" type="button" onclick="buttonBatalOtorisasiSPB(\'' + nobukti + '\')"><i class="bi bi-key"></i></button> ';
    html += '<button class="btn btn-primary btn-sm" title="Kirim Terima" type="button" onclick="buttonKirimTerima(\'' + nobukti + '\',\'' + namaCustSupp + '\',\'' + tglKirim + '\',\'' + tglTerimaBrg + '\',\'' + tglTerima + '\')"><i class="bi bi-calendar4-week"></i></button> ';
    html += '<button class="btn btn-success btn-sm" title="Terima Acc" type="button" onclick="buttonTerimaAcc(\'' + nobukti + '\',\'' + namaCustSupp + '\',\'' + tglSpbInvc + '\')"><i class="bi bi-calendar4-range"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" title="Edit" type="button" onclick="buttonKoreksiSPB(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button> ';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasiSPB(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

// col: [field, label, visible, type, hasTotal, decimals]. Numeric formatting stays
// plain .toFixed() (no id-ID thousands separator) to match this page's existing qty
// display -- unlike so.blade.php's Rupiah columns, these are quantities, not money.
function suratjalanFormatTanggal(raw) {
  if (!raw) { return ''; }
  var d = new Date(raw);
  if (isNaN(d)) { return ''; }
  var dd = ('0' + d.getDate()).slice(-2);
  var mm = ('0' + (d.getMonth() + 1)).slice(-2);
  return d.getFullYear() + '/' + mm + '/' + dd;
}

function tabelValueCell(row, col) {
  var raw = sjPickCI(row, col[0]);
  var type = col[3];

  // Badge pakai .sp-badge yang sudah ada (report-table.css, sudah dimuat di
  // halaman ini) -- is-inactive sudah merah, is-user sudah biru, tidak perlu
  // CSS baru.
  if (col[0] === 'URGENT') {
    var kelas = raw === 'URGENT' ? 'is-inactive' : 'is-user';
    return '<td><span class="sp-badge ' + kelas + '">' + (raw || '') + '</span></td>';
  }

  if (type === 'date') {
    return '<td>' + suratjalanFormatTanggal(raw) + '</td>';
  }
  if (type === 'float') {
    var dp = Number(col[5]) || 0;
    var n = (raw !== undefined && raw !== null && raw !== '') ? parseFloat(raw) : 0;
    if (isNaN(n)) { n = 0; }
    return '<td class="text-right">' + n.toFixed(dp) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners with no matching
// removeEventListener -- calling init() again (which reinitTabelX() do, on purpose, to
// re-bind after DataTables rebuilds) is only safe against a genuinely NEW <thead> node
// each time, so the whole element is replaced here rather than just its innerHTML.
function suratjalanReplaceThead(tableSel, cols, leadingThHtml) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  var headRowHtml = ReportTable.headHtml(cols);
  if (leadingThHtml) {
    headRowHtml = headRowHtml.replace('<tr>', '<tr>' + leadingThHtml);
  }
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

// Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
// scrollbar sendiri - port 1:1 dari soAturTinggiTabel()/poAturTinggiTabel() milik
// so.blade.php/purchaseOrder.blade.php.
function sjAturTinggiTabel() {
  var area = document.getElementById('content');
  var pane = document.querySelector('#myTabContent .tab-pane.active');
  if (!area || !pane) { return; }
  var wrap = pane.querySelector('.po-table-wrap');
  if (!wrap) { return; }

  wrap.style.maxHeight = 'none';

  var padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
  var batasBawah = area.getBoundingClientRect().bottom - padBawah;
  var kotak = wrap.getBoundingClientRect();
  var bawah = pane.getBoundingClientRect().bottom - kotak.bottom;

  var sisa = batasBawah - kotak.top - bawah - 4;
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
}

// dom string disamakan 1:1 dengan so.blade.php: 'po-table-wrap't membungkus HANYA isi
// tabelnya dalam kotak scroll, sedangkan info+pagination ('i'/'p') ada DI LUAR kotak itu
// supaya tidak ikut tergulung -- menggantikan moveDataTablePagination()/
// .tb-pagination-outside yang dipakai sebelumnya untuk menyelesaikan masalah yang sama.
const SJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function renderTabelRows(rows) {
  if (sjActiveKey !== 'tabel') { sjAktifkanTabel('tabel'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr style="color: red">';
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  suratjalanReplaceThead('#tabel', cols);
}

// "SO Siap Kirim" row action -- buttonAdd(NOBUKTI) sudah lama ada (dipakai utk
// mulai bikin Surat Jalan baru dari baris SO ini, isi input_add_noso) tapi
// tidak pernah benar-benar dipanggil dari mana pun; sekarang dipasang lagi di
// sini sebagai tombol "+".
function tabel2ActionsCell(row) {
  var nobukti = sjPickCI(row, 'NOBUKTI');
  return '<td class="text-center"><div class="action-buttons-wrap">'
    + '<button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd(\'' + nobukti + '\')"><i class="bi bi-plus"></i></button>'
    + '</div></td>';
}

function renderTabel2Rows(rows) {
  if (sjActiveKey !== 'tabel2') { sjAktifkanTabel('tabel2'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  suratjalanReplaceThead('#tabel2', cols, '<th style="padding: 4px 12px;">Actions</th>');
}

function renderTabel6Rows(rows) {
  if (sjActiveKey !== 'tabel6') { sjAktifkanTabel('tabel6'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; });
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel6ActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel6_data').innerHTML = html;
  suratjalanReplaceThead('#tabel6', cols, '<th style="padding: 4px 12px;">Actions</th>');
}

// tabel/tabel2 tidak lagi dipaginate DataTables di browser (server-side
// paging sekarang, lihat sjGotoPage() di bawah) -- lastTabelRows/dst cuma berisi
// SATU halaman, jadi DataTable() dipasang dengan paging/searching/info mati,
// murni buat konsistensi visual saja.
var SJ_DOM_STRING_NOPAGE = "<'po-table-wrap't>"

function reinitTabel() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: SJ_DOM_STRING_NOPAGE, paging: false, searching: false, info: false, ordering: false, drawCallback: function () { setTimeout(sjAturTinggiTabel, 0); } });
    ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel });
    sjAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: SJ_DOM_STRING_NOPAGE, paging: false, searching: false, info: false, ordering: false, drawCallback: function () { setTimeout(sjAturTinggiTabel, 0); } });
    ReportTable.init({ table: '#tabel2', bar: '#rtBarTabel2', onChange: reinitTabel2 });
    sjAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

// ============ Pagination server-side utk tabel/tabel2 ============
// Masing-masing simpan state (halaman, panjang halaman, kata pencarian) sendiri --
// mengubah salah satu TIDAK memengaruhi tabel lain, beda dari perilaku lama yang
// menyaring keempat tabel sekaligus lewat satu kotak pencarian.
var sjPageState = {
  tabel:  { page: 1, length: 10, search: '', prioritas: '', total: 0 },
  tabel2: { page: 1, length: 10, search: '', prioritas: '', total: 0 }
}
var SJ_PAGE_INFO = {
  tabel:  { rowsVar: 'lastTabelRows',  reinit: 'reinitTabel',  prevId: 'tabelPagerPrev1', numbersId: 'tabelPagerNumbers1', nextId: 'tabelPagerNext1', infoId: 'tabelPagerInfo1', searchInpId: 'tabelSearch1', lenSelId: 'tabelLen1', filterInpId: 'input_filterprioritas1', filterBadgeId: 'tabel1FilterBadge' },
  tabel2: { rowsVar: 'lastTabel2Rows', reinit: 'reinitTabel2', prevId: 'tabelPagerPrev2', numbersId: 'tabelPagerNumbers2', nextId: 'tabelPagerNext2', infoId: 'tabelPagerInfo2', searchInpId: 'tabelSearch2', lenSelId: 'tabelLen2', filterInpId: 'input_filterprioritas2', filterBadgeId: 'tabel2FilterBadge' }
}

// Bikin daftar tombol nomor halaman ala DataTables (1 ... 4 5 [6] 7 8 ... 20)
// -- sebelumnya cuma satu tombol (halaman aktif) tanpa info total, jadi user
// tidak tahu ada berapa halaman/data sebenarnya. Port pola windowing sederhana,
// bukan API DataTables asli (datanya per-halaman dari server, bukan di browser).
function sjBuildPageButtons(key, page, totalPages) {
  var windowSize = 2
  var pages = [1]
  if (page - windowSize > 2) { pages.push('...') }
  for (var p = Math.max(2, page - windowSize); p <= Math.min(totalPages - 1, page + windowSize); p++) { pages.push(p) }
  if (page + windowSize < totalPages - 1) { pages.push('...') }
  if (totalPages > 1) { pages.push(totalPages) }
  var html = ''
  pages.forEach(function (p) {
    if (p === '...') {
      html += '<span class="paginate_button disabled ellipsis">&hellip;</span>'
    } else {
      html += '<a class="paginate_button' + (p === page ? ' current' : '') + '" onclick="sjGotoPage(\'' + key + '\', ' + p + ')">' + p + '</a>'
    }
  })
  return html
}

// Markup+class-nya sengaja disamakan dengan pager DataTables asli
// (.paginate_button/.disabled, lihat .tb-pagination-outside di atas) supaya
// tampilannya identik dengan tabel6 walau datanya diambil per-halaman sendiri.
function sjUpdatePagerUI(key) {
  var st = sjPageState[key]
  var info = SJ_PAGE_INFO[key]
  var totalPages = Math.max(1, Math.ceil(st.total / st.length))
  var awal = st.total === 0 ? 0 : (st.page - 1) * st.length + 1
  var akhir = Math.min(st.page * st.length, st.total)
  $('#' + info.infoId).text('Showing ' + awal + ' to ' + akhir + ' of ' + st.total + ' entries')
  $('#' + info.numbersId).html(sjBuildPageButtons(key, st.page, totalPages))
  $('#' + info.prevId).toggleClass('disabled', st.page <= 1)
  $('#' + info.nextId).toggleClass('disabled', st.page >= totalPages)
}

function sjFetchPage(key) {
  var st = sjPageState[key]
  var info = SJ_PAGE_INFO[key]
  $.ajax({
    url: "{!! url('suratjalanpaginate') !!}",
    type: "get",
    async: false,
    data: { table: key, page: st.page, length: st.length, search: st.search, prioritas: st.prioritas },
    success: function (res) {
      window[info.rowsVar] = res.rows
      st.total = res.total
      window[info.reinit]()
      sjUpdatePagerUI(key)
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data, silahkan refresh browser')
    }
  })
}

function sjGotoPage(key, page) {
  var st = sjPageState[key]
  var totalPages = Math.max(1, Math.ceil(st.total / st.length))
  if (page < 1 || page > totalPages) { return }
  st.page = page
  sjFetchPage(key)
}

function sjIkatToolbarTabel(key) {
  var info = SJ_PAGE_INFO[key]
  var searchTimeout
  // 'input' (bukan 'keyup') supaya tombol "x" bawaan <input type="search">
  // ikut ke-tangkap -- klik tombol itu tidak memicu keyup (tidak ada tombol
  // keyboard yang ditekan), jadi dengan keyup kotak pencarian terlihat kosong
  // tapi hasil yang difilter sebelumnya tidak pernah direset.
  $('#' + info.searchInpId).on('input', function () {
    var value = this.value
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(function () {
      sjPageState[key].search = value
      sjPageState[key].page = 1
      sjFetchPage(key)
    }, 400)
  })
  $('#' + info.lenSelId).on('change', function () {
    sjPageState[key].length = Number(this.value) || 10
    sjPageState[key].page = 1
    sjFetchPage(key)
  })
}

// Filter "Status Prioritas" (Urgent/Not Urgent) utk tabel/tabel2 -- pola modal +
// tombol hijau po-btn-filter sama persis dengan modalFilterSPB milik tabel6,
// bukan dropdown lepas di toolbar. "Reset semua" cuma mengosongkan field di
// modal (belum diterapkan); baru dipakai betulan saat "Terapkan" diklik, sama
// seperti sjResetFilterFieldsSPB()/buttonFilterSPB().
function sjResetFilterFieldsTabel (key) {
  $('#' + SJ_PAGE_INFO[key].filterInpId).val('')
}

function sjUpdateFilterBadgeTabel (key) {
  var aktif = sjPageState[key].prioritas !== ''
  $('#' + SJ_PAGE_INFO[key].filterBadgeId).text(aktif ? '1 aktif' : '0 aktif')
}

function buttonFilterTabel (key) {
  sjPageState[key].prioritas = $('#' + SJ_PAGE_INFO[key].filterInpId).val()
  sjPageState[key].page = 1
  sjFetchPage(key)
  sjUpdateFilterBadgeTabel(key)
}

function reinitTabel6() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel6')) { $('#tabel6').DataTable().destroy(); }
    renderTabel6Rows(lastTabel6Rows);
    $('#tabel6').DataTable({ dom: SJ_DOM_STRING, lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false, drawCallback: function () { setTimeout(sjAturTinggiTabel, 0); } });
    ReportTable.init({ table: '#tabel6', bar: '#rtBarTabel6', onChange: reinitTabel6 });
    sjAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel6 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    sjAktifkanTabel(key);
    sjDoSetHeader(key, true);
    ({ tabel: reinitTabel, tabel2: reinitTabel2, tabel6: reinitTabel6 })[key]();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

$(document).ready(function(){

      document.getElementById('breadcrumb').innerHTML = "Surat Jalan"

      // #home/#tabel ("SO Belum Siap Kirim") is the tab shown by default, so
      // initialize it last -- reinitTabelX() each end by binding ReportTable to their
      // own table, and whichever runs last wins, so this order leaves the actually-
      // visible tab interactive.
      sjAktifkanTabel('tabel6');
      sjDoSetHeader('tabel6', false);
      lastTabel6Rows = @json($tempOutstanding6);
      reinitTabel6();

      sjAktifkanTabel('tabel2');
      sjDoSetHeader('tabel2', false);
      lastTabel2Rows = @json($tempOutstanding2);
      sjPageState.tabel2.total = {{ (int) $tempOutstanding2Total }};
      reinitTabel2();
      sjUpdatePagerUI('tabel2');
      sjIkatToolbarTabel('tabel2');

      sjAktifkanTabel('tabel');
      sjDoSetHeader('tabel', false);
      lastTabelRows = @json($tempOutstanding);
      sjPageState.tabel.total = {{ (int) $tempOutstandingTotal }};
      reinitTabel();
      sjUpdatePagerUI('tabel');
      sjIkatToolbarTabel('tabel');

      // Re-bind the interactive engine whenever the user switches tabs -- ReportTable's
      // listeners are bound to one table's DOM at a time. Just re-runs ReportTable.init()
      // (same call reinitTabelX() itself makes) WITHOUT the DataTables destroy+rebuild --
      // matches exactly what HeaderEngine.bindEngineDom() used to do, so switching tabs
      // doesn't lose the table's current search/sort/page state like a full reinit would.
      $('#nav-home-tab').on('shown.bs.tab', function () {
        sjAktifkanTabel('tabel');
        ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel });
        sjAturTinggiTabel();
      });
      $('#nav-profile-tab').on('shown.bs.tab', function () {
        sjAktifkanTabel('tabel2');
        ReportTable.init({ table: '#tabel2', bar: '#rtBarTabel2', onChange: reinitTabel2 });
        sjAturTinggiTabel();
      });
      $('#nav-profile4-tab').on('shown.bs.tab', function () {
        sjAktifkanTabel('tabel6');
        ReportTable.init({ table: '#tabel6', bar: '#rtBarTabel6', onChange: reinitTabel6 });
        sjAturTinggiTabel();
      });

      // "Surat Jalan Otorisasi" (tabel6) tetap dipaginate DataTables penuh di browser
      // (datanya sudah dibatasi periode dari server), jadi search/length-nya sendiri
      // masih lewat DataTable().search()/.page.len() seperti sebelumnya -- cuma
      // sekarang idnya sendiri (#tabelSearch6/#tabelLen6), tidak berbagi lagi dengan
      // tabel/tabel2.
      var tabel6SearchTimeout;
      $('#tabelSearch6').on('keyup', function () {
        var value = this.value;
        clearTimeout(tabel6SearchTimeout);
        tabel6SearchTimeout = setTimeout(function () {
          if ($.fn.DataTable.isDataTable('#tabel6')) { $('#tabel6').DataTable().search(value).draw(); }
        }, 400);
      });
      $('#tabelLen6').on('change', function () {
        var len = Number(this.value);
        if ($.fn.DataTable.isDataTable('#tabel6')) { $('#tabel6').DataTable().page.len(len).draw(); }
      });

            $("#tabel4").DataTable({
              "lengthChange": false,
                "paging": false ,
                // "columns" : [{"width" : "20px"}]

              });

        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

    $("#tabel_add_list_pelanggan").DataTable({
      "lengthChange": false,
        "paging": false ,
    });

  $("#tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });
    // $("#tabel_koreksi_list_gudang").DataTable({
    //   "lengthChange": false,
    //     "paging": false ,
    // });
    // $("#tabel_koreksi_list_barang").DataTable({
    //   "lengthChange": false,
    //     "paging": false ,
    // });

    //   formAddListItem
});


function closeShowHideAdd () {
  $('.showhide').hide();

}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
}


function buttonKoreksiListBarang () {


  let _token = $("#_token").val();
  let noso = $("#input_koreksi_noso").val();
  console.log(noso)
  listAddBarangKoreksi = []

  $.ajax({
    url: "{!! url('suratjalanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: noso

    },
    success: function(res) {
      console.log(res)
      if (!res.length) {
        alertify.warning("Tidak ada barang untuk ditambah")
        return
      }
      listAddBarangKoreksi = res
      let rowListBarangKoreksi =  ``

      listAddBarangKoreksi.forEach((item, i) => {
        rowListBarangKoreksi += `
          <tr class="pick-row" onclick="buttonKoreksiPickBarang(${i})">
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>

            <td class="text-right">${parseFloat(item.QntOut).toFixed(2) }</td>
            <td>${item.SATUAN}</td>

          </tr>
        `
      });
      document.getElementById(`tabel_data_koreksi_list_barang`).innerHTML = rowListBarangKoreksi





      $('.showhidemodalkoreksi').hide();
      $('#modalKoreksiListBarang').show();
      $('#formKoreksi .modal-title').text('Barang')
      $("#formKoreksi").modal('toggle')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}

function buttonKoreksiPickBarang (index ) {
  // tipeformitem

dataKoreksiAdd = listAddBarangKoreksi[index]

  document.getElementById(`Add${tipeformitem}KodeBrg`).value = dataKoreksiAdd.KodeBrg
  document.getElementById(`Add${tipeformitem}NamaBrg`).value = dataKoreksiAdd.NamaBrg

  let tempIsi = ''
  let tempSat = ''

  if (dataKoreksiAdd.NOSAT == 3) {
    tempIsi = dataKoreksiAdd.ISI3
    tempSat = dataKoreksiAdd.Sat3
  } else if (dataKoreksiAdd.NOSAT == 2) {
    tempIsi = dataKoreksiAdd.ISI2
    tempSat = dataKoreksiAdd.Sat2
  } else {
    tempIsi = dataKoreksiAdd.ISI1
    tempSat = dataKoreksiAdd.Sat1
  }

  document.getElementById(`Add${tipeformitem}InputIsi`).value = tempIsi
  document.getElementById(`AddAddInputSatuan1`).value = dataKoreksiAdd.Sat1
  document.getElementById(`Add${tipeformitem}InputSatuan`).value = tempSat
  document.getElementById(`Add${tipeformitem}InputQty`).value = parseFloat(dataKoreksiAdd.QntOut).toFixed(2)
  $('.showhidemodalkoreksi').hide();
  $('#modalKoreksiMain').show();
  $('#formKoreksi .modal-title').text('Koreksi SPB')
  // document.getElementById(`Add${tipeformitem}KodeBrg`).scrollIntoView();
  $("#formKoreksi").modal('toggle')

}


function buttonAddListEkspedisi () {
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListEkspedisi').show();
  // // showhidemodalfooteradd
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterList').show();

  $.ajax({
    url: "{!! url('suratjalanlistekspedisi') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickEkspedisi('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}'  )">

        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=2>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_ekspedisi").innerHTML = rowTable

      // Search box: DataTables is (re)initialized AFTER the rows are injected, not
      // once on document ready -- this table's body is replaced wholesale via
      // innerHTML every time this modal opens, and initializing beforehand would
      // leave DataTables searching its own stale/empty row cache instead of what's
      // actually on screen.
      if ($.fn.DataTable.isDataTable('#tabel_add_list_ekspedisi')) {
        $('#tabel_add_list_ekspedisi').DataTable().destroy();
      }
      $('#tabel_add_list_ekspedisi').DataTable({
        lengthChange: false,
        paging: false
      });

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListEkspedisi').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $('#form .modal-title').text('Ekspedisi')

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}
// input_add_kodegdg

function submitAddAdd () {
  console.log('submitAddAdd')
  let choice ="I"
  let _token = $("#_token").val();
  console.log(dataTableKoreksiHeader)
  console.log(dataTableKoreksi)
  console.log(dataKoreksiAdd)



  let NamaBrg = $("#AddAddNamaBrg").val();
  let KodeBrg = $("#AddAddKodeBrg").val();
  let KodeGdg = $("#AddAddKodeGdg").val();
  let NamaGdg = $("#AddAddKodeGdg option:selected").data('nama') || '';
  let Satuan = $("#AddAddInputSatuan").val();
  let Qty = $("#AddAddInputQty").val();
  let Isi = $("#AddAddInputIsi").val();
  let nobukti = $("#input_koreksi_nobukti").val();
  let sat1 =  $("#AddAddInputSatuan1").val();

  if (Number(Qty) > Number(dataKoreksiAdd.QntOut)) {
    alertify.warning('Qty melebihi QntOut')
    return
  }
  console.log( NamaBrg , KodeBrg )
  console.log( NamaGdg , KodeGdg )
  console.log( Qty , Satuan )


  if (!KodeGdg || !KodeBrg || !Satuan || Number(Qty) < 0) {
    alertify.warning("Input tidak lengkap")
    return
  }

  console.log({
    choice,
    nobukti: nobukti,
    nourut: dataTableKoreksiHeader[0].nourut,
    // urut: 0,
    tanggal: dataTableKoreksiHeader[0].TANGGAL,
    nospp: '',
    kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
    container: null,
    nocontainer: null,
    noseal: null,
    catatan: dataTableKoreksiHeader[0].Catatan,
    urut: 0,
    urutspp: 0,
    kodebarang: KodeBrg,
    qnt: Qty * Isi,
    qnt2: Qty,
    sat1: sat1,
    sat2: Satuan,
    isi: Isi,
    netw: null,
    grossw: null,
    namabarang: '',
    sopir: dataTableKoreksiHeader[0].SOPIR,
    kodegdg: KodeGdg,
    kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
    noresi: '',
    jumlahtagihan: 0,
    flagtipe: 1,
    nobatch: '-' ,
    noso: dataKoreksiAdd.NOSO ,
    urutso: dataKoreksiAdd.URUTSO,
    satx: ''

  })
  // return

    $.ajax({
      url: "{!! url('suratjalanspkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        nobukti: nobukti,
        nourut: dataTableKoreksiHeader[0].nourut,
        // urut: 0,
        tanggal: dataTableKoreksiHeader[0].TANGGAL,
        nospp: '',
        kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
        container: null,
        nocontainer: null,
        noseal: null,
        catatan: dataTableKoreksiHeader[0].Catatan,
        urut: 0,
        urutspp: 0,
        kodebarang: KodeBrg,
        qnt: Qty * Isi,
        qnt2: Qty,
        sat1: sat1,
        sat2: Satuan,
        nosat: dataKoreksiAdd.NOSAT,
        isi: Isi,
        netw: null,
        grossw: null,
        namabarang: '',
        sopir: dataTableKoreksiHeader[0].SOPIR,
        kodegdg: KodeGdg,
        kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
        noresi: '',
        jumlahtagihan: 0,
        flagtipe: 1,
        nobatch: '-' ,
        noso: dataKoreksiAdd.NOSO ,
        urutso: dataKoreksiAdd.URUTSO,
        satx: ''

      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res == 1) {
          loadAll()
          refreshDataTableKoreksi(nobukti)
          $(".showhide").hide()
          // $("#form").modal('toggle')
          alertify.success('Item telah ditambah');
          // console.log('before ===========')
          // loadAll()
          // console.log("after ===========")
        }

        if (res == 2) {
          // setNewNoBukti()
          // alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3 ) {
          // alertify.warning('Stok gudang tidak mencukupi');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })



}

function submitAddEdit () {
  console.log('submitAddEdit')
  let _token = $("#_token").val();
  console.log(dataTableKoreksiHeader)
  console.log(dataTableKoreksi)

  let NamaBrg = $("#AddEditNamaBrg").val();
  let KodeBrg = $("#AddEditKodeBrg").val();
  let KodeGdg = $("#AddEditKodeGdg").val();
  let NamaGdg = $("#AddEditKodeGdg option:selected").data('nama') || '';
  let Satuan = $("#AddEditInputSatuan").val();

  let sat1 = $("#AddEditInputSatuan1").val();
  let choice = "U"
  let Qty = $("#AddEditInputQty").val();
  console.log(NamaBrg , KodeBrg)
  console.log(NamaGdg , KodeGdg)
  console.log(Qty , Satuan)

  console.log({
    choice,
    nobukti: dataKoreksiEdit.NOBUKTI,
    nourut: dataTableKoreksiHeader[0].nourut,
    urut: dataKoreksiEdit.URUT,
    tanggal: dataTableKoreksiHeader[0].TANGGAL,
    nospp: dataKoreksiEdit.NoSC,
    kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
    container: null,
    nocontainer: null,
    noseal: null,
    catatan: dataTableKoreksiHeader[0].Catatan,
    kodebarang: dataKoreksiEdit.KODEBRG,
    qnt: Qty * dataKoreksiEdit.ISI,
    qnt2: Qty,
    sat1: sat1,
    sat2: Satuan,
    isi: dataKoreksiEdit.ISI,
    nosat: dataKoreksiEdit.nosat,
    netw: null,
    grossw: null,
    namabarang: dataKoreksiEdit.namabrgx,
    sopir: dataTableKoreksiHeader[0].SOPIR,
    kodegdg: KodeGdg,
    kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
    noresi: '',
    jumlahtagihan: 0,
    flagtipe: 1,
    nobatch: '-' ,
    noso: dataKoreksiEdit.NOSO ,
    urutso: dataKoreksiEdit.URUTSO,
    satx: dataKoreksiEdit.SATX

  })
  // return
  $.ajax({
    url: "{!! url('suratjalanspkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti: dataKoreksiEdit.NOBUKTI,
      nourut: dataTableKoreksiHeader[0].nourut,
      urut: dataKoreksiEdit.URUT,
      tanggal: dataTableKoreksiHeader[0].TANGGAL,
      nospp: dataKoreksiEdit.NoSC,
      kodecustsupp: dataTableKoreksiHeader[0].KODECUSTSUPP,
      container: null,
      nocontainer: null,
      noseal: null,
      catatan: dataTableKoreksiHeader[0].Catatan,
      urut: dataKoreksiEdit.URUT,
      kodebarang: dataKoreksiEdit.KODEBRG,
      qnt: Qty * dataKoreksiEdit.ISI,
      qnt2: Qty,
      sat1: sat1,
      sat2: Satuan,
      isi: dataKoreksiEdit.ISI,

      nosat: dataKoreksiEdit.nosat,
      netw: null,
      grossw: null,
      namabarang: dataKoreksiEdit.namabrgx,
      sopir: dataTableKoreksiHeader[0].SOPIR,
      kodegdg: KodeGdg,
      kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
      noresi: '',
      jumlahtagihan: 0,
      flagtipe: 1,
      nobatch: '-' ,
      noso: dataKoreksiEdit.NOSO ,
      urutso: dataKoreksiEdit.URUTSO,
      satx: dataKoreksiEdit.SATX

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {

        loadAll()
        refreshDataTableKoreksi(dataKoreksiEdit.NOBUKTI)
        $(".showhide").hide()
        alertify.success('Item telah diedit');
        // alertify.success('SPB telah ditambah');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }

      if (res == 2) {
        // setNewNoBukti()
        // alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }

      if (res == 3 ) {
        // alertify.warning('Stok gudang tidak mencukupi');
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddPickEkspedisi (kode, nama) {
  console.log('buttonAddPickEkspedisi')
  console.log(kode, nama)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODEVLS' , kode)
  //   onChangeHeader('KURS' , kurs)
  // }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama
  buttonAddListBatal()
  $("#form").modal('toggle')
}


function sjResetFilterFieldsSPB () {
  $('#input_filterspb').val('0')
}

function sjUpdateFilterBadgeSPB () {
  let n = Number($('#input_filterspb').val()) || 0
  $('#spbFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterSPB () {
  loadAll()
  sjUpdateFilterBadgeSPB()
}

function onChangePeriodeSPB () {
  let tglawal = $('#input_tanggalawal_spb').val()
  let tglakhir = $('#input_tanggalakhir_spb').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterSPB()
}

function loadAll () {
  console.log('loadall tes')
  let _token = $("#_token").val();
  let tglawalspb = $("#input_tanggalawal_spb").val()
  let tglakhirspb = $("#input_tanggalakhir_spb").val()
  let filterspb = $("#input_filterspb").val()

  $.ajax({
    url: "{!! url('suratjalanloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawalspb, tglakhirspb, filterspb
    },
    success: function(res) {
      dataRefreshOutstanding4 = res.tempOutstanding4
      dataRefreshOutstanding6 = res.tempOutstanding6


    }})

  // tabel/tabel2 ("SO Siap Kirim"/"SO Belum Siap Kirim") sudah dipaginate server-side
  // sendiri (lihat sjFetchPage()) dan TIDAK lagi termasuk di response
  // suratjalanloadall (cuma tempOutstanding4/tempOutstanding6) -- sebelumnya baris di
  // bawah ini menimpa lastTabelRows/lastTabel2Rows dengan res.tempOutstanding/
  // res.tempOutstanding2 yang sudah undefined, jadi setiap loadAll() dipanggil kedua
  // tabel itu ikut kekosongan. Refresh lewat sjFetchPage() supaya tetap ambil
  // halaman/pencarian/filter yang sedang aktif.
  sjFetchPage('tabel')
  sjFetchPage('tabel2')

  lastTabel6Rows = dataRefreshOutstanding6
  reinitTabel6()

          $('#tabel4').DataTable().destroy();

          let rowTable4 = ""
          dataRefreshOutstanding4.forEach((item, i) => {




            rowTable4 += `
            <tr>
              <td>${ item.NOBUKTI }</td>
              <td>${ formatDate(item.Tanggal , '/')}</td>
              <td>${ item.NamaCustSupp }</td>
              <td>${ item.NamaBrg }</td>
              <td>${ item.RefPR }</td>
              <td>${ item.Nopesanan }</td>
              <td class="text-right">${ parseFloat(item.Qnt).toFixed(2) }</td>
              <td class="text-right">${ parseFloat(item.QntOut).toFixed(2) }</td>
              <td>${ item.SATUAN }</td>
              <td class="text-right">${ parseFloat(item.SaldoQnt).toFixed(2) }</td>
              <td>${ item.catatan }</td>
              <td>${ formatDate(item.DUEDATE , '/')}</td>
              <td>${ item.namakebun }</td>
              <td>${ item.PartNumber }</td>
              <td>${ item.NamaMerk }</td>
              <td>${ item.UserID }</td>

            </tr>
            `



          });

          document.getElementById("tabel4_data").innerHTML = rowTable4
          $("#tabel4").DataTable({
            "lengthChange": false,
              "paging": false ,

            });

}

function buttonAddListBatal () {
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  //
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterMain').show();


  $("#form").modal('toggle')

}


function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}

function cleanFormAdd () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_kodegdg").value = ''
  document.getElementById("input_add_nopol").value = ''
  document.getElementById("input_add_sopir").value = ''
  document.getElementById("input_add_refukm").value = ''
  document.getElementById("input_add_catatanso").value = ''
  document.getElementById("input_add_kodeekspedisi").value = ''
  document.getElementById("input_add_ekspedisi").value = ''


}

function refreshDataTableKoreksi (NOBUKTI) {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // if (res.header[0].IsOtorisasi1 == 1) {
      //
      //   alertify.warning('Nobukti sudah di otorisasi')
      //   return
      // }

      if (!res.detail.length) {
        // $("#formKoreksi").modal('toggle')
        buttonCloseForm()
        alertify.warning("Data habis")
        return
      }

      dataTableKoreksiHeader = res.header
      dataTableKoreksi = res.detail

      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI

      document.getElementById("input_koreksi_nourut").value = res.header[0].nourut
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX
      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
        <tr class="text-left">
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.namabrgx ? item.namabrgx : ''}</td>
          <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
          <td>${item.SAT_1}</td>

          <td>${item.NAMAGDG}</td>
          <td class='text-center'>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit('${item.URUT}' , ${i})"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete('${item.URUT}' , ${i})"><i class="bi bi-trash"></i></button>


          </td>
        </tr>
        `

          // <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
          // <td>${item.SAT_2}</td>
      });


      document.getElementById("koreksiTableData").innerHTML = rowTableKoreksi




      // $("#formKoreksi").modal('toggle')




    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonKoreksiAdd () {
  console.log('buttonKoreksiAdd')
    tipeformitem = 'Add'
    document.getElementById("AddAddKodeBrg").value = ''
    document.getElementById("AddAddNamaBrg").value = ''
    document.getElementById("AddAddKodeGdg").value = ''
    document.getElementById("AddAddInputSatuan").value = ''
    document.getElementById("AddAddInputQty").value = '0.00'
    $('.showhide').hide();
    $('#formAddAdd').show();

}

function buttonBatalAdd () {

    $('.showhide').hide();
}

function buttonKoreksiDelete (urut, index) {
  console.log('buttonKoreksiDelete')
  let tempData = dataTableKoreksi[index]
  console.log(tempData)
  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + tempData.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('suratjalanspkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti: tempData.NOBUKTI,
            nourut: '',
            urut: tempData.URUT,
            tanggal: '',
            nospp: tempData.NoSC,
            kodecustsupp: '',
            container: null,
            nocontainer: null,
            noseal: null,
            catatan: '',
            urut: tempData.URUT,
            kodebarang: tempData.KODEBRG,
            qnt: 0,
            qnt2: 0,
            sat1: '',
            sat2: '',
            isi: tempData.ISI,

            nosat: tempData.nosat,
            netw: null,
            grossw: null,
            namabarang: tempData.namabrgx,
            sopir: dataTableKoreksiHeader[0].SOPIR,
            kodegdg: '',
            kodeexp :  dataTableKoreksiHeader[0].KODEEXP,
            noresi: '',
            jumlahtagihan: 0,
            flagtipe: 1,
            nobatch: '-' ,
            noso: tempData.NOSO ,
            urutso: tempData.URUTSO,
            satx: tempData.SATX

          },
          success: function(res) {
            console.log('res', res)
            refreshDataTableKoreksi(tempData.NOBUKTI)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });

}

function buttonKoreksiEdit (urut , index) {
  console.log('buttonKoreksiEdit')
    tipeformitem = 'Edit'
    console.log(dataTableKoreksi[index])
    dataKoreksiEdit = dataTableKoreksi[index]
    document.getElementById("AddEditKodeBrg").value = dataKoreksiEdit.KODEBRG
    document.getElementById("AddEditNamaBrg").value = dataKoreksiEdit.NAMABRG

    document.getElementById("AddEditKodeGdg").value = dataKoreksiEdit.KODEGDG
    document.getElementById("AddEditInputSatuan1").value = dataKoreksiEdit.SAT_1

    document.getElementById("AddEditInputQty").value = dataKoreksiEdit.QNT2 ? parseFloat(dataKoreksiEdit.QNT2).toFixed(2) : '0.00'
    document.getElementById("AddEditInputSatuan").value = dataKoreksiEdit.Satuan
    // document.getElementById("AddEditInputIsi").value = dataKoreksiEdit.ISI
    $('.showhide').hide();
    $('#formAddEdit').show();
}


function buttonDetailSPB (NOBUKTI) {

  tipeform = 'edit'

  // modalKoreksiMain
  // $('#modalKoreksiMain').show();
  console.log('buttonDetailSPB' , NOBUKTI)
  let dataTableDetail = []
  let dataTableDetailHeader = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      dataTableDetailHeader = res.header
      dataTableDetail = res.detail
      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX

      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.namabrgx ? item.namabrgx : ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
            <td>${item.SAT_2}</td>

            <td>${item.NAMAGDG}</td>
          </tr>
        `

      });


      document.getElementById("detailTableData").innerHTML = rowTableKoreksi

      // $("#formKoreksi").modal('toggle')

      if (res.detail.length) {

        $('#page1').hide();
        $('#page4').show();

      }



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}

function buttonKoreksiSPB (NOBUKTI) {

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'edit'

  // $('.showhidemodalkoreksi').hide();
  $('.showhide').hide();
  $('#koreksiTambahWrap').show();
  // modalKoreksiMain
  // $('#modalKoreksiMain').show();
  console.log('buttonKoreksiSPB' , NOBUKTI)
  dataTableKoreksi = []
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      if (res.header[0].IsOtorisasi1 == 1) {

        alertify.warning('Nobukti sudah di otorisasi')
        return
      }
      dataTableKoreksiHeader = res.header
      dataTableKoreksi = res.detail
      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value =  res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value =  res.header[0].NamaKebunX

      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM


      let rowTableKoreksi = ``
      res.detail.forEach((item, i) => {
        rowTableKoreksi += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.namabrgx ? item.namabrgx : ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>

            <td>${item.NAMAGDG}</td>
            <td class='text-center'>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit('${item.URUT}' , ${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete('${item.URUT}' , ${i})"><i class="bi bi-trash"></i></button>


            </td>
          </tr>
        `

      });

            // <td class="text-right">${parseFloat(item.QNT2).toFixed(2)}</td>
            // <td>${item.SAT_2}</td>

      document.getElementById("koreksiTableData").innerHTML = rowTableKoreksi

      // $("#formKoreksi").modal('toggle')

      if (res.detail.length) {

        $('#page1').hide();
        $('#page3').show();

      }



    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


function buttonAdd (NOBUKTI) {
  tipeform = 'add'
  cleanFormAdd()
  dataTableAdd = []
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  refreshDataTableAdd(NOBUKTI)
  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterMain').show();
  document.getElementById("input_add_kodegdg").value = ''
  document.getElementById("input_add_kodeekspedisi").value = ''
  document.getElementById("input_add_noso").value = NOBUKTI


  if (dataTableAdd.length) {


      setNewNoBukti(Number(dataTableAddHeader[0].TipePPN))

    $('#page1').hide();
    $('#page2').show();
  }
  // $("#form").modal('toggle')
  return
  // lockFormAdd()
}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}






function setNewNoBukti (xppn) {
  $.ajax({
    url: "{!! url('suratjalanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn: Number(xppn)
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function buttonOtorisasiSPB (NOBUKTI) {
  console.log('buttonOtorisasiSPB' , NOBUKTI)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Otorisasi', 'Otorisasi SPB ' + NOBUKTI + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('suratjalanspotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            NOBUKTI

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Otorisasi SPB')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}


function buttonBatalOtorisasiSPB (NOBUKTI) {
  console.log('buttonBatalOtorisasiSPB' , NOBUKTI)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi SPB ' + NOBUKTI + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('suratjalanspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            NOBUKTI

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi SPB')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}




function submitAdd () {

  console.log('Submit Add')
    let _token = $("#_token").val();
    let tempData = []

    // console.log('TES ==========')
    // return
    let qtyCheck = false

    let kodegdg = document.getElementById("input_add_kodegdg").value

    let kodeekspedisi = document.getElementById("input_add_kodeekspedisi").value
    let nobukti = document.getElementById("input_add_nobukti").value
    let nourut = document.getElementById("input_add_nourut").value
    let noso = document.getElementById("input_add_noso").value
    let tanggal = document.getElementById("input_add_tanggal").value
    let nopol = document.getElementById("input_add_nopol").value
    let sopir = document.getElementById("input_add_sopir").value
    let refukm = document.getElementById("input_add_refukm").value
    let alamatkirim = document.getElementById("input_add_alamatkirim").value
    let kodekebun = dataTableAdd[0].kodekebun
    let lokasipenerima = document.getElementById("input_add_lokasipenerima").value
    let customer = document.getElementById("input_add_customer").value
    let catatanso = document.getElementById("input_add_catatanso").value

    if (!kodegdg) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Gdg harus diisi");
      return
    }

    dataTableAdd.forEach((item, i) => {
      if (document.getElementById(`add_checkbox${i}`).checked) {
        dataTableAdd[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        if (Number(dataTableAdd[i].inputQntTerima) > Number(dataTableAdd[i].QntSisa)) {
          qtyCheck = true
        }
        tempData.push(dataTableAdd[i])
      }
    });
    console.log('qwer')
    // console.log(tempData)
    // console.log(qtyCheck)
    if (qtyCheck) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Qty SPB tidak bisa lebih besar dari Qty Sisa");
      return
    }
    // return

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let flag = false
    tempData.forEach((item, i) => {
      console.log(item , '==================')
      console.log(Number(item.inputQntTerima) ,Number(item.QntSisa) )
      if (Number(item.inputQntTerima) > Number(item.QntSisa)) {
        console.log('os')

        // return
        flag =true
      }
      if (Number(item.inputQntTerima) <= 0) {
        console.log('negatif')

        // return
        flag =true
      }
    });
    if (flag) {
      alertify.warning("Qty tidak bisa negatif / melebihi OS");
      return
    }
    let inputDate = $("#input_add_tanggal").val();
    // let nosj = $(`#input_add_nosj`).val();
    // let norspb = $(`#input_add_norspb`).val();
    // let nourut =  $(`#input_add_noUrut`).val();
    // let nopolkendaraan =  $(`#input_add_nopolkendaraan`).val();
    // let expedisi = document.getElementById("input_add_expedisi").value;
    // let lokasiterima =  $(`#input_add_lokasiterima`).val();


    let checkDate = new Date(inputDate)

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value
    console.log('asd')

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    console.log(tempData)

    console.log(
      {
          kodegdg,
          kodeekspedisi,
          nobukti,
          nourut,
          noso,
          tanggal,
          nopol,
          sopir,
          refukm,
          alamatkirim,
          lokasipenerima,
          customer,
          catatanso,
          tempData
      }
    )





    // return

    $.ajax({
      url: "{!! url('suratjalanspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,

            kodegdg,
            kodeekspedisi,
            nobukti,
            nourut,
            noso,
            tanggal,
            nopol,
            sopir,
            refukm,
            alamatkirim,
            lokasipenerima,
            customer,
            catatanso,
            tempData,
            noresi: '',
            jumlah: 0,
            jmlrecord: 0,
            kodekebun

      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res == 1) {
          tipeform = 'edit'
          loadAll()
          // $("#form").modal('toggle')
          alertify.success('SPB telah ditambah');
          buttonCloseForm()
          // console.log('before ===========')
          // loadAll()
          // console.log("after ===========")
        }

        if (res == 2) {
          setNewNoBukti(Number(dataTableAddHeader[0].TipePPN))
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3 ) {
          // alertify.warning('Stok gudang tidak mencukupi');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}



// Read-only counterpart to buttonKoreksiSPB() -- reuses the same #page3/
// suratjalangetdetailkoreksi (correct SJ-shaped fields: gudang/ekspedisi/sopir/qty,
// not so.blade.php's customer-shaped #formDetail this used to call via the wrong
// "sogetdetail" SO-items endpoint), but skips the "sudah otorisasi" block and the
// akses_iskoreksi gate (Detail is meant to work on EVERY row, unlike Edit, which is
// otorisasi-gated and permission-gated) and renders item rows without the
// Edit/Delete buttons, so nothing here is actually editable.
function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('buttonDetail', NOBUKTI)

  $('.showhide').hide();
  $('#koreksiTambahWrap').hide();
  dataTableKoreksi = []
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('suratjalangetdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      if (!res.header || !res.header.length) {
        alertify.warning('Data tidak ditemukan')
        return
      }

      dataTableKoreksiHeader = res.header
      dataTableKoreksi = res.detail
      document.getElementById("input_koreksi_catatanso").value = res.header[0].Catatan
      document.getElementById("input_koreksi_nobukti").value = res.header[0].NOBUKTI
      document.getElementById("input_koreksi_customer").value = res.header[0].NamaCustSupp
      document.getElementById("input_koreksi_noso").value = res.header[0].NOSOT
      document.getElementById("input_koreksi_ekspedisi").value = res.header[0].KODEEXP
      document.getElementById("input_koreksi_nopol").value = res.header[0].NoPolKend
      document.getElementById("input_koreksi_alamatkirim").value = res.header[0].AlamatKirimX
      document.getElementById("input_koreksi_gdg").value = '-'
      document.getElementById("input_koreksi_lokasipenerima").value = res.header[0].NamaKebunX
      document.getElementById("input_koreksi_sopir").value = res.header[0].SOPIR
      document.getElementById("input_koreksi_refukm").value = res.header[0].RefUKM

      let rowTableKoreksi = ``
      res.detail.forEach((item) => {
        rowTableKoreksi += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.namabrgx ? item.namabrgx : ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td>${item.NAMAGDG}</td>
            <td></td>
          </tr>
        `
      });

      if (!res.detail.length) {
        rowTableKoreksi = `<tr><td class="text-center" colspan="7">Belum ada barang</td></tr>`
      }

      document.getElementById("koreksiTableData").innerHTML = rowTableKoreksi

      $('#page1').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}




function refreshDataTableAdd (NOBUKTI = "") {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {


    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="7">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()


    $.ajax({
      url: "{!! url('suratjalangetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        // console.log('aaa')
        console.log('res' , res)

        // res.header.forEach((item, i) => {
        //   console.log('a' , i)
        // });
        if (!res.detail.length) {
          alertify.warning("Data tidak ditemukan")


        } else {
          dataTableAddHeader = res.header
          dataTableAdd = res.detail

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {
            rowTable += `<tr>
            <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
            <td class='text-left' >${item.KODEBRG}</td>
            <td class='text-left'>${item.NAMABRG}</td>
            <td class='text-left'>${item.SATUAN}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td class='text-right'>${parseFloat(item.QntSisa).toFixed(2)}</td>
            <td class='text-right'>${ Number(item.QntBooking) ? parseFloat(item.QntBooking).toFixed(2) : '0.00'}</td>
            <td class="text-center"><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${parseFloat(item.QntSisa).toFixed(2)}></td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="5">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("addTableData").innerHTML = rowTable
          document.getElementById("input_add_customer").value = res.header[0].NamaCustSupp
          document.getElementById("input_add_alamatkirim").value = res.header[0].ALAMAT

          document.getElementById("input_add_lokasipenerima").value = res.header[0].AlamatLokasi


        }



      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })




  }


}


function submitKirimTerima () {

  console.log('Submit KirimTerima')

    let _token = $("#_token").val();
    let tanggalKirim = document.getElementById("input_add_tanggalKirim").value
    let tanggalTerimaBarang = document.getElementById("input_add_tanggalTerimaBarang").value
    let tanggalTerima = document.getElementById("input_add_tanggalTerima").value
  
    console.log(
      {
          tanggalKirim, tanggalTerimaBarang, tanggalTerima
      }
    )

    // return

    $.ajax({
      url: "{!! url('suratJalanAddKirimTerima') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
            kodeInvoiceTemp,
            tanggalKirim,
            tanggalTerimaBarang,
            tanggalTerima

      },
      success: function(res) {
          loadAll()
          

  $("#formKirimTerima").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


function submitKirimTerimaAcc () {

  console.log('Submit KirimTerima')

    let _token = $("#_token").val();
    let tanggalKirimAcc = document.getElementById("input_add_tanggalKirimAcc").value
  
    console.log(
      {
          tanggalKirimAcc
      }
    )

    // return

    $.ajax({
      url: "{!! url('suratJalanAddKirimTerimaAcc') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
            kodeInvoiceTemp,
            tanggalKirimAcc

      },
      success: function(res) {
          loadAll()
          

  $("#formKirimTerimaAcc").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


</script>


<script>
{{-- setActiveTab()/its $(".nav-link").css({...}) reset+click-handler removed: it
     manually forced inline background-color/color/border on every .nav-link, which
     fought with (and beat, since inline style wins) the .custom-tabs/.nav-link.active
     CSS this page's tab bar was already restyled to use -- exact same leftover
     as invoicepenjualan.blade.php had. Bootstrap's own data-toggle="tab" already
     toggles the .active class on click, so the CSS handles this natively now. --}}

let kodeInvoiceTemp = ''
let kodeCustTemp = ''

function buttonKirimTerima(kodeInvoice, kodeCust, tglKirim, tglTerimaBarang, tglTerima){

  kodeCustTemp = kodeCust;
  kodeInvoiceTemp = kodeInvoice;

  document.getElementById('titleInvoice').innerHTML = kodeInvoice;
  document.getElementById('titleSupplier').innerHTML = kodeCust;

  document.getElementById('input_add_tanggalKirim').value = formatTanggal(tglKirim)
  document.getElementById('input_add_tanggalTerimaBarang').value = formatTanggal(tglTerimaBarang)
  document.getElementById('input_add_tanggalTerima').value = formatTanggal(tglTerima)
  

  $("#formKirimTerima").modal('toggle')

}

function buttonTerimaAcc (kodeInvoice, kodeCust, tglAcc){

  kodeCustTemp = kodeCust;
  kodeInvoiceTemp = kodeInvoice;

  document.getElementById('titleInvoice2').innerHTML = kodeInvoice;
  document.getElementById('titleSupplier2').innerHTML = kodeCust;

  document.getElementById('input_add_tanggalKirimAcc').value = formatTanggal(tglAcc)
  

  $("#formKirimTerimaAcc").modal('toggle')

}

function formatTanggal(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toISOString().split('T')[0]; // "2024-01-15"
}
</script>





@endsection
