<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <!-- Modal -->
<div class="modal fade"  id="formSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelSelect" class="table table-bordered table-striped"  >
          <thead class="text-center" id="tabelHeader">
            <tr>
              <th scope="col"></th>
            </tr>
          </thead>

          <tbody id="tabel_dataSelect" class="text-left" >
            <tr>
              <td></td>
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
<!-- End modal aktiva select customer-->

  </body>
</html>


<script>

function buttonPilih(idPart, selectedData)
  {
      console.log(idPart, 'asdqwdqwdqwd', selectedData)
      if (idPart == 1)
      {
        $("#inputCustomer").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 2)
      {
        $("#inputGroup").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 3)
      {
        $("#inputGudang").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 4)
      {
        $("#inputKategori").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 5)
      {
        $("#inputSubKategori").val(selectedData);
        $("#formSelect").modal("hide");
      }
      else if (idPart == 6)
      {
        $("#inputMerk").val(selectedData);
        $("#formSelect").modal("hide");
      }

  }

function buttonSelect (idModal)
{
  $("#formSelect").modal('toggle')

  if (idModal == "selectCustomer")
  {
    loadSelectCustomer();
  }
  else if (idModal == "selectGroup")
  {
    loadSelectGroup();
  }
  else if (idModal == "selectGudang")
  {
    loadSelectGudang();
  }
  else if (idModal == "selectKategori")
  {
    loadSelectKategori();
  }
  else if (idModal == "selectSubKategori")
  {
    loadSelectSubKategori();
  }
  else if (idModal == "selectMerk")
  {
    loadSelectMerk();
  }

}

function loadSelectCustomer() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadcustomer') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });
  let namaSelect = "Select Customer"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Kota</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KodeCustSupp}</td>
      <td>${item.NamaCustSupp}</td>
      <td>${item.NamaKota}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('1','${item.KodeCustSupp}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectGroup() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanstockfisikgudang_loadgroup') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Group"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KodeHDGRP}</td>
      <td>${item.NamaHDGRP}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('2', '${item.KodeHDGRP}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectGudang() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanstockfisikgudang_loadgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Gudang"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode Gudang</th>
        <th>Nama Gudang</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KODEGDG}</td>
      <td>${item.NAMA}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('3', '${item.KODEGDG}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectKategori() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadkategori') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Kategori"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode Sub Group</th>
        <th>Nama Sub Group</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KOdeSubGrp}</td>
      <td>${item.NamaSubGrp}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('4', '${item.KOdeSubGrp}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectSubKategori() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadsubkategori') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Sub-Kategori"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Kode Jenis</th>
        <th>Nama Jenis</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.Urut}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('5', '${item.Urut}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

function loadSelectMerk() {
  let _token = $("#_token").val();

  $('#tabelSelect').DataTable().destroy();

  $.ajax({
    url: "{!! url('laporanmarketingso_loadmerk') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let namaSelect = "Select Merk"
  document.getElementById('exampleModalLabel').innerHTML = namaSelect;

  let headerTable =
        `<tr>
        <th>Nama</th>
        <th>Kode Merk</th>
        <th>Actions</th>
        </tr>
        `;
  document.getElementById("tabelHeader").innerHTML = headerTable;

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.KodeMerk}</td>
      <td>${item.NamaMerk}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('6', '${item.KodeMerk}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataSelect").innerHTML = rowTable;
  $("#tabelSelect").DataTable({
    "lengthChange": false,
    "paging": true,
  });
}

</script>
