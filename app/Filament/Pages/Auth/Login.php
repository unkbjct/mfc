<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;

class Login extends \Filament\Pages\Auth\Login
{

    protected static ?string $navigationLabel = 'Авторизация';

    protected static ?string $title = 'Авторизация';

    public function getTitle(): string|Htmlable
    {
        return 'Авторизация';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Авторизация';
    }

    // public function registerAction(): Action
    // {
    //     return Action::make('registration')
    //         ->link()
    //         ->label('Создать аккаунт')
    //         ->url(filament()->getRegistrationUrl());
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                    ->label('Электронная почта'),
                $this->getPasswordFormComponent()
                    ->label('Пароль'),
                $this->getRememberFormComponent()
                    ->label('Оставаться в системе'),
            ]);
    }

    function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Войти')
            ->submit('authenticate');
    }
}
