<?php

namespace App\Filament\Resources\YResource\Widgets;

use App\Models\Load;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProcessLoad extends BaseWidget
{

    public static function canView(): bool
    {
        $load = Load::latest()->first();
        return ($load && $load->status == "processing") ? true : false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('', '')
                ->descriptionIcon('heroicon-o-clock')
                ->description('Данные импортируются')
                ->color('warning'),
            Stat::make('', '')
                ->description('Обновите страницу чтобы посмотреть, изменился ли статус загрузки')
                ->color('info')
        ];
    }
}
