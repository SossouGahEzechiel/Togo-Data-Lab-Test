<script setup lang="ts">
defineProps({
  isLoading: {
    type: Boolean,
    default: false,
  },
  text: {
    type: String,
    default: "",
  },
});
</script>

<template>
  <div v-if="isLoading" class="flex items-center">
    <span
      class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white border-t-transparent mr-2 my-auto"></span>
    <span v-if="text">{{ text }}</span>
  </div>
  <div v-else></div>
</template>
