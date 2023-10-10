<?php

namespace App\Filament\Resources\YesResource\Widgets;

use App\Models\Report;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class LatestReports extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Report::query()->latest();
    }

    protected  function getTableHeading(): string|Htmlable|null
    {
        return 'Последнии записи';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('department')
                ->sortable()
                ->label('МФЦ, в котором зарегистрировано дело'),
            TextColumn::make('service_name')
                ->limit(50)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
                    return $state;
                })
                ->sortable()
                ->label('Наименование услуги'),
            TextColumn::make('services_count')
                ->sortable()
                ->label('Число услуг в деле'),
            TextColumn::make('registration_datetime')
                ->sortable()
                ->label('Дата регистрации'),
            TextColumn::make('issue_datetime')
                ->sortable()
                ->label('Дата выдачи дела'),
            TextColumn::make('done_by')
                ->limit(50)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
                    return $state;
                })
                ->sortable()
                ->label('Наименование ОГВ, исполняющего услугу'),
            TextColumn::make('status')
                ->sortable()
                ->label('Текущий статус услуги'),
        ];
    }
}
