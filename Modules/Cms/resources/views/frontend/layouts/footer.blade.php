<footer class="bg-[#0f1f18] text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center font-headline-md text-sm font-bold">{{ Str::upper(Str::substr(config('app.name', 'CP'), 0, 2)) }}</span>
                    <span class="font-headline-md font-bold text-lg">{{ config('app.name', 'Company') }}</span>
                </div>
                <p class="text-white/60 text-sm leading-relaxed max-w-md">Sayur-mayur &amp; bahan dapur segar — sayur, telur, ikan, ayam, daging. Pasar ke dapur Anda, setiap hari.</p>
                <div class="flex gap-3 mt-6">
                    <a href="mailto:info@example.com" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors" aria-label="Email"><span class="material-symbols-outlined text-lg">mail</span></a>
                    <a href="tel:+622100000000" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors" aria-label="Phone"><span class="material-symbols-outlined text-lg">call</span></a>
                </div>
            </div>
            @if($footerMenu && $footerMenu->items)
                @php $footerItems = collect($footerMenu->items)->sortBy('sort_order')->values(); @endphp
                @foreach($footerItems->take(2) as $item)
                    <div class="md:col-span-2">
                        <h5 class="font-semibold text-white mb-4">{{ $item['label'] }}</h5>
                        <ul class="space-y-2.5">
                            @foreach(($item['children'] ?? []) as $child)
                                <li><a href="{{ $child['url'] ?? '#' }}" target="{{ $child['target'] ?? '_self' }}" class="text-white/60 text-sm hover:text-white transition-colors">{{ $child['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
                <div class="md:col-span-3">
                    <h5 class="font-semibold text-white mb-4">Kontak</h5>
                    <ul class="space-y-2.5 text-sm text-white/60">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-base mt-0.5 shrink-0">location_on</span><span>Alamat kantor Anda — ubah via seeder/CMS</span></li>
                        <li><a href="mailto:info@example.com" class="hover:text-white transition-colors">info@example.com</a></li>
                        <li><a href="tel:+622100000000" class="hover:text-white transition-colors">+62 21 0000 0000</a></li>
                    </ul>
                </div>
            @else
                <div class="md:col-span-2">
                    <h5 class="font-semibold text-white mb-4">Tautan</h5>
                    <ul class="space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ url('/#produk') }}" class="hover:text-white">Produk</a></li>
                        <li><a href="{{ url('/#tentang') }}" class="hover:text-white">Tentang Kami</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Kontak</a></li>
                    </ul>
                </div>
                <div class="md:col-span-2">
                    <h5 class="font-semibold text-white mb-4">Produk</h5>
                    <ul class="space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ url('/#produk') }}" class="hover:text-white">Sayur Mayur</a></li>
                        <li><a href="{{ url('/#produk') }}" class="hover:text-white">Telur &amp; Ayam</a></li>
                        <li><a href="{{ url('/#produk') }}" class="hover:text-white">Ikan &amp; Daging</a></li>
                    </ul>
                </div>
                <div class="md:col-span-3">
                    <h5 class="font-semibold text-white mb-4">Kontak</h5>
                    <ul class="space-y-2.5 text-sm text-white/60">
                        <li class="flex gap-2"><span class="material-symbols-outlined text-base mt-0.5 shrink-0">location_on</span><span>Pasar &amp; dapur — antar harian</span></li>
                        <li><a href="mailto:info@example.com" class="hover:text-white">info@example.com</a></li>
                        <li><a href="tel:+622100000000" class="hover:text-white">+62 21 0000 0000</a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between gap-3 text-xs text-white/40">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Company') }}. All rights reserved.</span>
            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Company Profile</span>
        </div>
    </div>
</footer>
