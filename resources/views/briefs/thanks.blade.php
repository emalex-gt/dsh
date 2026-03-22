<x-guest-layout :brief-mode="true">
    <section class="panel-premium relative overflow-hidden px-6 py-20 sm:px-10 lg:px-14">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.16),transparent_36%),radial-gradient(circle_at_bottom_right,rgba(163,230,53,0.14),transparent_32%)]"></div>

        <div class="relative mx-auto max-w-3xl text-center">
            <span class="brand-badge">Brief recibido</span>
            <h1 class="mt-6 text-4xl font-semibold text-white sm:text-5xl">
                Hemos guardado tu brief correctamente.
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-sm leading-8 text-slate-300 sm:text-base">
                Gracias por confiar en nosotros. Ya tenemos la informacion necesaria para revisar tu caso con detalle y preparar el siguiente paso con contexto y criterio.
            </p>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-400 sm:text-base">
                Nuestro equipo se pondra en contacto contigo a la mayor brevedad para confirmar la recepcion, resolver cualquier punto clave y avanzar contigo de forma clara desde el inicio.
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('brief.edit') }}" class="btn-secondary px-6 py-3">
                    Enviar otro brief
                </a>
            </div>
        </div>
    </section>
</x-guest-layout>


