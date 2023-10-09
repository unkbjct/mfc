<?php

namespace App\Filament\Pages;

use Filament\Panel;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $navigationLabel = 'Панель приборов';

    protected static ?string $title = 'Панель приборов';

    protected int | string | array $columnSpan = 'full';

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->widgets([]);
    }
}
