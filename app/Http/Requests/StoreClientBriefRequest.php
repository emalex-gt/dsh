<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreClientBriefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'legal_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'website' => ['nullable', 'url', 'max:255'],
            'social_links' => ['nullable', 'string'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_role' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],

            'eu_registered' => ['nullable', Rule::in(['si', 'no'])],
            'vat_number' => [Rule::requiredIf(fn () => $this->input('eu_registered') === 'si'), 'nullable', 'string', 'max:100'],
            'fiscal_name' => ['nullable', 'string', 'max:255'],
            'fiscal_address' => ['nullable', 'string', 'max:255'],
            'fiscal_postal_code' => ['nullable', 'string', 'max:30'],
            'fiscal_country' => ['nullable', 'string', 'max:120'],
            'commercial_registry_number' => ['nullable', 'string', 'max:100'],
            'billing_type' => ['nullable', Rule::in(['B2B', 'B2C'])],
            'intracommunity_invoice' => ['nullable', Rule::in(['si', 'no'])],

            'project_objectives' => ['nullable', 'array'],
            'project_objectives.*' => ['string', Rule::in(['Generar ventas', 'Captar leads', 'Automatizar procesos', 'Posicionamiento de marca', 'Escalar operaciones', 'Digitalizar negocio tradicional', 'Crear comunidad', 'Lanzar nuevo producto', 'Otro'])],
            'project_objectives_other' => ['nullable', 'string', 'max:255'],
            'results_timeframe' => ['nullable', Rule::in(['1-3 meses', '3-6 meses', '6-12 meses'])],

            'ideal_customer' => ['nullable', 'string'],
            'average_age' => ['nullable', 'string', 'max:100'],
            'main_market' => ['nullable', 'string', 'max:255'],
            'business_model' => ['nullable', Rule::in(['B2B', 'B2C', 'B2B y B2C'])],
            'average_ticket' => ['nullable', 'string', 'max:100'],
            'current_customer_acquisition' => ['nullable', 'string'],

            'selected_services' => ['required', 'array', 'min:1'],
            'selected_services.*' => ['string', Rule::in(['web', 'apps', 'store', 'design', 'systems'])],

            'web_pack' => [Rule::requiredIf(fn () => $this->hasService('web')), 'nullable', Rule::in(['MiniWeb', 'FullWeb', 'HiperWeb'])],
            'web_requirements' => [Rule::requiredIf(fn () => $this->hasService('web')), 'nullable', 'string'],

            'apps_pack' => [Rule::requiredIf(fn () => $this->hasService('apps')), 'nullable', Rule::in(['WebApp', 'EcommerceApp', 'Android & iOS'])],
            'apps_requirements' => [Rule::requiredIf(fn () => $this->hasService('apps')), 'nullable', 'string'],

            'store_pack' => [Rule::requiredIf(fn () => $this->hasService('store')), 'nullable', Rule::in(['MiniTienda', 'FullTienda', 'HiperTienda'])],
            'store_requirements' => [Rule::requiredIf(fn () => $this->hasService('store')), 'nullable', 'string'],

            'design_scope' => [Rule::requiredIf(fn () => $this->hasService('design')), 'nullable', Rule::in(['UI', 'UX', 'UI + UX'])],
            'design_requirements' => [Rule::requiredIf(fn () => $this->hasService('design')), 'nullable', 'string'],

            'systems_type' => [Rule::requiredIf(fn () => $this->hasService('systems')), 'nullable', 'array'],
            'systems_type.*' => ['string', Rule::in(['ERP', 'CRM', 'POS'])],
            'systems_requirements' => [Rule::requiredIf(fn () => $this->hasService('systems')), 'nullable', 'string'],

            'investment_budget' => ['required', 'numeric', 'min:0'],

            'brand_tone' => ['required', 'array', 'min:1'],
            'brand_tone.*' => ['string', Rule::in(['Corporativo', 'Directo', 'Premium', 'Disruptivo', 'Minimalista', 'Otro'])],
            'brand_tone_other' => ['nullable', 'string', 'max:255'],
            'brand_values' => ['required', 'string'],
            'competitive_differentiator' => ['required', 'string'],

            'main_competitors' => ['required', 'string'],
            'competitors_best' => ['required', 'string'],
            'competitors_worst' => ['required', 'string'],
            'competitive_advantage' => ['required', 'string'],

            'uses_crm' => ['required', Rule::in(['si', 'no'])],
            'uses_email_marketing' => ['required', Rule::in(['si', 'no'])],
            'needs_funnels' => ['required', Rule::in(['si', 'no'])],
            'needs_sales_automation' => ['required', Rule::in(['si', 'no'])],
            'needs_ads_integration' => ['required', Rule::in(['si', 'no'])],

            'desired_start_date' => ['required', 'date'],
            'has_deadline' => ['required', Rule::in(['si', 'no'])],
            'deadline_date' => [Rule::requiredIf(fn () => $this->input('has_deadline') === 'si'), 'nullable', 'date', 'after_or_equal:desired_start_date'],
            'has_launch_date' => ['required', Rule::in(['si', 'no'])],
            'launch_date' => [Rule::requiredIf(fn () => $this->input('has_launch_date') === 'si'), 'nullable', 'date'],

            'materials_available' => ['required', 'array', 'min:1'],
            'materials_available.*' => ['string', Rule::in(['Logo', 'Manual de marca', 'Fotos profesionales', 'Videos', 'Base de datos clientes', 'Nada (necesito todo)'])],

            'service_expectations' => ['required', 'array', 'min:1'],
            'service_expectations.*' => ['string', Rule::in(['Estrategia', 'Ejecucion tecnica', 'Optimizacion continua', 'Soporte mensual', 'Formacion', 'Consultoria'])],

            'optional_support' => ['nullable', 'array'],
            'optional_support.*' => ['string', Rule::in(['Mantenimiento mensual web', 'Soporte tecnico', 'Marketing continuo', 'Gestion Ads', 'Gestion Social Media'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (in_array('Otro', $this->input('project_objectives', []), true) && ! $this->filled('project_objectives_other')) {
                $validator->errors()->add('project_objectives_other', 'Indica el otro objetivo del proyecto.');
            }

            if (in_array('Otro', $this->input('brand_tone', []), true) && ! $this->filled('brand_tone_other')) {
                $validator->errors()->add('brand_tone_other', 'Indica el otro tono de marca.');
            }

            if ($this->hasService('systems') && count((array) $this->input('systems_type', [])) === 0) {
                $validator->errors()->add('systems_type', 'Selecciona al menos un tipo de sistema.');
            }
        });
    }

    public function validatedBriefData(): array
    {
        return $this->safe()->except(['_token']);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project_objectives' => array_values((array) $this->input('project_objectives', [])),
            'selected_services' => array_values((array) $this->input('selected_services', [])),
            'systems_type' => array_values((array) $this->input('systems_type', [])),
            'brand_tone' => array_values((array) $this->input('brand_tone', [])),
            'materials_available' => array_values((array) $this->input('materials_available', [])),
            'service_expectations' => array_values((array) $this->input('service_expectations', [])),
            'optional_support' => array_values((array) $this->input('optional_support', [])),
        ]);
    }

    private function hasService(string $service): bool
    {
        return in_array($service, (array) $this->input('selected_services', []), true);
    }
}
