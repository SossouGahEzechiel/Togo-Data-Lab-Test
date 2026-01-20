<template>
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<UserGroupIcon class="w-8 h-8 text-primary-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ usersCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<ShieldCheckIcon class="w-8 h-8 text-blue-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Administrateurs</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ adminCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<UserIcon class="w-8 h-8 text-green-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Employés</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ employeeCount }}</p>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					<TruckIcon class="w-8 h-8 text-yellow-500" />
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Chauffeurs</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ driverCount }}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ShieldCheckIcon, TruckIcon, UserGroupIcon, UserIcon } from '@heroicons/vue/24/outline';

defineProps<{
	usersCount: number;
	adminCount: number;
	employeeCount: number;
	driverCount: number;
}>()
</script>
