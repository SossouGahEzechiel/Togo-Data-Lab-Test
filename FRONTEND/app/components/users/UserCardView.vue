<template>
	<div class="lg:hidden space-y-4 p-4">
		<div v-if="filteredUsers.length === 0" class="text-center py-8">
			<UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
			<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun utilisateur</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
				{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer un utilisateur' }}
			</p>
		</div>

		<div v-else class="space-y-4">
			<div v-for="user in paginatedUsers" :key="user.id" class="bg-white dark:bg-dark-800 rounded-lg shadow p-4">
				<div class="flex items-start justify-between">
					<div class="flex items-start space-x-4">
						<div class="flex-shrink-0">
							<div v-if="user.image?.path" class="h-12 w-12 rounded-full overflow-hidden">
								<img :src="pathToTheServer(user.image.path)" :alt="user.fullName" class="h-full w-full object-cover" />
							</div>
							<div v-else
								class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
								<UserIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
							</div>
						</div>
						<div class="flex-1 min-w-0">
							<div class="flex items-center justify-between">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white">
									{{ user.fullName }}
								</h3>
								<div class="flex items-center space-x-2">
									<UserRoleBadge :role="user.role" size="sm" />
									<UserStatusBadge :is-active="user.isActive" size="sm" />
								</div>
							</div>
							<div class="mt-1">
								<p class="text-xs text-gray-500 dark:text-gray-400">
									{{ user.email }}
								</p>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									{{ user.phone || 'Non renseigné' }}
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-dark-700 pt-4">
					<div class="flex items-center space-x-3">
						<button @click="openEditModal(user)"
							class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
							Modifier
						</button>
					</div>
					<div class="flex items-center space-x-2">
						<button @click="toggleUserStatus(user)" :class="[
							'text-sm font-medium',
							user.isActive
								? 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300'
								: 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300'
						]">
							{{ user.isActive ? 'Désactiver' : 'Activer' }}
						</button>
						<button @click="openDeleteModal(user)"
							class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
							Supprimer
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script setup lang="ts">
import { UserGroupIcon, UserIcon } from "@heroicons/vue/24/outline";
import UserRoleBadge from "~/components/users/UserRoleBadge.vue";
import UserStatusBadge from "~/components/users/UserStatusBadge.vue";
import type { User } from "~/types/User";

defineProps<{
	filteredUsers: { id: string; firstName: string; lastName: string; fullName: string; email: string; role: import("c:/unnamed/full_stack/data_lab_test/FRONTEND/app/types/UserRoleEnum").UserRoleEnum; phone: string; isActive: boolean; hasConfiguredPassword: boolean; image: { id: string; path: string; }; token: string; }[];
	searchQuery: string;
	paginatedUsers: { id: string; firstName: string; lastName: string; fullName: string; email: string; role: import("c:/unnamed/full_stack/data_lab_test/FRONTEND/app/types/UserRoleEnum").UserRoleEnum; phone: string; isActive: boolean; hasConfiguredPassword: boolean; image: { id: string; path: string; }; token: string; }[];
	pathToTheServer: (path: string) => string;
	openEditModal: (user: User) => void;
	toggleUserStatus: (user: User) => void;
	openDeleteModal: (user: User) => void;
}>()
</script>
