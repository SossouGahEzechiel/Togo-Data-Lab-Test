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
							class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-dark-800 p-6 text-left align-middle shadow-xl transition-all">
							<div class="flex items-center">
								<div
									class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
									<ExclamationTriangleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
								</div>
							</div>
							<div class="mt-3 text-center sm:mt-5">
								<DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
									Supprimer la réservation
								</DialogTitle>
								<div class="mt-2">
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Êtes-vous sûr de vouloir supprimer la réservation pour
										<span class="font-semibold">{{ reservation?.mission?.label || 'cette mission' }}</span> ?
									</p>
									<div class="mt-4 bg-gray-50 dark:bg-dark-700 rounded-lg p-3 text-sm">
										<p class="text-gray-700 dark:text-gray-300">
											<strong>Mission:</strong> {{ reservation?.mission?.label || 'N/A' }}
										</p>
										<p class="text-gray-700 dark:text-gray-300 mt-1">
											<strong>Véhicule:</strong> {{ reservation?.vehicle?.brand }} {{ reservation?.vehicle?.model }} ({{
												reservation?.vehicle?.registrationNumber }})
										</p>
										<p class="text-gray-700 dark:text-gray-300 mt-1">
											<strong>Période:</strong> {{ formatDate(reservation?.from) }} → {{ formatDate(reservation?.to) }}
										</p>
										<p class="text-gray-700 dark:text-gray-300 mt-1">
											<strong>Statut:</strong> {{ reservation?.status }}
										</p>
									</div>
									<p class="text-xs text-red-600 dark:text-red-400 mt-2">
										⚠️ Cette action est irréversible et supprimera définitivement la réservation.
									</p>
								</div>
							</div>

							<div class="mt-6 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
								<button type="button" @click="close"
									class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
									Annuler
								</button>
								<button type="button" @click="confirm" :disabled="isDeleting" :class="[
									'mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm',
									isDeleting ? 'opacity-50 cursor-not-allowed' : ''
								]">
									<svg v-if="isDeleting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
										xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor"
											d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
										</path>
									</svg>
									{{ isDeleting ? 'Suppression...' : 'Supprimer' }}
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
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import type { Reservation } from '~/types/Reservation'

const props = defineProps<{
	isOpen: boolean
	reservation: Reservation | null
}>()

const emit = defineEmits<{
	close: []
	confirmed: []
}>()

const isDeleting = ref(false)

const formatDate = (dateString?: string) => {
	if (!dateString) return 'N/A'
	return new Date(dateString).toLocaleDateString('fr-FR', {
		year: 'numeric',
		month: '2-digit',
		day: '2-digit'
	})
}

const close = () => {
	isDeleting.value = false
	emit('close')
}

const confirm = async () => {
	isDeleting.value = true
	try {
		emit('confirmed')
	} catch (error) {
		console.error('Erreur lors de la suppression:', error)
		isDeleting.value = false
	}
}
</script>