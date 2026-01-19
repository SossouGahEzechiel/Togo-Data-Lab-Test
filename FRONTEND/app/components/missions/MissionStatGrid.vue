<template>
	<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
				<!-- Carte Total des missions -->
				<div class="bg-white dark:bg-dark-800 rounded-lg shadow p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
								<BriefcaseIcon class="w-6 h-6 text-primary-600 dark:text-primary-400" />
							</div>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total des missions</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
								{{ missionsCount }}
							</p>
						</div>
					</div>
				</div>

				<!-- Carte Missions en cours -->
				<div class="bg-white dark:bg-dark-800 rounded-lg shadow p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
								<PlayCircleIcon class="w-6 h-6 text-green-600 dark:text-green-400" />
							</div>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Missions en cours</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
								{{ activeMissionsCount }}
							</p>
						</div>
					</div>
				</div>

				<!-- Carte Missions terminées -->
				<div class="bg-white dark:bg-dark-800 rounded-lg shadow p-6">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
								<CheckCircleIcon class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
							</div>
						</div>
						<div class="ml-4">
							<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Missions terminées</p>
							<p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
								{{ completedMissionsCount }}
							</p>
						</div>
					</div>
				</div>
			</div>
</template>

<script setup lang="ts">
import { BriefcaseIcon, CheckCircleIcon, PlayCircleIcon } from '@heroicons/vue/24/outline';

defineProps<{
	missionsCount: number;
	activeMissionsCount: number;
	completedMissionsCount: number;
}>()
</script>
