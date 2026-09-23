@props(['title' => 'Chart', 'chart' => null])

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card">
    <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-xl">analytics</span>
        {{ $title }}
    </h3>
    {!! $chart->container() !!}
    {!! $chart->script() !!}
</div>
