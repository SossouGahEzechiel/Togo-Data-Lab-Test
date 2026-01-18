<template>
	<div id="notifications"></div>
</template>

<script setup lang="ts">
import { useToast } from 'vue-toastification'

// Initialiser les toasts
const toast = useToast()

// Exposer le toast globalement si nécessaire
const nuxtApp = useNuxtApp()
nuxtApp.provide('toast', toast)
</script>
