<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$user = new User;
		$param = [
			'name'=>'admin',
			'email'=>'admin@example.net',
			'password'=>Hash::make("password"),
			'role'=>'admin',
		];
		$user->fill($param)->save();

		$user = new User;
		$param = [
			'name'=>'tester',
			'email'=>'tester@example.net',
			'password'=>Hash::make("password"),
			'role'=>'user',
		];
		$user->fill($param)->save();
	}
}