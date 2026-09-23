@props(['tabs' => [], 'id' => 'tabs'])

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card">
    <div class="flex gap-1 border-b border-outline-variant mb-4">
        @foreach($tabs as $index => $tab)
        <button class="px-4 py-2 font-body-sm text-body-sm font-semibold border-b-2 {{ $index === 0 ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }} transition-colors"
                onclick="showTab('{{ $tab['id'] ?? $index }}')">
            {{ $tab['label'] ?? 'Tab ' . ($index + 1) }}
        </button>
        @endforeach
    </div>

    @foreach($tabs as $index => $tab)
    <div id="tab-{{ $tab['id'] ?? $index }}" class="{{ $index !== 0 ? 'hidden' : '' }}">
        {{ $tab['content'] ?? '' }}
    </div>
    @endforeach
</div>

@once
@push('scripts')
<script>
function showTab(id) {
    document.querySelectorAll('[id^="tab-"]').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + id)?.classList.remove('hidden');
    event.target.parentElement.querySelectorAll('button').forEach(b => {
        b.className = 'px-4 py-2 font-body-sm text-body-sm font-semibold border-b-2 border-transparent text-on-surface-variant hover:text-on-surface transition-colors';
    });
    event.target.className = 'px-4 py-2 font-body-sm text-body-sm font-semibold border-b-2 border-primary text-primary transition-colors';
}
</script>
@endpush
@endonce
