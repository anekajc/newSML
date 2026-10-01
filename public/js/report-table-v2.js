/*
 * SUPERSEDED — isinya sudah dipindah ke public/js/report-table.js sebagai
 * window.ReportTable (bukan lagi window.ReportTableV2). File ini TIDAK
 * dimuat lagi oleh report/masterreport2.blade.php, tetap disimpan di disk
 * sebagai arsip. Jangan diedit — edit public/js/report-table.js sebagai
 * gantinya.
 */

/*
 * report-table-v2.js
 * Header tabel interaktif untuk halaman report yang me-render <thead> dari
 * gcart_header (lihat report/masterreport2.blade.php).
 *
 *   - seret judul kolom untuk mengubah urutan kolom
 *   - menu roda gigi per kolom: sembunyikan, jumlah desimal, tampilkan total
 *   - bar di atas tabel: daftar kolom tersembunyi + dropdown "Tampilan"
 *     (Detail/Rekap) bila halaman memang punya filter tersebut
 *
 * Semua perubahan disimpan lewat fungsi milik masterreport2
 * (doButtonVisibility / doSetDesimal / doButtonTotal / doMoveHeader), jadi
 * modal "Atur Kolom" dan header tabel selalu memakai state yang sama dan
 * ikut tersimpan ke DBSIMPANHEADER.
 *
 * Pemakaian di halaman report:
 *
 *   ReportTableV2.init({
 *     table    : '#mainTable',
 *     bar      : '#rtv2Bar',
 *     onChange : render,                       // fungsi render tabel halaman
 *     views    : {                             // opsional (Detail/Rekap saja)
 *       label   : 'Tampilan',
 *       options : [{ value: '0', label: 'Detail', desc: 'Rincian per baris' }],
 *       get     : function () { return globalReportMode; },
 *       set     : function (v) { ... }
 *     }
 *   });
 *
 *   // di dalam render() halaman, ganti pembuatan <thead> menjadi:
 *   thead.innerHTML = ReportTableV2.headHtml(cols);
 */
(function () {
  "use strict";

  if (window.ReportTableV2) { return; }

  var cfg      = null;
  var openGidx = -1;   // index kolom (di gcart_header) yang menunya terbuka
  var dragGidx = -1;   // index kolom yang sedang diseret
  var menuEl   = null; // elemen .rt-colmenu di <body>

  /* ---------------- helper ---------------- */

  function cart() {
    return (typeof window.gcart_header !== "undefined" && window.gcart_header) ? window.gcart_header : [];
  }

  function isNumeric(col) {
    return (col && (col[3] === "float" || col[3] === "int"));
  }

  function closestEl(node, sel) {
    return (node && node.closest) ? node.closest(sel) : null;
  }

  function esc(val) {
    return String(val == null ? "" : val)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  function tableEl() {
    return (cfg && cfg.table) ? document.querySelector(cfg.table) : null;
  }

  function barEl() {
    return (cfg && cfg.bar) ? document.querySelector(cfg.bar) : null;
  }

  // Simpan langsung — hanya dipakai bila fungsi masterreport2 tidak tersedia.
  function saveHeader() {
    if (typeof window.doSimpanHeader !== "function" || typeof window.g_href === "undefined") { return; }
    window.doSimpanHeader(window.g_href, window.g_modeReport, cart(), window.gsum_issubtotal, window.gsum_isgrandtotal);
  }

  function fireChange() {
    if (cfg && typeof cfg.onChange === "function") { cfg.onChange(); }
  }

  /* ---------------- <thead> ---------------- */

  // cols = hasil gcart_header.filter(c => c[2] === 1) di halaman. Elemennya
  // adalah referensi ke array yang sama, jadi indexOf() memberi index global.
  function headHtml(cols) {
    var all  = cart();
    var html = "<tr>";

    (cols || []).forEach(function (c) {
      var g   = all.indexOf(c);
      var num = isNumeric(c);

      html += '<th class="rt-th' + (num ? " num" : "") + '" data-gidx="' + g + '">';
      html += '  <div class="th-inner" draggable="true">';
      html += '    <span class="th-grip"><i></i><i></i><i></i><i></i><i></i><i></i></span>';
      html += '    <span class="th-label">' + c[1] + "</span>";
      html += '    <button type="button" class="th-gear' + (openGidx === g ? " active" : "") +
              '" data-rtgear="' + g + '" title="Setting kolom"><i class="bi bi-gear"></i></button>';
      html += "  </div>";
      html += "</th>";
    });

    html += "</tr>";

    // bar ikut disegarkan tiap render supaya jumlah kolom tersembunyi akurat
    renderBar();

    return html;
  }

  function clearDragState() {
    var t = tableEl();
    if (!t) { return; }
    Array.prototype.forEach.call(t.querySelectorAll("thead .drag-over"), function (el) {
      el.classList.remove("drag-over");
    });
    Array.prototype.forEach.call(t.querySelectorAll("thead .th-inner.dragging"), function (el) {
      el.classList.remove("dragging");
    });
  }

  function bindHead(thead) {
    thead.addEventListener("dragstart", function (e) {
      var inner = closestEl(e.target, ".th-inner");
      if (!inner) { return; }
      var th = closestEl(inner, "th.rt-th");
      if (!th) { return; }

      dragGidx = Number(th.getAttribute("data-gidx"));
      inner.classList.add("dragging");
      closeMenu();

      if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = "move";
        try { e.dataTransfer.setData("text/plain", String(dragGidx)); } catch (err) { /* IE */ }
      }
    });

    thead.addEventListener("dragend", function () {
      dragGidx = -1;
      clearDragState();
    });

    thead.addEventListener("dragover", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (!th || dragGidx < 0) { return; }
      e.preventDefault();
      if (dragGidx !== Number(th.getAttribute("data-gidx"))) { th.classList.add("drag-over"); }
    });

    thead.addEventListener("dragleave", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (th && !th.contains(e.relatedTarget)) { th.classList.remove("drag-over"); }
    });

    thead.addEventListener("drop", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (!th) { return; }
      e.preventDefault();

      var from = dragGidx;
      var to   = Number(th.getAttribute("data-gidx"));
      dragGidx = -1;
      clearDragState();
      moveColumn(from, to);
    });

    thead.addEventListener("click", function (e) {
      var gear = closestEl(e.target, "[data-rtgear]");
      if (!gear) { return; }
      e.preventDefault();
      e.stopPropagation();

      var g = Number(gear.getAttribute("data-rtgear"));
      if (openGidx === g) { closeMenu(); return; }
      openGidx = g;
      openMenu(gear, g);
    });
  }

  function moveColumn(from, to) {
    var all = cart();
    if (from < 0 || to < 0 || from === to) { return; }
    if (from >= all.length || to >= all.length) { return; }

    if (typeof window.doMoveHeader === "function") {
      window.doMoveHeader(from, to);
    } else {
      all.splice(to, 0, all.splice(from, 1)[0]);
      saveHeader();
    }

    closeMenu();
    fireChange();
  }

  /* ---------------- menu kolom ---------------- */

  function openMenu(anchor, g) {
    var col = cart()[g];
    if (!col) { return; }

    destroyMenu();

    var html = '<div class="rt-colmenu-item" data-rtact="hide"><span>Sembunyikan kolom</span></div>';

    if (isNumeric(col)) {
      html += '<div class="rt-colmenu-divider"></div>';
      html += '<div class="rt-colmenu-item is-static"><span>Desimal</span>' +
              '<span class="rt-stepper">' +
              '<button type="button" data-rtact="dec-minus">&minus;</button>' +
              "<b>" + Number(col[5]) + "</b>" +
              '<button type="button" data-rtact="dec-plus">+</button>' +
              "</span></div>";
      html += '<div class="rt-colmenu-item is-static"><span>Tampilkan total</span>' +
              '<button type="button" class="rt-miniswitch' + (Number(col[4]) === 1 ? " on" : "") +
              '" data-rtact="total"></button></div>';
    }

    menuEl = document.createElement("div");
    menuEl.className = "rt-colmenu";
    menuEl.innerHTML = html;
    document.body.appendChild(menuEl);

    positionMenu(anchor);
    anchor.classList.add("active");

    menuEl.addEventListener("click", function (e) {
      e.stopPropagation();
      var el = closestEl(e.target, "[data-rtact]");
      if (el) { menuAction(el.getAttribute("data-rtact"), g); }
    });
  }

  function positionMenu(anchor) {
    var r = anchor.getBoundingClientRect();
    var w = menuEl.offsetWidth || 216;
    var h = menuEl.offsetHeight || 120;

    var left = Math.min(r.left, window.innerWidth - w - 12);
    if (left < 8) { left = 8; }

    var top = r.bottom + 6;
    if (top + h > window.innerHeight - 8) { top = Math.max(8, r.top - h - 6); }

    menuEl.style.left = left + "px";
    menuEl.style.top  = top + "px";
  }

  function menuAction(act, g) {
    var all = cart();
    if (!all[g]) { return; }

    if (act === "hide") {
      if (typeof window.doButtonVisibility === "function") { window.doButtonVisibility(g); }
      else { all[g][2] = 0; saveHeader(); }
      closeMenu();
      fireChange();
      return;
    }

    if (act === "dec-minus" || act === "dec-plus") {
      var step = (act === "dec-plus") ? 1 : -1;
      if (typeof window.doSetDesimal === "function") {
        window.doSetDesimal(g, step);
      } else {
        var next = Number(all[g][5]) + step;
        if (next < 0 || next > 4) { return; }
        all[g][5] = next;
        saveHeader();
      }
    } else if (act === "total") {
      if (typeof window.doButtonTotal === "function") {
        window.doButtonTotal(g);
      } else {
        all[g][4] = (Number(all[g][4]) === 1) ? 0 : 1;
        saveHeader();
      }
    } else {
      return;
    }

    // render ulang tabel, lalu buka lagi menu pada kolom yang sama
    fireChange();
    reopenMenu(g);
  }

  function reopenMenu(g) {
    var t = tableEl();
    if (!t) { closeMenu(); return; }

    var gear = t.querySelector('thead [data-rtgear="' + g + '"]');
    if (!gear) { closeMenu(); return; }

    openGidx = g;
    openMenu(gear, g);
  }

  function destroyMenu() {
    if (menuEl && menuEl.parentNode) { menuEl.parentNode.removeChild(menuEl); }
    menuEl = null;
  }

  function closeMenu() {
    openGidx = -1;
    destroyMenu();

    var t = tableEl();
    if (!t) { return; }
    Array.prototype.forEach.call(t.querySelectorAll("thead .th-gear.active"), function (b) {
      b.classList.remove("active");
    });
  }

  /* ---------------- bar di atas tabel ---------------- */

  function renderBar() {
    var bar = barEl();
    if (!bar) { return; }

    var all    = cart();
    var hidden = [];
    all.forEach(function (c, i) { if (Number(c[2]) !== 1) { hidden.push({ col: c, idx: i }); } });

    var items = "";
    if (hidden.length) {
      hidden.forEach(function (h) {
        items += '<div class="rt-drop-item" data-rtshow="' + h.idx + '"><span>' + h.col[1] +
                 '</span><small>tampilkan</small></div>';
      });
    } else {
      items = '<div class="rt-drop-empty">Tidak ada kolom tersembunyi</div>';
    }

    var html = "";
    html += '<div class="rt-drop">';
    html += '  <button type="button" class="rt-hidden-btn" data-rtbar="hidden"' + (hidden.length ? "" : " disabled") + ">";
    html += '    <i class="bi bi-plus-lg"></i><span>' +
            (hidden.length ? hidden.length + " kolom tersembunyi" : "Semua kolom tampil") + "</span>";
    html += "  </button>";
    html += '  <div class="rt-drop-menu">' + items + "</div>";
    html += "</div>";

    if (cfg && cfg.views && cfg.views.options && cfg.views.options.length) { html += viewHtml(); }

    bar.classList.add("rt-bar");
    bar.innerHTML = html;
  }

  function viewHtml() {
    var v       = cfg.views;
    var current = String(v.get ? v.get() : "");
    var label   = "";
    var items   = "";

    v.options.forEach(function (o) {
      var on = (String(o.value) === current);
      if (on) { label = o.label; }
      items += '<div class="rt-drop-item' + (on ? " active" : "") + '" data-rtview="' + esc(o.value) + '">' +
               "<span>" + esc(o.label) + "</span>" +
               (o.desc ? "<small>" + esc(o.desc) + "</small>" : "") +
               "</div>";
    });

    return '<div class="rt-drop">' +
           '  <button type="button" class="rt-view-btn" data-rtbar="view">' +
           '    <i class="bi bi-list"></i><span>' + esc(v.label || "Tampilan") + ": " + esc(label || "-") + "</span>" +
           "  </button>" +
           '  <div class="rt-drop-menu">' + items + "</div>" +
           "</div>";
  }

  function closeBarMenus() {
    var bar = barEl();
    if (!bar) { return; }
    Array.prototype.forEach.call(bar.querySelectorAll(".rt-drop-menu.open"), function (m) {
      m.classList.remove("open");
    });
  }

  function bindBar(bar) {
    bar.addEventListener("click", function (e) {
      var btn = closestEl(e.target, "[data-rtbar]");
      if (btn) {
        e.stopPropagation();
        if (btn.disabled) { return; }
        var menu = btn.parentNode.querySelector(".rt-drop-menu");
        var open = menu && menu.classList.contains("open");
        closeBarMenus();
        closeMenu();
        if (menu && !open) { menu.classList.add("open"); }
        return;
      }

      var show = closestEl(e.target, "[data-rtshow]");
      if (show) {
        e.stopPropagation();
        closeBarMenus();
        var g = Number(show.getAttribute("data-rtshow"));
        if (typeof window.doButtonVisibility === "function") { window.doButtonVisibility(g); }
        else if (cart()[g]) { cart()[g][2] = 1; saveHeader(); }
        fireChange();
        return;
      }

      var view = closestEl(e.target, "[data-rtview]");
      if (view) {
        e.stopPropagation();
        closeBarMenus();
        if (cfg.views && typeof cfg.views.set === "function") { cfg.views.set(view.getAttribute("data-rtview")); }
        renderBar();
      }
    });
  }

  /* ---------------- init ---------------- */

  function init(options) {
    cfg = options || {};

    var t = tableEl();
    if (!t) { return; }

    var thead = t.querySelector("thead");
    if (thead) { bindHead(thead); }

    var bar = barEl();
    if (bar) { bindBar(bar); }

    document.addEventListener("click", function () { closeMenu(); closeBarMenus(); });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) { closeMenu(); closeBarMenus(); }
    });

    // capture: ikut menangkap scroll di dalam .table-wrap (overflow:auto)
    window.addEventListener("scroll", function () { closeMenu(); }, true);
    window.addEventListener("resize", function () { closeMenu(); closeBarMenus(); });

    renderBar();
  }

  window.ReportTableV2 = {
    init:     init,
    headHtml: headHtml,
    refresh:  renderBar,
    close:    function () { closeMenu(); closeBarMenus(); }
  };
})();
