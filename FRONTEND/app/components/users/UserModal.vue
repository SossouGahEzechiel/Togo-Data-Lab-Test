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
							class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white dark:bg-dark-800 p-6 text-left align-middle shadow-xl transition-all">
							<DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
								{{ mode === 'create' ? 'Nouvel utilisateur' : 'Modifier l\'utilisateur' }}
							</DialogTitle>

							<!-- Formulaire -->
							<div class="mt-4">
								<form @submit.prevent="save" class="space-y-6">
									<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
										<!-- Prénom -->
										<div>
											<label for="firstName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Prénom *
											</label>
											<input id="firstName" v-model="form.firstName" type="text" required class="input-field"
												:class="{ 'border-red-500': validationErrors.firstName }" placeholder="Ex: Jean" />
											<p v-if="validationErrors.firstName" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.firstName }}
											</p>
										</div>

										<!-- Nom -->
										<div>
											<label for="lastName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Nom *
											</label>
											<input id="lastName" v-model="form.lastName" type="text" required class="input-field"
												:class="{ 'border-red-500': validationErrors.lastName }" placeholder="Ex: Dupont" />
											<p v-if="validationErrors.lastName" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.lastName }}
											</p>
										</div>

										<!-- Email -->
										<div>
											<label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Email *
											</label>
											<input id="email" v-model="form.email" type="email" required class="input-field"
												:class="{ 'border-red-500': validationErrors.email }"
												placeholder="Ex: jean.dupont@example.com" />
											<p v-if="validationErrors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.email }}
											</p>
										</div>

										<!-- Téléphone -->
										<div>
											<label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Téléphone
											</label>
											<input id="phone" v-model="form.phone" type="tel" class="input-field"
												:class="{ 'border-red-500': validationErrors.phone }" placeholder="Ex: +228 90 12 34 56" />
											<p v-if="validationErrors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.phone }}
											</p>
										</div>

										<!-- Rôle -->
										<div>
											<label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Rôle *
											</label>
											<select id="role" v-model="form.role" required class="input-field"
												:class="{ 'border-red-500': validationErrors.role }">
												<option value="">Sélectionnez un rôle</option>
												<option v-for="role in userRoles" :key="role" :value="role">
													{{ role }}
												</option>
											</select>
											<p v-if="validationErrors.role" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.role }}
											</p>
										</div>

										<!-- Statut -->
										<div>
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Statut
											</label>
											<div class="flex items-center space-x-4">
												<label class="flex items-center">
													<input type="radio" v-model="form.isActive" :value="true"
														class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
													<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</span>
												</label>
												<label class="flex items-center">
													<input type="radio" v-model="form.isActive" :value="false"
														class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
													<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactif</span>
												</label>
											</div>
											<p v-if="validationErrors.isActive" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.isActive }}
											</p>
										</div>

										<!-- Photo de profil -->
										<div class="md:col-span-2">
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Photo de profil
											</label>
											<div class="mt-2 flex items-center space-x-6">
												<div v-if="previewImage || (form.image?.path && mode === 'edit')" class="relative">
													<img :src="previewImage || pathToTheServer(form.image?.path || '')" alt="Photo de profil"
														class="h-24 w-24 rounded-full object-cover" />
													<button type="button" @click="removeImage"
														class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
														<XMarkIcon class="w-4 h-4" />
													</button>
												</div>
												<div v-else
													class="h-24 w-24 rounded-full bg-gray-200 dark:bg-dark-700 flex items-center justify-center">
													<UserIcon class="h-12 w-12 text-gray-400" />
												</div>
												<div>
													<label class="cursor-pointer">
														<input type="file" ref="fileInput" class="hidden" accept="image/*"
															@change="handleImageUpload" />
														<div class="btn-primary inline-flex items-center gap-2">
															<PhotoIcon class="w-4 h-4" />
															{{ previewImage || form.image?.path ? 'Changer la photo' : 'Ajouter une photo' }}
														</div>
													</label>
													<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
														PNG, JPG jusqu'à 2MB
													</p>
												</div>
											</div>
										</div>
									</div>

									<!-- Informations complémentaires -->
									<div v-if="mode === 'create'" class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
										<div class="flex">
											<InformationCircleIcon class="h-5 w-5 text-blue-400" />
											<div class="ml-3">
												<h3 class="text-sm font-medium text-blue-800 dark:text-blue-400">
													Information importante
												</h3>
												<div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
													<p>Un email sera automatiquement envoyé à l'utilisateur pour configurer son mot de passe lors
														de sa première connexion.</p>
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
import { PhotoIcon, XMarkIcon, UserIcon, InformationCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import type { User } from '~/types/User'
import { useUserStore } from '~/stores/UserStore'
import { ref, computed, reactive, watch } from 'vue'
import { userRoles } from '~/types/UserRoleEnum'
import { pathToTheServer } from '~/types/Image'

const props = defineProps<{
	isOpen: boolean
	user: User | null
	mode: 'create' | 'edit'
}>()

const emit = defineEmits<{
	close: []
	saved: []
}>()

const userStore = useUserStore()
const fileInput = ref<HTMLInputElement | null>(null)

const isPersisting = computed(() => userStore.isPersisting)
const validationErrors = computed(() => userStore.errors)

// Données du formulaire
const form = reactive({
	id: '',
	firstName: '',
	lastName: '',
	email: '',
	phone: '',
	role: 'Employé',
	isActive: true,
	image: null as any
})

// Image de prévisualisation
const previewImage = ref<string | null>(null)

// Méthodes
const handleImageUpload = (event: Event) => {
	const input = event.target as HTMLInputElement
	if (input.files && input.files[0]) {
		const file = input.files[0]

		// Vérifier la taille du fichier (max 2MB)
		if (file.size > 2 * 1024 * 1024) {
			alert('Le fichier est trop volumineux. Taille maximale: 2MB')
			return
		}

		const reader = new FileReader()
		reader.onload = (e) => {
			previewImage.value = e.target?.result as string
			// Stocker le fichier pour l'envoi
			form.image = file
		}
		reader.readAsDataURL(file)
	}
}

const removeImage = () => {
	previewImage.value = null
	form.image = null
	if (fileInput.value) {
		fileInput.value.value = ''
	}
}

const save = async () => {
	try {
		// Préparer les données pour l'envoi
		const userData = new FormData()

		// Ajouter les champs texte
		Object.keys(form).forEach(key => {
			if (key !== 'image' && form[key as keyof typeof form] !== null && form[key as keyof typeof form] !== undefined) {
				userData.append(key, String(form[key as keyof typeof form]))
			}
		})

		// Ajouter l'image si elle existe
		if (form.image instanceof File) {
			userData.append('image', form.image)
		}

		if (props.mode === 'create') {
			await userStore.store(userData as any)
		} else {
			await userStore.update(userData as any)
		}

		emit('saved')
	} catch (error) {
		console.error('Erreur lors de la sauvegarde:', error)
	}
}

const close = () => {
	// Réinitialiser le formulaire
	Object.keys(form).forEach(key => {
		if (key === 'role') {
			form[key] = 'Employé'
		} else if (key === 'isActive') {
			form[key] = true
		} else if (key !== 'id') {
			(form as any)[key] = ''
		}
	})
	previewImage.value = null
	if (fileInput.value) {
		fileInput.value.value = ''
	}
	emit('close')
}

// Watcher pour pré-remplir le formulaire quand un utilisateur est fourni
watch(() => props.user, (newUser) => {
	if (newUser) {
		Object.assign(form, {
			id: newUser.id || '',
			firstName: newUser.firstName || '',
			lastName: newUser.lastName || '',
			email: newUser.email || '',
			phone: newUser.phone || '',
			role: newUser.role || 'Employé',
			isActive: newUser.isActive ?? true,
			image: newUser.image || null
		})
		previewImage.value = null
	} else {
		Object.keys(form).forEach(key => {
			if (key === 'role') {
				form[key] = 'Employé'
			} else if (key === 'isActive') {
				form[key] = true
			} else if (key !== 'id') {
				(form as any)[key] = ''
			}
		})
		previewImage.value = null
	}
}, { immediate: true })

// Watcher pour réinitialiser l'image quand le modal se ferme
watch(() => props.isOpen, (isOpen) => {
	if (!isOpen) {
		previewImage.value = null
		if (fileInput.value) {
			fileInput.value.value = ''
		}
	}
})
</script>