<template>
	<div class="space-y-6">
		<!-- Header avec titre et bouton -->
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des véhicules</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
					{{ filteredVehicles.length }} véhicule(s) trouvé(s)
				</p>
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
						<XCircleIcon class="w-8 h-8 text-red-500" />
					</div>
					<div class="ml-4">
						<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Indisponibles</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ unavailableCount }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Liste des véhicules (Mobile: Cards, Desktop: Table) -->
		<div class="card">
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
			<div class="hidden lg:block">
				<div class="overflow-x-auto">
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
											<div
												class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
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
											class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
											title="Supprimer">
											<TrashIcon class="w-5 h-5" />
										</button>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Pagination -->
			<div v-if="filteredVehicles.length > 0"
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
							<span class="font-medium">{{ Math.min(endIndex, filteredVehicles.length) }}</span> sur
							<span class="font-medium">{{ filteredVehicles.length }}</span> résultats
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

		<!-- Modals -->
		<VehicleModal :is-open="isModalOpen" :vehicle="selectedVehicle" :mode="modalMode" @close="closeModal"
			@saved="handleVehicleSaved" />

		<!-- Modal de confirmation de suppression -->
		<VehicleDeleteModal v-if="vehicleToDelete" :is-open="isDeleteModalOpen" :vehicle="vehicleToDelete"
			@close="closeDeleteModal" @confirmed="handleDeleteConfirmed" />
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
	ChevronRightIcon
} from '@heroicons/vue/24/outline'

import { usePageTitle } from '~/composables/usePageTitle'
import { useVehicleStore } from '~/stores/VehicleStore'
import VehicleModal from '~/components/vehicles/VehicleModal.vue'
import VehicleCard from '~/components/vehicles/VehicleCard.vue'
import VehicleStatusBadge from '~/components/vehicles/VehicleStatusBadge.vue'
import VehicleDeleteModal from '~/components/vehicles/VehicleDeleteModal.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { initVehicleForm, type Vehicle } from '~/types/Vehicle'
import { vehicleStatuses, vehicleTypes } from '~/types/VehicleEnums'

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
	vehicles.value.filter(v => v.status === 'Disponible').length
)
const underRepairCount = computed(() =>
	vehicles.value.filter(v => v.status === 'En réparation').length
)
const unavailableCount = computed(() =>
	vehicles.value.filter(v => v.status === 'Indisponible').length
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
		await vehicleStore.getAll()
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
