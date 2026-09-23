@props(['perPage' => 25, 'perPageOptions' => [5,10,25,50,100], 'fields' => [], 'searchPlaceholder' => 'Search...'])
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
    <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
        <div class="relative sm:w-auto">
            <select id="perPage" class="h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" onchange="buildUrl()">
                @foreach($perPageOptions as $pp)
                <option value="{{ $pp }}" {{ request('per_page', $perPage) == $pp ? 'selected' : '' }}>{{ $pp }} / page</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-xl">expand_more</span>
        </div>

        @if(isset($advanced))
        <button type="button" class="inline-flex items-center justify-start gap-2 h-12 px-4 text-sm font-semibold rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container transition-all" onclick="document.getElementById('advFilter').classList.remove('hidden')">
            <span class="material-symbols-outlined text-lg">filter_alt</span>
            Advanced
        </button>
        @endif

        @if(count($fields))
        <div class="relative flex-1">
            <select id="filterField" class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all">
                @foreach($fields as $key => $label)
                <option value="{{ $key }}" {{ request('_field', array_key_first($fields)) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-xl">expand_more</span>
        </div>
        @endif

        <div class="flex flex-1">
            <input type="text" id="searchInput" class="flex-1 h-12 px-4 bg-white border border-outline-variant rounded-l-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm"
                value="{{ request('_q') }}"
                placeholder="{{ $searchPlaceholder }}" onkeydown="if(event.key==='Enter')buildUrl()">
            <button type="button" class="inline-flex items-center justify-center h-12 px-4 rounded-r-lg bg-primary text-on-primary hover:bg-primary/90 transition-colors" onclick="buildUrl()">
                <span class="material-symbols-outlined text-xl">search</span>
            </button>
        </div>
    </div>
</div>

@if(isset($advanced))
<div id="advFilter" class="hidden fixed inset-0 z-[60]">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('advFilter').classList.add('hidden')"></div>
    <div class="absolute right-0 top-0 h-dvh max-h-dvh w-80 max-w-[85vw] bg-surface-container-lowest shadow-xl flex flex-col">
        <div class="flex justify-between items-center p-4 pb-3 shrink-0">
            <h3 class="font-headline-md text-headline-md text-on-surface">Advanced Filter</h3>
            <button class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-surface-container transition-colors" onclick="document.getElementById('advFilter').classList.add('hidden')">
                <span class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>
        <div class="flex-1 min-h-0 overflow-y-auto px-4 pb-3 space-y-3">
            {{ $advanced }}
        </div>
        <div class="border-t border-outline-variant p-4 grid grid-cols-2 gap-2 shrink-0 bg-surface-container-lowest">
            <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
        </div>
    </div>
</div>
@endif
