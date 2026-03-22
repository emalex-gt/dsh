<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--name=} {--email=} {--password=}';

    protected $description = 'Crea un usuario administrador para el panel de Filament';

    public function handle(): int
    {
        $name = $this->option('name') ?: text(
            label: 'Nombre del administrador',
            required: true,
        );

        $email = $this->option('email') ?: text(
            label: 'Email del administrador',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['email' => $value],
                ['email' => ['required', 'email', 'max:255', 'unique:users,email']],
            )->errors()->first('email'),
        );

        $password = $this->option('password') ?: password(
            label: 'Contrasena del administrador',
            required: true,
        );

        Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ],
        )->validate();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->info("Administrador creado: {$user->email}");

        return self::SUCCESS;
    }
}
