@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-2xl border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100']) }}>
        {{ $status }}
    </div>
@endif
