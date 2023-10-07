<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;
    protected static ?string $navigationLabel = 'Custom Navigation Label';

    protected static ?string $title = "Загрузка нового отчета";

    protected static bool $canCreateAnother = false;
    protected static ?string $createLabel = 'asd';

    function getFormActions(): array
    {
        return [
            action::make('load')
                ->label('Загрузить')
                ->action(function () {
                    // dd($this);
                })->submit('asd'),
            action::make('cancel')
                ->label('Отмена')
                ->color('gray')
                ->action(fn () => redirect($this->previousUrl ?? $this->getResource()::getUrl('index'))),

        ];
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // dd($data['report']);
    //     return $data;
    // }

    
    function handleRecordCreation(array $data): Model
    {
        $reportName = $data['report'];
        
        dd($filepath = storage_path($reportName));

        return static::getModel()::create($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Успех')
            ->body('Отчет был успешно загружен.');
    }
}
