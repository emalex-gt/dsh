<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-300 via-cyan-400 to-lime-300 text-sm font-black tracking-[0.25em] text-slate-950 shadow-[0_10px_30px_rgba(120,255,182,0.28)]">
        DSH
    </span>
    <span class="flex flex-col leading-none">
        <span class="text-xs font-semibold uppercase tracking-[0.38em] text-cyan-200">Area Cliente</span>
        <span class="text-sm font-medium text-white/80">{{ config('app.name', 'DSH Studio') }}</span>
    </span>
</div>
