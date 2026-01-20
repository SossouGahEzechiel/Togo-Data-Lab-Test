<template>
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<CalendarIcon class="w-8 h-8 text-primary-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ reservationsCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<ClockIcon class="w-8 h-8 text-yellow-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">En attente</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ pendingCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<CheckCircleIcon class="w-8 h-8 text-green-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Validées</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ validatedCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<XCircleIcon class="w-8 h-8 text-red-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejetées</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ rejectedCount }}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
defineProps<{
	reservationsCount: number;
	pendingCount: number;
	validatedCount: number;
	rejectedCount: number;
}>()
</script>
