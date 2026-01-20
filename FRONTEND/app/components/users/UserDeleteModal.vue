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
									Supprimer l'utilisateur
								</DialogTitle>
								<div class="mt-2">
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Êtes-vous sûr de vouloir supprimer l'utilisateur
										<span class="font-semibold">{{ user?.fullName }}</span> ?
									</p>
									<div class="mt-4 bg-gray-50 dark:bg-dark-700 rounded-lg p-3 text-sm">
										<p class="text-gray-700 dark:text-gray-300">
											<strong>Email:</strong> {{ user?.email }}
										</p>
										<p class="text-gray-700 dark:text-gray-300 mt-1">
											<strong>Rôle:</strong> {{ user?.role }}
										</p>
										<p class="text-gray-700 dark:text-gray-300 mt-1">
											<strong>Statut:</strong> {{ user?.isActive ? 'Actif' : 'Inactif' }}
										</p>
									</div>
									<div v-if="hasLinkedReservations" class="mt-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-3">
										<div class="flex">
											<ExclamationCircleIcon class="h-5 w-5 text-yellow-400" />
											<div class="ml-3">
												<h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
													Attention
												</h3>
												<div class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
													<p>Cet utilisateur a des réservations associées. La suppression est déconseillée.</p>
													<p class="mt-1 text-xs">Considérez plutôt la désactivation du compte.</p>
												</div>
											</div>
										</div>
									</div>
									<p class="text-xs text-red-600 dark:text-red-400 mt-2">
										⚠️ Cette action est irréversible et supprimera définitivement le compte utilisateur.
									</p>
								</div>
							</div>

							<div class="mt-6 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
								<button type="button" @click="close"
									class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
									Annuler
								</button>
								<button type="button" @click="confirm" :disabled="isDeleting || hasLinkedReservations" :class="[
									'mt-3 inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm',
									(isDeleting || hasLinkedReservations) ? 'opacity-50 cursor-not-allowed' : ''
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
import { ExclamationTriangleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import type { User } from '~/types/User'
import { useReservationStore } from '~/stores/ReservationStore'
import { computed, ref } from 'vue'

const props = defineProps<{
	isOpen: boolean
	user: User | null
}>()

const emit = defineEmits<{
	close: []
	confirmed: []
}>()

const reservationStore = useReservationStore()
const isDeleting = ref(false)

// Vérifier si l'utilisateur a des réservations associées
const hasLinkedReservations = computed(() => {
	if (!props.user) return false

	// Vérifier dans les réservations si l'utilisateur est demandeur ou conducteur
	const reservations = reservationStore.reservations || []
	return reservations.some(reservation =>
		reservation.userId === props.user?.id ||
		reservation.driverId === props.user?.id
	)
})

const close = () => {
	isDeleting.value = false
	emit('close')
}

const confirm = async () => {
	if (hasLinkedReservations.value) {
		// Ne pas supprimer si l'utilisateur a des réservations
		return
	}

	isDeleting.value = true
	try {
		emit('confirmed')
	} catch (error) {
		console.error('Erreur lors de la suppression:', error)
		isDeleting.value = false
	}
}
</script>