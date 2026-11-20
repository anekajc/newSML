/*
 * UserFilter — simpan & pulihkan pilihan filter laporan per user.
 * Disimpan di server (DBUSERFILTER via UserFilterController), jadi ikut ke device lain.
 *
 * 1) Modal filter dengan select.rt-native (lihat reportaccountingkasharian.blade.php):
 *      UserFilter.restore('#modalFilter', function (values) { ...sinkron variabel global... });
 *      UserFilter.save('#modalFilter');   // panggil di handler tombol Terapkan
 *    - Hanya select.rt-native ber-id di dalam modal; tambahkan data-nosave untuk mengecualikan.
 *    - Nilai tersimpan yang opsinya sudah tidak ada di dropdown dilewati (default halaman tetap).
 *
 * 2) Filter lain (dropdown toolbar, picker kode+nama, input teks), lihat reportgudangkonsin.blade.php:
 *      UserFilter.track('toolbar', {
 *        otorisasi: UserFilter.menu('setOtorisasi', '#dropdownOtorisasi', function () { return globalOtorisasi; }),
 *        customer:  UserFilter.input('#inputCustomer'),
 *        perkiraan: { get: fn, set: fn(v), valid: fn(v), wrap: 'namaFungsiGlobal' }
 *      });
 *    - Dipanggil SEKALI di $(document).ready, setelah default halaman dipasang dan sebelum laporan dimuat.
 *    - Nilai tersimpan dipasang lewat set(v) — sama seperti user memilih — hanya kalau valid(v).
 *    - Simpan otomatis: setiap kali fungsi global di `wrap` dipanggil (klik user), atau input berubah.
 *      Bisa juga manual: UserFilter.commit('toolbar').
 *    - Tanggal / periode jangan dimasukkan (selalu default).
 *
 * Config (token, url) dari window.USER_FILTER_CFG — partials/user-filter.blade.php.
 */
(function (window, $) {
  'use strict';

  var cfg = window.USER_FILTER_CFG || {};
  var tracked = {};     // key -> fields
  var timers = {};      // key -> debounce timer
  var restoring = 0;    // >0 selama nilai tersimpan sedang dipasang (jangan simpan balik)

  function pageHref() {
    return (window.location.pathname || '').replace(/^\/+/, '').replace(/\/+$/, '');
  }

  function modalKey($modal) {
    return $modal.attr('id') || 'default';
  }

  function fields($modal) {
    return $modal.find('select.rt-native[id]').not('[data-nosave]');
  }

  function hasOption($select, value) {
    var found = false;
    $select.find('option').each(function () {
      if (String(this.value) === String(value)) { found = true; return false; }
    });
    return found;
  }

  function has(obj, k) {
    return Object.prototype.hasOwnProperty.call(obj, k);
  }

  /** Ambil filter tersimpan (sinkron). Gagal / belum ada = {}. */
  function load(key) {
    if (!cfg.getUrl) { return {}; }
    var saved = {};
    $.ajax({
      url: cfg.getUrl,
      type: 'get',
      async: false,
      data: { href: pageHref(), key: key },
      success: function (res) { saved = (res && typeof res === 'object') ? res : {}; },
      error: function (xhr) { console.warn('UserFilter: gagal memuat filter tersimpan', xhr.status); }
    });
    return saved;
  }

  /** Simpan objek datar { nama: nilai } (background, gagal diam-diam). */
  function store(key, values) {
    if (!cfg.saveUrl) { return; }
    $.ajax({
      url: cfg.saveUrl,
      type: 'post',
      data: {
        _token: cfg.token,
        href: pageHref(),
        key: key,
        filters: JSON.stringify(values)
      },
      error: function (xhr) { console.warn('UserFilter: gagal menyimpan filter', xhr.status); }
    });
  }

  /**
   * Ambil filter tersimpan & terapkan ke select di modal.
   * Sinkron (async:false) supaya nilai sudah terpasang sebelum kode halaman berikutnya
   * berjalan — sama seperti pola loadXxxDropdown yang sudah ada.
   * onRestored(values) hanya dipanggil dengan nilai yang benar-benar terpasang.
   */
  function restore(modalSelector, onRestored) {
    var $modal = $(modalSelector);
    if (!$modal.length || !cfg.getUrl) { return {}; }

    var saved = load(modalKey($modal));

    var applied = {};
    fields($modal).each(function () {
      var $s = $(this);
      var id = this.id;
      if (has(saved, id) && saved[id] !== null && hasOption($s, saved[id])) {
        $s.val(String(saved[id]));
        applied[id] = String(saved[id]);
      }
    });

    if (typeof onRestored === 'function') { onRestored(applied); }
    return applied;
  }

  /** Simpan nilai select di modal (background, gagal diam-diam). */
  function save(modalSelector) {
    var $modal = $(modalSelector);
    if (!$modal.length || !cfg.saveUrl) { return; }

    var values = {};
    fields($modal).each(function () {
      var v = $(this).val();
      if (v !== null && v !== undefined && !Array.isArray(v)) { values[this.id] = String(v); }
    });

    store(modalKey($modal), values);
  }

  /* ---------- track: filter di luar modal / picker / dropdown toolbar ---------- */

  /** Simpan semua field yang di-track pada `key` sekarang juga. */
  function commit(key) {
    var f = tracked[key];
    if (!f) { return; }
    clearTimeout(timers[key]);
    var values = {};
    Object.keys(f).forEach(function (name) {
      var v;
      try { v = f[name].get(); } catch (e) { return; }
      if (v !== null && v !== undefined && typeof v !== 'object') { values[name] = String(v); }
    });
    store(key, values);
  }

  /** Simpan sebentar lagi (beberapa setter yang dipanggil berurutan jadi satu request). */
  function schedule(key) {
    if (restoring > 0) { return; }
    clearTimeout(timers[key]);
    timers[key] = setTimeout(function () { commit(key); }, 300);
  }

  /** Bungkus fungsi global supaya setiap pemanggilannya ikut menyimpan filter. */
  function wrapGlobal(fnName, key) {
    var orig = window[fnName];
    if (typeof orig !== 'function') {
      console.warn('UserFilter: fungsi global ' + fnName + ' tidak ditemukan');
      return;
    }
    if (orig.__ufKeys) { orig.__ufKeys[key] = true; return; }
    var keys = {};
    keys[key] = true;
    var wrapped = function () {
      var r = orig.apply(this, arguments);
      Object.keys(keys).forEach(schedule);
      return r;
    };
    wrapped.__ufKeys = keys;
    window[fnName] = wrapped;
  }

  /**
   * Pulihkan & awasi sekumpulan filter.
   * fields = { nama: { get(), set(v), valid(v)?, wrap?: 'fnGlobal' | ['fn1','fn2'], input?: selector } }
   * Mengembalikan { nama: nilai } yang benar-benar dipasang.
   */
  function track(key, fieldSpec) {
    tracked[key] = fieldSpec;

    Object.keys(fieldSpec).forEach(function (name) {
      var s = fieldSpec[name];
      [].concat(s.wrap || []).forEach(function (fn) { wrapGlobal(fn, key); });
      if (s.input) {
        $(document).on('change', s.input, function () { schedule(key); });
      }
    });

    var saved = load(key);
    var applied = {};
    restoring++;
    try {
      Object.keys(fieldSpec).forEach(function (name) {
        var s = fieldSpec[name];
        if (!has(saved, name) || saved[name] === null) { return; }
        var v = String(saved[name]);
        try {
          if (typeof s.valid === 'function' && !s.valid(v)) { return; }
          s.set(v);
          applied[name] = v;
        } catch (e) {
          console.warn('UserFilter: gagal memulihkan ' + name, e);
        }
      });
    } finally {
      restoring--;
    }
    return applied;
  }

  /** true kalau menu dropdown punya item dengan data-value = v. */
  function menuHas(menuSelector, v) {
    var found = false;
    $(menuSelector).find('[data-value]').each(function () {
      if (String($(this).attr('data-value')) === String(v)) { found = true; return false; }
    });
    return found;
  }

  /** Field untuk dropdown toolbar: <ul id=menu> berisi item data-value, dipilih lewat fungsi global setterName(v). */
  function menu(setterName, menuSelector, getter) {
    return {
      get: getter,
      set: function (v) { window[setterName](v); },
      valid: function (v) { return menuHas(menuSelector, v); },
      wrap: setterName
    };
  }

  /** Field untuk <input>/<select> biasa: nilai = .val(); tersimpan tiap kali berubah. */
  function input(selector, valid) {
    return {
      get: function () { return $(selector).val(); },
      set: function (v) { $(selector).val(v); },
      valid: function (v) {
        var $el = $(selector);
        if (!$el.length) { return false; }
        if ($el.is('select') && !hasOption($el, v)) { return false; }
        return typeof valid === 'function' ? valid(v) : true;
      },
      input: selector
    };
  }

  window.UserFilter = {
    restore: restore,
    save: save,
    load: load,
    store: store,
    track: track,
    commit: commit,
    menu: menu,
    menuHas: menuHas,
    input: input
  };
})(window, window.jQuery);
