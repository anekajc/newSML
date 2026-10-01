@extends('newmaster')


@section('buttons')

  <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-primary btn-sm" type="button"   onclick="buttonAdd()" ><i class="bi bi-plus-lg"></i></button>
  </li>
  <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-success btn-sm" type="button"  onclick="buttonEdit()" ><i class="bi bi-pencil"></i></button>
  </li>
  <li class="nav-item">
    <button style="margin-right: 5px;" class="btn btn-danger btn-sm " type="button"  onclick="buttonDelete()" ><i class="bi bi-trash"></i></button>
  </li>



@endsection

@section('content')

<h1 >Menu List</h1>



<!-- <div class="container"> -->
<div class="container-fluid">
  @include('partials.tablecomponent' , ["data" => $menu , "fields" => ['KODEMENU' , 'Keterangan', 'L0' , 'ACCESS', 'href'] , "headers" => ['Kodemenu' , 'Keterangan' , 'L0' , 'Access',"href"]] )
</div>







<!-- <div> -->

<!-- start modal add barang jadi -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>KODEMENU</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_kodemenu" placeholder="Kode Menu" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Keterangan</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_keterangan" placeholder="Keterangan" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>L0</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="number" class="form-control" id="input_l0" placeholder=0 required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Access</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_access" placeholder="Access" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Href</label>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <input type="text" class="form-control" id="input_href" placeholder="Href" required>
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add barang jadi -->
@endsection

@section('js')
  <script type="text/javascript">

    let row_id = "";
    let action = "";
    let row_data = {};


    $(document).ready(function(){
      $("#tabel").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
    { "type": "string", "className": "text-left", "targets": [0,3] },
  ]});
    });




    function buttonAdd() {
      console.log('masok')
      document.getElementById("exampleModalLabel").innerHTML = "Add Menu";
      document.getElementById("input_kodemenu").value = "";
      document.getElementById("input_access").value = "";
      document.getElementById("input_l0").value = 0;
      document.getElementById("input_keterangan").value = "";
      document.getElementById("input_href").value = "";
      action = "Add";
      console.log('masok')
      $("#form").modal('toggle');
      console.log('masok');
    }

    function buttonEdit() {
      console.log(row_id)

      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      console.log('tes1')
      document.getElementById("exampleModalLabel").innerHTML = "Edit Menu";

      document.getElementById("input_kodemenu").value = row_data.KODEMENU;
      document.getElementById("input_access").value = row_data.ACCESS;
      document.getElementById("input_l0").value = row_data.L0;
      document.getElementById("input_keterangan").value = row_data.Keterangan;
      document.getElementById("input_href").value = row_data.href;
      action = "Edit";
      $("#form").modal('toggle');
      console.log('masok' , row_data);
    }

    function buttonDelete() {
      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus menu ' + row_data.KODEMENU + row_data.Keterangan + ' ?',
      function(){
        let _token = $("#_token").val();
        let kodemenu = row_data.KODEMENU;

        $.ajax({
          url: "{!! url('deletenewmenu') !!}",
          type: "POST",
          async: false,
          data: {
            _token : _token,
            KODEMENU: kodemenu
          },
          success: function(res) {
            alertify.success('Menu telah dihapus.');
            window.location.href = "newmenu";
          }
        })

    }
      ,function(){});
    }

    function submitForm1() {
        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let href = $("#input_href").val();

        //  check input
        if (kodemenu && keterangan && access) {
          // action add
          if (action == "Add") {
            console.log("Add Menu Submit")
            console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )

            console.log('starting create')
            $.ajax({
              url : "{!! url('addnewmenu') !!}",
              type : "POST",
              async : false,
              data : {
                _token : _token,
                KODEMENU : kodemenu,
                Keterangan : keterangan,
                L0 : l0,
                ACCESS : access,
                href: href
              },
              success: function (res) {
                if (res == 1) {
                  console.log('create success')
                  window.location.href = "newmenu";
                } else {
                  console.log('create fail')
                }
              }
            })
            $("#form").modal('toggle');


          } else if (action == "Edit") {
          // action edit
            console.log("Edit Menu Submit")
            console.log("Code lama :", row_data.KODEMENU)
            console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )
            let kodelama = row_data.KODEMENU

            console.log('starting edit')
            $.ajax({
              url : "{!! url('editnewmenu') !!}",
              type : "POST",
              async : false,
              data : {
                _token : _token,
                KODEMENU : kodemenu,
                Keterangan : keterangan,
                L0 : l0,
                ACCESS : access,
                kodelama: kodelama,
                href: href
              },
              success: function (res) {
                if (res == 1) {
                  console.log('edit success')
                } else {
                  console.log('edit fail')
                }
              }
            })
            $("#form").modal('toggle');
          }

        } else {
          // alertify kolom tidak terisi
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });
        }
    }

    function submitForm() {
      if (action == "Add") {
        console.log("Add Menu Submit")
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let href = $("#input_href").val();

        console.log(_token, kodemenu, keterangan, l0, access)

        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting create')
          $.ajax({
            url : "{!! url('addnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              href: href
            },
            success: function (res) {
              console.log(res, 'add menu <<')
              if (res == 1) {
                console.log('create success')

                window.location.href = "newmenu";
              } else {
                console.log('create fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      } else {
        console.log("Edit Menu Submit")
        console.log("Code lama :", row_data.KODEMENU)
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let kodelama = row_data.KODEMENU
        let href = $("#input_href").val();


        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting edit')
          $.ajax({
            url : "{!! url('editnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              kodelama: kodelama,
              href: href
            },
            success: function (res) {
              if (res == 1) {
                console.log('edit success')
                window.location.href = "newmenu";
              } else {
                console.log('edit fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      }
    }

    function tesclick(tesprint, tesprint1) {
      row_id = tesprint;
      row_data = tesprint1;
      $('#tabel_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      $("#tr"+tesprint).css('background-color', '#FFF59E');
      console.log(tesprint, tesprint1 ,row_id , action)
    }

    // $("#tabel1").on("click", "tbody tr", function () {
    //   console.log(table.row().data())
    // })
    // function select(_row, _id, _nama) {
      // $('#barangjadi_data > tr').each(function() {
      //   $(this).css('background-color', '');
      // });
    //   $("#"+_row+"-tr").css('background-color', 'gold');
    //   g_id = _id; g_nama = _nama;
    // }

  </script>

<!-- <script type="text/javascript">
  var g_id = "", g_nama = "";
  // start document ready
	$(document).ready(function(){
    var table = $('#tabel_menu').DataTable(
      {"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]}
      // { "paging": false, "bInfo" : false, "ordering": false }
    );
    table.on('search', function () {
      $('#menu_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    table.on('order', function () {
      $('#menu_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    $(".format_number").val(0);
    $("#isi1").val(1);
    $(".format_number").autoNumeric('init');
	});
  // end document ready

  // start reset input
  function reset(name = "") {
    $("#" + name + "kode").val("");
    $("#" + name + "nama").val("");
    $("#" + name + "args1").val("");
    $("#" + name + "args2").val("");
    $("#" + name + "args3").val("");
    $("#" + name + "args4").val("");
    $("#" + name + "args5").val("");
    $("#" + name + "args6").val("");
    $("#" + name + "satuan1").val("");
    $("#" + name + "satuan2").val("");
    $("#" + name + "satuan3").val("");
    $("#" + name + "isi1").val(1);
    $("#" + name + "isi2").val(0);
    $("#" + name + "isi3").val(0);
    $("#" + name + "harga1").val(0);
    $("#" + name + "harga2").val(0);
    $("#" + name + "harga3").val(0);
    $("#" + name + "konversi").val(0);
    $("#" + name + "aktif").val(1);
    $("#" + name + "minimum").val(0);
    $("#" + name + "maksimum").val(0);
  }
  // end reset input

  // start refresh tabel
  function loadAll() {
    $('#tabel_menu').DataTable().destroy();
    $('#menu_data > tr').each(function() {
      $(this).css('background-color', '');
    });
    g_id = "", g_nama = "";
    var _token = $("#_token").val();
    $.ajax({
      url     : "{!! url('loadAllBahanBaku') !!}",
      type    : "POST",
      async   : false,
      data    : {
        _token : _token,
      },
      success : function(result) {
        result = result.data;
        if (result.length > 0) {
          $('#bahanbaku_data').html("");
          var str = "";
          for (var i = 0; i < result.length; i++) {
            str = str + '<tr id="'+i+'-tr" onclick="select(' + i + ', ' + result[i].id + ', \'' + result[i].nama + '\')">\
              <td>' + result[i].kode + '</td>\
              <td>' + result[i].nama + '</td>\
              <td>' + result[i].satuan1 + '</td>\
              <td>' + numberWithCommas(result[i].isi1) + '</td>\
              <td>' + numberWithCommas(result[i].harga1) + '</td>\
              <td>' + result[i].satuan2 + '</td>\
              <td>' + numberWithCommas(result[i].isi2) + '</td>\
              <td>' + numberWithCommas(result[i].harga2) + '</td>';
            if (result[i].satuan3 == null) {
              str = str + "<td></td>";
            } else {
              str = str + '<td>' + result[i].satuan3 + '</td>';
            }
            str = str + '<td>' + numberWithCommas(result[i].isi3) + '</td>\
              <td>' + numberWithCommas(result[i].harga3) + '</td>\
              <td>' + result[i].aktif + '</td>';
            if (result[i].konversi == 1) {
              str = str + '<td><i class="fas fa-check-circle green"></i></td>';
            } else {
              str = str + '<td><i class="fas fa-times-circle red"></i></td>';
            }
            str = str + '<td>' + numberWithCommas(result[i].minimum) + '</td>\
              <td>' + numberWithCommas(result[i].maksimum) + '</td></tr>';
          }
          $('#menu_data').html(str);
        }
        else {
          $('#menu_data').html('<tr>\
            <td colspan="15">Tidak ada data bahan baku ditemukan.</td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
            <td style="display: none;"></td>\
          </tr>');
        }
        middleTD();
      }
    });
    var table = $('#tabel_menu').DataTable(
      {"lengthMenu": [[-1, 10, 25, 50, 100], ["All", 10, 25, 50, 100]]}
      // { "paging": false, "bInfo" : false, "ordering": false }
    );
    table.on('search', function () {
      $('#bahanbaku_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
    table.on('order', function () {
      $('#menu_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      g_id = "", g_nama = "";
    } );
  } -->

@endsection
