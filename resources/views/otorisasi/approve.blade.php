<!doctype html>
<html lang="id">
{{-- Halaman approve mobile - dibuka dari notifikasi push (OtorisasiRequestController::show).
     Sengaja tidak memakai layout newmasterTest (sidebar/tabel desktop); berdiri sendiri,
     tanpa jQuery. Approve/Reject lewat POST /otorisasi/{id}/approve|reject. --}}
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="theme-color" content="#2563eb" />
  <link rel="manifest" href="{!! URL::asset('manifest.webmanifest') !!}" />
  <link rel="apple-touch-icon" href="{!! URL::asset('img/pwa-192.png') !!}" />
  <title>Otorisasi {{ $label }} {{ $request->NOBUKTI }}</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0; background: #f3f4f6; color: #1f2937;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      font-size: 14px; padding-bottom: calc(96px + env(safe-area-inset-bottom));
    }
    header {
      background: #2563eb; color: #fff; padding: 14px 16px;
      padding-top: calc(14px + env(safe-area-inset-top));
      display: flex; align-items: center; gap: 10px;
    }
    header a { color: #fff; text-decoration: none; font-size: 20px; line-height: 1; }
    header h1 { margin: 0; font-size: 16px; font-weight: 600; }
    header small { display: block; opacity: .85; font-size: 12px; font-weight: 400; }
    main { padding: 12px; max-width: 640px; margin: 0 auto; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px; margin-bottom: 12px; }
    .card h2 { margin: 0 0 10px; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
    .kv { display: flex; justify-content: space-between; gap: 12px; padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
    .kv:last-child { border-bottom: none; }
    .kv span:first-child { color: #6b7280; white-space: nowrap; }
    .kv span:last-child { text-align: right; font-weight: 500; word-break: break-word; }
    .line { padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
    .line:last-child { border-bottom: none; }
    .line .amt { font-weight: 700; font-size: 15px; }
    .line .muted { color: #6b7280; font-size: 12.5px; margin-top: 2px; }
    .total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; padding-top: 10px; border-top: 2px solid #e5e7eb; margin-top: 4px; }
    .banner { border-radius: 12px; padding: 12px 14px; margin-bottom: 12px; font-weight: 600; }
    .banner small { display: block; font-weight: 400; margin-top: 3px; }
    .banner.P { background: #fef3e0; color: #92400e; }
    .banner.A { background: #e7f7ed; color: #166534; }
    .banner.R { background: #fdeaea; color: #991b1b; }
    .banner.C { background: #f3f4f6; color: #4b5563; }
    .actions {
      position: fixed; left: 0; right: 0; bottom: 0; background: #fff; border-top: 1px solid #e5e7eb;
      padding: 12px; padding-bottom: calc(12px + env(safe-area-inset-bottom)); display: flex; gap: 10px;
    }
    .actions button { flex: 1; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; }
    .btn-approve { background: #16a34a; color: #fff; }
    .btn-reject { background: #fff; color: #dc2626; border: 1.5px solid #fca5a5 !important; }
    button:disabled { opacity: .5; }
    #rejectBox { display: none; }
    textarea { width: 100%; min-height: 80px; border: 1px solid #d1d5db; border-radius: 10px; padding: 10px; font: inherit; }
    #msg { display: none; }
  </style>
</head>
<body>
  @php
    $statusText = ['P' => 'Menunggu otorisasi', 'A' => 'Disetujui', 'R' => 'Ditolak', 'C' => 'Dibatalkan'];
    $pending = $request->STATUS === 'P';
  @endphp

  <header>
    <a href="{{ url('otorisasiinbox') }}" aria-label="Kembali">&#8592;</a>
    <h1>Otorisasi {{ $label }}<small>{{ $request->NOBUKTI }}</small></h1>
  </header>

  <main>
    <div class="banner {{ $request->STATUS }}" id="statusBanner">
      {{ $statusText[$request->STATUS] ?? $request->STATUS }}
      <small>
        Diminta oleh {{ $request->REQUSER }} · {{ date('d-m-Y H:i', strtotime($request->TGLREQ)) }}
        @if (! $pending && $request->ACTUSER)
          <br>{{ $request->STATUS === 'R' ? 'Ditolak' : 'Diproses' }} oleh {{ $request->ACTUSER }}
          · {{ date('d-m-Y H:i', strtotime($request->TGLACT)) }}
        @endif
        @if ($request->CATATAN)
          <br>Catatan: {{ $request->CATATAN }}
        @endif
      </small>
    </div>

    <div class="banner" id="msg"></div>

    @if (! $summary)
      <div class="card">Data transaksi {{ $request->NOBUKTI }} tidak ditemukan.</div>
    @else
      <div class="card">
        <h2>Transaksi</h2>
        @foreach ($summary['header'] as $k => $v)
          <div class="kv"><span>{{ $k }}</span><span>{{ $v }}</span></div>
        @endforeach
      </div>

      <div class="card">
        <h2>Rincian ({{ count($summary['lines']) }})</h2>
        @foreach ($summary['lines'] as $line)
          <div class="line">
            <div class="amt">{{ $line['Jumlah'] }}</div>
            <div class="muted">{{ $line['Perkiraan'] }}</div>
            @if ($line['Lawan'])<div class="muted">Lawan: {{ $line['Lawan'] }}</div>@endif
            @if ($line['Keterangan'])<div class="muted">{{ $line['Keterangan'] }}</div>@endif
          </div>
        @endforeach
        <div class="total"><span>Total</span><span>Rp {{ number_format($summary['total'], 2, ',', '.') }}</span></div>
      </div>
    @endif

    @if ($pending && $canApprove && $summary)
      <div class="card" id="rejectBox">
        <h2>Alasan ditolak</h2>
        <textarea id="catatan" maxlength="255" placeholder="Tulis alasan penolakan…"></textarea>
      </div>
    @endif
  </main>

  @if ($pending && $canApprove && $summary)
    <div class="actions" id="actions">
      <button type="button" class="btn-reject" id="btnReject">Tolak</button>
      <button type="button" class="btn-approve" id="btnApprove">Setujui</button>
    </div>

    <script>
    (function () {
      var token = document.querySelector('meta[name="csrf-token"]').content;
      var base = "{!! url('otorisasi/'.$request->ID) !!}";
      var btnA = document.getElementById('btnApprove');
      var btnR = document.getElementById('btnReject');
      var rejecting = false;

      function showMsg(ok, text) {
        var el = document.getElementById('msg');
        el.className = 'banner ' + (ok ? 'A' : 'R');
        el.textContent = text;
        el.style.display = 'block';
        window.scrollTo(0, 0);
      }

      function send(action, body) {
        btnA.disabled = btnR.disabled = true;
        return fetch(base + '/' + action, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
          body: JSON.stringify(body || {})
        }).then(function (r) {
          return r.json().catch(function () { return { ok: false, message: 'Terjadi kesalahan (' + r.status + ')' }; });
        }).then(function (res) {
          showMsg(!!res.ok, res.message || (res.errors ? Object.values(res.errors)[0][0] : 'Terjadi kesalahan'));
          if (res.ok || /sudah/.test(res.message || '')) {
            document.getElementById('actions').style.display = 'none';
            document.getElementById('statusBanner').style.display = 'none';
            var box = document.getElementById('rejectBox');
            if (box) box.style.display = 'none';
          } else {
            btnA.disabled = btnR.disabled = false;
          }
        }).catch(function () {
          showMsg(false, 'Koneksi gagal, coba lagi');
          btnA.disabled = btnR.disabled = false;
        });
      }

      btnA.addEventListener('click', function () {
        if (rejecting) {
          rejecting = false;
          document.getElementById('rejectBox').style.display = 'none';
          btnR.textContent = 'Tolak';
          btnA.textContent = 'Setujui';
          return;
        }
        if (confirm(@json('Setujui otorisasi '.$label.' '.$request->NOBUKTI.'?'))) send('approve');
      });

      btnR.addEventListener('click', function () {
        if (!rejecting) {
          rejecting = true;
          document.getElementById('rejectBox').style.display = 'block';
          document.getElementById('catatan').focus();
          btnR.textContent = 'Kirim Penolakan';
          btnA.textContent = 'Batal';
          return;
        }
        var catatan = document.getElementById('catatan').value.trim();
        if (!catatan) { showMsg(false, 'Alasan penolakan wajib diisi'); return; }
        send('reject', { catatan: catatan });
      });
    })();
    </script>
  @endif
</body>
</html>
