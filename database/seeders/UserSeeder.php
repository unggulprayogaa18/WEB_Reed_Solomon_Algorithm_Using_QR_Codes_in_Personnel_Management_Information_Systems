<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Carbon\Carbon; // Import Carbon for timestamps

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('users')->truncate();

        DB::table('users')->insert([
            'nama' => 'Budi Santoso',
            'email' => 'pemimpin@example.com',
            'password' => Hash::make('1'), 
            'jabatan' => 'pemimpin',
            'no_telepon' => '081234567890',
            'email_verified_at' => Carbon::now(), 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'nama' => 'Siti Aminah',
            'email' => 'admin@example.com',
            'password' => Hash::make('2'), 
            'jabatan' => 'admin',
            'no_telepon' => '087654321098',
            'email_verified_at' => Carbon::now(), 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'nama' => 'Fasya triane',
            'email' => 'fasya@example.com',
            'password' => Hash::make('3'), 
            'jabatan' => 'pegawai',
            'no_telepon' => '089876543210',
            'email_verified_at' => Carbon::now(), 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Users seeded!');
    }
}
