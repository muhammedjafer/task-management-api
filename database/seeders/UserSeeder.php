<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product_owner = RoleEnum::PRODUCT_OWNER->value;
        $developer = RoleEnum::DEVELOPER->value;
        $tester = RoleEnum::TESTER->value;

        $users = [
            [
                'name' => 'Product owner',
                'email' => 'productowner@system.com',
                'email_verified_at' => now(),
                'role' => $product_owner,
                'password' => config('app.env') == 'production' ? Hash::make('productowner2024production') : Hash::make('productowner2024staging')
            ],
            [
                'name' => 'Developer',
                'email' => 'developer@system.com',
                'email_verified_at' => now(),
                'role' => $developer,
                'password' => Hash::make('developer2024system')
            ],
            [
                'name' => 'Tester',
                'email' => 'tester@system.com',
                'email_verified_at' => now(),
                'role' => $tester,
                'password' => Hash::make('tester2024system')
            ],
        ];

        foreach ($users as $user) {
            $email = $user['email'];
            
            User::updateOrCreate(
                ['email' => $email], 
                $user
            );
        }
    }
}
