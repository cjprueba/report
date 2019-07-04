<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() 
    {
        User::create([
        	'name' => 'cristian',
        	'email' => 'ccastro@guaranicard.com.py',
        	'password' => bcrypt(value: '12345')
        ]);
    }
}
