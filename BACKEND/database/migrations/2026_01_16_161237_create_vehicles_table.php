<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('vehicles', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('brand', 31)->comment("Marque");
			$table->string('model', 31)->comment("Modèle du véhicule");
			$table->string('type', 31)->comment("Type du véhicule");
			$table->string('registration_number', 15)->comment("Numéro d'immatriculation");
			$table->timestamp('registration_date')->comment("Date d'enregistrement");
			$table->integer('seats_count')->comment("Nombre de places");
			$table->string('status', 31)->comment("Statut du véhicule");
			$table->text('reason')->nullable()->comment("Justificatif de l'état du véhicule");
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('vehicles');
	}
};
