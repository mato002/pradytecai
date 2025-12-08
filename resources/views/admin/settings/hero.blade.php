@extends('layouts.admin')

@section('title', 'Hero Settings - Pradytecai')
@section('page_title', 'Hero Surface')
@section('page_eyebrow', 'Marketing shell')
@section('page_description', 'Control the primary hero background on the public site to match your latest product stories.')

@section('content')
    <div class="grid gap-8 lg:grid-cols-12">
        <div class="lg:col-span-7 space-y-6">
            <div class="glass-card">
                <h2 class="text-xl font-bold text-slate-900">Hero background image</h2>
                <p class="mt-2 text-base text-slate-700">
                    Upload a wide, high‑resolution image that feels <span class="font-semibold text-indigo-700">technical</span> –
                    data visualisations, network meshes, dashboards, or abstract circuitry usually work best.
                </p>

                @if (session('success'))
                    <div class="mt-4 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.settings.hero.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="hero_image" class="block text-base font-semibold text-slate-800 mb-2">
                            Background image
                        </label>
                        <div class="flex items-center gap-4">
                            <input
                                id="hero_image"
                                name="hero_image"
                                type="file"
                                accept="image/*"
                                class="block w-full text-base text-slate-900 file:mr-4 file:rounded-xl file:border-0
                                       file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold
                                       file:text-white hover:file:bg-indigo-700"
                            >
                        </div>
                        <p class="mt-2 text-sm text-slate-600">
                            Recommended: 1920×1080 or wider • up to 4&nbsp;MB • will be darkened and layered with animated tech overlays.
                        </p>
                        @error('hero_image')
                            <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="submit" class="btn-primary">
                            Save hero background
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-4">
            <div class="glass-card">
                <h3 class="text-lg font-bold text-slate-900 mb-3">Live preview</h3>
                <p class="text-sm text-slate-700 mb-4">
                    This is an approximate preview of how the public hero will render with the selected image and
                    animated tech overlays.
                </p>

                <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950">
                    <div class="relative aspect-[16/9]">
                        @if ($heroImageUrl)
                            <div
                                class="absolute inset-0 bg-cover bg-center opacity-70"
                                style="background-image: url('{{ $heroImageUrl }}');"
                            ></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-slate-950 to-slate-900"></div>
                        @endif

                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(56,189,248,0.18),_transparent_55%),radial-gradient(circle_at_bottom,_rgba(129,140,248,0.18),_transparent_55%)] mix-blend-screen"></div>
                        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(148,163,184,0.18)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.16)_1px,transparent_1px)] bg-[length:40px_40px] opacity-40"></div>

                        <div class="relative flex h-full flex-col items-center justify-center px-6 text-center">
                            <h4 class="text-lg font-semibold text-slate-50">
                                Enterprise Software Solutions
                            </h4>
                            <p class="mt-2 text-xs text-slate-300 max-w-sm">
                                Tech‑forward hero surface with animated overlays and your chosen background image.
                            </p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2 text-[10px] text-slate-200">
                                <span class="rounded-full border border-sky-400/40 bg-sky-500/10 px-3 py-1">
                                    BulkSMS CRM
                                </span>
                                <span class="rounded-full border border-indigo-400/40 bg-indigo-500/10 px-3 py-1">
                                    Microfinance stack
                                </span>
                                <span class="rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1">
                                    Realtime analytics
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mt-3 text-sm text-slate-600">
                    For best results, choose imagery with depth, gradients, or subtle patterns that complement
                    overlays rather than flat solid colours.
                </p>
            </div>
        </div>
    </div>
@endsection



