# Bootstrap Version Standard (4.6.2 JS)

## The standard

**Bootstrap JavaScript in this app is `public/js/bootstrap.bundle-4.6.2.min.js`** — Bootstrap
v4.6.2 with Popper 1.16.1 built in, one file, one `<script>` tag.

```blade
<script src="{!! URL::asset('public/js/bootstrap.bundle-4.6.2.min.js') !!}"></script>
```

That single tag replaces **both** of the legacy tags:

```blade
{{-- legacy — do not use in new layouts --}}
<script src="{!! URL::asset('public/js/popper.min.js') !!}"></script>     {{-- Popper ~1.14 --}}
<script src="{!! URL::asset('public/js/bootstrap.min.js') !!}"></script>  {{-- Bootstrap 4.0.0 --}}
```

Using the *bundle* is part of the standard, not an incidental detail: it makes it impossible to
pair a mismatched Popper with Bootstrap, which is the failure mode the two-tag form invites.

### Scope: JS only, for now

The standard covers **JavaScript**. It does **not** yet cover CSS — layouts stay on whichever
Bootstrap stylesheet they already load (`canvas/bootstrap.css` v4.5.0 or
`css/bootstrap.min.css` v4.0.0). Pairing 4.6.2 JS with 4.5.0 or 4.0.0 CSS is safe: the 4.x line
introduced no breaking markup or plugin-option changes that this app's usage touches (verified
specifically for the tooltip `boundary`/`container` options used throughout the gudang module —
present in 4.0.0 and 4.6.2 alike).

A CSS alignment would be a separate, larger job — see [CSS: the remaining gap](#css-the-remaining-gap)
below.

### When the standard applies

| Situation | What to do |
| --- | --- |
| Writing a **new** layout | Use the 4.6.2 bundle. Non-negotiable. |
| **Already editing** an existing layout for another reason | Migrate it while you're in there (recipe below), then smoke-test. |
| Existing layout you aren't otherwise touching | Leave it. Do **not** run a standalone sweep. |

This is deliberately opportunistic. There is no automated test coverage for views (`tests/` holds
only stub examples), so every migration costs a manual smoke-test pass. Converting all ~44
remaining layouts at once would mean ~44 manual passes with nothing to catch a regression.

## Reference implementation

**`resources/views/gudang/newmasterx.blade.php`** is the canonical example — copy its script
block when starting a new layout.

- Line ~90: `public/css/canvas/bootstrap.css` (v4.5.0 CSS)
- Lines ~349-353: the pinned 4.6.2 bundle, with an explanatory comment above it
- Consumed by `gudang/pemakaianbarang.blade.php` and `gudang/permintaanpemakaian.blade.php`

`gudang/newmaster.blade.php` (line ~424) is the other migrated layout, and is consumed by 21
views — it is the larger real-world proof that the bundle swap is safe.

Keep the explanatory comment when you copy the tag. It stops the next person from "helpfully"
reverting the layout to the shared `bootstrap.min.js` for consistency:

```blade
{{-- Pinned to Bootstrap 4.6.2 (bundle = Popper 1.16.1 + Bootstrap JS). This layout is
     intentionally NOT on the shared public/js/bootstrap.min.js (v4.0.0) — see
     docs/bootstrap4-version-alignment-guide.md before touching this. --}}
<script src="{!! URL::asset('public/js/bootstrap.bundle-4.6.2.min.js') !!}"></script>
```

## Current state

Counts below are **live layouts only** — the dated backup copies described in `CLAUDE.md`
(`report1705/`, `*backup*`, `*Old*`, parenthesised-date directories) are excluded.

**Bootstrap JS**

| File | Version | Live layouts |
| --- | --- | --- |
| `public/js/bootstrap.bundle-4.6.2.min.js` | **4.6.2** (standard) | 2 |
| `public/js/bootstrap.min.js` + `public/js/popper.min.js` | 4.0.0 | 44 |

**Bootstrap CSS** — split across two versions, unchanged by this standard:

| File | Version | Live layouts |
| --- | --- | --- |
| `public/css/bootstrap.min.css` | 4.0.0 | 31 |
| `public/css/canvas/bootstrap.css` | 4.5.0 | 23 |

(Some layouts load more than one stylesheet, so the counts overlap.)

The source for the bundle is `node_modules/bootstrap`, which vendors **4.6.2** — so the standard
matches what npm already installs, and no unvendored asset is involved. Note `package.json` still
declares `"bootstrap": "^4.0.0"`; the caret resolves to 4.6.2 today, but if you want the standard
pinned at the dependency level too, tighten that constraint.

## The hard rule: never edit the shared JS files in place

`public/js/bootstrap.min.js` and `public/js/popper.min.js` are shared by **44 live layouts**.
Overwriting their contents with 4.6.2 would silently upgrade every one of them at once — 44
untested layouts, no rollback granularity, and any breakage would surface in production as a
mystery.

Always add a **new version-suffixed file** and repoint **one layout at a time**.

## Migration recipe

Worked against `gudang/newmaster.blade.php` and `gudang/newmasterx.blade.php`.

1. **Confirm what the layout currently loads.**
   ```
   grep -nE "bootstrap|popper" resources/views/<module>/<layout>.blade.php
   ```
   Confirm you are on the *live* file, not a dated backup copy (see `CLAUDE.md`).

2. **Make sure the bundle exists.** It is already committed at
   `public/js/bootstrap.bundle-4.6.2.min.js`. If it ever goes missing, re-vendor it:
   ```
   cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js public/js/bootstrap.bundle-4.6.2.min.js
   ```
   Use `bootstrap.bundle.min.js`, not the plain `bootstrap.min.js` — the bundle is the one with
   Popper inside.

3. **Replace the two legacy tags with the one standard tag** in that single layout, keeping the
   explanatory comment. Leave every other layout untouched.

4. **Smoke-test every interactive Bootstrap surface** on a few pages that use the layout. There is
   no automated coverage; this is a manual pass. At minimum:
   - modals open **and close** — including stacked modals, since `newmaster.blade.php`'s
     `hidden.bs.modal` handler and `backdrop:'static'` config depend on those events firing
   - tabs (`data-toggle="tab"`)
   - tooltips (check `boundary`/`container` options still behave)
   - dropdowns

5. **Note the jQuery ordering trap** before you debug anything odd — see the traps section; the
   app runs on jQuery 3.3.1, not the newer copies it also ships.

## CSS: the remaining gap

Tracked here so it isn't rediscovered from scratch. **Not currently scheduled.**

Two Bootstrap stylesheets are in play (4.0.0 and 4.5.0), neither matching the 4.6.2 JS standard.
Aligning them would mean vendoring `node_modules/bootstrap/dist/css/bootstrap.min.css` (4.6.2) to
a version-suffixed name and repointing layouts one at a time — same discipline as the JS
migration, same never-overwrite-in-place rule.

Why it hasn't been done:

- `public/css/canvas/bootstrap.css` (10,292 lines) is loaded by 23 live layouts. It appears to be
  stock 4.5.0 rather than a customised Canvas-theme build, but that has **not** been diffed
  against upstream — verify before assuming a drop-in swap is safe.
- A CSS version bump has a real visual diff, unlike the JS bump. It needs a look-at-every-page
  pass, not just an interaction smoke-test.
- The skew is a hygiene problem, not a live bug. Nothing is known to be broken by it.

## Exception: the report layouts also load Bootstrap 5

Out of scope for this standard, documented so it isn't mistaken for something to "fix" casually.

Seven live report layouts load **Bootstrap 5.3.3 from the jsDelivr CDN** on top of the BS4 their
parent layout already loads:

`report/masterreport2.blade.php`, `masterreport2x`, `masterreport3`, `masterreport4`,
`masterreport5`, `masterreportGudang`, `masterreportNeraca`

Each of them `@extends` `report/newmaster2.blade.php` or `report/newmaster2x.blade.php`, which
load `canvas/bootstrap.css` (4.5.0) plus `popper.min.js` + `bootstrap.min.js` (4.0.0).

**Which version actually wins:** Blade's `@extends` renders the parent *after* the child's stray
markup, so the emitted HTML puts the BS5 CDN tags first and the parent's BS4 tags second. Later
wins in both cases — **BS4 CSS overrides BS5 CSS at equal specificity, and BS4's jQuery plugins
are registered last**. These pages genuinely run on Bootstrap 4; the BS5 CDN payload is
render-blocking dead weight.

You can see the consequence in the markup — e.g. `report/reportmarketingso.blade.php` hedges its
modal dismiss buttons with both `data-dismiss` (BS4) and `data-bs-dismiss` (BS5), and uses the
BS5-only `btn-close` class which no loaded stylesheet defines. The `#modalFilter.rt-filter` rules
in `public/css/report-table.css` are what actually style it.

Removing the BS5 CDN tags should be a no-op visually, but those layouts are shared across many
report pages — treat it as its own change with its own test pass, not a drive-by. See also
`docs/new-design-all-report-guide.md` (the "Class A pages" discussion) and `docs/report-table-guide.md`.

**A different case: a page on a BS5-*only* layout** (no BS4 fallback at all — e.g. the root
`resources/views/newmaster.blade.php`, which loads only the Bootstrap 5 CDN and a jQuery→
`bootstrap.Modal` shim, unlike the seven report layouts above which still run on BS4 underneath).
Bringing the gudang-style `report-table.css`/`report-table.js` system (see
`docs/new-design-gudang-style-guide.md`) to a page like that needs real BS4, not just "BS4 wins the
cascade" — that system's modals assume `data-dismiss`/`$.fn.modal` are owned by BS4. Prefer
retargeting the page to a sibling layout in the same module that's already BS4 (check what its
neighbors extend first) over stripping BS5 out of a layout shared by many unrelated pages. Same
rule either way: never edit a shared layout's Bootstrap tags as a side effect of an unrelated
styling task — ask first, state the blast radius.

## BS5 → BS4 class name translation table

These class names appear in some blades (copy-pasted from BS5-era snippets or AI output) but are
**undefined** by any stylesheet the page effectively runs on — they are not a conflict, they are
dead weight that silently does nothing (e.g. a label meant to be bold just... isn't).

| BS5 (undefined here) | BS4 equivalent | Notes |
| --- | --- | --- |
| `fw-bold` | `font-weight-bold` | |
| `fw-normal` | `font-weight-normal` | |
| `text-end` | `text-right` | |
| `text-start` | `text-left` | |
| `ms-{0-5}` | `ml-{0-5}` | margin-start → margin-left |
| `me-{0-5}` | `mr-{0-5}` | margin-end → margin-right |
| `ps-{0-5}` | `pl-{0-5}` | padding-start → padding-left |
| `pe-{0-5}` | `pr-{0-5}` | padding-end → padding-right |
| `rounded-end` | `rounded-right` | |
| `rounded-start` | `rounded-left` | |
| `form-select` | `custom-select`, or just drop it if the field already has `form-control` and no BS5-specific look is needed | Don't add `custom-select` reflexively — check whether the current appearance is already what's wanted before changing it |
| `form-label` | (no BS4 equivalent needed — `col-form-label` or nothing) | |
| `btn-close` | `close` | also needs the `&times;`/`<span aria-hidden>` inner markup BS4's `.close` expects |
| `data-bs-*` | `data-*` | e.g. `data-bs-toggle` → `data-toggle`, `data-bs-dismiss` → `data-dismiss` |

Audit any blade with:
```
grep -nE "\bfw-(bold|normal)\b|\btext-(end|start)\b|\b(ms|me|ps|pe)-[0-5]\b|rounded-(end|start)|form-select\b|form-label\b|btn-close\b|data-bs-" resources/views/<file>.blade.php
```

## Known latent traps

Found while auditing `gudang/permintaanpemakaian.blade.php`. None were changed as part of the JS
alignment — they're separate issues with larger blast radius.

- **`report-table.css:72`** — `.tb-report * { margin:0; padding:0 }` is a *descendant* reset, and
  `report-table.css` loads **after** `canvas/bootstrap.css`. Any Bootstrap spacing utility used on
  a descendant of a `.tb-report` wrapper loses the specificity tie and is silently zeroed. It
  currently only wraps a bare `<table>`, so it's safe today — but it's easy to break by adding a
  styled child.
- **`report-table.css` unscoped `:root` blocks** — four of them, defining `--sp-*`, `--rt-*`, and
  generic names like `--bg`/`--white`/`--text`/`--border`/`--radius`/`--muted`, plus a couple of
  unscoped bare selectors (`.checkmark-red`, `.neg`). `--white` collides by name with Bootstrap
  4's own `:root --white`. Both resolve to `#fff` today so nothing visibly breaks, but a future
  edit to either side could silently pick up the wrong value. Worth namespacing (`--rt-white`)
  next time that file is touched for another reason.
- **Triple jQuery load** — the gudang layouts (and others) load `public/js/canvas/jquery.js`
  (3.5.1), then `public/js/jquery.min.js`, then `public/js/jquery-3.3.1.min.js`, in that order.
  The last tag wins, so the app actually runs on jQuery **3.3.1** — the oldest of the three —
  while shipping the other two copies dead. Keep this in mind when a jQuery API seems missing.
- **`public/css/semantic.css` is misnamed** — it's AlertifyJS CSS (89 lines), not Semantic UI.
  `public/css/alertify.css` is then loaded again a few lines later in the same layout — a
  duplicate include, not a second library.
- **`public/css/canvas/dark.css`** (2,126 lines) is scoped entirely under a `.dark` ancestor
  class. `<body>` in these layouts only ever has `class="stretched"`, never `.dark`, so the whole
  file is inert. Safe to leave; not worth removing without a reason to touch that layout.
