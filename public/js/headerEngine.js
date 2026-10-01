/**
 * headerEngine.js
 *
 * Reusable "interactive column engine" -- drag-to-reorder columns, hide/show,
 * per-column decimals and totals, backed by window.ReportTable (see
 * public/js/report-table.js) for the actual drag/gear UI. This file owns the
 * *state* (gcart_header, which columns are visible/in what order) and its
 * *persistence* (loading/saving that layout per user, per table) so several
 * tables -- even across different pages/menus -- can each get their own
 * independently-saved column layout without fighting over shared globals.
 *
 * Extracted from resources/views/marketing/so.blade.php, where it originally
 * drove #tabel and #tabel7's column headers.
 *
 * ---------------------------------------------------------------------------
 * Why some globals below are NOT namespaced under HeaderEngine:
 *
 * report-table.js's ReportTable is itself a page-wide singleton that reads
 * window.gcart_header/g_href/g_modeReport/gsum_issubtotal/gsum_isgrandtotal
 * directly, and falls back to calling window.doMoveHeader/doButtonVisibility/
 * doSetDesimal/doButtonTotal/doSimpanHeader BY THESE EXACT NAMES if they
 * exist. That contract isn't something this file can change, so those
 * specific names stay bare globals -- everything else this file exposes is
 * namespaced under window.HeaderEngine.
 * ---------------------------------------------------------------------------
 *
 * Usage on a page with one or more interactive tables:
 *
 *   1. Load jQuery, then report-table.js, then this file, then your page's
 *      own <script> block.
 *
 *   2. Point HeaderEngine at your page's own header-persistence endpoints
 *      (see app/Http/Controllers/Marketing/SOController.php's
 *      loadHeader()/simpanHeader() methods for the expected request/response
 *      shape -- GET ?href=&mode= returning [{header, issubtotal, isgrandtotal}],
 *      and GET ?href=&mode=&header=&issubtotal=&isgrandtotal= to save):
 *
 *        HeaderEngine.configure({
 *          loadUrl: "{!! url('yourloadheaderroute') !!}",
 *          simpanUrl: "{!! url('yoursimpanheaderroute') !!}"
 *        });
 *
 *   3. Register each table, giving it a unique persistence key (href),
 *      the table/bar CSS selectors ReportTable.init() needs, a
 *      setDefault() callback that assigns this table's default columns to
 *      the (bare global) gcart_header array, and an onChange() callback that
 *      re-renders the table body/header and re-initializes DataTables (see
 *      so.blade.php's renderTabelRows()/reinitTabel() for a worked example):
 *
 *        HeaderEngine.registerTable('mytable', {
 *          href: 'mymenu_mytable',       // unique per table, saved to the DB
 *          tableSel: '#mytable',
 *          barSel: '#rtBarMyTable',
 *          setDefault: function () {
 *            gcart_header = [
 *              // [ field, label, visible(0/1), type, hasTotal(0/1), decimals ]
 *              ['KODE', 'Kode', 1, 'varchar', 0, 0],
 *              ['NILAI', 'Nilai', 1, 'float', 0, 2]
 *            ];
 *          },
 *          onChange: function () { reinitMyTable(); }
 *        });
 *
 *   4. On page load, for each table: activate its data, load/seed its
 *      header, render it, then bind ReportTable to the freshly-rendered DOM
 *      (bindEngineDom() must run AFTER your table's DOM is (re)built, never
 *      before -- ReportTable's listeners bind to whatever nodes exist at
 *      call time):
 *
 *        HeaderEngine.activateEngineData('mytable');
 *        HeaderEngine.doSetHeader(1);   // g_modeReport; use a constant if
 *                                       // your table has no Detail/Rekap mode
 *        renderMyTableRows(initialRows);
 *        // ...initialize DataTables here...
 *        HeaderEngine.bindEngineDom('mytable');
 *
 *   5. Whenever you switch to a different visible table (e.g. a tab click)
 *      or rebuild one table's rows, call activateEngineData(key) again
 *      before touching gcart_header, and bindEngineDom(key) again after the
 *      table's DOM is rebuilt.
 */

// report-table.js's ReportTable reads these exact global names -- keep them
// bare, not namespaced. See the file-top comment for why.
var g_href = '';
var g_modeReport = 1;
var gcart_header = [];
var gsum_issubtotal = 0, gsum_isgrandtotal = 0;

var HeaderEngine = (function () {
  var _config = { loadUrl: '', simpanUrl: '' };
  var _engines = {};
  var _activeKey = null;

  function configure(opts) {
    _config.loadUrl = opts.loadUrl;
    _config.simpanUrl = opts.simpanUrl;
  }

  // engine: { href, tableSel, barSel, setDefault(), onChange() }
  // gcart_header/issubtotal/isgrandtotal are filled in automatically as
  // this table's state gets checkpointed in/out by activateEngineData().
  function registerTable(key, engine) {
    engine.gcart_header = engine.gcart_header || [];
    engine.issubtotal = engine.issubtotal || 0;
    engine.isgrandtotal = engine.isgrandtotal || 0;
    _engines[key] = engine;
  }

  // Two data sources on the same page/table sometimes return the same
  // logical field with different casing (e.g. nopesanan vs NoPesanan) --
  // look a field up case-insensitively instead of trusting exact casing.
  function pickCI(row, key) {
    if (row[key] !== undefined) { return row[key]; }
    var lower = key.toLowerCase();
    for (var k in row) {
      if (k.toLowerCase() === lower) { return row[k]; }
    }
    return undefined;
  }

  function doGetHeader(_str) {
    var arr = [];
    _str.split('||').forEach(function (part) {
      var f = part.split(';;');
      arr.push([f[0], f[1], Number(f[2]), f[3], Number(f[4]), Number(f[5])]);
    });
    return arr;
  }

  function doLoadHeader(_hrefArg, _mode) {
    var _strHeader = "";
    $.ajax({
      url: _config.loadUrl,
      type: "get",
      async: false,
      data: { href: _hrefArg, mode: _mode },
      success: function (res) {
        if (res && res[0]) {
          _strHeader = res[0].header;
          gsum_issubtotal = Number(res[0].issubtotal) || 0;
          gsum_isgrandtotal = Number(res[0].isgrandtotal) || 0;
        }
      }
    });
    return _strHeader;
  }

  // Bare global on purpose -- see file-top comment. ReportTable's saveHeader()
  // fallback checks for window.doSimpanHeader by this exact name.
  window.doSimpanHeader = function (_hrefArg, _mode, _cart, _issubtotal, _isgrandtotal) {
    var _strHeader = "";
    _cart.forEach(function (item, i) {
      if (i != 0) { _strHeader += '||'; }
      _strHeader += item[0] + ';;' + item[1] + ';;' + item[2] + ';;' + item[3] + ';;' + item[4] + ';;' + item[5];
    });
    $.ajax({
      url: _config.simpanUrl,
      type: "get",
      async: false,
      data: {
        href: _hrefArg,
        mode: _mode,
        header: _strHeader,
        issubtotal: _issubtotal,
        isgrandtotal: _isgrandtotal
      },
      success: function (res) {}
    });
  };

  function doSetHeader(_modereport, _isReset) {
    _isReset = _isReset || false;
    var _strHeader = (!_isReset) ? doLoadHeader(g_href, _modereport) : "";
    if (_strHeader != "") {
      gcart_header = doGetHeader(_strHeader);
    } else {
      _engines[_activeKey].setDefault();
      window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
    }
  }

  // Bare globals on purpose -- see file-top comment. ReportTable's
  // moveColumn()/menuAction() fallbacks check for these exact names.
  window.doMoveHeader = function (_from, _to) {
    var moved = gcart_header.splice(_from, 1)[0];
    gcart_header.splice(_to, 0, moved);
    window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  };

  window.doButtonVisibility = function (_id) {
    gcart_header[_id][2] = gcart_header[_id][2] ? 0 : 1;
    window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  };

  window.doSetDesimal = function (_index, _step) {
    var v = (gcart_header[_index][5] || 0) + _step;
    if (v < 0) { v = 0; }
    if (v > 4) { v = 4; }
    gcart_header[_index][5] = v;
    window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  };

  window.doButtonTotal = function (_index) {
    gcart_header[_index][4] = gcart_header[_index][4] ? 0 : 1;
    window.doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal);
  };

  // Swaps which registered table's column state the global gcart_header/
  // g_href/etc. point to. Data-only -- doesn't touch the DOM/ReportTable, so
  // it's safe to call before a DataTables destroy()/rebuild.
  function activateEngineData(key) {
    var prev = _activeKey && _engines[_activeKey];
    if (prev) {
      prev.gcart_header = gcart_header;
      prev.issubtotal = gsum_issubtotal;
      prev.isgrandtotal = gsum_isgrandtotal;
    }
    _activeKey = key;
    var e = _engines[key];
    g_href = e.href;
    gcart_header = e.gcart_header;
    gsum_issubtotal = e.issubtotal;
    gsum_isgrandtotal = e.isgrandtotal;
  }

  // Re-binds ReportTable's drag/gear listeners to the given table's current
  // DOM. Must run AFTER any DataTables destroy()/rebuild, never before -- the
  // listeners bind to whatever thead/table/bar nodes exist at call time, so
  // binding to a about-to-be-replaced node leaves drag/gear silently dead
  // (or throwing) on the next interaction.
  function bindEngineDom(key) {
    var e = _engines[key];
    ReportTable.init({ table: e.tableSel, bar: e.barSel, onChange: e.onChange });
  }

  function activeKey() {
    return _activeKey;
  }

  return {
    configure: configure,
    registerTable: registerTable,
    pickCI: pickCI,
    doGetHeader: doGetHeader,
    doSetHeader: doSetHeader,
    activateEngineData: activateEngineData,
    bindEngineDom: bindEngineDom,
    activeKey: activeKey
  };
})();
