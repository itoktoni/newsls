<section class="pricing-section py-16 px-4">
    <div class="max-w-7xl mx-auto">
        @if(!empty($data['title']))
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $data['title'] }}</h2>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(!empty($data['plans']))
                @foreach($data['plans'] as $plan)
                    <div class="card bg-base-100 shadow-lg border border-base-300">
                        <div class="card-body text-center">
                            <h3 class="text-2xl font-bold">{{ $plan['name'] ?? '' }}</h3>
                            <div class="text-4xl font-extrabold my-4">{{ $plan['price'] ?? '' }}</div>
                            @if(!empty($plan['features']))
                                <ul class="space-y-3 text-left">
                                    @foreach($plan['features'] as $feature)
                                        <li class="flex items-center gap-2">
                                            @if($feature['included'] ?? true)
                                                <span class="text-success">&#10003;</span>
                                            @else
                                                <span class="text-error">&#10007;</span>
                                            @endif
                                            <span class="{{ ($feature['included'] ?? true) ? '' : 'line-through text-gray-400' }}">
                                                {{ $feature['title'] ?? '' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>