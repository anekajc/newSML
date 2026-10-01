

<table id="tabel" class="table table-bordered table-striped"  >
  <thead class="text-center">
    <tr>
      @foreach($headers as $header)
      <th scope="col">{{$header}}</th>
      @endforeach
    </tr>
  </thead>
  <tbody id="tabel_data" class="text-right" >
    @for ($i = 0; $i < count($data); $i++)
    <!-- <h5>{{$data[$i]}}</h5> -->
    <tr id="tr{!! $i !!}" onclick="tesclick( {{ $i }} , {{ $data[$i]  }} )">
      @foreach($fields as $field)
        <td>{{ $data[$i]->$field}}</td>
      @endforeach
    </tr>
    @endfor
    <!-- @foreach($data as $d)
    <h5>{{$d}}</h5>
    <tr onclick="tesclick( {{ $d  }} )">
      @foreach($fields as $field)
        <td>{{ $d->$field}}</td>
      @endforeach
    </tr>
    @endforeach -->

  </tbody>
</table>
