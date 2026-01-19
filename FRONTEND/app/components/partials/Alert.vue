<template>
	<Transition name="alert">
		<div v-if="alert" :class="getAlertClasses()">
			<div class="flex items-center alert-enter-active z-50">
				<Icon :name="getIconName()" class="h-5 w-5 mr-2" />
				<span>{{ alertMessage }}</span>
			</div>
		</div>
	</Transition>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAlert } from '@/composables/useAlert'

const { alert, getAlertClasses, getIconName } = useAlert()

const alertMessage = computed(() => alert.value)
</script>

<style scoped>
.alert-enter-active {
	animation: fade-in-down 0.5s ease-out;
}

.alert-leave-active {
	animation: fade-out-up 0.5s ease-out;
}
</style>
