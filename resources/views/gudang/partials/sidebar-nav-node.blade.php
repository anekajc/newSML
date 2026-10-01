{{--
    Sidebar node — renders one DBMENU entry (via NewMenuController::getMenuL0(6)), called
    only for depth 0 (top-level module) and depth 1 (its direct child). Deeper levels (real
    depth 2 — gudang menus nest up to 3 levels) are NOT rendered here:
    gudang/newmasterx.blade.php renders them separately into #flyout-root, a panel
    positioned outside <aside> so the sidebar's own overflow:hidden/max-height accordion
    can't clip them. A depth-1 node with children gets marked .has-sub and opens its
    flyout on hover instead of recursing further. Gudang copy of
    report/partials/sidebar-nav-node.blade.php — see docs/sidebar-navigation-migration.md §9.

    depth 0 with children  -> toggle-capable .nav-group (icon + label + chevron),
                               recurses into its own children at depth 1
    depth 0, no children   -> single clickable .nav-item row (module with no pages)
    depth 1 with children  -> .nav-child.has-sub, hover opens its #flyout-{KODEMENU}
                               panel (built separately); does NOT recurse or navigate
    depth 1, no children   -> plain clickable .nav-child

    Expected variables:
    - $node        the menu row (has ->href, ->child, ['Keterangan'], ['KODEMENU'])
                   -- ->child specifically: NewMenuController::getMenuL0() only assigns
                   it to menu0/menu1; the deepest level (menu2) is pushed into its
                   parent's child array but never gets ->child set on itself. Always
                   read it as `$node->child ?? []` — a bare `$node->child` on a menu2
                   node returns null (Eloquent's magic __get), and count(null) is a
                   fatal TypeError under PHP 8. Missing child = "no children" here,
                   which is correct anyway since menu2 is the deepest level.
    - $depth       0 or 1 (see above — this partial is never called at depth 2+)
    - $activePath  array of ancestor KODEMENU values from the top-level module down to
                   (not including) the current page — groups on this path start open
    - $currentHref trimmed href of the page currently being viewed, for leaf highlight
    - $iconMap     ['Keterangan' => svg markup], only used at depth 0
--}}
@php
    $hasChildren = count($node->child ?? []) > 0;
    $nodeHref = trim($node->href ?? '', '/');
    // Matched by KODEMENU (unique per row), not href — several category-header nodes
    // can share a blank href, and href-based matching would let those collide.
    $isOnPath = in_array($node['KODEMENU'], $activePath, true);
    $isCurrent = $nodeHref !== '' && strcasecmp($nodeHref, $currentHref) === 0;
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
                @foreach ($node->child as $child)
                    @include('gudang.partials.sidebar-nav-node', [
                        'node' => $child,
                        'depth' => 1,
                        'activePath' => $activePath,
                        'currentHref' => $currentHref,
                        'iconMap' => $iconMap,
                    ])
                @endforeach
            </div>
        </div>
    @else
        {{-- Top-level entry with no children: a single clickable row, no chevron/children. --}}
        <div class="nav-group {{ $isCurrent ? 'active' : '' }}">
            <div class="nav-item" onclick="window.location.href='{{ url($node->href ?? '') }}'">
                <span class="nav-icon">{!! $iconMap[$node['Keterangan']] ?? '' !!}</span>
                <span class="nav-label">{{ $node['Keterangan'] }}</span>
            </div>
        </div>
    @endif
@else
    @if ($hasChildren)
        {{-- Real depth 2 content is rendered into #flyout-root, keyed by this row's
             own KODEMENU; hover opens it (attachFlyoutHoverHandlers() in
             newmasterx.blade.php). No onclick navigation — this row is a folder. --}}
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
            onclick="window.location.href='{{ url($node->href ?? '') }}'">
            {{ $node['Keterangan'] }}
        </div>
    @endif
@endif
