<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Jobs\StartLoad;
use App\Models\Load;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected static ?string $title = "Загрузка нового отчета";

    protected static bool $canCreateAnother = false;

    function getFormActions(): array
    {
        return [
            action::make('load')
                ->label('Загрузить')
                ->action(function () {
                    if (!sizeof($this->data['report'])) {
                        Notification::make()
                            ->title('Файл не выбран')
                            ->danger()
                            ->send();
                        return;
                    };
                    $tmpFile = array_shift($this->data['report']);
                    $fileName = $tmpFile->getFileName();
                    Storage::disk('local')
                        ->move(
                            'livewire-tmp/' . $fileName,
                            'public/' . $fileName
                        );

                    $load = new Load();
                    $load->status = "processing";
                    $load->rows = 0;
                    $load->added = 0;
                    $load->duplicates = 0;
                    $load->save();

                    StartLoad::dispatch([
                        'filename' => $fileName,
                        'loadId' => $load->id,
                    ]);

                    Notification::make()
                        ->title('Загрузка успешно начата')
                        ->body('Вы можете отслеживать отслеживать статус загрузки в дашбоард')
                        ->success()
                        ->send();

                    return redirect('../');
                }),
            action::make('cancel')
                ->label('Отмена')
                ->color('gray')
                ->action(fn () => redirect($this->previousUrl ?? $this->getResource()::getUrl('index'))),

        ];
    }
}
