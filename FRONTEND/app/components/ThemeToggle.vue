<script setup lang="ts">
import { MoonIcon, SunIcon } from '@heroicons/vue/24/outline'

const { isDark, toggleTheme } = useTheme()
</script>

<template>
	<button @click="toggleTheme"
		class="p-2 rounded-lg bg-gray-200 dark:bg-dark-700 hover:bg-gray-300 dark:hover:bg-dark-600 transition-colors duration-200"
		aria-label="Toggle theme">
		<SunIcon v-if="isDark" class="w-5 h-5 text-secondary-500" />
		<MoonIcon v-else class="w-5 h-5 text-primary-600" />
	</button>
</template>
