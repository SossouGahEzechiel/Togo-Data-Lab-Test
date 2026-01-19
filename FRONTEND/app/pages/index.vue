<template>
	<Login />
</template>

<script setup>
definePageMeta({
	layout: false
});
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Login from './me-connecter.vue';

const router = useRouter();

onMounted(() => {
	router.push(AppUrl.LOGIN);
});
</script>
