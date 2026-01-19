<template>
	<div class="space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des missions</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
					{{ filteredMissions.length }} mission(s) trouvé(s)
				</p>
			</div>
			<button @click="openCreateModal" class="btn-primary inline-flex items-center gap-x-2">
				<PlusCircleIcon class="w-5 h-5" />
				Ajouter une mission
			</button>
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
						<input v-model="searchQuery" type="text" placeholder="Rechercher une mission..."
							class="input-field pl-10" />
					</div>
				</div>

				<!-- Filtre par statut -->
				<div>
					<select v-model="statusFilter" class="input-field">
						<option value="">Tous les statuts</option>
						<option value="active">En cours</option>
						<option value="completed">Terminées</option>
					</select>
				</div>
			</div>

			<!-- Filtres actifs -->
			<div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
				<span class="text-sm text-gray-500 dark:text-gray-400">Filtres :</span>
				<button v-if="statusFilter" @click="statusFilter = ''"
					class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
					{{ statusFilter === 'active' ? 'En cours' : 'Terminées' }}
					<XMarkIcon class="w-3 h-3" />
				</button>
				<button v-if="hasActiveFilters" @click="clearFilters"
					class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
					Effacer tous les filtres
				</button>
			</div>
		</div>

		<!-- Statistiques -->
		<MissionStatGrid :missionsCount="missions.length" :activeMissionsCount="activeMissionsCount"
			:completedMissionsCount="completedMissionsCount" />

		<!-- Liste des missions -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
			<div v-if="missionStore.isLoading" class="flex justify-center items-center p-8">
				<Spinner :is-loading="true" size="lg" />
			</div>

			<div v-else>
				<!-- Tableau desktop -->
				<MissionListView :filteredMissions="filteredMissions" :searchQuery="searchQuery"
					:paginatedMissions="paginatedMissions" :formatDate="formatDate" :openEditModal="openEditModal"
					:openViewModal="openViewModal" :openDeleteModal="openDeleteModal" />

				<!-- Liste mobile -->
				<MissionCardView :filteredMissions="filteredMissions" :searchQuery="searchQuery"
					:paginatedMissions="paginatedMissions" :formatDate="formatDate" :openEditModal="openEditModal"
					:openViewModal="openViewModal" :openDeleteModal="openDeleteModal" />
			</div>

			<!-- Pagination -->
			<div v-if="filteredMissions.length > 0"
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
							<span class="font-medium">{{ Math.min(endIndex, filteredMissions.length) }}</span> sur
							<span class="font-medium">{{ filteredMissions.length }}</span> résultats
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

		<!-- Modal de création/édition -->
		<MissionFormModal v-if="showMissionModal" :is-open="showMissionModal" :mission="selectedMission"
			:is-edit="isEditMode" @close="closeMissionModal" @save="handleSaveMission" />

		<!-- Modal de suppression personnalisé -->
		<MissionDeleteModal :showDeleteModal="showDeleteModal" :closeDeleteModal="closeDeleteModal"
			:missionToDelete="missionToDelete" :handleDeleteMission="handleDeleteMission" :isDeleting="isDeleting" />
	</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import {
	MagnifyingGlassIcon,
	ChevronLeftIcon,
	ChevronRightIcon,
	XMarkIcon,
	PlusCircleIcon
} from '@heroicons/vue/24/outline';
import MissionFormModal from '~/components/missions/MissionFormModal.vue';
import type { Mission } from '~/types/Mission';
import { useMissionStore } from '~/stores/MissionStore';
import { usePageTitle } from '~/composables/usePageTitle';
import Spinner from '~/components/partials/spinner.vue';
import MissionDeleteModal from '~/components/missions/MissionDeleteModal.vue'
import MissionStatGrid from '~/components/missions/MissionStatGrid.vue'
import MissionListView from '../../components/missions/MissionListView.vue'
import MissionCardView from '../../components/missions/MissionCardView.vue'

useHead({ title: "Gestion des missions" });
const { setPageTitle } = usePageTitle();

const missionStore = useMissionStore();
const searchQuery = ref('');
const statusFilter = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const showMissionModal = ref(false);
const showDeleteModal = ref(false);
const selectedMission = ref<Mission | null>(null);
const missionToDelete = ref<Mission | null>(null);
const isEditMode = ref(false);
const isDeleting = ref(false);

// Computed properties
const missions = computed(() => missionStore.missions);

const activeMissionsCount = computed(() =>
	missions.value.filter(mission => !mission.to).length
);

const completedMissionsCount = computed(() =>
	missions.value.filter(mission => mission.to).length
);

const filteredMissions = computed(() => {
	let filtered = missions.value;

	// Filtre par recherche
	if (searchQuery.value) {
		const query = searchQuery.value.toLowerCase();
		filtered = filtered.filter(mission =>
			mission.label.toLowerCase().includes(query) ||
			mission.description.toLowerCase().includes(query)
		);
	}

	// Filtre par statut
	if (statusFilter.value === 'active') {
		filtered = filtered.filter(mission => !mission.to);
	} else if (statusFilter.value === 'completed') {
		filtered = filtered.filter(mission => mission.to);
	}

	return filtered;
});

const hasActiveFilters = computed(() =>
	!!statusFilter.value || !!searchQuery.value
);

// Pagination
const totalPages = computed(() =>
	Math.ceil(filteredMissions.value.length / itemsPerPage.value)
);

const startIndex = computed(() =>
	(currentPage.value - 1) * itemsPerPage.value
);

const endIndex = computed(() =>
	startIndex.value + itemsPerPage.value
);

const paginatedMissions = computed(() =>
	filteredMissions.value.slice(startIndex.value, endIndex.value)
);

const pagesToShow = computed(() => {
	const pages = [];
	const maxPages = 5;

	if (totalPages.value <= maxPages) {
		for (let i = 1; i <= totalPages.value; i++) {
			pages.push(i);
		}
	} else {
		let start = Math.max(1, currentPage.value - 2);
		let end = Math.min(totalPages.value, start + maxPages - 1);

		if (end - start + 1 < maxPages) {
			start = end - maxPages + 1;
		}

		for (let i = start; i <= end; i++) {
			pages.push(i);
		}
	}

	return pages;
});

// Méthodes
const formatDate = (dateString: string) => {
	return new Date(dateString).toLocaleDateString('fr-FR', {
		year: 'numeric',
		month: '2-digit',
		day: '2-digit'
	});
};

const loadMissions = async () => {
	try {
		await missionStore.getAll();
	} catch (error) {
		console.error('Erreur lors du chargement des missions:', error);
		useAlert().showAlert('Une erreur est survenue lors du chargement des missions', 'error');
	}
};

const openCreateModal = () => {
	selectedMission.value = null;
	isEditMode.value = false;
	showMissionModal.value = true;
};

const openEditModal = (mission: Mission) => {
	selectedMission.value = { ...mission };
	isEditMode.value = true;
	showMissionModal.value = true;
};

const openViewModal = (mission: Mission) => {
	navigateTo(AppUrl.parameterize(AppUrl.MISSION_DETAILS, mission.id));
};

const openDeleteModal = (mission: Mission) => {
	missionToDelete.value = mission;
	showDeleteModal.value = true;
};

const closeMissionModal = () => {
	showMissionModal.value = false;
	selectedMission.value = null;
};

const closeDeleteModal = () => {
	showDeleteModal.value = false;
	missionToDelete.value = null;
	isDeleting.value = false;
};

const handleSaveMission = async (missionData: Mission) => {
	try {
		if (isEditMode.value && selectedMission.value) {
			await missionStore.update(missionData);
		} else {
			await missionStore.store(missionData);
		}
		closeMissionModal();
		useAlert().showAlert('Mission sauvegardée avec succès', 'success');
	} catch (error) {
		console.error('Erreur lors de la sauvegarde de la mission:', error);
		useAlert().showAlert('Une erreur est survenue lors de la sauvegarde', 'error');
	}
};

const handleDeleteMission = async () => {
	if (!missionToDelete.value) return;

	isDeleting.value = true;
	try {
		await missionStore.delete(missionToDelete.value.id);
		closeDeleteModal();
		useAlert().showAlert('Mission supprimée avec succès', 'success');
	} catch (error) {
		console.error('Erreur lors de la suppression de la mission:', error);
		useAlert().showAlert('Une erreur est survenue lors de la suppression', 'error');
	} finally {
		isDeleting.value = false;
	}
};

const clearFilters = () => {
	searchQuery.value = '';
	statusFilter.value = '';
	currentPage.value = 1;
};

// Watchers
watch([searchQuery, statusFilter], () => {
	currentPage.value = 1;
});

// Lifecycle
onMounted(() => {
	setPageTitle('Gestion des missions', 'Gestion et suivi des missions');
	loadMissions();
});
</script>
