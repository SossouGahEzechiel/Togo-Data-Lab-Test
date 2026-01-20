<template>
	<span :class="badgeClasses">
		{{ role }}
	</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { UserRoleEnum } from '~/types/UserRoleEnum';

interface Props {
	role: UserRoleEnum
	size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
	size: 'md'
})

const badgeClasses = computed(() => {
	const sizeClasses = {
		sm: 'px-1.5 py-0.5 text-xs',
		md: 'px-2 py-1 text-xs',
		lg: 'px-3 py-1.5 text-sm'
	}

	const baseClasses = `${sizeClasses[props.size]} font-semibold rounded-full`

	switch (props.role) {
		case 'Administrateur':
			return `${baseClasses} bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300`
		case 'Employé':
			return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300`
		case 'Chauffeur':
			return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300`
		default:
			return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300`
	}
})
</script>