<?php
namespace App\Filament\Console\Pages;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms;
use App\Models\User;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        // $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }


    // protected function createUser(array $data): User
    // {
    //     return User::create([
    //         'email' => $data['email'],
    //         'password' => bcrypt($data['password']),
    //         // 'name' => $data['name'], // 这里可以移除 name
    //     ]);
    // }
}
