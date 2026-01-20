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
								<div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full"
									:class="newStatus ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900'">
									<PowerIcon class="h-6 w-6"
										:class="newStatus ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" />
								</div>
							</div>
							<div class="mt-3 text-center sm:mt-5">
								<DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
									{{ newStatus ? 'Activer l\'utilisateur' : 'Désactiver l\'utilisateur' }}
								</DialogTitle>
								<div class="mt-2">
									<p class="text-sm text-gray-500 dark:text-gray-400">
										Êtes-vous sûr de vouloir {{ newStatus ? 'activer' : 'désactiver' }} l'utilisateur
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
											<strong>Statut actuel:</strong> {{ user?.isActive ? 'Actif' : 'Inactif' }}
										</p>
									</div>
									<p v-if="!newStatus" class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
										⚠️ L'utilisateur ne pourra plus se connecter
									</p>
								</div>
							</div>

							<div class="mt-6 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
								<button type="button" @click="close"
									class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
									Annuler
								</button>
								<button type="button" @click="confirm" :class="[
									'mt-3 inline-flex w-full justify-center rounded-md px-4 py-2 text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm',
									newStatus ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
								]">
									{{ newStatus ? 'Activer' : 'Désactiver' }}
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
import { PowerIcon } from '@heroicons/vue/24/outline'
import type { User } from '~/types/User'

const props = defineProps<{
	isOpen: boolean
	user: User | null
	newStatus: boolean
}>()

const emit = defineEmits<{
	close: []
	confirmed: []
}>()

const close = () => {
	emit('close')
}

const confirm = () => {
	emit('confirmed')
}
</script>