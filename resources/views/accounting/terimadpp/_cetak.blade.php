{{-- Cetak "Bukti Penerimaan DPP" - dipakai bersama oleh penerimaandpp.blade.php dan
     pelunasanpiutangdpp.blade.php (keduanya memanggil SP_CETAKTERIMADPP). Di-include di
     dalam <script> halaman, parameter: $urlCetak = route detailCetak milik halaman itu. --}}

// Ambil nilai field hasil SP tanpa peduli huruf besar/kecil nama kolom; kandidat
// pertama yang ada dan tidak null yang dipakai.
function ctdPick (row, keys) {
    if (!row) { return null }
    let peta = {}
    Object.keys(row).forEach(k => { peta[k.toLowerCase()] = k })
    for (let i = 0; i < keys.length; i++) {
        let k = peta[keys[i].toLowerCase()]
        if (k !== undefined && row[k] !== null && row[k] !== '') { return row[k] }
    }
    return null
}

function ctdEsc (v) {
    if (v === null || v === undefined) { return '' }
    return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
}

function ctdAngka (v) {
    return Number(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function ctdTanggal (v) {
    if (!v) { return '' }
    let d = String(v).split(' ')[0].split('T')[0].split('-')
    if (d.length !== 3) { return String(v) }
    return Number(d[2]) + '/' + Number(d[1]) + '/' + d[0]
}

function ctdJumlah (r) {
    return Number(ctdPick(r, ['dibayar']) || 0)
}

function ctdLunas (r) {
    let v = ctdPick(r, ['myLunas'])
    return v === true || v === 1 || v === '1' || String(v).toLowerCase() === 'true'
}

function submitPrint (nobukti) {
    let _token = $('#_token').val()
    let dataPrint = []
    $.ajax({
        url: "{!! url($urlCetak) !!}",
        type: "post",
        async: false,
        data: {
            _token: _token,
            NOBUKTI: nobukti
        },
        success: function(res) {
            dataPrint = res || []
        },
        error: function(err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
    })

    if (!dataPrint.length) {
        alertify.warning('Data cetak tidak ditemukan')
        return
    }

    let maxRow = 10
    let halaman = []
    for (let i = 0; i < dataPrint.length; i += maxRow) {
        halaman.push(dataPrint.slice(i, i + maxRow))
    }

    let h = dataPrint[0]
    let noBukti = ctdPick(h, ['NoBukti']) || nobukti || ''
    let tanggal = ctdTanggal(ctdPick(h, ['Tanggal']))
    let namaCust = ctdPick(h, ['NamaCustSupp']) || ''
    let ketAtas = ctdPick(h, ['KetAtas']) || ''

    let grandTotal = 0
    dataPrint.forEach(r => { grandTotal += ctdJumlah(r) })

    let css = `<style type="text/css">
      /* Ukuran disamakan dengan cetakan menu lain (mis. pengajuandpp/giroditerima):
         kontainer 21cm tanpa @page sehingga browser mengecilkannya agar muat kertas,
         teks tabel 10px. */
      body { font-family: sans-serif; font-size: 11px; margin: 0; color: #000; }
      .ctd-page { width: 21cm; margin-left: 7px; margin-top: 5px; page-break-after: always; }
      .ctd-page:last-child { page-break-after: auto; }
      table { width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 10px; }
      td, th { padding: 1px 5px 0px; vertical-align: middle; }
      .bx { border: 1px solid #000; }
      .judul { font-size: 16px; font-weight: bold; text-align: center; }
      .tb-det th { border: 1px solid #000; font-weight: normal; text-align: center; height: 18px; }
      .tb-det td { border-left: 1px solid #000; border-right: 1px solid #000; height: 16px; }
      .tb-det tr.ctd-total td { border: 1px solid #000; font-weight: bold; }
      .r { text-align: right; }
      .c { text-align: center; }
      .tb-ttd { margin-top: 18px; }
      .tb-ttd td { border: 1px solid #000; text-align: center; width: 25%; }
      .tb-ttd tr.ttd-isi td { height: 60px; }
      .hal { text-align: right; font-size: 9px; margin-top: 4px; }
    </style>`

    let html = `<html><head><title>Bukti Penerimaan DPP ${ctdEsc(noBukti)}</title>${css}</head><body>`

    halaman.forEach((rows, p) => {
        let akhir = p === halaman.length - 1

        html += `<div class="ctd-page">
          <table>
            <colgroup>
              <col style="width:10%"><col style="width:16%"><col style="width:40%">
              <col style="width:12%"><col style="width:22%">
            </colgroup>
            <tr>
              <td class="bx judul" colspan="3" rowspan="2">BUKTI PENERIMAAN DPP</td>
              <td class="bx">No. Bukti</td>
              <td class="bx">${ctdEsc(noBukti)}</td>
            </tr>
            <tr>
              <td class="bx">Tanggal</td>
              <td class="bx">${ctdEsc(tanggal)}</td>
            </tr>
            <tr>
              <td class="bx">Terima Dari</td>
              <td class="bx" colspan="2">${ctdEsc(namaCust)}</td>
              <td class="bx" colspan="2">${ctdEsc(ketAtas)}</td>
            </tr>
          </table>

          <table class="tb-det">
            <colgroup>
              <col style="width:10%"><col style="width:16%"><col style="width:52%">
              <col style="width:16%"><col style="width:6%">
            </colgroup>
            <thead>
              <tr>
                <th>Kode</th>
                <th>Nama Perkiraan</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>LNS</th>
              </tr>
            </thead>
            <tbody>`

        rows.forEach(r => {
            let ket = [ctdPick(r, ['NoFaktur']), ctdTanggal(ctdPick(r, ['TglTitip']))].filter(v => v).join(' ')
            let nott = ctdPick(r, ['Nott'])
            ket += ' No.TT: ' + (nott || '')
            html += `<tr>
                <td>${ctdEsc(ctdPick(r, ['Perkiraan']))}</td>
                <td>${ctdEsc(ctdPick(r, ['Keterangan']))}</td>
                <td>${ctdEsc(ket)}</td>
                <td class="r">${ctdAngka(ctdJumlah(r))}</td>
                <td class="c">${ctdLunas(r) ? '&#10003;' : ''}</td>
              </tr>`
        })

        for (let k = rows.length; k < maxRow; k++) {
            html += `<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>`
        }

        html += `<tr class="ctd-total">
                <td colspan="3" class="r">TOTAL</td>
                <td class="r">${akhir ? ctdAngka(grandTotal) : ''}</td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <table class="tb-ttd">
            <tr>
              <td>Menyetujui</td>
              <td>Mengetahui</td>
              <td>Kasir</td>
              <td>Penerima</td>
            </tr>
            <tr class="ttd-isi"><td></td><td></td><td></td><td></td></tr>
          </table>
          <div class="hal">Page ${p + 1} of ${halaman.length}</div>
        </div>`
    })

    html += `</body></html>`

    let w = window.open(' ')
    w.document.write(html)
    w.document.close()
    w.focus()
    w.print()
    w.close()
}
