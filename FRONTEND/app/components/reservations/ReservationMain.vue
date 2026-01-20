<template>
	<div class="space-y-6">
		<!-- En-tête de page -->
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
						{{ filteredReservations.length }} réservation(s) trouvée(s)
					</h1>
				</div>
			</div>
		</div>

		<ReservationFilters v-model:searchQuery="searchQuery" v-model:statusFilter="statusFilter"
			v-model:vehicleFilter="vehicleFilter" :reservationStatuses="reservationStatuses" :uniqueVehicles="uniqueVehicles"
			:hasActiveFilters="hasActiveFilters" :getVehicleLabel="getVehicleLabel" :clearFilters="clearFilters" />

		<!-- Statistiques -->
		<ReservationStatCard :reservationsCount="reservations.length" :pendingCount="pendingCount"
			:validatedCount="validatedCount" :rejectedCount="rejectedCount" />

		<!-- Liste des réservations -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
			<div v-if="reservationStore.isLoading" class="flex justify-center items-center p-8">
				<Spinner :is-loading="true" size="lg" />
			</div>

			<div v-else>
				<!-- Tableau desktop -->
				<ReservationListView :isManageMode="isManageMode" :filteredReservations="filteredReservations"
					:searchQuery="searchQuery" :paginatedReservations="paginatedReservations"
					:validateReservation="validateReservation" :rejectReservation="rejectReservation"
					:openEditModal="openEditModal" :openDeleteModal="openDeleteModal" />

				<!-- Liste mobile -->
				<ReservationCardView :isManageMode="isManageMode" :filteredReservations="filteredReservations"
					:searchQuery="searchQuery" :paginatedReservations="paginatedReservations" :openEditModal="openEditModal"
					:validateReservation="validateReservation" :rejectReservation="rejectReservation"
					:openDeleteModal="openDeleteModal" />
			</div>

			<!-- Pagination -->
			<ReservationPagination :filteredReservations="filteredReservations" :endIndex="endIndex"
				:pagesToShow="pagesToShow" v-model:currentPage="currentPage" v-model:totalPages="totalPages"
				v-model:startIndex="startIndex" />
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

import { useReservationStore } from '~/stores/ReservationStore'
import type { Reservation } from '~/types/Reservation'
import ReservationDeleteModal from '~/components/reservations/ReservationDeleteModal.vue'
import ReservationValidateModal from '~/components/reservations/ReservationValidateModal.vue'
import ReservationRejectModal from '~/components/reservations/ReservationRejectModal.vue'
import Spinner from '~/components/partials/Spinner.vue'
import { ref, computed, watch } from 'vue'
import { ReservationStatusEnum, reservationStatuses } from '~/types/ReservationEnum'
import ReservationListView from '~/components/reservations/ReservationListView.vue'
import ReservationCardView from '~/components/reservations/ReservationCardView.vue'
import ReservationPagination from '~/components/reservations/ReservationPagination.vue'
import ReservationStatCard from '~/components/reservations/ReservationStatCard.vue'
import ReservationFilters from '~/components/reservations/ReservationFilters.vue'

const props = defineProps<{
	isManageMode: boolean
}>();

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

const getVehicleLabel = (vehicleId: string) => {
	const vehicle = uniqueVehicles.value.find(v => v.id === vehicleId)
	return vehicle ? `${vehicle.brand} ${vehicle.model}` : 'Véhicule'
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
		await reservationStore.findAll(props.isManageMode ? undefined : useAuthStore().user?.id);
	} catch (error) {
		console.error('Erreur lors du chargement des réservations:', error)
	}
}

// Watchers
watch([searchQuery, statusFilter, vehicleFilter], () => {
	currentPage.value = 1
});

onMounted(()=>{
	loadReservations();
});
</script>
