@props(['children', 'value' => [], 'min' => 0, 'max' => 100])

<div class="repeater" data-min="{{ $min }}" data-max="{{ $max }}">
    <div class="repeater-items">
        @foreach($value as $index => $item)
            <div class="repeater-item" data-index="{{ $index }}">
                <div class="repeater-item-header">
                    <span class="repeater-item-handle">⠿</span>
                    <span class="repeater-item-title">Item {{ $index + 1 }}</span>
                    <button type="button" class="repeater-remove">×</button>
                </div>
                <div class="repeater-item-fields">
                    @foreach($children as $child)
                        <x-form.dynamic 
                            name="{{ $attributes->get('name') }}[{{ $index }}][{{ $child['name'] }}]"
                            :field="$child"
                            :value="$item[$child['name']] ?? null"
                        />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    
    <button type="button" class="repeater-add" 
            @if(count($value) >= $max) disabled @endif>
        + Add Item
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const repeaters = document.querySelectorAll('.repeater');
    
    repeaters.forEach(repeater => {
        const itemsContainer = repeater.querySelector('.repeater-items');
        const addButton = repeater.querySelector('.repeater-add');
        const max = parseInt(repeater.dataset.max);
        
        addButton.addEventListener('click', function() {
            const currentItems = itemsContainer.querySelectorAll('.repeater-item');
            if (currentItems.length >= max) return;
            
            const newIndex = currentItems.length;
            const newItem = document.createElement('div');
            newItem.className = 'repeater-item';
            newItem.dataset.index = newIndex;
            newItem.innerHTML = `
                <div class="repeater-item-header">
                    <span class="repeater-item-handle">⠿</span>
                    <span class="repeater-item-title">Item ${newIndex + 1}</span>
                    <button type="button" class="repeater-remove">×</button>
                </div>
                <div class="repeater-item-fields">
                    <!-- Fields will be added here -->
                </div>
            `;
            
            itemsContainer.appendChild(newItem);
            updateRepeaterState(repeater);
        });
        
        itemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('repeater-remove')) {
                const item = e.target.closest('.repeater-item');
                item.remove();
                updateRepeaterState(repeater);
            }
        });
        
        function updateRepeaterState(repeater) {
            const items = repeater.querySelectorAll('.repeater-item');
            const addButton = repeater.querySelector('.repeater-add');
            const max = parseInt(repeater.dataset.max);
            
            addButton.disabled = items.length >= max;
            
            items.forEach((item, index) => {
                item.dataset.index = index;
                const title = item.querySelector('.repeater-item-title');
                if (title) title.textContent = `Item ${index + 1}`;
            });
        }
    });
});
</script>
