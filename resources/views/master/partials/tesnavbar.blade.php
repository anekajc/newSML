
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
    <link rel="stylesheet" href="{!! URL::asset('css/datatables.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('css/jquery-ui.min.css') !!}">
    <title>AnekaJC</title>
    <!-- jQuery -->
    <link rel="stylesheet" href="{!! URL::asset('css/alertify.css') !!}">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<!-- Bootstrap files (jQuery first, then Popper.js, then Bootstrap JS) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" type="text/css"/> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" type="text/javascript"></script>


  <script type="text/javascript">
    /// some script

    // jquery ready start
    $(document).ready(function() {
      // jQuery code

      //////////////////////// Prevent closing from click inside dropdown
        $(document).on('click', '.dropdown-menu', function (e) {
          e.stopPropagation();
        });

        // make it as accordion for smaller screens
        if ($(window).width() < 992) {
          $('.dropdown-menu a').click(function(e){
            e.preventDefault();
              if($(this).next('.submenu').length){
                $(this).next('.submenu').toggle();
              }
              $('.dropdown').on('hide.bs.dropdown', function () {
             $(this).find('.submenu').hide();
          })
          });
      }

    }); // jquery end
  </script>

  <style type="text/css">
  /* html {
    overflow-y: scroll;
  } */
    @media (min-width: 992px){
      .dropdown-menu .dropdown-toggle:after{
        border-top: .3em solid transparent;
          border-right: 0;
          border-bottom: .3em solid transparent;
          border-left: .3em solid;
      }

      .dropdown-menu .dropdown-menu{
        margin-left:0; margin-right: 0;
      }

      .dropdown-menu li{
        position: relative;
      }
      .nav-item .submenu{
        display: none;
        position: absolute;
        left:100%; top:-7px;
      }
      .nav-item .submenu-left{
        right:100%; left:auto;
      }

      .dropdown-menu > li:hover{ background-color: #f1f1f1 }
      .dropdown-menu > li:hover > .submenu{
        display: block;
      }
    }
    .topnav {
      background-color: #333;
      overflow: hidden;
  }

  /* Style the links inside the navigation bar */
  .topnav a {
    float: left;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    font-size: 17px;
  }

  /* Change the color of links on hover */
  .topnav a:hover {
    background-color: #ddd;
    color: black;
  }

  /* Add a color to the active/current link */
  .topnav a.active {
    background-color: #04AA6D;
    color: white;
  }

  /* Right-aligned section inside the top navigation */
  .topnav-right {
    float: right;
  }
  </style>
</head>


<body style="background-color: #d9c4a9">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <!-- <div class="container"> -->
    <a class="navbar-brand" href="#">AnekaJC</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav" >
          <span class="navbar-toggler-icon" ></span>
      </button>



      <div class="collapse navbar-collapse" id="main_nav">

        <ul class="navbar-nav">
            <!-- <li class="nav-item"> <a class="nav-link" href="#"> TesTes </a> </li>
              <li class="nav-item"> <a class="nav-link" href="#"> TesTes </a> </li>
                <li class="nav-item"> <a class="nav-link" href="#"> TesTes </a> </li> -->
          @foreach ($menul0 as $menu)
          @if (count($menu['child']) == 0 )
          <li class="nav-item"> <a class="nav-link" href="{{url($menu->href)}}"> {{ $menu['Keterangan'] }} </a> </li>
          @else

          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"> {{ $menu['Keterangan'] }} </a>
              <ul class="dropdown-menu">
                @foreach ($menu['child'] as $menu1)
                @if (count($menu1['child']) == 0)
            	  <li><a class="dropdown-item" href="{{url($menu1->href)}}"> {{ $menu1['Keterangan'] }} </a></li>
                @else
            	  <li><a class="dropdown-item" href="#">{{ $menu1['Keterangan'] }} ></a>

            		 <ul class="submenu dropdown-menu">
                   @foreach ($menu1['child'] as $menu2)
            		    <li><a class="dropdown-item" href="{{url($menu2->href)}}">{{ $menu2['Keterangan'] }}</a></li>
                    <!-- <li><a class="dropdown-item" href=""> Third level 1</a></li>
              		    <li><a class="dropdown-item" href=""> Third level 2</a></li>
              		    <li><a class="dropdown-item" href=""> Third level 3 &raquo </a>
              			<ul class="submenu dropdown-menu">
              			    <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
              			    <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                        <li><a class="dropdown-item" href=""> Third level 3 &raquo </a>
                     <ul class="submenu dropdown-menu">
                         <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
                         <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                         <li><a class="dropdown-item" href=""> Third level 3 &raquo </a>
                       <ul class="submenu dropdown-menu">
                           <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
                           <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                           <ul class="submenu dropdown-menu">
                               <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
                               <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                               <ul class="submenu dropdown-menu">
                                   <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
                                   <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                                   <ul class="submenu dropdown-menu">
                                       <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
                                       <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
                                   </ul>

                               </ul>
                           </ul>
                       </ul>
                         </li>
                     </ul>
                       </li>
              			</ul>
              		    </li> -->
                  @endforeach
        		    </li>

          		 </ul>
               @endif
               @endforeach

              </ul>
          </li>
          @endif
          @endforeach
        </ul>


      </div>

  <!-- </div> -->

  </nav>

  <nav class="navbar navbar-expand-lg navbar-dark " style="height: 36px; background-color: grey;">
<!-- <div class="container"> -->
 <ul class="navbar-nav mr-auto">
   <li class="nav-item">
     <a class="nav-link">Periode</a>
   </li>
 </ul><div class="d-flex align-items-center">
    <ul class="navbar-nav ml-auto">
      @yield('buttons')
      <li class="nav-item">
      <a href="{!! url('logout') !!}" style="color: black;">Logout</a>
      </li>
    </ul>
  </div>
  </nav>



@yield('content')


@yield('js')
    <script type="text/javascript">
    function tesonclick() {
      console.log('asd')
    }</script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{!! URL::asset('js/jquery.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('js/alertify.min.js') !!}"></script>
    <script src="{!! URL::asset('js/autoNumeric.js') !!}"></script>
    <script src="{!! URL::asset('js/datatables.min.js') !!}"></script>
    <script src="{!! URL::asset('js/jquery-ui.min.js') !!}"></script>


  </body>
</html>
