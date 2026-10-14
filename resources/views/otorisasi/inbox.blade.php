@extends('newmasterTest')
@section('page-title', 'Permintaan Otorisasi')

{{-- Kotak masuk permintaan otorisasi (OtorisasiRequestController::index/loadAll).
     Status perangkat & tombol aktifkan notifikasi memakai window.OtoPush dari
     public/js/otorisasi-push.js (dimuat layout). --}}
@section('css')
<style>
#content { padding-top: 12px; }
.oto-wrap { max-width: 900px; }
.oto-card {
  background: #fff;
  border: 1px solid var(--border, #e5e7eb);
  border-radius: 10px;
  padding: 14px 16px;
  margin-bottom: 12px;
}
.oto-device { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.oto-device-text { flex: 1; min-width: 200px; font-size: 13px; color: #374151; }
.oto-device-text b { display: block; font-size: 14px; color: #111827; margin-bottom: 2px; }
.oto-btn {
  border: 1px solid #bfdbfe; background: #eff6ff; color: #1d4ed8;
  border-radius: 8px; padding: 6px 14px; font-size: 13px; font-weight: 600; cursor: pointer;
}
.oto-btn.secondary { border-color: #e5e7eb; background: #fff; color: #4b5563; }
.oto-tabs { display: flex; gap: 6px; margin-bottom: 10px; }
.oto-tab {
  border: 1px solid #e5e7eb; background: #fff; color: #4b5563; border-radius: 20px;
  padding: 5px 14px; font-size: 13px; font-weight: 600; cursor: pointer;
}
.oto-tab.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.oto-tab .n { margin-left: 4px; opacity: .8; }
.oto-item {
  display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;
  background: #fff; border: 1px solid var(--border, #e5e7eb); border-radius: 10px;
  padding: 11px 14px; margin-bottom: 8px;
}
a.oto-item:hover { border-color: #bfdbfe; color: inherit; text-decoration: none; }
.oto-item.unread { border-left: 4px solid #2563eb; }
.oto-item-main { flex: 1; min-width: 0; }
.oto-item-title { font-weight: 600; font-size: 14px; color: #111827; }
.oto-item-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }
.oto-item-note { font-size: 12px; color: #b91c1c; margin-top: 3px; }
.oto-status { font-size: 11px; font-weight: 700; border-radius: 12px; padding: 3px 9px; white-space: nowrap; }
.oto-status.P { background: #fef3e0; color: #b45309; }
.oto-status.A { background: #e7f7ed; color: #16a34a; }
.oto-status.R { background: #fdeaea; color: #dc2626; }
.oto-status.C { background: #f3f4f6; color: #6b7280; }
.oto-empty { text-align: center; color: #9ca3af; font-size: 13px; padding: 28px 0; }
</style>
@endsection

@section('content')
<div class="oto-wrap">
  <div class="oto-card oto-device">
    <i class="bi bi-phone" style="font-size:22px;color:#2563eb"></i>
    <div class="oto-device-text" id="otoDeviceText"><b>Notifikasi perangkat ini</b>Memeriksa…</div>
    <button type="button" class="oto-btn" id="otoDeviceBtn" style="display:none"></button>
  </div>

  <div class="oto-tabs">
    <button type="button" class="oto-tab" data-tab="pending">Menunggu saya<span class="n" id="otoNPending"></span></button>
    <button type="button" class="oto-tab" data-tab="saya">Permintaan saya<span class="n" id="otoNMine"></span></button>
  </div>

  <div id="otoListPending"></div>
  <div id="otoListMine" style="display:none"></div>
</div>
@endsection

@section('js')
<script>
(function () {
  var URLS = {
    loadAll: "{!! url('otorisasiinboxloadall') !!}",
    show: "{!! url('otorisasi') !!}/",
  };
  var STATUS = { P: 'Menunggu', A: 'Disetujui', R: 'Ditolak', C: 'Dibatalkan' };

  function esc(s) {
    return $('<div>').text(s == null ? '' : String(s)).html();
  }

  function itemHtml(r, mine) {
    var sub = mine
      ? 'Diminta ' + esc(r.tglreq) + (r.actuser ? ' · ' + (r.status === 'R' ? 'ditolak' : 'diproses') + ' oleh ' + esc(r.actuser) + ' ' + esc(r.tglact) : '')
      : 'Dari ' + esc(r.requser) + ' · ' + esc(r.tglreq);
    var note = r.status === 'R' && r.catatan ? '<div class="oto-item-note">Alasan: ' + esc(r.catatan) + '</div>' : '';
    return '<a class="oto-item' + (mine && r.unread ? ' unread' : '') + '" href="' + URLS.show + r.id + '">' +
      '<div class="oto-item-main"><div class="oto-item-title">' + esc(r.menu) + ' ' + esc(r.nobukti) + '</div>' +
      '<div class="oto-item-sub">' + sub + '</div>' + note + '</div>' +
      '<span class="oto-status ' + r.status + '">' + STATUS[r.status] + '</span></a>';
  }

  function render(list, el, mine, empty) {
    $(el).html(list.length ? list.map(function (r) { return itemHtml(r, mine); }).join('') : '<div class="oto-empty">' + empty + '</div>');
  }

  function load() {
    $.get(URLS.loadAll, function (res) {
      render(res.pending, '#otoListPending', false, 'Tidak ada permintaan yang menunggu otorisasi Anda');
      render(res.mine, '#otoListMine', true, 'Anda belum pernah meminta otorisasi');
      $('#otoNPending').text(res.pending.length ? '(' + res.pending.length + ')' : '');
      $('#otoNMine').text('');
      if (window.OtoPush) OtoPush.refreshCount();
    });
  }

  function showTab(tab) {
    $('.oto-tab').removeClass('active').filter('[data-tab="' + tab + '"]').addClass('active');
    $('#otoListPending').toggle(tab === 'pending');
    $('#otoListMine').toggle(tab === 'saya');
  }

  $('.oto-tab').on('click', function () { showTab($(this).data('tab')); });
  showTab(new URLSearchParams(location.search).get('tab') === 'saya' ? 'saya' : 'pending');
  load();

  // Status notifikasi perangkat ini
  var DEVICE = {
    on: ['Notifikasi aktif', 'Perangkat ini akan menerima notifikasi permintaan otorisasi.', 'Nonaktifkan'],
    off: ['Notifikasi belum aktif', 'Aktifkan supaya perangkat ini menerima notifikasi permintaan otorisasi.', 'Aktifkan Notifikasi'],
    denied: ['Notifikasi diblokir', 'Izinkan notifikasi untuk situs ini di pengaturan browser, lalu muat ulang halaman.', null],
    'ios-need-install': ['Pasang aplikasi dulu (iPhone/iPad)', 'Tekan tombol Share lalu "Add to Home Screen", buka AJC dari ikon di layar utama, kemudian aktifkan notifikasi di halaman ini.', null],
    unsupported: ['Tidak didukung', 'Browser ini tidak mendukung notifikasi push. Gunakan Chrome di Android/komputer, atau Safari (Add to Home Screen) di iPhone.', null],
  };

  function showDevice(st) {
    var d = DEVICE[st] || DEVICE.unsupported;
    $('#otoDeviceText').html('<b>' + d[0] + '</b>' + d[1]);
    $('#otoDeviceBtn').toggle(!!d[2]).text(d[2] || '').toggleClass('secondary', st === 'on').data('st', st);
  }

  document.addEventListener('otopush:status', function (e) { showDevice(e.detail); });

  $('#otoDeviceBtn').on('click', function () {
    var on = $(this).data('st') === 'on';
    (on ? OtoPush.unsubscribe() : OtoPush.subscribe())
      .then(function () { alertify.success(on ? 'Notifikasi dinonaktifkan' : 'Notifikasi aktif di perangkat ini'); })
      .catch(function (e) { alertify.warning(e.message); });
  });
})();
</script>
@endsection
