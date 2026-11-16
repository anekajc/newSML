@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'QC LPB')
@section('buttons')

@endsection

@section('content')
<div class="container-fluid">
{{-- <h1>QC LPB</h1> - judul pindah ke header (page-title) --}}
<!-- <svg id="barcode"></svg> -->
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="loadAll1()">tes1</button>
<button onclick="loadAll2()">tes2</button> -->
</div>


<!-- <div class="container"> -->
<div id="loadingContainer" style="display: none" class="container-fluid mt-6 justify-content-middle align-middle">
  <div class="text-center">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
</div>

<div id="printContainer" style="display:none">

</div>

<input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
<input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
<input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
<input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

<div id="contentContainer" class="container-fluid">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">LPB</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">QC</a>
      </div>
    </div>
  </div>
  <div class="card wms-kartu">
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Supplier</th>
                          <th scope="col">No. PO</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">Nama Gudang</th>
                          <th scope="col">FakturSupp</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for( $i = 0 ; $i < count($lpbArray) ; $i++ )
                        <tr>
                          <td>

                            <button style="" class="btn btn-success btn-sm text-center" type="button" onclick="buttonAddLPB('{{$lpbArray[$i][0]->NOBUKTI}}')"><i class="bi bi-clipboard-data"></i></button>
                          </td>
                            <td>{{  $lpbArray[$i][0]->NOBUKTI }}</td>
                            <td>{{  date("Y/m/d", strtotime($lpbArray[$i][0]->TANGGAL)) }}</td>
                            <td>{{  $lpbArray[$i][0]->NAMACUSTSUPP }}</td>
                            <td>{{  $lpbArray[$i][0]->NoPO }}</td>
                            <td>{{  $lpbArray[$i][0]->KodeGdg }}</td>
                            <td>{{  $lpbArray[$i][0]->NAMAGDG }}</td>
                            <td>{{  $lpbArray[$i][0]->FAKTURSUPP }}</td>
                        </tr>
                        @endfor

                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabelQC" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Supplier</th>
                          <th scope="col">No. PO</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">FakturSupp</th>
                          <th scope="col">FakturSupp</th>
                          <th scope="col">Tgl. QC</th>
                          <th scope="col">User QC</th>
                        </tr>
                      </thead>


                      <tbody id="tabelQC_data" class="text-left" >
                        @for( $i = 0 ; $i < count($qcArray) ; $i++ )
                        <tr>
                          <td>

                            <button style="" class="btn btn-warning btn-sm text-center" type="button" onclick="buttonEditQC('{{$qcArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-info"></i></button>
                          </td>
                            <td>{{  $qcArray[$i][0]->NOBUKTI }}</td>
                            <td>{{  date("Y/m/d", strtotime($qcArray[$i][0]->TANGGAL)) }}</td>
                            <td>{{  $qcArray[$i][0]->NAMACUSTSUPP }}</td>
                            <td>{{  $qcArray[$i][0]->NoPO }}</td>
                            <td>{{  $qcArray[$i][0]->KodeGdg }}</td>
                            <td>{{  $qcArray[$i][0]->NAMAGDG }}</td>
                            <td>{{  $qcArray[$i][0]->FAKTURSUPP }}</td>
                            <td>{{  $qcArray[$i][0]->TGLQC }}</td>
                            <td>{{  $qcArray[$i][0]->USERQC }}</td>
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
</div>


<!-- <div> -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">QC LPB</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="addLPB_NOBUKTI" placeholder="Nomor Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Nomor PO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="addLPB_NOPO" placeholder="Nomor PO" required disabled>
              </div>
            </div>
          </div>
          <div class="row">


        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>Supplier</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="addLPB_Supplier" placeholder="Supplier" disabled>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="addLPB_Gudang" placeholder="Gudang" disabled>
            </div>
          </div>
        </div>

        <div class="row">

        </div>
        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addLPBTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Stn</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty Reject</th>

                  </tr>
                </thead>


                <tbody id="addLPBTableData" class="text-left" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAddLPB()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- End modal add-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi QC</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="editQC_NOBUKTI" placeholder="Nomor Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Nomor PO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="editQC_NOPO" placeholder="Nomor PO" required disabled>
              </div>
            </div>
          </div>
          <div class="row">


        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>Supplier</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="editQC_Supplier" placeholder="Supplier" disabled>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label>Gudang</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="editQC_Gudang" placeholder="Gudang" disabled>
            </div>
          </div>
        </div>

        <div class="row">

        </div>
        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="editQCTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Stn</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty Reject</th>

                  </tr>
                </thead>


                <tbody id="editQCTableData" class="text-left" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>

                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitEditQC()">Submit</button> -->
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

  let dataAddLPB = []
  let dataEditQC = []

  let dataLPBRefresh = []
  let dataQCRefresh = []


  $(document).ready(function(){
        $("#tabel").DataTable({ 'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], 'orderable' : false },
    ]});
    // JsBarcode("#barcode", "29081488");
  });
  $(document).ready(function(){
        $("#tabelQC").DataTable({  'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0] , 'orderable' : false },
    ]});
  });

  function loadAll() {
    // console.log('1')
    // $('#contentContainer').hide();
    // document.getElementById("loadingContainer").style.display="block"
    // console.log('2')


    $.ajax({
      url: "{!! url('getAksesQCLPB') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res)
        document.getElementById("akses_istambah").value = res.ISTAMBAH
        document.getElementById("akses_iskoreksi").value = res.ISKOREKSI
        document.getElementById("akses_iscetak").value = res.ISCETAK
        document.getElementById("akses_ishapus").value = res.ISHAPUS
      }
    })


    $('#tabel').DataTable().destroy();
    $.ajax({
      url: "{!! url('getAllLPB') !!}",
      type: "get",
      async: false,

      success: function(res) {
        // console.log('masok')
        console.log('get all LPB')
        console.log(res)
        dataLPBRefresh = res
        let rowTabel = ""
        res.forEach((item, i) => {
          let date1 = ""
          if (item[0].TANGGAL) {
            let date = new Date(item[0].TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }

          let noBukti = ""
          let namaSupp = ""
          let noPO = ""
          let kodeGdg = ""
          let namaGdg = ""
          let fakturSupp = ""

          if (item[0].NOBUKTI) {
            noBukti = item[0].NOBUKTI
          }
          if (item[0].NAMACUSTSUPP) {
            namaSupp = item[0].NAMACUSTSUPP
          }
          if (item[0].NoPO) {
            noPO = item[0].NoPO
          }
          if (item[0].NAMAGDG) {
            namaGdg = item[0].NAMAGDG
          }
          if (item[0].KodeGdg) {
            kodeGdg = item[0].KodeGdg
          }
          if (item[0].FAKTURSUPP) {
            fakturSupp = item[0].FAKTURSUPP
          }



          rowTabel += `<tr>
          <td><button style="" class="btn btn-success btn-sm text-center" type="button" onclick="buttonAddLPB2(${i})"><i class="bi bi-clipboard-data"></i></button></td>
          <td>${noBukti}</td><td>${date1}</td><td>${namaSupp}</td><td>${noPO}</td><td>${kodeGdg}</td><td>${namaGdg}</td><td>${fakturSupp}</td></tr>`
        });
        document.getElementById("tabel_data").innerHTML = rowTabel
      }
    })



        $("#tabel").DataTable({'order': [[1, 'asc']],  "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
    ]});

    $('#tabelQC').DataTable().destroy();
    $.ajax({
      url: "{!! url('getAllQC') !!}",
      type: "get",
      async: false,

      success: function(res) {
        // console.log('masok')
        console.log('get all QC')
        console.log(res)
        dataQCRefresh = res
        let rowTabel=""
        res.forEach((item, i) => {
          let date1 = ""
          if (item[0].TANGGAL) {
            let date = new Date(item[0].TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }

          let noBukti = ""
          let namaSupp = ""
          let noPO = ""
          let kodeGdg = ""
          let namaGdg = ""
          let fakturSupp = ""


          let tglqc = ''
          let userqc = ''


          if (item[0].TGLQC) {
            tglqc = item[0].TGLQC
          }
          if (item[0].USERQC) {
            userqc = item[0].USERQC
          }

          if (item[0].NOBUKTI) {
            noBukti = item[0].NOBUKTI
          }
          if (item[0].NAMACUSTSUPP) {
            namaSupp = item[0].NAMACUSTSUPP
          }
          if (item[0].NoPO) {
            noPO = item[0].NoPO
          }
          if (item[0].NAMAGDG) {
            namaGdg = item[0].NAMAGDG
          }
          if (item[0].KodeGdg) {
            kodeGdg = item[0].KodeGdg
          }
          if (item[0].FAKTURSUPP) {
            fakturSupp = item[0].FAKTURSUPP
          }
          rowTabel += `<tr>
          <td><button style="" class="btn btn-warning btn-sm text-center" type="button" onclick="buttonEditQC2(${i})"><i class="bi bi-info"></i></button>
          </td>
          <td>${noBukti}</td><td>${date1}</td><td>${namaSupp}</td><td>${noPO}</td><td>${kodeGdg}</td><td>${namaGdg}</td><td>${fakturSupp}</td><td>${tglqc}</td><td>${userqc}</td></tr>`
        });
        document.getElementById("tabelQC_data").innerHTML = rowTabel
        document.getElementById("loadingContainer").style.display="none"
        $('#contentContainer').show();
      }
    })

      $("#tabelQC").DataTable({'order': [[1, 'asc']],  "lengthChange": false, "paging": false , "columnDefs": [
    { "type": "date", "targets": [2] },
    {  "className": "text-center", "targets": [0], 'orderable' : false },
  ]});
    // document.getElementById("loadingContainer").style.display="none"
    // tabelQC_data
    // console.log('3')

  }

  function loadAll1() {
    // contentContainer
    console.log('tes')

    document.getElementById("loadingContainer").style.display="block"
    $('#contentContainer').hide();
  }

  function loadAll2() {
    // contentContainer
    console.log('tes')

    document.getElementById("loadingContainer").style.display="none"
    $('#contentContainer').show();
  }


  function tesClick(a) {
    console.log(a)
  }

  function printLPB (index) {
    let barcode = dataAddLPB[index].KODEBRG
    let jumlahPrint = $(`#addLPB_QtyPrint${index}`).val();
    console.log('print : ' + barcode + ' ' + jumlahPrint +'x')
    // printPageArea(`#barcode${index}`)
    let printContent = document.getElementById(`containerbarcode${index}`).innerHTML;
    let printContent1 = ""
    for (let i=0; i < jumlahPrint ; i++ ) {
      printContent1 += `<div class="row">`
      printContent1 += printContent
      printContent1 += `</div>`
    }
    // let originalContent = document.body.innerHTML;
    // document.body.innerHTML = printContent1;
    // window.print();
    // document.body.innerHTML = originalContent;
    document.getElementById("printContainer").innerHTML = printContent1

    w=window.open(' ');

    w.document.write($(`#printContainer`).html());
    w.print();
    w.close();
  }

  function printQC (index) {
    // let barcode = dataEditQC[index].KODEBRG
    let jumlahPrint = $(`#editQC_QtyPrint${index}`).val();
    // console.log('print : ' + barcode + ' ' + jumlahPrint +'x')
    let printContent = document.getElementById(`containerbarcodeQC${index}`).innerHTML;
    let printContent1 = ""
    for (let i=0; i < jumlahPrint ; i++ ) {
      printContent1 += `<div class="row">`
      printContent1 += printContent
      printContent1 += `</div>`
    }
    document.getElementById("printContainer").innerHTML = printContent1

    w=window.open();
    w.document.write($(`#printContainer`).html());
    w.print();
    w.close();
  }

  function getDetailLPB (NoBukti) {
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailLPB') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NoBukti
      },
      success: function(res) {
        // console.log('masok')
        dataAddLPB = res[0]

      }
    })
  }

  function getDetailQC (NoBukti) {
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailQC') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NoBukti
      },
      success: function(res) {
        // console.log('masok')
        dataEditQC = res[0]

      }
    })
  }

  function onchangeQtyReject (index) {
    // let qtyTerima = $(`#addLPB_QtyTerima${index}`).val();
    // let qtyReject= $(`#addLPB_QtyReject${index}`).val();
    // document.getElementById(`addLPB_QtyPrint${index}`).value = qtyTerima - qtyReject
  }
  function onchangeQtyRejectQC (index) {
    // let qtyTerima = $(`#editQC_QtyTerima${index}`).val();
    // let qtyReject= $(`#editQC_QtyReject${index}`).val();
    // document.getElementById(`editQC_QtyPrint${index}`).value = qtyTerima - qtyReject
  }

  // function submitEditQC() {
  //   // console.log('tesEditQCSubmit')
  //   // mno
  //   // addLPB_QtyTerima
  //   let qtyCheck = false
  //   // console.log(qtyCheck, 'start')
  //   dataEditQC.forEach((item, i) => {
  //     let QntTerimaCheck = Number($(`#editQC_QtyTerima${i}`).val())
  //     let QntRejectCheck = Number($(`#editQC_QtyReject${i}`).val())
  //     // console.log(QntTerimaCheck, QntRejectCheck , qtyCheck)
  //     // console.log('=====')
  //
  //     if (QntRejectCheck > QntTerimaCheck) {
  //       qtyCheck = true
  //       // console.log(QntTerimaCheck, QntRejectCheck , qtyCheck, 'tes')
  //     }
  //   });
  //   // console.log(qtyCheck, '1')
  //   if (qtyCheck) {
  //     // console.log(qtyCheck, 'true')
  //     // qtyCheck = false
  //     alertify.warning("Qty Reject tidak bisa lebih besar dari Qty Terima");
  //     return
  //   }
  //
  //   // console.log('false')
  //   // return
  //
  //   tempArr = []
  //   dataEditQC.forEach((item, i) => {
  //     let QntReject= $(`#editQC_QtyReject${i}`).val()
  //     let QntReject1 = 0
  //     let QntReject2 = 0
  //     // console.log (QntReject, QntReject1, QntReject2, item.NOSAT , item.ISI1, item.ISI2)
  //     if (item.NOSAT == 1) {
  //       QntReject1 = QntReject
  //       QntReject2 = QntReject / item.ISI2
  //     } else if (item.NOSAT == 2) {
  //       QntReject1 = QntReject * item.ISI2
  //       QntReject2 = QntReject
  //     }
  //     tempArr.push({
  //       NOBUKTI: item.NOBUKTI,
  //       KODEBRG: item.KODEBRG,
  //       Urut: item.URUT,
  //       QntTerima: item.QntTerima,
  //       QntTerima1: item.Qnt1Terima,
  //       QntTerima2: item.Qnt2Terima,
  //       QntReject,
  //       QntReject1,
  //       QntReject2
  //      })
  //   });
  //
  //   console.log(tempArr)
  //   let _token = $("#_token").val();
  //   $.ajax({
  //     url: "{!! url('submitLPB') !!}",
  //     type: "post",
  //     async: false,
  //     data: {
  //       _token : _token,
  //       data: tempArr
  //     },
  //     success: function(res) {
  //       // console.log('masok')
  //       if (res == 1) {
  //         console.log('==========res===========')
  //         console.log(res)
  //         alertify.success('QC telah dikoreksi');
  //         $('#formKoreksi').modal('toggle');
  //         loadAll()
  //       }
  //
  //     }
  //   })
  //
  // }

  function submitAddLPB() {
    // mno
    // addLPB_QtyTerima
    let qtyCheck = false
    // console.log(qtyCheck, 'start')
    let qtyCheckOut = false
    // console.log(qtyCheckOut, 'start')
    console.log(dataAddLPB)



    dataAddLPB.forEach((item, i) => {
      // console.log("a")
      let QntTerimaCheck = Number($(`#addLPB_QtyTerima${i}`).val())
      // console.log('b')
      let QntRejectCheck = Number($(`#addLPB_QtyReject${i}`).val())
      // console.log(QntTerimaCheck, QntRejectCheck , qtyCheck)
      // console.log('=====')
      if (QntRejectCheck > Number(item.QntTerima)) {
        qtyCheck = true
        // console.log(QntTerimaCheck, QntRejectCheck , qtyCheck, 'tes')
      }
      // console.log('c')
      // console.log("Bonn")
      // console.log(QntTerimaCheck , (Number(item.QntTerima) + Number(item.QNTOUT)))
      if (QntTerimaCheck > (Number(item.QntTerima) + Number(item.QNTOUT))) {

        qtyCheckOut = true
      }

      // console.log('d')
    });
    // console.log(qtyCheck, '1')
    if (qtyCheck) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Qty Reject tidak bisa lebih besar dari Qty Terima");
      return
    }

    if (qtyCheckOut) {
      // console.log(qtyCheck, 'true')
      // qtyCheck = false
      alertify.warning("Qty Terima melebihi LPB");
      return
    }


    // return


    tempArr = []
    dataAddLPB.forEach((item, i) => {

      let QntReject= $(`#addLPB_QtyReject${i}`).val()
      let QntReject1 = 0
      let QntReject2 = 0
      // console.log (QntReject, QntReject1, QntReject2, item.NOSAT , item.ISI1, item.ISI2)
      if (item.NOSAT == 1) {
        QntReject1 = QntReject
        QntReject2 = QntReject / item.ISI2
      } else if (item.NOSAT == 2) {
        QntReject1 = QntReject * item.ISI2
        QntReject2 = QntReject
      }

      let QntTerima= $(`#addLPB_QtyTerima${i}`).val()
      let QntTerima1 = 0
      let QntTerima2 = 0
      // console.log (QntTerima, QntTerima1, QntTerima2, item.NOSAT , item.ISI1, item.ISI2)
      if (item.NOSAT == 1) {
        QntTerima1 = QntTerima
        QntTerima2 = QntTerima / item.ISI2
      } else if (item.NOSAT == 2) {
        QntTerima1 = QntTerima * item.ISI2
        QntTerima2 = QntTerima
      }
      // console.log (QntReject, QntReject1, QntReject2, item.NOSAT, item.ISI1, item.ISI2)

      tempArr.push({
        NOBUKTI: item.NOBUKTI,
        KODEBRG: item.KODEBRG,
        Urut: item.URUT,
        QntTerima: QntTerima,
        QntTerima1: QntTerima1,
        QntTerima2: QntTerima2,
        QntReject,
        QntReject1,
        QntReject2
       })
    });
    console.log(tempArr)
    // return
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('submitLPB') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        data: tempArr
      },
      success: function(res) {
        // console.log('masok')
        if (res == 1) {
          console.log('==========res===========')
          console.log(res)
          alertify.success('QC telah diupdate');
          $('#form').modal('toggle');
          $('#contentContainer').hide();
          document.getElementById("loadingContainer").style.display="block"
          console.log('loading1')
          loadAll()
          // document.getElementById("loadingContainer").style.display="none"
          // $('#contentContainer').show();
        }

      }
    })

  }

  function buttonEditQC2(index) {
    let NoBukti = dataQCRefresh[index][0].NOBUKTI
    buttonEditQC(NoBukti)

  }


  function buttonEditQC(NoBukti) {

    console.log('buttonEditQC')
    console.log(NoBukti)

    let akses = $("#akses_iskoreksi").val();
    let _token = $("#_token").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    console.log(NoBukti)
    getDetailQC(NoBukti)
    console.log('=============tes===========')
    console.log(dataEditQC)

    // let checkLockPeriode = [{
    //   BULAN: 0,
    //   TAHUN: 0
    // }]
    // let checkDateLockPeriode = new Date(dataEditQC[0].TANGGAL)
    // // console.log('2')
    // $.ajax({
    //   url: "{!! url('lockperiodecheck') !!}",
    //   type: "post",
    //   async: false,
    //   data: {
    //     _token : _token,
    //     tahun: checkDateLockPeriode.getFullYear(),
    //     bulan: checkDateLockPeriode.getMonth() + 1
    //   },
    //   success: function(res) {
    //     console.log(res)
    //     checkLockPeriode = res
    //   }
    // })
    // // console.log('tes')
    // if ( checkLockPeriode.length ) {
    //
    //     alertify.warning("Periode sudah di kunci");
    //     return
    // }


    let rowEditQC = ""
    dataEditQC.forEach((item, i) => {
      rowEditQC += `<tr><td>${item.KODEBRG}</td><td>${item.namabrgx}</td><td>${item.SATUAN}</td><td class="text-center"><input id="editQC_QtyTerima${i}" style="width: 100px;" class="text-right" type="number" value=${item.QntTerima} disabled></td><td class="text-right"><input id="editQC_QtyReject${i} " onchange="onchangeQtyRejectQC(${i} )" style="width: 100px;" class="text-right" type="number" min=0 max=${item.QntTerima} value=${Number(item.QntReject) ? item.QntReject : '0.00' } disabled></td></tr>`

    });
    document.getElementById("editQCTableData").innerHTML = rowEditQC
    dataEditQC.forEach((item,i) => {
      //JsBarcode(`#barcodeQC${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,);
    });
    document.getElementById("editQC_NOBUKTI").value = dataEditQC[0].NOBUKTI
    document.getElementById("editQC_NOPO").value = dataEditQC[0].NoPO

    document.getElementById("editQC_Supplier").value = dataEditQC[0].NAMACUSTSUPP
    document.getElementById("editQC_Gudang").value = dataEditQC[0].NAMAGDG


    $('#formKoreksi').modal('toggle');
  }

  function buttonAddLPB2(index) {
    console.log(index)
    console.log('button add LPB 2')
    console.log(dataLPBRefresh[index])
    console.log(dataLPBRefresh[index][0])
    let NoBukti = dataLPBRefresh[index][0].NOBUKTI
    buttonAddLPB(NoBukti)

  }

  function buttonAddLPB(NoBukti) {

    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    console.log('button add LPB')
    console.log(NoBukti)
    getDetailLPB(NoBukti)
    console.log(dataAddLPB)


    let rowAddLPB = ""
    dataAddLPB.forEach((item, i) => {
      // console.log(i)
      // console.log(item.NOBUKTI, item.NamaBrg)

      rowAddLPB += `<tr><td>${item.KODEBRG}</td><td>${item.namabrgx}</td><td>${item.SATUAN}</td><td class="text-center"><input id="addLPB_QtyTerima${i}" style="width: 100px;" class="text-right" type="number" value=${item.QntTerima}></td><td class="text-center"><input id="addLPB_QtyReject${i}" onchange="onchangeQtyReject(${i})" style="width: 100px;" class="text-right" type="number" min=0 max=${item.QntTerima} value=0.00></td></tr>`

    });


    document.getElementById("addLPBTableData").innerHTML = rowAddLPB
    dataAddLPB.forEach((item,i) => {
     // JsBarcode(`#barcode${i}`, item.NOBUKTI + item.KODEBRG ,{width: 2, height: 25,});
    });
    document.getElementById("addLPB_NOBUKTI").value = dataAddLPB[0].NOBUKTI
    document.getElementById("addLPB_NOPO").value = dataAddLPB[0].NoPO

    document.getElementById("addLPB_Supplier").value = dataAddLPB[0].NAMACUSTSUPP
    document.getElementById("addLPB_Gudang").value = dataAddLPB[0].NAMAGDG

    $('#form').modal('toggle');
  }



</script>


{{-- } - karakter sisa di luar <script> (dulu tampil sebagai teks di halaman) --}}


@endsection
