@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      <h1>Master Set Posting Piutang</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Posting Piutang</button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>
<div>
    <div class="col-6 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="height: 60px; width: 100px; " onclick="backToSetPosting()"  >Kembali ke Set Posting</button>
    </div>
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid" style="max-width: 900px">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
          <div class="row mt-4">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <div class="row mt-3">
            <div class="col-12" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="table table-bordered table-striped"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Perkiraan</th>
                          <th scope="col">Keterangan</th>
                          <th scope="col">Kas dan Bank?</th>
                          <th scope="col">Actions</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr >

                          <td>{{ $listData[$i]->Perkiraan }}</td>
                          <td>{{ $listData[$i]->keterangan }}</td>
                          <td>{{ $listData[$i]->IsLokalorExim }}</td>


                            <td class="text-center">
                              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                              <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('{{ $listData[$i]->Perkiraan }}')"><i class="bi bi-pen"></i></button>
                              <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('{{ $listData[$i]->Perkiraan }}')"><i class="bi bi-trash"></i></button>
                            </td>
                      </tr>
                      @endfor
                      </tbody>


                    </table>
              </div>
            </div>
          </div>


</div>


<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
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
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode" disabled>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-lg " style="height: 30px; " onclick="buttonSelectPerkiraan()"  >Select</button>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kas dan Bank?</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_add_nama">
                  <option value=0>N</option>
                  <option value=1>Y</option>
                </select>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Uang Muka?</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_add_uangmuka">
                  <option value=0>N</option>
                  <option value=1>Y</option>
                </select>
                </div>
              </div>

            </div>


    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
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
                  <label class="text-left">Perkiraan</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kode"disabled>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-lg " style="height: 30px; " onclick="buttonEditSelectPerkiraan()"  >Select</button>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kas dan Bank?</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_edit_nama">
                  <option value=0>N</option>
                  <option value=1>Y</option>
                </select>
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Uang Muka?</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <select class="form-control" id="input_edit_uangmuka">
                  <option value=0>N</option>
                  <option value=1>Y</option>
                </select>
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->

<!-- start modal aktiva select perkiraan -->
<div class="modal fade"  id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelAktivaSelectPerkiraan" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAktivaSelectPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonPilihPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal aktiva select perkiraan-->

<!-- start modal edit aktiva select perkiraan -->
<div class="modal fade"  id="formEditSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Posting Akumulasi Select Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabelEdiAktivaSelectPerkiraan" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="tabel_dataEditAktivaSelectPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonEditPilihPerkiraan()"><i class="bi bi-pen">Select</i></button>
                </td>
          </tr>
          </tbody>


        </table>


    </div>
        <div class="modal-footer">
           
        </div>
  </div>
</div>
</div>
<!-- End modal edit aktiva select perkiraan-->



@endsection

@section('js')
<script type="text/javascript">

let dataRefresh = []

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
        //    "columnDefs": [
        // { "type": "date", "targets": [1] },
        // {  "className": "text-center", "targets": [3] },
      // ]
    });
});
//
function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();


  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingpiutangloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
    <td>${item.Perkiraan}</td>
    <td>${item.keterangan}</td>
    <td>${item.IsLokalorExim}</td>
    <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
    </td>
    </tr>`
  });





  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

}
//
function buttonAdd () {


  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostingpiutangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].Perkiraan
      document.getElementById("input_edit_nama").value = res[0].IsLokalorExim

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Devisi', 'Apakah yakin ingin menghapus Perkiraan Posting Piutang ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostingpiutangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Perkiraan Posting Piutang telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let nama = $("#input_edit_nama").val();

  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostingspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      new_perkiraan
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Piutang telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let uangmuka = $("#input_add_uangmuka").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostingpiutangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      uangmuka
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Piutang telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function backToSetPosting() {
  window.location.href = "{{ ('mastersetposting') }}";
}

function buttonSelectPerkiraan () {
  loadSelectPerkiraan()
  $("#formSelectPerkiraan").modal('toggle')
}

function loadSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingpiutangloadperkiraan') !!}",
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

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilihPerkiraan('${item.Perkiraan}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelAktivaSelectPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonPilihPerkiraan(selectedPerkiraan) {
  $("#input_add_kode").val(selectedPerkiraan);
  $("#formSelectPerkiraan").modal("hide");

}

function buttonEditSelectPerkiraan () {
  loadEditSelectPerkiraan()
  $("#formEditSelectPerkiraan").modal('toggle')
}

function loadEditSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostingpiutangloadperkiraan') !!}",
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

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPilihPerkiraan('${item.Perkiraan}')">Select</button>
      </td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAktivaSelectPerkiraan").innerHTML = rowTable;
  $("#tabelEditAktivaSelectPerkiraan").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonEditPilihPerkiraan(selectedPerkiraan) {
  $("#input_edit_kode").val(selectedPerkiraan);
  $("#formEditSelectPerkiraan").modal("hide");

}

</script>




@endsection
