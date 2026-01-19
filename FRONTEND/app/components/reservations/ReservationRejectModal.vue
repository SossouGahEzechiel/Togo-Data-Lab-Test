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
									<XCircleIcon class="h-6 w-6 text-red-600 dark:text-red-400" />
								</div>
							</div>
							<div class="mt-3 text-center sm:mt-5">
								<DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
									Rejeter la réservation
								</DialogTitle>
								<div class="mt-2">
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Êtes-vous sûr de vouloir rejeter la réservation pour
										<span class="font-semibold">{{ reservation?.mission?.label }}</span> ?
									</p>
									<div class="mt-4">
										<label for="rejectionReason"
											class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
											Raison du rejet (optionnel)
										</label>
										<textarea id="rejectionReason" v-model="rejectionReason" rows="3" class="input-field w-full"
											placeholder="Expliquez la raison du rejet..." />
									</div>
								</div>
							</div>

							<div class="mt-6 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
								<button type="button" @click="close"
									class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
									Annuler
								</button>
								<button type="button" @click="confirm"
									class="mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">
									Rejeter
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
import { XCircleIcon } from '@heroicons/vue/24/outline'
import type { Reservation } from '~/types/Reservation'
import { ref } from 'vue'

const props = defineProps<{
	isOpen: boolean
	reservation: Reservation | null
}>()

const emit = defineEmits<{
	close: []
	confirmed: [reason: string]
}>()

const rejectionReason = ref('')

const close = () => {
	rejectionReason.value = ''
	emit('close')
}

const confirm = () => {
	emit('confirmed', rejectionReason.value)
	rejectionReason.value = ''
}
</script>