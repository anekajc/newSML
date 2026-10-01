<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="author" content="SemiColonWeb" />

  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet" />

  <title>AnekaJC - Login</title>

    <link rel="stylesheet" href="{!! URL::asset('css/loginpage.css') !!}">

</head>

<body class='background-login'>
  <div class="login-wrapper">

    <!-- Left: Branding Panel -->
    <div class="left-panel">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24">
          <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
      </div>
      <p class="brand-name">SPL</p>
      <p class="brand-subtitle">ERP</p>
      {{-- <div class="feature-list">
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Secure access
        </div>
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Real-time reports
        </div>
        <div class="feature-item">
          <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Multi-user support
        </div>
      </div> --}}
    </div>

    <!-- Right: Login Form -->
    <div class="right-panel">
      <p class="form-title">Login</p>
      <p class="form-subtitle">Masukan Username dan Password</p>

      <form action="{{ url('checkLogin') }}" method="post">
        {{-- onsubmit="return checkOnline()" --}}

        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="username" name="username" placeholder="User" required autofocus autocomplete="off" />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="password" name="password" placeholder="Password" />
          </div>
        </div>

        <button type="submit" class="btn-login" id="login-form-submit">Sign in</button>
      </form>

      <p class="form-footer">AnekaJC &copy; 2025 &mdash; All rights reserved</p>
    </div>

  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

  <script type="text/javascript">

    $(document).ready(function(){
      @if($errors->any())
        if(JSON.parse({!! $errors !!}) == "1") {
          alertify.alert('Login gagal!', 'Username dan Password tidak cocok.', function(){ });
        }
      @endif

      $.ajax({
        url     : "{{ url('updateIdle') }}",
        type    : "GET",
        async   : false
      });
    });

    function checkOnline() {
      var _token    = $("#_token").val();
      var _username = $("#username").val();
      var check     = 0;
      $.ajax({
        url     : "{{ url('checkOnline') }}",
        type    : "POST",
        async   : false,
        data    : { _token: _token, username: _username },
        success : function(result) { check = result; }
      });
      if (check == 0) { return true; }
      else { alertify.alert('Login gagal!', 'User sudah login.', function(){ }); return false; }
    }

  </script>


</body>
</html>
