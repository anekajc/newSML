@extends('master')

@section('breadcrumb')
<li class="nav-item">
  <a class="nav-link" href="#" data-toggle="tooltip" data-placement="bottom" title="Search"><span class="blue" id="title_page">Search</span></a>
</li>
@endsection

@section('content')
<input type="text" class="form-control" id="search" style="width: 300px">
@endsection

@section('js')
<script type="text/javascript">
	$(document).ready(function(){
	});

  $('#search').autocomplete({
    delay: 500,
    minLength: 2,
    source: function(request, response) {
      $.getJSON("{!! url('searchBarang') !!}", {
          term: request.term,
      }, function(data) {
          response(data);
      });
    },
    search: function (event, ui) {
      var key = CheckBrowser(event);
      if (key == 13)
          return true;
      else
          return false;
      }
    },
    focus: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
    },
    select: function(event, ui) {
      // prevent autocomplete from updating the textbox
      event.preventDefault();
      $('#search').val(ui.item.kode + " - " + ui.item.nama);
      // $('input[name="user_id"]').val(ui.item.id);
    }
  });
  $("#search").autocomplete( "option", "appendTo", ".eventInsForm" );
  // var path = "{{ url('searchBarang') }}";
  //   $('#search').typeahead({
  //       source:  function (query, process) {
  //       return $.get(path, { query: query }, function (data) {
  //               return process(data);
  //           });
  //       }
  //   });
  // $.ajax({
  //   url     : "{!! url('searchBarang') !!}",
  //   type    : "GET",
  //   async   : false,
  //   data    : {
  //     term : "101"
  //   },
  //   success : function(result) {
  //     alert(JSON.stringify(result));
  //   }
  // });
</script>
@endsection
