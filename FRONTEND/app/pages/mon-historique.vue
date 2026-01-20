<template>
	<ReservationMain :is-manage-mode="false" />
</template>

<script setup lang="ts">

import { usePageTitle } from '~/composables/usePageTitle'
import ReservationMain from '~/components/reservations/ReservationMain.vue'

const { setPageTitle } = usePageTitle();
useHead({title: "Historique de réservations"});

onMounted(() => {
	setPageTitle('Historique', 'Mon historique de réservations');
})
</script>
