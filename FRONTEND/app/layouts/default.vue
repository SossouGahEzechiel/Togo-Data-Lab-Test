<template>
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
		<Navbar />
		<main class="container mx-auto px-4 py-8">
			<slot />
		</main>
		<Notifications />
	</div>
</template>

<script setup lang="ts">
import Navbar from '~/components/layout/Navbar.vue'
</script>
