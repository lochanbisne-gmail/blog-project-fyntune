<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //This is the seeder table
         $user = App\User::create([
            'name'=>'admin',
            'email'=>'admin@admin.com',
            'password'=> Hash::make('password'),
            'admin'=>1,

        ]);
    }
}
