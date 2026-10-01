<!-- @section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Master Data / Master Bahan, Barang & Jasa / Barang Jadi"><span class="blue" id="title_page">Barang Jadi</span></a>
</li>
@endsection

@section('content')
<div class="container-fluid">
    <h1>tes</h1>
</div>
@endsection -->

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
    <title>Hello, world! {{ $title }} </title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
crossorigin="anonymous"></script>
<!-- Bootstrap files (jQuery first, then Popper.js, then Bootstrap JS) -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
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
    <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar1</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item {{ ($title) === "Home" ? 'active' : ""}}">
          <a class="nav-link" href="/solunadapurindo/tes">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ ($title) === "About" ? 'active' : ""}}" href="/solunadapurindo/tesAbout">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ ($title) === "Table" ? 'active' : ""}}" href="/solunadapurindo/tesTable">Table</a>
        </li>

        <!-- <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li> -->
      </ul>
    </div>
  </div>
  </nav>


  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
    <h3 style="margin-right:40px">Name</h3>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav" >
          <span class="navbar-toggler-icon"  >Tes</span>
      </button>



      <div class="collapse navbar-collapse" id="main_nav">

        <ul class="navbar-nav">
        <li class="nav-item"> <a class="nav-link" href="#"> First level 1 </a> </li>
        <li class="nav-item"> <a class="nav-link" href="#"> First level 2 </a></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">  First level 3  </a>
            <ul class="dropdown-menu">
        	  <li><a class="dropdown-item" href="#"> Second level 1 </a></li>
        	  <li><a class="dropdown-item" href="#"> Second level 2 &raquo </a>
        		 <ul class="submenu dropdown-menu">
        		    <li><a class="dropdown-item" href=""> Third level 1</a></li>
        		    <li><a class="dropdown-item" href=""> Third level 2</a></li>
        		    <li><a class="dropdown-item" href=""> Third level 3 &raquo </a>
        			<ul class="submenu dropdown-menu">
        			    <li><a class="dropdown-item" href=""> Fourth level 1</a></li>
        			    <li><a class="dropdown-item" href=""> Fourth level 2</a></li>
        			</ul>
        		    </li>
        		    <li><a class="dropdown-item" href=""> Second level  4</a></li>
        		    <li><a class="dropdown-item" href=""> Second level  5</a></li>
        		 </ul>
        	  </li>
        	  <li><a class="dropdown-item" href="#"> Dropdown item 3 </a></li>
        	  <li><a class="dropdown-item" href="#"> Dropdown item 4 </a>
            </ul>
        </li>
        <li class="nav-item"> <a class="nav-link" href="#"> First level 1 </a> </li>
        <li class="nav-item"> <a class="nav-link" href="#"> First level 2 </a></li>
        </ul>


      </div> <!-- navbar-collapse.// -->
      <div class="topnav-right" >
          <button type="button" class="btn btn-secondary">Tes</button>
              <button type="button" class="btn btn-secondary">Tes</button>
          <a href="#about" style="color:red">Logout</a>
        </div>
</div>
  </nav>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-top:20px; ">
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
          <li class="nav-item"> <a class="nav-link" href="#"> {{ $menu['Keterangan'] }} </a> </li>
          @else

          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"> {{ $menu['Keterangan'] }} </a>
              <ul class="dropdown-menu">
                @foreach ($menu['child'] as $menu1)
                @if (count($menu1['child']) == 0)
            	  <li><a class="dropdown-item" href="#"> {{ $menu1['Keterangan'] }} </a></li>
                @else
            	  <li><a class="dropdown-item" href="#">{{ $menu1['Keterangan'] }} ></a>

            		 <ul class="submenu dropdown-menu">
                   @foreach ($menu1['child'] as $menu2)
            		    <li><a class="dropdown-item" href="">{{ $menu2['Keterangan'] }}</a></li>
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
 </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn" style="background-color: red; height: 25px; margin-top: 2px; margin-right: 3px;" href="#!"><i
    class="fab fa-slack-hash"></i></a>
      </li>
      <li class="nav-item">
        <a class="btn" style="background-color: yellow; height: 25px; margin-top: 2px; margin-right: 3px;" href="#!" role="button"><i
    class="fab fa-slack-hash"></i></a>
      </li>
      <li class="nav-item">
        <a class="btn" style="background-color: green; height: 25px; margin-top: 2px; margin-right: 3px;" href="#!" role="button"><i
    class="fab fa-slack-hash"></i></a>
      </li>
    </li>
    <li class="nav-item">
      <a class="btn" style="background-color: blue; height: 25px; margin-top: 2px; margin-right: 3px;" href="#!" role="button"><i
  class="fab fa-slack-hash"></i></a>
    </li>
    <li class="nav-item">
      <a href="#" style="color: black;">Logout</a>
    </li>
    </ul>
  <!-- </div> -->
  </nav>


    <!-- <h5>{{ $menul0 }}</h5> -->
@yield('content')
  <!-- <h5> {{ $menul0 }} </h5>
    @yield('asdasd')

    </div> -->
    <script type="text/javascript">
    function tesonclick() {
      console.log('asd')
    }</script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
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
    @yield('js')

  </body>
</html>
