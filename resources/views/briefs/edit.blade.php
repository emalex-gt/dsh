@php
$briefData = $brief?->data ?? [];
$value = fn (string $key, mixed $default = '') => old($key, data_get($briefData, $key, $default));
$arrayValue = fn (string $key) => old($key, data_get($briefData, $key, []));
$stepRegistry = [
    ['key' => 'empresa', 'title' => 'Empresa', 'description' => 'Informacion general', 'enabled' => true],
    ['key' => 'fiscal', 'title' => 'Fiscal', 'description' => 'Datos fiscales', 'enabled' => false],
    ['key' => 'objetivos', 'title' => 'Objetivos', 'description' => 'Metas del proyecto', 'enabled' => false],
    ['key' => 'publico', 'title' => 'Publico', 'description' => 'Cliente ideal', 'enabled' => false],
    ['key' => 'servicios', 'title' => 'Servicios', 'description' => 'Servicios y requerimientos', 'enabled' => true],
    ['key' => 'inversion', 'title' => 'Inversion', 'description' => 'Presupuesto', 'enabled' => false],
    ['key' => 'marca', 'title' => 'Marca', 'description' => 'Branding y tono', 'enabled' => false],
    ['key' => 'competencia', 'title' => 'Competencia', 'description' => 'Contexto competitivo', 'enabled' => false],
    ['key' => 'automatizacion', 'title' => 'Automatizacion', 'description' => 'Procesos actuales', 'enabled' => false],
    ['key' => 'timeline', 'title' => 'Timeline', 'description' => 'Fechas clave', 'enabled' => false],
    ['key' => 'materiales', 'title' => 'Materiales', 'description' => 'Recursos disponibles', 'enabled' => false],
    ['key' => 'expectativas', 'title' => 'Expectativas', 'description' => 'Alcance esperado', 'enabled' => false],
];
$steps = array_values(array_filter($stepRegistry, fn (array $step): bool => $step['enabled']));
$objectiveOptions = ['Generar ventas', 'Captar leads', 'Automatizar procesos', 'Posicionamiento de marca', 'Escalar operaciones', 'Digitalizar negocio tradicional', 'Crear comunidad', 'Lanzar nuevo producto', 'Otro'];
$brandToneOptions = ['Corporativo', 'Directo', 'Premium', 'Disruptivo', 'Minimalista', 'Otro'];
$materialsOptions = ['Logo', 'Manual de marca', 'Fotos profesionales', 'Videos', 'Base de datos clientes', 'Nada (necesito todo)'];
$expectationOptions = ['Estrategia', 'Ejecucion tecnica', 'Optimizacion continua', 'Soporte mensual', 'Formacion', 'Consultoria'];
$supportOptions = ['Mantenimiento mensual web', 'Soporte tecnico', 'Marketing continuo', 'Gestion Ads', 'Gestion Social Media'];
$serviceSelections = old('service_item_selections', data_get($briefData, 'service_item_selections', []));
$selectedServiceIds = old('selected_service_ids', $preselectedServiceIds ?: data_get($briefData, 'selected_service_ids', array_filter([$value('selected_service_id')])));
$pendingToken = $pendingToken ?? null;
$initialStepKey = $initialStepKey ?? 'empresa';
@endphp

<x-guest-layout :brief-mode="true">
    <div
        x-data="briefWizard(@js([
            'steps' => $steps,
            'serviceCatalog' => $serviceCatalog,
            'selectedSubcategoryId' => $value('selected_service_subcategory_id'),
            'selectedServiceId' => $value('selected_service_id'),
            'selectedServiceIds' => $selectedServiceIds,
            'itemSelections' => $serviceSelections,
            'technicalQuoteRules' => $technicalQuoteRules,
            'brandTones' => $arrayValue('brand_tone'),
            'hasDeadline' => $value('has_deadline', 'no'),
            'hasLaunchDate' => $value('has_launch_date', 'no'),
            'initialStepKey' => $initialStepKey,
        ]))"
        class="panel-premium overflow-hidden"
    >
        <div class="grid min-h-[calc(100vh-4rem)] lg:grid-cols-[320px_1fr]">
            <aside class="hidden border-b border-white/10 bg-slate-950/60 p-6 lg:block lg:border-b-0 lg:border-r">
                <p class="text-xs uppercase tracking-[0.28em] text-cyan-200">Brief de proyecto</p>
                <h1 class="mt-4 text-2xl font-semibold text-white">Cuentanos sobre tu proyecto</h1>
                <p class="mt-3 text-sm leading-7 text-slate-400">Un recorrido guiado para entender tu negocio, tu punto de partida y el alcance que necesitas.</p>
                <div class="mt-8 h-2 overflow-hidden rounded-full bg-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-300 to-lime-300 transition-all duration-300" :style="`width: ${progress}%`"></div>
                </div>
                <div class="mt-8 space-y-3">
                    <template x-for="(item, index) in steps" :key="item.key">
                        <button
                            type="button"
                            @click="goToStep(index)"
                            class="flex w-full items-start gap-3 rounded-2xl border px-4 py-3 text-left transition"
                            :class="stepIndex === index ? 'border-cyan-300/40 bg-cyan-300/10' : 'border-white/10 bg-white/5 hover:bg-white/10'"
                        >
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                                :class="stepIndex === index ? 'bg-white text-slate-950' : 'bg-white/10 text-slate-300'"
                                x-text="index + 1"
                            ></span>
                            <span>
                                <span class="block text-sm font-medium text-white" x-text="item.title"></span>
                                <span class="block text-xs text-slate-400" x-text="item.description"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </aside>

            <section class="flex min-h-0 flex-col">
                <div class="border-b border-white/10 px-5 py-5 sm:px-8">
                    <div class="lg:hidden">
                        <div class="flex items-center justify-between gap-4">
                            <span class="brand-badge" x-text="`Paso ${stepNumber} de ${totalSteps}`"></span>
                            <span class="text-xs uppercase tracking-[0.24em] text-slate-400">Brief</span>
                        </div>
                        <p class="mt-4 text-xs uppercase tracking-[0.28em] text-cyan-200" x-text="currentStep.title"></p>
                        <h2 class="mt-2 text-2xl font-semibold text-white" x-text="currentStep.description"></h2>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-300 to-lime-300 transition-all duration-300" :style="`width: ${progress}%`"></div>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <p class="text-xs uppercase tracking-[0.28em] text-cyan-200" x-text="currentStep.title"></p>
                        <h2 class="mt-2 text-2xl font-semibold text-white" x-text="currentStep.description"></h2>
                    </div>
                    @if (session('status'))
                        <div class="mt-4 rounded-2xl border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-rose-300/20 bg-rose-300/10 px-4 py-3 text-sm text-rose-100">Revisa los campos marcados antes de continuar.</div>
                    @endif
                </div>

                <form id="brief-form" method="POST" action="{{ route('brief.update') }}" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @if ($pendingToken)<input type="hidden" name="pending_token" value="{{ $pendingToken }}">@endif
                    <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                        <section x-show="currentStep.key === 'empresa'" x-cloak data-step-panel="empresa" class="grid gap-5 md:grid-cols-2">
                            <div><label class="field-label">Nombre legal</label><input name="legal_name" class="field-input" value="{{ $value('legal_name') }}" required></div>
                            <div><label class="field-label">Nombre comercial</label><input name="brand_name" class="field-input" value="{{ $value('brand_name') }}"></div>
                            <div><label class="field-label">Pais</label><input name="country" class="field-input" value="{{ $value('country') }}" required></div>
                            <div><label class="field-label">Ciudad</label><input name="city" class="field-input" value="{{ $value('city') }}" required></div>
                            <div class="md:col-span-2"><label class="field-label">Sitio web actual</label><input name="website" type="text" class="field-input" value="{{ $value('website') }}"></div>
                            <div class="md:col-span-2"><label class="field-label">Redes sociales activas</label><textarea name="social_links" class="field-input min-h-28">{{ $value('social_links') }}</textarea></div>
                            <div><label class="field-label">Persona de contacto</label><input name="contact_name" class="field-input" value="{{ $value('contact_name') }}" required></div>
                            <div><label class="field-label">Cargo</label><input name="contact_role" class="field-input" value="{{ $value('contact_role') }}" required></div>
                            <div><label class="field-label">Email</label><input name="contact_email" type="email" class="field-input" value="{{ $value('contact_email', auth()->user()?->email) }}" required></div>
                            <div><label class="field-label">Telefono</label><input name="contact_phone" class="field-input" value="{{ $value('contact_phone') }}" required></div>
                        </section>

                        <section x-show="false" x-cloak data-step-panel="fiscal" class="grid gap-5 md:grid-cols-2">
                            <div><label class="field-label">Empresa en la Union Europea</label><select name="eu_registered" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('eu_registered') === 'si')>Si</option><option value="no" @selected($value('eu_registered') === 'no')>No</option></select></div>
                            <div><label class="field-label">Numero de VAT</label><input name="vat_number" class="field-input" value="{{ $value('vat_number') }}"></div>
                            <div><label class="field-label">Nombre fiscal completo</label><input name="fiscal_name" class="field-input" value="{{ $value('fiscal_name') }}" required></div>
                            <div><label class="field-label">Codigo postal</label><input name="fiscal_postal_code" class="field-input" value="{{ $value('fiscal_postal_code') }}" required></div>
                            <div class="md:col-span-2"><label class="field-label">Direccion fiscal completa</label><textarea name="fiscal_address" class="field-input min-h-28" required>{{ $value('fiscal_address') }}</textarea></div>
                            <div><label class="field-label">Pais fiscal</label><input name="fiscal_country" class="field-input" value="{{ $value('fiscal_country') }}" required></div>
                            <div><label class="field-label">Registro mercantil</label><input name="commercial_registry_number" class="field-input" value="{{ $value('commercial_registry_number') }}"></div>
                            <div><label class="field-label">Facturacion</label><select name="billing_type" class="field-input" required><option value="">Selecciona</option><option value="B2B" @selected($value('billing_type') === 'B2B')>B2B</option><option value="B2C" @selected($value('billing_type') === 'B2C')>B2C</option></select></div>
                            <div><label class="field-label">Facturacion intracomunitaria sin IVA</label><select name="intracommunity_invoice" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('intracommunity_invoice') === 'si')>Si</option><option value="no" @selected($value('intracommunity_invoice') === 'no')>No</option></select></div>
                        </section>

                        <section x-show="false" x-cloak data-step-panel="objetivos" class="space-y-5">
                            <div data-checkbox-group data-required="true" data-label="objetivos del proyecto" class="space-y-4">
                                <div class="grid gap-3 md:grid-cols-2">
                                    @foreach ($objectiveOptions as $option)
                                        <label class="panel-soft flex items-center gap-3 p-4 text-sm text-slate-200"><input type="checkbox" name="project_objectives[]" value="{{ $option }}" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" @checked(in_array($option, $arrayValue('project_objectives'), true))>{{ $option }}</label>
                                    @endforeach
                                </div>
                                <p data-group-error class="text-sm text-rose-300"></p>
                            </div>
                            <div><label class="field-label">Indica otro objetivo</label><input name="project_objectives_other" class="field-input" value="{{ $value('project_objectives_other') }}"></div>
                            <div><label class="field-label">En cuanto tiempo esperas resultados</label><select name="results_timeframe" class="field-input" required><option value="">Selecciona</option><option value="1-3 meses" @selected($value('results_timeframe') === '1-3 meses')>1-3 meses</option><option value="3-6 meses" @selected($value('results_timeframe') === '3-6 meses')>3-6 meses</option><option value="6-12 meses" @selected($value('results_timeframe') === '6-12 meses')>6-12 meses</option></select></div>
                        </section>

                        <section x-show="false" x-cloak data-step-panel="publico" class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2"><label class="field-label">Quien es tu cliente ideal</label><textarea name="ideal_customer" class="field-input min-h-28" required>{{ $value('ideal_customer') }}</textarea></div>
                            <div><label class="field-label">Edad promedio</label><input name="average_age" class="field-input" value="{{ $value('average_age') }}" required></div>
                            <div><label class="field-label">Pais o mercado principal</label><input name="main_market" class="field-input" value="{{ $value('main_market') }}" required></div>
                            <div><label class="field-label">Es B2B o B2C</label><select name="business_model" class="field-input" required><option value="">Selecciona</option><option value="B2B" @selected($value('business_model') === 'B2B')>B2B</option><option value="B2C" @selected($value('business_model') === 'B2C')>B2C</option><option value="B2B y B2C" @selected($value('business_model') === 'B2B y B2C')>B2B y B2C</option></select></div>
                            <div><label class="field-label">Ticket promedio</label><input name="average_ticket" class="field-input" value="{{ $value('average_ticket') }}" required></div>
                            <div class="md:col-span-2"><label class="field-label">Como consigues clientes actualmente</label><textarea name="current_customer_acquisition" class="field-input min-h-28" required>{{ $value('current_customer_acquisition') }}</textarea></div>
                        </section>

                        <section x-show="currentStep.key === 'servicios'" x-cloak data-step-panel="servicios" class="space-y-6">
                            <div class="grid gap-5 lg:grid-cols-2">
                                <div>
                                    <label class="field-label">Subcategoria del servicio</label>
                                    <select class="field-input" x-model="selectedSubcategoryId" @change="handleSubcategoryChange">
                                        <option value="">Selecciona una subcategoria</option>
                                        <template x-for="subcategory in catalogSubcategories" :key="subcategory.id">
                                            <option :value="String(subcategory.id)" :disabled="hasSubcategory(subcategory.id)" x-text="hasSubcategory(subcategory.id) ? `${subcategory.name} (ya seleccionado)` : subcategory.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="field-label">Servicio</label>
                                    <select class="field-input" x-model="selectedServiceId" @change="handleServiceChange">
                                        <option value="">Selecciona un servicio</option>
                                        <template x-for="service in availableServices" :key="service.id">
                                            <option :value="String(service.id)" x-text="service.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button type="button" class="btn-primary" @click="addSelectedService" :disabled="!currentService || hasSubcategory(selectedSubcategoryId)">Agregar servicio</button>
                                <p class="text-sm text-slate-400">Puedes agregar un servicio por cada subcategoria.</p>
                            </div>

                            <template x-for="serviceId in selectedServiceIds" :key="serviceId">
                                <section class="panel-soft space-y-5 p-5">
                                    <input type="hidden" name="selected_service_ids[]" :value="serviceId">
                                    <template x-if="serviceById(serviceId)">
                                        <div>
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <span class="brand-badge" x-text="subcategoryForService(serviceById(serviceId))?.name || 'Servicio'"></span>
                                                    <h3 class="mt-3 text-2xl font-semibold text-white" x-text="serviceById(serviceId).name"></h3>
                                                    <p class="mt-2 text-sm leading-7 text-slate-300" x-text="serviceById(serviceId).description || 'Configura los elementos incluidos en este servicio.'"></p>
                                                </div>
                                                <button type="button" class="btn-secondary" @click="removeSelectedService(serviceId)">Quitar</button>
                                            </div>

                                            <div class="mt-5 space-y-4">
                                                <template x-for="item in serviceById(serviceId).items || []" :key="item.id">
                                                    <section class="panel-dark space-y-4 p-5">
                                            <div>
                                                <p class="text-lg font-semibold text-white" x-text="item.name"></p>
                                                <p class="mt-2 text-sm leading-7 text-slate-400" x-text="item.description || 'Selecciona la opcion que mejor encaje con tu proyecto.'"></p>
                                            </div>

                                            <template x-if="item.options.length > 1">
                                                <div class="grid gap-3">
                                                    <template x-for="option in item.options" :key="option.id">
                                                        <label class="panel-soft flex cursor-pointer items-start gap-4 p-4">
                                                            <input
                                                                type="radio"
                                                                class="mt-1 h-5 w-5 shrink-0 border-white/30 bg-slate-950 text-lime-300 focus:ring-lime-300/40"
                                                                :name="`service_item_selections[${item.id}]`"
                                                                :value="String(option.id)"
                                                                x-model="itemSelections[item.id]"
                                                                :required="!isOptionalItem(item)"
                                                            >
                                                            <div class="flex items-start justify-between gap-4">
                                                                <div>
                                                                    <p class="text-sm font-semibold text-white" x-text="option.name"></p>
                                                                    <p class="mt-2 text-sm text-slate-400" x-text="option.description || ''"></p>
                                                                    <template x-if="option.price_summary.length">
                                                                        <div class="mt-3 space-y-1">
                                                                            <template x-for="priceLine in option.price_summary" :key="priceLine">
                                                                                <p class="text-xs uppercase tracking-[0.18em] text-cyan-200" x-text="priceLine"></p>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="item.options.length === 1">
                                                <div>
                                                    <input type="hidden" :name="`service_item_selections[${item.id}]`" :value="itemSelections[item.id] || ''">
                                                    <label class="panel-soft flex items-start gap-3 p-4 text-sm text-slate-200">
                                                        <input
                                                            type="checkbox"
                                                            class="mt-1 rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40"
                                                            :checked="isRequiredItem(item) || String(itemSelections[item.id] || '') === String(item.options[0].id)"
                                                            :disabled="isRequiredItem(item)"
                                                            @change="itemSelections[item.id] = $event.target.checked ? String(item.options[0].id) : ''"
                                                        >
                                                        <span>
                                                            <span class="block font-semibold text-white" x-text="item.options[0].name"></span>
                                                            <span class="mt-2 block text-slate-400" x-text="isRequiredItem(item) ? 'Incluido obligatoriamente en este servicio.' : (item.options[0].description || 'Activar este elemento dentro del servicio.')"></span>
                                                            <template x-if="item.options[0].price_summary.length">
                                                                <div class="mt-3 space-y-1">
                                                                    <template x-for="priceLine in item.options[0].price_summary" :key="priceLine">
                                                                        <p class="text-xs uppercase tracking-[0.18em] text-cyan-200" x-text="priceLine"></p>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </span>
                                                    </label>
                                                </div>
                                            </template>
                                                    </section>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </section>
                            </template>

                            <div x-show="selectedServiceIds.length === 0" class="panel-soft p-5 text-sm leading-7 text-slate-300">
                                Selecciona una subcategoria y un servicio, luego pulsa Agregar servicio.
                            </div>

                            <section x-show="selectedServiceIds.length" class="panel-dark p-5">
                                <p class="section-kicker">Estimacion automatica</p>
                                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                                    <div><p class="text-sm text-slate-400">Presupuesto tecnico base</p><p class="mt-1 text-2xl font-semibold text-white" x-text="formatMoney(liveEstimate.technicalBase)"></p></div>
                                    <div><p class="text-sm text-slate-400">Servicios y cargos seleccionados</p><p class="mt-1 text-2xl font-semibold text-white" x-text="formatMoney(liveEstimate.selectedCharges + liveEstimate.prepaidFees)"></p></div>
                                    <div><p class="text-sm text-slate-400">Total estimado con la seleccion</p><p class="mt-1 text-2xl font-semibold text-lime-200" x-text="formatMoney(liveEstimate.total)"></p></div>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-slate-400">Precio + IVA. Los cargos mensuales y anuales se suman por adelantado solo cuando los activas. El importe final se confirma despues de revisar el alcance con el equipo.</p>
                            </section>
                        </section>

                        <section x-show="currentStep.key === 'inversion'" x-cloak data-step-panel="inversion"><div class="max-w-xl"><label class="field-label">Presupuesto estimado en EUR</label><input name="investment_budget" type="number" min="0" step="0.01" class="field-input" value="{{ $value('investment_budget') }}" required></div></section>
                        <section x-show="currentStep.key === 'marca'" x-cloak data-step-panel="marca" class="space-y-5"><div data-checkbox-group data-required="true" data-label="tono de marca" class="space-y-4"><div class="grid gap-3 md:grid-cols-2">@foreach ($brandToneOptions as $option)<label class="panel-soft flex items-center gap-3 p-4 text-sm text-slate-200"><input type="checkbox" name="brand_tone[]" value="{{ $option }}" x-model="brandTones" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" @checked(in_array($option, $arrayValue('brand_tone'), true))>{{ $option }}</label>@endforeach</div><p data-group-error class="text-sm text-rose-300"></p></div><div x-show="brandTones.includes('Otro')" x-cloak><label class="field-label">Indica otro tono</label><input name="brand_tone_other" class="field-input" value="{{ $value('brand_tone_other') }}"></div><div><label class="field-label">Valores de marca</label><textarea name="brand_values" class="field-input min-h-28" required>{{ $value('brand_values') }}</textarea></div><div><label class="field-label">Diferenciador principal frente a la competencia</label><textarea name="competitive_differentiator" class="field-input min-h-28" required>{{ $value('competitive_differentiator') }}</textarea></div></section>
                        <section x-show="currentStep.key === 'competencia'" x-cloak data-step-panel="competencia" class="space-y-5"><div><label class="field-label">Principales competidores</label><textarea name="main_competitors" class="field-input min-h-24" required>{{ $value('main_competitors') }}</textarea></div><div><label class="field-label">Que hacen mejor</label><textarea name="competitors_best" class="field-input min-h-24" required>{{ $value('competitors_best') }}</textarea></div><div><label class="field-label">Que hacen peor</label><textarea name="competitors_worst" class="field-input min-h-24" required>{{ $value('competitors_worst') }}</textarea></div><div><label class="field-label">Cual es tu ventaja competitiva</label><textarea name="competitive_advantage" class="field-input min-h-24" required>{{ $value('competitive_advantage') }}</textarea></div></section>
                        <section x-show="currentStep.key === 'automatizacion'" x-cloak data-step-panel="automatizacion" class="grid gap-5 md:grid-cols-2"><div><label class="field-label">Usas CRM actualmente</label><select name="uses_crm" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('uses_crm') === 'si')>Si</option><option value="no" @selected($value('uses_crm') === 'no')>No</option></select></div><div><label class="field-label">Usas email marketing</label><select name="uses_email_marketing" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('uses_email_marketing') === 'si')>Si</option><option value="no" @selected($value('uses_email_marketing') === 'no')>No</option></select></div><div><label class="field-label">Necesitas embudos</label><select name="needs_funnels" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('needs_funnels') === 'si')>Si</option><option value="no" @selected($value('needs_funnels') === 'no')>No</option></select></div><div><label class="field-label">Necesitas automatizacion de ventas</label><select name="needs_sales_automation" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('needs_sales_automation') === 'si')>Si</option><option value="no" @selected($value('needs_sales_automation') === 'no')>No</option></select></div><div class="md:col-span-2"><label class="field-label">Necesitas integracion con Meta o Google Ads</label><select name="needs_ads_integration" class="field-input" required><option value="">Selecciona</option><option value="si" @selected($value('needs_ads_integration') === 'si')>Si</option><option value="no" @selected($value('needs_ads_integration') === 'no')>No</option></select></div></section>
                        <section x-show="currentStep.key === 'timeline'" x-cloak data-step-panel="timeline" class="grid gap-5 md:grid-cols-2"><div><label class="field-label">Cuando deseas iniciar</label><input name="desired_start_date" type="date" class="field-input" value="{{ $value('desired_start_date') }}" required></div><div><label class="field-label">Tienes fecha limite</label><select name="has_deadline" x-model="hasDeadline" class="field-input" required><option value="si" @selected($value('has_deadline') === 'si')>Si</option><option value="no" @selected($value('has_deadline', 'no') === 'no')>No</option></select></div><div x-show="hasDeadline === 'si'" x-cloak><label class="field-label">Fecha limite</label><input name="deadline_date" type="date" class="field-input" value="{{ $value('deadline_date') }}"></div><div><label class="field-label">Hay lanzamiento programado</label><select name="has_launch_date" x-model="hasLaunchDate" class="field-input" required><option value="si" @selected($value('has_launch_date') === 'si')>Si</option><option value="no" @selected($value('has_launch_date', 'no') === 'no')>No</option></select></div><div x-show="hasLaunchDate === 'si'" x-cloak><label class="field-label">Fecha de lanzamiento</label><input name="launch_date" type="date" class="field-input" value="{{ $value('launch_date') }}"></div></section>
                        <section x-show="currentStep.key === 'materiales'" x-cloak data-step-panel="materiales" class="space-y-4"><div data-checkbox-group data-required="true" data-label="materiales disponibles" class="space-y-4"><div class="grid gap-3 md:grid-cols-2">@foreach ($materialsOptions as $option)<label class="panel-soft flex items-center gap-3 p-4 text-sm text-slate-200"><input type="checkbox" name="materials_available[]" value="{{ $option }}" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" @checked(in_array($option, $arrayValue('materials_available'), true))>{{ $option }}</label>@endforeach</div><p data-group-error class="text-sm text-rose-300"></p></div></section>
                        <section x-show="currentStep.key === 'expectativas'" x-cloak data-step-panel="expectativas" class="space-y-6"><div data-checkbox-group data-required="true" data-label="expectativas del servicio" class="space-y-4"><div class="grid gap-3 md:grid-cols-2">@foreach ($expectationOptions as $option)<label class="panel-soft flex items-center gap-3 p-4 text-sm text-slate-200"><input type="checkbox" name="service_expectations[]" value="{{ $option }}" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" @checked(in_array($option, $arrayValue('service_expectations'), true))>{{ $option }}</label>@endforeach</div><p data-group-error class="text-sm text-rose-300"></p></div><div class="grid gap-3 md:grid-cols-2">@foreach ($supportOptions as $option)<label class="panel-soft flex items-center gap-3 p-4 text-sm text-slate-200"><input type="checkbox" name="optional_support[]" value="{{ $option }}" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" @checked(in_array($option, $arrayValue('optional_support'), true))>{{ $option }}</label>@endforeach</div></section>
                    </div>

                    <div class="border-t border-white/10 px-6 py-5 sm:px-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button type="button" @click="previousStep" class="btn-secondary" x-show="stepIndex > 0" x-cloak>Volver</button>
                            <div class="flex flex-col gap-3 sm:ml-auto sm:flex-row">
                                <button type="button" @click="nextStep" class="btn-primary" x-show="stepIndex < totalSteps - 1" x-cloak>Continuar</button>
                                <button type="button" @click="submitBrief" class="btn-primary" x-show="stepIndex === totalSteps - 1" x-cloak>Enviar brief</button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        window.briefWizard = function (config) {
            return {
                stepIndex: 0,
                steps: config.steps,
                totalSteps: config.steps.length,
                serviceCatalog: config.serviceCatalog || [],
                technicalQuoteRules: config.technicalQuoteRules || [],
                selectedSubcategoryId: config.selectedSubcategoryId ? String(config.selectedSubcategoryId) : '',
                selectedServiceId: config.selectedServiceId ? String(config.selectedServiceId) : '',
                selectedServiceIds: (config.selectedServiceIds || []).map(String),
                itemSelections: config.itemSelections || {},
                brandTones: config.brandTones || [],
                hasDeadline: config.hasDeadline || 'no',
                hasLaunchDate: config.hasLaunchDate || 'no',
                init() { this.stepIndex = Math.max(0, this.steps.findIndex((step) => step.key === config.initialStepKey)); this.selectedServiceIds.forEach((serviceId) => this.ensureRequiredSelections(this.serviceById(serviceId))); },
                get progress() { return Math.round(((this.stepIndex + 1) / this.totalSteps) * 100); },
                get stepNumber() { return this.stepIndex + 1; },
                get currentStep() { return this.steps[this.stepIndex] || this.steps[0] || { key: 'empresa', title: '', description: '' }; },
                get catalogSubcategories() { return this.serviceCatalog.flatMap((category) => category.subcategories || []); },
                get selectedSubcategory() { return this.catalogSubcategories.find((subcategory) => String(subcategory.id) === String(this.selectedSubcategoryId)) || null; },
                get selectedSubcategoryLabel() { return this.selectedSubcategory?.name || 'Servicio'; },
                get availableServices() { return this.selectedSubcategory?.services || []; },
                get currentService() { return this.availableServices.find((service) => String(service.id) === String(this.selectedServiceId)) || null; },
                serviceById(serviceId) { return this.catalogSubcategories.flatMap((subcategory) => subcategory.services || []).find((service) => String(service.id) === String(serviceId)) || null; },
                subcategoryForService(service) { return this.catalogSubcategories.find((subcategory) => (subcategory.services || []).some((candidate) => String(candidate.id) === String(service?.id))) || null; },
                hasSubcategory(subcategoryId) {
                    return this.selectedServiceIds.some((serviceId) => String(this.subcategoryForService(this.serviceById(serviceId))?.id) === String(subcategoryId));
                },
                addSelectedService() {
                    if (!this.currentService || this.hasSubcategory(this.selectedSubcategoryId)) return;
                    this.ensureRequiredSelections(this.currentService);
                    this.selectedServiceIds = [...this.selectedServiceIds, String(this.currentService.id)];
                    this.selectedSubcategoryId = '';
                    this.selectedServiceId = '';
                },
                isRequiredItem(item) { return Boolean(item?.is_required); },
                ensureRequiredSelections(service) {
                    for (const item of service?.items || []) {
                        if (!this.isRequiredItem(item) || (item.options || []).length !== 1) continue;
                        this.itemSelections[item.id] = String(item.options[0].id);
                    }
                },
                removeSelectedService(serviceId) {
                    const service = this.serviceById(serviceId);
                    for (const item of service?.items || []) delete this.itemSelections[item.id];
                    this.selectedServiceIds = this.selectedServiceIds.filter((id) => String(id) !== String(serviceId));
                },
                get liveEstimate() {
                    let technicalBase = 0;
                    let prepaidFees = 0;
                    let selectedCharges = 0;
                    for (const serviceId of this.selectedServiceIds) {
                        const service = this.serviceById(serviceId);
                        const subcategory = this.subcategoryForService(service);
                        const selectedOptions = (service?.items || []).map((item) => ({ item, option: (item.options || []).find((option) => String(option.id) === String(this.itemSelections[item.id])) })).filter((entry) => entry.option);
                        const library = selectedOptions.find((entry) => entry.item.name === 'Biblioteca')?.option;
                        const rule = this.technicalQuoteRules.find((candidate) => candidate.subcategory_code === subcategory?.code && candidate.service_name === service?.name && candidate.library_name === library?.name);
                        technicalBase += Number(rule?.technical_base || 0);
                        const selectedNames = selectedOptions.map((entry) => String(entry.option.name || '').toLocaleLowerCase());
                        const recurringFees = rule?.recurring_fees || [];
                        for (const fee of recurringFees) {
                            if (!selectedNames.includes(fee.name)) continue;
                            prepaidFees += Number(fee.amount || 0);
                        }
                        for (const entry of selectedOptions) {
                            if (entry.item.name === 'Biblioteca') continue;
                            if (recurringFees.some((fee) => fee.name === String(entry.option.name || '').toLocaleLowerCase())) continue;
                            for (const price of entry.option.prices || []) {
                                if (!['FIRST_YEAR', 'ONE_TIME'].includes(price.price_type)) continue;
                                const amount = Number(price.price || 0);
                                selectedCharges += amount;
                            }
                        }
                    }
                    return { technicalBase, selectedCharges, prepaidFees, total: technicalBase + prepaidFees + selectedCharges };
                },
                formatMoney(amount) { return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(Number(amount || 0)); },
                goToStep(targetIndex) { if (targetIndex < this.stepIndex || this.validateStep()) { this.stepIndex = targetIndex; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                nextStep() { if (this.stepIndex < this.totalSteps - 1 && this.validateStep()) { this.stepIndex += 1; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                previousStep() { if (this.stepIndex > 0) { this.stepIndex -= 1; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                handleSubcategoryChange() { this.selectedServiceId = ''; },
                handleServiceChange() {},
                isOptionalItem(item) { return item.item_type === 'SERVICE' && item.options.length === 1; },
                submitBrief() { if (this.validateStep()) { document.getElementById('brief-form').submit(); } },
                validateStep() {
                    const panel = this.$root.querySelector(`[data-step-panel="${this.currentStep.key}"]`);
                    if (!panel) return true;
                    for (const errorNode of panel.querySelectorAll('[data-group-error]')) errorNode.textContent = '';
                    for (const field of panel.querySelectorAll('input, select, textarea')) {
                        if (field.offsetParent === null || field.type === 'checkbox') continue;
                        if (!field.checkValidity()) { field.reportValidity(); return false; }
                    }
                    for (const group of panel.querySelectorAll('[data-checkbox-group]')) {
                        if (group.offsetParent === null || group.dataset.required !== 'true') continue;
                        if (![...group.querySelectorAll('input[type="checkbox"]')].some((option) => option.checked)) {
                            const errorNode = group.querySelector('[data-group-error]');
                            if (errorNode) errorNode.textContent = `Selecciona al menos una opcion en ${group.dataset.label}.`;
                            return false;
                        }
                    }
                    return true;
                },
            };
        };
    </script>
</x-guest-layout>
