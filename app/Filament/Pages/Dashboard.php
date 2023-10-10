<?php

namespace App\Filament\Pages;

use Filament\Panel;

class Dashboard extends \Filament\Pages\Dashboard
{

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected int | string | array $columnSpan = 'full';

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->widgets([]);
    }
}
