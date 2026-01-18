<template>
	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
		<div class="p-6">
			<div class="flex justify-between items-start mb-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
						{{ vehicle.brand }} {{ vehicle.model }}
					</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400">
						{{ vehicle.registrationNumber }}
					</p>
				</div>
				<StatusBadge :status="vehicle.status" />
			</div>

			<div class="grid grid-cols-2 gap-4 mb-4">
				<div class="space-y-1">
					<p class="text-xs text-gray-500 dark:text-gray-400">Type</p>
					<p class="text-sm font-medium text-gray-900 dark:text-white">
						{{ getVehicleTypeLabel(vehicle.type) }}
					</p>
				</div>
				<div class="space-y-1">
					<p class="text-xs text-gray-500 dark:text-gray-400">Places</p>
					<p class="text-sm font-medium text-gray-900 dark:text-white">
						{{ vehicle.seatsCount }}
					</p>
				</div>
				<div class="space-y-1">
					<p class="text-xs text-gray-500 dark:text-gray-400">Immatriculation</p>
					<p class="text-sm font-medium text-gray-900 dark:text-white">
						{{ vehicle.registrationDate }}
					</p>
				</div>
			</div>

			<div class="flex space-x-2">
				<UButton size="sm" variant="ghost" @click="$emit('view', vehicle.id)">
					Voir
				</UButton>
				<UButton v-if="canManage" size="sm" variant="outline" @click="$emit('edit', vehicle.id)">
					Modifier
				</UButton>
				<UButton v-if="canReserve && vehicle.status === 'available'" size="sm" @click="$emit('reserve', vehicle.id)">
					Réserver
				</UButton>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import type { Vehicle, VehicleType } from '~/types'

interface Props {
	vehicle: Vehicle
	canManage?: boolean
	canReserve?: boolean
}

defineProps<Props>()
defineEmits<{
	view: [id: string]
	edit: [id: string]
	reserve: [id: string]
}>()

const getVehicleTypeLabel = (type: VehicleType): string => {
	const types: Record<VehicleType, string> = {
		car: 'Voiture',
		motorcycle: 'Moto',
		truck: 'Camion',
		van: 'Fourgon',
		bus: 'Bus'
	}
	return types[type] || type
}
</script>
