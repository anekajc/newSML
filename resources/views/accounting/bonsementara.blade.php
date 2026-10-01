@extends('newmasterTest')
@section('page-title', 'Bon Sementara')

@section('css')
<style>
  /* Form Add/Edit/Detail/Otorisasi dibungkus div#formBsGrid supaya input memakai gaya
     #formBsGrid di public/css/newmaster.css (tinggi 38px, sudut 8px, border halus, abu-abu saat
     disabled). Dua pengecualian tinggi:
     - textarea tetap setinggi aslinya (atribut rows), supaya isi beberapa baris tetap terbaca;
     - input di dalam tabel item ikut gaya baru tapi tingginya kembali ke ukuran aslinya
       (termasuk form-control-sm), supaya baris tabel tidak ikut lebih tinggi. */
  #formBsGrid textarea.form-control,
  #formBsGrid table .form-control {
    height: auto;
  }

  /* Tombol browse (kaca pembesar) di dalam #formBsGrid disamakan tingginya dengan input.
     Banyak tombol masih membawa inline style="height:32px" dari tata letak lama, sedangkan
     input di sini 38px - karena itu !important. Tombol yang berada di .input-group dilepas
     tingginya supaya meregang (align-items: stretch) mengikuti input di sebelahnya: 38px di
     form, dan tetap setinggi input di dalam sel tabel. Tombol yang berdiri sendiri (bukan di
     .input-group) dipaku 38px. */
  #formBsGrid .btn:has(> .bi-search) {
    height: 38px !important;
  }

  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
  }
</style>

{{-- Header tabel interaktif (geser kolom + roda gigi sembunyikan kolom + bar kolom
     tersembunyi + modal filter) - sama seperti menu Penerimaan DPP / purchasing. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }
</style>
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">

<style>
/* Jarak kartu ke bar atas - sama seperti menu Penerimaan DPP / purchasing. */
#content { padding-top: 12px; }

/* Layout newmasterTest punya rule .card global (menu beranda: flex, align-items:center,
   text-align:center, cursor:pointer, terangkat saat hover) yang membuat toolbar & tabel
   menciut dan rata tengah kalau dibiarkan. Dinetralkan persis seperti penerimaandpp.blade.php. */
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

/* Desain tab disamakan dengan menu Penerimaan DPP (penerimaandpp.blade.php): pill
   group di latar abu, tab aktif biru solid dengan shadow, murni lewat class. */
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
  background: #007bff;
  border-color: #007bff;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}

/* layout newmasterTest punya rule .card global (align-items:center) yang override ini */
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

/* DataTables selalu menulis hasil pengukurannya sebagai inline style pada <table>,
   yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width, BUKAN width. */
#tabel, #tabel2 { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabel td:first-child:not([colspan]),
#tabel2 td:first-child:not([colspan]) { vertical-align: middle; }

#tabel td:first-child .po-aksi-wrap,
#tabel2 td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
#tabel2 td:first-child .btn {
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

#tabel td:first-child .btn:hover,
#tabel2 td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success, #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger,  #tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

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

/* ---------- Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar ---------- */
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

/* ---------- Tombol utama halaman - sama seperti menu Penerimaan DPP ---------- */
.btn-dpp-utama {
  height: 36px;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0 16px;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  box-shadow: none;
}
.btn-dpp-utama:hover,
.btn-dpp-utama:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-dpp-utama:active { background-color: #cfdcff !important; border-color: #a8bdff !important; color: #1d4ed8 !important; }

/* ---------- Tombol di modal Bon (mode tambah & edit) - sama seperti menu Penerimaan DPP ----------
   Markup-nya sengaja tetap .btn-primary / .btn-secondary bawaan Bootstrap; yang diganti hanya
   warnanya di sini, mengikuti pola penerimaandpp.blade.php. Selector dibatasi ke .modal-body /
   .modal-footer supaya tombol ikon di kolom Aksi tabel (#tabel/#tabel2) tidak ikut terkena. */
.modal-body .btn-primary,
.modal-footer .btn-primary {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

.modal-body .btn-primary:hover,
.modal-footer .btn-primary:hover {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}

.modal-body .btn-secondary,
.modal-footer .btn-secondary {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

.modal-body .btn-secondary:hover,
.modal-footer .btn-secondary:hover {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

/* ---------- Layout form di modal Bon (mode tambah, tambah kredit, & edit) ----------
   Label di kiri dengan lebar tetap, input mengisi sisa lebar sampai tepi kanan.
   Memakai grid 4 kolom: baris biasa isinya label + input yang membentang ke tepi
   kanan, sedangkan baris Jumlah/Kredit mengisi keempat kolom. */
#form .modal-content { border: none; border-radius: var(--rt-radius); }
#form .modal-header { padding: 16px 24px; border-bottom: 1px solid var(--rt-border-soft); }
#form .modal-body   { padding: 20px 24px; }
#form .modal-footer { padding: 14px 24px 18px; border-top: 1px solid var(--rt-border-soft); }

#form .bs-form {
  display: grid;
  grid-template-columns: 96px minmax(0, 1fr) 68px minmax(0, 1fr);
  gap: 14px 12px;
  align-items: center;
}

/* Label tema Canvas punya margin-bottom: 10px global - dinetralkan di sini supaya
   jarak antarbaris diatur gap grid, bukan hack margin-top negatif seperti dulu. */
#form .bs-form label {
  margin-bottom: 0;
  text-align: left;
  font-size: 11.5px;
  letter-spacing: .04em;
  color: var(--rt-ink-soft);
  white-space: nowrap;
  cursor: default;
}

/* Input yang membentang dari kolom input pertama sampai tepi kanan. */
#form .bs-form .bs-full { grid-column: 2 / -1; }

#form .form-control {
  height: 38px;
  border-radius: 8px;
  border: 1px solid var(--rt-border);
  font-size: 13.5px;
  color: var(--rt-ink);
  box-shadow: none;
}

#form .form-control:focus {
  border-color: var(--rt-indigo);
  box-shadow: 0 0 0 3px var(--rt-indigo-soft);
}

/* Field terkunci - abu-abu supaya jelas tidak bisa diisi di mode tersebut.
   Hanya tampilan; yang mengunci tetap atribut disabled & lockFormAdd(). */
#form .form-control:disabled,
#form .form-control[disabled] {
  background-color: #f1f3f5;
  border-color: #e3e6ea;
  color: #8A8F9C;
  cursor: not-allowed;
}

/* Layar sempit: label pindah ke atas input, semua jadi satu kolom. */
@media (max-width: 575.98px) {
  #form .bs-form { grid-template-columns: 1fr; gap: 4px 0; }
  #form .bs-form label { text-align: left; margin-top: 8px; }
  #form .bs-form .bs-full { grid-column: 1 / -1; }
}
</style>
@endsection


@section('content')


<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >

  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          Penambahan Bon
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          Outstanding Bon
        </a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          {{-- Toolbar: periode (tanggal 1 s/d akhir bulan periode kerja), kotak cari,
               jumlah baris per halaman, dan tombol Filter (dropdown Perkiraan). --}}
          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" class="po-filter-inp" id="outTglAwal" value="{!! $outTglAwal !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" class="po-filter-inp" id="outTglAkhir" value="{!! $outTglAkhir !!}">
            </div>
            <input type="search" id="outSearch" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap">
              <label for="outLen">Tampilkan</label>
              <select id="outLen" class="po-len-inp">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">Semua</option>
              </select>
            </div>
            <button class="po-btn-filter" type="button" id="outBtnFilter" onclick="$('#modalFilterOut').modal('show')">
              <i class="bi bi-funnel"></i> Filter
            </button>
            <div class="po-toolbar-act">
              <button type="button" class="btn btn-dpp-utama" onclick="buttonAdd()">Tambah</button>
            </div>
          </div>

          {{-- #rtBarOut diisi lewat JS oleh ReportTable - lihat bsInitReportTableSekali(). --}}
          <div id="rtBarOut"></div>

          <table id="tabel" class="data-table po-aksi-hover">
            <thead id="tabel_header_out" class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                <th style="padding: 4px 12px;" scope="col">No. Bon</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Penerima</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Debet</th>
                <th style="padding: 4px 12px;" scope="col">Kredit</th>
                <th style="padding: 4px 12px;" scope="col">Saldo</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left">
              {{-- Baris + judul kolom digambar renderTabelOutstanding() lewat JS dari
                   data dan konfigurasi kolom yang dikirim loadAll(). --}}
            </tbody>
          </table>
        </div>

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="po-toolbar">
            <div class="po-filter-wrap">
              <label>Periode</label>
              <input type="date" class="po-filter-inp" id="bsTglAwal" value="{!! $tglAwal !!}">
              <span class="po-filter-sep">s/d</span>
              <input type="date" class="po-filter-inp" id="bsTglAkhir" value="{!! $tglAkhir !!}">
            </div>
            <input type="search" id="bsSearch" class="po-search-inp" placeholder="Cari data">
            <div class="po-len-wrap">
              <label for="bsLen">Tampilkan</label>
              <select id="bsLen" class="po-len-inp">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">Semua</option>
              </select>
            </div>
            <button class="po-btn-filter" type="button" id="bsBtnFilter" onclick="$('#modalFilterBs').modal('show')">
              <i class="bi bi-funnel"></i> Filter
            </button>
            <div class="po-toolbar-act">
              <!-- <button type="button" class="btn btn-dpp-utama" onclick="buttonAdd()">+ Tambah Bon</button> -->
            </div>
          </div>

          {{-- #rtBar diisi lewat JS oleh ReportTable - lihat bsInitReportTableSekali(). --}}
          <div id="rtBar"></div>

          <table id="tabel2" class="data-table po-aksi-hover">
            <thead id="tabel_header_penerimaan" class="text-center">
              <tr>
                {{-- NONAKTIF kolom Actions tab Outstanding Bon - hidupkan lagi dengan membuka komentar di 4 titik bertanda ini
                <th style="padding: 4px 12px;" scope="col">Actions</th>
                --}}
                <th style="padding: 4px 12px;" scope="col">No. Bon</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">Penerima</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Debet</th>
                <th style="padding: 4px 12px;" scope="col">Kredit</th>
                <th style="padding: 4px 12px;" scope="col">Saldo</th>
              </tr>
            </thead>
            <tbody id="tabel2_data" class="text-left">
              {{-- Baris digambar renderTabelPenerimaan() lewat JS. --}}
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

</div>
</div>


<!-- modal filter tab Penambahan Bon -->
<div class="modal fade rt-filter" id="modalFilterOut">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Penambahan Bon
          <span class="rt-active-badge" id="outFilterBadge">1 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterOut').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="outModalPerkiraan">Perkiraan</label>
              <select class="rt-native" id="outModalPerkiraan">
                @for ($i = 0; $i < count($perkiraan); $i++)
                  <option value='{{ $perkiraan[$i]->Perkiraan }}'>{{ $perkiraan[$i]->Keterangan }} ( {{ $perkiraan[$i]->Perkiraan }} )</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="$('#modalFilterOut').modal('hide')">Batal</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterOut').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="outTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter tab Penambahan Bon -->

<!-- modal filter tab Outstanding Bon -->
<div class="modal fade rt-filter" id="modalFilterBs">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Outstanding Bon
          <span class="rt-active-badge" id="bsFilterBadge">1 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterBs').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="bsModalPerkiraan">Perkiraan</label>
              <select class="rt-native" id="bsModalPerkiraan">
                @for ($i = 0; $i < count($perkiraan); $i++)
                  <option value='{{ $perkiraan[$i]->Perkiraan }}'>{{ $perkiraan[$i]->Keterangan }} ( {{ $perkiraan[$i]->Perkiraan }} )</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="$('#modalFilterBs').modal('hide')">Batal</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterBs').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="bsTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter tab Outstanding Bon -->


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document" style="">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Bon</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="formBsGrid">
      <div class="modal-body">
        <div class="bs-form">
          <label for="input_add_tanggal">Tanggal</label>
          <input type="date" class="form-control bs-full" id="input_add_tanggal">

          <label for="input_add_nobon">No Bon</label>
          <input type="text" class="form-control bs-full" id="input_add_nobon" disabled>

          <label for="input_add_penerima">Penerima</label>
          <input type="text" class="form-control bs-full" id="input_add_penerima">

          <label for="input_add_keterangan">Keterangan</label>
          <input type="text" class="form-control bs-full" id="input_add_keterangan">

          <label for="input_add_jumlah">Jumlah</label>
          <input type="text" inputmode="decimal" class="form-control text-right" id="input_add_jumlah">

          <label for="input_add_kredit">Kredit</label>
          <input type="text" inputmode="decimal" class="form-control text-right" id="input_add_kredit">
        </div>

        {{-- Sisa bon dipakai buttonAddKredit(); di luar grid supaya tidak membuat sel kosong. --}}
        <input type="hidden" id="input_add_sisa">
      </div>
      </div>{{-- /#formBsGrid --}}


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" id="buttonSubmitAdd" class="btn btn-primary" onclick="submitAdd()">Simpan</button>
        <button type="button" id="buttonSubmitEdit" class="btn btn-primary" onclick="submitEdit()">Simpan</button>
      </div>
      </div>


      </div>

    </div>
  </div>

<!-- End modal add-->


  </div>


@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataBon = {}
let tipeform = ''
let tipeadd = ''
let tipeedit = ''

$(document).ready(function(){

  bsSetPerkiraan('{{ count($perkiraan) ? addslashes($perkiraan[0]->Perkiraan) : "" }}')
  bsInitReportTableSekali()
  bsPasangSeparatorInput('input_add_jumlah')
  bsPasangSeparatorInput('input_add_kredit')

  // DataTables mengukur lebar kolom saat init. Tabel di tab yang awalnya
  // tersembunyi terukur 0, jadi lebarnya dihitung ulang begitu tabnya dibuka.
  $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().columns.adjust()
    }
    if ($.fn.DataTable.isDataTable('#tabel2')) {
      $('#tabel2').DataTable().columns.adjust()
    }
  })

  loadAll()

});

/* ==========================================================================
   Kedua tabel halaman ini memakai pola ReportTable (geser kolom + sembunyikan
   kolom + bar kolom tersembunyi), disalin dari penerimaandpp.blade.php.

   href dipatok, bukan diambil dari window.location - harus sama persis dengan
   BonSementaraController::HREF dan ::HREF_OUT.
   ========================================================================== */
const BS_HREF     = 'bonsementara'            // #tabel2 - Outstanding Bon
const BS_HREF_OUT = 'bonsementaraoutstanding' // #tabel  - Penambahan Bon

let bsCart = []      // susunan kolom #tabel2 (Outstanding Bon)
let bsCartOut = []   // susunan kolom #tabel (Penambahan Bon)

let dataPenerimaan = []  // hasil query untuk #tabel2 (res.tempPenerimaan)
let dataOutstanding = [] // hasil query untuk #tabel (res.tempOutstanding)

/* ReportTable membaca susunan kolom dari window.gcart_header dan menyimpan lewat
   window.g_href, jadi keduanya harus ditukar setiap kali tabel yang dipegang
   berganti. Penukarannya dipasang sebagai onActivate di ReportTable.init() dan
   dipanggil ReportTable.use() di awal tiap fungsi render. */
let bsTabelAktif = 'out'

function bsPakaiKolomOut () {
  bsTabelAktif = 'out'
  window.g_href = BS_HREF_OUT
  window.gcart_header = bsCartOut
}

function bsPakaiKolomPenerimaan () {
  bsTabelAktif = 'penerimaan'
  window.g_href = BS_HREF
  window.gcart_header = bsCart
}

function bsBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0 : 'varchar', 1 : 'float', 2 : 'date' }
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      tipeNama[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function bsKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function bsFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function bsRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return bsFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Angka mentah (tanpa pemisah ribuan) dari input yang sudah diberi separator -
// dipakai sebelum mengirim jumlah/kredit ke server.
function bsAngkaMentah (val) {
  return Number(String(val || '0').split(',').join('')) || 0
}

// Memasang separator ribuan langsung saat mengetik di input nominal.
function bsPasangSeparatorInput (id) {
  let el = document.getElementById(id)
  if (!el) { return }
  el.addEventListener('input', function () {
    let raw = el.value.replace(/,/g, '')
    if (raw === '' || raw === '-') { return }
    let minus = raw.charAt(0) === '-'
    if (minus) { raw = raw.substring(1) }
    let parts = raw.split('.')
    let bulat = parts[0].replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    let desimal = parts.length > 1 ? '.' + parts[1].replace(/\D/g, '').slice(0, 2) : ''
    el.value = (minus ? '-' : '') + bulat + desimal
  })
}

function bsHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let bsRtSudahInit = false

function bsInitReportTableSekali () {
  if (bsRtSudahInit || typeof ReportTable === 'undefined') { return }
  bsRtSudahInit = true

  ReportTable.init({
    table      : '#tabel',
    bar        : '#rtBarOut',
    onChange   : renderTabelOutstanding,
    onActivate : bsPakaiKolomOut
  })

  ReportTable.init({
    table      : '#tabel2',
    bar        : '#rtBar',
    onChange   : renderTabelPenerimaan,
    onActivate : bsPakaiKolomPenerimaan
  })

  // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
  // roda gigi / pegangan geser ke penangan milik ReportTable.
  ;['tabel_header_out', 'tabel_header_penerimaan'].forEach(function (idThead) {
    let guard = false
    let thead = document.getElementById(idThead)
    if (!thead) { return }

    thead.addEventListener('click', function (e) {
      if (guard) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      guard = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      guard = false
    }, true)
  })
}

/* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus
   tabel dengan #<id>_wrapper saat init, jadi acuannya ikut berpindah. */
function rtPindahBar (idBar, idTabel) {
  let bar = document.getElementById(idBar)
  let tabel = document.getElementById(idTabel)
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#' + idTabel)) {
    acuan = document.getElementById(idTabel + '_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

/* ---------- Filter Perkiraan (dua modal, satu nilai yang sama) ---------- */
// Perkiraan bukan filter opsional seperti Penagih/Otorisasi di Penerimaan DPP -
// nilainya selalu wajib dipilih dan dipakai kedua query di server. Kedua modal
// (tab Penambahan Bon & Outstanding Bon) disinkronkan supaya selalu menunjuk
// perkiraan yang sama.
let bsPerkiraan = ''

function bsSetPerkiraan (val) {
  bsPerkiraan = val
  let selOut = document.getElementById('outModalPerkiraan')
  let selBs  = document.getElementById('bsModalPerkiraan')
  if (selOut) { selOut.value = val }
  if (selBs)  { selBs.value = val }
}

function outTerapkanFilter () {
  bsSetPerkiraan($('#outModalPerkiraan').val())
  $('#modalFilterOut').modal('hide')
  loadAll()
}

function bsTerapkanFilter () {
  bsSetPerkiraan($('#bsModalPerkiraan').val())
  $('#modalFilterBs').modal('hide')
  loadAll()
}

/* ---------- Kotak cari, panjang halaman, periode - tab Penambahan Bon (#tabel) ---------- */
let outPanjangHalaman = 10

function outIkatSearch () {
  let input = document.getElementById('outSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel').DataTable().search(input.value).draw()
  })
}

function outIkatPanjangHalaman () {
  let sel = document.getElementById('outLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(outPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    outPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel').DataTable().page.len(outPanjangHalaman).draw()
  })
}

function outIkatPeriode () {
  let awal  = document.getElementById('outTglAwal')
  let akhir = document.getElementById('outTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

/* ---------- Kotak cari, panjang halaman, periode - tab Outstanding Bon (#tabel2) ---------- */
let bsPanjangHalaman = 10

function bsIkatSearch () {
  let input = document.getElementById('bsSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

function bsIkatPanjangHalaman () {
  let sel = document.getElementById('bsLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(bsPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    bsPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(bsPanjangHalaman).draw()
  })
}

function bsIkatPeriode () {
  let awal  = document.getElementById('bsTglAwal')
  let akhir = document.getElementById('bsTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

/* ---------- Simpan / muat susunan kolom ---------- */
window.g_href = BS_HREF_OUT
window.g_modeReport = 1
window.gcart_header = []

// Keduanya dipanggil ReportTable untuk tabel yang sedang aktif, jadi cart dan href
// yang dipakai mengikuti bsTabelAktif (lihat bsPakaiKolom*()).
window.doSimpanHeader = function (href, mode) {
  let outAktif = (bsTabelAktif === 'out')
  let cart     = outAktif ? bsCartOut : bsCart

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : outAktif ? BS_HREF_OUT : BS_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Tombol "Reset kolom". Memakai endpoint milik menu ini sendiri karena
// HeaderTableController belum punya cabang untuk href halaman ini.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  let outAktif = (bsTabelAktif === 'out')

  $.ajax({
    url   : "{!! url('bonsementararesetheader') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      tabel  : outAktif ? 'outstanding' : 'penerimaan'
    },
    success : function (res) {
      let cart = bsBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      if (outAktif) { bsCartOut = cart } else { bsCart = cart }
      window.gcart_header = cart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

/* ==========================================================================
   Tabel Penambahan Bon (#tabel) - seluruh baris dbBon (debet maupun kredit) pada
   periode terpilih. Tombol aksi: + Kredit, Koreksi, Hapus.
   ========================================================================== */
function renderTabelOutstanding () {
  window.g_modeReport = 1
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabel') }
  bsPakaiKolomOut()

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = (bsCartOut || []).filter(c => Number(c[2]) === 1)
  let kolomRender = cols.map(bsKolomRender)

  let thead = document.getElementById('tabel_header_out')
  thead.innerHTML = bsHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let rowTable = ''
  ;(dataOutstanding || []).forEach((item) => {
    // Tombol aksi hanya untuk baris bon (debet). Baris kredit (pembayaran bon) sel
    // Action-nya sengaja dikosongkan.
    if (Number(item.Debet) > 0) {
      rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">`
      /* NONAKTIF tombol + Kredit di tab Penambahan Bon - hidupkan lagi dengan membuka komentar ini
      rowTable += `<button class="btn btn-primary btn-sm" type="button" title="+ Kredit" onclick="buttonAddKredit('${item.NoBukti}','${item.Penerima}')"><i class="bi bi-file-earmark-minus"></i></button>`
      */
      rowTable += `<button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NoBukti}','${item.Urut}','${item.Perkiraan}')"><i class="bi bi-pen"></i></button>`
      rowTable += `<button class="btn btn-danger btn-sm" type="button" title="Hapus" onclick="buttonDelete('${item.NoBukti}','${item.Urut}','${item.Perkiraan}')"><i class="bi bi-trash"></i></button>`
      rowTable += `</div></td>`
    } else {
      rowTable += `<tr><td class="text-center"></td>`
    }
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${bsRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${bsRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `</tr>`
  })

  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel').DataTable({
    lengthChange: false,
    pageLength: outPanjangHalaman,
    order: [],
    columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })

  rtPindahBar('rtBarOut', 'tabel')
  outIkatSearch()
  outIkatPanjangHalaman()
  outIkatPeriode()
  let inputSearch = document.getElementById('outSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel').DataTable().search(inputSearch.value).draw()
  }
}

/* ==========================================================================
   Tabel Outstanding Bon (#tabel2) - bon yang Debet-nya belum lunas dibayar
   Kredit. Tombol aksi: + Kredit saja.
   ========================================================================== */
function renderTabelPenerimaan () {
  window.g_modeReport = 1
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabel2') }
  bsPakaiKolomPenerimaan()

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols = (bsCart || []).filter(c => Number(c[2]) === 1)
  let kolomRender = cols.map(bsKolomRender)

  let thead = document.getElementById('tabel_header_penerimaan')
  thead.innerHTML = bsHeadHtml(cols)
  let baris = thead.querySelector('tr')
  /* NONAKTIF kolom Actions tab Outstanding Bon - hidupkan lagi dengan membuka komentar di 4 titik bertanda ini
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }
  */

  let rowTable = ''
  ;(dataPenerimaan || []).forEach((item) => {
    /* NONAKTIF kolom Actions tab Outstanding Bon - hidupkan lagi dengan membuka komentar di 4 titik bertanda ini
    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">
        <button class="btn btn-primary btn-sm" type="button" title="+ Kredit" onclick="buttonAddKredit('${item.NoBukti}','${item.Penerima}')"><i class="bi bi-file-earmark-minus"></i></button>
      </div></td>`
    */
    rowTable += `<tr>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${bsRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${bsRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `</tr>`
  })

  document.getElementById('tabel2_data').innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: bsPanjangHalaman,
    order: [],
    // NONAKTIF kolom Actions tab Outstanding Bon - hidupkan lagi dengan membuka komentar di 4 titik bertanda ini
    // columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })

  rtPindahBar('rtBar', 'tabel2')
  bsIkatSearch()
  bsIkatPanjangHalaman()
  bsIkatPeriode()
  let inputSearch = document.getElementById('bsSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
}

function loadAll () {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('bonsementaraloadall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan   : bsPerkiraan,
      tglawal     : $('#bsTglAwal').val(),
      tglakhir    : $('#bsTglAkhir').val(),
      outtglawal  : $('#outTglAwal').val(),
      outtglakhir : $('#outTglAkhir').val()
    },
    success: function(res) {
      bsCartOut = bsBuatCart(res.outheadertableheader, res.outheadertablevalue, res.outisnumeric, res.outisshown, res.outdesimal, res.outaliasordered)
      dataOutstanding = res.tempOutstanding || []

      bsCart = bsBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      dataPenerimaan = res.tempPenerimaan || []

      renderTabelOutstanding()
      renderTabelPenerimaan()
    }})

}

function submitEdit () {
  console.log('submitEdit')


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let tanggal  = $("#input_add_tanggal").val()
  let nobon  = $("#input_add_nobon").val()
  let penerima  = $("#input_add_penerima").val()
  let keterangan  = $("#input_add_keterangan").val()
  let jumlah  = bsAngkaMentah($("#input_add_jumlah").val())
  let kredit  = bsAngkaMentah($("#input_add_kredit").val())
  let perkiraan  = bsPerkiraan
  let choice = "U"
  let urut = dataBon.Urut
  let devisi = '01'
  let valas = dataBon.KodeVls
  let kurs = dataBon.Kurs
  let debetd = 0
  let kreditd = 0
  let tglinput = new Date()

  if (jumlah > 0 && kredit > 0 ) {
    alertify.warning('DB/CR')
    return
  } else if (jumlah <= 0 && kredit <= 0) {
    alertify.warning("DB/CR")
    return
  }



  $.ajax({
      url: "{!! url('bonsementaraspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobon,
        tanggal,
        penerima,
        keterangan,
        jumlah,
        kredit,
        urut,
        devisi,
        tglinput,
        debetd,
        kreditd,
        perkiraan,
        tipeadd
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bon telah ditambah');

          loadAll()

          $("#form").modal('toggle')

        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}


function submitAdd () {
  console.log('submitAdd')


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let tanggal  = $("#input_add_tanggal").val()
  let nobon  = $("#input_add_nobon").val()
  let penerima  = $("#input_add_penerima").val()
  let keterangan  = $("#input_add_keterangan").val()
  let jumlah  = bsAngkaMentah($("#input_add_jumlah").val())
  let kredit  = bsAngkaMentah($("#input_add_kredit").val())
  let perkiraan  = bsPerkiraan
  let choice = "I"
  let urut = 0
  let devisi = '01'
  let valas = 'IDR'
  let kurs = 1
  let debetd = 0
  let kreditd = 0
  let tglinput = new Date()

  if (!penerima) {
    alertify.warning('Penerima harus diisi')
    return
  }

  let sisa = bsAngkaMentah($("#input_add_sisa").val())

  if (tipeadd == 'kredit') {
    if (kredit <= 0 ) {

      alertify.warning("Jumlah <= 0")
      return
    }

    if (kredit > sisa) {
      alertify.warning("Kredit melebihi debet")
      return
    }


  } else {
    if (jumlah <= 0 ) {

      alertify.warning("Jumlah <= 0")
      return
    }
  }




  $.ajax({
      url: "{!! url('bonsementaraspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobon,
        tanggal,
        penerima,
        keterangan,
        jumlah,
        kredit,
        urut,
        devisi,
        tglinput,
        debetd,
        kreditd,
        perkiraan,
        tipeadd
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bon telah ditambah');

          loadAll()

          $("#form").modal('toggle')

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })


}

function cleanFormAdd () {

  document.getElementById('input_add_nobon').value = ''
  // Sama seperti di mode edit: valueAsDate menulis komponen UTC, jadi sebelum jam 07:00 WIB
  // tanggalnya mundur sehari. Diisi lewat .value dengan formatDate() yang memakai waktu lokal.
  document.getElementById('input_add_tanggal').value = formatDate(new Date())
  document.getElementById('input_add_penerima').value = ''
  document.getElementById('input_add_keterangan').value = ''
  document.getElementById('input_add_jumlah').value = '0.00'
  document.getElementById('input_add_kredit').value = '0.00'

}

function lockFormAdd (value= false) {
  document.getElementById('input_add_penerima').disabled = value
  document.getElementById('input_add_jumlah').disabled = value
  document.getElementById('input_add_kredit').disabled = !value
  // document.getElementById('input_add_tanggal').disabled = value

}



function buttonDelete (nobukti, urut, perkiraan) {
  // tipeform = 'edit'
  dataBon = {}
  console.log(nobukti, urut, perkiraan)
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('bonsementaraspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut,
      perkiraan

    },
    success: function(res) {
      console.log(res)
      if (!res.detail.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      } else {


        dataBon = res.detail[0]
        let tempAngka = 0
        if (Number(dataBon.Debet) > 0) {
          tipeedit = 'debet'
          tempAngka = bsFormatAngkaDes(dataBon.Debet, 2)
        } else {
          tipeedit = 'kredit'
          tempAngka = bsFormatAngkaDes(dataBon.Kredit, 2)
        }
        console.log(tipeedit)

        if( tipeedit == 'debet' && res.check.length > 0) {
          alertify.warning("Sudah ada transaksi kredit, tidak bisa hapus debet")
          return
        }




        alertify.confirm('Hapus Bon', 'Hapus Bon ' + nobukti + ` ${tipeedit} ${tempAngka} ?`,
            function() {
              let _token  = $("#_token").val()
              let tanggal  = $("#input_add_tanggal").val()
              let nobon  = nobukti
              let penerima  = $("#input_add_penerima").val()
              let keterangan  = $("#input_add_keterangan").val()
              let jumlah  = bsAngkaMentah($("#input_add_jumlah").val())
              let kredit  = bsAngkaMentah($("#input_add_kredit").val())
              let perkiraan  = bsPerkiraan
              let choice = "D"
              let urut = dataBon.Urut
              let devisi = '01'
              let valas = dataBon.KodeVls
              let kurs = dataBon.Kurs
              let debetd = 0
              let kreditd = 0
              let tglinput = new Date()




              $.ajax({
                  url: "{!! url('bonsementaraspadd') !!}",
                  type: "post",
                  async: false,
                  data: {
                    _token,
                    choice,
                    nobon,
                    tanggal,
                    penerima,
                    keterangan,
                    jumlah,
                    kredit,
                    urut,
                    devisi,
                    tglinput,
                    debetd,
                    kreditd,
                    perkiraan,
                    tipeadd
                  },
                  success: function(res) {
                    console.log(res ,'!')

                    if (res == 1) {
                      // $("#form").modal('toggle')
                      alertify.success('Bon telah dihapus');

                      loadAll()


                    }

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





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonKoreksi (nobukti, urut, perkiraan) {
  tipeform = 'edit'
  dataBon = {}
  lockFormAdd(true)

  $("#buttonSubmitAdd").hide()
  $("#buttonSubmitEdit").show()
  console.log(nobukti, urut, perkiraan)
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('bonsementaraspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut,
      perkiraan

    },
    success: function(res) {
      console.log(res)
      if (!res.detail.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      } else {


        dataBon = res.detail[0]

        document.getElementById('input_add_nobon').value = nobukti
        // Pakai .value + formatDate(), bukan valueAsDate: valueAsDate membaca objek Date sebagai
        // UTC, sedangkan "2026-09-16 00:00:00.000" dari getDetailOutstanding() diparse sebagai jam
        // 00:00 WIB, sehingga tanggalnya mundur sehari (tampil 15). formatDate() memakai getDate()
        // lokal, jadi tanggalnya sama persis dengan yang tersimpan di dbBon.
        document.getElementById('input_add_tanggal').value = formatDate(dataBon.Tanggal)
        document.getElementById('input_add_jumlah').value = bsFormatAngkaDes(dataBon.Debet, 2)
        document.getElementById('input_add_kredit').value = bsFormatAngkaDes(dataBon.Kredit, 2)
        document.getElementById('input_add_penerima').value = dataBon.Penerima

        document.getElementById('input_add_keterangan').value = dataBon.Keterangan
        if (Number(dataBon.Debet) > 0) {
          document.getElementById("input_add_jumlah").disabled = false
          document.getElementById("input_add_kredit").disabled = true
          tipeedit = 'debet'
        } else {
          document.getElementById("input_add_jumlah").disabled = true
          document.getElementById("input_add_kredit").disabled = false
          tipeedit = 'kredit'
        }
        console.log(tipeedit)

        if( tipeedit == 'debet' && res.check.length > 0) {
          alertify.warning("Sudah ada transaksi kredit, tidak bisa dikoreksi")
          return
        } else {
          $("#form").modal('toggle')

        }

      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let bulan = $("#periode_bulan").val()
  let tahun = $("#periode_tahun").val()
  console.log(bulan, tahun)
  bulan = '0' + bulan
  let xbulan = bulan.slice(-2)
  let xtahun = tahun.slice(-2)
  console.log(xbulan, xtahun)
  let perkiraan  = bsPerkiraan
  $.ajax({
    url: "{!! url('bonsementaraspnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan
    },
    success: function(res) {

      console.log(res)
      if (res.length == 0) {
        document.getElementById("input_add_nobon").value = xtahun + xbulan + '001'

      } else {
        document.getElementById("input_add_nobon").value = Number(res[0].NoBukti) + 1

      }

    }})
}



function buttonAdd () {
  tipeform = 'add'
  tipeadd = 'nonkredit'
  lockFormAdd(false)
  cleanFormAdd()
  setNewNoBukti()
  $("#buttonSubmitAdd").show()
  $("#buttonSubmitEdit").hide()
  $("#form").modal('toggle')

}

function buttonAddKredit (nobukti, penerima) {
  lockFormAdd(true)
  cleanFormAdd()
  tipeadd = 'kredit'

    document.getElementById("input_add_nobon").value = nobukti
    document.getElementById("input_add_penerima").value = penerima
    let _token = $("#_token").val()
    let perkiraan = bsPerkiraan
    $.ajax({
      url: "{!! url('bonsementaraspdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        urut: 0,
        perkiraan

      },
      success: function(res) {


        document.getElementById("input_add_kredit").value = bsFormatAngkaDes(res.sisa[0].sisa, 2)
        document.getElementById("input_add_sisa").value = res.sisa[0].sisa




      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
    $("#buttonSubmitAdd").show()
    $("#buttonSubmitEdit").hide()
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


</script>

@endsection
