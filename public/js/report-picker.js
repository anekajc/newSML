/* report-picker.js — inisialisasi DataTables untuk tabel pemilih (picker) di dalam modal
   pada halaman report. Perilakunya disalin dari kasInitPicker() di public/js/kas.js dan
   pickerKasInit() di public/js/picker-kas.js supaya dropdown "Tampilkan", kotak Search,
   info, dan pagination sama persis dengan picker di menu Kas.

   Dimuat sekali dari resources/views/report/newmaster2.blade.php dan newmaster2x.blade.php.
   Tampilan berasal dari public/css/picker-kas.css (class penanda "picker-kas") ditambah
   ".rt-picker-v2" di public/css/report-table.css.

   Pemakaian: isi <tbody> dulu, lalu panggil rtPickerInit('id_tabel', {opsi DataTables
   khusus halaman, boleh dikosongkan}). */

var RT_PICKER_DEFAULTS = {
    lengthChange: true,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
    paging: true,
    pageLength: 10,
    // Hanya tabel yang di-scroll; info + tombol halaman terkunci di bawahnya
    // (.rt-picker-scroll / .rt-picker-bawah di public/css/report-table.css).
    dom: 'lfr<"rt-picker-scroll"t><"rt-picker-bawah"ip>',
    language: {
        lengthMenu: 'Tampilkan _MENU_',
        emptyTable: 'Tidak ada data',
        zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
};

/* Skin modal picker. Dipasang saat init (bukan lewat hook show.bs.modal) karena setiap
   picker memanggil init tepat sebelum modalnya dibuka, dan halaman reportstock* memakai
   overlay kustom ".modal-picker" yang tidak pernah memicu event Bootstrap. */
function rtPickerSkin(idTabel) {
    var $modal = $('#' + idTabel).closest('.modal, .modal-picker');
    if (!$modal.length) return;
    $modal.addClass('rt-picker-v2 picker-kas');
    $modal.find('.modal-footer .btn').each(function () {
        if ($.trim($(this).text()) === 'Batal') {
            $(this).addClass('picker-kas-batal');
        }
    });
}

function rtPickerInit(idTabel, opsi) {
    var sel = '#' + idTabel;
    rtPickerSkin(idTabel);

    if ($.fn.DataTable.isDataTable(sel)) {
        // destroy() (DataTables 1.10) mengembalikan baris LAMA dari cache-nya, sehingga baris
        // baru yang barusan diisi pemanggil hilang. Simpan baris baru dulu, pasang lagi
        // setelah destroy.
        var $tbody = $(sel).children('tbody');
        var barisBaru = $tbody.children().detach();
        $(sel).DataTable().destroy();
        $tbody.empty().append(barisBaru);
    }
    // Baris placeholder ber-colspan ("Belum ada data") membuat DataTables error.
    if ($(sel + ' tbody td[colspan]').length) {
        $(sel + ' tbody').empty();
    }

    // Extend dangkal kecuali language: halaman yang hanya mengirim language.emptyTable
    // tetap mendapat 'Tampilkan _MENU_' dari default.
    var o = $.extend({}, RT_PICKER_DEFAULTS, opsi || {});
    o.language = $.extend({}, RT_PICKER_DEFAULTS.language, (opsi && opsi.language) || {});
    return $(sel).DataTable(o);
}
