<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'Admin BTQR',
            'email' => 'admin@btqr.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        \App\Models\Siswa::create([
            'name' => 'Siswa BTQR',
            'email' => 'siswa@btqr.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_active' => true,
        ]);

        \App\Models\Pengajar::create([
            'name' => 'Pengajar BTQR',
            'email' => 'pengajar@btqr.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_active' => true,
        ]);
    }
}
