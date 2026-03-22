<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Soporte 24/7</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Nuevo ticket</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Cuanto mejor describas la necesidad, mas rapido podremos darte una respuesta util y accionable.
                    </p>
                </div>
                <a href="{{ route('client.tickets.index') }}" class="btn-secondary">Volver a tickets</a>
            </div>
        </div>
    </x-slot>

    <div class="panel-premium p-6 sm:p-8">
        <form method="POST" action="{{ route('client.tickets.store') }}" class="grid gap-6 md:grid-cols-2">
            @csrf
            <div class="md:col-span-2">
                <label class="field-label">Asunto</label>
                <input name="subject" class="field-input" value="{{ old('subject') }}" required>
                @error('subject') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Categoria</label>
                <select name="category" class="field-input" required>
                    <option value="">Selecciona</option>
                    <option value="tecnico" @selected(old('category') === 'tecnico')>Tecnico</option>
                    <option value="desarrollo" @selected(old('category') === 'desarrollo')>Desarrollo</option>
                    <option value="acceso" @selected(old('category') === 'acceso')>Acceso</option>
                    <option value="facturacion" @selected(old('category') === 'facturacion')>Facturacion</option>
                    <option value="otro" @selected(old('category') === 'otro')>Otro</option>
                </select>
                @error('category') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="field-label">Prioridad</label>
                <select name="priority" class="field-input" required>
                    <option value="media" @selected(old('priority', 'media') === 'media')>Media</option>
                    <option value="baja" @selected(old('priority') === 'baja')>Baja</option>
                    <option value="alta" @selected(old('priority') === 'alta')>Alta</option>
                </select>
                @error('priority') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="field-label">Mensaje</label>
                <textarea name="message" class="field-input min-h-52" required>{{ old('message') }}</textarea>
                @error('message') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-white/10 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('client.tickets.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Enviar ticket</button>
            </div>
        </form>
    </div>
</x-app-layout>
