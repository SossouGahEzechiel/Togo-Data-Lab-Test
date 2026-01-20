<template>
	<div class="hidden lg:block overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
			<thead class="bg-gray-50 dark:bg-dark-700">
				<tr>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						UTILISATEUR
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						CONTACT
					</th>
					<th scope="col"
						class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
						RÔLE
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
				<tr v-if="filteredUsers.length === 0">
					<td colspan="6" class="px-6 py-8 text-center">
						<UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun utilisateur</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer un utilisateur' }}
						</p>
					</td>
				</tr>
				<tr v-for="user in paginatedUsers" :key="user.id">
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="flex items-center">
							<div class="flex-shrink-0 h-10 w-10">
								<div v-if="user.image?.path" class="h-10 w-10 rounded-full overflow-hidden">
									<img :src="pathToTheServer(user.image.path)" :alt="user.fullName"
										class="h-full w-full object-cover" />
								</div>
								<div v-else
									class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
									<UserIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
								</div>
							</div>
							<div class="ml-4">
								<div class="text-sm font-medium text-gray-900 dark:text-white">
									{{ user.fullName }}
								</div>
								<div class="text-sm text-gray-500 dark:text-gray-400">
									{{ user.email }}
								</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="text-sm text-gray-900 dark:text-white">
							{{ user.email }}
						</div>
						<div class="text-xs text-gray-500 dark:text-gray-400">
							{{ user.phone || 'Non renseigné' }}
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<UserRoleBadge :role="user.role" />
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<UserStatusBadge :is-active="user.isActive" />
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
						<div class="flex items-center space-x-3">
							<button @click="openEditModal(user)"
								class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
								title="Modifier">
								<PencilSquareIcon class="w-5 h-5" />
							</button>
							<button @click="toggleUserStatus(user)" :class="[
								user.isActive
									? 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300'
									: 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300'
							]" :title="user.isActive ? 'Désactiver' : 'Activer'">
								<PowerIcon v-if="user.isActive" class="w-5 h-5" />
								<CheckCircleIcon v-else class="w-5 h-5" />
							</button>
							<button @click="openDeleteModal(user)"
								class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Supprimer">
								<TrashIcon class="w-5 h-5" />
							</button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script setup lang="ts">
import { CheckCircleIcon, PencilSquareIcon, PowerIcon, TrashIcon, UserGroupIcon, UserIcon } from "@heroicons/vue/24/outline";
import type { User } from "~/types/User";
import UserStatusBadge from "./UserStatusBadge.vue";
import UserRoleBadge from "./UserRoleBadge.vue";

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
