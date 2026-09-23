@extends('cms::frontend.layouts.public')

@section('title', $entry?->title ?? 'Hubungi Kami')

@section('content')
    @if ($html)
        {!! $html !!}
    @else
        {{-- Fallback jika belum ada content type contact --}}
        <section class="py-24 bg-surface">
            <div class="max-w-7xl mx-auto px-8">
                <div class="flex items-center gap-4 mb-16">
                    <div class="h-px bg-outline-variant flex-grow"></div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface shrink-0 px-4">Hubungi Kami</h1>
                    <div class="h-px bg-outline-variant flex-grow"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    {{-- Contact Info --}}
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-8">Informasi Kontak</h2>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary-container/10 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">location_on</span>
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md text-on-surface mb-1">Alamat</h4>
                                    <p class="text-on-surface-variant">Alamat kantor Anda</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary-container/10 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">call</span>
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md text-on-surface mb-1">Telepon</h4>
                                    <p class="text-on-surface-variant">+62 800 0000 0000</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary-container/10 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">mail</span>
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md text-on-surface mb-1">Email</h4>
                                    <p class="text-on-surface-variant">info@example.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-primary-container/10 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary">schedule</span>
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md text-on-surface mb-1">Jam Kerja</h4>
                                    <p class="text-on-surface-variant">Senin - Jumat: 08:00 - 17:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Form --}}
                    <div class="bg-white rounded-2xl p-8 border border-outline-variant/30 shadow-sm">
                        <h2 class="font-headline-md text-headline-md text-on-surface mb-6">Kirim Pesan</h2>

                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.post') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="font-label-md text-label-md text-on-surface block mb-2">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md @error('name') border-red-500 @enderror" />
                                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-md text-label-md text-on-surface block mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md @error('email') border-red-500 @enderror" />
                                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-md text-label-md text-on-surface block mb-2">Subjek</label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md @error('subject') border-red-500 @enderror" />
                                @error('subject')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="font-label-md text-label-md text-on-surface block mb-2">Pesan</label>
                                <textarea name="message" rows="5" required
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                                @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Honeypot --}}
                            <div class="hidden" aria-hidden="true">
                                <label>Jangan isi ini</label>
                                <input type="text" name="website" value="" autocomplete="off" tabindex="-1" />
                            </div>

                            {{-- CAPTCHA --}}
                            <div>
                                <label class="font-label-md text-label-md text-on-surface block mb-2">Captcha</label>
                                <div class="flex items-center gap-4">
                                    <img src="{{ route('captcha.contact', ['key' => $captchaKey = uniqid()]) }}" alt="Captcha" class="rounded-lg border border-outline-variant" style="height:56px;" id="captcha-image" />
                                    <button type="button" onclick="document.getElementById('captcha-image').src='{{ route('captcha.contact') }}?key='+document.querySelector('input[name=captcha_key]').value+'&_='+Date.now()" class="px-4 py-2.5 bg-surface-container border border-outline-variant rounded-xl text-sm hover:bg-surface-container-high transition-colors">
                                        Refresh
                                    </button>
                                </div>
                                <input type="hidden" name="captcha_key" value="{{ $captchaKey }}">
                                <input type="text" name="captcha" required placeholder="Masukkan hasil captcha"
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md @error('captcha') border-red-500 @enderror mt-3" />
                                @error('captcha')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="w-full bg-primary text-on-primary py-3.5 rounded-xl font-label-md hover:opacity-90 active:scale-95 transition-all">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection