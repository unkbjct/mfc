<?php

namespace App\Filament\Resources\NResource\Widgets;

use App\Models\Load;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class LatestLoads extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Последнии импорты отчетов';
    }

    protected function getTableQuery(): Builder
    {
        return Load::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            // TextColumn::make('user.email')
                // ->label('Пользователь'),
            TextColumn::make('rows')
                ->label('Всего записей'),
            TextColumn::make('added')
                ->label('Добавлено'),
            TextColumn::make('duplicates')
                ->label('Не добавленные(дубликаты)'),
            TextColumn::make('created_at')
                ->label('Дата загрузки'),
        ];
    }
}
