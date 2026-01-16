<?php

use App\Models\{Mission, User, Vehicle};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('reservations', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamp('from')->comment("Date de début d'utilisation du véhicule");
			$table->timestamp('to')->nullable()->comment("Date de fin d'utilisation du véhicule");
			$table->string('condition')->nullable()->comment("État du véhicule à son retour");
			$table->string('status')->comment("Statut du véhicule");
			$table->timestamp('return_date')->nullable()->comment("Date de retour du véhicule");

			$table->foreignIdFor(Mission::class)->comment("Mission liée");
			$table->foreignIdFor(Vehicle::class)->comment("Véhicule utilisé");
			$table->foreignIdFor(User::class)->comment("Auteur de la réservation");
			$table->uuid('driver_id')->nullable()->comment("Le chauffeur qui a conduit le véhicule");
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('reservations');
	}
};
