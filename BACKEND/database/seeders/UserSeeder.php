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
		collect([
			[
				'first_name' => 'Kodjo',
				'last_name'  => 'Mensah',
				'email'     => 'kodjo.mensah@organisation.gouv.tg',
				'role'      => UserRoleEnum::ADMIN,
				'phone'     => '+22890011223',
				'password'  => Hash::make('password123'),
				'is_active' => false
			],
			[
				'first_name' => 'Afi',
				'last_name'  => 'Tchalla',
				'email'     => 'afi.tchalla@organisation.gouv.tg',
				'role'      => UserRoleEnum::EMPLOYEE,
				'phone'     => '+22891004567',
				'password'  => Hash::make('password123'),
				'is_active' => false
			],
			[
				'first_name' => 'Yawovi',
				'last_name'  => 'Kossi',
				'email'     => 'yawovi.kossi@organisation.gouv.tg',
				'role'      => UserRoleEnum::DRIVER,
				'phone'     => '+22892007891',
				'password'  => Hash::make('password123'),
				'is_active' => true
			],
			[
				'first_name' => 'Essowè',
				'last_name'  => 'Adomah',
				'email'     => 'essowe.adomah@organisation.gouv.tg',
				'role'      => UserRoleEnum::EMPLOYEE,
				'phone'     => '+22893005678',
				'password'  => Hash::make('password123'),
				'is_active' => true
			],
			[
				'first_name' => 'Akouvi',
				'last_name'  => 'Sodjinou',
				'email'     => 'akouvi.sodjinou@organisation.gouv.tg',
				'role'      => UserRoleEnum::DRIVER,
				'phone'     => '+22894003456',
				'password'  => Hash::make('password123'),
				'is_active' => true
			],
		])->each(fn(array $user) => User::query()->create($user));
	}
}
