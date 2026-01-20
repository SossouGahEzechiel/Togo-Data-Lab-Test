<template>
	<div class="space-y-6">
		<!-- Header avec titre et bouton -->
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ filteredVehicles.length }} véhicule(s) trouvé(s)
				</h1>
			</div>
			<button @click="openCreateModal" class="btn-primary inline-flex items-center gap-x-2">
				<PlusCircleIcon class="w-5 h-5" />
				Ajouter un véhicule
			</button>
		</div>

		<!-- Filtres et recherche -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow-sm p-4 space-y-4">
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
				<!-- Champ de recherche -->
				<div class="md:col-span-2">
					<div class="relative">
						<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
							<MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
						</div>
						<input v-model="searchQuery" type="text" placeholder="Rechercher un véhicule..."
							class="input-field pl-10" />
					</div>
				</div>

				<!-- Filtre par statut -->
				<div>
					<select v-model="selectedStatus" class="input-field">
						<option value="">Tous les statuts</option>
						<option v-for="status in vehicleStatuses" :key="status" :value="status">
							{{ status }}
						</option>
					</select>
				</div>

				<!-- Filtre par type -->
				<div>
					<select v-model="selectedType" class="input-field">
						<option value="">Tous les types</option>
						<option v-for="type in vehicleTypes" :key="type" :value="type">
							{{ type }}
						</option>
					</select>
				</div>
			</div>

			<!-- Filtres actifs -->
			<div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
				<span class="text-sm text-gray-500 dark:text-gray-400">Filtres :</span>
				<button v-if="selectedStatus" @click="selectedStatus = ''"
					class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
					{{ selectedStatus }}
					<XMarkIcon class="w-3 h-3" />
				</button>
				<button v-if="selectedType" @click="selectedType = ''"
					class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
					{{ selectedType }}
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
						<TruckIcon class="w-8 h-8 text-green-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ vehicles.length }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<CheckCircleIcon class="w-8 h-8 text-green-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disponibles</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ availableCount }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<WrenchIcon class="w-8 h-8 text-yellow-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">En réparation</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ underRepairCount }}</p>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="flex items-center">
					<div class="flex-shrink-0">
						<LockClosedIcon class="w-8 h-8 text-sky-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Réservés</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ reservedCount }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Liste des véhicules (Mobile: Cards, Desktop: Table) -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
			<!-- Vue mobile (Cards) -->
			<div class="lg:hidden space-y-4">
				<div v-if="isLoading" class="text-center py-8">
					<div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
					<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Chargement des véhicules...</p>
				</div>

				<div v-else-if="filteredVehicles.length === 0" class="text-center py-8">
					<TruckIcon class="mx-auto h-12 w-12 text-gray-400" />
					<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun véhicule</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
						{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par ajouter un véhicule' }}
					</p>
					<div class="mt-6">
						<button @click="openCreateModal" type="button" class="btn-primary inline-flex items-center gap-x-2">
							<PlusCircleIcon class="w-5 h-5" />
							Ajouter un véhicule
						</button>
					</div>
				</div>

				<div v-else class="space-y-4">
					<VehicleCard v-for="vehicle in filteredVehicles" :key="vehicle.id" :vehicle="vehicle" @edit="openEditModal"
						@delete="confirmDelete" />
				</div>
			</div>

			<!-- Vue desktop (Table) -->
			<VehicleListView :isLoading="isLoading" :filteredVehicles="filteredVehicles" :searchQuery="searchQuery"
				:openEditModal="openEditModal" :openViewModal="openViewModal" :confirmDelete="confirmDelete" />

			<!-- Pagination -->
			<Paginator :filtered-data-count="filteredVehicles.length" :endIndex="endIndex" :pagesToShow="pagesToShow"
				v-model:currentPage="currentPage" v-model:totalPages="totalPages" v-model:startIndex="startIndex" />

			<!-- Modals -->
			<VehicleModal :is-open="isModalOpen" :vehicle="selectedVehicle" :mode="modalMode" @close="closeModal"
				@saved="handleVehicleSaved" />

			<!-- Modal de confirmation de suppression -->
			<VehicleDeleteModal v-if="vehicleToDelete" :is-open="isDeleteModalOpen" :vehicle="vehicleToDelete"
				@close="closeDeleteModal" @confirmed="handleDeleteConfirmed" />
		</div>
	</div>
</template>

<script setup lang="ts">
import {
	PlusCircleIcon,
	MagnifyingGlassIcon,
	XMarkIcon,
	TruckIcon,
	CheckCircleIcon,
	WrenchIcon,
	XCircleIcon,
	PencilSquareIcon,
	EyeIcon,
	TrashIcon,
	ChevronLeftIcon,
	ChevronRightIcon,
	LockClosedIcon
} from '@heroicons/vue/24/outline'

import { usePageTitle } from '~/composables/usePageTitle'
import { useVehicleStore } from '~/stores/VehicleStore'
import VehicleModal from '~/components/vehicles/VehicleModal.vue'
import VehicleCard from '~/components/vehicles/VehicleCard.vue'
import VehicleStatusBadge from '~/components/vehicles/VehicleStatusBadge.vue'
import VehicleDeleteModal from '~/components/vehicles/VehicleDeleteModal.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { initVehicleForm, type Vehicle } from '~/types/Vehicle'
import { VehicleStatusEnum, vehicleStatuses, vehicleTypes } from '~/types/VehicleEnums'
import Paginator from '~/components/partials/Paginator.vue'
import VehicleListView from '../../components/vehicles/VehicleListView.vue'

useHead({ title: "Gestion du parc auto-mobile" });
const { setPageTitle } = usePageTitle();
const vehicleStore = useVehicleStore();
const { vehicles, isLoading } = storeToRefs(vehicleStore);

// État du composant
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedType = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// État pour les modals
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedVehicle = ref<Vehicle | null>(null)
const vehicleToDelete = ref<Vehicle | null>(null)

const filteredVehicles = computed(() => {
	return vehicles.value.filter(vehicle => {
		const matchesSearch = !searchQuery.value ||
			vehicle.brand.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			vehicle.model.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			vehicle.registrationNumber.toLowerCase().includes(searchQuery.value.toLowerCase())

		const matchesStatus = !selectedStatus.value || vehicle.status === selectedStatus.value
		const matchesType = !selectedType.value || vehicle.type === selectedType.value

		return matchesSearch && matchesStatus && matchesType
	})
})

const availableCount = computed(() =>
	vehicles.value.filter(v => v.status === VehicleStatusEnum.AVAILABLE).length
)
const underRepairCount = computed(() =>
	vehicles.value.filter(v => v.status === VehicleStatusEnum.UNDER_REPAIR).length
)
const reservedCount = computed(() =>
	vehicles.value.filter(v => v.status === VehicleStatusEnum.RESERVED).length
)

const hasActiveFilters = computed(() =>
	!!selectedStatus.value || !!selectedType.value || !!searchQuery.value
)

// Pagination
const totalPages = computed(() =>
	Math.ceil(filteredVehicles.value.length / itemsPerPage.value)
)
const startIndex = computed(() =>
	(currentPage.value - 1) * itemsPerPage.value
)
const endIndex = computed(() =>
	startIndex.value + itemsPerPage.value
)
const paginatedVehicles = computed(() =>
	filteredVehicles.value.slice(startIndex.value, endIndex.value)
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
const openCreateModal = () => {
	selectedVehicle.value = initVehicleForm()
	modalMode.value = 'create'
	isModalOpen.value = true
}

const openEditModal = (vehicle: Vehicle) => {
	selectedVehicle.value = { ...vehicle }
	modalMode.value = 'edit'
	isModalOpen.value = true
}

const openViewModal = (vehicle: Vehicle) => {
	// Navigation vers la page de détails
	navigateTo(AppUrl.parameterize(AppUrl.VEHICLE_DETAILS, vehicle.id))
}

const closeModal = () => {
	isModalOpen.value = false
	selectedVehicle.value = null
}

const handleVehicleSaved = () => {
	closeModal()
	loadVehicles()
}

const confirmDelete = (vehicle: Vehicle) => {
	vehicleToDelete.value = vehicle
	isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
	isDeleteModalOpen.value = false
	vehicleToDelete.value = null
}

const handleDeleteConfirmed = async () => {
	if (vehicleToDelete.value) {
		try {
			await vehicleStore.delete(vehicleToDelete.value.id)
		} catch (error) {
			console.error('Erreur lors de la suppression:', error)
		}
	}
	closeDeleteModal()
}

const clearFilters = () => {
	searchQuery.value = ''
	selectedStatus.value = ''
	selectedType.value = ''
	currentPage.value = 1
}

const loadVehicles = async () => {
	try {
		await vehicleStore.findAll()
	} catch (error) {
		console.error('Erreur lors du chargement des véhicules:', error)
	}
}

// Watchers
watch([searchQuery, selectedStatus, selectedType], () => {
	currentPage.value = 1
})

// Lifecycle
onMounted(() => {
	setPageTitle('Gestion des véhicules', 'Liste et gestion du parc automobile')
	loadVehicles()
})
</script>
