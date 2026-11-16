@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Opname Gudang')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-6 text-left">
      {{-- <h1>Opname Gudang</h1> - judul pindah ke header (page-title) --}}
    </div>
    <div class="col-6 text-right">
      <button data-wms-toolbar="tabel" type="button" class="btn btn-primary btn-sm " onclick="buttonAdd()"  >Add Opname Gudang</button>
      
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />


  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />


  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
          <div class="row mt-4">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <div class="row mt-3">
            <div class="col-12" style="overflow:auto;">
              <div class="card wms-kartu">

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Opname Stock</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Gdg</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)

                        <tr >

                          <td>
                            <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->Nobukti }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->Nobukti }}')"><i class="bi bi-pen"></i></button>
                          </td>
                            <td>{{ $penerimaanArray[$i][0]->Nobukti}}</td>
                            <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tanggal)) !!}</td>
                            <td>{{ $penerimaanArray[$i][0]->NamaGDG}}</td>
                      </tr>
                      @endfor
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noopn" value="TES/TES/00001"  disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>


          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Lokasi</label>
              </div>
            </div>

            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kodelokasi" placeholder="Kode Lokasi" disabled>
              </div>
            </div>
            <div class="col-2 text-right">
              <!-- <div class="form-control"> -->
            <button class="btn form-group btn-success btn-sm text-right" onclick="resetInputAdd('input_add_kodelokasi')"><i class="bi bi-arrow-clockwise"></i></button>
            <!-- </div> -->
            </div>






            <!-- <div class="col-2">
              <div class="form-group">
                <label>Gdg Tujuan</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_gdgtujuan" placeholder="Gdg Tujuan" disabled>
              </div>
            </div> -->


          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Brg</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kodebarang" placeholder="Kode Brg" disabled>
              </div>
            </div>
            <div class="col-2 text-right">
              <div class="form-group">
            <button class="btn btn-success btn-sm text-right" onclick="resetInputAdd('input_add_kodebarang')"><i class="bi bi-arrow-clockwise"></i></button>
          </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Scanner</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_scanner" oninput="enterScanner(event)" placeholder="Scan Kode">
              </div>
            </div>
            <!-- <div class="col-1 text-right">
              <div class="form-group">
            <button class="btn btn-success btn-sm text-right"><i class="bi bi-plus"></i></button>
            </div>
            </div> -->

            <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="focusScanner()" class="btn btn-success btn-sm text-right"><i class="bi bi-upc-scan"></i></button>
            </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Jumlah Barang</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_qty" value=0.00 required>
              </div>
            </div>
            <div class="col-2 text-right">
              <div class="form-group">
                <!-- <span>FEET</span> -->
            <button onclick="buttonAddItem()" class="btn btn-success btn-sm text-right"><i class="bi bi-plus"></i></button>
            </div>
            </div>


        </div>

        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->
      <div class="line"></div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Lokasi</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty Gdg</th>
                    <th scope="col">Satuan</th>
                    <!-- <th scope="col">Qty Debet</th>
                    <th scope="col">Qty Kredit</th> -->
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="addTableData" class="text-right" >
                  <tr >
                    <td colspan="3">Text</td>
                      <!-- <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td><button>asd</button></td> -->
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal add-->


<!-- start modal detail koreksi -->
<div class="modal fade" id="formDetailKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detailKoreksi_noopn" value="TES/TES/00001"  disabled>
              </div>
            </div>
            <!-- <div class="col-">
            </div> -->
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detailKoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>


          </div>




        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->
        <div class="container-fluid mt-4" style="overflow-x: auto;">

              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Lokasi</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty Gdg</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Debet</th>
                    <th scope="col">Qty Kredit</th>
                    <!-- <th scope="col">Actions</th> -->
                  </tr>
                </thead>


                <tbody id="detailKoreksiTableData" class="text-right" >
                  <tr >
                    <td colspan="3">Text</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal detail koreksi-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_koreksi_noUrut" value="" />
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noopn" value="TES/TES/00001"  disabled>
              </div>
            </div>
            <!-- <div class="col-1">
            </div> -->
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>


          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Lokasi</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_kodelokasi" placeholder="Kode Lokasi" disabled>
              </div>
            </div>
            <div class="col-2 text-right">
              <div class="form-group">
            {{-- Dinonaktifkan 2026-10-22: resetInputAdd memindah fokus ke scanner form Add (tersembunyi). --}}
            {{-- <button class="btn btn-success btn-sm text-right" onclick="resetInputAdd('input_koreksi_kodelokasi')"><i class="bi bi-arrow-clockwise"></i></button> --}}
            <button class="btn btn-success btn-sm text-right" onclick="resetInputKoreksi('input_koreksi_kodelokasi')"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Kode Brg</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_kodebarang" placeholder="Kode Brg" disabled>
              </div>
            </div>
            <div class="col-2 text-right">
              <div class="form-group">
            <button class="btn btn-success btn-sm text-right" onclick="resetInputKoreksi('input_koreksi_kodebarang')"><i class="bi bi-arrow-clockwise"></i></button>
          </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Scanner</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_scanner" oninput="enterScannerKoreksi(event)" placeholder="Scan Kode">
              </div>
            </div>
            <!-- <div class="col-1 text-right">
              <div class="form-group">
            <button class="btn btn-success btn-sm text-right"><i class="bi bi-plus"></i></button>
            </div>
            </div> -->

            <div class="col-2 text-right">
              <div class="form-group">
            {{-- Dinonaktifkan 2026-10-22: focusScanner() memfokuskan scanner form Add, bukan form Koreksi. --}}
            {{-- <button onclick="focusScanner()" class="btn btn-success btn-sm text-right"><i class="bi bi-upc-scan"></i></button> --}}
            <button onclick="focusScannerKoreksi()" class="btn btn-success btn-sm text-right"><i class="bi bi-upc-scan"></i></button>
            </div>
            </div>




            <!-- <div class="col-2">
              <div class="form-group">
                <label>Gdg Tujuan</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_gdgtujuan" placeholder="Gdg Tujuan" disabled>
              </div>
            </div> -->


          </div>
          <div class="row">

            <div class="col-12">
              <div class="form-group">
                <label>Jumlah Barang</label>
              </div>
            </div>
            <div class="col-10">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_koreksi_qty" value='0.00' required>
              </div>
            </div>
            <div class="col-2 text-right">
              <div class="form-group">
                <!-- <span>FEET</span> -->
            <button onclick="buttonAddKoreksiItem()" class="btn btn-success btn-sm text-right"><i class="bi bi-plus"></i></button>
            </div>
            </div>


        </div>

        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->
      <div class="line"></div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="koreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Lokasi</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">QGdg</th>
                    <th scope="col">Satuan</th>
                    <!-- <th scope="col">Qty Debet</th>
                    <th scope="col">Qty Kredit</th> -->
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="koreksiTableData" class="text-right" >
                  <tr >
                    <td colspan="3">Text</td>
                      <!-- <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td><button>asd</button></td> -->
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <button type="button" class="btn btn-primary" onclick="submitKoreksi()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->






@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">


  let dataRefreshPenerimaan = []
  let addDataArray = []
  let checkBarang = []
  let checkLokasi = []

  let koreksiDataArray = []

  // Ditambahkan 2026-10-22: gudang dari lokasi terakhir yang di-scan. Dulu dibaca dari checkLokasi
  // (hasil scan terakhir); kalau yang terakhir di-scan barang, checkLokasi kosong -> error JS
  // "Cannot read properties of undefined (reading 'KodeGdg')" dan item tidak masuk list.
  let kodeGdgAdd = ''
  let kodeGdgKoreksi = ''

  $(document).ready(function(){
        $("#tabel").DataTable({ "lengthChange": false,
        'order': [[1, 'asc']],
            "paging": false ,
             "columnDefs": [
          { "type": "date", "targets": [2] },
          {  "className": "text-center", "targets": [0], "orderable" :false },
        ]});


  });

  function enterScannerKoreksi (e) {
    // if (e.which === 13) {
      let _token = $("#_token").val();
      let barcode = $("#input_koreksi_scanner").val();
      console.log(barcode)

      if (barcode.length < 4) {
        console.log("<4")
        document.getElementById("input_koreksi_scanner").value = ''
        $('#input_koreksi_scanner').focus();
        alertify.warning('Kode tidak dikenali')
        return
      }

      // if (barcode.length === 8) {
      //   document.getElementById("input_koreksi_kodelokasi").value = barcode
      // } else if ( barcode.length === 14) {
      //   document.getElementById("input_koreksi_kodebarang").value = barcode
      // }
      $.ajax({
        url: "{!! url('opnamegudangspcheck') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kode: barcode
        },
        success: function(res) {
          console.log(res, "resssss")
          console.log(res.checkBarang)
          console.log(res.checkBarang[0])
          console.log(res.checkLokasi)
          console.log(res.checkLokasi[0])

          checkBarang = res.checkBarang
          checkLokasi = res.checkLokasi


          // koreksiDataArray = res


        }
      })

      if (checkLokasi.length) {
        document.getElementById("input_koreksi_kodelokasi").value = barcode
        kodeGdgKoreksi = checkLokasi[0].KodeGdg
      } else if (checkBarang.length) {
        document.getElementById("input_koreksi_kodebarang").value = barcode
      } else {
        alertify.warning('Kode tidak dikenali')
      }


      // console.log(barcode)
      let kodebrg = $("#input_koreksi_kodebarang").val();
      let kodelokasi = $("#input_koreksi_kodelokasi").val();
      if (kodebrg && kodelokasi) {
        $('#input_koreksi_qty').focus();
      }

      document.getElementById("input_koreksi_scanner").value = ''

    // }
  }

  function enterScanner (e) {
    // console.log(e.which)
    // if (e.which === 13) {
      let _token = $("#_token").val();
      let barcode = $("#input_add_scanner").val();
      console.log(barcode)

      if (barcode.length < 4) {
        console.log("<4")
        document.getElementById("input_add_scanner").value = ''
        $('#input_add_scanner').focus();
        alertify.warning('Kode tidak dikenali')
        return
      }

      $.ajax({
        url: "{!! url('opnamegudangspcheck') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kode: barcode
        },
        success: function(res) {
          console.log(res, "resssss")
          console.log(res.checkBarang)
          console.log(res.checkBarang[0])
          console.log(res.checkLokasi)
          console.log(res.checkLokasi[0])

          checkBarang = res.checkBarang
          checkLokasi = res.checkLokasi


          // koreksiDataArray = res


        }
      })


      if (checkLokasi.length) {
        document.getElementById("input_add_kodelokasi").value = barcode
        kodeGdgAdd = checkLokasi[0].KodeGdg
      } else if (checkBarang.length) {
        document.getElementById("input_add_kodebarang").value = barcode
      } else {
        alertify.warning('Kode tidak dikenali')
      }
      // console.log(barcode)
      let kodebrg = $("#input_add_kodebarang").val();
      let kodelokasi = $("#input_add_kodelokasi").val();
      if (kodebrg && kodelokasi) {
        $('#input_add_qty').focus();
      }

      document.getElementById("input_add_scanner").value = ''

    // }
  }

  function resetInputAdd (htmlId) {
    document.getElementById(htmlId).value = ''
    if (htmlId === 'input_add_kodelokasi') kodeGdgAdd = ''
    focusScanner()
  }

  // Ditambahkan 2026-10-22: tombol reset Kode Brg di modal Koreksi memanggil fungsi ini sejak
  // versi lama, tapi fungsinya tidak pernah ada (error JS "resetInputKoreksi is not defined").
  function resetInputKoreksi (htmlId) {
    document.getElementById(htmlId).value = ''
    if (htmlId === 'input_koreksi_kodelokasi') kodeGdgKoreksi = ''
    focusScannerKoreksi()
  }

  function focusScannerKoreksi () {
    $('#input_koreksi_scanner').focus();
  }

  function refreshTableKoreksi (NOBUKTI) {
    let _token = $("#_token").val();
    // formDetailKoreksi
    $.ajax({
      url: "{!! url('getdetailpenerimaanopnamegudang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res, "resssss")

        koreksiDataArray = res


      }
    })
    if (!koreksiDataArray.length) {
      alertify.success('Item habis')
      $("#formKoreksi").modal('toggle')
      return
    }


    document.getElementById("input_koreksi_noopn").value = koreksiDataArray[0].Nobukti
    document.getElementById("input_koreksi_noUrut").value = koreksiDataArray[0].NOURUT



    let date = new Date(koreksiDataArray[0].tanggal);
    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#input_koreksi_tanggal').val(date1)



    let rowTable = ""
    // <td>${Number(item.Qntdb).toFixed()}</td>
    // <td>${Number(item.QntCr).toFixed()}</td>
    koreksiDataArray.forEach((item, i) => {

      console.log(item.kodebrg)
      rowTable += `<tr>
      <td>${item.Lokasi}</td>
      <td>${item.kodebrg}</td>
      <td>${item.namaBrg}</td>
      <td><input id="input_koreksi_qntGudang${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${Number(item.QntOpname).toFixed()}></td>
      <td>${Number(item.SaldoComp).toFixed()}</td>
      <td>${item.Satuan}</td>
      <td class="text-center">
        <button class="btn btn-danger btn-sm" type="button" onclick=""><i class="bi bi-trash"></i></button></td>
      </td>
      </tr>`
    });
    console.log('tes2')
    document.getElementById("input_koreksi_qty").val = '0.00'
    document.getElementById("koreksiTableData").innerHTML = rowTable
  }


    function buttonDetailKoreksi (NOBUKTI) {
      console.log(NOBUKTI)

      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('getdetailpenerimaanopnamegudang') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NOBUKTI: NOBUKTI
        },
        success: function(res) {
          console.log(res, "resssss")


          // document.getElementById("input_koreksidetail_noout").value = nooutbrg
          document.getElementById("input_detailKoreksi_noopn").value = res[0].Nobukti
          // document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg
          // document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
          let date = new Date(res[0].tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
          $('#input_detailKoreksi_tanggal').val(date1)



          let rowTable = ""
          res.forEach((item, i) => {

            console.log(item.kodebrg)
            rowTable += `<tr>
            <td>${item.Lokasi}</td>
            <td>${item.kodebrg}</td>
            <td>${item.namaBrg}</td>
            <td>${Number(item.QntOpname).toFixed()}</td>
            <td>${Number(item.SaldoComp).toFixed()}</td>
            <td>${item.Satuan}</td>
            <td>${Number(item.Qntdb).toFixed()}</td>
            <td>${Number(item.QntCr).toFixed()}</td>
            </tr>`
          });
          console.log('tes2')
          console.log(rowTable)
          document.getElementById("detailKoreksiTableData").innerHTML = rowTable



        }
      })
      console.log('tes')
      $("#formDetailKoreksi").modal('toggle')
    }

  function buttonKoreksi (NOBUKTI) {

    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    console.log(NOBUKTI)
    let _token = $("#_token").val();
    // formDetailKoreksi
    $.ajax({
      url: "{!! url('getdetailpenerimaanopnamegudang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res, "resssss")

        koreksiDataArray = res


      }
    })

    document.getElementById("input_koreksi_noopn").value = koreksiDataArray[0].Nobukti
    document.getElementById("input_koreksi_noUrut").value = koreksiDataArray[0].NOURUT

    let date = new Date(koreksiDataArray[0].tanggal);
    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#input_koreksi_tanggal').val(date1)



    let rowTable = ""
    koreksiDataArray.forEach((item, i) => {

      console.log(item.kodebrg)
      rowTable += `<tr>
      <td>${item.Lokasi}</td>
      <td>${item.kodebrg}</td>
      <td>${item.namaBrg}</td>
      <td><input id="input_koreksi_qntGudang${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${Number(item.QntOpname).toFixed()}></td>
      <td>${Number(item.SaldoComp).toFixed(0)}</td>
      <td>${item.Satuan}</td>
      <td class="text-center">
        <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKoreksiItem(${i})"><i class="bi bi-trash"></i></button></td>
      </td>
      </tr>`
    });
    console.log('tes2')
    document.getElementById("input_koreksi_kodebarang").value = ''
    document.getElementById("input_koreksi_kodelokasi").value = ''
    kodeGdgKoreksi = ''
    document.getElementById("input_koreksi_qty").val = '0.00'
    document.getElementById("koreksiTableData").innerHTML = rowTable

    $("#formKoreksi").modal('toggle')
    $('#formKoreksi').on('shown.bs.modal', function () {
      $('#input_koreksi_scanner').trigger('focus')
    })
  }

  function refreshTableAdd () {
    let rowTable = ""


    addDataArray.forEach((item, i) => {
      let qntDebet = 0
      let qntKredit = 0
      if (Number(item.Qnt) > Number(item.QntGdg)) {
        qntDebet = item.Qnt - item.QntGdg
      }
      if (Number(item.Qnt) < Number(item.QntGdg)) {
        qntKredit = item.QntGdg - item.Qnt
      }
      let qntGdg = 0
      if (item.QntGdg) {
        qntGdg = parseFloat(item.QntGdg).toFixed(0)
      }
      let satuan = ''
      if (item.Satuan) {
        satuan = item.Satuan
      }
      // <td><input onchange="" id="input_add_qntDebet${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${Number(qntDebet)} disabled></td>
      // <td><input onchange="" id="input_add_qntKredit${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${Number(qntKredit)} disabled></td>
      // onchange="changeQnt(${i})"

      rowTable += `<tr>
      <td>${item.Kodelokasi}</td>
      <td>${item.Kodebrg}</td>
      <td>${item.Namabrg}</td>
      <td><input id="input_add_qntTerima${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${Number(item.Qnt)}></td>
      <td><input id="input_add_qntGudang${i}" style="width: 80px;" class="text-right" type="number" min=0 value=${qntGdg} disabled></td>
      <td>${satuan}</td>
      <td class="text-center">
        <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
      </td>
      </tr>`
    });

    if (!addDataArray.length) {
      rowTable = `<tr>
      <td class="text-center" colspan=9>Belum ada item</td>
      </tr>`
    }

    document.getElementById("addTableData").innerHTML = rowTable

  }

  function loadAll () {
    $.ajax({
      url: "{!! url('loadallopnamegudang') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res)
        dataRefreshPenerimaan = res.penerimaanArray
      }
    })

    $('#tabel').DataTable().destroy();

    let rowTable = ""
    dataRefreshPenerimaan.forEach((item, i) => {
      let date1 = ""
      if (item[0].tanggal) {
        let date = new Date(item[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }

      rowTable += `<tr>
      <td>
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item[0].Nobukti}')"><i class="bi bi-info-lg"></i></button>
      <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].Nobukti}')"><i class="bi bi-pen"></i></button>
      </td>
      <td>${item[0].Nobukti}</td>
      <td>${date1}</td>
      <td>${item[0].NamaGDG}</td>
      </tr>`
    });


    document.getElementById("tabel_data").innerHTML = rowTable

    $("#tabel").DataTable({ "lengthChange": false,
    'order': [[1, 'asc']],
        "paging": false ,
         "columnDefs": [
      // { "type": "date", "targets": [1] },
      {  "className": "text-center", "targets": [0], "orderable" :false },
    ]});


  }

  // function


  function changeQnt (index) {
    let qntDebet = 0.00
    let qntKredit = 0.00
    let qntOpname = document.getElementById(`input_add_qntTerima${index}`).value
    let qntGdg = document.getElementById(`input_add_qntGudang${index}`).value
    if (Number(qntOpname) > Number(qntGdg)) {
      qntDebet = qntOpname - qntGdg
    }
    if (Number(qntOpname) < Number(qntGdg)) {
      qntKredit = qntGdg - qntOpname
    }
    document.getElementById(`input_add_qntDebet${index}`).value = qntDebet
    document.getElementById(`input_add_qntKredit${index}`).value = qntKredit
  }

  function setNewNoBukti () {
    $.ajax({
      url: "{!! url('getnobuktiopnamegudang') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res, 'NoBUkti')

        document.getElementById("input_add_noopn").value = res[0].Nobukti
        document.getElementById("input_add_noUrut").value = res[0].Nourut
      }
    })
  }

  function buttonAdd() {
    console.log('buttonAdd')

    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    setNewNoBukti()


    document.getElementById("input_add_kodebarang").value = ''
    document.getElementById("input_add_kodelokasi").value = ''
    kodeGdgAdd = ''
    document.getElementById("input_add_scanner").value = ''
    document.getElementById("input_add_qty").value = '0.00'

    addDataArray = []
    refreshTableAdd()

    $("#form").modal('toggle')
    $('#form').on('shown.bs.modal', function () {
      $('#input_add_scanner').trigger('focus')
    })
    // $('#input_add_scanner').focus();
  }

  function buttonDeleteItem (index) {
    if (index > -1) {
      addDataArray.splice(index,1)

    }

    refreshTableAdd()
  }

  function submitAdd () {
    let _token = $("#_token").val();
    let noopn = document.getElementById("input_add_noopn").value
    // let kodegudang = document.getElementById("input_add_kodegdg").value
    let inputDate = document.getElementById("input_add_tanggal").value

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value
    console.log(periode_tahun, periode_bulan)
    let checkDate = new Date(inputDate)
    console.log(checkDate.getFullYear(), checkDate.getMonth() +1 )

    // console.log('tes2')
    // return
    let nourut = document.getElementById("input_add_noUrut").value
    // if (kodegudang === "0") {
    //     alertify.warning("gudang harus dipilih");
    //     return
    // }
    console.log('2')
    if (!addDataArray.length) {
      alertify.warning("tidak ada item di list");
      return
    }
    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      // console.log('ga sama')
      alertify.warning("tanggal tidak sesuai periode");
      return
    }
    console.log("1")
    // console.log(document.getElementById(`input_add_qntTerima${i}`).value)
    // console.log(document.getElementById(`input_add_qntDebet${i}`).value)
    // console.log(document.getElementById(`input_add_qntKredit${i}`).value)

    addDataArray.forEach((item, i) => {
      let inputQnt = document.getElementById(`input_add_qntTerima${i}`).value
      let qntDebet = 0
      let qntKredit = 0
      if (Number(inputQnt) > Number(item.QntGdg)) {
        qntDebet = inputQnt - item.QntGdg
      }
      if (Number(inputQnt) < Number(item.QntGdg)) {
        qntKredit = item.QntGdg - inputQnt
      }
      item.inputQnt = inputQnt
      item.inputQntDebet = qntDebet
      item.inputQntKredit = qntKredit
      item.Selisih = item.SaldoComp - document.getElementById(`input_add_qntTerima${i}`).value
      item.urut = 0
    });

    // console.log(kodelokasi)
    // console.log(kodegudang)
    let choice = "I"
    console.log(noopn)
    console.log(nourut)
    console.log(inputDate)
    console.log(addDataArray)

    $.ajax({
      url: "{!! url('addformopnamegudang') !!}",
      type: "post",
      async: false,
      data: {
        jmlRecord: 0,
        _token : _token,
        choice,
        noopn,
        nourut,
        inputDate,
        note : "",
        keterangan: "",
        tempData : addDataArray

      },
      success: function(res) {
        console.log(res)
        if (res == 1) {
        console.log(res ,'ress =============================')
        console.log('success add form')
        loadAll()
        $("#form").modal('toggle')
        // Ditambahkan 2026-10-22: dulu tidak ada pesan setelah simpan berhasil.
        alertify.success('Opname gudang telah ditambah');
        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
      }
    })

  }

  function focusScanner () {
    $('#input_add_scanner').focus();
  }

  function buttonDeleteKoreksiItem (index) {
    // koreksiDataArray
    let tempData = []
    // tempData.push({
    //
    // })
    // let kodegudang = 'GSML'
    let kodelokasi = document.getElementById("input_koreksi_kodelokasi").value
    let kodebarang = ''
    let qty = 0
    console.log(koreksiDataArray[index])

    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + koreksiDataArray[index].namaBrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"
        let noopn = document.getElementById("input_koreksi_noopn").value
        let inputDate = document.getElementById("input_koreksi_tanggal").value
        let nourut = document.getElementById("input_koreksi_noUrut").value

        tempData.push({
          Kodelokasi: kodelokasi,
          Kodebrg: kodebarang,
          Namabrg: 0,
          Qnt: 0,
          QntGdg: 0,
          Satuan: 0,
          Kodegdg: 0,
          SaldoComp: 0,
          NoSat: 0,
          Isi: 1,
          Harga: 0,
          Saldo2Comp: 0,
          Qnt2Opname: 0,
          Selisih2: 0,
          Qnt2DB: 0,
          Qnt2CR: 0,
          isCek: 0,
          isCek2: 0,
          Tipe: "KRS",
          Lokasi: '',
          Rak: '',
          inputQnt: 0,
          Selisih: 0,
          inputQntDebet: 0,
          inputQntKredit: 0,
          urut: koreksiDataArray[index].urut
        })

        console.log(tempData)
        $.ajax({
          url: "{!! url('addformopnamegudang') !!}",
          type: "post",
          async: false,
          data: {
            jmlRecord: 1,
            _token : _token,
            choice,
            noopn,
            nourut,
            inputDate,
            note : "",
            keterangan: "",
            tempData

          },
          success: function(res) {
            console.log(res ,'ress =============================')
            console.log('success delete koreksi')
            refreshTableKoreksi(noopn)
            loadAll()
          }
        })
      }
    ,function(){});
  }

  function buttonAddKoreksiItem () {
    console.log('buttonAddItemKoreksi')

    // let kodegudang = 'GSML'
    let kodelokasi = document.getElementById("input_koreksi_kodelokasi").value
    let kodebarang = document.getElementById("input_koreksi_kodebarang").value
    let qty = document.getElementById("input_koreksi_qty").value
    console.log(kodelokasi, kodebarang)
    console.log(koreksiDataArray)
    if (!kodelokasi || !kodebarang) {
      alertify.warning("kodelokasi dan barang harus diisi");
      return
    }
    if (!qty) {
      if (qty !== 0) {
        alertify.warning("qty harus diisi");
        return
      }

    }

    // if (Number(qty) < 0) {
    //
    //     alertify.warning("qty harus diisi");
    //     return
    //
    //
    // }

    let tempArray = koreksiDataArray.filter( function (el) {
      return el.Lokasi === kodelokasi && el.kodebrg === kodebarang
    })

    if (tempArray.length) {
      alertify.warning("Item sudah ada di list");
      return
    }

    // Dinonaktifkan 2026-10-22: checkLokasi kosong bila scan terakhir adalah barang (error JS).
    // let kodegudang = checkLokasi[0].KodeGdg
    let kodegudang = kodeGdgKoreksi
    if (!kodegudang) {
      alertify.warning("Scan ulang kode lokasi");
      return
    }
    console.log(kodegudang)
    let _token = $("#_token").val();
    let item = {}
      $.ajax({
        url: "{!! url('detailitemopnamegudang') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kodelokasi,
          kodebarang,
          kodegudang

        },
        success: function(res) {
          console.log(res ,'ress =============================')
          console.log(res[0])
          item = res[0]
        }
      })

      if(!item) {
        console.log('barang tidak ditemukan')

        document.getElementById("input_koreksi_kodebarang").value = ''
        document.getElementById("input_koreksi_qty").value = '0.00'
        $('#input_koreksi_scanner').focus();
        alertify.warning("Item tidak ditemukan");
        return
      }

      console.log("item" , item)
      console.log(item.SALDOQNT)
      console.log(Number(item.SALDOQNT))
      console.log(Number(item.SALDOQNT) - Number(qty))
      let qntDebet = 0.00
      let qntKredit = 0.00
      if (Number(qty) > Number(item.SALDOQNT)) {
        qntDebet = qty - item.SALDOQNT
      }
      if (Number(qty) < Number(item.SALDOQNT)) {
        qntKredit = item.SALDOQNT - qty
      }
      let qntGdg = 0.00
      if (item.SALDOQNT) {
        qntGdg = parseFloat(item.SALDOQNT).toFixed(2)
      }

      let koreksiAddItem = []
      koreksiAddItem.push({
        Kodelokasi: kodelokasi,
        Kodebrg: kodebarang,
        Namabrg: item.NAMABRG,
        Qnt: qty,
        QntGdg: item.SALDOQNT,
        Satuan: item.Satuan,
        Kodegdg: kodegudang,
        SaldoComp: item.SALDOQNT,
        NoSat: item.Nosat,
        Isi: 1,
        Harga: 0,
        Saldo2Comp: 0,
        Qnt2Opname: 0,
        Selisih2: 0,
        Qnt2DB: 0,
        Qnt2CR: 0,
        isCek: 0,
        isCek2: 0,
        Tipe: "KRS",
        Lokasi: item.KODELOKASI,
        Rak: item.KODERAK,
        inputQnt: qty,
        Selisih: Number(item.SALDOQNT) - Number(qty),
        inputQntDebet: qntDebet,
        inputQntKredit: qntKredit,
        urut: 0
      })
      console.log(koreksiAddItem)

      let noopn = document.getElementById("input_koreksi_noopn").value
      // let kodegudang = document.getElementById("input_koreksi_kodegdg").value
      let inputDate = document.getElementById("input_koreksi_tanggal").value
      let nourut = document.getElementById("input_koreksi_noUrut").value

      let periode_bulan = document.getElementById("periode_bulan").value
      let periode_tahun = document.getElementById("periode_tahun").value
      console.log(periode_tahun, periode_bulan)
      let checkDate = new Date(inputDate)
      console.log(checkDate.getFullYear(), checkDate.getMonth() +1 )

      // console.log('tes2')
      // return

      let choice = "I"
      console.log('=======================')
      console.log(noopn)
      console.log(nourut)
      console.log(inputDate)
      console.log(koreksiDataArray)
      // return
      $.ajax({
        url: "{!! url('addformopnamegudang') !!}",
        type: "post",
        async: false,
        data: {
          jmlRecord: 1,
          _token : _token,
          choice,
          noopn,
          nourut,
          inputDate,
          note : "",
          keterangan: "",
          tempData : koreksiAddItem

        },
        success: function(res) {
          console.log(res ,'ress =============================')
          console.log('success add form')
          loadAll()
          refreshTableKoreksi(noopn)
        }
      })



  }

  function submitKoreksi () {
    console.log(koreksiDataArray)

    let _token = $("#_token").val();
    let choice = "U"
    let noopn = document.getElementById("input_koreksi_noopn").value
    let inputDate = document.getElementById("input_koreksi_tanggal").value
    let nourut = document.getElementById("input_koreksi_noUrut").value
    let tempData = []

    koreksiDataArray.forEach((item, i) => {

      let qntDebet = 0.00
      let qntKredit = 0.00
      if (Number(document.getElementById(`input_koreksi_qntGudang${i}`).value) > Number(item.SaldoComp)) {
        qntDebet = document.getElementById(`input_koreksi_qntGudang${i}`).value - item.SaldoComp
      }
      if (Number(document.getElementById(`input_koreksi_qntGudang${i}`).value) < Number(item.SaldoComp)) {
        qntKredit = item.SaldoComp - document.getElementById(`input_koreksi_qntGudang${i}`).value
      }
      let qntGdg = 0.00
      if (item.SaldoComp) {
        qntGdg = parseFloat(item.SaldoComp).toFixed(2)
      }
      tempData.push({
        Kodelokasi: item.Lokasi,
        Kodebrg: item.kodebrg,
        Namabrg: item.namaBrg,
        Qnt: document.getElementById(`input_koreksi_qntGudang${i}`).value,
        QntGdg: item.SaldoComp,
        Satuan: item.Satuan,
        Kodegdg: item.Kodegdg,
        SaldoComp: item.SaldoComp,
        NoSat: item.nosat,
        Isi: item.isi,
        Harga: 0,
        Saldo2Comp: 0,
        Qnt2Opname: 0,
        Selisih2: 0,
        Qnt2DB: 0,
        Qnt2CR: 0,
        isCek: 0,
        isCek2: 0,
        Tipe: "KRS",
        Lokasi: item.Lokasi,
        Rak: item.Rak,
        inputQnt: document.getElementById(`input_koreksi_qntGudang${i}`).value,
        Selisih: Number(item.SaldoComp) - Number(document.getElementById(`input_koreksi_qntGudang${i}`).value),
        inputQntDebet: qntDebet,
        inputQntKredit: qntKredit,
        urut: item.urut
      })
    });



    console.log(tempData)

    $.ajax({
      url: "{!! url('addformopnamegudang') !!}",
      type: "post",
      async: false,
      data: {
        jmlRecord: 1,
        _token : _token,
        choice,
        noopn,
        nourut,
        inputDate,
        note : "",
        keterangan: "",
        tempData

      },
      success: function(res) {
        if (res == 1) {
          alertify.success('Qty telah diedit')
        }
        console.log(res ,'ress =============================')
        console.log('success edit koreksi')
        loadAll()
        refreshTableKoreksi(noopn)
      }
    })
  }

  function buttonAddItem () {
    console.log('buttonAddItem')


    let kodelokasi = document.getElementById("input_add_kodelokasi").value
    let kodebarang = document.getElementById("input_add_kodebarang").value
    let qty = document.getElementById("input_add_qty").value
    console.log(kodelokasi, kodebarang)
    if (!kodelokasi || !kodebarang) {
      alertify.warning("kodelokasi dan barang harus diisi");
      return
    }
    if (!qty) {
      if (qty !== 0) {
        alertify.warning("qty harus diisi");
        return
      }

    }


    let tempArray = addDataArray.filter( function (el) {
      return el.Kodelokasi === kodelokasi && el.Kodebrg === kodebarang
    })

    if (tempArray.length) {
      alertify.warning("Item sudah ada di list");
      return
    }

    // Dinonaktifkan 2026-10-22: checkLokasi kosong bila scan terakhir adalah barang (error JS).
    // let kodegudang = checkLokasi[0].KodeGdg
    let kodegudang = kodeGdgAdd
    if (!kodegudang) {
      alertify.warning("Scan ulang kode lokasi");
      return
    }
    console.log(kodegudang)
    // return

    let _token = $("#_token").val();
    let item = {}
      $.ajax({
        url: "{!! url('detailitemopnamegudang') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kodelokasi,
          kodebarang,
          kodegudang

        },
        success: function(res) {
          console.log(res ,'ress =============================')
          console.log(res[0])
          item = res[0]
        }
      })
      // return

      if(!item) {
        console.log('barang tidak ditemukan')

        document.getElementById("input_add_kodebarang").value = ''
        document.getElementById("input_add_qty").value = '0.00'
        $('#input_add_scanner').focus();
        alertify.warning("Item tidak ditemukan");
        return
      }

    addDataArray.push(
      {
        Kodelokasi: kodelokasi,
        Kodebrg: kodebarang,
        Namabrg: item.NAMABRG,
        Qnt: qty,
        QntGdg: item.SALDOQNT,
        Satuan: item.Satuan,
        Kodegdg: kodegudang,
        SaldoComp: item.SALDOQNT,
        NoSat: item.Nosat,
        Isi: 1,
        Harga: 0,
        Saldo2Comp: 0,
        Qnt2Opname: 0,
        Selisih2: 0,
        Qnt2DB: 0,
        Qnt2CR: 0,
        isCek: 0,
        isCek2: 0,
        Tipe: "KRS",
        Lokasi: item.KODELOKASI,
        Rak: item.KODERAK
      }
    )



    refreshTableAdd()

    document.getElementById("input_add_kodebarang").value = ''
    document.getElementById("input_add_qty").value = '0.00'
    // document.getElementById("input_add_kodelokasi").value = ''
    $('#input_add_scanner').focus();

  }


</script>




@endsection
