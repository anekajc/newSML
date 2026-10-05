<!DOCTYPE html>
<html dir="ltr" lang="en-US">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="SemiColonWeb" />

    <!-- Stylesheets
	============================================= -->
    <!-- <link
      href="https://fonts.googleapis.com/css?family=Lato:300,400,400i,700|Poppins:300,400,500,600,700|PT+Serif:400,400i&display=swap"
      rel="stylesheet"
      type="text/css"
    />

    <link rel="stylesheet" href="{!! URL::asset('public/css/jquery-ui.min.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/datatables.min.css') !!}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">

    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/bootstrap.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/style.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/dark.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/font-icons.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/animate.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/magnific-popup.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('public/css/canvas/custom.css') !!}">
    <link rel="stylesheet" href="{{ URL::asset('public/css/alertify.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css"> -->


    <!-- <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="style.css" type="text/css" />
    <link rel="stylesheet" href="css/dark.css" type="text/css" />
    <link rel="stylesheet" href="css/font-icons.css" type="text/css" />
    <link rel="stylesheet" href="css/animate.css" type="text/css" />
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css" />

    <link rel="stylesheet" href="css/custom.css" type="text/css" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Document Title
	============================================= -->
    <title>Pie Charts &amp; Skills | Canvas</title>
  </head>

  <body class="stretched">
    <!-- Document Wrapper
	============================================= -->
    <div id="wrapper" class="clearfix">
      <!-- Header
		============================================= -->
      <header id="header" class="full-header">
        <div id="header-wrap">
          <div class="container">
            <div class="header-row">
              <!-- Logo
						============================================= -->
              <div id="logo">
                <a
                  href="index.html"
                  class="standard-logo"
                  data-dark-logo="images/logo-dark.png"
                  >AnekaJC</a
                >
                <a
                  href="index.html"
                  class="retina-logo"
                  data-dark-logo="images/logo-dark@2x.png"
                  >AnekaJC</a
                >
              </div>
              <!-- #logo end -->

              <div class="header-misc">
                <!-- Top Search
							============================================= -->
              <div id="top-search" class="header-misc-icon">
                  <ul class="menu-container" style="justify-content: left !important; align-items: left !important;">
                    <a class="menu-link" href="{!! url('logout') !!}"><div>Logout</div></a>


                  <ul>
              </div>

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
              <nav class="primary-menu">
                <ul class="menu-container" style="justify-content: left !important; align-items: left !important;">
                  <li class="menu-item">
                    <a class="menu-link" href="index.html"><div style="color: red;">Home</div></a>
                    <ul class="sub-menu-container">
                      <li class="menu-item">
                        <a class="menu-link" href="intro.html#section-niche"
                          ><div>Niche Demos</div></a
                        >
                      </li>


                      <li class="menu-item">
                        <a class="menu-link" href="index-onepage.html"
                          ><div>Home - One Page</div></a
                        >
                        <ul class="sub-menu-container">
                          <li class="menu-item">
                            <a class="menu-link" href="index-onepage.html"
                              ><div>One Page - Default</div></a
                            >
                          </li>
                          <li class="menu-item">
                            <a class="menu-link" href="index-onepage-2.html"
                              ><div>One Page - Submenu</div></a
                            >
                          </li>
                          <li class="menu-item">
                            <a class="menu-link" href="index-onepage-3.html"
                              ><div>One Page - Dots Style</div></a
                            >
                          </li>
                        </ul>
                      </li>
                      <li class="menu-item mega-menu mega-menu-small">
                        <a class="menu-link" href="#"><div>Extras</div></a>
                        <div class="mega-menu-content">
                          <div class="row mx-0">
                            <ul class="sub-menu-container mega-menu-column col">
                              <li class="menu-item">
                                <a class="menu-link" href="index-wedding.html"
                                  ><div>Wedding</div></a
                                >
                              </li>
                              <li class="menu-item">
                                <a
                                  class="menu-link"
                                  href="index-restaurant.html"
                                  ><div>Restaurant</div></a
                                >
                              </li>
                              <li class="menu-item">
                                <a class="menu-link" href="index-events.html"
                                  ><div>Events</div></a
                                >
                              </li>
                            </ul>
                            <ul class="sub-menu-container mega-menu-column col">
                              <li class="menu-item">
                                <a class="menu-link" href="index-parallax.html"
                                  ><div>Parallax</div></a
                                >
                              </li>
                              <li class="menu-item">
                                <a
                                  class="menu-link"
                                  href="index-app-showcase.html"
                                  ><div>App Showcase</div></a
                                >
                              </li>
                              <li class="menu-item">
                                <a class="menu-link" href="index-boxed.html"
                                  ><div>Boxed Layout</div></a
                                >
                              </li>
                            </ul>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>
              </nav>
              <!-- #primary-menu end -->


            </div>
          </div>
        </div>
      </header>
      <header id="header" class="full-header" style="z-index: 1">
        <div id="header-wrap">
          <!-- <div class="container"> -->
            <!-- <div class="header-row"> -->

              <nav class="navbar navbar-expand-lg navbar-dark " style="height: 39px; background-color: #5A5A5A;">
              <!-- <div class="container"> -->
                 <ul class="navbar-nav mr-auto">
                     <div style="color: white">
                       Periode
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
      <section id="content">
        <div class="content-wrap">
          <div class="container clearfix">
            <div class="row gutter-40 col-mb-80">
              <!-- Post Content
						============================================= -->
            <!-- <div class="line"></div> -->
              @yield('content')

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
      <footer id="footer" class="dark">
        <div class="container">
          <!-- Footer Widgets
				============================================= -->

          <!-- .footer-widgets-wrap end -->
        </div>

        <!-- Copyrights
			============================================= -->
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
        <!-- #copyrights end -->
      </footer>
      <!-- #footer end -->
    </div>
    <!-- #wrapper end -->

    <!-- Go To Top
	============================================= -->
    <div id="gotoTop" class="icon-angle-up"></div>

    <!-- External JavaScripts
	============================================= -->
  <script src="{!! URL::asset('public/js/canvas/jquery.js') !!}"></script>
  <script src="{!! URL::asset('public/js/jquery.min.js') !!}"></script>
  <script src="{!! URL::asset('public/js/jquery-3.3.1.min.js') !!}"></script>
  <script src="{!! URL::asset('public/js/datatables.min.js') !!}"></script>
  <script src="{!! URL::asset('public/js/jquery-ui.min.js') !!}"></script>
  <script src="{{ URL::asset('public/js/alertify.js') }}"></script>
    <!-- <script src="js/jquery.js"></script> -->

    <!-- Footer Scripts
	============================================= -->

  <script src="{!! URL::asset('public/js/canvas/functions.js') !!}"></script>
    <!-- <script src="js/functions.js"></script> -->
  </body>
</html>
