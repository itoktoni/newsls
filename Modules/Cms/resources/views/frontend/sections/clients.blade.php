@php $items = $data['clients'] ?? (array_is_list($data) ? $data : []); @endphp
@if(empty($items))
    @php $items = []; @endphp
@endif
<section class="py-14 bg-white border-y border-outline-variant/10">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <p class="text-center text-xs font-semibold tracking-[0.18em] uppercase text-on-surface-variant mb-6">Dipercaya oleh</p>
        @if(empty($items))
            <p class="text-center text-sm text-on-surface-variant">Logo klien akan tampil di sini — isi dari CMS.</p>
        @else
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($items as $item)
                    @php $name = $item['name'] ?? $item['label'] ?? ''; $logo = $item['logo'] ?? $item['image'] ?? ''; @endphp
                    <div class="h-14 px-6 rounded-xl border border-outline-variant/20 bg-surface-container-low/60 flex items-center justify-center gap-2">
                        @if($logo)<img src="{{ $logo }}" alt="{{ $name }}" class="h-7 w-auto object-contain" />@endif
                        <span class="text-sm font-medium text-on-surface-variant">{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
