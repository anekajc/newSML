{{--
    Sidebar node — renders one DBMENUREPORT entry, called only for depth 0 (top-level
    module) and depth 1 (its direct child). Deeper levels (real depth 2/3 — report
    menus nest up to 4 levels) are NOT rendered here: newmaster2x.blade.php renders
    them separately into #flyout-root, a panel positioned outside <aside> so the
    sidebar's own overflow:hidden/max-height accordion can't clip them. A depth-1 node
    with children gets marked .has-sub and opens its flyout on hover instead of
    recursing further — see docs/sidebar-navigation-migration.md §8.

    depth 0 with children  -> toggle-capable .nav-group (icon + label + chevron),
                               recurses into its own children at depth 1
    depth 0, no children   -> single clickable .nav-item row (module with no pages)
    depth 1 with children  -> .nav-child.has-sub, hover opens its #flyout-{KODEMENU}
                               panel (built separately); does NOT recurse or navigate
    depth 1, no children   -> plain clickable .nav-child

    Expected variables:
    - $node        the menu row (has ->href, ->child, ['Keterangan'], ['KODEMENU'])
                   -- ->child specifically: NewMenuController::getMenuL0Report() only
                   assigns it to menu0/menu1/menu2; the deepest level (menu3) is pushed
                   into its parent's child array but never gets ->child set on itself.
                   Always read it as `$node->child ?? []` — a bare `$node->child` on a
                   menu3 node returns null (Eloquent's magic __get), and count(null) is
                   a fatal TypeError under PHP 8. Missing child = "no children" here,
                   which is correct anyway since menu3 is the deepest level.
    - $depth       0 or 1 (see above — this partial is never called at depth 2+)
    - $activePath  array of ancestor KODEMENU values from the top-level module down to
                   (not including) the current page — groups on this path start open
    - $currentHref trimmed href of the page currently being viewed, for leaf highlight
    - $iconMap     ['Keterangan' => svg markup], only used at depth 0
    - $hasLeafDescendant  recursive closure: does this node (or something under it)
                   have a real, non-"#" href? Rows that fail this check are skipped
                   entirely (not rendered even as a disabled label) — the same rule,
                   same name, as the Report card grid's own hasLeafDescendant(), kept
                   in sync so the sidebar and the Report page always show the same
                   reachable set. Callers (newmaster2x.blade.php) must pre-filter with
                   it before @include'ing a top-level node; this partial applies it
                   itself when recursing into $node->child.
--}}
@php
    $visibleChildren = array_values(array_filter($node->child ?? [], fn ($c) => $hasLeafDescendant($c)));
    $hasChildren = count($visibleChildren) > 0;
    $nodeHref = trim($node->href ?? '', '/');
    // Matched by KODEMENU (unique per row), not href — several category-header nodes
    // can share a blank href, and href-based matching would let those collide.
    $isOnPath = in_array($node['KODEMENU'], $activePath, true);
    $isCurrent = $nodeHref !== '' && strcasecmp($nodeHref, $currentHref) === 0;

    // DBMENUREPORT.href holds a BARE route slug ("reportaccountinghutangkartu"), never
    // an absolute path. Resolution now happens client-side via the page's goTo()
    // JS helper (the same one the report-card grid uses) instead of baking a
    // pre-resolved url() into the markup here — one JS-side implementation of the
    // "prefix with the app base URL, strip a leading slash" rule instead of two.
    // rawurlencode(), not urlencode(): goTo() decodes with decodeURIComponent() on
    // the JS side, and rawurlencode's %20-for-space (vs urlencode's '+') is the
    // encoding decodeURIComponent expects.
    //
    // 238 DBMENUREPORT rows use "#" as a placeholder href (category headers, plus a
    // number of childless rows that therefore render as leaves here). Keep those
    // non-clickable — null $navHrefEncoded means the onclick attribute is omitted
    // entirely, same guard as before.
    $navHrefEncoded = ($nodeHref !== '' && $nodeHref !== '#') ? rawurlencode($nodeHref) : null;
@endphp

@if ($depth === 0)
    @if ($hasChildren)
        <div class="nav-group {{ $isOnPath ? 'active open' : '' }}">
            <div class="nav-item" onclick="toggleNavGroup(this)">
                <span class="nav-icon">{!! $iconMap[$node['Keterangan']] ?? '' !!}</span>
                <span class="nav-label">{{ $node['Keterangan'] }}</span>
                <span class="nav-chevron">
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </span>
            </div>
            <div class="nav-children">
                @foreach ($visibleChildren as $child)
                    @include('report.partials.sidebar-nav-node', [
                        'node' => $child,
                        'depth' => 1,
                        'activePath' => $activePath,
                        'currentHref' => $currentHref,
                        'iconMap' => $iconMap,
                        'hasLeafDescendant' => $hasLeafDescendant,
                    ])
                @endforeach
            </div>
        </div>
    @else
        {{-- Top-level entry with no children: a single clickable row, no chevron/children. --}}
        <div class="nav-group {{ $isCurrent ? 'active' : '' }}">
            <div class="nav-item" @if ($navHrefEncoded) onclick="goTo('{{ $navHrefEncoded }}')" @endif>
                <span class="nav-icon">{!! $iconMap[$node['Keterangan']] ?? '' !!}</span>
                <span class="nav-label">{{ $node['Keterangan'] }}</span>
            </div>
        </div>
    @endif
@else
    @if ($hasChildren)
        {{-- Real depth 2/3 content is rendered into #flyout-root, keyed by this row's
             own KODEMENU; hover opens it (attachFlyoutHoverHandlers() in
             newmaster2x.blade.php). No onclick navigation — this row is a folder. --}}
        <div class="nav-child has-sub" data-flyout-id="flyout-{{ $node['KODEMENU'] }}">
            <span class="nav-child-label">{{ $node['Keterangan'] }}</span>
            <span class="nav-child-arrow">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        </div>
    @else
        <div class="nav-child {{ $isCurrent ? 'active-child' : '' }}"
            @if ($navHrefEncoded) onclick="goTo('{{ $navHrefEncoded }}')" @endif>
            {{ $node['Keterangan'] }}
        </div>
    @endif
@endif
