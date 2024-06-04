<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the user\'s password?');
        
        $role = $this->choice(
            'What is the user\'s role?', 
            [   
                RoleEnum::TESTER->name, 
                RoleEnum::DEVELOPER->name, 
                RoleEnum::PRODUCT_OWNER->name
            ],
            0
        );

        $roleValue = RoleEnum::getValue($role);

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $roleValue,
        ], [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|int|in:'.implode(',', RoleEnum::values()),
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'role' => $roleValue,
        ]);

        $this->info('User created successfully!');
        return 0;
    }
}
