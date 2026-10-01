@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Barang Jasa')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Barang Jasa</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Barang Jasa</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Barang Jasa</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

  <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
            <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
            <th style="padding: 4px 12px;" scope="col">Group</th>
            <th style="padding: 4px 12px;" scope="col">Header Group</th>
            <th style="padding: 4px 12px;" scope="col">Sub Group</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
      </tbody>
      </table>

    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="bs-form">
          <label for="input_add_kodegroup">Group</label>
          <div class="d-flex align-items-center" style="gap:8px">
            <input type="text" class="form-control" id="input_add_kodegroup" value='JS' disabled style="flex:0 0 70px">
            <input type="text" class="form-control" value='Jasa' disabled>
          </div>
          <label for="input_add_isaktif">Status</label>
          <select id="input_add_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>

          <label for="input_add_kodeheadgroup">HeadGroup</label>
          <select id="input_add_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>
          <label for="input_add_kodesubgroup">SubGroup</label>
          <select id="input_add_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>

          <label for="input_add_kodebarang">Kode Barang</label>
          <input type="text" class="form-control" id="input_add_kodebarang" disabled >
          <div style="grid-column: 3 / -1"></div>

          <label for="input_add_namabarang">Nama Barang</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang" ></div>

          <label for="input_add_namabarang2">Nama Barang 2</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang2" ></div>

          <label for="input_add_satuan">Satuan</label>
          <input type="text" class="form-control" id="input_add_satuan" >
          <label for="input_add_isi">Isi</label>
          <input type="text" value=1 disabled class="form-control" id="input_add_isi" >

          <label for="input_add_keterangan">Keterangan</label>
          <div class="bs-full"><input type="text" class="form-control text-left" id="input_add_keterangan" ></div>
        </div>





    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->


<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="bs-form">
          <label for="input_edit_kodegroup">Group</label>
          <div class="d-flex align-items-center" style="gap:8px">
            <input type="text" class="form-control" id="input_edit_kodegroup" value='JS' disabled style="flex:0 0 70px">
            <input type="text" class="form-control" value='Jasa' disabled>
          </div>
          <label for="input_edit_isaktif">Status</label>
          <select id="input_edit_isaktif" class="form-control" aria-label="Default select example">
                    <option value=1>Aktif</option>
                    <option value=0>NonAktif</option>
                  </select>

          <label for="input_edit_kodeheadgroup">HeadGroup</label>
          <select disabled id="input_edit_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control" aria-label="Default select example">
                    <option selected value="" disabled>Pilih HeadGroup</option>
                  </select>
          <label for="input_edit_kodesubgroup">SubGroup</label>
          <select disabled id="input_edit_kodesubgroup" onchange="changeInputSubGroup()"  class="form-control" aria-label="Default select example" >
                    <option selected disabled value="">Pilih SubGroup</option>
                  </select>

          <label for="input_edit_kodebarang">Kode Barang</label>
          <input type="text" class="form-control" id="input_edit_kodebarang" disabled >
          <div style="grid-column: 3 / -1"></div>

          <label for="input_edit_namabarang">Nama Barang</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_namabarang" ></div>

          <label for="input_edit_namabarang2">Nama Barang 2</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_namabarang2" ></div>

          <label for="input_edit_satuan">Satuan</label>
          <input type="text" class="form-control" id="input_edit_satuan" >
          <label for="input_edit_isi">Isi</label>
          <input type="text" value=1 disabled class="form-control" id="input_edit_isi" >

          <label for="input_edit_keterangan">Keterangan</label>
          <div class="bs-full"><input type="text" class="form-control text-left" id="input_edit_keterangan" ></div>
        </div>





    </div>
  </div>
  <div class="modal-footer">
     
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->





@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let listSelectHeadGroup = []
let listSelectSubGroup = []
let dataRefresh = []

function buttonAdd () {
  console.log('buttonAdd')

  $("#form").modal('toggle')
}

function loadAll() {
  $('#tabel').DataTable().destroy();

  // document.getElementById('breadcrumb').innerHTML = "Master Barang Jasa" // dimatikan: judul sekarang di bar atas (page-title)

  let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
  $('#tabel').DataTable({
    processing: true,
    serverSide: true,

    paging: true,
    searching: true,
    lengthChange: true,
    pageLength: currentLength,

    dom: MasterList.dom, "order": [], "language": MasterList.bahasa,

    ajax: {
      url: "{!! url('masterbarangjasaloadall') !!}",
      type: "GET"
    },
    columns: [
      {
        data: null,
        className: "text-center",
        render: function (data, type, row) {
          return `
            <div class="action-buttons-wrap text-center" style="position: relative;">
            <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${row.KODEBRG}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${row.KODEBRG}')"><i class="bi bi-trash"></i></button>
            </div>
          `;
        }
      },
      { data: 'KODEBRG' },
      { data: 'NAMABRG' },
      { data: 'nNAMAGROUP' },
      { data: 'nNAMAHDGROUP' },
      { data: 'nNAMASUBGROUP' }
    ],
    createdRow: function (row, data, dataIndex) {
      if (data.ISAKTIF == 0) {
        $(row).addClass('text-danger');
      }
    },
    lengthChange: true,
    paging: true
  });
      MasterList.selesai('#tabel')
}

function submitEdit () {
  let _token = $("#_token").val();
  console.log('submitEdit')
  let kodegroup = $("#input_edit_kodegroup").val();
  let kodeheadgroup = $("#input_edit_kodeheadgroup").val();
  let kodesubgroup = $("#input_edit_kodesubgroup").val();
  let kodebarang = $("#input_edit_kodebarang").val();
  let namabarang = $("#input_edit_namabarang").val();
  let namabarang2 = $("#input_edit_namabarang2").val();
  let satuan = $("#input_edit_satuan").val();
  let isi = $("#input_edit_isi").val();
  let isaktif = $("#input_edit_isaktif").val();
  let keterangan = $("#input_edit_keterangan").val();

  console.log('kodegroup', kodegroup)
  console.log('kodeheadgroup', kodeheadgroup)
  console.log('kodesubgroup', kodesubgroup)
  console.log('kodebarang', kodebarang)
  console.log('namabarang', namabarang)
  console.log('namabarang2', namabarang2)
  console.log('satuan', satuan)
  console.log('isi', isi)
  console.log('isaktif', isaktif)
  console.log('keterangan', keterangan)

  if (!kodegroup) {
    alertify.warning("KodeGroup harus diisi");
    return
  }
  if (!kodeheadgroup) {
    alertify.warning("KodeHeadGroup  harus diisi");
    return
  }
  if (!kodesubgroup) {
    alertify.warning("KodeSubGroup harus diisi");
    return
  }
  if (!kodebarang) {
    alertify.warning("KodeBarang harus diisi");
    return
  }
  if (!namabarang) {
    alertify.warning("NamaBarang harus diisi");
    return
  }
  if (!satuan) {
    alertify.warning("Satuan 1 harus diisi");
    return
  }
  if (!isi) {
    alertify.warning("Isi 1 harus diisi");
    return
  }
  $.ajax({
    url: "{!! url('masterbarangjasaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      namabarang,
      namabarang2,
      kodebarang,
      satuan,
      isi,
      isaktif,
      keterangan

    },
    success: function(res) {

    console.log(res)

    if (res != 1) {
      alertify.warning(res);
    }  else {
      alertify.success('Barang telah diedit');
      loadAll()
      $("#formEdit").modal('toggle')
    }

    }})

}

function submitAdd () {
  let _token = $("#_token").val();
  console.log('submitAdd')
  let kodegroup = $("#input_add_kodegroup").val();
  let kodeheadgroup = $("#input_add_kodeheadgroup").val();
  let kodesubgroup = $("#input_add_kodesubgroup").val();
  let kodebarang = $("#input_add_kodebarang").val();
  let namabarang = $("#input_add_namabarang").val();
  let namabarang2 = $("#input_add_namabarang2").val();
  let satuan = $("#input_add_satuan").val();
  let isi = $("#input_add_isi").val();
  let isaktif = $("#input_add_isaktif").val();
  let keterangan = $("#input_add_keterangan").val();

  console.log('kodegroup', kodegroup)
  console.log('kodeheadgroup', kodeheadgroup)
  console.log('kodesubgroup', kodesubgroup)
  console.log('kodebarang', kodebarang)
  console.log('namabarang', namabarang)
  console.log('namabarang2', namabarang2)
  console.log('satuan', satuan)
  console.log('isi', isi)
  console.log('isaktif', isaktif)
  console.log('keterangan', keterangan)

  if (!kodegroup) {
    alertify.warning("KodeGroup harus diisi");
    return
  }
  if (!kodeheadgroup) {
    alertify.warning("KodeHeadGroup  harus diisi");
    return
  }
  if (!kodesubgroup) {
    alertify.warning("KodeSubGroup harus diisi");
    return
  }
  if (!kodebarang) {
    alertify.warning("KodeBarang harus diisi");
    return
  }
  if (!namabarang) {
    alertify.warning("NamaBarang harus diisi");
    return
  }
  if (!satuan) {
    alertify.warning("Satuan 1 harus diisi");
    return
  }
  if (!isi) {
    alertify.warning("Isi 1 harus diisi");
    return
  }


    $.ajax({
      url: "{!! url('masterbarangjasaspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        kodegroup,
        kodeheadgroup,
        kodesubgroup,
        namabarang,
        namabarang2,
        kodebarang,
        satuan,
        isi,
        isaktif,
        keterangan

      },
      success: function(res) {

      console.log(res)

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success('Barang telah ditambah');
        loadAll()
        $("#form").modal('toggle')
      }

      }})



}



function buttonAdd () {


    $.ajax({
      url: "{!! url('masterbarangjasalistselect') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {

        console.log(res)
        // console.log(res.listHeadGroup)
        // console.log(res.listSubGroup)
        // console.log(res.listSubKategori);
        // console.log(res.listSubKategori);

        listSelectHeadGroup = res.listHeadGroup
        listSelectSubGroup = res.listSubGroup

      }})

      console.log('================')
      console.log(listSelectHeadGroup)
      console.log(listSelectSubGroup)

      let rowSelect = `<option selected disabled value="">Pilih HeadGroup</option>`
      listSelectHeadGroup.forEach((item, i) => {
        rowSelect += `
          <option value="${item.KODEHDGRP}">${item.NAMAHDGRP}</option>
        `
      });
      document.getElementById("input_add_kodeheadgroup").innerHTML = rowSelect
      document.getElementById("input_add_kodesubgroup").innerHTML =  `<option selected disabled value="">Pilih SubGroup</option>`
      // form Add dikosongkan (dulu sisa isian tambah sebelumnya ikut terbawa)
      document.getElementById("input_add_kodebarang").value = ''
      document.getElementById("input_add_namabarang").value = ''
      document.getElementById("input_add_namabarang2").value = ''
      document.getElementById("input_add_satuan").value = ''
      document.getElementById("input_add_isaktif").value = 1
      document.getElementById("input_add_keterangan").value = ''

      // let rowSelectSubGroup = `<option selected disabled value="">Pilih SubGroup</option>`

      // document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`

    $("#form").modal('toggle')
}

function changeInputHeadGroup () {
  // console.log('tes')
  // let valueGroup = $("#input_add_kodegroup").val();
  let valueHeadGroup = $("#input_add_kodeheadgroup").val();
  console.log(valueHeadGroup)

  let temp = listSelectSubGroup.filter (function (el) {
    return el.KodeHDGrp == valueHeadGroup
  })

  let rowSelect = `<option selected disabled value="">Pilih SubGroup</option>`
  temp.forEach((item, i) => {
    rowSelect += `
    <option value="${item.KodeSubGrp}">${item.NamaSubGrp}</option>
    `
  });

  document.getElementById("input_add_kodesubgroup").innerHTML = rowSelect
  // document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`
  document.getElementById("input_add_kodebarang").value = ''


  console.log(temp)
}

function changeInputSubGroup () {
  let valueHeadGroup = $("#input_add_kodeheadgroup").val();
  let valueSubGroup = $("#input_add_kodesubgroup").val();
  console.log(valueHeadGroup, valueSubGroup)

  let kodebarang = 'JS.' + valueSubGroup + '.'
  console.log(kodebarang)

  // masterbarangjasaspcheckdbbarang

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterbarangjasaspcheckdbbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodebarang: kodebarang + '%'
    },
    success: function(res) {

      console.log(res)

      if(!res.length) {
        console.log('+ 001')
        kodebarang += '001'
      } else {
        console.log(res[0].KODEBRG)
        let tes = res[0].KODEBRG
        // let temp = tes.slice(-4)
        let temp = tes.slice(kodebarang.length , kodebarang.length + 3)
        console.log(temp)
        console.log(Number(temp))
        let tempUrut = '0000' + (Number(temp) + 1)
        console.log(tempUrut.slice(-3))
        kodebarang += tempUrut.slice(-3)
      }

    }})

  document.getElementById("input_add_kodebarang").value = kodebarang
}

function buttonEdit (kodebarang) {
  console.log('buttonEdit')
  console.log(kodebarang)
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('masterbarangjasaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kodebarang: kodebarang
    },
    success: function(res) {

      console.log(res)

      // document.getElementById("input_add_kodebarang").value = ''
      document.getElementById("input_edit_kodebarang").value = res[0].KODEBRG
      document.getElementById("input_edit_namabarang").value = res[0].NAMABRG
      document.getElementById("input_edit_namabarang2").value = res[0].NamaBrg2
      document.getElementById("input_edit_satuan").value = res[0].SAT1
      document.getElementById("input_edit_isi").value = String(res[0].ISI1 ?? '').replace(/^(-?)\./, (m, minus) => minus + '0.')
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_isaktif").value = res[0].ISAKTIF
      let temp1 = `<option selected value='${res[0].KodeHdGrp}' >${res[0].nNAMAHDGROUP}</option>`
      let temp2 = `<option selected value='${res[0].KODESUBGRP}' >${res[0].nNAMASUBGROUP}</option>`


      document.getElementById("input_edit_kodeheadgroup").innerHTML = temp1
      document.getElementById("input_edit_kodesubgroup").innerHTML = temp2
    }})
    $("#formEdit").modal('toggle')
}


function buttonDelete (kodebarang) {
  console.log('buttonDeleteHarga')
  console.log(kodebarang)

  let _token = $("#_token").val();
// return
  alertify.confirm('Hapus Barang', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangjasaspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");




          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

window.onload = function() {
  loadAll();
}




</script>




@endsection
