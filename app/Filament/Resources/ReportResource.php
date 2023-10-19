<?php

namespace App\Filament\Resources;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = "Отчет";
    protected static ?string $pluralModelLabel = "Отчеты";



    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                FileUpload::make('report')
                    ->label('Отчет')
                    ->acceptedFileTypes([
                        '.csv',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel'
                    ])
                    ->required(true),
            ])->columns(1);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->searchable('Поиск по наименованию услуги')
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
            ])
            ->filters([])->paginated([10, 25, 50, 100, 500, 'all'])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->emptyStateHeading('Отчеты отсутствуют');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
        ];
    }
}
