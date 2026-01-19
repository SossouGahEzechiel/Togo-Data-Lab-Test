<template>
	<div class="min-h-screen flex items-center justify-center bg-primary-50 dark:bg-dark-900 px-4 sm:px-6 lg:px-0">
		<div class="flex w-full max-w-4xl min-h-[250px] overflow-hidden bg-white dark:bg-dark-800 rounded-lg shadow-xl">
			<!-- Côté gauche - Logo -->
			<div class="hidden md:flex md:w-1/2 bg-primary-900 text-white flex-col justify-center items-center p-8">
				<div class="space-y-4 text-center">
					<h1 class="text-4xl font-bold tracking-tight">AutoParc TG</h1>
					<TruckIcon class="w-24 h-24 text-primary-100" />
					<p class="text-primary-100 text-sm mt-4">Première connexion</p>
				</div>
			</div>

			<!-- Côté droit - Formulaire avec animation -->
			<div class="w-full p-8 md:w-1/2 md:p-12 transform transition-all duration-500 hover:scale-[1.005]">
				<div class="mb-8 text-center">
					<h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Configurer votre mot de passe</h2>
					<p class="text-gray-500 dark:text-gray-400">
						C'est votre première connexion. Veuillez créer un mot de passe sécurisé.
					</p>
				</div>

				<form @submit.prevent="handlePasswordSetup" class="space-y-6">
					<!-- Champ Nouveau mot de passe -->
					<div class="space-y-2">
						<label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							Nouveau mot de passe
						</label>
						<div class="relative">
							<span
								class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
								<LockClosedIcon class="w-4 h-4" aria-hidden="true" />
							</span>
							<input id="password" v-model="passwordData.password" :type="showpassword ? 'text' : 'password'"
								placeholder="Saisissez votre nouveau mot de passe" class="input-field pl-10 pr-10 py-3"
								autocomplete="new-password" required @input="validatePassword" />
							<button type="button" @click="showpassword = !showpassword"
								class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
								<EyeIcon v-if="showpassword" class="w-4 h-4" />
								<EyeSlashIcon v-else class="w-4 h-4" />
							</button>
						</div>

						<!-- Indicateurs de force du mot de passe -->
						<div class="space-y-1 mt-2">
							<div class="flex items-center space-x-2">
								<div class="w-full h-1.5 rounded-full bg-gray-200 dark:bg-dark-600 overflow-hidden">
									<div :class="[
										'h-full rounded-full transition-all duration-300',
										passwordStrength === 'weak' ? 'w-1/3 bg-red-500' : '',
										passwordStrength === 'medium' ? 'w-2/3 bg-yellow-500' : '',
										passwordStrength === 'strong' ? 'w-full bg-green-500' : ''
									]"></div>
								</div>
								<span class="text-xs font-medium whitespace-nowrap" :class="{
									'text-red-600 dark:text-red-400': passwordStrength === 'weak',
									'text-yellow-600 dark:text-yellow-400': passwordStrength === 'medium',
									'text-green-600 dark:text-green-400': passwordStrength === 'strong'
								}">
									{{ passwordStrength === 'weak' ? 'Faible' : passwordStrength === 'medium' ? 'Moyen' : 'Fort' }}
								</span>
							</div>

							<!-- Règles de validation avec icônes plus petites -->
							<ul class="space-y-0.5 text-xs text-gray-500 dark:text-gray-400 mt-2">
								<li class="flex items-center">
									<CheckCircleIcon :class="[
										'w-3.5 h-3.5 mr-1.5 flex-shrink-0',
										passwordRequirements.minLength ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'
									]" />
									<span :class="passwordRequirements.minLength ? 'text-green-600 dark:text-green-400' : ''">
										8 caractères minimum
									</span>
								</li>
								<li class="flex items-center">
									<CheckCircleIcon :class="[
										'w-3.5 h-3.5 mr-1.5 flex-shrink-0',
										passwordRequirements.hasUpperCase ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'
									]" />
									<span :class="passwordRequirements.hasUpperCase ? 'text-green-600 dark:text-green-400' : ''">
										Au moins une majuscule
									</span>
								</li>
								<li class="flex items-center">
									<CheckCircleIcon :class="[
										'w-3.5 h-3.5 mr-1.5 flex-shrink-0',
										passwordRequirements.hasLowerCase ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'
									]" />
									<span :class="passwordRequirements.hasLowerCase ? 'text-green-600 dark:text-green-400' : ''">
										Au moins une minuscule
									</span>
								</li>
								<li class="flex items-center">
									<CheckCircleIcon :class="[
										'w-3.5 h-3.5 mr-1.5 flex-shrink-0',
										passwordRequirements.hasNumber ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'
									]" />
									<span :class="passwordRequirements.hasNumber ? 'text-green-600 dark:text-green-400' : ''">
										Au moins un chiffre
									</span>
								</li>
								<li class="flex items-center">
									<CheckCircleIcon :class="[
										'w-3.5 h-3.5 mr-1.5 flex-shrink-0',
										passwordRequirements.hasSpecialChar ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'
									]" />
									<span :class="passwordRequirements.hasSpecialChar ? 'text-green-600 dark:text-green-400' : ''">
										Au moins un caractère spécial
									</span>
								</li>
							</ul>
						</div>

						<p v-if="validationErrors.password" class="text-red-500 text-xs mt-1">
							{{ validationErrors.password }}
						</p>
					</div>

					<!-- Champ Confirmation mot de passe -->
					<div class="space-y-2">
						<label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
							Confirmer le mot de passe
						</label>
						<div class="relative">
							<span
								class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
								<LockClosedIcon class="w-4 h-4" aria-hidden="true" />
							</span>
							<input id="password_confirmation" v-model="passwordData.password_confirmation"
								:type="showPassword_confirmation ? 'text' : 'password'" placeholder="Confirmez votre mot de passe"
								class="input-field pl-10 pr-10 py-3" autocomplete="new-password" required @input="checkPasswordMatch" />
							<button type="button" @click="showPassword_confirmation = !showPassword_confirmation"
								class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
								<EyeIcon v-if="showPassword_confirmation" class="w-4 h-4" />
								<EyeSlashIcon v-else class="w-4 h-4" />
							</button>
						</div>

						<!-- Indicateur de correspondance avec icônes plus petites -->
						<div v-if="passwordData.password_confirmation" class="flex items-center space-x-1.5 mt-1">
							<CheckCircleIcon v-if="passwordsMatch && passwordData.password_confirmation.length > 0"
								class="w-3.5 h-3.5 text-green-500 flex-shrink-0" />
							<XCircleIcon v-else-if="passwordData.password_confirmation.length > 0"
								class="w-3.5 h-3.5 text-red-500 flex-shrink-0" />
							<span :class="[
								'text-xs',
								passwordsMatch && passwordData.password_confirmation.length > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
							]">
								{{ passwordsMatch && passwordData.password_confirmation.length > 0 ?
									'Les mots de passe correspondent' : 'Les mots de passe ne correspondent pas' }}
							</span>
						</div>

						<p v-if="validationErrors.password_confirmation" class="text-red-500 text-xs mt-1">
							{{ validationErrors.password_confirmation }}
						</p>
					</div>

					<!-- Boutons -->
					<div class="flex flex-col sm:flex-row justify-center gap-4 pt-4">
						<button type="submit" :disabled="isLoading || !isFormValid" :class="[
							'btn-primary flex items-center justify-center w-full text-center py-3 px-4',
							(!isFormValid || isLoading) ? 'opacity-50 cursor-not-allowed' : ''
						]">
							<Spinner v-if="isLoading" :is-loading="isLoading" />
							<span class="ml-2">{{ isLoading ? 'Configuration en cours...' : 'Configurer le mot de passe' }}</span>
						</button>
					</div>

					<!-- Message d'information -->
					<div v-if="!isFormValid"
						class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
						<p class="text-xs text-yellow-700 dark:text-yellow-300">
							Veuillez remplir tous les champs et respecter les critères de sécurité pour configurer votre mot de passe.
						</p>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
// Le script reste identique
import {
	LockClosedIcon,
	EyeIcon,
	EyeSlashIcon,
	CheckCircleIcon,
	XCircleIcon
} from '@heroicons/vue/16/solid';
import { TruckIcon } from '@heroicons/vue/24/outline';
import Spinner from '~/components/partials/spinner.vue';
import { ref, computed, reactive } from 'vue';
import { initPasswordConfiguration, type ConfigurePasswordCredential } from '~/types/User';

definePageMeta({ layout: false, ssr: false });
useHead({ title: "Configurer mon mot de passe" });

const passwordData = reactive<ConfigurePasswordCredential>(initPasswordConfiguration());

const showpassword = ref(false);
const showPassword_confirmation = ref(false);

const authStore = useAuthStore();
const { validationErrors } = storeToRefs(authStore);

const passwordStrength = ref<'weak' | 'medium' | 'strong'>('weak');
const passwordsMatch = ref(false);
const passwordRequirements = reactive({
	minLength: false,
	hasUpperCase: false,
	hasLowerCase: false,
	hasNumber: false,
	hasSpecialChar: false
});

const isLoading = ref(false);

const validatePassword = () => {
	const password = passwordData.password;

	passwordRequirements.minLength = password.length >= 8;
	passwordRequirements.hasUpperCase = /[A-Z]/.test(password);
	passwordRequirements.hasLowerCase = /[a-z]/.test(password);
	passwordRequirements.hasNumber = /[0-9]/.test(password);
	passwordRequirements.hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

	const requirementsMet = Object.values(passwordRequirements).filter(Boolean).length;

	if (requirementsMet <= 2) {
		passwordStrength.value = 'weak';
	} else if (requirementsMet <= 4) {
		passwordStrength.value = 'medium';
	} else {
		passwordStrength.value = 'strong';
	}

	checkPasswordMatch();

	if (password && password.length < 8) {
		validationErrors.value.password = 'Le mot de passe doit contenir au moins 8 caractères';
	} else if (password && !passwordRequirements.hasUpperCase) {
		validationErrors.value.password = 'Le mot de passe doit contenir au moins une majuscule';
	} else if (password && !passwordRequirements.hasLowerCase) {
		validationErrors.value.password = 'Le mot de passe doit contenir au moins une minuscule';
	} else if (password && !passwordRequirements.hasNumber) {
		validationErrors.value.password = 'Le mot de passe doit contenir au moins un chiffre';
	} else if (password && !passwordRequirements.hasSpecialChar) {
		validationErrors.value.password = 'Le mot de passe doit contenir au moins un caractère spécial';
	} else {
		validationErrors.value.password = '';
	}
};

const checkPasswordMatch = () => {
	if (passwordData.password && passwordData.password_confirmation) {
		passwordsMatch.value = passwordData.password === passwordData.password_confirmation;

		if (!passwordsMatch.value) {
			validationErrors.value.password_confirmation = 'Les mots de passe ne correspondent pas';
		} else {
			validationErrors.value.password_confirmation = '';
		}
	} else {
		passwordsMatch.value = false;
		validationErrors.value.password_confirmation = '';
	}
};

const isFormValid = computed(() => {
	return (
		passwordData.password.length >= 8 &&
		passwordData.password_confirmation.length >= 8 &&
		passwordsMatch.value &&
		passwordStrength.value !== 'weak' &&
		!validationErrors.value.password &&
		!validationErrors.value.password_confirmation
	);
});

const handlePasswordSetup = async () => {
	if (!isFormValid.value) {
		validationErrors.value._general = 'Veuillez corriger les erreurs avant de soumettre';
		return;
	}

	isLoading.value = true;
	validationErrors.value._general = '';

	try {
		await authStore.configurePassword(passwordData);
		navigateTo(AppUrl.DASHBOARD);
	} catch (error: any) {
		console.error("Erreur lors de la configuration du mot de passe:", error);
		validationErrors.value._general = error.message || 'Une erreur est survenue lors de la configuration';
	} finally {
		isLoading.value = false;
	}
};
</script>
