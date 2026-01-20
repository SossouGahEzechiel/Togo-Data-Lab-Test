<template>
	<div class="bg-white dark:bg-dark-800 rounded-lg shadow-sm p-4 space-y-4">
		<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
			<!-- Recherche -->
			<div class="md:col-span-2">
				<div class="relative">
					<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
						<MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
					</div>
					<input v-model="searchQuery" type="text" placeholder="Rechercher une réservation..."
						class="input-field pl-10" />
				</div>
			</div>

			<!-- Filtre par statut -->
			<div>
				<select v-model="statusFilter" class="input-field">
					<option value="">Tous les statuts</option>
					<option v-for="status in reservationStatuses" :key="status" :value="status">
						{{ status }}
					</option>
				</select>
			</div>

			<!-- Filtre par véhicule -->
			<div>
				<select v-model="vehicleFilter" class="input-field">
					<option value="">Tous les véhicules</option>
					<option v-for="vehicle in uniqueVehicles" :key="vehicle.id" :value="vehicle.id">
						{{ vehicle.brand }} {{ vehicle.model }}
					</option>
				</select>
			</div>
		</div>

		<!-- Filtres actifs -->
		<div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
			<span class="text-sm text-gray-500 dark:text-gray-400">Filtres :</span>

			<button v-if="statusFilter" @click="statusFilter = ''"
				class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
				{{ statusFilter }}
				<XMarkIcon class="w-3 h-3" />
			</button>

			<button v-if="vehicleFilter" @click="vehicleFilter = ''"
				class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
				{{ getVehicleLabel(vehicleFilter) }}
				<XMarkIcon class="w-3 h-3" />
			</button>

			<button v-if="hasActiveFilters" @click="clearFilters"
				class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
				Effacer tous les filtres
			</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import type { ReservationStatusEnum } from '~/types/ReservationEnum';

// Props en lecture seule
defineProps<{
	reservationStatuses: ReservationStatusEnum[];
	uniqueVehicles: any[];
	hasActiveFilters: boolean;
	getVehicleLabel: (vehicleId: string) => string;
	clearFilters: () => void;
}>();

// Modèles bidirectionnels
const searchQuery = defineModel<string>('searchQuery', { required: true });
const statusFilter = defineModel<string>('statusFilter', { required: true });
const vehicleFilter = defineModel<string>('vehicleFilter', { required: true });
</script>