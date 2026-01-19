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
								{{ mode === 'create' ? 'Nouvelle réservation' : 'Modifier la réservation' }}
							</DialogTitle>

							<!-- Formulaire -->
							<div class="mt-4">
								<form @submit.prevent="save" class="space-y-6">
									<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
										<!-- Mission -->
										<div>
											<label for="missionId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Mission *
											</label>
											<select
												id="missionId"
												v-model="form.missionId"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.missionId }"
											>
												<option value="">Sélectionnez une mission</option>
												<option v-for="mission in missions" :key="mission.id" :value="mission.id">
													{{ mission.label }}
												</option>
											</select>
											<p v-if="validationErrors.missionId" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.missionId }}
											</p>
										</div>

										<!-- Véhicule -->
										<div>
											<label for="vehicleId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Véhicule *
											</label>
											<select
												id="vehicleId"
												v-model="form.vehicleId"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.vehicleId }"
											>
												<option value="">Sélectionnez un véhicule</option>
												<option v-for="vehicle in availableVehicles" :key="vehicle.id" :value="vehicle.id">
													{{ vehicle.brand }} {{ vehicle.model }} ({{ vehicle.registrationNumber }})
												</option>
											</select>
											<p v-if="validationErrors.vehicleId" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.vehicleId }}
											</p>
										</div>

										<!-- Conducteur -->
										<div>
											<label for="driverId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Conducteur *
											</label>
											<select
												id="driverId"
												v-model="form.driverId"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.driverId }"
											>
												<option value="">Sélectionnez un conducteur</option>
												<option v-for="driver in drivers" :key="driver.id" :value="driver.id">
													{{ driver.name }} - {{ driver.email }}
												</option>
											</select>
											<p v-if="validationErrors.driverId" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.driverId }}
											</p>
										</div>

										<!-- Statut -->
										<div>
											<label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Statut *
											</label>
											<select
												id="status"
												v-model="form.status"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.status }"
											>
												<option value="">Sélectionnez un statut</option>
												<option v-for="status in reservationStatuses" :key="status" :value="status">
													{{ status }}
												</option>
											</select>
											<p v-if="validationErrors.status" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.status }}
											</p>
										</div>

										<!-- Date de début -->
										<div>
											<label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Date de début *
											</label>
											<input
												id="from"
												v-model="form.from"
												type="datetime-local"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.from }"
											/>
											<p v-if="validationErrors.from" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.from }}
											</p>
										</div>

										<!-- Date de fin -->
										<div>
											<label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Date de fin *
											</label>
											<input
												id="to"
												v-model="form.to"
												type="datetime-local"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.to }"
											/>
											<p v-if="validationErrors.to" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.to }}
											</p>
										</div>

										<!-- Date de retour (optionnel) -->
										<div>
											<label for="returnDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Date de retour (optionnel)
											</label>
											<input
												id="returnDate"
												v-model="form.returnDate"
												type="datetime-local"
												class="input-field"
												:class="{ 'border-red-500': validationErrors.returnDate }"
											/>
											<p v-if="validationErrors.returnDate" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.returnDate }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
												À remplir uniquement si le véhicule est déjà retourné
											</p>
										</div>

										<!-- Utilisateur demandeur -->
										<div>
											<label for="userId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Demandeur *
											</label>
											<select
												id="userId"
												v-model="form.userId"
												required
												class="input-field"
												:class="{ 'border-red-500': validationErrors.userId }"
											>
												<option value="">Sélectionnez un demandeur</option>
												<option v-for="user in users" :key="user.id" :value="user.id">
													{{ user.name }} - {{ user.email }}
												</option>
											</select>
											<p v-if="validationErrors.userId" class="mt-1 text-sm text-red-600 dark:text-red-400">
												{{ validationErrors.userId }}
											</p>
										</div>

										<!-- Informations complémentaires -->
										<div class="md:col-span-2">
											<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
												Informations complémentaires
											</label>
											<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-dark-700 rounded-lg">
												<div v-if="selectedMission" class="space-y-1">
													<h4 class="text-sm font-medium text-gray-900 dark:text-white">Mission sélectionnée</h4>
													<p class="text-xs text-gray-600 dark:text-gray-400">{{ selectedMission.description }}</p>
													<p class="text-xs text-gray-500 dark:text-gray-400">Type: {{ selectedMission.type }}</p>
												</div>
												<div v-if="selectedVehicle" class="space-y-1">
													<h4 class="text-sm font-medium text-gray-900 dark:text-white">Véhicule sélectionné</h4>
													<p class="text-xs text-gray-600 dark:text-gray-400">{{ selectedVehicle.brand }} {{ selectedVehicle.model }}</p>
													<p class="text-xs text-gray-500 dark:text-gray-400">{{ selectedVehicle.type }} - {{ selectedVehicle.seatsCount }} places</p>
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
										<button
											type="button"
											@click="close"
											class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600"
										>
											Annuler
										</button>
										<button
											type="submit"
											:disabled="isPersisting"
											:class="[
												'btn-primary inline-flex items-center',
												isPersisting ? 'opacity-50 cursor-not-allowed' : ''
											]"
										>
											<svg
												v-if="isPersisting"
												class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
												xmlns="http://www.w3.org/2000/svg"
												fill="none"
												viewBox="0 0 24 24"
											>
												<circle
													class="opacity-25"
													cx="12"
													cy="12"
													r="10"
													stroke="currentColor"
													stroke-width="4"
												></circle>
												<path
													class="opacity-75"
													fill="currentColor"
													d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
												></path>
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
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import type { Reservation } from '~/types/Reservation'
import { useReservationStore } from '~/stores/ReservationStore'
import { useMissionStore } from '~/stores/MissionStore'
import { useVehicleStore } from '~/stores/VehicleStore'
import { useAuthStore } from '~/stores/AuthStore'
import { ref, computed, watch, reactive } from 'vue'
import { reservationStatuses } from '~/types/ReservationEnum'

const props = defineProps<{
	isOpen: boolean
	reservation: Reservation | null
	mode: 'create' | 'edit'
}>()

const emit = defineEmits<{
	close: []
	saved: []
}>()

const reservationStore = useReservationStore()
const missionStore = useMissionStore()
const vehicleStore = useVehicleStore()
const userStore = useAuthStore()

const isPersisting = computed(() => reservationStore.isPersisting)
const validationErrors = computed(() => reservationStore.errors)

// Données du formulaire
const form = reactive({
	id: '',
	missionId: '',
	vehicleId: '',
	driverId: '',
	userId: '',
	status: 'En attente',
	from: '',
	to: '',
	returnDate: ''
})

// Données pour les sélecteurs
const missions = computed(() => missionStore.missions || [])
const vehicles = computed(() => vehicleStore.vehicles || [])
const users = computed(() => userStore.users || [])

// Filtrer les conducteurs (utilisateurs avec rôle conducteur)
const drivers = computed(() => {
	return users.value.filter(user => user.role === 'driver' || user.role === 'DRIVER' || user.role?.toLowerCase().includes('driver'))
})

// Filtrer les véhicules disponibles (statut "Disponible")
const availableVehicles = computed(() => {
	return vehicles.value.filter(vehicle => vehicle.status === 'Disponible')
})

// Données sélectionnées pour l'affichage
const selectedMission = computed(() => {
	return missions.value.find(m => m.id === form.missionId)
})

const selectedVehicle = computed(() => {
	return vehicles.value.find(v => v.id === form.vehicleId)
})

// Méthodes
const save = async () => {
	try {
		if (props.mode === 'create') {
			await reservationStore.store(form as any)
		} else {
			await reservationStore.update(form as any)
		}
		emit('saved')
	} catch (error) {
		console.error('Erreur lors de la sauvegarde:', error)
	}
}

const close = () => {
	// Réinitialiser le formulaire
	Object.keys(form).forEach(key => {
		if (key !== 'status') {
			(form as any)[key] = ''
		} else {
			(form as any)[key] = 'En attente'
		}
	})
	emit('close')
}

// Watcher pour pré-remplir le formulaire quand une réservation est fournie
watch(() => props.reservation, (newReservation) => {
	if (newReservation) {
		Object.assign(form, {
			id: newReservation.id || '',
			missionId: newReservation.missionId || '',
			vehicleId: newReservation.vehicleId || '',
			driverId: newReservation.driverId || '',
			userId: newReservation.userId || '',
			status: newReservation.status || 'En attente',
			from: formatDateTimeForInput(newReservation.from),
			to: formatDateTimeForInput(newReservation.to),
			returnDate: newReservation.returnDate ? formatDateTimeForInput(newReservation.returnDate) : ''
		})
	} else {
		Object.keys(form).forEach(key => {
			if (key !== 'status') {
				(form as any)[key] = ''
			} else {
				(form as any)[key] = 'En attente'
			}
		})
	}
}, { immediate: true })

// Formatage des dates pour l'input datetime-local
const formatDateTimeForInput = (dateString: string) => {
	if (!dateString) return ''
	const date = new Date(dateString)
	const pad = (n: number) => n.toString().padStart(2, '0')
	return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

// Charger les données nécessaires quand le modal s'ouvre
watch(() => props.isOpen, async (isOpen) => {
	if (isOpen) {
		try {
			// Charger les missions, véhicules et utilisateurs si nécessaire
			if (missions.value.length === 0) await missionStore.findAll()
			if (vehicles.value.length === 0) await vehicleStore.findAll()
			// if (users.value.length === 0) await userStore.findAll()
		} catch (error) {
			console.error('Erreur lors du chargement des données:', error)
		}
	}
})
</script>