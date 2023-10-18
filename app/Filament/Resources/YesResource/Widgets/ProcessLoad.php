<?php

namespace App\Filament\Resources\YResource\Widgets;

use App\Models\Load;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\Widget;

class ProcessLoad extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $load = Load::latest()->first();
        return $load->status == "processing" ? true : false;
    }

    protected function getStats(): array
    {
        // $success = Report::where("status", "Выдано")->get();
        // $cancel = Report::where("status", "Перенаправлено")->get();
        return [
            Stat::make('', '')
                ->description('Импорт данных происходит в данный момент, в фоновом режиме')
                // ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
        ];
    }
}
