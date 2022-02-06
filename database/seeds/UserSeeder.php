<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 20)->create();
        User::create([
            'first_name' => 'mohamed alaa',
            'last_name' => 'mohamed',
            'email' => 'mohamed@gmail.com',
            'password' => Hash::make('123456'),
            // 'password_confirm' => '123456',

        ]);
        $roles = Role::all();

        $users = User::all();
        foreach ($users as $user) {
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $roles->random()->id
            ]);
        }
    }
}
