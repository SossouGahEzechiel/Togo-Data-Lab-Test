<template>
	<div v-if="filteredReservations.length > 0"
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
					<span class="font-medium">{{ Math.min(endIndex, filteredReservations.length) }}</span> sur
					<span class="font-medium">{{ filteredReservations.length }}</span> résultats
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
					<button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages"
						class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-800 dark:text-gray-400 dark:hover:bg-dark-700 disabled:opacity-50">
						<ChevronRightIcon class="h-5 w-5" />
					</button>
				</nav>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import type { Reservation } from '~/types/Reservation';

defineProps<{
	filteredReservations: Reservation[];
	endIndex: number;
	pagesToShow: number[];
}>()
const currentPage = defineModel<number>('currentPage', { required: true })
const totalPages = defineModel<number>('totalPages', { required: true })
const startIndex = defineModel<number>('startIndex', { required: true })
</script>
