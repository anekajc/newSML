{{-- Lapisan "Memuat data..." untuk tabel daftar (#page1) di semua tab - lihat public/js/tabel-memuat.js.
     Di-include di awal @section('js') tiap menu Purchasing & Marketing. --}}
<script src="{!! URL::asset('js/tabel-memuat.js') !!}?v={{ @filemtime(base_path('public/js/tabel-memuat.js')) ?: '1' }}"></script>
