<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class Register extends \Filament\Pages\Auth\Register
{

    public function getTitle(): string|Htmlable
    {
        return 'Регистрация';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Создание аккаунта';
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label('Авторизуйтесь')
            ->url(filament()->getLoginUrl());
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent()
                            ->label('Имя'),
                        $this->getEmailFormComponent()
                            ->label('Электронная почта'),
                        $this->getPasswordFormComponent()
                            ->label('Пароль'),
                        $this->getPasswordConfirmationFormComponent()
                            ->label('Подтвреждение пароля'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label('Зарегестрироваться')
            ->submit('register');
    }
}
