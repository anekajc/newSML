<!DOCTYPE html>
<html dir="ltr" lang="en-US">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />

    <!-- Stylesheets
  ============================================= -->
    <link
      href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
      rel="stylesheet"
      type="text/css"
    />
    <link rel="stylesheet" href="{!! URL::asset('css/semantic.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/select2.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}">

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
  ============================================= -->
    <title>{{$akses['namamenu']}}</title>

    @yield('css')
    <!-- <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
          background: none;
          color: black!important;
          border-radius: 4px;
          border: 1px solid #FFFFFF;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
          background: none;
          color: black!important;
        }
    </style> -->
  </head>

  <body class="stretched" >
    <!-- Document Wrapper

  ============================================= -->
    <div id="wrapper" class="clearfix">
      <!-- Header
    ============================================= -->
      <header id="header" class="full-header">
        <div id="header-wrap" style="height:50px;">
          <div class="container" style="height:50px;">
            <div class="header-row" style="height:50px;">
              <!-- Logo
            ============================================= -->
            <div id="logo">
              <a
                href = 'home'
                class="navbar-brand"
                data-dark-logo="images/logo-dark.png"
                >{{$akses['program']}}</a
              >
              </div>
              <!-- #logo end -->

              <!-- <div class="header-misc">
              <div id="top-search" class="" style="height:50px;">
                  <ul class="menu-container" style="height:30px !important; justify-content:end;">
                    <a class="mt-3" href="{!! url('logout') !!}"><div>Logout</div></a>
                  <ul>
              </div>
              </div> -->

              <div id="primary-menu-trigger">
                <svg class="svg-trigger" viewBox="0 0 100 100">
                  <path
                    d="m 30,33 h 40 c 3.722839,0 7.5,3.126468 7.5,8.578427 0,5.451959 -2.727029,8.421573 -7.5,8.421573 h -20"
                  ></path>
                  <path d="m 30,50 h 40"></path>
                  <path
                    d="m 70,67 h -40 c 0,0 -7.5,-0.802118 -7.5,-8.365747 0,-7.563629 7.5,-8.634253 7.5,-8.634253 h 20"
                  ></path>
                </svg>
              </div>

              <!-- Primary Navigation
            ============================================= -->

              <nav class="primary-menu" style="height:50px">
                <ul class="menu-container" style="justify-content: left !important; align-items: left !important; height:50px;">
                  @foreach ($akses['menul0'] as $menu0)
                    @if (count($menu0['child']) == 0)
                    <li class="menu-item" style="height:50px">
                      <a class="menu-link" href="{{url($menu0->href)}}"><div>{{$menu0['Keterangan']}}</div></a>
                    </li>
                    @else
                    <li class="menu-item"style="display:flex;">
                      <a class="" style="padding:15px; color:black;" href="{{url($menu0->href)}}"><div>{{$menu0['Keterangan']}}</div></a>
                      <ul class="sub-menu-container" >
                        @foreach ($menu0['child'] as $menu1)
                          @if (count($menu1['child']) == 0)
                          <li class="menu-item">
                            <a class="menu-link" href="{{url($menu1->href)}}"><div>{{ $menu1['Keterangan'] }}</div></a>
                          </li>
                          @else
                          <li class="menu-item">
                            <a class="menu-link" href="{{url($menu1->href)}}"><div>{{ $menu1['Keterangan'] }}</div></a>
                            <ul class="sub-menu-container">
                              @foreach ($menu1['child'] as $menu2)
                                @if (count($menu2['child']) == 0)
                                <li class="menu-item">
                                  <a class="menu-link" href="{{url($menu2->href)}}"><div>{{ $menu2['Keterangan'] }}</div></a>
                                </li>
                                @else
                                <li class="menu-item">
                                  <a class="menu-link" href="{{url($menu2->href)}}"><div>{{ $menu2['Keterangan'] }}</div></a>
                                  <ul class="sub-menu-container">
                                      @foreach ($menu2['child'] as $menu3)
                                      <li class="menu-item">
                                        <a class="menu-link" href="{{url($menu3->href)}}"
                                          ><div>{{ $menu3['Keterangan'] }}</div></a>
                                      </li>
                                      @endforeach
                                    </ul>
                                </li>
                                @endif
                              @endforeach
                            </ul>
                          </li>
                          @endif
                        @endforeach
                      </ul>
                    </li>
                    @endif
                  @endforeach
                </ul>
              </nav>
              <!-- #primary-menu end -->
              <div style="justify-content: end;">
                <ul class="menu-container" style="height:50px;">
                  <a class="mt-1 ml-3" style="color: red;" href="{!! url('home') !!}"><div> </i>
                    ↪
                  </div></a>
                <ul>
              </div>
            </div>
          </div>
        </div>

      </header>
    <header id="header" class="full-header" style="z-index: 1">
      <div id="header-wrap">
        <!-- <div class="container"> -->
          <!-- <div class="header-row"> -->

            <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="height: 25px;">
            <!-- <div class="container"> -->
               <ul class="navbar-nav mr-auto">
                   <div style="color: white">
                    Username : {!! \Auth::user()->username !!}  -  Periode : {!! $akses['periode']->bulan !!} / {!! $akses['periode']->tahun !!}
                   </div>

               </ul>
               <div class="d-flex align-items-center align-middle" style="vertical-align: middle;">
                  <ul class="navbar-nav ml-auto">
                    @yield('buttons')


                  </ul>
                </div>
              </nav>
          <!-- </div> -->
        <!-- </div> -->
      </div>
    </header>




      <!-- #header end -->

      <!-- Page Title
    ============================================= -->

      <!-- #page-title end -->

      <!-- Content
    ============================================= -->
      <section id="content" class="mt-3 mb-6">
        <div class="content-wrap" style='margin-top:-40px;'>
          <!-- <div class="container clearfix"> -->
          <div class="container-fluid px-5 clearfix">
            <div class="row gutter-40 col-mb-80">
              <!-- <h1>asd</h1> -->
              <!-- Post Content
            ============================================= -->
            <!-- <div class="line"></div> -->
            @yield('content')

            <!-- start modal customize table -->
            <div class="modal fade"  id="formCustomizeTable" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm modal-dialog-centered"  role="document" style="max-width: 40%">
                <div class="modal-content">
                  <div class="modal-header text-right">
                    <h5 class="modal-title" id="formCustomizeTableLabel">Customize Table</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeFormCustomizeTable()">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="container-fluid">
                      <div class="row mt-3">
                        <div class="col-12" id="tabelcustomize_data" style="overflow:auto;">
                          
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="doResetHeader()" title="Reset">Reset</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeFormCustomizeTable()" title="Tutup (Esc)">Tutup</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End modal customize table -->


            <!-- start modal filter data -->
            <div class="modal fade"  id="formFilterData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm modal-dialog-centered"  role="document" style="max-width: 50%">
                <div class="modal-content">
                  <div class="modal-header text-right">
                    <h5 class="modal-title" id="formFilterDataLabel">Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeFormFilterData()">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="container-fluid">
                      <div class="row mt-3">
                        <div class="col-12" style="overflow:auto;">
                          <div class="">
                                <table id="tabelfilter" class="table table-bordered table-striped"  >
                                  <thead id="tabelfilter_header" class="text-center">
                                    <tr>
                                      <th>Nomor Bukti</th>
                                      <th>Tanggal</th>
                                    </tr>
                                  </thead>
                                  <tbody id="tabelfilter_data" class="text-center" >
                                  </tbody>
                                </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeFormFilterData()" title="Batal (Esc)">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="showReportFilter()" title="Submit (Ctrl+F)">Submit</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End modal filter data -->
            <!-- <div class="postcontent col-lg-9">

              <div class="row">
                <div class="col-md-4 mb-5 mb-md-0 text-center">
                  <div class="rounded-skill mb-0" data-color="#00ACEE" data-size="150" data-percent="90" data-width="2" data-speed="3000"><i class="icon-twitter2"></i></div>
                </div>

                <div class="col-md-4 mb-5 mb-md-0 text-center">
                  <div class="rounded-skill mb-0" data-color="#3B5998" data-size="150" data-percent="75" data-width="3" data-speed="4000"><i class="icon-facebook2"></i></div>
                </div>

                <div class="col-md-4 mb-5 mb-md-0 text-center">
                  <div class="rounded-skill mb-0" data-color="#EA4C89" data-size="150" data-percent="80" data-width="4" data-speed="2000"><i class="icon-dribbble2"></i></div>
                </div>
              </div>

              <div class="line"></div>

              <div class="row">
                <div class="col-sm-6 mb-5 mb-sm-0 text-center">
                  <div class="rounded-skill mb-0" data-color="#DD4B39" data-size="200" data-percent="70" data-width="3" data-speed="2500">Static Text</div>
                </div>

                <div class="col-sm-6 mb-5 mb-sm-0 text-center">
                  <div class="rounded-skill mb-0" data-color="#3F729B" data-size="200" data-percent="85" data-width="3" data-speed="6500">
                    <div class="counter counter-inherit"><span data-from="1" data-to="85" data-refresh-interval="50" data-speed="6000"></span>%</div>
                  </div>
                </div>
              </div>

              <div class="divider"><i class="icon-circle"></i></div>

              <h3>Skills</h3>

              <ul class="skills">
                <li data-percent="80">
                  <span>Wordpress</span>
                  <div class="progress">
                    <div class="progress-percent"><div class="counter counter-inherit counter-instant"><span data-from="0" data-to="80" data-refresh-interval="30" data-speed="1100"></span>%</div></div>
                  </div>
                </li>
                <li data-percent="60">
                  <span>CSS3</span>
                  <div class="progress">
                    <div class="progress-percent"><div class="counter counter-inherit counter-instant"><span data-from="0" data-to="60" data-refresh-interval="30" data-speed="1100"></span>%</div></div>
                  </div>
                </li>
                <li data-percent="90">
                  <span>HTML5</span>
                  <div class="progress">
                    <div class="progress-percent"><div class="counter counter-inherit counter-instant"><span data-from="0" data-to="90" data-refresh-interval="30" data-speed="1100"></span>%</div></div>
                  </div>
                </li>
                <li data-percent="70">
                  <span>jQuery</span>
                  <div class="progress">
                    <div class="progress-percent"><div class="counter counter-inherit counter-instant"><span data-from="0" data-to="70" data-refresh-interval="30" data-speed="1100"></span>%</div></div>
                  </div>
                </li>
              </ul>

            </div> -->


              <!-- .postcontent end -->

              <!-- Sidebar
            ============================================= -->

              <!-- .sidebar end -->
            </div>
          </div>
        </div>
      </section>
      <!-- #content end -->

      <!-- Footer
    ============================================= -->
      <!-- <footer id="footer" class="dark">
        <div class="container">
        </div>

        <div id="copyrights">
          <div class="container">
            <div class="row col-mb-30">
              <div class="col-md-6 text-center text-md-left">
                Copyrights &copy; 2020 All Rights Reserved by Canvas Inc.<br />
                <div class="copyright-links">
                  <a href="#">Terms of Use</a> / <a href="#">Privacy Policy</a>
                </div>
              </div>

              <div class="col-md-6 text-center text-md-right">
                <div
                  class="d-flex justify-content-center justify-content-md-end"
                >
                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-facebook"
                  >
                    <i class="icon-facebook"></i>
                    <i class="icon-facebook"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-twitter"
                  >
                    <i class="icon-twitter"></i>
                    <i class="icon-twitter"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-gplus"
                  >
                    <i class="icon-gplus"></i>
                    <i class="icon-gplus"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-pinterest"
                  >
                    <i class="icon-pinterest"></i>
                    <i class="icon-pinterest"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-vimeo"
                  >
                    <i class="icon-vimeo"></i>
                    <i class="icon-vimeo"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-github"
                  >
                    <i class="icon-github"></i>
                    <i class="icon-github"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-yahoo"
                  >
                    <i class="icon-yahoo"></i>
                    <i class="icon-yahoo"></i>
                  </a>

                  <a
                    href="#"
                    class="social-icon si-small si-borderless si-linkedin"
                  >
                    <i class="icon-linkedin"></i>
                    <i class="icon-linkedin"></i>
                  </a>
                </div>

                <div class="clear"></div>

                <i class="icon-envelope2"></i> info@canvas.com
                <span class="middot">&middot;</span>
                <i class="icon-headphones"></i> +1-11-6541-6369
                <span class="middot">&middot;</span>
                <i class="icon-skype2"></i> CanvasOnSkype
              </div>
            </div>
          </div>
        </div>
      </footer> -->


    </div>
    <!-- #wrapper end -->

    <!-- Go To Top
  ============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>

    <!-- External JavaScripts
    ============================================= -->

    <script src="{!! URL::asset('js/canvas/jquery.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>
    <script src="{!! URL::asset('js/qrcode.min.js') !!}"></script>
    <script src="{!! URL::asset('js/browsemaster.js') !!}"></script>

    <!-- Footer Scripts
  ============================================= -->
  <script src="{!! URL::asset('js/canvas/functions.js') !!}"></script>
  <script src="{!! URL::asset('js/canvas/JsBarcode.all.min.js') !!}"></script>
  <!-- <script type="text/javascript" src="http://www.example.co.uk/assets/js/autoNumeric.js"></script> -->


    <script type="text/javascript">
      var g_modeModal = "";
      var g_modal_filterdata = "filterdata", g_modal_customizetable = "customizetable";

      var g_lastrowfilter = -1;

      document.onkeydown = function(e) {
        if(event.keyCode == 123) { return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ return false; }
        if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ return false; }

        if(e.keyCode === 13 && e.ctrlKey){ return doCtrlEnter(); }
        if(e.keyCode === 38 && e.ctrlKey){ return godown(); }
        if(e.keyCode === 27){ return doEscButton(); }
        
        if(e.ctrlKey && e.shiftKey && e.altKey && e.keyCode === 90) {
          // Ctrl + Shift + Alt + Z
          e.preventDefault(); // Prevent any default action
          doAdminMenu();
        }
      }
      $("button").addClass('btn-sm'); $(".form-control").addClass('form-control-sm');
      $(document).on('hidden.bs.modal', '.modal', function () { $('.modal:visible').length && $(document.body).addClass('modal-open'); });
      $('.modal').modal({ show: false, keyboard: false, backdrop: 'static' }); $("title").html($("#title_page").html());
      $(function () { $('[data-toggle="tooltip"]').tooltip() }); $("[rel='tooltip']").tooltip();

      function doAdminMenu() {
        doUpdateDBMENUREPORT();
      }

      function doUpdateDBMENUREPORT() {
        $.ajax({
          url     : "{!! url('globalfunctions_doUpdateDBMENUREPORT') !!}",
          type    : "get",
          async   : false,
          success: function(res) {
            if (res == "1") { alertify.success("Update berhasil"); }
          }
        })
      }

      function onEnterSetup(_id_name) {
        var input = document.getElementById(_id_name);
        input.addEventListener("keypress", function(event) {
          if (event.key === "Enter") {
            /* Buatlah function bernama onEnterFunction */
            /* Isinya berupa Switch Case per nama ID */
            onEnterFunction(_id_name);
          }
        });
      }

      function doCtrlEnter() {
        switch (g_modeModal) {
          case g_modal_filterdata :
                     doShowReportFilter();
                     break;
                                  
          default :
                     doMakeTable();
                     break;
        }
      }

      function doEscButton() {
        switch (g_modeModal) {
          case g_modal_customizetable :
                     closeFormCustomizeTable();
                     break;
                         
          case g_modal_filterdata :
                     closeFormFilterData();
                     break;
         
          default :
                    return;
        }
      }

      function generateDateNow() {
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);

        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        return today;
      }

      function generatTimeNow() {
        var now = new Date();
        return now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
      }

      function numberWithCommas(n) { var parts=n.toString().split("."); return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : ""); }
      function numberNoCommas(n) { return n.replace(/,/g, ""); }

      // function numberNoDecimals(n) { var _num = (n==".00") ? "0" : n.replace(".00", ""); return _num; }
      function numberNoDecimals(n) {
        var _isDesimal = false;
        for (var i = 0; i < n.length; i++) {
          if (n[i] == ".") { _isDesimal = true; break; }
        }
        if (!_isDesimal) { return (n == "") ? "0" :n; }

        var _num = n;

        while (_num != "") {
          var lastChar = _num[_num.length -1];
          if (lastChar == "0") {
            // menghilangkan semua nol di belakang koma
            _num = _num.substring(0, _num.length-1);
          } else {
            // hilangkan jika koma, ada kemungkinan nilainya jadi empty
            if (lastChar == ".") {
              _num = _num.substring(0, _num.length-1);
            }

            // jika ketemu yang bukan nol, loop berhenti
            break;
          }
        }

        return (_num == "") ? "0" : _num;
      }

      function currencyNormalizer(n) {
        var _isDesimal = false;
        for (var i = 0; i < n.length; i++) {
          if (n[i] == ".") { _isDesimal = true; break; }
        }
        if (!_isDesimal) { return (n == "") ? 0.0 : parseFloat(n); }

        var _num = n;

        while (_num != "") {
          var lastChar = _num[_num.length -1];
          if (lastChar == "0") {
            // menghilangkan semua nol di belakang koma
            _num = _num.substring(0, _num.length-1);
          } else {
            // hilangkan jika koma, ada kemungkinan nilainya jadi empty
            if (lastChar == ".") {
              _num = _num.substring(0, _num.length-1);
            }

            // jika ketemu yang bukan nol, loop berhenti
            break;
          }
        }

        return (_num == "") ? 0.0 : parseFloat(_num);
      }

      function removeSpasi(n) { return n.replace(/ /g, ""); }

      function numbersWithDividers(n) {
        if (isNaN(n)) return ""; // Handle non-numeric input gracefully

        // Use only the integer part of the number
        let integerPart = Math.floor(n).toString();

        // Format the integer part with dot separators
        return integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      //TIME IS MONEY
      let dateNow = new Date();
      let dateHours = dateNow.getHours();    // Get current hour (0-23)
      let dateMinutes = dateNow.getMinutes(); // Get current minute (0-59)
      let dateSeconds = dateNow.getSeconds(); // Get current second (0-59)

      function padZero(num) {
        return num.toString().padStart(2, '0');
      }

   	  let dateIndo = dateNow.toLocaleDateString('id-ID');

      function toInteger(n) { return parseInt(n.replace(/,/g, "")); }
      function toFloat(n) { n= (n.substring(0,1)=="." ? "0" : "")+n; return parseFloat(n.replace(/,/g, "")); }

      function round(value, precision) { var multiplier = Math.pow(10, precision || 0); return Math.round(value * multiplier) / multiplier; } function middleTD() {}
      function format_timestamp(date) { if (date == "" || date == null) return ""; tgl = date.split(" ")[0]; waktu = date.split(" ")[1]; return tgl.split("-")[2] + "/" + tgl.split("-")[1] + "/" + tgl.split("-")[0] + " " + waktu; }

      function format_date(date, isMonthName = false, separatorOld = "-", separatorNew = "/", withDay = true) { 
        if (date == "" || date == null) { return ""; }

        let monthNames = [
              "JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE",
              "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"
            ];

        date = date.substring(0, 10);
        let _day   = date.split(separatorOld)[2];
        let _month = (isMonthName) ? monthNames[date.split(separatorOld)[1] - 1] : date.split(separatorOld)[1];
        let _year  = date.split(separatorOld)[0];
        let _separator = (isMonthName) ? " " : separatorNew;

        return (withDay) ? _day + _separator + _month + _separator + _year : _month + _separator + _year
      }

      function format_number(n, _decimal = 0, _comma = true) {
        return (_comma) ? numberWithCommas(n.toFixed(_decimal)) : n.toFixed(_decimal); 
      }

      function messageRequired(_input_name) {
        return "Kolom "+ _input_name +" harus terisi.";
      }
      function messageHiddenRequired(_input_name) {
        return "Terjadi kesalahan. "+ _input_name +" tidak ditemukan. Silahkan refresh halaman.";
      }
      function messageNotZero(_input_name) {
        return "Kolom "+ _input_name +" tidak boleh nol.";
      }
      function messageMustNumber(_input_name) {
        return "Kolom "+ _input_name +" harus angka.";
      }
      function messageNotEmptyCart(_menu_name) {
        return "Detail "+ _menu_name +" tidak boleh kosong";
      }

      function nullToEmpty (_val) { return (_val == null) ? ""  : _val; }
      function nullToZero  (_val) { return (_val == null) ?  0  : _val; }
      function nullToStrip (_val) { return (_val == null) ? "-" : _val; }
      function emptyToZero (_val) { return (_val == "")   ? "0" : _val; }

      function cekRequiredNotEmpty(_id_name) {
        if ($("#" + _id_name).val() != "") {
          return true;
        } else {
          $("#" + _id_name).focus();
          return false;
        }
      }
      function cekRequiredNotZero(_id_name) {
        var _nominal = $("#" + _id_name).val();
        if (_nominal != "" && _nominal != "0.00" && _nominal != "0") {
          return true;
        } else {
          $("#" + _id_name).focus();
          return false;
        }
      }

      function setEmptyNumberToZero(_id_name) {
        return ($("#" + _id_name).val() != "") ? $("#" + _id_name).val().replace(/,/g, '') : "0";
      }

      function setFocus(_modal, _item) {
        $('#' + _modal).on('shown.bs.modal', function () {
          $('#' + _item).focus();
        })
      }

      function doSelectRow(_id_data, _row, _oldrow, _tr = "row", _bgcolor = "blue", _color = "white") {
        $('#'+_id_data+' > tr').each(function() {
          $(this).css('background-color', '');
          $(this).css('color', '');
        });

        if (_row != _oldrow) {
          $("#"+_row+"-tr"+_tr).css('background-color', _bgcolor);
          $("#"+_row+"-tr"+_tr).css('color', _color);

          return _row;
        } else { 
          return "";
        }
      }

      function trWithSelectRow(_id_kode_urut, _name = "row", _addition_str = "") {
        let id_kode = ($.isNumeric(_id_kode_urut)) ? _id_kode_urut : removeSpasi(_id_kode_urut);
        return '<tr id="' + id_kode + '-tr'+_name+'" onclick="select'+_name+'(\'' + id_kode + '\', '+_addition_str+')">';
      }
      


      /* ============== START MAIN REPORT ============== */

      function doMakeTable() {
        makeTable();
      }

      function godown() {
        document.getElementById("showTableReport").scrollIntoView({ behavior : "smooth" });
      }

      /* ============== END OF MAIN REPORT ============== */





      /* ============== START CUSTOMIZE HEADER ============== */

      function doLoadHeader(_href, _mode) {
        let _header = "";

        $.ajax({
          url     : "{!! url('globalfunctions_doLoadHeader') !!}",
          type    : "get",
          async   : false,
          data    : {
            href : _href,
            mode : _mode
          },
          success: function(res) {
            _header = (res.length > 0) ? res[0].header : "";
          }
        })

        return _header;
      }

      function doGetHeader(_strHeader) {
        let _cart = [];

        _strHeader.split("||").forEach((item, i) => {
          let temp = [];
          temp.push(item.split(";;")[0]);
          temp.push(item.split(";;")[1]);
          temp.push(toInteger(item.split(";;")[2]));
          _cart.push(temp);
        });

        return _cart;
      }

      function doSimpanHeader(_href, _mode, _cart) {
        let _strHeader = "";

        _cart.forEach((item, i) => {
          if (i != 0) { _strHeader += '||'; }
          _strHeader += item[0] + ';;' + item[1] + ';;' + item[2];
        });

        $.ajax({
          url     : "{!! url('globalfunctions_doSimpanHeader') !!}",
          type    : "get",
          async   : false,
          data    : {
            href : _href,
            mode : _mode,
            header : _strHeader
          },
          success: function(res) {
            //
          }
        })
      }

      function showFormCustomizeTable() {
        g_modeModal = g_modal_customizetable;
        showCustomize();
        $("#formCustomizeTable").modal('toggle');
      }

      function closeFormCustomizeTable() {
        g_modeModal = "";
        $("#formCustomizeTable").modal('toggle');
      }

      function doResetHeader() {
        alertify.confirm('Reset Customize Table', 'Apakah yakin ingin mengembalikan tabel ke pengaturan awal?',
        function() {
            resetHeader();
          }, function(){}
        );
      }

      /* ============== END OF CUSTOMIZE HEADER ============== */





      /* ============== START FILTER DATA ============== */

      function showFormFilterData() {
        g_modeModal = g_modal_filterdata;
        showFilter();
        $("#formFilterData").modal('toggle');
      }

      function closeFormFilterData() {
        g_modeModal = "";
        g_lastrowfilter = -1;
        $("#formFilterData").modal('toggle');
      }

      function doShowReportFilter() {
        showReportFilter();
      }

      /* ============== END FILTER DATA ============== */



      function exportTableToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        // Specify file name
        filename = filename?filename+'.xls': '{!! $akses['xlsfilename'] !!}.xls';

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

    </script>
    @yield('js')
  </body>
</html>
