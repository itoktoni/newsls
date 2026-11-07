@props(['empty' => 'No data found.'])
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden form-card">
    <div class="p-0">
        <div class="hidden lg:block max-w-full table-scroll" data-twrap>
            <table class="w-max min-w-full border-collapse">
                <thead>
                    <tr class="border-b-2 border-outline-variant bg-surface-container">
                        {{ $head }}
                    </tr>
                </thead>
                <tbody class="[&>tr]:border-b [&>tr]:border-outline-variant/50 [&>tr:last-child]:border-b-0 [&>tr]:hover:bg-surface-container-low/50">
                    {{ $body }}
                </tbody>
            </table>
        </div>
        <div class="table-hbar bg-surface-container-lowest border-t border-outline-variant on" data-hbar><div data-hbar-inner></div></div>

        @if(isset($mobile))
        <div class="lg:hidden">{{ $mobile }}</div>
        @endif
    </div>
</div>
<style>
    .table-scroll {
        overflow-x: scroll;
        overflow-y: visible;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior-x: contain;
    }
    .table-scroll::-webkit-scrollbar {
        display: none;
    }
    .table-scroll table {
        width: max-content;
        min-width: 100%;
    }
    thead tr th {
        padding: 0.875rem 1.25rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #191c1e;
        background: #f2f4f6;
        border-bottom: 2px solid #c4c5d5;
    }
    tbody tr td {
        padding: 0.75rem 1.25rem;
        text-align: left;
        vertical-align: middle;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: #191c1e;
        border-bottom: 1px solid #eceef0;
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    .table-hbar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: calc(4rem + 48px);
        z-index: 44;
        height: 16px;
        overflow-x: scroll;
        overflow-y: hidden;
        scrollbar-width: thin;
    }
    .table-hbar > div {
        height: 1px;
    }
    .table-hbar.on {
        display: block;
    }
    @media (min-width: 768px) {
        .table-hbar {
            bottom: 64px;
        }
    }
    @media (min-width: 1024px) {
        .table-hbar {
            left: 18rem;
        }
    }
    @media (max-width: 1023.98px) {
        .table-hbar.on {
            display: none;
        }
    }
    /* Tanpa action bar (mis. warehouse): footer scrollbar nempel paling
       bawah viewport, bukan melayang. Dengan action bar: tetap di atasnya. */
    body:not(:has(.action-bar)) .table-hbar.on {
        bottom: 4rem;
    }
    @media (min-width: 768px) {
        body:not(:has(.action-bar)) .table-hbar.on {
            bottom: 0;
        }
    }
</style>
<script>
    (function () {
        function pairs() {
            var out = [];
            document.querySelectorAll('[data-twrap]').forEach(function (wrap) {
                var root = wrap.closest('.form-card') || wrap.parentElement;
                var bar = root ? root.querySelector('[data-hbar]') : null;
                var inner = bar ? bar.querySelector('[data-hbar-inner]') : null;
                if (wrap && bar && inner) out.push({ root: root, wrap: wrap, bar: bar, inner: inner });
            });
            return out;
        }
        // Samakan lebar isi bar dengan lebar tabel. Tanpa logika tampil/
        // sembunyi — bar selalu ada seperti action bar.
        function syncWidth() {
            pairs().forEach(function (p) {
                var w = p.wrap.scrollWidth + 'px';
                if (p.inner.style.width !== w) p.inner.style.width = w;
            });
        }
        function wire() {
            pairs().forEach(function (p) {
                if (p.root.dataset.hwired) return;
                p.root.dataset.hwired = '1';
                p.bar.addEventListener('scroll', function () {
                    if (p.wrap.scrollLeft !== p.bar.scrollLeft) p.wrap.scrollLeft = p.bar.scrollLeft;
                }, { passive: true });
                p.wrap.addEventListener('scroll', function () {
                    if (p.bar.scrollLeft !== p.wrap.scrollLeft) p.bar.scrollLeft = p.wrap.scrollLeft;
                }, { passive: true });
            });
            syncWidth();
        }
        wire();
        window.addEventListener('resize', syncWidth);
        window.addEventListener('load', syncWidth);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(syncWidth);
        setTimeout(syncWidth, 400);
        document.addEventListener('livewire:navigated', wire);
    })();
</script>
