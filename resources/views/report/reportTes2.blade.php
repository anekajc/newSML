@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-6 text-left">
      <h3>Report - Pengadaan</h3>
    </div>
  </div>
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">
          <div class="row">

          </div>
          <div class="row text-right justify-content-center">
            <div class="card w-50">
              <div class="card-body">
                <div class='row'>

                  <div class="container" style="height:30px;">
                      <div class="row justify-content-center">
                          <div class="col">
                              <h5 class="text-center">
                                  <input type="radio" id="closingPr" name="selectPr" value="Closing PR">
                                  <label for="closingPr">Closing PR</label>
                              </h5>
                          </div>
                          <div class="col">
                              <h5 class="text-center">
                                  <input type="radio" id="pr" name="selectPr" value="PR">
                                  <label for="pr">PR</label>
                              </h5>
                          </div>
                      </div>
                  </div>
                <div class="container-fluid">

                <div style="background-color: #E8E8E8; padding: 5px; " class="rounded">
                <div class="row mt-2 col-12">
                  <h5 class="col-2 text-center">Periode</h5>
                  <div class="col-3"><input id="inputDate1" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                  <div class="col-2">s/d
                  </div>
                  <div class="col-3"><input id="inputDate2" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                </div>
              </div>

                <div class="row text-center mt-3">
                  <div class="col-12">
                    <label class="col-2" for="otorisasiSelect">Otorisasi : </label>
                    <select id="inputReport1" name="otorisasiSelect" class=" col-3 form-select" aria-label="Default select example">
                      <option value="All">Otorisasi</option>
                      <option value="Non">Non-Otorisasi</option>
                      <option value="Semua">Semua</option>
                    </select>

                    <label class="col-2" for="orderBySelect">Order By : </label>
                    <select id="inputReport2" name="orderBySelect" class=" col-3 form-select" aria-label="Default select example">
                      <option value="N">No. Bukti</option>
                      <option value="T">No. Barang</option>
                    </select>
                  </div>
                </div>

                  <button type="button" class="mt-2 btn btn-primary text-right justify-content-right" style="height: 37px;" data-toggle="modal" data-target="#formFilterData">Filter Data</button>
                  <button type="button" class="mt-2 btn btn-primary text-right justify-content-right" style="height: 37px;" data-toggle="modal" onClick="buttonFormCustomize()">Customize Table</button>
                  <a href="#" class="mt-2 btn btn-primary text-right justify-content-right" onclick="makeTable()">Semua</a>
              </div>

              </div>
            </div>
          </div>

        <div class="container-fluid mt-6">

          <div id="showTableReport" style="display:none; background-color: white; padding: 10px" class="row mt-4 rounded">

              <div class="col-12 text-right">
                <button type="button" class="btn btn-success" onclick="exportTableToExcel('tabel')">Export to Excel</button>
                <button type="button" class="btn btn-secondary" onclick="closeTable()">Close Table</button>
              </div>
            <div class="col-12 mt-4" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="table table-bordered table-striped">

                      <thead class="text-left" >
                        <tr>
                          <th colspan="13"  style="text-align: left; font-weight: bold;">PT. MITRA GLOBALINDO LESTARI<br/>Report Purchasing Request Per No. Bukti<br/></th>
                        </tr>

                        <tr id="periodeTable">
                          <th colspan="13"  style="text-align: left; font-weight: bold;">PERIODE:</th>
                        </tr>
                        <tr>
                          <th colspan="13"></th>
                        </tr>
                        <tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">
                          <th scope="col" style="border: 1px solid black;">No. Bukti</th>
                          <th scope="col" style="border: 1px solid black;">Tanggal</th>
                          <th scope="col" style="border: 1px solid black;">Customer</th>
                          <th scope="col" style="border: 1px solid black;">Kode Barang</th>
                          <th scope="col" style="border: 1px solid black;">Nama Barang</th>
                          <th scope="col" style="border: 1px solid black;">Sat</th>
                          <th scope="col" style="border: 1px solid black;">Qnt</th>
                          <th scope="col" style="border: 1px solid black;">Qnt Batal</th>
                          <th scope="col" style="border: 1px solid black;">Keterangan</th>

                        </tr>

                      </thead>


                      <tbody id="tabel_data" class="text-center"  style="border: 1px solid black; text-align: center;">
                        <tr style="text-align: center">



                          <td colspan=13 style="border: 1px solid black;">Tidak ada data ditemukan</td>


                      </tr>
                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>
</div>

<!-- start modal select add group aktiva -->
<div class="modal fade" id="formCustomize" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Customize Table</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelAddGroupAktiva" class="table table-bordered table-striped">
          <thead class="text-center">
            <th scope="col"></th>
            <th scope="col">Data</th>
            <th scope="col">Order</th>
            <!-- Table headers will be dynamically populated here -->
          </thead>
          <tbody id="tabel_dataAddGroupAktiva" class="text-left">
            <!-- Table body content will be populated here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal select add group aktiva-->



<!-- start modal select add group aktiva -->
<div class="modal fade"  id="formFilterData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelFilterData" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Filter Data</th>

            </tr>
          </thead>

          <tbody id="tabel_dataFilterData" class="text-left" >
            <tr>

              <td></td>

                <!-- <td class="text-center">
                  <button type="button" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td> -->
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        </div>
  </div>
</div>
</div>
<!-- End modal select add group aktiva-->



@endsection

@section('js')
<script type="text/javascript">

let noUrutTable = 1
let nomor = ''

function closeTable () {

  document.getElementById("showTableReport").style.display = "none"
}

function makeTable () {
  noUrutTable = 1
  console.log('makeTable')
  let date1 = $("#inputDate1").val();
  let date2 = $("#inputDate2").val();
  let input1 = $("#inputReport1").val();
  let input2 = $("#inputReport2").val();


  // let _token = $("#_token").val();
  // console.log(_token)

  console.log(date1)
  console.log(date2)
  console.log(input1)
  console.log(input2)

  let monthNames = [
    "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
    "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
    ];
    let dateComponents = date1.split('-'); // Split the date string into components
    let dateComponents2 = date2.split('-');

    let inputYear = parseInt(dateComponents[0]);
    let inputYear2 = parseInt(dateComponents2[0]);

    let inputMonth = parseInt(dateComponents[1]);
    let inputMonth2 = parseInt(dateComponents2[1]);

    let inputDate = parseInt(dateComponents[2]);
    let inputDate2 = parseInt(dateComponents2[2]);

    let formattedMonth = monthNames[inputMonth - 1];
    let formattedMonth2 = monthNames[inputMonth2 - 1];

    let formattedDateInput = `${inputDate} ${formattedMonth} ${inputYear}`;
    let formattedDateInput2 = `${inputDate2} ${formattedMonth2} ${inputYear2}`;

    let periodeTable = `<th colspan="13"  style="text-align: left; font-weight: bold;">PERIODE: ${formattedDateInput} S.D ${formattedDateInput2}</th>`
  //
  document.getElementById("showTableReport").style.display = "block"
  $.ajax({
    url: "{!! url('reportTesTable') !!}",
    type: "get",
    async: false,
    data: {
      date1,
      date2,
      input1
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""

      res.forEach((item, i) => {
        let dateDokumen = new Date(item.TanggalDokumen);
        let dayDokumen = ("0" + dateDokumen.getDate()).slice(-2);
        let monthDokumen = ("0" + (dateDokumen.getMonth() + 1)).slice(-2);
        let dateShowDokumen = dateDokumen.getFullYear()+"-"+(monthDokumen)+"-"+(dayDokumen) ;
        //
        let dateLPB = new Date(item.TanggalKeluar);
        let dayLPB = ("0" + dateLPB.getDate()).slice(-2);
        let monthLPB = ("0" + (dateLPB.getMonth() + 1)).slice(-2);
        let dateShowLPB = dateLPB.getFullYear()+"-"+(monthLPB)+"-"+(dayLPB) ;
        //
        let dateInput = new Date(item.TglInput);
        let dayInput = ("0" + dateInput.getDate()).slice(-2);
        let monthInput = ("0" + (dateInput.getMonth() + 1)).slice(-2);
        let dateShowInput = dateInput.getFullYear()+"-"+(monthInput)+"-"+(dayInput) ;

        if (nomor == item.NoBukti) {
          rowTable += `<tr style="text-align: center">

          <td style="border: 1px solid black;">${item.NoBukti}</td>
          <td style="border: 1px solid black;">${item.Tanggal.toLocaleString('en-CA')}</td>
          <td style="border: 1px solid black;">${item.NAMACUSTSUPP}</td>
          <td style="border: 1px solid black;">${item.KodeBrg}</td>
          <td style="border: 1px solid black;">${item.NamaBrg}</td>
          <td style="border: 1px solid black;">${item.Satuan}</td>
          <td style="border: 1px solid black;">${item.Qnt}</td>
          <td style="border: 1px solid black;">${item.Qntbatal}</td>
          <td style="border: 1px solid black;">${item.Keterangan}</td>

          </tr>`
          noUrutTable ++

        } else {
          if (noUrutTable !== 1) {
            rowTable += `<tr style="text-align: center">

              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>

              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>
              <td style="border: 1px solid black;"></td>


            </tr>`
          }
          rowTable += `<tr style="text-align: center">

          <td style="border: 1px solid black;">${item.NoBukti}</td>
          <td style="border: 1px solid black;">${item.Tanggal.toLocaleString('en-CA')}</td>
          <td style="border: 1px solid black;">${item.NAMACUSTSUPP}</td>
          <td style="border: 1px solid black;">${item.KodeBrg}</td>
          <td style="border: 1px solid black;">${item.NamaBrg}</td>
          <td style="border: 1px solid black;">${item.Satuan}</td>
          <td style="border: 1px solid black;">${item.Qnt}</td>
          <td style="border: 1px solid black;">${item.Qntbatal}</td>
          <td style="border: 1px solid black;">${item.Keterangan}</td>


          </tr>`

          noUrutTable ++
        }
        nomor = item.NoBukti

      });

      // Set the innerHTML of the table body
      document.getElementById("tabel_data").innerHTML = rowTable;

      if(!res.length) {
        rowTable = `<tr style="text-align: center">

          <td colspan=13 style="border: 1px solid black;">Tidak ada data ditemukan</td>



        </tr>`
      }
      document.getElementById("periodeTable").innerHTML = periodeTable
      document.getElementById("tabel_data").innerHTML = rowTable

  }})
}

function exportTableToExcel(tableID, filename = '') {

  // makeTable()
  // return
  var downloadLink;
  var dataType = 'application/vnd.ms-excel';
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

  // Specify file name
  filename = filename?filename+'.xls':'Report Mutasi Mesin.xls';

  // Create download link element
  downloadLink = document.createElement("a");

  document.body.appendChild(downloadLink);

  if(navigator.msSaveOrOpenBlob){
      var blob = new Blob(['\ufeff', tableHTML], {
          type: dataType
      });
      navigator.msSaveOrOpenBlob( blob, filename);
  }else{
      // Create a link to the file
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

      // Setting the file name
      downloadLink.download = filename;

      //triggering the function
      downloadLink.click();
  }

}

// function buttonFormCustomize () {
//   let _token = $("#_token").val();
//   $("#formCustomize").modal('toggle')
//
//   $.ajax({
//     url: "{!! url('takeDataFormCustomize') !!}",
//     type: "get",
//     async: false,
//     data: {
//       _token: _token,
//     },
//     success: function (res) {
//       console.log(res);
//       dataRefresh = res;
//     },
//   });
// }

function buttonFormCustomize() {
    let _token = $("#_token").val();
    $("#formCustomize").modal('toggle')

    let date1 = $("#inputDate1").val();
    let date2 = $("#inputDate2").val();
    let input1 = $("#inputReport1").val();
    let input2 = $("#inputReport2").val();

    console.log(date1)
    console.log(date2)
    console.log(input1)
    console.log(input2)

    $.ajax({
        url: "{!! url('takeDataFormCustomize') !!}",
        type: "get",
        async: false,
        data: {
            _token: _token,
              date1,
              date2,
              input1
        },
        success: function(res) {
            console.log(res);
            populateTable(res); // Call function to populate both table headers and body
        },
    });
}

function populateTable(data) {
    let tableHeaders = ''; // Initialize empty string for table headers
    let tableBody = ''; // Initialize empty string for table body

    // Populate table headers
    Object.keys(data[0]).forEach(key => {
        tableHeaders += `<th scope="col">${key}</th>`;
    });



    // Populate table body
    data.forEach(row => {
    tableBody += '<tr>'; // Begin a new table row
    const rowValues = Object.values(row).join(''); // Concatenate all values of the row into one string
    tableBody += `<td style="border: 1px solid black;">checkbox</td>
                  <td style="border: 1px solid black;">${rowValues}</td>
                  <td style="border: 1px solid black;" class="text-center">
                    <button type="button" class="btn btn-primary" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" class="btn btn-primary" onclick="buttonPilihAkumulasiPerkiraan()"><i class="bi bi-arrow-down"></i></button>
                  </td>`; // Add a single cell with all the row's values
    tableBody += '</tr>'; // End the table row
});

    // Insert table headers into thead
    $('#tabelAddGroupAktiva thead').html();

    // Insert table body into tbody
    $('#tabel_dataAddGroupAktiva').html(tableBody);
}







</script>



@endsection
