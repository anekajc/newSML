@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Faktur Pajak')
@section('title', 'SML - Faktur Pajak')

@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicepenjualan/suratjalan/invoicejasa before it. Only layout/toolbar/
     column-header interactivity changed -- all business logic (loadAll,
     buttonAdd/buttonDelete, ExportDataToExcel, the import-excel form) is untouched. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
.toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.page-title {
  font-size: 19px;
  font-weight: 800;
  color: #1f2430;
}

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

#content { padding-top: 12px; }

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

/* {{-- Kolom Aksi tabel -- pastel round-button treatment, copied and rescoped to
     this page's own #tabel from so.blade.php's own @section('css'). --}} */
#tabel td:first-child {
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

#tabel td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel thead th,
#tabel_export thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel tbody tr:nth-of-type(odd),
#tabel_export tbody tr:nth-of-type(odd) {
  background-color: #fbfbfc;
}

#tabel tbody tr:hover,
#tabel_export tbody tr:hover {
  background-color: #f5f3ff;
}
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
</style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar modal add export --}}

<style>
  #tabel_export_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_export_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

{{-- end tampilan search bar modal add export --}}

{{-- tampilan search bar modal add pelanggan --}}
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
</style>

<style>
  #tabel_add_list_lokasi_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_lokasi_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

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
</style>

<style>
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
</style>

{{-- end tampilan search bar modal add pelanggan --}}

{{-- tampilan search sales --}}
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
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
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
  #tabel tbody .action-buttons-wrap {
    opacity: 0;
    visibility: hidden;
    transform: translateX(-6px);
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
  }
  /* Show them when hovering the table row */
  #tabel tbody tr:hover .action-buttons-wrap,
  #tabel tbody tr:focus-within .action-buttons-wrap {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
  }

  .data-table th,
  .data-table td {
    white-space: nowrap;
  }
</style>
{{-- end tampilan search modal barang all --}}
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

  {{-- Tab bar: PO's exact card.tab-card + custom-tabs anchor pattern (via
       so.blade.php), only one tab so nothing to switch, but same visual shell. --}}
  {{-- <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          Faktur Pajak
        </a>
      </div>
    </div>
  </div> --}}

  {{-- Toolbar: periode (with its existing onchange="loadAll()" kept exactly as-is)
       + Export Excel/Import Excel now inline in the same row, PO's po-toolbar-act
       pattern, instead of their old separate rows above the tab bar. --}}
  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="loadAll()" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="loadAll()" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') !!}">
        </div>

        <div class="po-toolbar-act">
          <form id="formImport" action="{{ url('fakturpajakimportexcel') }}" method="POST" enctype="multipart/form-data" style="margin:0;">
            @csrf
            <input type="file" id="import_file" name="import_file" hidden
                  onchange="document.getElementById('formImport').submit()">
            <label for="import_file" class="btn btn-chip-biru" style="margin:0; cursor:pointer;">Import Excel</label>
          </form>
          <button type="button" class="btn btn-chip-biru" onclick="openModalExport()">EXPORT EXCEL</button>
        </div>
      </div>
      @if (Session::has('success'))
      <div class="alert alert-success" style="margin: 0 14px 10px;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
          <p>{{ Session::get('success') }}</p>
      </div>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding: 0">
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="fpSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="fpLen1">Tampilkan</label>
                    <select id="fpLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                <div id="rtBarTabel"></div>
                <table id="tabel" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint">
                  <i class="bi bi-info-circle"></i>
                  Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                  untuk menyembunyikan kolom atau mengatur jumlah desimal.
                </div>
              </div>
            </div>
          </div>
        </div>



      </div>
    </div>
  </div>

</div>
</div>

<!--  -->

<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Faktur Pajak</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div id='formBsGrid' class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NO BUKTI</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tgl Pajak</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal"  value="{!!  \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')  !!}" >
                </div>
              </div>


                            </div>
                            <div class="row" style="margin-top: -10px">


              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">No Pajak</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_nopajak" placeholder="" >
                </div>
              </div>


            </div>

    </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button id="buttonSubmitAdd" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
    <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal" style="
    height: 30px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    >Batal</button>

    <button type="button" id="buttonSubmitAdd" class="btn btn-chip-biru btn-lg" style="
    height: 30px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background-color 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    onclick="submitAdd()">Submit</button>

    <!-- <button id="buttonSubmitBatalOto" type="button" class="btn btn-primary" onclick="submitBatalOto()">Batal Otorisasi</button> -->
  </div>
</div>
</div>
</div>
<!-- start modal export -->
<div class="modal fade" id="formExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Export</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row mb-2 col-12">
            <button type="button"
                    class="btn btn-chip-biru"
                    onclick="ExportDataToExcel()">
            Export
            </button>
          </div>
          <div class="row">
            <div class="col-12">
              <table id="tabel_export" class="data-table">
                <thead id="theadCustom" class="text-center">
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Nomor Bukti</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Nama Cust</th>
                    <th scope="col">No. PO</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_export" class="text-left">
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                    <tr>
                      <td class="text-center">
                        <input type="checkbox" id="exportData{{ $i }}" name="option1" value="{{ $tempOutstanding[$i]->NoBukti }}">
                      </td>
                      <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                      <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
                      <td>{{ $tempOutstanding[$i]->NamaCustSupp }}</td>
                      <td>{{ $tempOutstanding[$i]->PONo }}</td>
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
<!-- End modal export -->



@endsection

@section('js')

<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>

<script type="text/javascript">

// let tempNoBukti = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php (sama
 * seperti so.blade.php/invoicepenjualan.blade.php/invoicejasa.blade.php).
 * Halaman ini cuma punya satu tabel jadi tidak perlu poPindahBar-style
 * dynamic selector, tapi strukturnya dibuat sama supaya konsisten.
 */
let fpCart = []
const FP_HREF = 'fakturpajak'
const FP_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const FP_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function fpPickCI (row, key) {
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function fpDefaultCart () {
  return [
    ['NoBukti',      'No. Invoice',      1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',          1, 'date',    0, 0],
    ['NamaCustSupp', 'Nama Cust',        1, 'varchar', 0, 0],
    ['PONo',         'No PO',            1, 'varchar', 0, 0],
    ['NoPajak',      'No Pajak',         1, 'varchar', 0, 0],
    ['TglFPJ',       'Tgl Faktur Pajak', 1, 'date',    0, 0],
    ['DPP',          'DPP',              1, 'float',   0, 2],
    ['PPN',          'PPN',              1, 'float',   0, 2],
    ['NNET',         'Subtotal',         1, 'float',   0, 2],
  ]
}

function fpBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)
    cart.push([
      values[i],
      h,
      Number(isshowns[i]) === 1 ? 1 : 0,
      FP_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = FP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = fpCart || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(FP_TIPE_KODE[c[3]] ?? 0)
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
      href     : FP_HREF,
      urut     : 1
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : FP_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        let header = res.headertableheader
        let value = res.headertablevalue
        let isnumeric = res.isnumeric
        let isshown = res.isshown
        let tipe = res.desimal || []
        fpCart = fpBuatCart(header, value, isnumeric, isshown, tipe)
      } else {
        fpCart = fpDefaultCart()
        window.gcart_header = fpCart
        window.doSimpanHeader()
      }
      window.gcart_header = fpCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      fpCart = fpDefaultCart()
      window.gcart_header = fpCart
    }
  })
}

let fpRtSudahInit = false
function fpInitReportTableSekali () {
  if (fpRtSudahInit || typeof ReportTable === 'undefined') { return }
  fpRtSudahInit = true
  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })
}

function tulisTheadHeaderFP (cols) {
  let thead = document.querySelector('#tabel thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function fpValueCell (row, col) {
  let raw = fpPickCI(row, col[0]);
  let type = col[3];

  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    return '<td>' + formatDate(raw, '/') + '</td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function tabelActionsCell (row) {
  let nobukti = fpPickCI(row, 'NoBukti');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd(\'' + nobukti + '\' , \'' + fpPickCI(row, 'NoPajak') + '\' , \'' + fpPickCI(row, 'TglFPJ') + '\')"><i class="bi bi-plus"></i></button>';
  html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete(\'' + nobukti + '\')"><i class="bi bi-trash"></i></button>';
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (fpCart.length ? fpCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += fpValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderFP(cols);
}

let lastTabelRows = []
let fpPanjangHalaman = 10

function fpIkatSearch () {
  let input = document.getElementById('fpSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function fpIkatPanjangHalaman () {
  let sel = document.getElementById('fpLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(fpPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    fpPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(fpPanjangHalaman).draw()
    }
  })
}

const FP_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: FP_DOM_STRING,
      lengthChange: false,
      pageLength: fpPanjangHalaman,
      paging: true,
      order: [[1, 'asc']],
      ordering: false,
    });
    fpIkatSearch();
    fpIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function(){
      window.gcart_header = fpCart
      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();
      fpInitReportTableSekali();

	$("#tabel_export").DataTable({
          lengthChange: false,
          paging: false,
          searching: true,
          order: [[1, 'asc']],
          columnDefs: [
              { targets:[0], orderable:false }
          ]
      });


  //   formAddListItem
});

function loadAll () {
  console.log('loadall');

  let _token  = $("#_token").val()
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()

  $.ajax({
    url: "{!! url('fakturpajakloadall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir
    },
    success: function(res) {

      $('#tabel').DataTable().destroy();

      let rowTable = ''




      res.tempOutstanding.forEach((item, i) => {
        rowTable += `<tr>
        <td class='text-center'>
            <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('${item.NoBukti }' , '${item.NoPajak }' , '${item.TglFPJ }')"><i class="bi bi-plus"></i></button>

            <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.NoBukti }' )"><i class="bi bi-trash"></i></button>



          </td>
          <td>${item.NoBukti }</td>
          <td>${formatDate(item.Tanggal , '/')}</td>
         <!-- <td>${item.KodeCustSupp }</td> -->
          <td>${item.NamaCustSupp }</td>
          <td>${item.PONo ? item.PONo : '' }</td>

          <td>${item.NoPajak ? item.NoPajak : ''  }</td>
          <td>${ item.TglFPJ ? formatDate(item.TglFPJ , '/') : '' }</td>

          <td>${formatAngka(parseFloat(item.DPP).toFixed(2)) }</td>
          <td>${formatAngka(parseFloat(item.PPN).toFixed(2) )}</td>
          <td>${formatAngka(parseFloat(item.NNET).toFixed(2)) }</td>







        </tr>`
      });








      document.getElementById("tabel_data").innerHTML = rowTable


$("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          { "className": "text-right", "targets": [7, 8, 9] },
          // "columns" : [{"width" : "20px"}]

        ]
        });









    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()
  // let x = new Array(5)
  // console.log(x)
  // x.push('tes')
  // console.log(x)
  // x.splice(-2 , 0 , 'tus')
  //
  // console.log(x)

  console.log(nobukti , tanggal , nopajak)

  $.ajax({
    url: "{!! url('fakturpajakspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      nopajak,
      tglpajak : tanggal

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      $("#form").modal('toggle')
      alertify.success('Berhasil update faktur pajak')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAdd (nobukti , nopajak = '' , tanggal = new Date()  ) {
  // console.log('a')
  // console.log('buttonAdd' , nobukti , nopajak, tanggal)
  // console.log('pajak' , nopajak)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (!tanggal || tanggal == 'null') {
    // console.log('b')
    tanggal = new Date()
  }

  // input_add_nobukti
  document.getElementById("input_add_nobukti").value = nobukti
  // document.getElementById("input_add_tanggal").value = formatDate(new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0))
  document.getElementById("input_add_tanggal").value = formatDate(tanggal)
  document.getElementById("input_add_nopajak").value = nopajak

  $("#form").modal('toggle')
}

function buttonDelete (nobukti ) {


  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus no pajak ' +nobukti + ' ?',
      function() {
        let _token = $("#_token").val();






        $.ajax({
          url: "{!! url('fakturpajakspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti

          },
          success: function(res) {
            console.log('res', res)
            // refreshDataTableKoreksi(tempData.NOBUKTI)
            loadAll()

            // lockFormAdd()
            // $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus nopajak')

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

function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}



function openModalExport (POCustomer, cust, barang) {

  $('.showhide').hide();
  $("#formExport").modal('toggle');
}

  // reference only for routing purposes 
  // Route::post('/fakturpajakexportexcel' , 'FakturPajakController@spExport')->middleware('auth');
  
function ExportDataToExcel() {
    let selectedNoBukti = [];
    document.querySelectorAll('#tabel_data_export input[type="checkbox"]:checked').forEach(function(checkbox) {
        selectedNoBukti.push(checkbox.value);
    });

    if (selectedNoBukti.length === 0) {
        alert('Pilih minimal satu data!');
        return;
    }

    $.ajax({
        url: "{!! url('fakturpajakexportexcel') !!}",
        type: "get",
        async: false,
        data: {
            nobukti: selectedNoBukti
        },
        success: function(res) {

    // -- Sheet 1: Header --------------------------------------
    let wsData = [
        // Row 1 - extra header
        ['', '', res.header[0]?.IDTKUPenjual ?? ''],

        // Row 2 - extra header (empty)
        ['', '', ''],

        // Row 3 - column headers
        [
            'Baris', 'Tanggal', 'JenisFaktur', 'KodeTransaksi',
            'KeteranganTambahan', 'Dokumenpendukung', 'Periode dok pendukung',
            'referensi', 'capfasilitas', 'IDTKUPenjual', 'NPWPNIKPembeli',
            'JenisIDPembeli', 'NegaraPembeli', 'NomorDokumenPembeli',
            'NamaPembeli', 'AlamatPembeli', 'EmailPembeli', 'IDTKUPembeli'
        ],

        // Row 4+ - data rows
        ...res.header.map(function(h,i) {
            return [
                i+1,
                h.Tanggal ? h.Tanggal.substring(0, 10) : '',
                h.JenisFaktur ?? '',
                h.KodeTransaksi ?? '',
                h.KeteranganTambahan ?? '',
                h.Dokumenpendukung ?? '',
                h['Periode dok pendukung'] ?? '',
                h.referensi ?? '',
                h.capfasilitas ?? '',
                h.IDTKUPenjual ?? '',
                h.NPWPNIKPembeli ?? '',
                h.JenisIDPembeli ?? '',
                h.NegaraPembeli ?? '',
                h.NomorDokumenPembeli ?? '',
                h.NamaPembeli ?? '',
                h.AlamatPembeli ?? '',
                h.EmailPembeli ?? '',
                h.IDTKUPembeli ?? '',
            ];
        }),
        [
            'END', '', '', '',
            '', '', '',
            '', '', '', '',
            '', '', '',
            '', '', '', ''
        ]
    ];

    let detailData = [
    // Row 1 - column headers
    [
        'Baris',
        'BarangJasa',
        'KodeBarangJasa',
        'NamaBarangJasa',
        'NamaSatuanUkur',
        'HargaSatuan',
        'JumlahBarangJasa',
        'TotalDiskon',
        'DPP',
        'DPPNilaiLain',
        'TarifPPN',
        'PPN',
        'tarifppnbm',
        'ppnbm',
    ],

    // Row 2+ - data rows
    ...res.detail.map(function(d, i) {   // ? res.detail, not res.header
        return [
            i + 1,
            d.BarangJasa ?? '',
            d.KodeBarangJasa ?? '',
            d.NamaBarangJasa ?? '',
            d.NamaSatuanUkur ?? '',
            d.HargaSatuan ?? '',
            d.JumlahBarangJasa ?? '',
            d.TotalDiskon ?? '',
            d.DPP ?? '',
            d.DPPNilaiLain ?? '',
            d.TarifPPN ?? '',
            d.PPN ?? '',
            d.tarifppnbm ?? '',
            d.ppnbm ?? '',
        ];
    }),

    // Last row - END
    ['END', '', '', '', '', '', '', '', '', '', '', '', '', '']
];

    // -- Build workbook with 2 sheets -------------------------
    let wb = XLSX.utils.book_new();

    let wsHeader = XLSX.utils.aoa_to_sheet(wsData);  

    wsHeader['!merges'] = [
    { s: { r: 0, c: 2 }, e: { r: 0, c: 17 } },  // Row 1 merge
    { s: { r: 1, c: 2 }, e: { r: 1, c: 17 } },  // Row 2 merge
];     // switched to aoa

    XLSX.utils.book_append_sheet(wb, wsHeader, 'Faktur');   // Sheet 1

    let wsDetail = XLSX.utils.aoa_to_sheet(detailData);    // detail stays as json_to_sheet
    XLSX.utils.book_append_sheet(wb, wsDetail, 'DetailFaktur');   // Sheet 2

    // -- Download ---------------------------------------------
    XLSX.writeFile(wb, 'FakturPajak_' + new Date().toISOString().slice(0, 10) + '.xlsx');
}
    });

    $('.showhide').hide();
}


</script>




@endsection
