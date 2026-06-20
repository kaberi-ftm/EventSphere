<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'admin',
                'display_name' => 'Administrator'
            ],
            [
                'name' => 'executive',
                'display_name' => 'Club Executive'
            ],
            [
                'name' => 'volunteer',
                'display_name' => 'Volunteer'
            ],
            [
                'name' => 'participant',
                'display_name' => 'Participant'
            ]
        ]);
    }
}