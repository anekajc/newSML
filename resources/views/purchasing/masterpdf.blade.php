<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/bootstrap.min.css') !!}">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/font-awesome.css') !!}">
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/alertify.css') !!}">
    <!-- Semantic CSS -->
    <link rel="stylesheet" href="{!! URL::asset('public/css/semantic.css') !!}">

    <link rel="stylesheet" href="{!! URL::asset('public/css/select2.min.css') !!}">

    <title>Project</title>
    <style type="text/css">
      table {
        font-size: 12px;
      }

      .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 3px;
      }

      label {
        font-size: 0.8rem !important;
      }

      .form-group {
        padding: 0px !important;
        margin-bottom: 0.5rem;
      }
    </style>
    @yield('css')

  </head>
  <body>
    @yield('content')
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{!! URL::asset('public/js/jquery-3.3.1.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/select2.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/popper.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/bootstrap.min.js') !!}"></script>
    <script src="{!! URL::asset('public/js/alertify.js') !!}"></script>
    <script src="{!! URL::asset('public/js/autoNumeric.js') !!}"></script>
    @yield('js')
  </body>
</html>
