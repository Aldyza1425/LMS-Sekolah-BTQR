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
        \App\Models\Siswa::firstOrCreate(
            ['email' => 'siswa@btqr.com'],
            [
                'name' => 'Siswa BTQR',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_active' => true,
            ]
        );

        \App\Models\Pengajar::firstOrCreate(
            ['email' => 'pengajar@btqr.com'],
            [
                'name' => 'Pengajar BTQR',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
