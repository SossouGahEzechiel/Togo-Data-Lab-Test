<template>
	<div class="space-y-6" v-if="vehicle">
		<!-- En-tête de page -->
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ vehicle.brand }} {{ vehicle.model }}
					</h1>
					<p class="text-gray-600 dark:text-gray-400 mt-1">
						Détails du véhicule et historique des réservations
					</p>
				</div>
				<div class="flex items-center space-x-3">
					<button @click="editVehicle" class="btn-primary flex items-center gap-2 whitespace-nowrap">
						<PencilSquareIcon class="w-5 h-5" />
						Modifier
					</button>
					<button @click="goBack"
						class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
						Retour
					</button>
				</div>
			</div>
			<div class="mt-4 border-b border-gray-200 dark:border-dark-600"></div>
		</div>

		<!-- Section principale à 2 colonnes -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Colonne gauche : Informations du véhicule -->
			<div class="lg:col-span-2 space-y-6">
				<!-- Card principale du véhicule -->
				<div class="card">
					<div class="flex flex-col md:flex-row gap-6">
						<!-- Images du véhicule -->
						<div class="md:w-1/3">
							<div class="space-y-4">
								<!-- Image principale -->
								<div v-if="vehicle.images?.length > 0" class="relative">
									<img :src="getImageUrl(vehicle.images[0])" :alt="`${vehicle.brand} ${vehicle.model}`"
										class="w-full h-64 object-cover rounded-lg" />
									<div v-if="vehicle.images.length > 1"
										class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
										+{{ vehicle.images.length - 1 }} image(s)
									</div>
								</div>
								<div v-else
									class="w-full h-64 bg-gray-200 dark:bg-dark-700 rounded-lg flex items-center justify-center">
									<TruckIcon class="h-16 w-16 text-gray-400" />
								</div>

								<!-- Miniatures des images -->
								<div v-if="vehicle.images?.length > 1" class="grid grid-cols-4 gap-2">
									<div v-for="(image, index) in vehicle.images.slice(1, 5)" :key="index"
										@click="setMainImage(index + 1)" class="cursor-pointer">
										<img :src="getImageUrl(image)" :alt="`Image ${index + 2}`"
											class="w-full h-16 object-cover rounded hover:opacity-80 transition-opacity" />
									</div>
								</div>
							</div>
						</div>

						<!-- Informations du véhicule -->
						<div class="md:w-2/3">
							<div class="space-y-6">
								<!-- Titre et statut -->
								<div class="flex items-start justify-between">
									<div>
										<h2 class="text-xl font-bold text-gray-900 dark:text-white">
											{{ vehicle.brand }} {{ vehicle.model }}
										</h2>
										<p class="text-gray-500 dark:text-gray-400 mt-1">
											{{ vehicle.type }}
										</p>
									</div>
									<VehicleStatusBadge :status="vehicle.status" size="lg" />
								</div>

								<!-- Détails techniques -->
								<div class="grid grid-cols-2 gap-4">
									<div class="space-y-1">
										<p class="text-sm text-gray-500 dark:text-gray-400">Immatriculation</p>
										<p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">
											{{ vehicle.registrationNumber }}
										</p>
									</div>
									<div class="space-y-1">
										<p class="text-sm text-gray-500 dark:text-gray-400">Date d'immatriculation</p>
										<p class="text-lg font-semibold text-gray-900 dark:text-white">
											{{ formatDate(vehicle.registrationDate) }}
										</p>
									</div>
									<div class="space-y-1">
										<p class="text-sm text-gray-500 dark:text-gray-400">Nombre de places</p>
										<div class="flex items-center space-x-2">
											<UserIcon class="h-5 w-5 text-gray-400" />
											<p class="text-lg font-semibold text-gray-900 dark:text-white">
												{{ vehicle.seatsCount }} place(s)
											</p>
										</div>
									</div>
									<div class="space-y-1">
										<p class="text-sm text-gray-500 dark:text-gray-400">Type de véhicule</p>
										<p class="text-lg font-semibold text-gray-900 dark:text-white">
											{{ vehicle.type }}
										</p>
									</div>
								</div>

								<!-- Raison (si applicable) -->
								<div v-if="vehicle.reason" class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
									<div class="flex">
										<ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
										<div class="ml-3">
											<h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
												Information importante
											</h3>
											<div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
												<p>{{ vehicle.reason }}</p>
											</div>
										</div>
									</div>
								</div>

								<!-- Statistiques -->
								<div class="border-t border-gray-200 dark:border-dark-700 pt-6">
									<h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
										Statistiques
									</h3>
									<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
										<div class="text-center p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
											<p class="text-2xl font-bold text-gray-900 dark:text-white">
												{{ totalReservations }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">Réservations totales</p>
										</div>
										<div class="text-center p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
											<p class="text-2xl font-bold text-green-600 dark:text-green-400">
												{{ completedReservations }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">Terminées</p>
										</div>
										<div class="text-center p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
											<p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
												{{ upcomingReservations }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">À venir</p>
										</div>
										<div class="text-center p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
											<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
												{{ activeReservations }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">En cours</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Historique des réservations -->
				<div class="card">
					<div class="flex items-center justify-between mb-6">
						<div>
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">
								Historique des réservations
							</h2>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
								{{ filteredReservations.length }} réservation(s) trouvée(s)
							</p>
						</div>
						<div class="flex items-center space-x-4">
							<div class="relative">
								<MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
								<input v-model="searchQuery" type="text" placeholder="Rechercher..."
									class="input-field pl-10 pr-4 py-2 w-48" />
							</div>
							<select v-model="statusFilter" class="input-field py-2">
								<option value="">Tous les statuts</option>
								<option value="En attente">En attente</option>
								<option value="Validée">Validées</option>
								<option value="Rejetée">Rejetées</option>
								<option value="Passée">Passées</option>
							</select>
						</div>
					</div>

					<div v-if="isLoading" class="flex justify-center items-center py-8">
						<Spinner :is-loading="true" size="md" />
					</div>

					<div v-else-if="filteredReservations.length === 0" class="text-center py-8">
						<CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune réservation</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							Ce véhicule n'a pas encore été réservé
						</p>
					</div>

					<div v-else>
						<!-- Tableau desktop -->
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
									<tr v-for="reservation in paginatedReservations" :key="reservation.id">
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="flex items-center">
												<div class="flex-shrink-0 h-10 w-10">
													<div
														class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
														<BriefcaseIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
													</div>
												</div>
												<div class="ml-4">
													<div class="text-sm font-medium text-gray-900 dark:text-white">
														{{ reservation.mission?.label || 'Mission non spécifiée' }}
													</div>
													<div class="text-sm text-gray-500 dark:text-gray-400">
														Demandé par: {{ reservation.user?.fullName || 'N/A' }}
													</div>
												</div>
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
												{{ formatDateTime(reservation.from) }}
											</div>
											<div class="text-xs text-gray-500 dark:text-gray-400">
												→ {{ formatDateTime(reservation.to) }}
											</div>
											<div v-if="reservation.returnDate" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
												Retour: {{ formatDateTime(reservation.returnDate) }}
											</div>
										</td>
										<td class="px-6 py-4 whitespace-nowrap">
											<ReservationStatusBadge :status="reservation.status" />
										</td>
										<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
											<div class="flex items-center space-x-2">
												<button @click="viewReservation(reservation)"
													class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
													title="Voir détails">
													<EyeIcon class="w-5 h-5" />
												</button>
												<button @click="editReservation(reservation)"
													class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
													title="Modifier">
													<PencilSquareIcon class="w-5 h-5" />
												</button>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- Liste mobile -->
						<div class="lg:hidden space-y-4">
							<div v-for="reservation in paginatedReservations" :key="reservation.id"
								class="border border-gray-200 dark:border-dark-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
								<div class="flex items-start justify-between">
									<div class="flex-1">
										<div class="flex items-center space-x-3">
											<div class="flex-shrink-0">
												<div
													class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
													<BriefcaseIcon class="h-5 w-5 text-primary-600 dark:text-primary-400" />
												</div>
											</div>
											<div class="min-w-0 flex-1">
												<div class="flex items-center justify-between">
													<p class="text-sm font-medium text-gray-900 dark:text-white">
														{{ reservation.mission?.label || 'Mission' }}
													</p>
													<ReservationStatusBadge :status="reservation.status" size="sm" />
												</div>
												<div class="mt-1 space-y-1">
													<p class="text-xs text-gray-500 dark:text-gray-400">
														{{ reservation.driver?.fullName || 'Conducteur non assigné' }}
													</p>
													<p class="text-xs text-gray-500 dark:text-gray-400">
														{{ formatDateTime(reservation.from) }} → {{ formatDateTime(reservation.to) }}
													</p>
													<p v-if="reservation.returnDate" class="text-xs text-yellow-600 dark:text-yellow-400">
														Retour: {{ formatDateTime(reservation.returnDate) }}
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div
									class="mt-4 flex items-center justify-end space-x-3 border-t border-gray-200 dark:border-dark-700 pt-4">
									<button @click="viewReservation(reservation)"
										class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
										Détails
									</button>
									<button @click="editReservation(reservation)"
										class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium">
										Modifier
									</button>
								</div>
							</div>
						</div>

						<!-- Pagination -->
						<div v-if="totalPages > 1"
							class="flex items-center justify-between border-t border-gray-200 dark:border-dark-700 px-4 py-3 sm:px-6 mt-6">
							<div class="flex-1 flex justify-between sm:hidden">
								<button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
									class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-dark-700">
									Précédent
								</button>
								<button @click="currentPage = Math.min(totalPages, currentPage + 1)"
									:disabled="currentPage === totalPages"
									class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-dark-700">
									Suivant
								</button>
							</div>
							<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
								<div>
									<p class="text-sm text-gray-700 dark:text-gray-300">
										Affichage de <span class="font-medium">{{ startIndex + 1 }}</span> à
										<span class="font-medium">{{ Math.min(endIndex, filteredReservations.length) }}</span> sur
										<span class="font-medium">{{ filteredReservations.length }}</span> résultats
									</p>
								</div>
								<div>
									<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
										<button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
											class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-400 dark:hover:bg-dark-700 disabled:opacity-50">
											<ChevronLeftIcon class="h-5 w-5" />
										</button>
										<button v-for="page in pagesToShow" :key="page" @click="currentPage = page" :class="[
											'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
											currentPage === page
												? 'z-10 bg-primary-50 border-primary-500 text-primary-600 dark:bg-primary-900 dark:border-primary-400 dark:text-primary-300'
												: 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-dark-800 dark:border-dark-600 dark:text-gray-400 dark:hover:bg-dark-700'
										]">
											{{ page }}
										</button>
										<button @click="currentPage = Math.min(totalPages, currentPage + 1)"
											:disabled="currentPage === totalPages"
											class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-400 dark:hover:bg-dark-700 disabled:opacity-50">
											<ChevronRightIcon class="h-5 w-5" />
										</button>
									</nav>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Colonne droite : Actions et informations -->
			<div class="space-y-6">
				<!-- Actions rapides -->
				<div class="card">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						Actions
					</h2>
					<div class="space-y-3">
						<button @click="createReservation"
							class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
							<PlusCircleIcon class="w-5 h-5 mr-2" />
							Nouvelle réservation
						</button>
						<button @click="changeVehicleStatus"
							class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							<Cog6ToothIcon class="w-5 h-5 mr-2" />
							Changer le statut
						</button>
						<button @click="viewReports"
							class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							<ChartBarIcon class="w-5 h-5 mr-2" />
							Voir les rapports
						</button>
						<button @click="deleteVehicle"
							class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
							<TrashIcon class="w-5 h-5 mr-2" />
							Supprimer le véhicule
						</button>
					</div>
				</div>

				<!-- Prochaines réservations -->
				<div class="card">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-lg font-semibold text-gray-900 dark:text-white">
							Prochaines réservations
						</h2>
						<span class="text-xs text-gray-500 dark:text-gray-400">
							{{ upcomingReservationsList.length }}
						</span>
					</div>
					<div v-if="upcomingReservationsList.length === 0" class="text-center py-4">
						<p class="text-sm text-gray-500 dark:text-gray-400">Aucune réservation à venir</p>
					</div>
					<div v-else class="space-y-3">
						<div v-for="reservation in upcomingReservationsList.slice(0, 3)" :key="reservation.id"
							class="p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
							<div class="flex items-center justify-between">
								<div>
									<p class="text-sm font-medium text-gray-900 dark:text-white">
										{{ formatDate(reservation.from) }}
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										{{ reservation.mission?.label || 'Mission' }}
									</p>
								</div>
								<ReservationStatusBadge :status="reservation.status" size="sm" />
							</div>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
								{{ reservation.driver?.fullName || 'Conducteur non assigné' }}
							</p>
						</div>
					</div>
				</div>

				<!-- Calendrier du mois -->
				<div class="card">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						Disponibilité ce mois
					</h2>
					<div class="space-y-2">
						<div class="flex items-center justify-between">
							<span class="text-sm text-gray-600 dark:text-gray-400">Jours disponibles</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ availableDays }}/{{ totalDays }}</span>
						</div>
						<div class="w-full bg-gray-200 dark:bg-dark-700 rounded-full h-2">
							<div :style="{ width: `${(availableDays / totalDays) * 100}%` }" class="bg-green-500 h-2 rounded-full">
							</div>
						</div>
						<div class="pt-4 border-t border-gray-200 dark:border-dark-700">
							<p class="text-xs text-gray-500 dark:text-gray-400">
								{{ nextAvailableDate ? `Prochaine disponibilité: ${formatDate(nextAvailableDate)}` : 'Disponible immédiatement' }}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Chargement -->
	<div v-else-if="isLoading" class="flex justify-center items-center min-h-[400px]">
		<Spinner :is-loading="true" size="lg" />
	</div>

	<!-- Erreur -->
	<div v-else-if="error" class="text-center py-12">
		<ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-400" />
		<h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Véhicule non trouvé</h3>
		<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
			Le véhicule que vous recherchez n'existe pas ou a été supprimé.
		</p>
		<div class="mt-6">
			<button @click="goBack" class="btn-primary">
				Retour à la liste
			</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import {
	TruckIcon,
	PencilSquareIcon,
	UserIcon,
	ExclamationTriangleIcon,
	MagnifyingGlassIcon,
	BriefcaseIcon,
	EyeIcon,
	CalendarIcon,
	ChevronLeftIcon,
	ChevronRightIcon,
	PlusCircleIcon,
	Cog6ToothIcon,
	ChartBarIcon,
	TrashIcon
} from '@heroicons/vue/24/outline'

import { usePageTitle } from '~/composables/usePageTitle'
import { useVehicleStore } from '~/stores/VehicleStore'
import { useReservationStore } from '~/stores/ReservationStore'
import type { Vehicle } from '~/types/Vehicle'
import VehicleStatusBadge from '~/components/vehicles/VehicleStatusBadge.vue'
import ReservationStatusBadge from '~/components/reservations/ReservationStatusBadge.vue'
import Spinner from '~/components/partials/Spinner.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Reservation } from '~/types/Reservation'

const route = useRoute()
const router = useRouter()
const { setPageTitle } = usePageTitle()

const vehicleStore = useVehicleStore()
const reservationStore = useReservationStore()

const vehicle = ref<Vehicle | null>(null)
const isLoading = ref(true)
const error = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Computed properties
const vehicleReservations = computed(() => {
	return vehicle.value?.reservations || []
})

const filteredReservations = computed(() => {
	let filtered = vehicleReservations.value

	// Filtre par recherche
	if (searchQuery.value) {
		const query = searchQuery.value.toLowerCase()
		filtered = filtered.filter(reservation =>
			reservation.mission?.label?.toLowerCase().includes(query) ||
			reservation.driver?.fullName?.toLowerCase().includes(query) ||
			reservation.user?.fullName?.toLowerCase().includes(query)
		)
	}

	// Filtre par statut
	if (statusFilter.value) {
		filtered = filtered.filter(reservation => reservation.status === statusFilter.value)
	}

	// Tri par date de début (plus récent en premier)
	return filtered.sort((a, b) => new Date(b.from).getTime() - new Date(a.from).getTime())
})

// Statistiques
const totalReservations = computed(() => vehicleReservations.value.length)
const completedReservations = computed(() =>
	vehicleReservations.value.filter(r => r.status === 'Passée').length
)
const upcomingReservations = computed(() =>
	vehicleReservations.value.filter(r => r.status === 'Validée' && new Date(r.from) > new Date()).length
)
const activeReservations = computed(() =>
	vehicleReservations.value.filter(r => r.status === 'Validée' && new Date(r.from) <= new Date() && new Date(r.to) >= new Date()).length
)

// Réservations à venir (pour le panneau latéral)
const upcomingReservationsList = computed(() => {
	return vehicleReservations.value
		.filter(r => r.status === 'Validée' && new Date(r.from) > new Date())
		.sort((a, b) => new Date(a.from).getTime() - new Date(b.from).getTime())
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredReservations.value.length / itemsPerPage.value))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)
const endIndex = computed(() => startIndex.value + itemsPerPage.value)
const paginatedReservations = computed(() => filteredReservations.value.slice(startIndex.value, endIndex.value))

const pagesToShow = computed(() => {
	const pages = []
	const maxPages = 5

	if (totalPages.value <= maxPages) {
		for (let i = 1; i <= totalPages.value; i++) {
			pages.push(i)
		}
	} else {
		let start = Math.max(1, currentPage.value - 2)
		let end = Math.min(totalPages.value, start + maxPages - 1)

		if (end - start + 1 < maxPages) {
			start = end - maxPages + 1
		}

		for (let i = start; i <= end; i++) {
			pages.push(i)
		}
	}

	return pages
})

// Disponibilité
const availableDays = computed(() => {
	// Simuler des données de disponibilité
	// En réalité, vous calculeriez cela en fonction des réservations
	return 20
})

const totalDays = computed(() => {
	const now = new Date()
	const year = now.getFullYear()
	const month = now.getMonth()
	return new Date(year, month + 1, 0).getDate()
})

const nextAvailableDate = computed(() => {
	const upcoming = upcomingReservationsList.value
	if (upcoming.length > 0) {
		return upcoming[upcoming.length - 1].to
	}
	return null
})

// Méthodes utilitaires
const getImageUrl = (image: any) => {
	if (image instanceof File) {
		return URL.createObjectURL(image)
	}
	return image?.path ? `${import.meta.env.VITE_API_FILE_PATH}${image.path}` : ''
}

const setMainImage = (index: number) => {
	// Cette fonctionnalité nécessiterait une logique plus complexe
	// pour vraiment changer l'image principale
	console.log('Changer image principale:', index)
}

const formatDate = (dateString: string) => {
	return new Date(dateString).toLocaleDateString('fr-FR', {
		day: '2-digit',
		month: '2-digit',
		year: 'numeric'
	})
}

const formatDateTime = (dateString: string) => {
	return new Date(dateString).toLocaleString('fr-FR', {
		day: '2-digit',
		month: '2-digit',
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	})
}

// Navigation
const goBack = () => {
	naviagteTo(AppUrl.VEHICLES_LISTE);
}

const editVehicle = () => {
	if (vehicle.value) {
		router.push(`/vehicles/edit/${vehicle.value.id}`)
	}
}

const viewReservation = (reservation: Reservation) => {
	router.push(`/reservations/${reservation.id}`)
}

const editReservation = (reservation: Reservation) => {
	router.push(`/reservations/edit/${reservation.id}`)
}

const createReservation = () => {
	router.push(`/reservations/new?vehicleId=${vehicle.value?.id}`)
}

const changeVehicleStatus = () => {
	// Ouvrir un modal ou rediriger vers une page de modification du statut
	console.log('Changer statut du véhicule')
}

const viewReports = () => {
	router.push(`/reports/vehicle/${vehicle.value?.id}`)
}

const deleteVehicle = async () => {
	if (!vehicle.value) return

	if (confirm(`Êtes-vous sûr de vouloir supprimer le véhicule ${vehicle.value.brand} ${vehicle.value.model} ?`)) {
		try {
			await vehicleStore.delete(vehicle.value.id)
			router.push('/vehicles')
		} catch (error) {
			console.error('Erreur lors de la suppression:', error)
		}
	}
}

// Charger les données
const loadVehicle = async () => {
	isLoading.value = true
	error.value = false

	try {
		const vehicleId = route.params.id as string
		const data = await vehicleStore.findOne(vehicleId)

		if (data) {
			vehicle.value = data
			setPageTitle(`${data.brand} ${data.model}`, 'Détails du véhicule')
		} else {
			error.value = true
		}
	} catch (err) {
		console.error('Erreur lors du chargement du véhicule:', err)
		error.value = true
	} finally {
		isLoading.value = false
	}
}

// Watchers
watch([searchQuery, statusFilter], () => {
	currentPage.value = 1
})

// Lifecycle
onMounted(() => {
	loadVehicle()
})
</script>