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
						DESCRIPTION
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
				<tr v-if="filteredMissions.length === 0">
					<td colspan="6" class="px-6 py-8 text-center">
						<BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune mission</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer une mission' }}
						</p>
					</td>
				</tr>
				<tr v-for="mission in paginatedMissions" :key="mission.id">
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="flex items-center">
							<div class="flex-shrink-0 h-10 w-10">
								<div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
									<BriefcaseIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
								</div>
							</div>
							<div class="ml-4">
								<div class="text-sm font-medium text-gray-900 dark:text-white truncate">
									{{ mission.label }}
								</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4">
						<div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" :title="mission.description">
							{{ mission.description || 'Aucune description' }}
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<div class="flex flex-col">
							<div class="text-sm text-gray-900 dark:text-white">
								{{ formatDate(mission.from) }}
							</div>
							<div v-if="mission.to" class="text-gray-500 dark:text-gray-400 text-sm">
								{{ formatDate(mission.to) }}
							</div>
						</div>
					</td>
					<td class="px-6 py-4 whitespace-nowrap">
						<span :class="[
							'px-2 py-1 text-xs font-semibold rounded-full',
							mission.to
								? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
								: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
						]">
							{{ mission.to ? 'Terminée' : 'En cours' }}
						</span>
					</td>
					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
						<div class="flex items-center space-x-3">
							<button @click="openEditModal(mission)"
								class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
								title="Modifier">
								<PencilSquareIcon class="w-5 h-5" />
							</button>
							<button @click="openViewModal(mission)"
								class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
								title="Voir détails">
								<EyeIcon class="w-5 h-5" />
							</button>
							<button @click="openDeleteModal(mission)"
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
import { BriefcaseIcon, EyeIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import type { Mission } from '~/types/Mission';

defineProps<{
	filteredMissions: Mission[];
	searchQuery: string;
	paginatedMissions: Mission[];
	formatDate: (dateString: string) => string;
	openEditModal: (mission: Mission) => void;
	openViewModal: (mission: Mission) => void;
	openDeleteModal: (mission: Mission) => void;
}>()
</script>
