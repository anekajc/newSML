@extends('newmasterTest')
@section('buttons')
@section('page-title', 'PR Non - Agen')

@endsection


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

  /* Padding atas-bawah dinolkan: dengan padding 8px tombol setinggi 35,5px, lebih tinggi dari
     input (34,25px) - stretch hanya bisa memanjangkan, jadi tombol menonjol ke bawah. Tanpa
     padding vertikal tinggi tombol sepenuhnya mengikuti input di sebelahnya. */
  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
    padding-top: 0;
    padding-bottom: 0;
  }
</style>
  {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + modal
     filter), disamakan dengan resources/views/purchasing/purchaseOrder.blade.php. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }
</style>
<style>
  /* Tombol browse (kaca pembesar) tetap menempel ke input, tapi sudutnya membulat
     (bukan kotak). !important untuk menimpa inline
     style="border-radius:0" dan aturan .input-group bawaan Bootstrap. */
  .btn-chip-biru:has(> .bi-search),
  .btn-browsing:has(> .bi-search) {
    border-radius: 6px !important;
  }
  /* Input di kiri tombol browse ikut membulat di sisi kanannya (lewati input hidden). */
  .form-control:has(+ .btn > .bi-search),
  .form-control:has(+ :is([type=hidden], [hidden]) + .btn > .bi-search),
  .form-control:has(+ .input-group-append > .btn > .bi-search) {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }
</style>
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
{{-- Modal pemilih (Perkiraan/Barang/Supplier/Customer/dst.) disamakan persis dengan modal pemilih menu Kas - lihat public/css/picker-kas.css. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
/* Halaman ini dirancang mengisi tinggi layar (lihat prAturTinggiTabel()), jadi padding
   atas #content layout dikecilkan - sama seperti purchaseOrder.blade.php. */
#content { padding-top: 12px; }

/* Rule .card global di layout newmasterx sebenarnya untuk kartu menu dashboard
   (flex + align-items:center + efek melayang saat hover). Kalau dipakai untuk card berisi
   tabel, card-body tidak melar mengikuti halaman melainkan menyusut mengikuti isinya
   lalu tampil di tengah. */
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

.dataTables_scrollBody {
    border: none !important;
}

.table-responsive {
    border: none !important;
}

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
   style pada <table>, yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width,
   BUKAN width, dan di-scope lewat ID (bukan class) karena DataTables meng-clone tabel
   sambil membuang id saat mengukur kolom - lihat catatan yang sama di purchaseOrder.blade.php. */
#tabel2 {
  min-width: 100%;
}

/* ---------- Kolom Aksi tabel (#tabel2 di page1, #tabel_add di form Add Item) - tombol
   bulat kecil, warna pastel, disalin dari purchaseOrder.blade.php supaya tampilannya
   seragam. Di #tabel2 Actions ada di kolom PALING KIRI (first-child), sedangkan di
   #tabel_add Actions ada di kolom PALING KANAN (last-child). ---------- */
#tabel2 td:first-child:not([colspan]) {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

/* #tabel_add: JANGAN pakai display:flex pada <td> - sel yang di-flex berhenti jadi
   kotak sel tabel sehingga lebar kolomnya tidak lagi sinkron dengan <th> di atasnya
   (ini penyebab kolom Actions kelihatan miring). Tombolnya sendiri sudah inline-flex
   30x30 lewat rule .btn di bawah, jadi text-align:center saja cukup. */
#tabel_add td:last-child:not([colspan]) {
  text-align: center;
  white-space: nowrap;
}

#tabel_add td:last-child .btn + .btn {
  margin-left: 4px;
}

#tabel2 td:first-child .btn,
#tabel_add td:last-child .btn {
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

#tabel2 td:first-child .btn:hover,
#tabel_add td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel2 td:first-child .btn-success,
#tabel_add td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel2 td:first-child .btn-warning,
#tabel_add td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel2 td:first-child .btn-primary,
#tabel_add td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel2 td:first-child .btn-danger,
#tabel_add td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

/* btn-info tidak ada di palet purchaseOrder.blade.php (di sana tombol Otorisasi memakai
   btn-primary) - halaman ini memakai btn-info untuk tombol Otorisasi supaya beda warna
   dari tombol Print yang sudah memakai btn-primary, jadi tint-nya dilengkapi sendiri
   mengikuti bahasa desain yang sama (pastel + teks berwarna). */
#tabel2 td:first-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

/* ---------- #tabel_add (tabel item di form Add Item) dan #tabel_detail (tabel item
   di form Detail) - header abu-abu uppercase + zebra + hover, disalin dari
   purchaseOrder.blade.php. ---------- */
#tabel_add thead th,
#tabel_detail thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel_add tbody tr:nth-of-type(odd),
#tabel_detail tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel_add tbody tr:hover,
#tabel_detail tbody tr:hover { background-color: #f5f3ff; }

/* Qty / Sat / Actions rata tengah, header DAN isi, supaya lurus segaris. Selector
   pakai #id + :nth-child supaya menang atas .data-table thead th { text-align:left }
   di layout newmasterx. */
#tabel_add thead th:nth-child(n+3),
#tabel_add tbody td:nth-child(n+3):not([colspan]) {
  text-align: center;
}

/* #tabel_detail cuma 4 kolom (Kode, Nama, Qty, Sat) - Qty & Sat rata tengah. */
#tabel_detail thead th:nth-child(n+3),
#tabel_detail tbody td:nth-child(n+3):not([colspan]) {
  text-align: center;
}

/* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol Add Item,
   Submit Add/Edit, dan Batal di form - disalin dari purchaseOrder.blade.php. ---------- */
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

/* Batal = aksi sekunder, jadi abu-abu muda dengan teks gelap (bukan solid gelap). */
.btn-batal-add {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
}

.btn-batal-add:hover,
.btn-batal-add:focus {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

.btn-batal-add:active {
  background-color: #dee2e6 !important;
  border-color: #ced4da !important;
  color: #343a40 !important;
}

/* ---------- Modal cari kode barang (#formAddListItem) - baris diklik langsung untuk
   memilih (tidak ada lagi tombol "+" di kolom Actions), disalin dari
   resources/views/purchasing/modals/modalPOAdd.blade.php dengan scope #form diganti
   #formAddListItem. ---------- */
#formAddListItem tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}

#formAddListItem tbody tr.pick-row:hover td {
  background-color: #eef2ff;
}

#formAddListItem thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}

#formAddListItem tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

/* Kotak search dipermodern (ikon kaca pembesar + sudut membulat) mengikuti gaya
   .po-search-inp di public/css/po-table-header.css - tapi pencariannya TETAP menembak
   server saat Enter (searchBarangAll()), bukan menyaring instan seperti di Purchase Order. */
#formAddListItem #input_search_barang_all {
  width: 260px;
  max-width: 100%;
  font-size: 13px;
  padding: 7px 10px 7px 32px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}

#formAddListItem #input_search_barang_all:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Tombol di kolom Action baru muncul saat barisnya di-hover. Opt-in lewat kelas
   po-aksi-hover supaya tabel lain tidak ikut terpengaruh. visibility (bukan display)
   supaya lebar kolomnya tetap dipesan - tabel tidak melompat saat tombol muncul/hilang. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar. Bentuknya disalin dari
   purchaseOrder.blade.php (meniru .po-filter-wrap milik public/css/po-table-header.css)
   tapi ditulis di sini supaya perubahan ini cukup mengunggah file blade-nya saja. */
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
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
  background-repeat: no-repeat;
  background-position: right center;
}

/* ---------- Perataan kotak centang untuk semua tabel di halaman ini ----------
   Dikumpulkan di satu tempat supaya tabel utama maupun tabel di dalam modal
   berperilaku sama. Dua hal yang dibereskan:
   1. .form-check bawaan Bootstrap memberi padding-left pada wadahnya dan
      float:left pada inputnya, jadi text-center saja tidak membuat kotaknya
      benar-benar di tengah kolom.
   2. vertical-align:middle mengacu ke titik tengah x-height teks, sedangkan isi
      kolom sebelahnya huruf kapital/angka. Tanpa koreksi -2px kotak centangnya
      terlihat turun sedikit dibanding teks di sebelahnya. */
td .form-check {
  padding-left: 0;
  margin: 0;
  min-height: 0;
}
td input[type="checkbox"] {
  float: none;
  margin: 0;
  vertical-align: middle;
  position: relative;
  top: -2px;
  /* Ukuran diseragamkan lewat scale, bukan width/height, supaya bentuk kotaknya
     tetap bawaan browser. width/height dikembalikan ke auto supaya ukuran khusus
     dari class lain (mis. .pba-chk-lg di invoicepembelian) tidak membuat sebagian
     kotak jadi lebih besar dari yang lain. */
  width: auto;
  height: auto;
  transform: scale(1.5);
}
</style>
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}">
<input type="hidden" id="periode_bulan" value="{{ $periode->bulan }}">
<input type="hidden" id="akses_istambah" value="{{ $akses->ISTAMBAH }}">
<input type="hidden" id="akses_ishapus" value="{{ $akses->ISHAPUS }}">
<input type="hidden" id="akses_iskoreksi" value="{{ $akses->ISKOREKSI }}">
<input type="hidden" id="akses_iscetak" value="{{ $akses->ISCETAK }}">
<input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
<input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">


<div id="page1">
  <div class="card">
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="prTglAwal" value="{!! \Carbon\Carbon::create($periode->tahun, $periode->bulan, 1)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="prTglAkhir" value="{!! \Carbon\Carbon::create($periode->tahun, $periode->bulan, 1)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="prSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat prIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="prLen">Tampilkan</label>
          <select id="prLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="prBtnFilter" onclick="$('#modalFilterPR').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat prInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel2" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">Authorized</th>
            <th style="padding: 4px 12px;" scope="col">User Oto</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
            <th style="padding: 4px 12px;" scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelPR() lewat JS, sama seperti purchaseOrder.blade.php,
               supaya susunan kolom hasil geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
        </tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>
</div>

<!-- start modal add -->

<div id="page2" class="container-fluid" style="display:none;" >
<div id="formBsGrid">
  <div class="page-title">
    <div class="row">
      <div class="col-6 text-left">
        <!-- <h1>Form PR Non-Agen</h1> -->
      </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
        height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonCloseForm()">Close</button>
    </div>
    </div>

    </div>
  <div class="row">
    <!-- <div class="col-6 text-left">
      <h1>Form Pembelian Non-Agen</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
        height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonCloseForm()">Close</button>
    </div> -->
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <!-- <input type="hidden" id="input_kodegroup" value="" /> -->
            <input type="hidden" class="form-control" id="input_add_nourut" placeholder="No Urut" disabled>
          <div class="col-md-4">
            <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
          </div>
          <div class="col-md-4">
            <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>Departemen</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <select id="input_add_kodedepartemen" class="form-control text-left" aria-label="Default select example">
                  <option selected value="" disabled>Pilih Dept</option>
                </select>
              </div>
            </div>
          </div>
          </div>
          <div class="col-md-4">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-lg btn-chip-biru" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: background-color 0.3s, box-shadow 0.3s;"
            onclick="buttonAddAddItem()">Tambah Item</button>
        </div>
      </div>
    </div>
    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="data-table">
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td class="text-center" colspan="5">Belum ada barang</td>
                </tr>
              </tbody>
            </table>
          </div>
          {{-- <div class="text-right">
            <button type="button" class="btn btn-primary" style="
            height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAdd()">Submit</button>
        </div> --}}
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
    <!-- ADD SUBGROUP -->
    <div id="addAddItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-3" style="margin-top:5px;">
                    <div class="form-group">
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-left" placeholder="Kode Barang">
                  <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            <div class="row" style="margin-top:-15px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-left" disabled>
              </div>
            </div>
              <div class="row" style="margin-top:-15px;">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                  <label>Quantity</label>
                </div>
                </div>
                <div class="col-md-3">
                  <input id="input_add_add_qnt" type="text" value=0.00 class="form-control text-right input-partial-number">
                </div>
                <div class="col-md-2" style="margin-top:5px;">
                  <label for="input_add_add_satuan">Satuan</label>
                </div>
                <div class="col-md-3">
                  <select id="input_add_add_satuan" class="form-control">
                    <option value="" disabled selected>Pilih Satuan</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
            <div class="row">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                  <label>Keterangan</label>
                </div>
              </div>
                <div class="col-md-8">
                  <textarea type="text" style="width: 100%; resize: none" rows=3  class="form-control text-left" id="input_add_add_keterangan"></textarea>
                </div>
            </div>
          </div>
          </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-lg btn-batal-add" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()" >Batal</button>

                <button type="button" id="submitAddAdd" class="btn btn-lg btn-chip-biru" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddAdd()">Simpan</button>

                <button type="button" id="submitAddEdit" class="btn btn-lg btn-chip-biru" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddEdit()">Simpan</button>
              </div>
            </div>
          </div>

    <!-- END ADD ADD -->

    <!-- ADD EDIT -->

    <div id="addEditItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item Kedua</h4>
              </div>
            </div>

            {{-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_add_edit_nopocust" type="text" class="form-control" disabled>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">
                <button type="button" disabled onclick="" class="btn btn-primary">+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_keterangannama" type="text" class="form-control text-left" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2">
                <label for="input_add_edit_satuan">Satuan</label>
              </div>
              <div class="col-md-4">
                <select id="input_add_edit_satuan" class="form-control" name="satuan">
                  <option value="" selected disabled>Pilih Satuan</option>
                </select>
              </div>
            </div>
            <div class="row">


            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_add_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()">Batal</button>
                {{-- <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> --}}
              </div>
            </div>
          </div>

    <!-- END ADD EDIT -->

  </div>
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
</div>

</div>{{-- /#formBsGrid --}}
</div> {{-- end page 2 --}}


<!-- End modal add-->

<!-- start modal list item add -->
{{-- Disamakan persis dengan modal pemilih menu Kas (picker-kas). Kotak cari lama (#input_search_barang_all) diganti kotak Search bawaan DataTables - Enter di kotak itu tetap mencari ke server, lihat prTabelBarangInit()/searchBarangAll(). --}}
<div class="modal fade picker-kas" id="formAddListItem" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Master Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px; ">
              <table id="tabel_add_list_item">
                <thead>
                  <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>
                  <th scope="col">Part Number</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_item" class="text-left">
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid" style="display:none;">
<div id="formBsGrid">
        <div class="row">
          <div class="col-6 text-left">
            <!-- <h2>Detail Pembelian Non-Agen</h2> -->
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px;
            margin-top: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonCloseForm()">Close</button>
          </div>
        </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>Departemen</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <select disabled id="input_detail_kodedepartemen" class="form-control" aria-label="Default select example">
                      <!-- <option selected value="" disabled>Pilih Dept</option> -->
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
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
        </div>

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="data-table">
              <thead>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                </tr>
              </thead>
              <tbody id="tabel_data_detail" class="text-left" >
                <tr>
                  <td class="text-center" colspan="4">Belum ada barang</td>
                </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>
</div>{{-- /#formBsGrid --}}
</div>

<div class="modal fade" id="formHeaderTable" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Table Setting</h5>
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto"
          data-dismiss="modal" aria-label="Close"
          style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
          <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid mt-4">



          <div class="row">
            <div class="table-responsive">
            <table id="tabel_headertable" class="table table-bordered table-striped">
              <thead id="tabel_header_headertable" class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kolom</th>
                  <th scope="col">Tampil</th>
                </tr>
              </thead>
              <tbody id="tabel_data_headertable" class="text-left">
                <tr>
                  <td class="text-center" colspan="3">Silakan ketik pencarian</td>
                </tr>
              </tbody>
            </table>
          </div>
          </div>



        </div>

        <div class="row ">
          <div class="col-md-12 text-right">
            <div class="row">
              <div class="col-md-12">

              </div>
            </div>

          <button type="button" class="btn btn-primary" onclick="saveHeaderTable()" class="btn btn-secondary"  >Save</button>
      </div>
      </div>
      </div>



    </div>
  </div>
</div>


</div>
</div>
<!-- End modal detail-->

<!-- modal filter status/otorisasi Permintaan Pembelian Non-Agen -->
<div class="modal fade rt-filter" id="modalFilterPR">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter PR Non-Agen
          <span class="rt-active-badge" id="prFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPR').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="prModalStatus">Status</label>
              <select class="rt-native" id="prModalStatus">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
                <option value="Batal">Batal</option>
              </select>
            </div>
            <div>
              <label class="rt-field-label" for="prModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="prModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="prResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterPR').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="prTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter status/otorisasi Permintaan Pembelian Non-Agen -->

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">
let dataAddListItem = []
let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataEditListItem = []

let xisshown = []
let xheadertableheader = []
let xheadertablevalue = []
let xisnumeric = []



let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
      // negativeSignCharacter: 'z'
     }
  );
});

$(document).ready(function(){
  // grid = new gridjs.Grid({
  //       search: true,
  //       pagination: true,
  //       sort: true,
  //       columns: [
  //           "Kode Barang",
  //           "Nama Barang",
  //           "Qty"
  //       ],
  //       data: []
  //   });
  //
  //   grid.render(document.getElementById('tabel_wrapper'));

//   $('#tabel').DataTable({
//     destroy: true,
//     responsive: true,
//     lengthChange: false,
//     paging: false,
//     autoWidth: false
// });



  // === buat search barang di field inputan ===
  document.getElementById("input_add_add_kodebarang").addEventListener("keypress", function (e) {
  if (e.which == 13) {
    let search = this.value.trim();

    if (!search) {
      alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.");
      return;
    }


    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search: search,
        isagen: 0
      },
      success: function(res) {
        dataAddListItem = res;

        if (!res.length) {
          $('#formAddListItem').modal('show');
          prTabelBarangInit('', search, 'Tidak ada data');
          return;
        }

        if (res.length === 1) {
          buttonAddAddInsertItem(0);
          return;
        }

        $('#formAddListItem').modal('show');

        let rowTable = "";
        res.forEach((item, i) => {
          rowTable += `
            <tr class="pick-row" onclick="buttonAddAddInsertItem(${i})">
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>
              <td>${item.NAMAMERK ?? ''}</td>
              <td>${item.PartNumber ?? ''}</td>
            </tr>`;
        });

        prTabelBarangInit(rowTable, search);
      },
      error: function(err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser');
      }
    });
  }
});
console.log("ready");

// Idempotent - hanya benar-benar mengikat sekali seumur halaman, lihat definisinya.
prInitReportTableSekali()
loadAll()
});

function saveHeaderTable () {
  let href = window.location.pathname.split('/').filter(Boolean)[1];
  let _token = $("#_token").val();
  // console.log()

  console.log(JSON.stringify(xisshown))
  console.log(JSON.stringify(xheadertableheader))
  console.log(JSON.stringify(xheadertablevalue))
  console.log(JSON.stringify(xisnumeric))
  console.log(href)
  $.ajax({
    url: "{!! url('saveheadertable') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      header : JSON.stringify(xheadertableheader) ,
       isnumber : JSON.stringify(xisnumeric) ,
       tipe : '',
         value : JSON.stringify(xheadertablevalue) ,
          isshown : JSON.stringify(xisshown) ,

        href : href
    },
    success: function(res) {
      loadAll()
        $("#formHeaderTable").modal('toggle')

    }})


}

function buttonChangeOrder (type = 0, index =0) {
  console.log("buttonChangeOrder")
  console.log(type , index)

  // let xisshown = []
  // let xheadertableheader = []
  // let xheadertablevalue = []
  // let xisnumeric = []
  if (type == 0) {
    //naikkin posisi -1
    let tempisshown =  xisshown[index]
    let tempheadertableheader =  xheadertableheader[index]
    let tempheadertablevalue =  xheadertablevalue[index]
    let tempisnumeric =  xisnumeric[index]

    xisshown[index] = xisshown[index - 1]
    xheadertableheader[index] = xheadertableheader[index - 1]
    xheadertablevalue[index] = xheadertablevalue[index - 1]
    xisnumeric[index] = xisnumeric[index - 1]

    xisshown[index - 1] = tempisshown
    xheadertableheader[index - 1] = tempheadertableheader
    xheadertablevalue[index - 1] = tempheadertablevalue
    xisnumeric[index - 1] = tempisnumeric
    refreshHeaderTable()
  } else {
    // nurunin posisi +1
    let tempisshown =  xisshown[index]
    let tempheadertableheader =  xheadertableheader[index]
    let tempheadertablevalue =  xheadertablevalue[index]
    let tempisnumeric =  xisnumeric[index]

    xisshown[index] = xisshown[index + 1]
    xheadertableheader[index] = xheadertableheader[index + 1]
    xheadertablevalue[index] = xheadertablevalue[index + 1]
    xisnumeric[index] = xisnumeric[index + 1]

    xisshown[index + 1] = tempisshown
    xheadertableheader[index + 1] = tempheadertableheader
    xheadertablevalue[index + 1] = tempheadertablevalue
    xisnumeric[index + 1] = tempisnumeric
    refreshHeaderTable()

  }
}

function onclickcheckboxheadertable (index) {

    if (document.getElementById(`headertable_checkbox${index}`).checked) {
      xisshown[index] = 1

    } else {
      xisshown[index] = 0

    }

    console.log(xisshown)
}

function refreshHeaderTable () {
  // let href = window.location.pathname.split('/').filter(Boolean)[1];
  // console.log(href)
  // let _token = $("#_token").val();
  //
  // $.ajax({
  //   url: "{!! url('getheadertable') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token : _token,
  //     href
  //   },
  //   success: function(res) {
  //       console.log('======xxxxx==========')
  //     console.log(res)
  //     console.log(JSON.parse(res.isshown))
  //     console.log(JSON.parse(res.headertableheader))
  //     console.log(JSON.parse(res.headertablevalue))
  //     console.log(JSON.parse(res.isnumeric))
  //     xisshown = JSON.parse(res.isshown)
  //     xheadertableheader = JSON.parse(res.headertableheader)
  //     xheadertablevalue = JSON.parse(res.headertablevalue)
  //     xisnumeric = JSON.parse(res.isnumeric)
      let rowTable = ''
      console.log('len' , xheadertableheader.length)
      xheadertableheader.forEach((item, i) => {
        console.log(i)
        rowTable +=  `<tr>`
        rowTable += `<td class="text-center">`
        if (i != 0) {
          console.log("button down")
          rowTable +=  `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(0 , ${i})"><i class="bi bi-arrow-up"></i></button>`
        } else {
            rowTable +=  `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-up" disabled></i></button>`
        }
        if (i != xheadertableheader.length - 1 ) {

            console.log("button up")
            rowTable += `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(1 , ${i})"><i class="bi bi-arrow-down"></i></button>`
        } else {
            rowTable += `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-down" disabled></i></button>`
        }

        rowTable += `</td>`
          rowTable+= `  <td>${item}</td>
            <td class="text-center"><input class="" type="checkbox" value="" onchange='onclickcheckboxheadertable(${i})' id="headertable_checkbox${i}"></td>





        `
        rowTable += `</tr>`
      });



      document.getElementById("tabel_data_headertable").innerHTML = rowTable
      console.log(xisshown)
      xheadertableheader.forEach((item, i) => {
        console.log(xisshown[i])
        console.log(Number(xisshown[i]))
        if (Number(xisshown[i])) {

          document.getElementById(`headertable_checkbox${i}`).checked = true
        }

      });
      // $("#formHeaderTable").modal('toggle')



  //   }
  // })

}

function buttonHeaderTable () {
    let href = window.location.pathname.split('/').filter(Boolean)[1];
    console.log(href)
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('getheadertable') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        href
      },
      success: function(res) {
          console.log('======xxxxx==========')
        console.log(res)

        // if (res.isparsed == 0) {
        //   xisshown = JSON.parse(res.isshown)
        //   xheadertableheader = JSON.parse(res.headertableheader)
        //   xheadertablevalue = JSON.parse(res.headertablevalue)
        //   xisnumeric = JSON.parse(res.isnumeric)
        //
        //
        // } else {
          xisshown = res.isshown
          xheadertableheader = res.headertableheader
          xheadertablevalue = res.headertablevalue
          xisnumeric = res.isnumeric


        // }
        // console.log(JSON.parse(res.isshown))
        // console.log(JSON.parse(res.headertableheader))
        // console.log(JSON.parse(res.headertablevalue))
        // console.log(JSON.parse(res.isnumeric))

        refreshHeaderTable()
        $("#formHeaderTable").modal('toggle')

      }
    })



}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}


// Dipatok, bukan diambil dari window.location: PembelianPermintaanNonAgenController@index
// memakai $req->path() dan HeaderTableController@getHeaderTable membandingkan
// $req->href == 'pembelianpermintaannonagen'. Kalau ketiganya tidak sama persis,
// pengaturan kolom yang tersimpan tidak akan pernah terbaca lagi.
const PR_HREF = 'pembelianpermintaannonagen'

let prCart = []
let dataPR = []

function prBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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
      tipe === 0 ? h : values[i],        // 0 nama field di data
      label,                             // 1 judul kolom
      Number(isshowns[i]) === 1 ? 1 : 0, // 2 tampil
      tipeNama[tipe] || 'varchar',       // 3 tipe data
      0,                                 // 4 total (tidak dipakai halaman ini)
      isNaN(des) ? 0 : des,              // 5 jumlah desimal
      h,                                 // 6 header asli
      values[i],                         // 7 value asli
      tipe                               // 8 isnumeric asli
    ])
  });
  return cart
}

// Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan, karena
// ReportTable.headHtml() memakai indexOf() terhadap gcart_header untuk mendapat
// indeks global tiap kolom.
function prKolomTampil () {
  return (prCart || []).filter(c => Number(c[2]) === 1)
}

function prKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

// formatAngka() selalu menempelkan '.' + bagian desimal, sehingga input tanpa titik
// (mis. hasil toFixed(0)) jadi "123.undefined". Dipakai versi yang sadar jumlah desimal,
// sama seperti poFormatAngkaDes() di purchaseOrder.blade.php.
function prFormatAngkaDes (nilai, des) {
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

function prRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return prFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman harus tetap tampil:
// judul kolomnya jatuh ke <th> biasa, hanya tanpa drag & roda gigi.
function prHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman.
// report-table.js tidak punya teardown; render ulang selanjutnya hanya menulis ulang
// innerHTML-nya (lihat renderTabelPR()) - sama seperti purchaseOrder.blade.php.
let prRtSudahInit = false

function prInitReportTableSekali () {
  if (prRtSudahInit || typeof ReportTable === 'undefined') { return }
  prRtSudahInit = true

  ReportTable.init({
    table    : '#tabel2',
    bar      : '#rtBar',
    onChange : renderTabelPR
  })

  // DataTables memasang handler sort LANGSUNG di tiap <th> (bukan didelegasikan), sedangkan
  // roda gigi/drag milik report-table.js didelegasikan di <thead> lewat listener fase bubble.
  // Tanpa penanganan khusus, klik roda gigi juga memicu sort DataTables. Solusinya: hentikan
  // event ASLINYA sebelum sempat mencapai <th> (fase capture, di <thead>), lalu tembakkan ULANG
  // satu event click baru langsung ke <thead> dengan target di-override - lihat penjelasan
  // lengkapnya di poInitReportTableSekali() pada purchaseOrder.blade.php.
  let prGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (prGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      prGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      prGuardUlangKlik = false
    }, true)
  }
}

// Pastikan #rtBar duduk tepat sebelum tabel (sibling, bukan anak di dalam wrapper DataTables) -
// lihat catatan panjang di poPindahBar() pada purchaseOrder.blade.php soal kenapa ini penting.
function prPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabel2')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabel2')) {
    acuan = document.getElementById('tabel2_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

// Ikat kotak search custom (#prSearch, statis di blade - di luar #tabel2_wrapper jadi
// tidak ikut terhapus saat .DataTable().destroy() menulis ulang wrapper). Diikat sekali
// lewat dataset.rtBound karena renderTabelPR() memanggil ini tiap kali tabel di-destroy+init.
function prIkatSearch () {
  let input = document.getElementById('prSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

// Jumlah baris per halaman, dikendalikan dropdown #prLen. Disimpan di variabel, bukan
// hanya dibaca dari elemen select-nya, karena renderTabelPR() melakukan destroy+init
// tiap kali kolom digeser/disembunyikan - tanpa ini tabel selalu balik ke nilai awal
// walau dropdownnya masih menunjuk pilihan pengguna. Nilai -1 berarti "semua data".
let prPanjangHalaman = 10
function prIkatPanjangHalaman () {
  let sel = document.getElementById('prLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(prPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    prPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(prPanjangHalaman).draw()
  })
}

// Ubah salah satu tanggal periode -> muat ulang data.
function prIkatPeriode () {
  let awal  = document.getElementById('prTglAwal')
  let akhir = document.getElementById('prTglAkhir')
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

// Kotak scroll tabel dibuat setinggi sisa ruang di #content, sama seperti
// poAturTinggiTabel() di purchaseOrder.blade.php.
function prAturTinggiTabel () {
  let area = document.getElementById('content')
  let wrap = document.querySelector('#page1 .po-table-wrap')
  if (!area || !wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let pageEl = document.getElementById('page1')
  let bawah = pageEl.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

function prAngka (v) {
  let n = Number(v)
  return isNaN(n) ? 0 : n
}

// Qnt/QntBatal/QntPO adalah angka per BARANG, sedangkan satu baris tabel adalah satu
// No Bukti yang bisa berisi banyak barang - jadi dijumlahkan dulu se-grup.
//   sisa = Qnt - QntBatal
//   sisa <= 0    -> Batal (qty habis dibatalkan)
//   QntPO >= sisa -> Sudah (sudah ditarik penuh ke Purchase Order)
//   selain itu   -> Belum
// Cek Batal HARUS lebih dulu: saat sisa 0, syarat (po >= sisa) juga ikut benar dan akan
// salah terbaca sebagai "Sudah".
function prStatusPR (grup) {
  let qnt = 0, batal = 0, po = 0
  ;(grup || []).forEach((d) => {
    qnt   += prAngka(d.Qnt)
    batal += prAngka(d.QntBatal)
    po    += prAngka(d.QntPO)
  })

  let sisa = qnt - batal
  if (sisa <= 0)  { return 'Batal' }
  if (po >= sisa) { return 'Sudah' }
  return 'Belum'
}

// Warna disamakan dengan kolom Status di Purchase Order.
const PR_BADGE_STATUS = {
  'Sudah' : 'is-active',
  'Belum' : 'is-user',
  'Batal' : 'is-inactive'
}

function prBadgeStatus (grup) {
  let s = prStatusPR(grup)
  return `<span class="sp-badge ${PR_BADGE_STATUS[s] || ''}">${s}</span>`
}

function prOtorisasiPR (grup) {
  return Number(grup[0].IsOtorisasi1) ? 'Sudah' : 'Belum'
}

// 'SEMUA' = tidak menyaring. Disimpan di luar renderTabelPR() supaya tetap berlaku saat
// tabel digambar ulang (ganti periode, sehabis simpan, dst).
let prFilterStatus = 'SEMUA'
let prFilterOtorisasi = 'SEMUA'

function prUpdateFilterBadge () {
  let jml = 0
  if (prFilterStatus !== 'SEMUA') { jml++ }
  if (prFilterOtorisasi !== 'SEMUA') { jml++ }
  let badge = document.getElementById('prFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function prTerapkanFilter () {
  prFilterStatus = $('#prModalStatus').val() || 'SEMUA'
  prFilterOtorisasi = $('#prModalOtorisasi').val() || 'SEMUA'
  prUpdateFilterBadge()
  $('#modalFilterPR').modal('hide')
  renderTabelPR()
}

function prResetFilter () {
  prFilterStatus = 'SEMUA'
  prFilterOtorisasi = 'SEMUA'
  $('#prModalStatus').val('SEMUA')
  $('#prModalOtorisasi').val('SEMUA')
  prUpdateFilterBadge()
  $('#modalFilterPR').modal('hide')
  renderTabelPR()
}

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan: report-table.js sudah punya fallback yang memutasi gcart_header
 * sendiri lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke
 * doSimpanHeader di bawah. Jadi yang perlu disediakan hanya dua fungsi ini.
 */
window.g_href = PR_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  prCart.forEach((c) => {
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
      // DBHEADERTABLE tidak punya kolom desimal; kolom `tipe` dipakai untuk itu
      // karena selama ini ditulis kosong dan tidak pernah dibaca balik.
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : PR_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Dipakai tombol "Reset kolom" di bar. Tombol itu hanya muncul kalau fungsi ini ada.
// Harus async:false karena report-table.js langsung menggambar ulang setelahnya.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : PR_HREF,
      reset  : 1
    },
    success : function (res) {
      prCart = prBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = prCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function loadAll () {
  let _token = $("#_token").val()
  let tglawal = $('#prTglAwal').val()
  let tglakhir = $('#prTglAkhir').val()

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenloadall') !!}",
    type: "post",
    data: {
      _token,
      tglawal,
      tglakhir,
      // Selalu SEMUA - penyaringan otorisasi dipindah ke sisi browser di renderTabelPR(),
      // sama seperti Purchase Order, supaya filter bertahan tanpa menembak server ulang.
      isoto: 2,
      href: PR_HREF
    },
    success: function (res) {
      prCart = prBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = prCart
      dataPR = res.listData1 || []
      renderTabelPR()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data Permintaan Pembelian Non-Agen')
    }
  })
}

function renderTabelPR () {
  window.g_modeReport = 1
  window.gcart_header = prCart

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  // Satu daftar kolom untuk header DAN isi baris, supaya jumlah kolomnya selalu sama.
  let cols = prKolomTampil()
  let kolomRender = cols.map(prKolomRender)

  // <thead> HANYA ditulis ulang innerHTML-nya, elemennya sendiri tidak diganti -
  // sudah diikat sekali oleh prInitReportTableSekali().
  let thead = document.getElementById('tabel_header')
  thead.innerHTML = prHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Authorized</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
      <th style="padding: 4px 12px;" scope="col">Status</th>
    `)
  }

  let dataTampil = dataPR || []
  if (prFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (grup) { return prOtorisasiPR(grup) === prFilterOtorisasi })
  }
  if (prFilterStatus !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (grup) { return prStatusPR(grup) === prFilterStatus })
  }

  let rowTable = ''
  dataTampil.forEach((grup) => {
    let item = grup[0]
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}', '${item.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint('${item.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}', '${item.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${prRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${prRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${isOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
      <td class="text-center">${prBadgeStatus(grup)}</td>
    </tr>`
  });

  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: prPanjangHalaman,
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
    // Actions). Data sudah datang terurut dari server (Tanggal/NoBukti terbaru dulu),
    // jadi di sini cukup dipertahankan urutan DOM apa adanya.
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
  });

  prPindahBar()
  prIkatSearch()
  prIkatPanjangHalaman()
  prIkatPeriode()
  // Init DataTable di atas mereset filter pencarian - kotak #prSearch sendiri statis
  // di blade dan nilainya tidak ikut hilang, jadi diterapkan ulang di sini.
  let inputSearch = document.getElementById('prSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  prAturTinggiTabel()
}

function buttonOtorisasi (nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenupdateotorisasi') !!}",
    type: "post",
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res && res.ok > 0) {
        alertify.success('Berhasil otorisasi');
        let grup = dataPR.find(function (g) { return g[0].NoBukti === nobukti })
        if (grup) {
          grup.forEach(function (item) {
            item.IsOtorisasi1 = 1
            item.OtoUser1 = res.otouser
            item.TglOto1 = res.tgloto
          })
        }
        renderTabelPR()
      } else {
        alertify.warning('Gagal otorisasi');
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}


function buttonBatalOtorisasi (nobukti, isOtorisasi) {

console.log( $("#akses_isbatal").val());
let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access batal');
    return;
  }

  if (Number(isOtorisasi) === 0) {
    alertify.warning('Belum diotorisasi');
    return;
  }

alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
    let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('pembelianpermintaannonagenupdatebatalotorisasi') !!}",
        type: "post",
        data: {
          _token,
          nobukti,
          otorisasi: 0,
          pket :value
        },
        success: function (res) {
          if (res && res.ok > 0) {
            alertify.success('Berhasil batal otorisasi');
            let grup = dataPR.find(function (g) { return g[0].NoBukti === nobukti })
            if (grup) {
              grup.forEach(function (item) {
                item.IsOtorisasi1 = 0
                item.OtoUser1 = ''
                item.TglOto1 = null
              })
            }
            renderTabelPR()
          } else {
            alertify.warning('Gagal batal otorisasi');
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
        }
      });
  },
  function() {
    alertify.error("Action cancelled");
  }
);




}


function submitAddEdit () {
    console.log('submitAddEdit');

    let checkDate = new Date($("#input_add_tanggal").val());
    let periode_bulan = document.getElementById("periode_bulan").value;
    let periode_tahun = document.getElementById("periode_tahun").value;

    if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
        alertify.warning("Tanggal tidak sesuai periode");
        return;
    }

    let jmlrecord = (tipeform === "edit") ? 1 : 0;

    let _token = $("#_token").val();
    let choice = "U";
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let tanggal = $("#input_add_tanggal").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = formatAngkaVal($("#input_add_add_qnt").val())
    let keterangan = $("#input_add_add_keterangan").val();
    let kodedepartemen = $("#input_add_kodedepartemen").val();

    if (!kodebarang || !satuan || qnt <= 0 || !kodedepartemen) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = tempEdit;
    let isi = 0;
    let nosat = parseInt(satuan);
    let qnt1 = 0;

    console.log('Satuan dipilih:', satuan);
    console.log('SAT1:', tempEdit.SAT1, 'ISI1:', tempEdit.ISI1);
    console.log('SAT2:', tempEdit.SAT2, 'ISI2:', tempEdit.ISI2);
    console.log('SAT3:', tempEdit.SAT3, 'ISI3:', tempEdit.ISI3);

    if (nosat === 1) {
    qnt1 = qnt * tempEdit.ISI1;
    satuan = tempEdit.SAT1;
    isi = parseFloat(String(tempEdit.ISI1).replace(/\./g,''));
    } else if (nosat === 2) {
        qnt1 = qnt * tempEdit.ISI2;
        satuan = tempEdit.SAT2;
        isi = parseFloat(String(tempEdit.ISI2).replace(/\./g,''));
    } else if (nosat === 3) {
        qnt1 = qnt * tempEdit.ISI3;
        satuan = tempEdit.SAT3;
        isi = parseFloat(String(tempEdit.ISI3).replace(/\./g,''));
    } else {
        alertify.warning("Satuan tidak valid");
        return;
    }

    console.log('Nosat:', nosat, 'Isi:', isi, 'Qnt1:', qnt1);

    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan ? keterangan.replace(/["']/g, '') : '';

    console.log("Data yang akan dikirim:", {
        choice, nobukti, nourut, tanggal, kodedepartemen,
        kodebarang, keterangannama, satuan, qnt, nosat, isi,
        keterangan, urut: tempEdit.Urut, jmlrecord
    });

    $.ajax({
        url: "{!! url('pembelianpermintaannonagenspadd') !!}",
        type: "POST",
        async: false,
        data: {
            _token,
            choice,
            nobukti,
            nourut,
            tanggal,
            kodedepartemen,
            isjasa: 0,
            pagen: 0,
            pjasa: 0,
            kodebarang,
            keterangannama,
            satuan,
            qnt,
            keterangan,
            nosat,
            isi,
            urut: tempEdit.Urut,
            isclose: 0,
            isclosed: 0,
            noso: '',
            urutso: 0,
            nopocust: '',
            jmlrecord
        },
        success: function(res) {
            console.log('respoedit', res);
            loadAll();
            $('.showhide').hide();
            refreshDataTableAdd(nobukti);
            alertify.success('Berhasil edit item');
        },
        error: function(err) {
            console.log('Error saat submit:', err);
            alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
    });
}

function buttonAddEditItem (index) {
  tipeformitem = 'edit';
  let _token = $("#_token").val();
  console.log('buttonAddEditItem');

  $('.showhide').hide();
  document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("input_add_add_kodebarang").disabled = true;

  tempEdit = dataTableAdd[index];
  tempIndexEdit = index;

  // Isi dropdown satuan
  let selectOption = '<option value=0 selected>Pilih Satuan</option>';
  if (tempEdit.SAT1) {
    selectOption += `<option value=1>${tempEdit.SAT1} - ${Number(String(tempEdit.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempEdit.SAT2) {
    selectOption += `<option value=2>${tempEdit.SAT2} - ${Number(String(tempEdit.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempEdit.SAT3) {
    selectOption += `<option value=3>${tempEdit.SAT3} - ${Number(String(tempEdit.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = selectOption;

  // Isi input
  document.getElementById("input_add_add_kodebarang").value = tempEdit.KodeBrg || '';
  document.getElementById("input_add_add_keterangannama").value = tempEdit.NamaBrg || '';
  document.getElementById("input_add_add_qnt").value = parseFloat(tempEdit.Qnt || 0).toFixed(2);
  document.getElementById("input_add_add_keterangan").value = tempEdit.Keterangan || '';
  document.getElementById("input_add_add_satuan").value = String(tempEdit.NoSat || 0);

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti) {




  tipeform = 'edit'
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhide').hide();
  lockFormAdd()

  // document.getElementById("input_add_tanggal").disabled = true

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_add_kodedepartemen").innerHTML = selectDept
    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
    //seharusnya ini pakai data table add
      dataTableAdd = res
  }})
  let rowTable = ``
  dataTableAdd.forEach((item, i) => {
    rowTable += `<tr>
    <td>${item.KodeBrg}</td>
    <td>${item.NamaBrg}</td>
    <td class="text-center">${formatAngka(item.Qnt)}</td>
    <td class="text-center">${item.Satuan}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem('${i}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem('${i}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });

  let date = new Date(dataTableAdd[0].Tanggal);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day);
  $('#input_add_tanggal').val(date1)

  document.getElementById("tabel_data_add").innerHTML  = rowTable
  document.getElementById("input_add_nobukti").value  = dataTableAdd[0].NoBukti
  document.getElementById("input_add_nourut").value  = dataTableAdd[0].Nourut
  document.getElementById("input_add_kodedepartemen").value  = dataTableAdd[0].KDDep
  // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal
  $('#page1').hide();
  $('#page2').show();
}



function buttonAddDeleteItem (index) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let data = dataTableAdd[index]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KodeBrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"
        let nourut = $("#input_add_nourut").val();
        let nobukti = $("#input_add_nobukti").val();
        let tanggal = $("#input_add_tanggal").val();
        let isjasa = 0
        let pagen = 0
        let pjasa = 0
        let urut = data.Urut
        let kodebarang = data.KodeBrg
        let qnt = data.Qnt
        let nosat = data.NoSat
        let satuan = data.Satuan
        let isi = data.Isi
        let keterangan = data.Keterangan
        let isclose = 0
        let isclosed =0
        let kddep = "TEMP"
        let keterangannama = data.NamaBrg
        let noso = data.NOSO
        let urutso = data.URUTSO
        let nopocust = data.NoSOCust
        let jmlrecord = 0

        $.ajax({
          url: "{!! url('pembelianpermintaannonagenspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nourut,
            nobukti,
            tanggal,
            isjasa,
            pagen,
            pjasa,
            urut,
            kodebarang,
            qnt,
            nosat,
            satuan,
            isi,
            keterangan,
            isclose,
            isclosed,
            kddep,
            keterangannama,
            noso,
            urutso,
            nopocust,
            jmlrecord,
          },
          success: function(res) {
            alertify.success("Item sudah di delete");
            refreshDataTableAdd(nobukti)
            loadAll()


        }})
      }
    ,function(){
      console.log('no')
    });
}


function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      // isagen: 0
    },
    success: function(res) {
      console.log('dept' , res)
      let selectDept = ``
      res.forEach((item, i) => {
        selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
      });

      document.getElementById("input_detail_kodedepartemen").innerHTML = selectDept

    }})

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg}</td>
        <td class="text-center">${formatAngka(item.Qnt)}</td>
        <td class="text-center">${item.Satuan}</td>
        </tr>`
      });

      let date = new Date(res[0].Tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NoBukti
      document.getElementById("input_detail_kodedepartemen").value = res[0].KDDep
      // document.getElementById("input_detail_tanggal").value  = res[0].Tanggal

  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function buttonAdd () {


let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {


    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return

}



  tipeform = 'add'
  $('.showhide').hide();
  cleanFormAdd()
  unlockFormAdd();

  let akses = $("#akses_istambah").val();
  console.log("Akses" , akses)
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  // pembelianpermintaannonagenspnobukti
  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []
    cleanFormAdd()

    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistdepartemen') !!}",
      type: "get",
      async: false,
      data: {
        // isagen: 0
      },
      success: function(res) {
        console.log('dept' , res)
        let selectDept = `<option selected value="" disabled>Pilih Dept</option>`
        res.forEach((item, i) => {
          selectDept += `<option value="${item.KDDEP}">${item.KDDEP} - ${item.NMDEP}</option>`
        });

        document.getElementById("input_add_kodedepartemen").innerHTML = selectDept

      }})

  refreshDataTableAdd()
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function closeListItemAdd () {
  $("#formAddListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("page2");
  modal.style.display = "block";

}

function closeListItemEdit () {
  $("#formEditListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("formEdit");
  modal.style.display = "block";
}

function buttonCloseForm () {
  $('#page2').hide();
  $('#page3').hide();
  $('#page1').show();
  prAturTinggiTabel()
}

function buttonAddListKodeBarang () {
  prTabelBarangInit('', '');

  $('#formAddListItem').modal('show');
}

// Kotak cari lama (#input_search_barang_all) sudah diganti kotak Search DataTables, yang
// dikosongkan lagi setiap modal dibuka lewat prTabelBarangInit('', '') di atas.
// $('#formAddListItem').on('hidden.bs.modal', function () {
//   $('#input_search_barang_all').val('');
// });


/* Modal Master Barang - tampilan disamakan dengan modal pemilih menu Kas (picker-kas):
   dropdown "Tampilkan" + kotak Search bawaan DataTables (lihat public/js/picker-kas.js).
   Data barang tetap dicari ke server (listBarang() hanya mengembalikan data kalau ada kata
   kunci), jadi kotak Search DataTables itu sekaligus menjadi kotak cari server: tekan Enter
   -> searchBarangAll(). Sambil mengetik, DataTables tetap menyaring hasil yang sedang tampil. */
function prTabelBarangInit (rowTable, cari, pesanKosong) {
  $('#tabel_data_add_list_item').html(rowTable || '');
  pickerKasInit('tabel_add_list_item', {
    search: { search: cari || '' },
    language: {
      lengthMenu: 'Tampilkan _MENU_',
      emptyTable: pesanKosong || 'Ketik kode / nama barang di kotak Search, lalu tekan Enter',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });
  $('#tabel_add_list_item_filter input')
    .off('keypress.prBarang')
    .on('keypress.prBarang', searchBarangAll)
    .trigger('focus');
}
function searchBarangAll (e) {
  if (e.which == 13) {
    let search = $(e.target).val().trim();

    if (!search) {
      prTabelBarangInit('', '');
      return;
    }


    $.ajax({
      url: "{!! url('pembelianpermintaannonagenlistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search,
        isagen : 0
      },
      success: function (res) {
        dataAddListItem = res;
        let rowTable = "";

        if (!res.length) {
          prTabelBarangInit('', search, 'Tidak ada data');
          return;
        }

        res.forEach((item, i) => {
          rowTable += `
            <tr class="pick-row" onclick="buttonAddAddInsertItem(${i})">
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>
              <td>${item.NAMAMERK ?? ''}</td>
              <td>${item.PartNumber ?? ''}</td>
            </tr>`;
        });

        prTabelBarangInit(rowTable, search);
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser');
      }
    });
  }
}

function buttonAddAddInsertItem (i) {
  console.log('index:', i);
  console.log('dataAddListItem:', dataAddListItem);

  if (!dataAddListItem[i]) {
    alertify.warning('Data tidak valid');
    return;
  }

  let item = dataAddListItem[i];

  $('#input_add_add_kodebarang').val(item.KODEBRG);
  $('#input_add_add_keterangannama').val(item.NAMABRG);

  let satuanOptions = '';
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1} - ${Number(String(item.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2} - ${Number(String(item.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3} - ${Number(String(item.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  $('#input_add_add_satuan').html(satuanOptions);

  $('#formAddListItem').modal('hide');

  setTimeout(() => {
    document.getElementById("input_add_add_qnt").focus();
    document.getElementById("input_add_add_qnt").select();
  }, 300);
}

  function submitAddAdd () {
    console.log('submitAddAdd');

    let checkDate = new Date($("#input_add_tanggal").val())

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    let jmlrecord = 0
    if (tipeform == 'edit'){
      jmlrecord = 1
    }
    let _token = $("#_token").val();
    let choice = "I";
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let tanggal = $("#input_add_tanggal").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = formatAngkaVal($("#input_add_add_qnt").val())
    let keterangan = $("#input_add_add_keterangan").val();
    let kodedepartemen = $("#input_add_kodedepartemen").val();

    if (!kodebarang || !satuan || qnt <= 0 || !kodedepartemen) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = dataAddListItem.find(item => item.KODEBRG === kodebarang);
    if (!barang) {
        alertify.warning("Barang tidak ditemukan di daftar");
        return;
    }

    let isi = 0;
    let nosat = 0;
    if (satuan === barang.SAT1) {
        isi = parseFloat(String(barang.ISI1).replace(/\./g,''));
        nosat = 1;
    } else if (satuan === barang.SAT2) {
        isi = parseFloat(String(barang.ISI2).replace(/\./g,''));
        nosat = 2;
    } else if (satuan === barang.SAT3) {
        isi = parseFloat(String(barang.ISI3).replace(/\./g,''));
        nosat = 3;
    } else {
        alertify.warning("Satuan tidak valid");
        return;
    }

    // untuk mencegah SQL error (hapus tanda kutip)
    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan ? keterangan.replace(/["']/g, '') : '';

    console.log({
          _token,
          choice,
          nobukti,
          nourut,
          tanggal,
          kodedepartemen,
          isjasa: 0,
          pagen: 0,
          pjasa: 0,
          kodebarang,
          keterangannama,
          satuan,
          qnt,
          keterangan,
          nosat,
          isi,
          urut: 0,
          isclose: 0,
          isclosed: 0,
          noso: '',
          urutso: 0,
          nopocust: '',
          jmlrecord})

    $.ajax({
        url: "{!! url('pembelianpermintaannonagenspadd') !!}",
        type: "POST",
        async: false,
        data: {
          _token,
          choice,
          nobukti,
          nourut,
          tanggal,
          kodedepartemen,
          isjasa: 0,
          pagen: 0,
          pjasa: 0,
          kodebarang,
          keterangannama,
          satuan,
          qnt,
          keterangan,
          nosat,
          isi,
          urut: 0,
          isclose: 0,
          isclosed: 0,
          noso: '',
          urutso: 0,
          nopocust: '',
          jmlrecord
        },
        success: function (res) {
            console.log('respoadd', res)
            if (res == 1) {
                loadAll()
                tipeform = 'edit'
                cleanFormAddAdd()
                refreshDataTableAdd(nobukti)
                alertify.success('Berhasil menambah item')
              }
              else if (res == 2) {
                setNewNoBukti()
                alertify.warning('Nobukti telah direfresh silahkan submit ulang')
              }
            },
            error: function (err) {
              console.log(err)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
              }
          });
      }


function buttonEditAddItem () {

let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {


    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return

}

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  $('.showhideedit').hide();

  tempEditAdd = {}
  document.getElementById("input_edit_add_refso").value = "-"
  document.getElementById("input_edit_add_nopocust").value = ""
  document.getElementById("input_edit_add_kodebarang").value = ""
  document.getElementById("input_edit_add_keterangannama").value = ""
  document.getElementById("input_edit_add_qnt").value = "0.00"
  document.getElementById("input_edit_add_keterangan").value = ""
  document.getElementById("input_edit_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'

  $('#editAddItem').show();
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();
  tempAdd = {}
  // document.getElementById("inlineRadio1").checked = false
  // document.getElementById("input_add_add_refso").value = "-"
  // document.getElementById("input_add_add_nopocust").value = ""
  document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
  document.getElementById("input_add_add_keterangannama").value = ""
  document.getElementById("input_add_add_qnt").value = "0.00"
  document.getElementById("input_add_add_keterangan").value = ""
  // Menentukan isi dropdown berdasarkan tempAdd
  let satuanOptions = `<option value="" selected disabled>Pilih Satuan</option>`;

  if (tempAdd.SAT1) {
    satuanOptions += `<option value="${tempAdd.SAT1}">[1] ${tempAdd.SAT1} - ${Number(String(tempAdd.ISI1||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempAdd.SAT2) {
    satuanOptions += `<option value="${tempAdd.SAT2}">[2] ${tempAdd.SAT2} - ${Number(String(tempAdd.ISI2||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  if (tempAdd.SAT3) {
    satuanOptions += `<option value="${tempAdd.SAT3}">[3] ${tempAdd.SAT3} - ${Number(String(tempAdd.ISI3||0).replace(/\./g,'')).toLocaleString('id-ID')}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = satuanOptions;


  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
}

function closeShowHideAdd () {
  $('.showhide').hide();
}

function closeShowHideEdit () {
  $('.showhideedit').hide();
}


function refreshDataTableAdd (NOBUKTI = "") {
  console.log('refreshDataTableAdd', NOBUKTI)

  if (!NOBUKTI) {
    let rowTable = `<tr>
      <td class="text-center" colspan="5">Belum ada barang</td>
    </tr>`
    document.getElementById("tabel_data_add").innerHTML = rowTable
    return
  }

  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('pembelianpermintaannonagenspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log('res', res)

      if (!res.length) {
        alertify.warning("Data habis")
        $('#page3').hide();
        $('#page2').hide();
        $('#page1').show();
        return
      }

      dataTableAdd = res
      dataHeaderAdd = res[0]

      let rowTable = ""
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
            <td class="text-center">${formatAngka(item.Qnt)}</td>
            <td class="text-center">${item.Satuan}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`
      });

      if (!dataTableAdd.length) {
        rowTable = '<tr><td colspan="5" class="text-center">Belum ada item</td></tr>';
      }

      document.getElementById("tabel_data_add").innerHTML = rowTable
      // document.getElementById("input_add_nobukti").value = dataHeaderAdd.nobukti
      // document.getElementById("input_add_kodedepartemen").value = dataHeaderAdd.kodedepartemen
    }
  })
}


function cleanFormAddAdd (){
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_keterangannama").value = ''
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  document.getElementById("input_add_add_keterangan").value = ''
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kodedepartemen").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("input_add_kodedepartemen").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_kodedepartemen").disabled = false
}


function formatAngka (angkaString) {
      if (!Number(angkaString)) {
        return '0.00'
      }
      angkaString = parseFloat(angkaString).toFixed(2)

      let tempAngka = angkaString.split('.')

      if (tempAngka[0][0] == '-') {
        let temp2=''

        let tempAngka1 = tempAngka[0].split('-')
        for (let i = 0; i < tempAngka1[1].length; i++) {
          if (i != 0 && i % 3 == 0) {
            temp2 = ',' + temp2
          }
          temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

        }
        temp2 += '.' + tempAngka[1]
        temp2 = '-' + temp2

        return temp2
      }
      let temp1 = ''
      for (let i = 0; i < tempAngka[0].length; i++) {
        if (i != 0 && i % 3 == 0) {
          temp1 = ',' + temp1
        }
        temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1

      }
      temp1 += '.' + tempAngka[1]
      return temp1
    }



function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''


    $.ajax({
      url: "{!! url('purchaseRequestCetak') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7)
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .border-bottom {
        border: bottom;
      }
      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. PRAMUKA NO. 63 RT. 11 BANJARMASIN 70249</div>
                        <div class="pb-1" style="width: 100%">TELP : 0511 - 3269593 | FAX : 0511 - 3272142</div>
                        <div class="pb-1" style="width: 100%">E-Mail : spl@indo.net</div>
                      </div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">SURAT PERMINTAAN PEMBELIAN</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. SPP</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Nobukti}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. Ref SO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOSO}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NO. PO Cust</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoSOCust || ''}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${tanggalOnly}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td colspan=6>Harap disediakan bahan / barang sebagai berikut :</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 5%">Kode Barang</td>
                    <td class="text-center" style="width: 30%">Nama Barang</td>
                    <td class="text-center" style="width: 5%">Sat</td>
                    <td class="text-center" style="width: 5%">PR</td>
                    <td class="text-center" style="width: 5%">Keterangan</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.kodebrg}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 30%;">${itemSub.NamaBrg}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: center;">${itemSub.Sat}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: right;">${itemSub.Qnt ? formatAngka(itemSub.Qnt) : ''}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.keterangan}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows � table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

  tempPrintStr += `</tbody>`;
  tempPrintStr += `</table>`;

  tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Dibuat Oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui oleh,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Diterima oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
      </tr>
    </table>
  </div>

</div>
`


    tempPrintStr += `</div>`
  });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }


      function formatAngkaVal (angka) {
        return Number(angka.split(',').join(''))
      }


</script>

{{-- script buat hover belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    // setActiveTab(true);

    // buat ganti tab
    // tabHome.addEventListener('click', function () {
    //   setActiveTab(true);
    // });
    //
    // tabProfile.addEventListener('click', function () {
    //   setActiveTab(false);
    // });
  </script>

@endsection
