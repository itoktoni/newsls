@php
    $subtitle = $data['subtitle'] ?? '';
    $title = $data['title'] ?? 'Verifikasi Sertifikat';
    $description = $data['description'] ?? '';
    $placeholder = $data['input_placeholder'] ?? 'Masukkan Nomor Sertifikat';
    $buttonText = $data['button_text'] ?? 'Verifikasi Sekarang';
@endphp

<section class="py-24 bg-[#007A4D] relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-secondary-container rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-secondary-container rounded-full blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
    </div>
    <div class="max-w-4xl mx-auto px-8 relative z-10">
        <div class="text-center mb-12">
            @if($subtitle)
                <span class="font-label-md text-secondary-container tracking-widest uppercase block mb-2">{{ $subtitle }}</span>
            @endif
            <h2 class="font-headline-lg text-headline-lg text-white">{{ $title }}</h2>
            @if($description)
                <p class="text-white/80 mt-4 max-w-lg mx-auto">{{ $description }}</p>
            @endif
        </div>
        <div class="glass-dark p-2 rounded-2xl flex flex-col md:flex-row items-stretch gap-2 border-white/20">
            <div class="flex-grow flex items-center px-6 py-4 gap-4">
                <span class="material-symbols-outlined text-secondary-container text-3xl">qr_code_scanner</span>
                <input class="w-full bg-transparent border-none text-white focus:ring-0 text-lg placeholder:text-white/40" placeholder="{{ $placeholder }}" type="text" />
            </div>
            <button class="bg-secondary-container text-on-secondary-container px-10 py-4 rounded-xl font-headline-md flex items-center justify-center gap-3 hover:bg-secondary-fixed-dim transition-all shadow-lg shadow-black/10">
                {{ $buttonText }}
            </button>
        </div>
        <div class="flex justify-center gap-8 mt-10">
            <div class="flex items-center gap-2 text-white/70 text-label-sm">
                <span class="material-symbols-outlined text-sm text-secondary-container">verified_user</span>
                256-bit Encrypted
            </div>
            <div class="flex items-center gap-2 text-white/70 text-label-sm">
                <span class="material-symbols-outlined text-sm text-secondary-container">history</span>
                Real-time Logs
            </div>
            <div class="flex items-center gap-2 text-white/70 text-label-sm">
                <span class="material-symbols-outlined text-sm text-secondary-container">account_balance</span>
                Terakreditasi KAN
            </div>
        </div>
    </div>
</section>