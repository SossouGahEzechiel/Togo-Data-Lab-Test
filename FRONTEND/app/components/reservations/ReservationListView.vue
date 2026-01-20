<template>
	<div class="hidden lg:block overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
			<thead class="bg-gray-50 dark:bg-dark-700">
				<tr>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						MISSION
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						VÉHICULE
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						CONDUCTEUR
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						PÉRIODE
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						STATUT
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						ACTIONS
					</th>
				</tr>
			</thead>
			<tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
				<tr v-if="filteredReservations.length === 0">
					<td colspan="6" class="px-6 py-8 text-center">
						<CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune réservation</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer une réservation' }}
						</p>
					</td>
				</tr>
				<tr v-for="reservation in paginatedReservations" :key="reservation.id">
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="flex items-center">
							<div class="flex-shrink-0 h-10 w-10">
								<div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
									<BriefcaseIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
								</div>
							</div>
							<div class="ml-4">
								<div class="text-sm font-medium text-gray-900 dark:text-white">
									{{ reservation.mission?.label || 'Mission non spécifiée' }}
								</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div v-if="reservation.vehicle" class="text-sm text-gray-900 dark:text-white">
							{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }}
						</div>
						<div v-else class="text-sm text-gray-500 dark:text-gray-400">
							Non assigné
						</div>
						<div class="text-xs text-gray-500 dark:text-gray-400">
							{{ reservation.vehicle?.registrationNumber || '' }}
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div v-if="reservation.driver" class="text-sm text-gray-900 dark:text-white">
							{{ reservation.driver.fullName }}
						</div>
						<div v-else class="text-sm text-gray-500 dark:text-gray-400">
							Non assigné
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="text-sm text-gray-900 dark:text-white">
							{{ formatDate(reservation.from) }}
						</div>
						<div class="text-xs text-gray-500 dark:text-gray-400">
							→ {{ formatDate(reservation.to) }}
						</div>
						<div v-if="reservation.returnDate" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
							Retour: {{ formatDate(reservation.returnDate) }}
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<ReservationStatusBadge :status="reservation.status" />
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
						<div class="flex items-center space-x-2">
							<!-- Boutons de validation/rejet pour les réservations en attente -->
							<template v-if="isManageMode">
								<template v-if="reservation.status === 'En attente'">
									<button @click="validateReservation(reservation)"
										class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1"
										title="Valider">
										<CheckCircleIcon class="w-5 h-5" />
									</button>
									<button @click="rejectReservation(reservation)"
										class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
										title="Rejeter">
										<XCircleIcon class="w-5 h-5" />
									</button>
								</template>
							</template>

							<!-- Boutons standard -->
							<template v-else>
								<button @click="openEditModal(reservation)"
									class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 p-1"
									title="Modifier">
									<PencilSquareIcon class="w-5 h-5" />
								</button>
								<button @click=""
									class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 p-1"
									title="Voir détails">
									<EyeIcon class="w-5 h-5" />
								</button>
								<button @click="openDeleteModal(reservation)"
									class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
									title="Supprimer">
									<TrashIcon class="w-5 h-5" />
								</button>
							</template>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup lang="ts">
import { BriefcaseIcon, CalendarIcon, CheckCircleIcon, EyeIcon, PencilSquareIcon, TrashIcon, XCircleIcon } from '@heroicons/vue/24/outline';
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
