<?php

namespace Database\Seeders;

use App\Enums\VehicleStatusEnum;
use App\Enums\VehicleTypeEnum;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
	public function run(): void
	{
		collect([
			[
				'brand' => 'Toyota',
				'model' => 'Hiace',
				'type' => VehicleTypeEnum::MINIBUS->value,
				'registration_number' => 'TG G/A 1024',
				'registration_date' => '2023-06-15',
				'seats_count' => 15,
				'status' => VehicleStatusEnum::AVAILABLE->value,
				'reason' => null,
			],
			[
				'brand' => 'Nissan',
				'model' => 'Urvan',
				'type' => VehicleTypeEnum::MINIBUS->value,
				'registration_number' => 'TG G/B 0456',
				'registration_date' => '2022-11-03',
				'seats_count' => 12,
				'status' => VehicleStatusEnum::UNDER_REPAIR->value,
				'reason' => 'Panne moteur nécessitant une intervention technique.',
			],
			[
				'brand' => 'Hyundai',
				'model' => 'County',
				'type' => VehicleTypeEnum::BUS->value,
				'registration_number' => 'TG G/C 3310',
				'registration_date' => '2021-09-20',
				'seats_count' => 30,
				'status' => VehicleStatusEnum::SUSPENDED->value,
				'reason' => 'Véhicule temporairement suspendu pour raisons administratives.',
			],
			[
				'brand' => 'Toyota',
				'model' => 'Land Cruiser',
				'type' => VehicleTypeEnum::SUV->value,
				'registration_number' => 'TG G/D 8891',
				'registration_date' => '2024-02-10',
				'seats_count' => 5,
				'status' => VehicleStatusEnum::AVAILABLE->value,
				'reason' => null,
			],
			[
				'brand' => 'Mitsubishi',
				'model' => 'L200',
				'type' => VehicleTypeEnum::PICKUP->value,
				'registration_number' => 'TG G/E 2175',
				'registration_date' => '2023-01-05',
				'seats_count' => 5,
				'status' => VehicleStatusEnum::AVAILABLE->value,
				'reason' => null,
			],
		])->each(fn(array $vehicle) => Vehicle::query()->create($vehicle));
	}
}
