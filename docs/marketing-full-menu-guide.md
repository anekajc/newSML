Task: Port a menu to match so.blade.php's UI 1:1, following the same pattern already applied to so.blade.php, invoicepenjualan.blade.php, suratjalan.blade.php, invoicejasa.blade.php, purchaseOrder.blade.php, and fakturpajak.blade.php.

User's standing instructions for this pattern:

Always use so.blade.php as the reference/standpoint for matching UI.
No new CSS — reuse po-table-header.css + copy the small page-local block (.custom-tabs, .tab-card, .po-len-wrap/.po-len-inp, pastel #tabel td:first-child .btn-* action-button styling) verbatim from so.blade.php's own @section('css').
Every modal picker that currently has a "+" button per row → convert to click-anywhere-on-row-to-pick (<tr class="pick-row" onclick="...">, remove the Actions column/button). Exception: tables that are genuine multi-select checklists (checkboxes, e.g. export-selection tables) stay as-is — do not convert those.
Every table with bg-primary text-white thead → strip it, use class="data-table" instead (grey bg, dark text comes free from newmasterx's own CSS).
Preserve all existing business-logic functions (loadAll, add/edit/delete/otorisasi handlers, import/export) — only the layout/toolbar/column-header interactivity changes.
If there's an Import Excel / Export Excel button pair, put them inline in the same toolbar row as the periode date pickers (po-toolbar + po-toolbar-act), and make sure the periode inputs' existing onchange handler is preserved.
Concrete steps per page (established over 5 prior ports):

@extends → newmasterTest (or purchasing.newmasterx, check what the other already-ported marketing pages use — most recent ones use newmasterTest).
Add <link> to po-table-header.css + the copied page-local <style> block in @section('css').
Add <script src="report-table.js"> to @section('js') — do this FIRST, before anything else. This was missed on both invoicejasa.blade.php and fakturpajak.blade.php and caused a DataTables ... Cannot read properties of undefined (reading 'aDataSort'/'mData') crash both times, because window.ReportTable being undefined makes the thead-writing function silently no-op, leaving DataTables to init on a table with zero header columns.
Restructure tab bar → card.tab-card + .custom-tabs pill pattern (remove old inline-style tab coloring and any leftover setActiveTab() JS that manually sets .nav-link inline styles — that fights the new CSS since inline styles win the cascade).
Restructure toolbar → po-toolbar (periode/search/Tampilkan/Import/Export as applicable).
Restructure each table → po-toolbar (search+Tampilkan) + #rtBarTabelX + class="data-table" + po-rt-hint, empty <thead> populated at runtime.
Build the inline changeable-headers engine per table (xxCart, xxAktifkanTabel, xxBuatCart, window.doSimpanHeader/doSetHeader via saveheadertable/getheadertable, xxInitReportTableSekali, renderXRows, reinitX) — copy the shape from invoicejasa.blade.php's JS block, it's the cleanest reference.
Convert loadAll()'s duplicate row-building template strings to call the same renderXRows() functions instead of building HTML twice.
Convert picker modals' "+" button rows to pick-row click pattern; fix any DataTables columnDefs/index references that assumed an Actions column at index 0.
Verification before calling it done (run all of these, don't skip):


# Blade compiles
php artisan tinker --execute="app('blade.compiler')->compileString(file_get_contents(resource_path('views/marketing/cetaktandaterima.blade.php')));"

# JS syntax (strip Blade directives, check with node --check)
# div balance (custom line-by-line tracer skipping script/style/comments)
# grep for stray bg-primary text-white left behind
# grep to confirm report-table.js script tag is present