<?php

namespace App\Filament\Widgets;

use App\Models\Brief;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BriefsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Briefs recibidos', Brief::count())
                ->description('Total acumulado'),
            Stat::make('Este mes', Brief::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count())
                ->description('Entradas del mes actual'),
            Stat::make('Envios publicos', Brief::whereNull('user_id')->count())
                ->description('Sin cuenta vinculada'),
        ];
    }
}
