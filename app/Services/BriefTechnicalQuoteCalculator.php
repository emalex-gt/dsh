<?php

namespace App\Services;

use App\Models\Calculation;
use App\Models\CatalogService;
use App\Models\LibraryType;
use Illuminate\Support\Collection;

class BriefTechnicalQuoteCalculator
{
    /**
     * Builds an immutable technical estimate from the active B-team rule.
     */
    public function calculate(CatalogService $commercialService, Collection $selectedOptions): array
    {
        $selectedOptions = $selectedOptions->filter();
        $technicalService = $this->technicalServiceFor($commercialService);
        $libraryType = $this->libraryTypeFor($selectedOptions);

        if (! $technicalService || ! $libraryType) {
            return $this->unavailable(
                $commercialService,
                $technicalService,
                $libraryType,
                'Falta configurar el servicio tecnico o el tipo de biblioteca equivalente.',
            );
        }

        $calculation = Calculation::query()
            ->with(['extraFees.extraFee.taxRate', 'extraFees.extraFee.currency'])
            ->where('catalog_service_id', $technicalService->id)
            ->where('library_type_id', $libraryType->id)
            ->first();

        if (! $calculation) {
            return $this->unavailable(
                $commercialService,
                $technicalService,
                $libraryType,
                'No existe una regla de calculo B activa para esta combinacion.',
            );
        }

        $selectedOptionEntries = $this->selectedOptionEntries($selectedOptions, $calculation);
        $extras = $this->selectedRecurringFees($selectedOptions, $calculation);
        $commercialSubtotal = round($selectedOptionEntries->sum('amount') + $extras->sum('amount'), 2);
        $commercialTax = round($selectedOptionEntries->sum('tax_amount') + $extras->sum('tax_amount'), 2);
        $baseBudget = (float) $calculation->total_final;

        return [
            'status' => 'calculated',
            'currency' => 'EUR',
            'commercial_service' => [
                'id' => $commercialService->id,
                'name' => $commercialService->name,
            ],
            'technical_service' => [
                'id' => $technicalService->id,
                'name' => $technicalService->name,
                'scenario' => $calculation->scenario,
            ],
            'library' => [
                'id' => $libraryType->id,
                'name' => $libraryType->name,
                'adjustment_type' => $libraryType->adjustment_type,
                'adjustment_value' => (float) $libraryType->adjustment_value,
            ],
            'effort' => [
                'days' => $calculation->days,
                'hours_per_day' => $calculation->hours_per_day,
                'people' => $calculation->people,
                'rate_per_hour' => (float) $calculation->rate_per_hour,
                'subtotal_hours' => $calculation->subtotal_hours,
                'subtotal_amount' => (float) $calculation->subtotal_amount,
            ],
            'budget' => [
                'base_total' => (float) $calculation->base_total,
                'technical_base' => $baseBudget,
                'selected_services_subtotal' => $commercialSubtotal,
                'selected_services_tax' => $commercialTax,
                'estimated_total_before_tax' => round($baseBudget + $commercialSubtotal, 2),
                // The client sees net pricing. VAT is stated separately and not added here.
                'estimated_total' => round($baseBudget + $commercialSubtotal, 2),
            ],
            'selected_option_charges' => $selectedOptionEntries->values()->all(),
            'selected_extra_fees' => $extras->values()->all(),
            'notes' => $calculation->notes,
            'calculated_at' => now()->toIso8601String(),
        ];
    }

    private function technicalServiceFor(CatalogService $commercialService): ?CatalogService
    {
        $subcategoryCode = $commercialService->subcategory?->code;

        if (! $subcategoryCode) {
            return null;
        }

        return CatalogService::query()
            ->where('active', true)
            ->where('name', $commercialService->name)
            ->whereHas('subcategory', fn ($query) => $query
                ->where('code', $subcategoryCode)
                ->whereHas('category', fn ($categoryQuery) => $categoryQuery
                    ->where('type', 'B_EQUIPO')
                    ->where('active', true)))
            ->with('subcategory')
            ->orderBy('id')
            ->first();
    }

    private function libraryTypeFor(Collection $selectedOptions): ?LibraryType
    {
        $libraryOption = $selectedOptions->first(fn ($option) => strcasecmp($option->item?->name ?? '', 'Biblioteca') === 0);

        if (! $libraryOption) {
            return null;
        }

        return LibraryType::query()
            ->where('active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($libraryOption->name)])
            ->first();
    }

    private function selectedOptionEntries(Collection $selectedOptions, Calculation $calculation): Collection
    {
        $extraNames = $calculation->extraFees
            ->map(fn ($fee) => mb_strtolower($fee->extraFee?->name ?? ''))
            ->filter();

        return $selectedOptions
            ->reject(fn ($option) => strcasecmp($option->item?->name ?? '', 'Biblioteca') === 0)
            ->reject(fn ($option) => $extraNames->contains(mb_strtolower($option->name)))
            ->flatMap(function ($option) use ($calculation): array {
                return $option->prices
                    ->where('active', true)
                    ->whereIn('price_type', ['FIRST_YEAR', 'ONE_TIME'])
                    ->map(function ($price) use ($option, $calculation): array {
                        $baseAmount = (float) $price->price;
                        $amount = $option->item?->applies_dsh
                            ? round($baseAmount * (1 + ((float) $calculation->dsh_percentage / 100)), 2)
                            : $baseAmount;
                        $rate = (float) ($price->taxRate?->rate ?? 0);

                        return [
                            'item' => $option->item?->name,
                            'option' => $option->name,
                            'price_type' => $price->price_type,
                            'amount' => $amount,
                            'tax_rate' => $rate,
                            'tax_amount' => round($amount * $rate, 2),
                        ];
                    })
                    ->all();
            });
    }

    private function selectedRecurringFees(Collection $selectedOptions, Calculation $calculation): Collection
    {
        $selectedNames = $selectedOptions->map(fn ($option) => mb_strtolower($option->name));

        return $calculation->extraFees
            // Recurring services selected by the client are collected upfront.
            ->filter(fn ($fee) => $this->isRecurringPeriod($fee->extraFee?->period))
            ->filter(fn ($fee) => $selectedNames->contains(mb_strtolower($fee->extraFee?->name ?? '')))
            ->map(function ($fee) use ($calculation): array {
                $baseAmount = (float) ($fee->amount ?? $fee->extraFee?->amount ?? 0);
                $amount = $fee->extraFee?->applies_dsh
                    ? round($baseAmount * (1 + ((float) $calculation->dsh_percentage / 100)), 2)
                    : $baseAmount;
                $rate = (float) ($fee->extraFee?->taxRate?->rate ?? 0);

                return [
                    'name' => $fee->extraFee?->name,
                    'period' => $fee->extraFee?->period,
                    'amount' => $amount,
                    'tax_rate' => $rate,
                    'tax_amount' => round($amount * $rate, 2),
                    'notes' => $fee->notes,
                    'prepaid_with_first_charge' => true,
                ];
            });
    }

    private function isRecurringPeriod(?string $period): bool
    {
        $value = mb_strtolower(trim((string) $period));

        return str_contains($value, 'mes')
            || str_contains($value, 'month')
            || str_contains($value, 'ano')
            || str_contains($value, 'año')
            || str_contains($value, 'year');
    }

    private function unavailable(
        CatalogService $commercialService,
        ?CatalogService $technicalService,
        ?LibraryType $libraryType,
        string $reason,
    ): array {
        return [
            'status' => 'not_available',
            'currency' => 'EUR',
            'commercial_service' => ['id' => $commercialService->id, 'name' => $commercialService->name],
            'technical_service' => $technicalService ? ['id' => $technicalService->id, 'name' => $technicalService->name] : null,
            'library' => $libraryType ? ['id' => $libraryType->id, 'name' => $libraryType->name] : null,
            'reason' => $reason,
            'calculated_at' => now()->toIso8601String(),
        ];
    }
}
