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
								{{ mode === 'create' ? 'Ajouter un véhicule' : 'Modifier le véhicule' }}
							</DialogTitle>

							<!-- Formulaire va ici -->
							<div class="mt-2">
								<p class="text-sm text-gray-500 dark:text-gray-400">
									Formulaire de saisie des informations du véhicule
								</p>
							</div>

							<div class="mt-6 flex justify-end space-x-3">
								<button type="button" @click="close"
									class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
									Annuler
								</button>
								<button type="button" @click="save" class="btn-primary">
									{{ mode === 'create' ? 'Créer' : 'Mettre à jour' }}
								</button>
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
import type { Vehicle } from '~/types/Vehicle'

const props = defineProps<{
	isOpen: boolean
	vehicle: Vehicle | null
	mode: 'create' | 'edit'
}>()

const emit = defineEmits<{
	close: []
	saved: []
}>()

const close = () => {
	emit('close')
}

const save = () => {
	// Logique de sauvegarde
	emit('saved')
}
</script>
