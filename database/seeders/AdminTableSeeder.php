<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$admin = new Admin;
		$param = [
			'admin_name'=>'admin',
			'admin_email'=>'admin@example.net',
			'password'=>Hash::make("password"),
		];
		$admin->fill($param)->save();
    }
}
