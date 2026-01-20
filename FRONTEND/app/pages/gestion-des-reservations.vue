<template>
	<ReservationMain :is-manage-mode="true" />
</template>

<script setup lang="ts">

import { usePageTitle } from '~/composables/usePageTitle'
import ReservationMain from '~/components/reservations/ReservationMain.vue'

const { setPageTitle } = usePageTitle();
useHead({ title: "Gestion des réservations" });

onMounted(() => {
	setPageTitle('Gestion des réservations', 'Gestion et suivi des réservations de véhicules');
})
</script>
