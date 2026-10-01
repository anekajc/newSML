@extends('newmaster')
@section('buttons')

@endsection


@section('css')
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
</style>
{{-- end tampilan search modal barang all --}}

{{-- report-table.css powers the .tb-report/.toolbar/.search-inp/.table-wrap classes
     used to reskin page1 below -- same file so.blade.php/penawaranso.blade.php link
     for their own reskinned tables. No JS from that pair (report-table.js,
     headerEngine.js) is loaded here -- this page keeps its existing server-rendered
     @for-loop table and GET-reload search exactly as before, only restyled. --}}
<link rel="stylesheet" href="{{ asset('css/report-table.css') }}?v={{ filemtime(public_path('css/report-table.css')) }}">

<style>
  /* Tab pill, matching so.blade.php's .radioChoiceMaster look exactly (copied
     from that file) -- sits inline at the left of the toolbar row below,
     not in its own separate card. */
  .radioChoiceMaster {
    display: inline-flex;
    list-style: none;
    margin: 0;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
    flex-shrink: 0;
  }
  .radioChoiceMaster-btn {
    display: inline-flex;
    align-items: center;
    border: none;
    border-radius: 999px;
    padding: 8px 18px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    background-color: transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    outline: none;
    box-shadow: none;
    cursor: pointer;
  }
  .radioChoiceMaster-btn:hover {
    color: #212529;
    background-color: rgba(0,0,0,0.04);
  }
  .radioChoiceMaster-btn.active {
    color: #fff;
    background-color: #007bff;
    box-shadow: 0 2px 6px rgba(0,123,255,0.35);
  }

  /* #tabel body row look + circular Actions-column buttons, copied from
     purchaseOrder.blade.php's own #tabel styling -- .tb (from report-table.css)
     only styles the header, not the body rows or the existing plain
     .btn-primary/.btn-danger buttons already in the Actions column. */
  #tabel.tb tbody td {
    padding: 10px 14px;
    font-size: 15px;
    border-bottom: 1px solid #F1F5F9;
  }
  #tabel.tb tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }
  #tabel.tb tbody tr:hover {
    background-color: #f5f3ff;
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

  /* "Reset kolom" pill + its row, same block used in penawaranso.blade.php/
     so.blade.php -- kept OUTSIDE #rtBarTabel (as a flex sibling, not a child)
     because report-table.js's renderBar() fully overwrites that div's
     innerHTML on every drag/hide/decimal change. Not part of report-table.css
     itself, so declared here. */
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

  {{-- This page still uses its own table ids/classes instead of po-table-header.css's
       .data-table, so the nowrap rule is scoped to those ids directly. --}}
  #tabel th, #tabel td,
  #addTable th, #addTable td,
  #otorisasiTable th, #otorisasiTable td,
  #tabel_add_list_lokasi th, #tabel_add_list_lokasi td {
    white-space: nowrap;
  }
</style>
@endsection

@section('content')



<div id="page1" class="mainpage container-fluid">



<!-- <button onclick="loadAll()">tes</button> -->
<!-- Standalone page title dropped to match purchaseOrder.blade.php's page1, which
     doesn't show one either (its own is commented out) -- the tab pill below already
     labels this section.
<div class="container-fluid">
  <div class="row" >
    <div class="col-6 text-left">
      <h2 style="margin-top: -85px">Verifikasi Penawaran</h2>
    </div>
    <div class="col-6 text-right">
    </div>
  </div>
</div>
-->

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
  {{-- Tab pill in its own row above the toolbar, matching purchaseOrder.blade.php's
       screenshot (tabs sit above the filter row, not inline inside it) and
       so.blade.php's own placement of .radioChoiceMaster. Only one real tab
       exists here ("Verifikasi"); nav-profile-tab elsewhere in this file's JS
       is dead/never wired to a second pane, so it isn't given one either. --}}
  <div style="margin-bottom:12px;">
    <div class="radioChoiceMaster" id="nav-tab" role="tablist">
      <a class="radioChoiceMaster-btn active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" disabled>
        Verifikasi
      </a>
    </div>
  </div>

  {{-- No outer Bootstrap .card wrapping the table -- .tb-report/.table-wrap below
       already render as their own card-like panel (report-table.css), the same way
       so.blade.php/penawaranso.blade.php's tables sit directly in their tab-content
       with no surrounding .card. --}}
  <div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="tb-report main">
      {{-- Toolbar matching purchaseOrder.blade.php's .po-toolbar layout: search
           fields + the submit/filter button grouped together on the left. PO
           also has a right-hand +Add/+Tambah PR group pushed over by
           .action-group's margin-left:auto, but this page has no equivalent
           "add" action, so there's nothing on the right here. Still a plain
           GET form -- same field ids/names/values and submit behavior as
           before, only restyled. --}}
      <form method="GET" class="toolbar" style="margin-bottom:10px;">
        <input type="text"
            id="search_customer"
            name="search_customer"
            value="{{ request('search_customer') }}"
            class="search-inp"
            placeholder="Search Customer">
        <input type="text"
            id="search_nobukti"
            name="search_nobukti"
            value="{{ request('search_nobukti') }}"
            class="search-inp"
            placeholder="Search No. Bukti">
        <input type="text"
            id="search_barang"
            name="search_barang"
            value="{{ request('search_barang') }}"
            class="search-inp"
            placeholder="Search Barang">
        <button type="submit" name="search" value="1" class="btn-load">
          <i class="bi bi-search"></i> Cari
        </button>
      </form>

      <div class="rt-bar-row">
        <button class="rt-reset-btn" type="button" title="Reset kolom" onclick="buttonHeaderTable()">
          <i class="bi bi-arrow-clockwise"></i> Reset kolom
        </button>
        <div id="rtBarTabel"></div>
      </div>

      <div class="table-outer">
      <div class="table-wrap">
          <table id="tabel" class="tb">
            {{-- Header content is fully JS-owned -- verifPenawaranReplaceThead()
                 (called from renderTabelRows(), run on page load and after every
                 search/otorisasi refresh) replaces this <thead>'s contents based
                 on gcart_header. --}}
            <thead style="white-space:nowrap;"></thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>
      </div>
      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
      </div>
    </div>
  </div>

</div>

</div>
{{-- closes #page1 --}}
</div>




<div class="container-fluid mainpage" id="page2" style="display: none">

  <div id="" class="">
  <!-- <div class="modal-header">


      <h5 class="modal-title" id="">Add</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div> -->

  <div class="container-fluid">


    <!-- <div id="qrcode"></div> -->
    <div class="row" style="margin-top: -60px">
      <div class="col-6 text-left">
        <h1>Form Invoice</h1>
      </div>
      <div class="col-6 text-right">
        <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button> -->
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

      </div>
    </div>
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>




  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid mt-4">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-md-3">
          <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <!-- <div class="col-md-3 text-right">
            <div class="form-group">
          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tgl</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
            <div class="row">


            </div>
        </div>

      </div>
    </div>
    <div class="container-fluid">

      <hr/ style="margin-top: -8px">
    </div>
    <div class="container-fluid mt-4">
      <div class="row">



        <div class="col-md-3">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_namacustomer"  disabled></textarea>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">

              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tipe PPN</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <select id="input_add_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
                      <option value=0 selected>None</option>
                      <option value=1 >Exclude</option>

                      <option value=2 >Include</option>
                    </select>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sales</label>
                  </div>
                </div>
                <!-- <div class="col-md-3">
                  <div class="row">

                  <div class="form-group">
                </div>
              </div>

                </div> -->
                <div class="col-md-8">
                  <div class="form-group input-group">
                    <input type="text" class="form-control" id="input_add_sales" value="" disabled >
                    <input type="hidden" class="form-control text-center" id="input_add_kodesales" value="" disabled >
                    <button class="btn btn-primary btn-sm text-right" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-plus"></i></button>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">



              <!-- </div>

            </div> -->

            <!-- <div class="col-md-6">
              <div class="row"> -->






                    <div class="col-12">
                      <div class="form-group">
                        <label>Lokasi Penerima</label>
                      </div>
                    </div>

                  </div>
                    <div class="row">


                    <div class="col-12" style="margin-top: -10px">
                      <div class="form-group input-group">
                        <input type="hidden" class="form-control text-center" id="input_add_kodelokasipenerima" value="" disabled >
                        <input type="text" class="form-control" id="input_add_lokasipenerima" value="" disabled >
                        <button class="btn btn-primary btn-sm text-right" id="buttonAddListLokasiPenerima" onclick="buttonAddListLokasiPenerima()"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  <!-- </div>
                </div> -->


              <!-- </div>

            </div> -->




          </div>

        </div>

        <div class="col-md-3">
          <div class="row">
            <!-- <div class="col-md-6">
              <div class="row"> -->

              <!-- </div>
            </div> -->


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>


                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nopo" value="" onblur="onChangeHeader('pono' , 'input_add_nopo' , 'No PO')"  >
                  </div>
                </div>
              <!-- </div>
            </div> -->

          </div>
            <div class="row" style="margin-top: -10px">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Uang Muka</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_uangmuka" value="0.00"  onblur="onChangeUangMuka('nuangmuka' , 'input_add_uangmuka' , 'uang muka')">
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-12">
                  <div class="form-group">
                    <label>Catatan</label>
                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">


                <div class="col-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_catatan" value="" onblur="onChangeHeader('footnote' , 'input_add_catatan', 'catatan')" >
                  </div>
                </div>
              <!-- </div>
            </div> -->

          </div>

        </div>





        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Pembayaran</label>
              </div>
            </div>


            <div class="col-md-8">
              <div class="form-group">
                <select id="input_add_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePembayaran()">
                  <option value=0 selected>Tunai</option>
                  <option value=1 >Kredit</option>
                </select>
              </div>
            </div>
          <!-- </div>
        </div> -->

      </div>
        <div class="row" style="margin-top: -10px">


        <!-- <div class="col-md-6">
          <div class="row"> -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Hari</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="number" class="form-control  text-right" id="input_add_hari" value="0"  onblur="onChangeHeader('hari' , 'input_add_hari')">
                <input type="hidden" class="form-control  text-right" id="input_add_harikredit" value="0"  >
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">


            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                </div>


                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_valas" value="IDR" disabled >
                  </div>
                </div>
              <!-- </div>
            </div> -->


            </div>
            <div class="row" style="margin-top: -10px">

            <!-- <div class="col-md-6">
              <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_kurs" value="1.00"  onblur="onChangeKurs('kurs' , 'input_add_kurs')">
                  </div>
                </div>


          </div>
        </div>

        <div class="col-md-3 mt-2">
          <div class="row">



          </div>
        </div>

        <div class="col-md-3 mt-2">
          <div class="row">


          </div>
        </div>








      </div>










      </div>



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->
<div class="container-fluid">
  <hr/>

</div>

  <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Sub Total</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>

              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=7 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>

  </div>




  <div id="" class="container-fluid">
    <div class="row">
      <div class="col-md-12 mt-2 text-right">
      <!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  >+ Tambah Item</button> -->
      <button type="button" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAddItem()" class="btn btn-secondary"><b>+ Tambah Item</b></button>
  </div>

    </div>
    <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>

  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <hr/>
    <div class="row">
      <div class="col-12">
        <h4>Add Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-6">



    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Barang</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">

          <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>
        </div>
      </div>

      <div class="col-md-5">
        <div class="input-group form-group">
          <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
        </div>
      </div>

    </div>

  </div>




</div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-6">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>


      <div class="col-md-3">
        <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Harga</label>
      </div>
      </div>

      <div class="col-md-3">
        <input id="AddAddInputHarga" type="number" value='0.00' class="form-control text-right">
        <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
        <input id="AddAddInputSatuan" type="hidden"  class="form-control  " disabled>
      </div>

    </div>
  </div>


  <div class="col-md-4">

  </div>

</div>
  <div class="row" style="margin-top: -10px">


  <div class="col-md-6 ">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-8">
        <input id="AddAddKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>

    </div>
  </div>
  </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">
        <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button> -->
        <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
        <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" >Add</button> -->
        <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit Add</button>

        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>



  <div id="formAddEdit" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <hr/>
    <div class="row">
      <div class="col-12">
        <h4>Edit Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-4">



    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Barang</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right"> -->

          <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->
        <!-- </div> -->
      <div class="col-md-8">
        <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
      </div>

    </div>

  </div>

  <div class="col-md-4">
    <!-- <div class="row"> -->
      <div class="row">
        <div class="col-md-4 ">
          <div class="form-group">
          <label>Nama Barang</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">

            <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
          </div>
        </div>
      </div>

    <!-- </div> -->

  </div>

  <div class="col-md-4">

  </div>


  </div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-4">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>


      <div class="col-md-8">
        <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
      </div>



    </div>
  </div>


  <div class="col-md-4">

    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
        <label>Harga</label>
      </div>
      </div>
      <div class="col-md-8">
        <input id="AddEditInputUrutItem" type="hidden"  class="form-control  " disabled>
        <input id="AddEditInputHarga" type="number" value='0.00' class="form-control text-right">
        <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
        <input id="AddEditInputSatuan" type="hidden"  class="form-control  " disabled>
      </div>
    </div>

  </div>
  <!-- <div class="col-md-6">

  </div> -->

  <div class="col-md-4">

  </div>


  </div>
  <div class="row" style="margin-top: -10px">

  <div class="col-md-8">
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-md-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-4">
        <input id="AddEditKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>

    </div>
  </div>
  </div>



    <div class="row mt-2">
      <div class="col-md-12 text-right mt-4">
        <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button>

        <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->

        <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
        <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" >Add</button> -->
        <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit Edit</button>

        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>

    </div>
    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>



  </div>



</div>


<div class="container-fluid mainpage" id="page3" style="display: none">

  <div id="" class="container-fluid ">


    <div id="" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Otorisasi</h1>
          </div>
          <div class="col-6 text-right">
            <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button> -->
            <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>


    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">
              <div class="form-group">
            <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_kodecustomer" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No Bukti" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
      <div class="container-fluid" >
        <hr/ style="margin-top: -8px">

      </div>

      <div class="container-fluid mt-4">
      <div class="row">





          <div class="col-md-3">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_namacustomer"  disabled></textarea>
                </div>
              </div>
              <div class="col-12" style="margin-top: -10px">

                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatcustomer"  disabled></textarea>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="row">



          <div class="col-md-6">
            <div class="row">
              <!-- <div class="col-md-6">
                <div class="row"> -->


              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_otorisasi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                    </div>
                  </div>


                <!-- </div>

              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <!-- <div class="col-md-3">
                    <div class="row">

                    <div class="form-group">
                  <button class="btn btn-primary btn-sm text-right" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-plus"></i></button>
                  </div>
                </div>

                  </div> -->
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" value="" disabled >
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodesales" value="" disabled >
                    </div>
                  </div>


                <!-- </div>

              </div> -->




            </div>

          </div>

          <div class="col-md-6">
            <div class="row" >



              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>No PO</label>
                    </div>
                  </div>


                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_nopo" value="" disabled  >
                    </div>
                  </div>
                <!-- </div>
              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">


                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" value="0.00"  disabled>
                    </div>
                  </div>
                <!-- </div>
              </div> -->

            </div>

          </div>

          <div class="col-12">
            <div class="row" style="margin-top: -10px">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Lokasi Penerima</label>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodelokasipenerima" value="" disabled >
                      <input type="text" class="form-control" id="input_otorisasi_lokasipenerima" value="" disabled >
                    </div>
                  </div>

                </div>
              </div>





              <div class="col-md-6">
                <div class="row">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>

                </div>

                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_catatan" value="" disabled >
                    </div>
                  </div>

                </div>

              </div>

            </div>




          </div>
        </div>







      </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" value="IDR" disabled >
                </div>
              <!-- </div>
            </div> -->
          </div>



        </div>
          <div class="row" style="margin-top:-10px">


          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_kurs" value="1.00" disabled>
                </div>
              </div>
            <!-- </div>
          </div> -->

        </div>
          <div class="row" style="margin-top: -10px">




          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <select id="input_otorisasi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                    <option value=0 selected>Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            <!-- </div>
          </div> -->

          <!-- <div class="col-md-6">
            <div class="row"> -->

          </div>
            <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control  text-right" id="input_otorisasi_hari" value="0" disabled>
                  <input type="hidden" class="form-control  text-right" id="input_otorisasi_harikredit" value="0" disabled >
                </div>
              </div>
            <!-- </div>
          </div> -->
            </div>

          </div>

          <div class="col-md-3">
            <div class="row">



            </div>
          </div>










        </div>










        </div>



      </div>

      <!-- <div class="row "> -->

  <!-- </div> -->




  <div class="container-fluid">
    <hr/>

  </div>


    <div class="container-fluid mt-4" style="overflow:auto; margin-top:-35px;">
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <table id="otorisasiTable" class="table table-bordered table-hover table-striped table-responsive-lg">
          <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>


              </tr>
            </thead>


            <tbody id="otorisasiTableData" class="" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>


    <div id="" class="container-fluid mt-2">
      <div class="row">
        <div class="text-right col-12">
          <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitOtorisasi()" class="btn btn-secondary"><b>Otorisasi</b></button>
        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>
    </div>

    </div>


</div></div>
<!-- </div> -->


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">


      <div id="" class="showhideform">
      <div class="modal-header">


          <h5 class="modal-title" id="">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12" style="margin-top:-30px;">
              <h3>Detail</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto; margin-top:-60px;">
              <table id="tabel_add_list_lokasi" class="table table-bordered table-hover table-striped table-responsive-lg">
                <thead class="text-center bg-primary text-white">
                <!-- <tr><th style="padding: 4px 12px;" scope="col">Actions</th> -->

                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Gdg</th>
                  <th style="padding: 4px 12px;" scope="col">Stock</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_lokasi" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>


















      </div>


    </div>
  </div>

<!-- End modal add-->






<div class="modal fade" id="formOtorisasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">








    </div>
  </div>

<!-- End modal oto-->





@endsection

@section('js')
<script src="{{ asset('js/report-table.js') }}"></script>
<script src="{{ asset('js/headerEngine.js') }}?v={{ filemtime(public_path('js/headerEngine.js')) }}"></script>
<script type="text/javascript">
let xppncust = 0

let tipeform = 'add'

let dataTableAdd = []
let dataTableKoreksi = []
let dataEdit = {}

let tempKode = ''

// -- #tabel interactive column engine (public/js/headerEngine.js) --
// Same shared drag/hide/decimal-column engine used by so.blade.php/
// penawaranso.blade.php, backed by VerifikasiPenawaranController::
// loadHeader/simpanHeader (same generic (username, href, reportmode)
// contract, its own href so its saved layout doesn't collide with any
// other page's).
HeaderEngine.configure({
  loadUrl: "{!! url('verifikasipenawaranloadheader') !!}",
  simpanUrl: "{!! url('verifikasipenawaransimpanheader') !!}"
});

var lastTabelRows = [];

HeaderEngine.registerTable('tabel', {
  href: 'verifikasipenawaran_tabel',
  tableSel: '#tabel',
  barSel: '#rtBarTabel',
  setDefault: function () { setDefaultHeaderTabel(); },
  onChange: function () { reinitTabel(); }
});

// Columns match VerifikasiPenawaranController::index()/loadAll()'s SELECT list.
function setDefaultHeaderTabel() {
  gcart_header = [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Customer',  1, 'varchar', 0, 0],
    ['NAMAPIC',      'PIC',       1, 'varchar', 0, 0],
    ['KODEBRG',      'Kode Brg',  1, 'varchar', 0, 0],
    ['NAMABRG',      'Nama Brg',  1, 'varchar', 0, 0],
    ['IsVerf',       'Verifikasi',1, 'bool',    0, 0],
    ['TglVerf',      'Tgl Verf',  1, 'date',    0, 0],
    ['UserVerf',     'User Verf', 1, 'varchar', 0, 0],
    ['HARGA',        'Harga',     1, 'float',   0, 2],
    ['tipe',         'Tipe',      1, 'varchar', 0, 0],
    ['NamaMerk',     'Merk',      1, 'varchar', 0, 0],
    ['ketdet',       'Ket',       1, 'varchar', 0, 0],
    ['QNT',          'Qnt',       1, 'float',   0, 2],
    ['QntSO',        'Qnt SO',    1, 'float',   0, 2],
    ['Sisa',         'Sisa',      1, 'float',   0, 2]
  ];
}

function tabelActionsCell(row) {
  var nobukti = HeaderEngine.pickCI(row, 'NOBUKTI');
  var urut = HeaderEngine.pickCI(row, 'Urut');
  if (Number(HeaderEngine.pickCI(row, 'IsVerf')) === 1) {
    return '<td class="text-center"><div class="action-buttons-wrap"><button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\',\'' + urut + '\')"><i class="bi bi-dash"></i></button></div></td>';
  }
  return '<td class="text-center"><div class="action-buttons-wrap"><button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\',\'' + urut + '\')"><i class="bi bi-plus"></i></button></div></td>';
}

// col[5] (decimals) is user-editable via the gear menu's stepper, so float
// formatting has to read it live rather than assume a fixed precision.
function tabelValueCell(row, col) {
  var raw = HeaderEngine.pickCI(row, col[0]);
  var type = col[3];
  if (type === 'date') {
    return '<td>' + (raw ? formatDate(raw, '/') : '') + '</td>';
  }
  if (type === 'float') {
    return '<td class="text-right">' + (raw !== undefined && raw !== null && raw !== '' ? formatAngka(raw) : '') + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function verifPenawaranReplaceThead(cols) {
  var oldThead = document.querySelector('#tabel thead');
  if (!oldThead || !window.ReportTable) { return; }
  var headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

function renderTabelRows(rows) {
  if (HeaderEngine.activeKey() !== 'tabel') { HeaderEngine.activateEngineData('tabel'); }
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  verifPenawaranReplaceThead(cols);
}

function reinitTabel() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: 't', lengthChange: false, paging: false, ordering: false });
    HeaderEngine.bindEngineDom('tabel');
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable() {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    HeaderEngine.activateEngineData('tabel');
    HeaderEngine.doSetHeader(1, true);
    reinitTabel();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

HeaderEngine.activateEngineData('tabel');
HeaderEngine.doSetHeader(1);
lastTabelRows = @json($tempOutstanding);
reinitTabel();

$(document).ready(function(){

  document.getElementById('breadcrumb').innerHTML = "Verifikasi Penawaran";

  $("#tabel2").DataTable({
    lengthChange: false,
    paging: false,
    order: [[1, 'asc']],
    columnDefs: [
      { targets: [0], orderable: false }
    ]
  });

});

function renderTable(data) {
  // Kept as a thin wrapper (searchData()'s success callback + the initial
  // page-ready call above both call this by name) -- the actual rendering
  // now goes through the interactive column engine.
  lastTabelRows = data || [];
  reinitTabel();
}

function searchData() {

  let nobukti  = $('#search_nobukti').val();
  let customer = $('#search_customer').val();
  let barang   = $('#search_barang').val();

  $.ajax({
    url: "{!! url('verifikasipenawaranloadall') !!}",
    type: "GET",
    dataType: "json",
    cache: false, 
    data: {
      search_nobukti: nobukti,
      search_customer: customer,
      search_barang: barang,
      _t: Date.now() 
    },
    success: function(res) {

      if (!res || !res.tempOutstanding) {
        renderTable([])
        return
      }

      renderTable(res.tempOutstanding);

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Gagal load data')
    }
  });
}


function buttonDetail (kodebrg) {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('verifikasipenawarandetailbarang') !!}",
    type: "post",
    async: false,
    data: {
      kodebrg,
      _token
    },
    success: function(res) {

      // $('#tabel_').DataTable().destroy();

      let rowTable = ''

      if (!res.length) {


        alertify.warning("Data tidak ditemukkan")
        return
      }


      res.forEach((item, i) => {
        rowTable += `<tr>

          <td>${ item.KODEBRG }</td>
          <td>${ item.NAMABRG }</td>
          <td>${ item.KODEGDG }</td>
                  <td class="text-right">${item.SaldoQnt ? formatAngka(item.SaldoQnt ) : '' }</td>


        </tr>
          `
      });

      document.getElementById("tabel_data_add_list_lokasi").innerHTML = rowTable

      $("#form").modal("toggle");


},
error: function (err) {
  console.log(err)
  console.log(err.status)
  console.log(err.statusText)
  alertify.warning('Terjadi kesalahan silahkan refresh browser')
}

})
}


function buttonBatalOtorisasi(nobukti, urut) {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('verifikasipenawaranbatalotorisasi') !!}",
    type: "POST",
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {

      alertify.success('Berhasil Batal Verifikasi')

      searchData()

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan')
    }
  });

}

function buttonOtorisasi(nobukti, urut) {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('verifikasipenawaranotorisasi') !!}",
    type: "POST",
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {

      alertify.success('Berhasil Verifikasi')

      searchData()

    },
    error: function(err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan')
    }
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
  angkaString = parseFloat(angkaString).toFixed(2)
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
</script>


<script>

  $(document).ready(function () {

  const urlParams = new URLSearchParams(window.location.search)

  if (urlParams.has('search')) {

  }

})


</script>


<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(idNav , flag = 0) {
    $(".nav-item").css("background-color", "#f8f9fa");
    $(".nav-item").css("color", "#007bff");
    if (flag) {


      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
      return
    }
    if(idNav == 'nav-profile-tab') {

      loadAllInfo()

      console.log(idNav)
      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
    } else {
      loadAll()

      console.log(idNav)
      document.getElementById(idNav).style.backgroundColor = '#007bff';
      document.getElementById(idNav).style.color = '#fff';
    }

    // if (homeActive) {
    //   tabHome.style.backgroundColor = '#007bff';
    //   tabHome.style.color = '#fff';
    //   tabProfile.style.backgroundColor = '#f8f9fa';
    //   tabProfile.style.color = '#007bff';
    // } else {
    //   tabProfile.style.backgroundColor = '#007bff';
    //   tabProfile.style.color = '#fff';
    //   tabHome.style.backgroundColor = '#f8f9fa';
    //   tabHome.style.color = '#007bff';
    // }
  }

  // Default warna tab
  document.getElementById('nav-home-tab').addEventListener('click', function () {
    setActiveTab("nav-home-tab");
  });

  // buat ganti tab

</script>




@endsection
