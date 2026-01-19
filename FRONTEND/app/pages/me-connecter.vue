<template>
	<div class="min-h-screen flex items-center justify-center bg-primary-50 dark:bg-dark-900 px-4 sm:px-6 lg:px-0">
		<div class="flex w-full max-w-4xl min-h-[250px] overflow-hidden bg-white dark:bg-dark-800 rounded-lg shadow-xl">
			<!-- Côté gauche - Logo -->
			<div class="hidden md:flex md:w-1/2 bg-primary-900 text-white flex-col justify-center items-center p-8">
				<div class="space-y-4 text-center">
					<h1 class="text-4xl font-bold tracking-tight">AutoParc TG</h1>
					<TruckIcon :style="{ width: '96px', height: '96px' }" />
				</div>
			</div>

			<!-- Côté droit - Formulaire avec animation -->
			<div class="w-full p-8 md:w-1/2 md:p-12 transform transition-all duration-500 hover:scale-[1.005]">
				<div class="mb-10 text-center">
					<h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Bienvenue</h2>
					<p class="text-gray-500 dark:text-gray-400">Connectez-vous pour accéder à votre compte</p>
				</div>

				<form @submit.prevent="handleLogin" class="space-y-6">
					<!-- Champ Email -->
					<div class="space-y-2">
						<div class="relative">
							<span
								class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
								<EnvelopeIcon class="w-5 h-5" aria-hidden="true" />
							</span>
							<input id="email" v-model="credentials.email" type="email" placeholder="Email"
								class="input-field pl-10 pr-4 py-3" autocomplete="email" required />
						</div>
						<p v-if="validationErrors.email || validationErrors._general" class="text-red-500 text-sm mt-1">
							{{ validationErrors.email || validationErrors._general }}
						</p>
					</div>

					<!-- Champ Mot de passe -->
					<div class="space-y-2">
						<div class="relative">
							<span
								class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
								<LockClosedIcon class="w-5 h-5" aria-hidden="true" />
							</span>
							<input id="password" v-model="credentials.password" type="password" placeholder="Mot de passe" :class="[
								'input-field pl-10 pr-4 py-3',
								validationErrors.password ? 'border-red-500' : '',
							]" autocomplete="current-password" required />
						</div>
						<p v-if="validationErrors.password" class="text-red-500 text-sm mt-1">
							{{ validationErrors.password }}
						</p>
					</div>

					<!-- Options -->
					<div class="flex items-center justify-end">
						<div>
							<a href="#"
								class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors duration-200">
								Mot de passe oublié ?
							</a>
						</div>
					</div>

					<!-- Bouton de connexion -->
					<div class="flex justify-center">
						<button type="submit"
							class="btn-primary flex items-center justify-center w-full md:w-80 text-center py-3 px-4"
							:disabled="isLoading">
							<Spinner :is-loading="isLoading" />
							<span class="ml-3">{{ isLoading ? 'Connexion en cours...' : 'Connexion' }}</span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/16/solid';
import { TruckIcon } from '@heroicons/vue/24/outline';
import Spinner from '~/components/partials/Spinner.vue';
import { initLoginCredential, type LoginCredential } from '~/types/User';

definePageMeta({ layout: false, ssr: false });
useHead({ title: "Connexion" });

const authStore = useAuthStore();
const isLoading = ref<boolean>(false);
const credentials = ref<LoginCredential>(initLoginCredential());
const { validationErrors } = storeToRefs(authStore);

const handleLogin = async () => {
	isLoading.value = true;
	try {
		await authStore.login(credentials.value);
		credentials.value = initLoginCredential();
		await navigateTo(AppUrl.DASHBOARD);
	} catch (error) {
		console.log("Error while authenticating:", error);
	} finally {
		isLoading.value = false;
	}
}
</script>
