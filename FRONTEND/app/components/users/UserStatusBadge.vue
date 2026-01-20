<template>
	<span :class="badgeClasses">
		{{ isActive ? 'Actif' : 'Inactif' }}
	</span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
	isActive: boolean
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

	return props.isActive
		? `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300`
		: `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300`
})
</script>