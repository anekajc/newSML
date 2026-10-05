<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="Description" content="Sistem Informasi Akuntansi">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/simple-sidebar.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/bootstrap.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/fontawesome.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/all.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/style.css') !!}">
    <!-- Font Awesome CSS -->
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/alertify.css') !!}">
    <!-- Semantic CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/datatables.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/jquery-ui.min.css') !!}">
    <title></title>
    @yield('css')
    <style>
    .table {
      background: white;
    }
    body {
      background-image: url('img/image_bg_1.jpeg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }
    #top_menu{
      background-image: linear-gradient(to right, rgba(0, 0, 0, 1), rgba(238, 232, 170, 1));
    }
    </style>
  </head>
  <body>
    <div class="d-flex" id="wrapper">
      <div id="sidebar-wrapper">
        <div id="sidebar" style="padding-right: 0px !important;">
          <div class="list-group panel" style="padding-top: 2.9rem;">
            @for ($i = 0; $i < count($menu); $i++)
              @if ($menu[$i]->l0 == 0 || $i == 0)
                <a href="#menu{!! $menu[$i]->id !!}" class="list-group-item" data-toggle="collapse" data-parent="#sidebar" aria-expanded="false" onclick="selectmenu({!! $menu[$i]->id !!})" id="menuu{!! $menu[$i]->id !!}">{!! $menu[$i]->keterangan !!}</a>
                <div class="collapse" id="menu{!! $menu[$i]->id !!}">
              @else
                @if ($i + 1 < count($menu))
                  @if ($menu[$i + 1]->l0 > $menu[$i]->l0)
                    <a href="#menu{!! $menu[$i]->id !!}" class="list-group-item" data-toggle="collapse" aria-expanded="false" onclick="selectmenu({!! $menu[$i]->id !!})" id="menua{!! $menu[$i]->id !!}">{!! $menu[$i]->keterangan !!}</a>
                    <div class="collapse" id="menu{!! $menu[$i]->id !!}">
                  @elseif ($menu[$i + 1]->l0 == $menu[$i]->l0)
                    <a href="{!! url($menu[$i]->access) !!}" class="list-group-item" data-parent="#menu{!! $menu[$i]->parent !!}" onclick="selectmenu({!! $menu[$i]->id !!})" id="menua{!! $menu[$i]->id !!}">{!! $menu[$i]->keterangan !!}</a>
                  @else
                    <a href="{!! url($menu[$i]->access) !!}" class="list-group-item" data-parent="#menu{!! $menu[$i]->parent !!}" onclick="selectmenu({!! $menu[$i]->id !!})" id="menua{!! $menu[$i]->id !!}">{!! $menu[$i]->keterangan !!}</a>
                    @for ($a = 0; $a < ($menu[$i]->l0 - $menu[$i + 1]->l0); $a++)
                      </div>
                    @endfor
                  @endif
                @else
                  <a href="{!! url($menu[$i]->access) !!}" class="list-group-item" data-parent="#menu{!! $menu[$i]->parent !!}" onclick="selectmenu({!! $menu[$i]->id !!})" id="menua{!! $menu[$i]->id !!}">{!! $menu[$i]->keterangan !!}</a>
                  @for ($a = 0; $a < $menu[$i]->l0; $a++)
                </div>
                  @endfor
                @endif
              @endif
            @endfor
          </div>
        </div>
      </div>
    <!-- </div> -->

      <div class="col-12" id="page-content-wrapper">
        <nav class="navbar navbar-expand navbar-light fixed-top bg-dark" id="top_menu" style="position: fixed;">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <!-- <ul class="navbar-nav mr-auto">
            </ul> -->
            <ul class="navbar-nav mr-auto">
              <li class="nav-item"><a class="nav-link" href="#" id="menu-toggle" style="color: white;"><i class="fas fa-bars"></i>&nbsp;&nbsp;&nbsp;&nbsp;</a></li>
              @yield('breadcrumb')
              <li class="nav-item"><a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Periode" style="color: white; padding-left:0px;">&nbsp;&nbsp; |&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-clock"></i> {!! $periode->bulan !!} / {!! $periode->tahun !!} &nbsp;&nbsp; |</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">
                  <i class="fas fa-user"></i> {!! \Auth::user()->name !!}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="{!! url('logout') !!}">Logout</a>
                </div>
              </li>
              @yield('button-add-refresh')
            </ul>
          </div>
        </nav>
        <section class="content">
          <div class="container-fluid">
            @yield('content')
            <!-- <div class="card-footer">
            </div> -->
          </div>
        </section>

      </div>

    </div>
    <!-- <footer class="main-footer"> -->
      <!-- <div class="card"> -->
        <!-- <div class="card-footer">
          <strong>Copyright &copy; 2019</strong>
          All rights reserved.
          <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.
          </div>
        </div> -->
      <!-- </div> -->
    <!-- </footer> -->
    <!-- <nav class="fixed-bottom" style="position: fixed;">
        <div class="card">
          <div class="card-body">
            <strong>Copyright &copy; 2019</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
              <b>Version</b> 1.0.
            </div>
          </div>
        </div>
    </nav> -->
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{!! URL::asset('public/js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/alertify.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('public/js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/jquery-ui.min.js') !!}"></script>
    <script type="text/javascript">
    var g_id = "";
    var g_id2 = "";
    var g_name = "";
      $.fn.modal.Constructor.prototype._enforceFocus = function() {};
      (function($){
        var originalVal = $.fn.val;
        $.fn.val = function(){
          var prev;
          if (arguments.length > 0) { prev = originalVal.apply(this,[]); }
          var result = originalVal.apply(this,arguments);
          if (arguments.length > 0 && prev != originalVal.apply(this,[])) $(this).trigger('change');  // OR with custom event $(this).trigger('value-changed')
          return result;
        };
      })(jQuery);
      $("#menu-toggle").click(function(e) { e.preventDefault(); $("#wrapper").toggleClass("toggled"); if ($("#wrapper").hasClass("toggled")) { $("#sidebar").css("overflow-y", "auto"); $("body").css("overflow-x", "hidden"); } else { $("#sidebar").css("overflow-y", "hidden"); $("body").css("overflow-x", "auto"); } });
      function selectmenu(_id) {
        var menuu=0;
        g_id=_id;
        if(g_name != "menuu"+_id+" "){
          if (g_id != g_id2) {
            // $("#menuu"+g_id2+" ").css("background-image", "linear-gradient(to right, rgba(0, 0, 0, 1), rgba(238, 232, 170, 1))");
            $("#menuu"+g_id2+" ").css("background-color", "transparent");
          }
          menuu=1;
        }
        if(g_id != g_id2){
          // $("#menua"+g_id2+" ").css("background-image", "linear-gradient(to right, rgba(0, 0, 0, 1), rgba(238, 232, 170, 1))");
          $("#menua"+g_id2+" ").css("background-color", "transparent");
        }
        $("#menuu"+_id+" ").css("background-color", "#4CAF50");
        $("#menua"+_id+" ").css("background-color", "gold");
        g_id2=_id;
        g_name = "menuu"+_id+" ";
      }
    </script>
    @yield('js')
    <script type="text/javascript">
      document.onkeydown = function(e) {
        if(event.keyCode == 123) { return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ return false; }
      }
      $("button").addClass('btn-sm'); $('select').select2({ width: '100%' }); $(".form-control").addClass('form-control-sm');
      $(document).on('hidden.bs.modal', '.modal', function () { $('.modal:visible').length && $(document.body).addClass('modal-open'); });
      $('.modal').modal({ show: false, keyboard: false, backdrop: 'static' }); $("title").html($("#title_page").html());
      $(function () { $('[data-toggle="tooltip"]').tooltip() }); $("[rel='tooltip']").tooltip();
      function numberWithCommas(n) { var parts=n.toString().split("."); return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : ""); }
      function toInteger(n) { return parseInt(n.replace(/,/g, "")); } function toFloat(n) { return parseFloat(n.replace(/,/g, "")); }
      function round(value, precision) { var multiplier = Math.pow(10, precision || 0); return Math.round(value * multiplier) / multiplier; } function middleTD() {}
      function format_date(date) { if (date == "" || date == null) { return ""; } return date.split("-")[2] + "/" + date.split("-")[1] + "/" + date.split("-")[0]; }
      function format_timestamp(date) { if (date == "" || date == null) return ""; tgl = date.split(" ")[0]; waktu = date.split(" ")[1]; return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu; }
      function checkHold(no_bukti) {
        var _token = $("#_token").val(); var count;
        $.ajax({
          url     : "{!! url('checkHold') !!}",
          type    : "POST",
          async   : false,
          data    : { _token : _token, no_bukti : no_bukti },
          success : function(result) { count = result; }
        });
        if (count > 0) return true;
        return false;
      }
      function hold(no_bukti) {
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('hold') !!}",
          type    : "POST",
          async   : false,
          data    : { _token : _token, no_bukti : no_bukti }
        });
      }
      function release(no_bukti) {
        var _token = $("#_token").val();
        $.ajax({
          url     : "{!! url('release') !!}",
          type    : "POST",
          async   : false,
          data    : { _token : _token, no_bukti : no_bukti }
        });
      }
    </script>
  </body>
</html>
