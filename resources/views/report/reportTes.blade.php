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
                  <button type="button" class="mt-2 btn btn-primary text-right justify-content-right" style="height: 37px;" data-toggle="modal" data-target="#formCustomize" onClick=updateHeaderFromCheckboxes()>Customize Table</button>
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
        <table id="sourceTable" class="table table-bordered table-striped">
          <thead class="text-center">
            <th style="border: 1px solid black;" scope="col">Checkbox</th>
            <th style="border: 1px solid black;" scope="col">Data</th>
            <th style="border: 1px solid black;" scope="col">Order</th>
            <!-- Table headers will be dynamically populated here -->
          </thead>
          <tbody id="tabel_dataAddGroupAktiva" class="text-left">
            <tr id="row_0">
              <td style="border: 1px solid black;" class="text-center">
                          <input type="checkbox" id="vehicle3" name="vehicle3" value="0">
                        </td>
                      <td style="border: 1px solid black;">NoBukti</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="0" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_0')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_0')"><i class="bi bi-arrow-down"></i></button>
                      </td>
              </tr>

            <tr id="row_1"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="1">
                      </td>
                      <td style="border: 1px solid black;">Tanggal</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="1" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_1')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_1')"><i class="bi bi-arrow-down"></i></button>
                      </td>
            </tr>
            <tr id="row_2"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="2">
                      </td>
                      <td style="border: 1px solid black;">KodeCustSupp</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="2" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_2')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_2')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_3"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="3">
                      </td>
                      <td style="border: 1px solid black;">NAMACUSTSUPP</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="3" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_3')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_3')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                      <tr id="row_4"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="4">
                      </td>
                      <td style="border: 1px solid black;">Urut</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="4" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_4')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_4')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_5"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="5">
                      </td>
                      <td style="border: 1px solid black;">KodeBrg</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="5" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_5')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_5')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_6"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="6">
                      </td>
                      <td style="border: 1px solid black;">NamaBrg</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="6" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_6')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_6')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_7"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="7">
                      </td>
                      <td style="border: 1px solid black;">Qnt</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="7" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_7')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_7')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_8"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="8">
                      </td>
                      <td style="border: 1px solid black;">NoSat</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="8" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_8')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_8')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_9"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="9">
                      </td>
                      <td style="border: 1px solid black;">Isi</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="9" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_9')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_9')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_10"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="10">
                      </td>
                      <td style="border: 1px solid black;">Satuan</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="10" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_10')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_10')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_11"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="11">
                      </td>
                      <td style="border: 1px solid black;">Keterangan</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="11" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_11')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_11')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_12"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="12">
                      </td>
                      <td style="border: 1px solid black;">NeedOtorisasi</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="12" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_12')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_12')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_13"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="13">
                      </td>
                      <td style="border: 1px solid black;">Qntbatal</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="13" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_13')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_13')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_14"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="14">
                      </td>
                      <td style="border: 1px solid black;">Tglbatal</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="14" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_14')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_14')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_15"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="15">
                      </td>
                      <td style="border: 1px solid black;">UserBatal</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="15" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_15')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_15')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                    <tr id="row_16"><td style="border: 1px solid black;" class="text-center">
                        <input type="checkbox" id="vehicle3" name="vehicle3" value="16">
                      </td>
                      <td style="border: 1px solid black;">PartNumber</td>
                      <td style="border: 1px solid black;" class="text-center">
                        <input type="number" class="order-input" value="16" min="0">
                        <button type="button" class="btn btn-primary" onclick="buttonUp('row_16')"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-primary" onclick="buttonDown('row_16')"><i class="bi bi-arrow-down"></i></button>
                      </td>
                    </tr>
                </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary"id="insertButton" data-dismiss="modal">Submit Customization</button>
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

let dynamicTable = '';

const _header = [
  [{ 'colName': 'NoBukti' }, { 'headName': 'No Bukti' }, { 'isTampil': false }, { 'colNumber': '0'}],
  [{ 'colName': 'Tanggal' }, { 'headName': 'Tanggal' }, { 'isTampil': true }, { 'colNumber': '1'}],
  [{ 'colName': 'KodeCustSupp' }, { 'headName': 'Customer' }, { 'isTampil': true }, { 'colNumber': '2'}],
  [{ 'colName': 'NAMACUSTSUPP' }, { 'headName': 'Nama Customer' }, { 'isTampil': true }, { 'colNumber': '3'}],
  [{ 'colName': 'Urut' }, { 'headName': 'Total' }, { 'isTampil': true }, { 'colNumber': '4'}],
  [{ 'colName': 'KodeBrg' }, { 'headName': 'Kode Barang' }, { 'isTampil': true }, { 'colNumber': '5'}],
  [{ 'colName': 'NamaBrg' }, { 'headName': 'Nama Barang' }, { 'isTampil': true }, { 'colNumber': '6'}],
  [{ 'colName': 'Qnt' }, { 'headName': 'Qnt' }, { 'isTampil': true }, { 'colNumber': '7'}],
  [{ 'colName': 'NoSat' }, { 'headName': 'No Satuan' }, { 'isTampil': true }, { 'colNumber': '8'}],
  [{ 'colName': 'Isi' }, { 'headName': 'Isi' }, { 'isTampil': true }, { 'colNumber': '9'}],
  [{ 'colName': 'Satuan' }, { 'headName': 'Satuan' }, { 'isTampil': true }, { 'colNumber': '10' }],
  [{ 'colName': 'Keterangan' }, { 'headName': 'Keterangan' }, { 'isTampil': true }, { 'colNumber': '11' }],
  [{ 'colName': 'NeedOtorisasi' }, { 'headName': 'Otorisasi' }, { 'isTampil': true }, { 'colNumber': '12' }],
  [{ 'colName': 'Qntbatal' }, { 'headName': 'Qnt Batal' }, { 'isTampil': true }, { 'colNumber': '13' }],
  [{ 'colName': 'Tglbatal' }, { 'headName': 'Tgl Batal' }, { 'isTampil': true }, { 'colNumber': '14' }],
  [{ 'colName': 'UserBatal' }, { 'headName': 'User Batal' }, { 'isTampil': true }, { 'colNumber': '15' }],
  [{ 'colName': 'PartNumber' }, { 'headName': 'Part Number' }, { 'isTampil': true }, { 'colNumber': '16' }],
];

function updateHeaderFromCheckboxes() {
  $('#sourceTable input[type="checkbox"]').each(function(index) {
    const isChecked = $(this).is(':checked');
    _header[index][2].isTampil = isChecked;
  });
}

// Call this function whenever a checkbox is changed
$('#sourceTable input[type="checkbox"]').change(function() {
  updateHeaderFromCheckboxes();
});

function closeTable () {

  document.getElementById("showTableReport").style.display = "none"
}

function makeTable() {
  let rowTable = '';
  // Generate table headers (with gray background) - done once outside the loop
  const tableHeader = generateTableHeader();

  noUrutTable = 1;
  console.log('makeTable');
  let date1 = $("#inputDate1").val();
  let date2 = $("#inputDate2").val();
  let input1 = $("#inputReport1").val();
  let input2 = $("#inputReport2").val();

  console.log(date1);
  console.log(date2);
  console.log(input1);
  console.log(input2);

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

  let periodeTable = `<th colspan="13" style="text-align: left; font-weight: bold;">PERIODE: ${formattedDateInput} S.D ${formattedDateInput2}</th>`

  document.getElementById("showTableReport").style.display = "block";
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
      console.log(res);
      let headerRow = `<tr style="background-color: lightgray; text-align: center">`;
      _header.forEach(headerItem => {
        if (headerItem[2].isTampil) {
          headerRow += `<th>${headerItem[1].headName}</th>`;
        }
      });
      headerRow += `</tr>`;

      rowTable = headerRow;

      res.forEach((item, i) => {
        // Check if it's a new group of data (based on NoBukti)
        if (nomor !== item.NoBukti) {
          // Add a separator row (optional)
          rowTable += `<tr style="text-align: center"><td colspan="13" style="border-top: 1px solid black;"></td></tr>`;
        }

        // Add data row
        rowTable += `<tr style="text-align: center">`;
        noUrutTable++;

        _header.forEach(headerItem => {
          if (headerItem[2].isTampil) {
            rowTable += `<td style="border: 1px solid black;">${item[headerItem[0].colName]}</td>`;
          }
        });

        rowTable += `</tr>`;
        nomor = item.NoBukti;
      });

      if (noUrutTable !== 1) {
        rowTable += `</tr>`; // Close the last data row if open
      }

      document.getElementById("tabel_data").innerHTML = rowTable;

      if (!res.length) {
        rowTable = `<tr style="text-align: center">
          <td colspan=13 style="border: 1px solid black;">Tidak ada data ditemukan</td>
        </tr>`;
      }
      document.getElementById("periodeTable").innerHTML = periodeTable;
      document.getElementById("tabel_data").innerHTML = rowTable;
    }
  });
}


function generateTableHeader() {
  let rowHeader = '';
  _header.forEach(headerItem => {
    if (headerItem[2].isTampil) {
      rowHeader += `<th>${headerItem[1].headName}</th>`;
    }
  });
  return rowHeader;
}

function exportTableToExcel(tableID, filename = '') {

  var downloadLink;
  var dataType = 'application/vnd.ms-excel';
  var tableSelect = document.getElementById(tableID);
  var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

  filename = filename?filename+'.xls':'Report Pengadaan.xls';
  downloadLink = document.createElement("a");
  document.body.appendChild(downloadLink);

  if(navigator.msSaveOrOpenBlob){
      var blob = new Blob(['\ufeff', tableHTML], {
          type: dataType
      });
      navigator.msSaveOrOpenBlob( blob, filename);
  }else{
      downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
      downloadLink.download = filename;
      downloadLink.click();
  }

}

function buttonUp(rowId) {
  const currentRow = $(`#${rowId}`).closest('tr');
  const prevRow = currentRow.prev('tr');
  if (prevRow.length !== 0) {
    currentRow.insertBefore(prevRow);
    updateHeaderFromRow(currentRow);
  }
}

function buttonDown(rowId) {
  const currentRow = $(`#${rowId}`).closest('tr');
  const nextRow = currentRow.next('tr');
  if (nextRow.length !== 0) {
    currentRow.insertAfter(nextRow);
    updateHeaderFromRow(currentRow);
  }
}

function updateHeaderFromRow(row) {
  const rowId = row.attr('id');
  const order = row.find('.order-input').val(); // Get the order value from the input field
  const columnIndex = parseInt(row.find('input[type="checkbox"]').attr('value')); // Get the column index from checkbox value

  console.log(columnIndex +' heheheh')

  const headerItem = _header.find(item => item[0].colNumber == columnIndex);

  // const headerCheck2 = _header;
  // console.log(headerCheck2);
  //
  // const headerCheck = _header[2][3].colNumber;
  // console.log(headerCheck);

  console.log(headerItem + " hahaha");

  // Check if header item exists (prevents undefined errors)
  if (!headerItem) {
    console.error(`Could not find header item for column index ${columnIndex}`);
    return;
  }

  // Assuming the fourth element holds the order data
  headerItem[3] = { orderHeader: parseInt(order) }; // Update the order data in the header item (if applicable)
}

function insertCheckedRows() {
  $('#sourceTable input[type="checkbox"]:checked').each(function() {
    // Get the corresponding row data
    const rowData = $(this).closest('tr').find('td').map(function() {
      return $(this).text();
    }).get();

    // Append the row data to dynamicTable
    let newRow = '<tr>';
    rowData.forEach(value => {
      newRow += `<td>${value}</td>`;
    });
    newRow += '</tr>';
    dynamicTable += newRow;
  });
}

$('#insertButton').click(insertCheckedRows);

// Function to save checkbox state to localStorage
function saveCheckboxState(id, isChecked) {
    localStorage.setItem(id, isChecked);
}

// Function to save row order to localStorage
function saveRowOrder(order) {
    localStorage.setItem('rowOrder', JSON.stringify(order));
}

// Function to retrieve checkbox state from localStorage
function getCheckboxState(id) {
    return localStorage.getItem(id) === 'true';
}

// Function to retrieve row order from localStorage
function getRowOrder() {
    const order = localStorage.getItem('rowOrder');
    return order ? JSON.parse(order) : [];
}

// Function to update checkbox state based on saved state
function updateCheckboxState() {
    $('input[type="checkbox"]').each(function() {
        const id = $(this).closest('tr').attr('id');
        const isChecked = getCheckboxState(id);
        $(this).prop('checked', isChecked);
    });
}

// Function to update row order based on saved order
function updateRowOrder() {
    const order = getRowOrder();
    const tbody = $('#sourceTable tbody');
    order.forEach(id => tbody.append($(`#${id}`)));
}

// Event listener for checkbox change
$('input[type="checkbox"]').change(function() {
    const id = $(this).closest('tr').attr('id');
    saveCheckboxState(id, $(this).prop('checked'));
});

// Event listener for button click to insert rows
$('#insertButton').click(function() {
    // Insert logic here
});

updateCheckboxState();
updateRowOrder();

</script>



@endsection
