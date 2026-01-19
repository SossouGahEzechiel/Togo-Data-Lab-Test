<template>
	<div class="bg-white dark:bg-dark-800 rounded-lg shadow p-4">
		<div class="flex items-start justify-between">
			<div class="flex items-start space-x-4">
				<div class="flex-shrink-0">
					<div class="h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
						<TruckIcon class="h-6 w-6 text-primary-600 dark:text-primary-400" />
					</div>
				</div>
				<div class="flex-1 min-w-0">
					<div class="flex items-center justify-between">
						<h3 class="text-sm font-medium text-gray-900 dark:text-white">
							{{ vehicle.brand }} {{ vehicle.model }}
						</h3>
						<VehicleStatusBadge :status="vehicle.status" />
					</div>
					<div class="mt-1">
						<p class="text-xs text-gray-500 dark:text-gray-400">
							<span class="font-mono">{{ vehicle.registrationNumber }}</span>
							• {{ vehicle.type }}
						</p>
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
							{{ vehicle.seatsCount }} place(s) • {{ vehicle.registrationDate }}
						</p>
						<p v-if="vehicle.reason" class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
							<ExclamationTriangleIcon class="w-3 h-3 inline mr-1" />
							{{ vehicle.reason }}
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-dark-700 pt-4">
			<div class="flex items-center space-x-3">
				<button @click="$emit('edit', vehicle)"
					class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
					Modifier
				</button>
				<button @click="$emit('view', vehicle)"
					class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 text-sm font-medium">
					Détails
				</button>
			</div>
			<button @click="$emit('delete', vehicle)"
				class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
				Supprimer
			</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import { TruckIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import type { Vehicle } from '~/types/Vehicle'
import VehicleStatusBadge from './VehicleStatusBadge.vue'

defineProps<{
	vehicle: Vehicle
}>()

defineEmits<{
	edit: [vehicle: Vehicle]
	view: [vehicle: Vehicle]
	delete: [vehicle: Vehicle]
}>()
</script>
