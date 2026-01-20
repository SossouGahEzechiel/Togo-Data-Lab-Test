<template>
	<div class="space-y-6">
		<!-- En-tête de page -->
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
						Gestion des réservations
					</h1>
					<p class="text-gray-600 dark:text-gray-400 mt-1">
						Gestion et suivi des réservations de véhicules
					</p>
				</div>
			</div>
			<div class="mt-4 flex items-center justify-between">
				<div class="text-sm text-gray-500 dark:text-gray-400">
					{{ filteredReservations.length }} réservation(s) trouvée(s)
				</div>
			</div>
			<div class="mt-4 border-b border-gray-200 dark:border-dark-600"></div>
		</div>

		<!-- Filtres et recherche -->
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

		<!-- Statistiques -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<CalendarIcon class="w-8 h-8 text-primary-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ reservations.length }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<ClockIcon class="w-8 h-8 text-yellow-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">En attente</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ pendingCount }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<CheckCircleIcon class="w-8 h-8 text-green-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Validées</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ validatedCount }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<XCircleIcon class="w-8 h-8 text-red-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejetées</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ rejectedCount }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Liste des réservations -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
			<div v-if="reservationStore.isLoading" class="flex justify-center items-center p-8">
				<Spinner :is-loading="true" size="lg" />
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
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Liste mobile -->
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
										<div
											class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
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
											<p class="text-xs text-gray-500 dark:text-gray-400">
												Demandé par: {{ reservation.user?.fullName || 'N/A' }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
												{{ reservation.vehicle ?
													`${reservation.vehicle.brand} ${reservation.vehicle.model}` :
													'Véhicule non assigné' }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
												Conducteur: {{ reservation.driver?.firstName || 'Non assigné' }}
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
								<!-- Boutons de validation/rejet pour mobile -->
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
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Pagination -->
			<div v-if="filteredReservations.length > 0"
				class="flex items-center justify-between border-t border-gray-200 dark:border-dark-700 px-4 py-3 sm:px-6">
				<div class="flex-1 flex justify-between sm:hidden">
					<button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
						class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-300 dark:hover:bg-dark-700">
						Précédent
					</button>
					<button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
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

		<!-- Modal de confirmation de suppression -->
		<ReservationDeleteModal v-if="reservationToDelete" :is-open="isDeleteModalOpen" :reservation="reservationToDelete"
			@close="closeDeleteModal" @confirmed="handleDeleteConfirmed" />

		<!-- Modals de validation/rejet -->
		<ReservationValidateModal v-if="reservationToValidate" :is-open="isValidateModalOpen"
			:reservation="reservationToValidate" @close="closeValidateModal" @confirmed="handleValidateConfirmed" />

		<ReservationRejectModal v-if="reservationToReject" :is-open="isRejectModalOpen" :reservation="reservationToReject"
			@close="closeRejectModal" @confirmed="handleRejectConfirmed" />
	</div>
</template>

<script setup lang="ts">
import {
	MagnifyingGlassIcon,
	PlusIcon,
	CalendarIcon,
	ClockIcon,
	CheckCircleIcon,
	XCircleIcon,
	BriefcaseIcon,
	ChevronLeftIcon,
	ChevronRightIcon,
	XMarkIcon
} from '@heroicons/vue/24/outline'

import { usePageTitle } from '~/composables/usePageTitle'
import { useReservationStore } from '~/stores/ReservationStore'
import type { Reservation } from '~/types/Reservation'
import ReservationDeleteModal from '~/components/reservations/ReservationDeleteModal.vue'
import ReservationStatusBadge from '~/components/reservations/ReservationStatusBadge.vue'
import ReservationValidateModal from '~/components/reservations/ReservationValidateModal.vue'
import ReservationRejectModal from '~/components/reservations/ReservationRejectModal.vue'
import Spinner from '~/components/partials/Spinner.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { ReservationStatusEnum, reservationStatuses } from '~/types/ReservationEnum'

const { setPageTitle } = usePageTitle()
const reservationStore = useReservationStore()

// État du composant
const searchQuery = ref('')
const statusFilter = ref('')
const vehicleFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// État pour les modals
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isValidateModalOpen = ref(false)
const isRejectModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedReservation = ref<Reservation | null>(null)
const reservationToDelete = ref<Reservation | null>(null)
const reservationToValidate = ref<Reservation | null>(null)
const reservationToReject = ref<Reservation | null>(null)

// Computed properties
const reservations = computed(() => reservationStore.reservations)

const filteredReservations = computed(() => {
	console.log("reservations.value:", reservations.value);

	return reservations.value.filter(reservation => {
		const matchesSearch = !searchQuery.value ||
			(reservation.mission?.label?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
			(reservation.vehicle?.brand?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
			(reservation.vehicle?.model?.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
			(reservation.driver?.fullName?.toLowerCase().includes(searchQuery.value.toLowerCase()))

		const matchesStatus = !statusFilter.value || reservation.status === statusFilter.value

		const matchesVehicle = !vehicleFilter.value || reservation.vehicleId === vehicleFilter.value

		return matchesSearch && matchesStatus && matchesVehicle
	})
})

const pendingCount = computed(() =>
	reservations.value.filter(r => r.status === 'En attente').length
)
const validatedCount = computed(() =>
	reservations.value.filter(r => r.status === 'Validée').length
)
const rejectedCount = computed(() =>
	reservations.value.filter(r => r.status === 'Rejetée').length
)

const uniqueVehicles = computed(() => {
	const vehicles = new Map()
	reservations.value.forEach(reservation => {
		if (reservation.vehicle) {
			vehicles.set(reservation.vehicle.id, reservation.vehicle)
		}
	})
	return Array.from(vehicles.values())
})

const hasActiveFilters = computed(() =>
	!!statusFilter.value || !!vehicleFilter.value || !!searchQuery.value
)

// Pagination
const totalPages = computed(() =>
	Math.ceil(filteredReservations.value.length / itemsPerPage.value)
)
const startIndex = computed(() =>
	(currentPage.value - 1) * itemsPerPage.value
)
const endIndex = computed(() =>
	startIndex.value + itemsPerPage.value
)
const paginatedReservations = computed(() =>
	filteredReservations.value.slice(startIndex.value, endIndex.value)
)

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

// Méthodes
const formatDate = (dateString: string) => {
	return new Date(dateString).toLocaleDateString('fr-FR', {
		year: 'numeric',
		month: '2-digit',
		day: '2-digit'
	})
}

const getVehicleLabel = (vehicleId: string) => {
	const vehicle = uniqueVehicles.value.find(v => v.id === vehicleId)
	return vehicle ? `${vehicle.brand} ${vehicle.model}` : 'Véhicule'
}

const openCreateModal = () => {
	selectedReservation.value = null
	modalMode.value = 'create'
	isModalOpen.value = true
}

const openEditModal = (reservation: Reservation) => {
	selectedReservation.value = { ...reservation }
	modalMode.value = 'edit'
	isModalOpen.value = true
}

const closeModal = () => {
	isModalOpen.value = false
	selectedReservation.value = null
}

const handleReservationSaved = () => {
	closeModal()
	loadReservations()
}

const validateReservation = (reservation: Reservation) => {
	reservationToValidate.value = reservation
	isValidateModalOpen.value = true
}

const rejectReservation = (reservation: Reservation) => {
	reservationToReject.value = reservation
	isRejectModalOpen.value = true
}

const openDeleteModal = (reservation: Reservation) => {
	reservationToDelete.value = reservation
	isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
	isDeleteModalOpen.value = false
	reservationToDelete.value = null
}

const closeValidateModal = () => {
	isValidateModalOpen.value = false
	reservationToValidate.value = null
}

const closeRejectModal = () => {
	isRejectModalOpen.value = false
	reservationToReject.value = null
}

const handleDeleteConfirmed = async () => {
	if (reservationToDelete.value) {
		try {
			await reservationStore.delete(reservationToDelete.value.id)
		} catch (error) {
			console.error('Erreur lors de la suppression:', error)
		}
	}
	closeDeleteModal()
}

const handleValidateConfirmed = async () => {
	if (reservationToValidate.value) {
		try {
			await reservationStore.applyDecision(reservationToValidate.value.id, ReservationStatusEnum.VALIDATED);
		} catch (error) {
			console.error('Erreur lors de la validation:', error)
		}
	}
	closeValidateModal()
}

const handleRejectConfirmed = async () => {
	if (reservationToReject.value) {
		try {
			await reservationStore.applyDecision(reservationToReject.value.id, ReservationStatusEnum.REJECTED);
		} catch (error) {
			console.error('Erreur lors du rejet:', error)
		}
	}
	closeRejectModal()
}

const clearFilters = () => {
	searchQuery.value = ''
	statusFilter.value = ''
	vehicleFilter.value = ''
	currentPage.value = 1
}

const loadReservations = async () => {
	try {
		await reservationStore.findAll()
	} catch (error) {
		console.error('Erreur lors du chargement des réservations:', error)
	}
}

// Watchers
watch([searchQuery, statusFilter, vehicleFilter], () => {
	currentPage.value = 1
})

// Lifecycle
onMounted(() => {
	setPageTitle('Gestion des réservations', 'Gestion et suivi des réservations de véhicules')
	loadReservations()
})
</script>