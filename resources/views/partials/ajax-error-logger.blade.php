{{-- Prints every failed jQuery AJAX call to the browser console. For server errors the
     JSON body comes from the render callback in bootstrap/app.php (message, exception,
     file, line) -- only sent to logged-in users. Include after jQuery is loaded and
     before @yield('js'). Pages' own error: callbacks still run as before. --}}
<script>
  if (window.jQuery) {
    jQuery(document).ajaxError(function (event, xhr, settings, thrownError) {
      if (thrownError === 'abort') { return; }

      var res = xhr.responseJSON;
      if (!res) {
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
      }

      var where = '[AJAX ' + xhr.status + '] ' + (settings.type || 'GET') + ' ' + settings.url;
      if (res && res.exception) {
        console.error(where + '\n' + res.exception + ': ' + res.message + '\n    at ' + res.file + ':' + res.line);
      } else {
        console.error(where + ' ' + ((res && res.message) || thrownError || 'Request failed'));
      }
    });
  }
</script>
