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
        <div class="table-hbar bg-surface-container-lowest border-t border-outline-variant" data-hbar style="display:none"><div data-hbar-inner></div></div>

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
        overflow-x: auto;
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
</style>
<script>
    (function () {
        function wire(root) {
            if (!root || root.dataset.hwired) return;
            var wrap = root.querySelector('[data-twrap]');
            var bar = root.querySelector('[data-hbar]');
            var inner = root.querySelector('[data-hbar-inner]');
            if (!wrap || !bar || !inner) return;
            root.dataset.hwired = '1';
            if (!document.querySelector('[data-hbar].on')) {
                bar.classList.add('on');
                bar.style.display = '';
            }
            var go = function () {
                var w = wrap.scrollWidth + 'px';
                if (inner.style.width !== w) inner.style.width = w;
            };
            go();
            bar.addEventListener('scroll', function () {
                if (wrap.scrollLeft !== bar.scrollLeft) wrap.scrollLeft = bar.scrollLeft;
            }, { passive: true });
            wrap.addEventListener('scroll', function () {
                if (bar.scrollLeft !== wrap.scrollLeft) bar.scrollLeft = wrap.scrollLeft;
            }, { passive: true });
            window.addEventListener('resize', go);
            if (document.fonts && document.fonts.ready) document.fonts.ready.then(go);
            setTimeout(go, 400);
        }
        function wireAll() {
            document.querySelectorAll('[data-twrap]').forEach(function (el) {
                wire(el.closest('.form-card') || el.parentElement);
            });
        }
        wireAll();
        document.addEventListener('livewire:navigated', wireAll);
    })();
</script>
