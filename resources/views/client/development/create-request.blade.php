<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Desarrollo</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Nueva solicitud</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Explica con claridad el ajuste que necesitas para que el equipo pueda trabajarlo dentro del proyecto.
                    </p>
                </div>
                <a href="{{ route('client.development.requests.index') }}" class="btn-secondary">Volver a Edit Area</a>
            </div>
        </div>
    </x-slot>

    <div class="panel-premium p-6 sm:p-8">
        <form method="POST" action="{{ route('client.development.requests.store') }}" class="grid gap-6">
            @csrf
            <div>
                <label class="field-label">Asunto</label>
                <input name="subject" class="field-input" value="{{ old('subject') }}" required>
                @error('subject') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Prioridad</label>
                <select name="priority" class="field-input" required>
                    @foreach (\App\Models\DevelopmentRequest::priorityOptions() as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Detalle</label>
                <textarea name="message" class="field-input min-h-40" required>{{ old('message') }}</textarea>
                @error('message') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn-primary">Crear solicitud</button>
                <a href="{{ route('client.development.requests.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
