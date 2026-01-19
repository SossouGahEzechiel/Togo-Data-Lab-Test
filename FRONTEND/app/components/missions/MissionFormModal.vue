<template>
	<!-- Overlay du modal -->
	<div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
		<!-- Overlay sombre -->
		<div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$emit('close')"></div>

		<!-- Contenu du modal -->
		<div class="flex min-h-full items-center justify-center p-4">
			<div class="relative bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-[700px] w-full max-h-[90vh] overflow-y-auto">
				<!-- En-tête du modal -->
				<div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-dark-600">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
						{{ isEdit ? 'Modifier la mission' : 'Nouvelle mission' }}
					</h3>
					<button @click="$emit('close')"
						class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
						<span class="sr-only">Fermer</span>
						<XMarkIcon class="w-6 h-6" />
					</button>
				</div>

				<!-- Corps du modal -->
				<div class="p-4 space-y-8">
					<form @submit.prevent="handleSubmit" class="space-y-4">
						<!-- Champ Libellé -->
						<div>
							<label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Libellé *
							</label>
							<input id="label" v-model="formData.label" type="text" required class="input-field w-full"
								:class="{ 'border-red-500': errors.label }" placeholder="Ex: Mission de maintenance" />
							<p v-if="errors.label" class="text-red-500 text-xs mt-1">{{ errors.label }}</p>
						</div>

						<!-- Champ Description -->
						<div>
							<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Description
							</label>
							<textarea id="description" v-model="formData.description" rows="3" class="input-field w-full resize-none"
								:class="{ 'border-red-500': errors.description }"
								placeholder="Description détaillée de la mission..."></textarea>
							<p v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</p>
						</div>

						<!-- Champ Date de début -->
						<div>
							<label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Date de début *
							</label>
							<input id="from" v-model="formData.from" type="date" required class="input-field w-full"
								:class="{ 'border-red-500': errors.from }" />
							<p v-if="errors.from" class="text-red-500 text-xs mt-1">{{ errors.from }}</p>
						</div>

						<!-- Champ Date de fin -->
						<div>
							<label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
								Date de fin (optionnel)
							</label>
							<input id="to" v-model="formData.to" type="date" :min="formData.from" class="input-field w-full"
								:class="{ 'border-red-500': errors.to }" />
							<p v-if="errors.to" class="text-red-500 text-xs mt-1">{{ errors.to }}</p>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
								Laissez vide si la mission est toujours en cours
							</p>
						</div>

						<!-- Boutons d'action -->
						<div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-dark-600">
							<button type="button" @click="$emit('close')"
								class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700 rounded-lg transition-colors">
								Annuler
							</button>
							<button type="submit" :disabled="isLoading" :class="[
								'btn-primary px-4 py-2 text-sm font-medium flex items-center gap-2',
								isLoading ? 'opacity-50 cursor-not-allowed' : ''
							]">
								<Spinner v-if="isLoading" :is-loading="true" size="sm" />
								<span>{{ isEdit ? 'Mettre à jour' : 'Créer' }}</span>
							</button>
						</div>
					</form>

					<!-- Informations supplémentaires -->
					<div class="px-6 py-4 bg-gray-50 dark:bg-dark-700">
						<p class="text-sm text-gray-500 dark:text-gray-400">
							Informations supplémentaires concernant cette mission.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import Spinner from '~/components/partials/spinner.vue';
import type { Mission } from '~/types/Mission';
import { initMissionForm } from '~/types/Mission';

interface Props {
	isOpen: boolean;
	mission?: Mission | null;
	isEdit?: boolean;
}

interface Emits {
	(event: 'close'): void;
	(event: 'save', mission: Mission): void;
}

const props = withDefaults(defineProps<Props>(), {
	isOpen: false,
	mission: null,
	isEdit: false
});

const emit = defineEmits<Emits>();

const formData = ref(initMissionForm(props.mission || undefined));
const errors = ref<Record<string, string>>({});
const isLoading = ref(false);

// Mettre à jour le formulaire quand la mission change
watch(() => props.mission, (newMission) => {
	if (newMission) {
		formData.value = initMissionForm(newMission);
	} else {
		formData.value = initMissionForm();
	}
}, { immediate: true });

// Validation du formulaire
const validateForm = (): boolean => {
	const newErrors: Record<string, string> = {};

	if (!formData.value.label.trim()) {
		newErrors.label = 'Le libellé est obligatoire';
	}

	if (!formData.value.from) {
		newErrors.from = 'La date de début est obligatoire';
	}

	if (formData.value.to && new Date(formData.value.to) < new Date(formData.value.from)) {
		newErrors.to = 'La date de fin doit être postérieure à la date de début';
	}

	errors.value = newErrors;
	return Object.keys(newErrors).length === 0;
};

// Soumission du formulaire
const handleSubmit = async () => {
	if (!validateForm()) return;

	isLoading.value = true;
	try {
		// Clean data
		const missionData: Mission = {
			...formData.value,
			to: formData.value.to || null
		};

		emit('save', missionData);
	} catch (error) {
		console.error('Erreur lors de la soumission du formulaire:', error);
	} finally {
		isLoading.value = false;
	}
};
</script>
