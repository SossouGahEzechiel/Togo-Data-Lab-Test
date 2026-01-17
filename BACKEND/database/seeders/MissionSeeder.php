<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
	public function run(): void
	{
		collect([
			[
				'label' => "Recensement administratif de la population",
				'description' => "Organisation et conduite d'opérations de collecte de données démographiques afin de mettre à jour les registres administratifs nationaux.",
				'from' => "2025-01-10",
				'to' => "2025-03-30"
			],
			[
				'label' => "Contrôle et supervision des infrastructures routières",
				'description' => "Inspection des routes nationales et régionales afin de vérifier leur conformité aux normes de sécurité et d'identifier les besoins de réhabilitation.",
				'from' => "2025-04-01",
				'to' => "2025-04-20"
			],
			[
				'label' => "Campagne nationale de sensibilisation sanitaire",
				'description' => "Mise en œuvre d'actions de communication et de proximité pour informer les populations sur les mesures de prévention sanitaire.",
				'from' => "2025-05-05",
				'to' => "2025-06-05"
			],
			[
				'label' => "Audit administratif des services déconcentrés",
				'description' => "Évaluation du fonctionnement des services administratifs régionaux afin d'améliorer la qualité du service public.",
				'from' => "2025-06-15",
				'to' => "2025-07-10"
			],
			[
				'label' => "Formation des agents publics à la dématérialisation",
				'description' => "Renforcement des capacités des agents de l'administration publique sur l'utilisation des plateformes numériques de gestion.",
				'from' => "2025-08-01",
				'to' => "2025-08-15"
			]
		])->each(fn(array $mission) => Mission::query()->create($mission));
	}
}
