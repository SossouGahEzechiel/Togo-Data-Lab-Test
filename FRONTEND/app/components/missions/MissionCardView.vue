<template>
	<div class="lg:hidden space-y-4 p-4">
		<div v-if="filteredMissions.length === 0" class="text-center py-8">
			<BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
			<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune mission</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
				{{ searchQuery ? 'Aucun résultat pour votre recherche' : 'Commencez par créer une mission' }}
			</p>
		</div>

		<div v-else class="space-y-4">
			<div v-for="mission in paginatedMissions" :key="mission.id"
				class="bg-white dark:bg-dark-800 rounded-lg shadow p-4">
				<div class="flex items-start justify-between">
					<div class="flex items-start space-x-4">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
								<BriefcaseIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
							</div>
						</div>
						<div class="flex-1 min-w-0">
							<div class="flex items-center justify-between">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white">
									{{ mission.label }}
								</h3>
								<span :class="[
									'px-2 py-1 text-xs font-semibold rounded-full',
									mission.to
										? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
										: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
								]">
									{{ mission.to ? 'Terminée' : 'En cours' }}
								</span>
							</div>
							<div class="mt-1">
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-wrap">
									{{ mission.description || 'Aucune description' }}
								</p>
								<!-- <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									{{ mission.vehicle ? `${mission.vehicle.brand} ${mission.vehicle.model}` : 'Aucun véhicule' }}
								</p> -->
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									{{ formatDate(mission.from) }}
									<span v-if="mission.to" class="text-gray-500 dark:text-gray-400">
										→ {{ formatDate(mission.to) }}
									</span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-dark-700 pt-4">
					<div class="flex items-center space-x-3">
						<button @click="openEditModal(mission)"
							class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
							Modifier
						</button>
						<button @click="openViewModal(mission)"
							class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium">
							Détails
						</button>
					</div>
					<button @click="openDeleteModal(mission)"
						class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
						Supprimer
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { BriefcaseIcon } from '@heroicons/vue/24/outline';
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
