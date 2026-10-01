# Sidebar Navigation Migration Guide

How to replace an old top-bar mega-menu layout (e.g. `accounting/newmaster.blade.php`)
with the icon-rail, click-to-expand **sidebar** used by
`resources/views/purchasing/newmasterx.blade.php`.

---

## 1. What you're replacing

The old layout (`accounting/newmaster.blade.php` and its siblings across modules) uses a
horizontal top nav built from the Canvas theme:

```blade
<nav class="primary-menu">
  <ul class="menu-container">
    @foreach ($menul0 as $menu0)
      @if (count($menu0['child']) == 0)
        <li class="menu-item"><a class="menu-link" href="{{url($menu0->href)}}">...</a></li>
      @else
        <li class="menu-item">
          <a>{{$menu0['Keterangan']}}</a>
          <ul class="sub-menu-container">
            @foreach ($menu0['child'] as $menu1)
              <li class="menu-item"><a class="menu-link" href="{{url($menu1->href)}}">...</a></li>
            @endforeach
          </ul>
        </li>
      @endif
    @endforeach
  </ul>
</nav>
```

Submenus open on **CSS hover** (Canvas theme's own `.sub-menu-container` rules) and there's
a second thin header bar underneath showing username/periode and `@yield('buttons')`.

The new layout replaces both header bars with a **left sidebar** (icon-only, widens on
hover) plus a slim top header holding a breadcrumb, period badge, and avatar. Submenus
(`nav-children`) no longer open on hover — they open when the parent is **clicked**, accordion-style
(only one open at a time). This doc captures that pattern so it can be reapplied.

---

## 2. Reference implementation

Read `resources/views/purchasing/newmasterx.blade.php` end to end before copying — it's the
current source of truth. Key pieces, by line range (approximate, will drift):

| Piece | Where |
|---|---|
| `@php` block resolving `$activeMenuName` / `$autoPageTitle` from the current route | top of the file, before `<!doctype html>` |
| CSS variables (`--sidebar-col`, `--sidebar-exp`, `--sidebar-bg`, …) | `:root` in `<style>` |
| `.sidebar`, `.sidebar:hover` (width expand) | ~L82-96 |
| `.nav-group`, `.nav-item`, `.nav-icon`, `.nav-label` | ~L150-226 |
| `.nav-children` / `.nav-group.open>.nav-children` (click-to-open) | ~L228-245 |
| `.nav-child` (submenu row) | ~L247-279 |
| `<aside class="sidebar">` markup + icon SVG dictionary + `@foreach ($menul0 as $menu0)` | ~L664-712 |
| `<header class="header">` with breadcrumb + period badge + avatar | further down `<body>` |
| `toggleNavGroup()` JS | near the other small JS helpers (`numberWithCommas`, etc.) |

> There's also a large commented-out `<!-- ... -->` block further down the file — an older,
> static (non-`@foreach`) draft of the same sidebar, kept for reference/icon SVGs only. It is
> **not live markup**; don't copy from it without checking against the rules below (it still
> has the old `x`-as-chevron placeholder and hover-based children, both removed from the live
> version).

A second reference exists for layouts that don't fit this simple case — data wrapped in
`$akses` instead of top-level variables, menus nesting past 2 levels, or a large
extended-by-many-pages blast radius: `resources/views/report/newmaster2x.blade.php` +
`resources/views/report/partials/sidebar-nav-node.blade.php`. See §7 before starting a
migration where any of those apply.

---

## 3. Anatomy of the sidebar

### 3.1 Page shell
`body { display:flex; height:100vh; overflow:hidden; }` with two flex children:
`<aside class="sidebar">` (fixed icon width, expands on `:hover`) and `<div class="main">`
(header + `<div class="content">@yield('content')</div>`).

### 3.2 CSS — copy these rules as-is
```css
:root {
  --sidebar-col: 64px;
  --sidebar-exp: 240px;
  --hdr: 52px;
  --blue: #1a73e8;
  --sidebar-bg: #1e2a3a;
  --sidebar-hover: #2a3a50;
}

.sidebar {
  width: var(--sidebar-col);
  background: var(--sidebar-bg);
  display: flex; flex-direction: column;
  transition: width 0.22s ease;
  overflow: hidden; flex-shrink: 0; z-index: 100;
}
.sidebar:hover { width: var(--sidebar-exp); }

.nav-group { position: relative; }
.nav-item {
  display: flex; align-items: center; padding: 11px 16px; gap: 12px;
  cursor: pointer; border-left: 3px solid transparent;
  transition: background .15s, border-color .15s; white-space: nowrap;
}
.nav-item:hover { background: var(--sidebar-hover); }
.nav-group.active > .nav-item { background: rgba(26,115,232,.18); border-left-color: var(--blue); }

.nav-label { opacity: 0; transition: opacity .18s; }
.sidebar:hover .nav-label { opacity: 1; }

.nav-chevron { opacity: 0; transition: opacity .18s, transform .2s; }
.sidebar:hover .nav-chevron { opacity: 1; }
.nav-group.open > .nav-item .nav-chevron { transform: rotate(90deg); }

.nav-children {
  max-height: 0; overflow: hidden;
  transition: max-height .25s ease;
  background: rgba(0,0,0,.15);
}
/* Children only show once the group has been CLICKED open — no :hover trigger.
   Direct child (>) is deliberate: see the nesting gotcha in §6. */
.nav-group.open > .nav-children { max-height: 600px; }
/* And only once the sidebar itself is wide enough to show the labels */
.sidebar:not(:hover) .nav-children { max-height: 0 !important; }

.nav-child {
  display: flex; align-items: center; padding: 8px 16px 8px 48px; gap: 8px;
  cursor: pointer; font-size: 12.5px; color: rgba(255,255,255,.6);
}
.nav-child:hover { background: rgba(255,255,255,.06); color: #fff; }
```

> **Do not** add `.nav-group:hover .nav-children` or `.nav-group:hover > .nav-item
> .nav-chevron` rules — that's the old "every hover opens a folder" behavior this pattern
> intentionally removes (see the 2026-08-14 change to `newmasterx.blade.php`). Visibility is
> driven **only** by the `.open` class, toggled in JS.

### 3.3 Markup — one `.nav-group` per top-level menu item
```blade
@foreach ($menul0 as $menu0)
  <div class="nav-group {{ $menu0['Keterangan'] === $activeMenuName ? 'active open' : '' }}">
    <div class="nav-item"
      onclick="{{ count($menu0['child']) > 0
          ? 'toggleNavGroup(this)'
          : "window.location.href='" . $menu0->href . "'" }}">
      <span class="nav-icon">{!! /* icon dictionary, see §3.4 */ '' !!}</span>
      <span class="nav-label">{{ $menu0['Keterangan'] }}</span>
      @if (count($menu0['child']) > 0)
        <span class="nav-chevron">
          <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </span>
      @endif
    </div>

    @if (count($menu0['child']) > 0)
      <div class="nav-children">
        @foreach ($menu0['child'] as $menu1)
          <div class="nav-child" onclick="window.location.href='{{ $menu1->href }}'">
            {{ $menu1['Keterangan'] }}
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endforeach
```
Rules carried over from the purchasing migration:
- **A parent with children only toggles** (never navigates); **a parent with no children,
  and every child row, navigates** via `window.location.href`.
- **The chevron only renders for parents that have children.** A leaf parent (no
  `nav-children`) has nothing to expand, so it gets no chevron — don't render an empty
  `<span class="nav-chevron">` for it, and don't fall back to placeholder text (an earlier
  draft used a literal `x` glyph; always use the arrow SVG above).
- `$activeMenuName` comes from the route-matching `@php` block described in §5.1 — **do not**
  hardcode a module name here (e.g. `== 'Pengadaan'`) when the layout will be shared by more
  than one page/module.

### 3.4 Icon dictionary
`newmasterx.blade.php` keys a small inline-SVG map by `$menu0['Keterangan']` (`'Berkas'`,
`'Master'`, `'Accounting'`, `'Pengadaan'`, `'Marketing'`, `'Gudang'`, `'Report'`), falling
back to `''` for unknown labels. Copy that map verbatim — the `Keterangan` values come from
the same `DBMENU` rows regardless of which module's layout renders them, so the dictionary
is shared vocabulary, not purchasing-specific. Add entries here if a new top-level module
name shows up unstyled (blank icon).

### 3.5 JS — the accordion toggle
```js
function toggleNavGroup(itemEl) {
  var group = itemEl.closest(".nav-group");
  var wasOpen = group.classList.contains("open");
  document.querySelectorAll("#nav .nav-group.open").forEach(function (g) {
    if (g !== group) g.classList.remove("open");
  });
  group.classList.toggle("open", !wasOpen);
}
```
Put it next to the other tiny helpers (`numberWithCommas`, `format_date`, …) already in the
layout's `<script>` block. It closes any other open group and flips the clicked one — single
open folder at a time. Uses `closest()`/`querySelectorAll` on classes, not IDs, so it's
unaffected by `.nav-group` elements sharing an `id` (see gotcha in §6).

### 3.6 Header bar (breadcrumb / period / avatar)
```blade
<header class="header">
  <div class="breadcrumb" id="breadcrumb">
    <span onclick="...">Beranda</span> <span class="bc-sep">›</span>
    <span onclick="window.location.href='home<module>'">...</span> <span class="bc-sep">›</span>
    <b>Page Title</b>
  </div>
  <div class="header-right">
    <div class="period-badge">Periode: {{ ... }} {{ $periode->tahun }}</div>
    <div id="avatar" class="avatar">{{ \Auth::user()->username[0] }}</div>
  </div>
</header>
```
`$periode` comes from the same `GlobalController::getPeriode()` call every module already
uses — no new plumbing needed.

---

## 4. Data plumbing (unchanged)

Both the old and new layouts consume the exact same server-side shape — you are only
changing the Blade/CSS/JS, not the controller contract:

- `$menul0` — from `NewMenuController::getMenuL0($headermenu)`, each item has `->child`
  (array of `$menu1`) attached. The `$headermenu` int passed varies per module/controller
  (see `HomeController.php` for the per-module values already in use).
- `$menu0->href` / `$menu1->href` — route strings, used as-is.
- `$periode` — `{bulan, tahun}` from `GlobalController::getPeriode()`.

Nothing here needs to change when migrating a layout to this sidebar pattern.

---

## 5. Making it work for a layout shared by many pages

`newmasterx.blade.php` is `@extends`-ed by exactly **one** page today
(`pembelianpermintaannonagen.blade.php`), but the active-module and page-title logic is
already written to be **route-driven**, not hardcoded — copy it as-is into a shared layout
(e.g. `accounting/newmaster.blade.php`, extended by 13+ pages) rather than reinventing it.

### 5.1 Route-driven active module + page title
At the very top of the file, before `<!doctype html>`:
```blade
@php
    // Resolve which top-level menu group the current route belongs to, so the sidebar can
    // mark that group active/open and the breadcrumb can name the page without either
    // being hardcoded per page. Matches request()->path() against DBMENU's href column,
    // first on the top-level entries, then on their children. If the route isn't in the
    // menu tree both stay null: no group opens and the breadcrumb falls back to @section.
    $currentPath = trim(request()->path(), '/');
    $activeMenuName = null; // Keterangan of the matching top-level group
    $autoPageTitle = null;  // Keterangan of the matching page entry

    foreach ($menul0 as $m0) {
        if (strcasecmp(trim($m0->href ?? '', '/'), $currentPath) === 0) {
            $activeMenuName = $m0['Keterangan'];
            $autoPageTitle = $m0['Keterangan'];
            break;
        }
        foreach ($m0['child'] as $m1) {
            if (strcasecmp(trim($m1->href ?? '', '/'), $currentPath) === 0) {
                $activeMenuName = $m0['Keterangan'];
                $autoPageTitle = $m1['Keterangan'];
                break 2;
            }
        }
    }
@endphp
```
Then, instead of hardcoded literals:
```blade
<title>@yield('title', 'Accounting')</title>          {{-- was: <title>Purchasing</title> --}}
...
<div class="nav-group {{ $menu0['Keterangan'] === $activeMenuName ? 'active open' : '' }}">
...
<b>@yield('breadcrumb_title', $autoPageTitle)</b>      {{-- was a hardcoded page name --}}
```
`@yield(..., $autoPageTitle)` means: use the child page's `@section('breadcrumb_title', ...)`
if it set one, otherwise fall back to whatever `DBMENU.Keterangan` says for the matched
route — so **pages that set nothing still show a correct name**, and a page can still
override it (e.g. to show a friendlier label than the raw menu text) with:
```blade
@section('title', 'Neraca Lajur')
@section('breadcrumb_title', 'Neraca Lajur')
```
near its `@extends`. Same reasoning applies to `<title>` — pass a per-module fallback (the
module's own name) as the `@yield` default rather than leaving it blank.

Second header-matching caveat: the breadcrumb's *second* crumb (module name / "Purchasing" in
the reference file) and its `onclick="window.location.href='home<module>'"` are still
hand-written per layout, not derived from `$activeMenuName` — set that crumb's label/href to
match whichever module the layout belongs to.

### 5.2 Scope of impact
Changing `newmasterx.blade.php` only ever affected one page. **Editing
`accounting/newmaster.blade.php` directly affects every page that extends it** (13+ pages as
of this writing — reconfirm with `grep -rl "extends('accounting.newmaster')" resources/views`
before starting). Two safe ways to proceed, in order of preference:
1. **Preferred:** with §5.1's route-driven logic in place, edit `accounting/newmaster.blade.php`
   in place — one migration, all pages get the new sidebar together, each correctly showing
   its own active module/title with zero controller changes.
2. If you want to migrate incrementally: create `accounting/newmasterx.blade.php` as a new
   file (mirroring how `purchasing/newmasterx.blade.php` sits alongside the untouched
   `purchasing/newmaster.blade.php`), move pages over to `@extends('accounting.newmasterx')`
   one at a time, and delete the old layout once every page has moved.

Ask before picking — it changes the blast radius of the edit.

---

## 6. Known gotchas to carry over or fix

- **Close `.nav-group` INSIDE the `@foreach`.** This is the one that actually bit us.
  `newmasterx.blade.php` originally had the group's closing `</div>` placed *after*
  `@endforeach`, so every iteration opened a `<div class="nav-group">` that was never
  closed and the browser silently **nested each group inside the previous one**. Symptom:
  clicking one parent opens/closes all the others, because `.nav-group.open .nav-children`
  (descendant selector) then matched every nested group's children, and
  `closest('.nav-group')` resolved to the innermost group instead of the clicked one.
  Two defenses, apply both:
  1. Put `</div>` immediately after the `@if (count($menu0['child']) > 0) … @endif` block,
     still **inside** the `@foreach` — so the groups are siblings.
  2. Use the **direct-child** selector `.nav-group.open > .nav-children` (not a descendant
     space) so even accidental nesting can't leak the `.open` state downward.

  If you ever see "one click opens every folder", check the closing-div placement first —
  Blade won't warn you, and the rendered HTML looks fine until you inspect the DOM tree.
- **Don't put a fixed `id` on `.nav-group`.** The reference file used to carry a copy-pasted
  `id="berkas"` on every group (duplicate IDs across the page); it's been removed. Target
  groups via `closest('.nav-group')` or a `data-*` attribute instead.
- `@yield('buttons')` from the old second header bar (action buttons like Save/Print) has no
  direct equivalent in the new `.header` — decide where those buttons live in the new layout
  (likely inside `.content` / the page's own toolbar) before migrating a page that uses them.
- The new layout's `<style>` block is large and self-contained (no shared CSS file yet) —
  keep it inline like `newmasterx.blade.php` does, don't split it out mid-migration unless
  you're doing that for every sidebar layout at once.

---

## 7. Second reference: nested menus, `$akses`-shaped data, many pages

`resources/views/report/newmaster2x.blade.php` (+ its recursive partial
`resources/views/report/partials/sidebar-nav-node.blade.php`) is a second, more involved
application of this pattern, built as a **new sibling file** next to the untouched
`report/newmaster2.blade.php` (121 pages currently extend the old one — nothing was
switched over yet). Read it if the layout you're migrating has either of the two problems
below; skip this section for a simple 1-2 level, `$menul0`-at-top-scope case like purchasing.

### 7.1 Data comes wrapped in `$akses`, not top-level variables
Report controllers call `AksesTrait::cekAkses($href)` (not `GlobalController::getAkses`), which
returns everything already resolved server-side — **use it directly, don't rederive it**:
- `$akses['menul0']` — from `NewMenuController::getMenuL0Report()`, not `getMenuL0()`.
- `$akses['href']` — the *exact* route slug the controller passed to `cekAkses()` (e.g.
  `"reportaccountinghutangkartu"`), which already matches `DBMENUREPORT.href` — use this
  directly for path-matching instead of guessing from `request()->path()`.
- `$akses['namamenu']` — the current page's `Keterangan`, already looked up server-side.
  Use it as the `@yield('title'/'breadcrumb_title', ...)` fallback directly; no need to
  re-walk the menu tree to find it (unlike `$autoPageTitle` in the purchasing version).
- `$akses['periode']`, `$akses['program']`, `\Auth::user()->username` — same shape as
  before, just sourced from `$akses` instead of top-level `$periode`.

### 7.2 Menus deeper than 2 levels — recursive partial + per-level accordion
Some menu trees genuinely nest further than "group + flat children" (report menus go
`menu0 → menu1 → menu2 → menu3`, all real, all populated in the old dropdown nav — this
isn't dead/unused depth like purchasing's `.nav-subchild`). Two changes from the base
pattern:

1. **Render recursively**, not with N hand-nested `@foreach`s. One partial that includes
   itself per child, keyed on whether the node has children (renders a toggle `.nav-group`,
   indented per depth via inline `padding-left`) or not (renders a clickable leaf — `.nav-item`
   style at depth 0, `.nav-child` style deeper). See `sidebar-nav-node.blade.php`.
2. **Scope the accordion to siblings at the same level**, not globally. The flat
   `toggleNavGroup()` in §3.5 closes *every* `#nav .nav-group.open` — correct only when all
   groups are siblings under `#nav`. With real nesting, closing every open group anywhere in
   the tree would also collapse the ancestors you just opened to reach the clicked node. Use:
   ```js
   function toggleNavGroup(itemEl) {
     var group = itemEl.closest(".nav-group");
     var parent = group.parentElement; // #nav (top level) or a .nav-children (nested)
     var wasOpen = group.classList.contains("open");
     parent.querySelectorAll(":scope > .nav-group.open").forEach(function (g) {
       if (g !== group) g.classList.remove("open");
     });
     group.classList.toggle("open", !wasOpen);
   }
   ```
   `:scope > .nav-group.open` restricts the "close others" sweep to direct children of the
   clicked node's own parent container — siblings at that level close each other; ancestors
   and descendants elsewhere are untouched. This is a strict superset of the flat version
   (identical behavior when `parent` is `#nav`), so it's safe to use even for a 2-level layout.

**Gotcha: don't track the open-path by href.** Pure category-header nodes (a grouping label
with no real page of its own) often have a **blank href**, and more than one branch can have
a blank-href node with the same label (e.g. "Piutang" under both Accounting and Marketing).
Tracking the ancestor chain as an array of hrefs and checking membership with `in_array()`
lets *any* other blank-href node anywhere in the tree false-match and incorrectly render as
active/open. Track by **`KODEMENU`** instead — always present and unique per row (it's the
same column `NewMenuController` itself uses for hierarchy building via `substr()` prefix
matching) — and merge it into the path array as the recursive `$findPath` closure unwinds.
See the `@php` block at the top of `newmaster2x.blade.php` for the full implementation.

### 7.3 `@yield('buttons')` — dropped, not relocated
Unlike §6's open question, this was resolved for the report layout: **`@yield('buttons')` was
removed entirely**, matching the reference sidebar's header (breadcrumb + period + avatar,
no button slot). 31 of the 121 pages extending the old `newmaster2.blade.php` currently
define `@section('buttons')` — those buttons **will not appear** on any page moved to
`newmaster2x.blade.php` until that page is individually edited to render its buttons inside
its own `@yield('content')` toolbar instead. This is a per-page follow-up, not something the
layout migration itself can paper over.

### 7.4 Logout — a visible link, not a dropdown (superseded once, see §8.3)
The purchasing reference file's `.avatar` is decorative only — no click handler, no way to
log out from it. That's a real gap the base pattern doesn't cover (the old top-nav always had
a visible "Log Out" link). `newmaster2x.blade.php` first closed this gap with a click-to-open
avatar dropdown, then **switched to a plain always-visible link** once
`references/newmaster.blade.php` (§8) supplied an actual second-codebase reference to match:
```blade
<a class="logout-link" href="{{ url('logout') }}"><i class="bi bi-power"></i> Log Out</a>
```
Check your own logout route before assuming either form works — ours (`routes/web.php`) is an
**unnamed** `GET` route, so `url('logout')` is correct and `route('logout')` (which the other
codebase's reference uses) would throw here.

The purchasing reference (`newmasterx.blade.php`) still has this gap unfixed as of this
writing — worth porting back if/when that file is touched again.

### 7.5 Don't import page-content CSS classes into a shared layout
`newmasterx.blade.php`'s `<style>` block also defines generic-looking classes for its own
bespoke content (`.page-title`, `.card`, `.btn`, `.data-table`, `.badge`, `.summary-card`,
plus a blanket `* { margin:0; padding:0; box-sizing:border-box }` reset and a `body` font/color
override). None of that is specific to the sidebar — it's that one page's own content styling.
**Don't copy it into a layout meant for many existing pages.** Two concrete risks it would
create: (1) a page-wide reset landing *after* Bootstrap in the cascade would zero out
Bootstrap's own component spacing (buttons, modals, form-groups) across every page using the
layout; (2) generic class names like `.page-title`/`.avatar`/`.header` can collide with
identically-named classes already defined by page-specific stylesheets (e.g.
`report-table.css` already defines `.tb-report .page-title` / `.tb-report .avatar` — scoped
under `.tb-report` so it happened not to collide here, but check before assuming). Port only
the sidebar/nav/header/breadcrumb/avatar component rules (renamed/prefixed if there's any
name overlap with an already-loaded stylesheet), and leave existing page content CSS alone.

> **This risk became real, not hypothetical, in §8.** `public/css/newmaster.css` — the shared
> stylesheet `newmaster2x.blade.php` now links — carries exactly this same blanket reset and the
> same generic `.btn`/`.card`/`.badge`/`.data-table`/`.page-title` classes. It's tolerated for
> now only because the blast radius is one page; watch for regressions per §8.4 before this
> layout is rolled out further.

---

## 8. Third iteration: matching a second codebase's actual design (`newmaster.css` + flyouts)

After §7 shipped, the user supplied the *real* design source from another codebase (PT. SPL) —
`references/newmaster.blade.php` (the layout) + `public/css/newmaster.css` (its styling, now
copied into this repo). `newmaster2x.blade.php` and `sidebar-nav-node.blade.php` were rewritten
again to match it. This section documents what changed from §7 and why; §7.1–§7.3's guidance
(the `$akses` shape, `KODEMENU` path-tracking, dropped `@yield('buttons')`) is unaffected and
still applies as written.

**What this reference is not**: a `references/newmaster2.blade.php` was dropped in first and
turned out to be byte-for-byte identical to our own old top-nav `report/newmaster2.blade.php`
(no sidebar in it at all — its one `sidebar` hit is a commented-out placeholder). If you're
ever handed a "reference" file and it looks suspiciously like something already in this repo,
diff it before trusting it.

### 8.1 Shared external stylesheet, not a self-contained `<style>` block
Every earlier layout in this guide (`newmasterx.blade.php`, `newmaster2x.blade.php` before this
rewrite) inlined its entire sidebar design in its own `<head>`. The new reference instead ships
one shared file, `public/css/newmaster.css`, linked normally:
```blade
<link rel="stylesheet" href="{!! URL::asset('public/css/newmaster.css') !!}?v={{ @filemtime(base_path('public/css/newmaster.css')) ?: '1' }}">
```
It defines `.sidebar`/`.nav-group`/`.nav-item`/`.nav-icon`/`.nav-label`/`.nav-chevron`/
`.nav-children`/`.nav-child`, `.main`/`.header`/`.titleText`/`.period-badge`/`.avatar`/
`.logout-link`, plus `.card-grid`/`.card`/`.btn`/`.data-table`/`.badge`/`.summary-card` (unused
here — module-home/card-grid rendering wasn't ported, see §8.2). The `?v={{ @filemtime(...) }}`
cache-bust matches the existing convention already used for `report-table.css`/
`customize-table.css` in this same file.

**Asset path gotcha**: the reference's own Blade (and this repo's stray `resources/views/tes.blade.php`
scratch copy, superseded by this work) uses `asset('css/newmaster.css')`. This repo serves
assets via `URL::asset('public/css/…')` — copy the `public/` prefix, not the bare form, or it 404s.

### 8.2 Ported visuals + interactions only, not the client-side menu engine
The reference renders its *entire* sidebar in JavaScript from a `GET /getmenu/{n}` JSON endpoint
(`GetMenu()` controller method), and clicking a top-level module doesn't just expand it — it
replaces the content area with a client-rendered card grid of that module's pages
(`showModuleHome()`). **None of that was ported.** Neither the endpoint nor the `GetMenu()`
method exist in this codebase, and our server-side Blade rendering from `$akses['menul0']`
already correctly handles the report module's 4-level menu + active-path (§7.2) with zero new
backend work. Only the reference's **visual design and DOM interactions** were adopted:
sidebar/header markup and classes, the logo-click-goes-home behavior, the flyout hover
mechanism (§8.3 below), and the plain-title header (§8.4).

If a future task asks for the full port (JSON endpoints, client-rendered card-grid module
home), that's new backend work, not a CSS/Blade migration — scope and estimate it separately.

### 8.3 Deep levels: hover flyout, not further inline nesting
§7.2's recursive partial let a `.nav-group` nest arbitrarily deep, each level indenting further
inside the 240px rail. The reference instead caps inline nesting at **two** tiers — a top-level
`.nav-group` (module) and its direct `.nav-child` rows — and renders anything deeper as a
**flyout panel** that pops out beside the rail on hover, positioned with real
`getBoundingClientRect()` coordinates and flipped to stay on-screen near viewport edges.

This repo's report menus go one level deeper than the reference models (4 levels vs. its 3), so
the port makes a judgment call the reference doesn't need to: a depth-2 node that itself has
children (the real 4th level) is **flattened into the same flyout** as a captioned sub-list
(`.nav-flyout-caption` + indented `.nav-flyout-item`s) rather than opening a second nested
flyout. Revisit this if a 4-level flyout-in-flyout is ever actually requested — it isn't today.

**Why the flyout lives outside `<aside>`, in its own `#flyout-root` container appended near the
end of `<body>`**: the sidebar's own accordion depends on `overflow:hidden` + `max-height` on
`.nav-group`/`.nav-children` to animate open/closed. Any descendant of that subtree gets
clipped by it regardless of the descendant's own `position`/`z-index` — `position:fixed` doesn't
escape an `overflow:hidden` ancestor by itself. Living *outside* that subtree sidesteps the
problem entirely; `position:fixed` then positions relative to the viewport as normal, since
`body ` (or any of its other ancestors here) has no `transform`/`filter` that would establish a
new containing block for fixed-position descendants.

`sidebar-nav-node.blade.php` is only ever called with `depth: 0` or `depth: 1` now — it no
longer recurses into depth 2+. `newmaster2x.blade.php` renders the flyout panels itself, in a
second, separate loop over the same `$akses['menul0']` tree, keyed by each depth-1 node's
`KODEMENU` (`id="flyout-{{ $m1['KODEMENU'] }}"` on the panel, `data-flyout-id="flyout-..."` on
the matching `.nav-child.has-sub` row) — the two loops must stay in sync on that ID scheme.

The hover mechanics (`attachFlyoutHoverHandlers()` in `newmaster2x.blade.php`) are ported from
the reference near-verbatim: a `HIDE_DELAY` grace period so the cursor can travel from the row
to the panel without it closing, `flyout-pinned` on `#sidebar` (keeps the rail wide while a
flyout is open) and `flyout-owner` on the owning `.nav-group` (keeps it visually expanded),
synced by `syncSidebarPin()` on every show/hide.

### 8.4 Header: plain title, no breadcrumb chain
The reference's topbar is a single `.titleText` (page title only) plus period/avatar/logout on
the right — no "Beranda › Module › Page" breadcrumb chain. `newmaster2x.blade.php`'s header
was simplified to match:
```blade
<header class="header">
  <div class="titleText" id="breadcrumb">@yield('breadcrumb_title', $akses['namamenu'])</div>
  <div class="header-right">
    <div class="period-badge">Username: {{ \Auth::user()->username }} &ndash; Periode: {{ $akses['periode']->bulan }} / {{ $akses['periode']->tahun }}</div>
    <div class="avatar">{{ strtoupper(\Auth::user()->username[0]) }}</div>
    <a class="logout-link" href="{{ url('logout') }}"><i class="bi bi-power"></i> Log Out</a>
  </div>
</header>
```
`$activeMenuName`/`$activeMenuHref` (§5.1's route-driven breadcrumb-crumb variables) became
dead code once the crumb chain was dropped and were removed from the `@php` block — only
`$activePath` (still needed to mark the active `.nav-group` open) remains. If a future layout
in this family wants the breadcrumb chain back, that computation is easy to reintroduce; don't
resurrect the unused variables speculatively in the meantime.

Icon note: the old header's logout icon was `<i class="bi-power">` (missing the required base
`bi` class Bootstrap Icons needs — `bi bi-power` — so it silently rendered no icon at all). The
reference's markup has the correct two-class form; carry that forward, not the old broken one.

### 8.5 CSS specificity: overriding `newmaster.css`'s built-in hover behavior
`newmaster.css` (copied from the other codebase) still contains the exact hover-opens-everything
behavior this project removed twice already (§1, §7's own history):
```css
.nav-group.open > .nav-item .nav-chevron,
.nav-group:hover > .nav-item .nav-chevron { transform: rotate(90deg); ... }
.nav-group:hover .nav-children,
.nav-group.open .nav-children { max-height: 600px; }
```
Because `newmaster2x.blade.php` links this file rather than owning the rules outright, killing
the `:hover` half requires an override in the page's own `<style>` block — and that override
needs `!important`, not just later-in-source-wins: `.nav-group:hover > .nav-item .nav-chevron`
has **higher specificity** than a plain `.nav-group > .nav-item .nav-chevron` override (`:hover`
counts as a class-level selector), so an unqualified override would silently lose regardless of
load order. The working pattern (see `newmaster2x.blade.php`'s `<style>` block):
```css
.nav-children { max-height: 0 !important; }                          /* blanket-kill hover-open */
.sidebar:hover .nav-group.open > .nav-children { max-height: 2000px !important; } /* higher specificity, wins back over the blanket rule for the real click-open case */
```
The second rule needs higher specificity than the *first*, not than `newmaster.css` — both of
your own rules are `!important`, so among those two it's ordinary specificity that decides,
while `!important` alone is enough to beat `newmaster.css`'s non-important rules regardless of
selector weight. `newmaster.css`'s own `.sidebar:not(:hover) .nav-children { max-height:0 !important }`
guard (children only visible once the rail is actually wide) is untouched and still holds.

---

## 9. Fourth iteration: applying the §8 design to a bare-`$menul0` module (gudang)

`resources/views/gudang/newmasterx.blade.php` (+
`resources/views/gudang/partials/sidebar-nav-node.blade.php`) applies §8's design — shared
`newmaster.css`, click-accordion, hover flyouts, plain title, visible logout — to the gudang
module, built as a **new sibling file** next to the untouched `gudang/newmaster.blade.php` (22
pages currently extend the old one; nothing was switched over). This is the case where §7's
"is the data wrapped in `$akses`?" question answers **no** — gudang controllers pass bare
`$menul0`/`$periode`, the same shape purchasing's `newmasterx.blade.php` already consumes — so
this section is really "§5.1's route-driven `@php` block, redone with §8's markup/CSS/flyouts
instead of §3's inline-only sidebar." Read this section when your target layout has bare
top-level menu variables (not `$akses`) but still wants the flyout-based design.

### 9.1 Data shape differences from the report version

| | report (`newmaster2x.blade.php`, §8) | gudang (`newmasterx.blade.php`, this section) |
|---|---|---|
| menu source | `$akses['menul0']`, from `getMenuL0Report()` | bare `$menul0`, from `getMenuL0(6)` — every gudang controller passes `6` |
| depth | 4 levels (`menu0→menu1→menu2→menu3`) | 3 levels (`menu0→menu1→menu2`) |
| current page | `$akses['href']` — exact slug the controller already resolved | **`trim(request()->path(), '/')`**, same as §5.1 — gudang controllers don't hand the layout a pre-resolved href |
| header title | `$akses['namamenu']` | resolved by walking `$menul0` in the same `@php` block that builds `$activePath` (§5.1-style `$autoPageTitle`, folded into the recursive `$findPath` closure so the tree is only walked once) |
| periode | `$akses['periode']` | bare `$periode` |
| `@yield('buttons')` | dropped (§7.3) | **kept** — all 22 gudang pages already define it (empty today), so preserving the yield costs nothing and avoids §7.3's per-page cleanup debt. Rendered inside `.header-right`, before the period badge. |

`getMenuL0()` has the same "deepest level never gets `->child` assigned to itself" shape as
`getMenuL0Report()` (§7.2's gotcha) — it sets `->child` on menu0 and menu1 only. Every read
in the gudang files stays `count($node->child ?? [])`, same reasoning as before.

The hardcoded "Berkas → Set Periode" entry that lived inside gudang's old top navbar
(`gudang/newmaster.blade.php`, the literal `<li>` block before the `@foreach ($menul0 ...)`
loop) isn't a row in `$menul0` at all, so it doesn't come along for free — it's pinned in as a
static `.nav-group` above the `@foreach` in `newmasterx.blade.php`, the same way it was
hand-written before.

### 9.2 Keep the module's own asset/JS set — don't reuse another layout's list wholesale

Unlike §7/§8 (report layouts sharing report's own asset list), gudang's asset list, JS helper
functions (`formatAngka`, `format_date`, etc.), and Bootstrap JS pin are **not** identical to
report's — copying report's `<head>`/`<script>` list wholesale would silently swap gudang onto
the wrong Bootstrap bundle. Specifically: gudang's old layout pins
`public/js/bootstrap.bundle-4.6.2.min.js` (Popper 1.16.1 + Bootstrap JS bundled) to match its
`canvas/bootstrap.css` v4.5.0 — see `docs/bootstrap4-version-alignment-guide.md` — while
report's layout loads separate `popper.min.js` + `bootstrap.min.js` (v4.0.0). Only **add**
`newmaster.css` (last, so its `body{display:flex;height:100vh}` wins over
`canvas/style.css`) to gudang's existing list; don't replace the list.

### 9.3 `newmaster.css`'s content-kit classes collide with Bootstrap 4 — scope them

§7.5 flagged this as a hypothetical risk and §8's note said it "became real, not
hypothetical" for the report layout's Bootstrap modal buttons. Gudang makes the collision
impossible to defer any longer: `newmaster.css`'s demo-content kit defines `.btn`
(`border:none`, custom padding/radius), `.btn-primary`, **`.btn-danger`** (pale pink instead
of Bootstrap red), and **`.card`** (forces `display:flex`+centering+`cursor:pointer`) — gudang
pages use `.card` 24× and `.btn-danger` on ~20 pages for real Bootstrap components, not the
reference's demo cards.

Fixed by scoping `newmaster.css`'s content-kit rules (`.page-title`, `.card*`, `.c-*`,
`.subpage`, `.toolbar`, `.btn*`, `.data-table*`, `.badge*`, `.summary-*`, `.grid-view`,
`.list-view`) under an opt-in `.nm-ui` ancestor class — they now only apply inside a container
someone deliberately marks `.nm-ui` to build a page using that demo kit, which nothing in this
repo does yet. Shell rules (`.sidebar*`, `.nav-*`, `.main`, `.header`, `.titleText`,
`.period-badge`, `.avatar`, `.logout-link`, `.content`, `:root` vars, the `*` reset, `body`)
stay global — those are what every layout linking this file actually needs. This also
retroactively fixes the report layout's masterreport2x modal-button risk noted in §8/§7.5,
since nothing there uses `.nm-ui` either.

If you ever want to actually use the card-grid/toolbar/data-table kit for a real page, wrap
that page's markup in `<div class="nm-ui">...</div>` rather than un-scoping the rules globally.

### 9.4 Flyout depth: one tier, no 4th-level flatten

Gudang only nests 3 levels deep (vs. report's 4), so `#flyout-root` in
`gudang/newmasterx.blade.php` is a single loop — `.nav-child.has-sub` rows (depth 1, with
children) get a flyout of plain `.nav-flyout-item` rows (depth 2, the deepest level). There's
no depth-2-node-with-its-own-children case to flatten into a captioned sub-list like §8.3's
report-specific 4th-level handling — if gudang's menu tree ever grows a 4th level, revisit
that section before assuming the flat loop still covers it.

---

## 10. Verification checklist

1. Sidebar starts icon-only; hovering it widens to show labels (independent of any folder
   being open).
2. No folder's children appear just from hovering over the collapsed rail.
3. Clicking a parent **with children** shows an arrow chevron that rotates 90° when
   expanded; a parent with **no** children shows no chevron at all. Clicking a different
   parent closes the first and opens the new one (only one open at a time). Verify the
   **other** parents are genuinely unaffected — if they open/close in sympathy, the
   `.nav-group` divs are nested (see §6).
4. Clicking a parent with **no** children, or any child row, navigates immediately.
5. On page load, the module the current page actually belongs to starts expanded — driven by
   the `$activeMenuName` route match, not a hardcoded module name. Check this on **at least
   two different pages/modules** if the layout is shared.
6. Breadcrumb and `<title>` show the correct per-page text on every page extending the
   layout: pages with an explicit `@section('breadcrumb_title', ...)` show that, pages
   without one still show the right name via the route-match fallback (not blank, not
   another page's leftover text).
7. Period badge and avatar render as before. **Note:** what "as before" means for logout
   changed across this doc's own history — §5/§7-era layouts used an avatar click-to-open
   dropdown; §8's reference-matched layout (`newmaster2x.blade.php` as of §8) uses a plain
   always-visible `Log Out` link instead. Verify whichever your layout actually implements,
   and that it actually logs out (`url('logout')`, not `route('logout')`, unless your logout
   route is named).

For a **nested** layout using the §7 recursive-partial pattern (inline accordion all the way
down, no flyouts), additionally:

8. A level-2/3 node with its own children behaves exactly like a top-level parent: click to
   expand, chevron rotates, only one sibling *at that level* is open at a time. Opening a
   deeply-nested group must **not** collapse the ancestor groups you opened to reach it.
9. Two branches with same-named-but-blank-href category headers (e.g. "Piutang" under two
   different top-level modules) don't cross-activate — only the branch actually containing
   the current page opens.

For a layout using the §8 flyout pattern (depth 2+ pops out beside the rail instead of
nesting further inline — this is what `newmaster2x.blade.php` does today), additionally:

10. Hovering a `.nav-child.has-sub` row opens its flyout beside the rail, **not clipped** by
    the sidebar's own accordion. Moving the cursor from the row into the panel keeps it open
    (the `HIDE_DELAY` grace period); moving away from both closes it shortly after.
11. Only one flyout is visible at a time; opening a new one closes whichever was previously
    open. The sidebar itself (`#sidebar.flyout-pinned`) and the owning `.nav-group`
    (`.flyout-owner`) both stay visually expanded/wide while a flyout is open, even though
    the cursor has left the rail to reach the panel.
12. The panel repositions to stay on-screen near the right/bottom viewport edges rather than
    overflowing off-screen.
13. A depth-2 node that itself has children (the real 4th menu level) renders as a captioned
    sub-list *inside the same flyout* — confirm it doesn't try to open a second nested flyout.
14. The chevron-rotate and children-reveal overrides actually beat `newmaster.css`'s built-in
    `:hover` rules (§8.5) — moving down the collapsed rail must **not** flash any folder open;
    only an explicit click does. If this regresses, it's almost always a missing `!important`
    or a specificity mismatch against `newmaster.css`, not a logic bug in `toggleNavGroup()`.

For any layout: pages that used to show `@section('buttons')` content under the old layout:
confirm whether the migration decided to drop that yield (as it did for the report layout) or
keep it — if dropped, those buttons are expected to be missing until the page itself is
updated to render them inside its own content.
