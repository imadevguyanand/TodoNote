<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TodoNote;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(TodoTableSeeder::class);
    }
}

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'id' => User::TEST_USER_ID, 
            'email' => 'test_user@gmail.com', 
            'password' => Hash::make('complexpassword')
        ]);
    }
}

class TodoTableSeeder extends Seeder
{
    public function run()
    {
        TodoNote::create([
            'user_id' => User::TEST_USER_ID, 
            'content' => 'Complete the coding challenge',
            'completed_at' => Carbon::now()
        ]);
    }
}