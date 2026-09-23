<div class="p-3 pb-4 border-b border-outline-variant">
    <div class="relative">
        <select class="w-full h-10 pl-3 pr-8 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all">
            <option>{{ config('app.name', 'Laravel') }}</option>
            @foreach ($options ?? [] as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-lg">expand_more</span>
    </div>
</div>
