<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientBriefRequest;
use App\Models\Brief;
use App\Models\Calculation;
use App\Models\CatalogService;
use App\Models\ServiceCategory;
use App\Services\BriefEstimateConfirmationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClientBriefController extends Controller
{
    public function edit(Request $request): View
    {
        return view('briefs.edit', [
            'brief' => request()->user()?->briefs()->latest('submitted_at')->latest('id')->first(),
            'preselectedServiceIds' => $this->preselectedServiceIds($request),
            'serviceCatalog' => $this->serviceCatalog(),
            'technicalQuoteRules' => $this->technicalQuoteRules(),
        ]);
    }

    public function show(Request $request): View
    {
        return view('briefs.show', [
            'brief' => $request->user()->briefs()->latest('submitted_at')->latest('id')->first(),
        ]);
    }

    public function showClientData(Request $request): View
    {
        return view('client.data', [
            'user' => $request->user(),
        ]);
    }

    public function thanks(): View
    {
        return view('briefs.thanks');
    }

    public function update(StoreClientBriefRequest $request, BriefEstimateConfirmationService $confirmationService): RedirectResponse
    {
        $pending = $confirmationService->createPending($request->validatedBriefData());

        return redirect()->route('brief.review', $pending['token']);
    }

    private function serviceCatalog(): array
    {
        $dshPercentages = Calculation::query()
            ->with('service.subcategory')
            ->get()
            ->filter(fn (Calculation $calculation): bool => $calculation->service?->subcategory !== null)
            ->groupBy(fn (Calculation $calculation): string => "{$calculation->service->subcategory->code}|{$calculation->service->name}")
            ->map(fn ($calculations): float => (float) $calculations->first()->dsh_percentage);

        return ServiceCategory::query()
            ->where('type', 'A_SERVICIOS')
            ->where('active', true)
            ->with([
                'subcategories' => fn ($query) => $query
                    ->where('active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'services' => fn ($serviceQuery) => $serviceQuery
                            ->where('active', true)
                            ->orderBy('sort_order')
                            ->with([
                                'items' => fn ($itemQuery) => $itemQuery
                                    ->where('active', true)
                                    ->orderBy('sort_order')
                                    ->with([
                                        'options' => fn ($optionQuery) => $optionQuery
                                            ->where('active', true)
                                            ->orderBy('sort_order')
                                            ->with([
                                                'prices' => fn ($priceQuery) => $priceQuery
                                                    ->where('active', true)
                                                    ->orderBy('price_type')
                                                    ->with(['currency', 'taxRate']),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ])
            ->get()
            ->map(function (ServiceCategory $category) use ($dshPercentages): array {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'subcategories' => $category->subcategories->map(function ($subcategory) use ($dshPercentages): array {
                        return [
                            'id' => $subcategory->id,
                            'name' => $subcategory->name,
                            'code' => $subcategory->code,
                            'services' => $subcategory->services->map(function ($service) use ($subcategory, $dshPercentages): array {
                                $dshPercentage = (float) $dshPercentages->get("{$subcategory->code}|{$service->name}", 0);

                                return [
                                    'id' => $service->id,
                                    'name' => $service->name,
                                    'code' => $service->code,
                                    'description' => $service->description,
                                    'items' => $service->items->map(function ($item) use ($dshPercentage): array {
                                        return [
                                            'id' => $item->id,
                                            'name' => $item->name,
                                            'description' => $item->description,
                                            'item_type' => $item->item_type,
                                            'is_required' => $item->is_required,
                                            'options' => $item->options->map(function ($option) use ($item, $dshPercentage): array {
                                                return [
                                                    'id' => $option->id,
                                                    'name' => $option->name,
                                                    'description' => $option->description,
                                                    'prices' => $option->prices
                                                        ->map(function ($price) use ($item, $dshPercentage): array {
                                                            $basePrice = (float) $price->price;
                                                            $priceWithDsh = $item->applies_dsh
                                                                ? round($basePrice * (1 + ($dshPercentage / 100)), 2)
                                                                : $basePrice;

                                                            return [
                                                                'price_type' => $price->price_type,
                                                                'price' => $priceWithDsh,
                                                                'tax_rate' => (float) ($price->taxRate?->rate ?? 0),
                                                            ];
                                                        })
                                                        ->values()
                                                        ->all(),
                                                    'price_summary' => $option->prices
                                                        ->map(function ($price) use ($item, $dshPercentage): string {
                                                            $priceWithDsh = $item->applies_dsh
                                                                ? (float) $price->price * (1 + ($dshPercentage / 100))
                                                                : (float) $price->price;
                                                            $amount = number_format($priceWithDsh, 2, ',', '.');
                                                            $symbol = $price->currency?->symbol ?: '€';
                                                            $label = match ($price->price_type) {
                                                                'FIRST_YEAR' => 'Primer año',
                                                                'RENEWAL' => 'Renovación',
                                                                'ONE_TIME' => 'Pago único',
                                                                default => $price->price_type,
                                                            };

                                                            $tax = $price->taxRate?->name ? " + {$price->taxRate->name}" : '';

                                                            return "{$label}: {$amount} {$symbol}{$tax}";
                                                        })
                                                        ->values()
                                                        ->all(),
                                                ];
                                            })->values()->all(),
                                        ];
                                    })->values()->all(),
                                ];
                            })->values()->all(),
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function preselectedServiceIds(Request $request): array
    {
        $requestedServices = collect(explode(',', (string) $request->query('servicios', '')))
            ->map(fn (string $service): string => mb_strtolower(trim($service)))
            ->filter()
            ->unique()
            ->values();

        if ($requestedServices->isEmpty()) {
            return [];
        }

        $services = CatalogService::query()
            ->where('active', true)
            ->whereHas('subcategory.category', fn ($query) => $query
                ->where('type', 'A_SERVICIOS')
                ->where('active', true))
            ->with('subcategory')
            ->get();

        return $requestedServices
            ->map(function (string $requestedService) use ($services): ?int {
                $service = $services->first(fn (CatalogService $service): bool => mb_strtolower("{$service->subcategory->code}.{$service->code}") === $requestedService);

                return $service?->id;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function technicalQuoteRules(): array
    {
        return Calculation::query()
            ->with(['service.subcategory', 'libraryType', 'extraFees.extraFee.taxRate'])
            ->get()
            ->filter(fn (Calculation $calculation): bool => $calculation->service && $calculation->libraryType)
            ->map(function (Calculation $calculation): array {
                $prepaidFees = $calculation->extraFees->filter(function ($fee): bool {
                    $period = mb_strtolower((string) $fee->extraFee?->period);

                    return str_contains($period, 'mes') || str_contains($period, 'month') || str_contains($period, 'ano') || str_contains($period, 'año') || str_contains($period, 'year');
                });

                return [
                    'subcategory_code' => $calculation->service->subcategory?->code,
                    'service_name' => $calculation->service->name,
                    'library_name' => $calculation->libraryType->name,
                    'technical_base' => (float) $calculation->total_final,
                    'recurring_fees' => $prepaidFees->map(function ($fee) use ($calculation): array {
                        $baseAmount = (float) ($fee->amount ?? $fee->extraFee?->amount ?? 0);
                        $amount = $fee->extraFee?->applies_dsh
                            ? round($baseAmount * (1 + ((float) $calculation->dsh_percentage / 100)), 2)
                            : $baseAmount;
                        $taxRate = (float) ($fee->extraFee?->taxRate?->rate ?? 0);

                        return [
                            'name' => mb_strtolower((string) $fee->extraFee?->name),
                            'amount' => $amount,
                            'tax_amount' => round($amount * $taxRate, 2),
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }
}
