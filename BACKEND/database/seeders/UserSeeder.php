<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		User::query()->insert([
			[
				'firstName' => 'Kodjo',
				'lastName'  => 'Mensah',
				'email'     => 'kodjo.mensah@organisation.gouv.tg',
				'role'      => UserRoleEnum::ADMIN,
				'phone'     => '+22890011223',
				'password'  => Hash::make('password123'),
			],
			[
				'firstName' => 'Afi',
				'lastName'  => 'Tchalla',
				'email'     => 'afi.tchalla@organisation.gouv.tg',
				'role'      => UserRoleEnum::EMPLOYEE,
				'phone'     => '+22891004567',
				'password'  => Hash::make('password123'),
			],
			[
				'firstName' => 'Yawovi',
				'lastName'  => 'Kossi',
				'email'     => 'yawovi.kossi@organisation.gouv.tg',
				'role'      => UserRoleEnum::DRIVER,
				'phone'     => '+22892007891',
				'password'  => Hash::make('password123'),
			],
			[
				'firstName' => 'Essowè',
				'lastName'  => 'Adomah',
				'email'     => 'essowe.adomah@organisation.gouv.tg',
				'role'      => UserRoleEnum::EMPLOYEE,
				'phone'     => '+22893005678',
				'password'  => Hash::make('password123'),
			],
			[
				'firstName' => 'Akouvi',
				'lastName'  => 'Sodjinou',
				'email'     => 'akouvi.sodjinou@organisation.gouv.tg',
				'role'      => UserRoleEnum::DRIVER,
				'phone'     => '+22894003456',
				'password'  => Hash::make('password123'),
			],
		]);
	}
}
