<template>
	<div class="lg:hidden space-y-4 p-4">
		<div v-if="filteredReservations.length === 0" class="text-center py-8">
			<CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
			<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune réservation</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
				{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer une réservation' }}
			</p>
		</div>

		<div v-else class="space-y-4">
			<div v-for="reservation in paginatedReservations" :key="reservation.id"
				class="bg-white dark:bg-dark-800 rounded-lg shadow p-4">
				<div class="flex items-start justify-between">
					<div class="flex items-start space-x-4">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
								<BriefcaseIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
							</div>
						</div>
						<div class="flex-1 min-w-0">
							<div class="flex items-center justify-between">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white">
									{{ reservation.mission?.label || 'Mission non spécifiée' }}
								</h3>
								<ReservationStatusBadge :status="reservation.status" />
							</div>
							<div class="mt-1">
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									{{ reservation.vehicle ?
										`${reservation.vehicle.brand} ${reservation.vehicle.model}` :
										'Véhicule non assigné' }}
								</p>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									Conducteur: {{ reservation.driver?.fullName || 'Non assigné' }}
								</p>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									{{ formatDate(reservation.from) }} → {{ formatDate(reservation.to) }}
								</p>
								<p v-if="reservation.returnDate" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
									Retour: {{ formatDate(reservation.returnDate) }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-dark-700 pt-4">
					<div class="flex items-center space-x-3">
						<template v-if="isManageMode">
							<template v-if="reservation.status === 'En attente'">
								<div class="flex items-center space-x-2">
									<button @click="validateReservation(reservation)"
										class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
										Valider
									</button>
									<button @click="rejectReservation(reservation)"
										class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
										Rejeter
									</button>
								</div>
							</template>
						</template>
						<template v-else>
							<button @click="openEditModal(reservation)"
								class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
								Modifier
							</button>
							<button @click=""
								class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium">
								Détails
							</button>
							<button @click="openDeleteModal(reservation)"
								class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
								Supprimer
							</button>
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { BriefcaseIcon, CalendarIcon } from '@heroicons/vue/24/outline';
import type { Reservation } from '~/types/Reservation';
import ReservationStatusBadge from './ReservationStatusBadge.vue';

defineProps<{
	filteredReservations: Reservation[];
	isManageMode: boolean;
	searchQuery: string;
	paginatedReservations: Reservation[];
	validateReservation: (reservation: Reservation) => void;
	rejectReservation: (reservation: Reservation) => void;
	openEditModal: (reservation: Reservation) => void;
	openDeleteModal: (reservation: Reservation) => void;
}>()
</script>
