
<!-- start detail akun -->
<div class="modal fade"  id="formDetailAkun" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunAdd()">Add Data</i></button>
        <table id="tabelDetailAkun" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Kode</th>
              <th scope="col">Perkiraan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataDetailAkun" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEdit()"><i class="bi bi-pen">Select</i></button>
                  <button type="button" onclick="buttonDetailAkunDelete()"><i class="bi bi-trash">Select</i></button>
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
<!-- End modal detail akun-->

<!-- start modal add detail akun-->
<div class="modal fade"  id="formDetailAkunAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodedetail" placeholder="Kode" disabled>
                </div>
              </div>

            </div>

          <div class="row mt-2 align-items-center">
            <div class="col-md-4">
              <label class="form-label">Hutang/Piutang</label>
            </div>
            <div class="col-md-8">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraandetail" disabled>
                <button type="button" class="btn btn-primary btn-select" onclick="buttonDetailAkunSelect()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

        </div>

        </div>
            <div class="modal-footer">
                 
                <button type="button" class="btn btn-primary" onclick="submitAddDetailAkun()">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- End modal add detail akun-->

{{-- start modal edit akun --}}
<div class="modal fade"  id="formDetailAkunEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodedetail" placeholder="Kode" disabled>
                </div>
              </div>

            </div>

            
          <div class="row mt-2 align-items-center">
            <div class="col-md-5">
              <label class="form-label">Hutang/Piutang</label>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraandetail" disabled>
                <button type="button" class="btn btn-primary btn-select" onclick="buttonDetailAkunSelect()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-primary" onclick="submitAddDetailAkunEdit()">Submit</button>
  </div>
</div>
</div>
</div>

<!-- start detail akun perkiraan -->
<div class="modal fade"  id="formDetailAkunAddPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Akun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      {{-- <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetailAkunAdd('${item.Perkiraan}')">Add Data</i></button> --}}
        <table id="tabelDetailAkunAddPerkiraan" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th scope="col">Perkiraan</th>
              <th scope="col">Keterangan</th>

            </tr>
          </thead>

          <tbody id="tabel_dataDetailAkunAddPerkiraan" class="text-left" >
            <tr>

              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEditSelect()"><i class="bi bi-pen">Select</i></button>
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
<!-- End modal detail akun perkiraan-->

{{-- END OF DETAIL AKUN ==================================================================================================== --}}

<!-- start detail akun -->
<div class="modal fade"  id="formAlamat" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alamat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <button class="btn btn-primary btn-sm" type="button" onclick="buttonAlamatAdd()">Add Data</i></button>
        <table id="tabelAlamat" class="table table-bordered table-striped"  >
          <thead id='theadCustom' class="text-center">
            <tr>
              <th scope="col">Actions</th>
              <th scope="col">Nama</th>
              <th scope="col">Alamat</th>
              <th scope="col">Telpon</th>
              <th scope="col">Fax</th>

            </tr>
          </thead>

          <tbody id="tabel_dataAlamat" class="text-left" >
            <tr>

              <td></td>
              <td></td>
              <td></td>
              <td></td>

                <td class="text-center">
                  <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                  <button type="button" onclick="buttonDetailAkunEdit()"><i class="bi bi-pen">Select</i></button>
                  <button type="button" onclick="buttonDetailAkunDelete()"><i class="bi bi-trash">Select</i></button>
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
<!-- End modal detail akun-->

<!-- start modal add detail akun-->
<div class="modal fade"  id="formAlamatAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Detail Alamat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodeDetailAlamat" placeholder="Kode" readonly>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_namaAlamat">
                </div>
              </div>

              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Up</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_upAlamat">
                </div>
              </div>

            </div>
  
            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_alamatAlamat">
                </div>
              </div>
            </div>
            
            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telp</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_telpAlamat">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Fax</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_faxAlamat">
                </div>
              </div>

            </div>

        </div>
        
        </div>
            <div class="modal-footer">
                 
                <button type="button" class="btn btn-primary" onclick="submitAddAlamat()">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- End modal add detail akun-->


<!-- start modal add detail akun-->
<div class="modal fade"  id="formAlamatEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Detail Alamat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Kode</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_kodeDetailAlamat" placeholder="Kode" disabled>
                </div>
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Nama</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_namaAlamat">
                </div>
              </div>

              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Up</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_upAlamat">
                </div>
              </div>

            </div>
  
            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Alamat</label>
                </div>
              </div>
              <div class="col-10">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_alamatAlamat">
                </div>
              </div>
            </div>
            
            <div class="row mt-2">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Telp</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_telpAlamat">
                </div>
              </div>
              
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Fax</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_edit_faxAlamat">
                </div>
              </div>

            </div>

        </div>
        
        </div>
            <div class="modal-footer">
                 
                <button type="button" class="btn btn-primary" onclick="submitAddAlamatEdit()">Submit Edit</button>
            </div>
        </div>
    </div>
</div>
<!-- End modal add detail akun-->


<script>

function submitAddDetailAkunEdit () {

  let _token = $("#_token").val();
  let kodecust = $("#input_edit_kodedetail").val();
  let perkiraan = $("#input_edit_perkiraandetail").val();

  if (!kodecust) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!perkiraan) {
    alertify.warning("Perkiraan harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastercustomerspadddetailakunedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodecust,
      perkiraan,
      perkiraanOld : perkiraanOldTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Detail Akun telah diedit");
        loadDetailAkun()
        $("#formDetailAkunEdit").modal('hide')
      }

    }})

}

function buttonAlamatEdit (nomor, kode, nama, alamat, telp, fax, up){
  document.getElementById("input_edit_kodeDetailAlamat").value = kode;
  document.getElementById("input_edit_namaAlamat").value = nama;
  document.getElementById("input_edit_alamatAlamat").value = alamat;
  document.getElementById("input_edit_telpAlamat").value = telp;
  document.getElementById("input_edit_faxAlamat").value = fax;
  document.getElementById("input_edit_upAlamat").value = up;
  nomorTemp = nomor;

  $("#formAlamatEdit").modal('toggle')
}


function submitAddAlamat () {

  let _token = $("#_token").val();
  let kodeCustSupp = $("#input_add_kodeDetailAlamat").val();
  let nama = $("#input_add_namaAlamat").val();
  let alamat = $("#input_add_alamatAlamat").val();
  let telp = $("#input_add_telpAlamat").val();
  let fax = $("#input_add_faxAlamat").val();
  let up = $("#input_add_upAlamat").val();

  console.log(kodeCustSupp, nama, alamat, telp, fax, up);
  console.log('SUBMIT ADD ALAMAT')

  $.ajax({
    url: "{!! url('mastersupplierspaddalamat') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      nama,
      alamat,
      telp,
      fax,
      up
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Alamat telah ditambah");
        loadAlamat()
        $("#formAlamatAdd").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function submitAddAlamatEdit () {

  let _token = $("#_token").val();
  let kodeCustSupp = $("#input_edit_kodeDetailAlamat").val();
  let nama = $("#input_edit_namaAlamat").val();
  let alamat = $("#input_edit_alamatAlamat").val();
  let telp = $("#input_edit_telpAlamat").val();
  let fax = $("#input_edit_faxAlamat").val();
  let up = $("#input_edit_upAlamat").val();

  $.ajax({
    url: "{!! url('mastersuppliereditalamat') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp,
      nama,
      alamat,
      telp,
      fax,
      up,
      nomor : nomorTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Alamat telah diedit");
        loadAlamat()
        $("#formAlamatEdit").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonAlamat (kodeDetail) {
  noBuktiDetailTemp = kodeDetail
  loadAlamat(kodeDetail)
  $("#formAlamat").modal('toggle')
}


function loadAlamat (kodeDetail) {

  console.log(kodeDetail);
  let _token = $("#_token").val();
  kodeDetailPerkiraan = kodeDetail;
  console.log(noBuktiDetailTemp)

  $('#tabelAlamat').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersupplierloadalamat') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeDetail: noBuktiDetailTemp
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
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
        <button class="btn-action-sm btn-action-success" type="button" onclick="buttonAlamatEdit('${item.Nomor}','${item.KodeCustSupp}','${item.Nama}','${item.Alamat}','${item.Telp}','${item.Fax}','${item.UP}')"><i class="bi bi-pen"></i></button>
        <button class="btn-action-sm btn-action-danger" type="button" onclick="buttonAlamatDelete('${item.Nomor}', '${item.KodeCustSupp}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
      <td>${item.Nama}</td>
      <td>${item.Alamat}</td>
      <td>${item.Telp}</td>
      <td>${item.Fax}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAlamat").innerHTML = rowTable;
  $("#tabelAlamat").DataTable({
    "lengthChange": false,
    "paging": false,
  });
}

function buttonAlamatDelete (kode, kodeCustSupp) {
  console.log(kode, kodeCustSupp)
  let _token = $("#_token").val();

  alertify.confirm('Hapus Akun', 'Apakah yakin ingin menghapus Data Alamat ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersupplierdeletealamat') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nomor : kode,
            kodeCustSupp
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAlamat();
              alertify.success("Customer telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });

}

function buttonAlamatAdd (){
  document.getElementById("input_add_kodeDetailAlamat").value = noBuktiDetailTemp;
  document.getElementById("input_add_namaAlamat").value = '';
  document.getElementById("input_add_upAlamat").value = '';
  document.getElementById("input_add_alamatAlamat").value = '';
  document.getElementById("input_add_telpAlamat").value = '';
  document.getElementById("input_add_faxAlamat").value = '';
  $("#formAlamatAdd").modal('toggle')
}

</script>