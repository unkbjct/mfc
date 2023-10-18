<?php

namespace App\Filament\Resources\YesResource\Widgets;

use App\Models\Report;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $reports = Report::all();
        // $success = Report::where("status", "Выдано")->get();
        // $cancel = Report::where("status", "Перенаправлено")->get();
        return [
            Stat::make('', $reports->count())
                // ->description('32k increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Выдано', $reports->where('status', '=', 'Выдано')->count()),
            Stat::make('Перенаправлено', $reports->where('status', '=', 'Перенаправлено')->count()),
            Stat::make('Передано на выдачу', $reports->where('status', '=', 'Передано на выдачу')->count()),
            Stat::make('В обработке', $reports->where('status', '=', 'В обработке')->count()),
        ];
    }
    protected static bool $isLazy = TRUE;
}
