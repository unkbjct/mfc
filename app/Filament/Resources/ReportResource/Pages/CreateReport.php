<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Filament\Resources\ReportResource\Pages;
use App\Imports\ReportImport;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;

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
                ->successRedirectUrl($this->getResource()::getUrl('progress'))
                ->action(function () {
                    $tmpFile = array_shift($this->data['report']);
                    $fileName = $tmpFile->getFileName();
                    Storage::disk('local')
                        ->move(
                            'livewire-tmp/' . $fileName,
                            'public/' . $fileName
                        );
                    Excel::queueImport(new ReportImport, $fileName, 'public');
                    // redirect($this->getResource()::getUrl('progress'));
                }),
            action::make('cancel')
                ->label('Отмена')
                ->color('gray')
                ->action(fn () => redirect($this->previousUrl ?? $this->getResource()::getUrl('index'))),

        ];
    }

    function handleRecordCreation(array $data): Model
    {
        $reportName = $data['report'];
        Excel::import(new ReportImport, $reportName, 'public');
        redirect()->route('asd');
        return static::getModel()::create([
            'department' => 'for remove',
            'service_name' => '1',
            'services_count' => 1,
            'registration_datetime' => '1',
            'issue_datetime' => '1',
            'done_by' => '1',
            'status' => '1',
        ]);
    }
    // protected function getRedirectUrl(): string
    // {
    //     // return $this->getResource()::getUrl('index');
    // }
    protected function getCreatedNotification(): ?Notification
    {
        Report::where('department', '=', 'for remove')->delete();
        return Notification::make()
            ->success()
            ->title('Успех')
            ->body('Отчет был успешно загружен.');
    }
}
