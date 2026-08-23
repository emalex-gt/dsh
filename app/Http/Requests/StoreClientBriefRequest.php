<?php

namespace App\Http\Requests;

use App\Models\CatalogService;
use App\Services\BriefTechnicalQuoteCalculator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
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
            'selected_service_ids' => ['required', 'array', 'min:1'],
            'selected_service_ids.*' => ['required', 'integer', 'distinct', 'exists:catalog_services,id'],
            'service_item_selections' => ['nullable', 'array'],
            'service_item_selections.*' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $services = $this->selectedServices();

            if ($services->count() !== count((array) $this->input('selected_service_ids', []))) {
                return;
            }

            if ($services->pluck('service_subcategory_id')->unique()->count() !== $services->count()) {
                $validator->errors()->add('selected_service_ids', 'Solo puedes seleccionar un servicio por subcategoria.');
            }

            $rawSelections = $this->rawSelections();

            foreach ($services as $service) {
                foreach ($service->items->where('active', true) as $item) {
                    $optionIds = $item->options->where('active', true)->pluck('id')->map(fn (int $id): string => (string) $id);
                    $selectedValue = (string) $rawSelections->get((string) $item->id, '');
                    $isOptionalSingleService = $item->item_type === 'SERVICE' && $optionIds->count() === 1 && ! $item->is_required;

                    if (! $isOptionalSingleService && $optionIds->isNotEmpty() && $selectedValue === '') {
                        $validator->errors()->add("service_item_selections.{$item->id}", "Selecciona una opcion para {$item->name}.");
                    }

                    if ($selectedValue !== '' && ! $optionIds->contains($selectedValue)) {
                        $validator->errors()->add("service_item_selections.{$item->id}", "La opcion elegida para {$item->name} no es valida.");
                    }
                }
            }
        });
    }

    public function validatedBriefData(): array
    {
        $data = $this->safe()->except(['_token']);
        $services = $this->selectedServices();
        $rawSelections = $this->rawSelections();
        $calculator = app(BriefTechnicalQuoteCalculator::class);
        $configurations = [];
        $estimates = [];

        foreach ($services as $service) {
            $selectedOptions = $this->selectedOptionMap($service, $rawSelections);
            $items = $service->items
                ->where('active', true)
                ->map(function ($item) use ($selectedOptions, $service): ?array {
                    $option = $selectedOptions->get($item->id);

                    if (! $option) {
                        return null;
                    }

                    return [
                        'service_id' => $service->id,
                        'service_name' => $service->name,
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'item_type' => $item->item_type,
                        'option_id' => $option->id,
                        'option_name' => $option->name,
                    ];
                })
                ->filter()
                ->values()
                ->all();
            $estimate = $calculator->calculate($service, $selectedOptions);

            $configurations[] = [
                'category_id' => $service->subcategory->category->id,
                'category_name' => $service->subcategory->category->name,
                'subcategory_id' => $service->subcategory->id,
                'subcategory_name' => $service->subcategory->name,
                'service_id' => $service->id,
                'service_name' => $service->name,
                'items' => $items,
            ];
            $estimates[] = $estimate;
        }

        $first = $configurations[0] ?? null;
        $data['selected_service_ids'] = $services->pluck('id')->all();
        $data['selected_services'] = collect($configurations)->map(fn (array $service) => "{$service['subcategory_name']} / {$service['service_name']}")->all();
        $data['selected_service_configurations'] = $configurations;
        $data['selected_service_items'] = collect($configurations)->pluck('items')->flatten(1)->values()->all();
        $data['service_item_selections'] = $rawSelections->all();
        $data['selected_service_category_id'] = $first['category_id'] ?? null;
        $data['selected_service_category_name'] = $first['category_name'] ?? null;
        $data['selected_service_subcategory_id'] = $first['subcategory_id'] ?? null;
        $data['selected_service_subcategory_name'] = $first['subcategory_name'] ?? null;
        $data['selected_service_id'] = $first['service_id'] ?? null;
        $data['selected_service_name'] = $first['service_name'] ?? null;
        $data['selected_service_summary'] = collect($data['selected_services'])->implode(', ');
        $data['technical_estimates'] = $estimates;
        $data['technical_estimate'] = $this->aggregateEstimate($estimates);

        return $data;
    }

    protected function prepareForValidation(): void
    {
        $serviceIds = (array) $this->input('selected_service_ids', []);

        if ($serviceIds === [] && filled($this->input('selected_service_id'))) {
            $serviceIds = [$this->input('selected_service_id')];
        }

        $this->merge([
            'selected_service_ids' => array_values(array_filter($serviceIds, fn (mixed $id): bool => filled($id))),
            'service_item_selections' => collect((array) $this->input('service_item_selections', []))
                ->mapWithKeys(fn (mixed $value, mixed $key): array => [(string) $key => $value])
                ->all(),
        ]);
    }

    private function selectedServices(): Collection
    {
        $ids = collect((array) $this->input('selected_service_ids', []))
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        return CatalogService::query()
            ->whereIn('id', $ids)
            ->with([
                'subcategory.category',
                'items.options.prices.currency',
                'items.options.prices.taxRate',
            ])
            ->get()
            ->sortBy(fn (CatalogService $service): int => $ids->search($service->id))
            ->values();
    }

    private function rawSelections(): Collection
    {
        return collect((array) $this->input('service_item_selections', []))
            ->filter(fn (mixed $value): bool => filled($value))
            ->mapWithKeys(fn (mixed $value, mixed $key): array => [(string) $key => (int) $value]);
    }

    private function selectedOptionMap(CatalogService $service, Collection $rawSelections): Collection
    {
        return $service->items->mapWithKeys(function ($item) use ($rawSelections): array {
            $option = $item->options->firstWhere('id', $rawSelections->get((string) $item->id));

            if ($option) {
                $option->setRelation('item', $item);
            }

            return [$item->id => $option];
        });
    }

    private function aggregateEstimate(array $estimates): array
    {
        $calculated = collect($estimates)->where('status', 'calculated')->values();

        if ($calculated->isEmpty()) {
            return ['status' => 'not_available', 'reason' => 'No hay reglas tecnicas configuradas para los servicios seleccionados.', 'quotes' => $estimates];
        }

        $sum = fn (string $key): float => round((float) $calculated->sum(fn (array $quote): float => (float) data_get($quote, "budget.{$key}", 0)), 2);

        $firstQuote = $calculated->first();

        return [
            'status' => 'calculated',
            'currency' => 'EUR',
            'technical_service' => data_get($firstQuote, 'technical_service'),
            'library' => data_get($firstQuote, 'library'),
            'effort' => data_get($firstQuote, 'effort'),
            'budget' => [
                'base_total' => $sum('base_total'),
                'technical_base' => $sum('technical_base'),
                'selected_services_subtotal' => $sum('selected_services_subtotal'),
                'selected_services_tax' => $sum('selected_services_tax'),
                'estimated_total_before_tax' => $sum('estimated_total_before_tax'),
                'estimated_total' => $sum('estimated_total'),
            ],
            'selected_option_charges' => $calculated
                ->pluck('selected_option_charges')
                ->flatten(1)
                ->values()
                ->all(),
            'selected_extra_fees' => $calculated
                ->pluck('selected_extra_fees')
                ->flatten(1)
                ->values()
                ->all(),
            'quotes' => $estimates,
            'calculated_at' => now()->toIso8601String(),
        ];
    }
}
