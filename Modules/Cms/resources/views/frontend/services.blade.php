@extends('cms::frontend.layouts.public')

@section('title', $entry?->title ?? 'Pelayanan Kami')

@section('content')
    @if ($html)
        {!! $html !!}
    @else
        {{-- Fallback jika belum ada content type services --}}
        <section class="py-24 bg-surface-container-low">
            <div class="max-w-7xl mx-auto px-8">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                    <div class="max-w-2xl">
                        <div class="w-12 h-1 bg-secondary-container mb-6"></div>
                        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Pelayanan Kami</h2>
                        <p class="text-on-surface-variant text-body-lg">Solusi teknis terdepan untuk fasilitas kesehatan modern, memastikan setiap alat diagnostik beroperasi dalam batas presisi yang ketat.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl p-8 border border-outline-variant/30 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-surface-container-low text-primary flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[32px]">biotech</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Kalibrasi Alat Kesehatan</h3>
                        <p class="text-on-surface-variant text-[15px] mb-6 leading-relaxed">Layanan kalibrasi untuk berbagai jenis alat kesehatan dengan standar akurasi tinggi dan tersertifikasi.</p>
                        <div class="h-px bg-outline-variant/30 mb-6"></div>
                        <ul class="space-y-3 font-body-md text-[13px] text-outline">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Alat Diagnostik</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Alat Laboratorium</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Alat Terapi</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl p-8 border border-outline-variant/30 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-surface-container-low text-primary flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[32px]">insights</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Inspeksi Fasilitas</h3>
                        <p class="text-on-surface-variant text-[15px] mb-6 leading-relaxed">Inspeksi menyeluruh terhadap fasilitas kesehatan untuk memastikan kepatuhan terhadap standar yang berlaku.</p>
                        <div class="h-px bg-outline-variant/30 mb-6"></div>
                        <ul class="space-y-3 font-body-md text-[13px] text-outline">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Inspeksi Sarana</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Inspeksi Prasarana</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Audit Kepatuhan</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl p-8 border border-outline-variant/30 hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-surface-container-low text-primary flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[32px]">school</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mb-4">Pelatihan & Sertifikasi</h3>
                        <p class="text-on-surface-variant text-[15px] mb-6 leading-relaxed">Program pelatihan dan sertifikasi untuk tenaga teknis fasilitas kesehatan.</p>
                        <div class="h-px bg-outline-variant/30 mb-6"></div>
                        <ul class="space-y-3 font-body-md text-[13px] text-outline">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Teknisi Alat Kesehatan</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Manajemen Faskes</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-secondary-container rounded-full"></span>Keselamatan Pasien</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection