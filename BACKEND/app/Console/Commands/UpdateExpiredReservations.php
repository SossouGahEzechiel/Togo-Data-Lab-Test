<?php

namespace App\Console\Commands;

use App\Enums\ReservationStatusEnum;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateExpiredReservations extends Command
{
	protected $signature = 'reservations:update-expired';
	protected $description = 'Mettre à jour les réservations validées qui sont passées';

	public function handle()
	{
		$this->info('Début de la mise à jour des réservations expirées...');

		$today = Carbon::today();

		try {
			DB::beginTransaction();

			// Mise à jour en masse pour de meilleures performances
			$updated = Reservation::where('status', ReservationStatusEnum::VALIDATED)
				->whereDate('to', '<', $today)
				->update(['status' => ReservationStatusEnum::PASSED]);

			DB::commit();

			if ($updated === 0) {
				$this->info('Aucune réservation à mettre à jour.');
			} else {
				$this->info("✓ {$updated} réservation(s) mise(s) à jour avec succès.");
			}

			Log::info("Réservations expirées mises à jour", [
				'updated' => $updated,
				'date' => now()->toDateTimeString(),
			]);

			return Command::SUCCESS;
		} catch (\Exception $e) {
			DB::rollBack();

			$this->error("Erreur lors de la mise à jour : {$e->getMessage()}");

			Log::error("Erreur lors de la mise à jour des réservations expirées", [
				'error' => $e->getMessage(),
				'trace' => $e->getTraceAsString(),
			]);

			return Command::FAILURE;
		}
	}
}
