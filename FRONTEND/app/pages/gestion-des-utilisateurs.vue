<template>
	<div class="space-y-6">
		<!-- En-tête de page -->
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
						{{ filteredUsers.length }} utilisateur(s) trouvé(s)
					</h1>
				</div>
				<button @click="openCreateModal" class="btn-primary flex items-center gap-2 whitespace-nowrap">
					<PlusIcon class="w-5 h-5" />
					Nouvel utilisateur
				</button>
			</div>
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
						<input v-model="searchQuery" type="text" placeholder="Rechercher un utilisateur..."
							class="input-field pl-10" />
					</div>
				</div>

				<!-- Filtre par rôle -->
				<div>
					<select v-model="roleFilter" class="input-field">
						<option value="">Tous les rôles</option>
						<option v-for="role in userRoles" :key="role" :value="role">
							{{ role }}
						</option>
					</select>
				</div>

				<!-- Filtre par statut -->
				<div>
					<select v-model="statusFilter" class="input-field">
						<option value="">Tous les statuts</option>
						<option value="active">Actif</option>
						<option value="inactive">Inactif</option>
					</select>
				</div>
			</div>

			<!-- Filtres actifs -->
			<div v-if="hasActiveFilters" class="flex flex-wrap gap-2">
				<span class="text-sm text-gray-500 dark:text-gray-400">Filtres :</span>
				<button v-if="roleFilter" @click="roleFilter = ''"
					class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
					{{ roleFilter }}
					<XMarkIcon class="w-3 h-3" />
				</button>
				<button v-if="statusFilter" @click="statusFilter = ''"
					class="inline-flex items-center gap-x-1 px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
					{{ statusFilter === 'active' ? 'Actif' : 'Inactif' }}
					<XMarkIcon class="w-3 h-3" />
				</button>
				<button v-if="hasActiveFilters" @click="clearFilters"
					class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
					Effacer tous les filtres
				</button>
			</div>
		</div>

		<!-- Statistiques -->
		<UserStats :usersCount="users.length" :adminCount="adminCount" :employeeCount="employeeCount"
			:driverCount="driverCount" />

		<!-- Liste des utilisateurs -->
		<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
			<div v-if="userStore.isLoading" class="flex justify-center items-center p-8">
				<Spinner :is-loading="true" size="lg" />
			</div>

			<div v-else>
				<!-- Tableau desktop -->
				<UserListView :filteredUsers="filteredUsers" :searchQuery="searchQuery" :paginatedUsers="paginatedUsers"
					:pathToTheServer="pathToTheServer" :openEditModal="openEditModal" :toggleUserStatus="toggleUserStatus"
					:openDeleteModal="openDeleteModal" />

				<!-- Liste mobile -->
				<UserCardView :filteredUsers="filteredUsers" :searchQuery="searchQuery" :paginatedUsers="paginatedUsers"
					:pathToTheServer="pathToTheServer" :openEditModal="openEditModal" :toggleUserStatus="toggleUserStatus"
					:openDeleteModal="openDeleteModal" />
			</div>

			<!-- Pagination -->
			<Paginator :filtered-data-count="filteredUsers.length" :endIndex="endIndex" :pagesToShow="pagesToShow"
				v-model:currentPage="currentPage" v-model:totalPages="totalPages" v-model:startIndex="startIndex" />
		</div>

		<!-- Modals -->
		<UserModal :is-open="isModalOpen" :user="selectedUser" :mode="modalMode" @close="closeModal"
			@saved="handleUserSaved" />

		<!-- Modal de confirmation de suppression -->
		<UserDeleteModal v-if="userToDelete" :is-open="isDeleteModalOpen" :user="userToDelete" @close="closeDeleteModal"
			@confirmed="handleDeleteConfirmed" />

		<!-- Modal de confirmation de changement de statut -->
		<UserStatusModal v-if="userToToggleStatus" :is-open="isStatusModalOpen" :user="userToToggleStatus"
			:new-status="!userToToggleStatus.isActive" @close="closeStatusModal" @confirmed="handleStatusConfirmed" />
	</div>
</template>

<script setup lang="ts">
import {
	MagnifyingGlassIcon,
	PlusIcon,
	XMarkIcon,
} from '@heroicons/vue/24/outline'

import { usePageTitle } from '~/composables/usePageTitle'
import { useUserStore } from '~/stores/UserStore'
import type { User } from '~/types/User'
import UserModal from '~/components/users/UserModal.vue'
import UserDeleteModal from '~/components/users/UserDeleteModal.vue'
import UserStatusModal from '~/components/users/UserStatusModal.vue'
import Spinner from '~/components/partials/Spinner.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { pathToTheServer } from '~/types/Image'
import { userRoles } from '~/types/UserRoleEnum'
import UserListView from '~/components/users/UserListView.vue'
import UserCardView from '~/components/users/UserCardView.vue'
import Paginator from '~/components/partials/Paginator.vue'
import UserStats from '~/components/users/UserStats.vue'

useHead({ title: "Gestion des utilisateurs" })
const { setPageTitle } = usePageTitle()
const userStore = useUserStore()

// État du composant
const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)

// État pour les modals
const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isStatusModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedUser = ref<User | null>(null)
const userToDelete = ref<User | null>(null)
const userToToggleStatus = ref<User | null>(null)

// Computed properties
const users = computed(() => userStore.users)

const filteredUsers = computed(() => {
	return users.value.filter(user => {
		const matchesSearch = !searchQuery.value ||
			user.firstName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			user.lastName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			user.fullName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
			user.phone?.toLowerCase().includes(searchQuery.value.toLowerCase())

		const matchesRole = !roleFilter.value || user.role === roleFilter.value

		const matchesStatus = !statusFilter.value ||
			(statusFilter.value === 'active' && user.isActive) ||
			(statusFilter.value === 'inactive' && !user.isActive)

		return matchesSearch && matchesRole && matchesStatus
	})
})

const adminCount = computed(() =>
	users.value.filter(u => u.role === 'Administrateur').length
)
const employeeCount = computed(() =>
	users.value.filter(u => u.role === 'Employé').length
)
const driverCount = computed(() =>
	users.value.filter(u => u.role === 'Chauffeur').length
)

const hasActiveFilters = computed(() =>
	!!roleFilter.value || !!statusFilter.value || !!searchQuery.value
)

// Pagination
const totalPages = computed(() =>
	Math.ceil(filteredUsers.value.length / itemsPerPage.value)
)
const startIndex = computed(() =>
	(currentPage.value - 1) * itemsPerPage.value
)
const endIndex = computed(() =>
	startIndex.value + itemsPerPage.value
)
const paginatedUsers = computed(() =>
	filteredUsers.value.slice(startIndex.value, endIndex.value)
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
	selectedUser.value = null
	modalMode.value = 'create'
	isModalOpen.value = true
}

const openEditModal = (user: User) => {
	selectedUser.value = { ...user }
	modalMode.value = 'edit'
	isModalOpen.value = true
}

const closeModal = () => {
	isModalOpen.value = false
	selectedUser.value = null
}

const handleUserSaved = () => {
	closeModal()
	loadUsers()
}

const toggleUserStatus = (user: User) => {
	userToToggleStatus.value = user
	isStatusModalOpen.value = true
}

const openDeleteModal = (user: User) => {
	userToDelete.value = user
	isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
	isDeleteModalOpen.value = false
	userToDelete.value = null
}

const closeStatusModal = () => {
	isStatusModalOpen.value = false
	userToToggleStatus.value = null
}

const handleDeleteConfirmed = async () => {
	if (userToDelete.value) {
		try {
			await userStore.delete(userToDelete.value.id)
		} catch (error) {
			console.error('Erreur lors de la suppression:', error)
		}
	}
	closeDeleteModal()
}

const handleStatusConfirmed = async () => {
	if (userToToggleStatus.value) {
		try {
			const updatedUser = {
				...userToToggleStatus.value,
				isActive: !userToToggleStatus.value.isActive
			}
			await userStore.update(updatedUser)
		} catch (error) {
			console.error('Erreur lors du changement de statut:', error)
		}
	}
	closeStatusModal()
}

const clearFilters = () => {
	searchQuery.value = ''
	roleFilter.value = ''
	statusFilter.value = ''
	currentPage.value = 1
}

const loadUsers = async () => {
	try {
		await userStore.findAll()
	} catch (error) {
		console.error('Erreur lors du chargement des utilisateurs:', error)
	}
}

// Watchers
watch([searchQuery, roleFilter, statusFilter], () => {
	currentPage.value = 1
})

// Lifecycle
onMounted(() => {
	setPageTitle('Gestion des utilisateurs', 'Gestion des comptes utilisateurs et des permissions')
	loadUsers()
})
</script>