<template>
	<span :class="[
		'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
		variantClasses
	]">
		{{ label }}
	</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
	status: string
	label?: string
}

const props = defineProps<Props>()

const variantClasses = computed(() => {
	const variants: Record<string, string> = {
		// Vehicle status
		'available': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
		'reserved': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
		'suspended': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
		'under_repair': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
		'unavailable': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',

		// Reservation status
		'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
		'validated': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
		'rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
		'passed': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
	}

	return variants[props.status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
})

const label = computed(() => {
	if (props.label) return props.label

	const labels: Record<string, string> = {
		'available': 'Disponible',
		'reserved': 'Réservé',
		'suspended': 'Suspendu',
		'under_repair': 'En réparation',
		'unavailable': 'Indisponible',
		'pending': 'En attente',
		'validated': 'Validée',
		'rejected': 'Rejetée',
		'passed': 'Terminée'
	}

	return labels[props.status] || props.status
})
</script>
