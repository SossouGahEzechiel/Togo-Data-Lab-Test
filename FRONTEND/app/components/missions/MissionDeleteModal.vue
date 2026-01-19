<template>
	<div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
		<div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeDeleteModal"></div>

		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-md w-full">
				<div class="p-6">
					<!-- Icône d'avertissement -->
					<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
						<ExclamationTriangleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
					</div>

					<!-- Titre -->
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">
						Supprimer la mission
					</h3>

					<!-- Message -->
					<p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
						Êtes-vous sûr de vouloir supprimer la mission "{{ missionToDelete?.label }}" ? Cette action est
						irréversible.
					</p>

					<!-- Boutons -->
					<div class="mt-6 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
						<button type="button" @click="closeDeleteModal"
							class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							Annuler
						</button>
						<button type="button" @click="handleDeleteMission" :disabled="isDeleting"
							class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">
							<Spinner v-if="isDeleting" :is-loading="true" size="sm" />
							<span>Supprimer</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import Spinner from '../partials/spinner.vue';

defineProps<{
	showDeleteModal: boolean;
	closeDeleteModal: () => void;
	missionToDelete: { id: string; label: string; description: string; from: string; to: string | null; } | null;
	handleDeleteMission: () => Promise<void>;
	isDeleting: boolean;
}>()
</script>
