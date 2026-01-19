<template>
	Hello
</template>

<script setup lang="ts">
useHead({ title: "Tableau de bord" });
const { setPageTitle } = usePageTitle();
onMounted(() => { setPageTitle('Tableau de bord', 'Vue d\'ensemble de votre activité') })
</script>
