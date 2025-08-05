<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class usuarios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'name'=> 'Paulo Antonio Vital', 
            'email' => 'pauloavital@gmail.com',
            'senha' => md5('123456789')
        ]);

        DB::table('usuarios')->insert([
            'name'=> 'Paulo A. Vital', 
            'email' => 'pauloavital2@gmail.com',
            'senha' => md5('123456789')
        ]);
    }
}
