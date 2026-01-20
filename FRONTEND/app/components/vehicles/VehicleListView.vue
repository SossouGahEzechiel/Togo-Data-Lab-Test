<template>
	<div class="hidden lg:block overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
			<thead class="bg-gray-50 dark:bg-dark-700">
				<tr>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Véhicule
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Immatriculation
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Type
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Places
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Statut
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						Actions
					</th>
				</tr>
			</thead>
			<tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
				<tr v-if="isLoading">
					<td colspan="6" class="px-6 py-8 text-center">
						<div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
						<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Chargement des véhicules...</p>
					</td>
				</tr>
				<tr v-else-if="filteredVehicles.length === 0">
					<td colspan="6" class="px-6 py-8 text-center">
						<TruckIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun véhicule</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par ajouter un véhicule' }}
						</p>
					</td>
				</tr>
				<tr v-else v-for="vehicle in filteredVehicles" :key="vehicle.id">
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="flex items-center">
							<div class="flex-shrink-0 h-10 w-10">
								<div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
									<TruckIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
								</div>
							</div>
							<div class="ml-4">
								<div class="text-sm font-medium text-gray-900 dark:text-white">
									{{ vehicle.brand }} {{ vehicle.model }}
								</div>
								<div class="text-sm text-gray-500 dark:text-gray-400">
									{{ vehicle.registrationDate }}
								</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="text-sm text-gray-900 dark:text-white font-mono">
							{{ vehicle.registrationNumber }}
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<span
							class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
							{{ vehicle.type }}
						</span>
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
						{{ vehicle.seatsCount }} place(s)
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<VehicleStatusBadge :status="vehicle.status" />
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
						<div class="flex items-center space-x-3">
							<button @click="openEditModal(vehicle)"
								class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
								title="Modifier">
								<PencilSquareIcon class="w-5 h-5" />
							</button>
							<button @click="openViewModal(vehicle)"
								class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
								title="Voir détails">
								<EyeIcon class="w-5 h-5" />
							</button>
							<button @click="confirmDelete(vehicle)"
								class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Supprimer">
								<TrashIcon class="w-5 h-5" />
							</button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup lang="ts">
import { EyeIcon, PencilSquareIcon, TrashIcon, TruckIcon } from '@heroicons/vue/24/outline';
import type { Vehicle } from '~/types/Vehicle';
import VehicleStatusBadge from './VehicleStatusBadge.vue';

defineProps<{
	isLoading: boolean;
	filteredVehicles: Vehicle[];
	searchQuery: string;
	openEditModal: (vehicle: Vehicle) => void;
	openViewModal: (vehicle: Vehicle) => void;
	confirmDelete: (vehicle: Vehicle) => void;
}>()
</script>
