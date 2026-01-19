<template>
	<span :class="badgeClasses">
		{{ status }}
	</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ReservationStatusEnum } from '~/types/ReservationEnum';

const props = defineProps<{
	status: ReservationStatusEnum
}>()

const badgeClasses = computed(() => {
	const baseClasses = 'px-2 py-1 text-xs font-semibold rounded-full'

	switch (props.status) {
		case ReservationStatusEnum.PENDING:
			return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300`
		case ReservationStatusEnum.VALIDATED:
			return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300`
		case ReservationStatusEnum.REJECTED:
			return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300`
		case ReservationStatusEnum.PASSED:
			return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300`
		default:
			return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300`
	}
})
</script>