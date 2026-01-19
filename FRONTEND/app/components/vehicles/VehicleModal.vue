<template>
	<TransitionRoot appear :show="isOpen" as="template">
		<Dialog as="div" @close="close" class="relative z-50">
			<TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
				leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 bg-black bg-opacity-25" />
			</TransitionChild>

			<div class="fixed inset-0 overflow-y-auto">
				<div class="flex min-h-full items-center justify-center p-4 text-center">
					<TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
						enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
						leave-to="opacity-0 scale-95">
						<DialogPanel
							class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white dark:bg-dark-800 p-6 text-left align-middle shadow-xl transition-all">
							<DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
								{{ mode === 'create' ? 'Ajouter un véhicule' : 'Modifier le véhicule' }}
							</DialogTitle>

							<!-- Formulaire -->
							<div class="mt-4">
								<form @submit.prevent="save" class="space-y-6">
									<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
										<!-- Marque -->
										<div>
											<label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Marque *
											</label>
											<input id="brand" v-model="form.brand" type="text" required class="input-field"
												:class="{ 'border-red-500': validationErrors.brand }" placeholder="Ex: Toyota" />
											<p v-if="validationErrors.brand" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.brand }}
											</p>
										</div>

										<!-- Modèle -->
										<div>
											<label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Modèle *
											</label>
											<input id="model" v-model="form.model" type="text" required class="input-field"
												:class="{ 'border-red-500': validationErrors.model }" placeholder="Ex: RAV4" />
											<p v-if="validationErrors.model" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.model }}
											</p>
										</div>

										<!-- Type de véhicule -->
										<div>
											<label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Type de véhicule *
											</label>
											<select id="type" v-model="form.type" required class="input-field"
												:class="{ 'border-red-500': validationErrors.type }">
												<option value="">Sélectionnez un type</option>
												<option v-for="type in vehicleTypes" :key="type" :value="type">
													{{ type }}
												</option>
											</select>
											<p v-if="validationErrors.type" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.type }}
											</p>
										</div>

										<!-- Numéro d'immatriculation -->
										<div>
											<label for="registrationNumber"
												class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Numéro d'immatriculation *
											</label>
											<input id="registrationNumber" v-model="form.registrationNumber" type="text" required
												class="input-field uppercase" :class="{ 'border-red-500': validationErrors.registrationNumber }"
												placeholder="Ex: TG-1234-AB" />
											<InvalidField :message="validationErrors.registrationNumber" />
										</div>

										<!-- Date d'immatriculation -->
										<div>
											<label for="registrationDate"
												class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Date d'immatriculation *
											</label>
											<input id="registrationDate" v-model="form.registrationDate" type="date" required
												class="input-field" :class="{ 'border-red-500': validationErrors.registrationDate }" />
											<InvalidField :message="validationErrors.registrationDate" />
										</div>

										<!-- Nombre de places -->
										<div>
											<label for="seatsCount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Nombre de places *
											</label>
											<input id="seatsCount" v-model.number="form.seatsCount" type="number" min="1" max="50" required
												class="input-field" :class="{ 'border-red-500': validationErrors.seatsCount }"
												placeholder="Ex: 5" />
											<InvalidField :message="validationErrors.seatsCount" />
										</div>

										<!-- Statut -->
										<div>
											<label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Statut *
											</label>
											<select id="status" v-model="form.status" required class="input-field"
												:class="{ 'border-red-500': validationErrors.status }" @change="handleStatusChange">
												<option value="">Sélectionnez un statut</option>
												<option v-for="status in vehicleStatuses" :key="status" :value="status">
													{{ status }}
												</option>
											</select>
											<InvalidField :message="validationErrors.status" />
										</div>

										<!-- Raison (uniquement pour certains statuts) -->
										<div v-if="showReasonField" class="md:col-span-2">
											<label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Raison {{ form.status === 'En réparation' ? 'de la réparation' : '' }}
											</label>
											<textarea id="reason" v-model="form.reason" rows="3" class="input-field"
												:class="{ 'border-red-500': validationErrors.reason }" :placeholder="getReasonPlaceholder()" />
											<InvalidField :message="validationErrors.reason" />
										</div>

										<!-- Upload d'images -->
										<div class="md:col-span-2">
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Images du véhicule
											</label>
											<div
												class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 dark:border-dark-600 px-6 py-10">
												<div class="text-center">
													<PhotoIcon class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
													<div class="mt-4 flex text-sm text-gray-600 dark:text-gray-400">
														<label for="file-upload"
															class="relative cursor-pointer rounded-md bg-white dark:bg-dark-800 font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300">
															<span>Télécharger des fichiers</span>
															<input id="file-upload" type="file" multiple accept="image/*" class="sr-only"
																@change="handleFileUpload" />
														</label>
														<p class="pl-1">ou glisser-déposer</p>
													</div>
													<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
														PNG, JPG, GIF jusqu'à 10MB
													</p>
												</div>
											</div>

											<!-- Prévisualisation des images -->
											<div v-if="previewImages.length > 0" class="mt-4">
												<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
													<div v-for="(image, index) in previewImages" :key="index" class="relative group">
														<img :src="image.url" :alt="`Image ${index + 1}`"
															class="h-20 w-full object-cover rounded-lg" />
														<button type="button" @click="removeImage(index)"
															class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
															<XMarkIcon class="w-4 h-4" />
														</button>
													</div>
												</div>
											</div>

											<!-- Images existantes -->
											<div v-if="existingImages.length > 0" class="mt-4">
												<p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
													Images existantes
												</p>
												<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
													<div v-for="(image, index) in existingImages" :key="image.id || index" class="relative group">
														<img :src="pathToTheServer(image.path) || '/placeholder-vehicle.jpg'" :alt="`Image existante ${index + 1}`"
															class="h-20 w-full object-cover rounded-lg" />
														<button v-if="mode === 'edit'" type="button" @click="removeExistingImage(index)"
															class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
															<XMarkIcon class="w-4 h-4" />
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Messages d'erreur généraux -->
									<div v-if="validationErrors._general" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
										<div class="flex">
											<ExclamationCircleIcon class="h-5 w-5 text-red-400" />
											<div class="ml-3">
												<h3 class="text-sm font-medium text-red-800 dark:text-red-400">
													Erreur de validation
												</h3>
												<div class="mt-2 text-sm text-red-700 dark:text-red-300">
													<p>{{ validationErrors._general }}</p>
												</div>
											</div>
										</div>
									</div>

									<!-- Actions -->
									<div class="mt-6 flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-dark-700">
										<button type="button" @click="close"
											class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
											Annuler
										</button>
										<button type="submit" :disabled="isPersisting" :class="[
											'btn-primary inline-flex items-center',
											isPersisting ? 'opacity-50 cursor-not-allowed' : ''
										]">
											<svg v-if="isPersisting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
												xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
												<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
												</circle>
												<path class="opacity-75" fill="currentColor"
													d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
												</path>
											</svg>
											{{ isPersisting ? 'Enregistrement...' : (mode === 'create' ? 'Créer' : 'Mettre à jour') }}
										</button>
									</div>
								</form>
							</div>
						</DialogPanel>
					</TransitionChild>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>

<script setup lang="ts">
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { PhotoIcon, XMarkIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import type { Vehicle } from '~/types/Vehicle'
import { initVehicleForm } from '~/types/Vehicle'
import { useVehicleStore } from '~/stores/VehicleStore'
import { ref, watch, computed, reactive } from 'vue'
import { pathToTheServer, type Image } from '~/types/Image'
import { vehicleStatuses, vehicleTypes } from '~/types/VehicleEnums'
import InvalidField from '../partials/InvalidField.vue'

const props = defineProps<{
	isOpen: boolean
	vehicle: Vehicle | null
	mode: 'create' | 'edit'
}>()

const emit = defineEmits<{
	close: []
	saved: []
}>()

const vehicleStore = useVehicleStore()
const isPersisting = computed(() => vehicleStore.isPersisting)
const validationErrors = computed(() => vehicleStore.errors)

// Données du formulaire
const form = reactive<Vehicle>(initVehicleForm())
const previewImages = ref<Array<{ file: File; url: string }>>([])
const existingImages = ref<Image[]>([])

// Champ de raison conditionnel
const showReasonField = computed(() => {
	return form.status === 'Indisponible' ||
		form.status === 'En réparation' ||
		form.status === 'Suspendu'
})

// Méthodes
const handleStatusChange = () => {
	// Réinitialiser la raison si le statut ne la nécessite plus
	if (!showReasonField.value) {
		form.reason = null
	}
}

const getReasonPlaceholder = () => {
	switch (form.status) {
		case 'En réparation':
			return 'Décrivez la nature de la réparation...'
		case 'Indisponible':
			return 'Expliquez pourquoi le véhicule est indisponible...'
		case 'Suspendu':
			return 'Raison de la suspension...'
		default:
			return 'Raison...'
	}
}

const handleFileUpload = (event: Event) => {
	const input = event.target as HTMLInputElement
	if (input.files) {
		for (let i = 0; i < input.files.length; i++) {
			const file = input.files[i]
			if (file.size > 10 * 1024 * 1024) { // 10MB max
				alert(`Le fichier ${file.name} dépasse la taille maximale de 10MB`)
				continue
			}

			const reader = new FileReader()
			reader.onload = (e) => {
				previewImages.value.push({
					file,
					url: e.target?.result as string
				})
			}
			reader.readAsDataURL(file)
		}
		// Réinitialiser l'input pour permettre le téléchargement des mêmes fichiers
		input.value = ''
	}
}

const removeImage = (index: number) => {
	previewImages.value.splice(index, 1)
}

const removeExistingImage = (index: number) => {
	existingImages.value.splice(index, 1)
	// Mettre à jour le formulaire
	form.images = existingImages.value
}

const save = async () => {
	try {
		// Préparer les données
		const vehicleData = {
			...form,
			images: existingImages.value // Inclure les images existantes restantes
		}

		if (props.mode === 'create') {
			await vehicleStore.store(vehicleData)
		} else {
			await vehicleStore.update(vehicleData)
		}

		// Gérer l'upload des nouvelles images séparément si nécessaire
		// (à implémenter selon votre API)

		emit('saved')
	} catch (error) {
		console.error('Erreur lors de la sauvegarde:', error)
	}
}

const close = () => {
	// Réinitialiser le formulaire
	Object.assign(form, initVehicleForm())
	previewImages.value = []
	existingImages.value = []
	emit('close')
}

// Watcher pour pré-remplir le formulaire quand un véhicule est fourni
watch(() => props.vehicle, (newVehicle) => {
	if (newVehicle) {
		Object.assign(form, newVehicle)
		//@ts-ignore
		existingImages.value = [...newVehicle.images]
	} else {
		Object.assign(form, initVehicleForm())
		existingImages.value = []
	}
}, { immediate: true })

// Watcher pour réinitialiser les images de prévisualisation quand le modal se ferme
watch(() => props.isOpen, (isOpen) => {
	if (!isOpen) {
		previewImages.value = []
	}
})
</script>
