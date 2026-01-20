<template>
	<div class="space-y-6">
		<!-- En-tête de page -->
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
						{{ mode === 'create' ? 'Nouvelle réservation' : 'Modifier la réservation' }}
					</h1>
					<p class="text-gray-600 dark:text-gray-400 mt-1">
						{{ mode === 'create' ?
						 'Création d\'une nouvelle réservation de véhicule' :
						  'Modification de la réservation existante' }}
					</p>
				</div>
				<div class="flex items-center space-x-3">
					<button @click="cancel" type="button"
						class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
						Annuler
					</button>
					<button @click="save" type="button" :disabled="isPersisting" :class="[
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
						{{ isPersisting ? 'Enregistrement...' : (mode === 'create' ? 'Créer la réservation' : 'Mettre à jour') }}
					</button>
				</div>
			</div>
			<div class="mt-4 border-b border-gray-200 dark:border-dark-600"></div>
		</div>

		<!-- Panneau principal -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Formulaire principal -->
			<div class="lg:col-span-2">
				<div class="card space-y-6">
					<!-- Informations de base -->
					<div>
						<h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
							Informations de la réservation
						</h2>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
							<!-- Mission -->
							<div>
								<label for="missionId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Mission *
								</label>
								<select id="missionId" v-model="form.missionId" required class="input-field"
									:class="{ 'border-red-500': validationErrors.missionId }">
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
								<select id="vehicleId" v-model="form.vehicleId" required class="input-field"
									:class="{ 'border-red-500': validationErrors.vehicleId }">
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
								<select id="driverId" v-model="form.driverId" required class="input-field"
									:class="{ 'border-red-500': validationErrors.driverId }">
									<option value="">Sélectionnez un conducteur</option>
									<option v-for="driver in drivers" :key="driver.id" :value="driver.id">
										{{ driver.fullName }} - {{ driver.email }}
									</option>
								</select>
								<p v-if="validationErrors.driverId" class="mt-1 text-sm text-red-600 dark:text-red-400">
									{{ validationErrors.driverId }}
								</p>
							</div>

							<!-- Date de début -->
							<div>
								<label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Date de début *
								</label>
								<input id="from" v-model="form.from" type="datetime-local" required class="input-field"
									:class="{ 'border-red-500': validationErrors.from }" />
								<p v-if="validationErrors.from" class="mt-1 text-sm text-red-600 dark:text-red-400">
									{{ validationErrors.from }}
								</p>
							</div>

							<!-- Date de fin -->
							<div>
								<label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Date de fin *
								</label>
								<input id="to" v-model="form.to" type="datetime-local" required class="input-field"
									:class="{ 'border-red-500': validationErrors.to }" />
								<p v-if="validationErrors.to" class="mt-1 text-sm text-red-600 dark:text-red-400">
									{{ validationErrors.to }}
								</p>
							</div>

							<!-- Date de retour (optionnel) -->
							<div>
								<label for="returnDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
									Date de retour (optionnel)
								</label>
								<input id="returnDate" v-model="form.returnDate" type="datetime-local" class="input-field"
									:class="{ 'border-red-500': validationErrors.returnDate }" />
								<p v-if="validationErrors.returnDate" class="mt-1 text-sm text-red-600 dark:text-red-400">
									{{ validationErrors.returnDate }}
								</p>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
									À remplir uniquement si le véhicule est déjà retourné
								</p>
							</div>
						</div>
					</div>

					<!-- Informations complémentaires -->
					<div v-if="selectedMission || selectedVehicle || selectedDriver"
						class="border-t border-gray-200 dark:border-dark-700 pt-6">
						<h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
							Détails de la sélection
						</h2>
						<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
							<!-- Détails de la mission -->
							<div v-if="selectedMission" class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2 flex items-center">
									<BriefcaseIcon class="w-4 h-4 mr-2 text-primary-500" />
									Mission sélectionnée
								</h3>
								<p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedMission.label }}</p>
								<p v-if="selectedMission.description" class="text-xs text-gray-600 dark:text-gray-400 mt-1">
									{{ selectedMission.description }}
								</p>
							</div>

							<!-- Détails du véhicule -->
							<div v-if="selectedVehicle" class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2 flex items-center">
									<TruckIcon class="w-4 h-4 mr-2 text-primary-500" />
									Véhicule sélectionné
								</h3>
								<p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedVehicle.brand }} {{
									selectedVehicle.model }}</p>
								<div class="mt-1 space-y-1">
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Immatriculation: {{ selectedVehicle.registrationNumber }}
									</p>
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Type: {{ selectedVehicle.type }}
									</p>
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Places: {{ selectedVehicle.seatsCount }}
									</p>
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Statut: <span :class="getStatusBadgeClass(selectedVehicle.status)">{{ selectedVehicle.status
											}}</span>
									</p>
								</div>
							</div>

							<!-- Détails du conducteur -->
							<div v-if="selectedDriver" class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
								<h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2 flex items-center">
									<UserIcon class="w-4 h-4 mr-2 text-primary-500" />
									Conducteur sélectionné
								</h3>
								<p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedDriver.fullName }}</p>
								<div class="mt-1 space-y-1">
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Email: {{ selectedDriver.email }}
									</p>
									<p class="text-xs text-gray-600 dark:text-gray-400">
										Téléphone: {{ selectedDriver.phone || 'Non renseigné' }}
									</p>
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
				</div>
			</div>

			<!-- Panneau latéral -->
			<div class="space-y-6">
				<!-- Récapitulatif -->
				<div class="card">
					<h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
						Récapitulatif
					</h2>
					<div class="space-y-3">
						<div v-if="selectedMission" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Mission:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedMission.label }}</span>
						</div>
						<div v-if="selectedVehicle" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Véhicule:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedVehicle.brand }} {{
								selectedVehicle.model }}</span>
						</div>
						<div v-if="selectedDriver" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Conducteur:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedDriver.fullName }}</span>
						</div>
						<div v-if="form.from" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Début:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDateTimeDisplay(form.from)
								}}</span>
						</div>
						<div v-if="form.to" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Fin:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDateTimeDisplay(form.to)
								}}</span>
						</div>
						<div v-if="form.returnDate" class="flex justify-between">
							<span class="text-sm text-gray-500 dark:text-gray-400">Retour:</span>
							<span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDateTimeDisplay(form.returnDate)
								}}</span>
						</div>
						<div class="pt-3 border-t border-gray-200 dark:border-dark-700">
							<div class="flex justify-between">
								<span class="text-sm font-medium text-gray-900 dark:text-white">Durée:</span>
								<span class="text-sm font-medium text-primary-600 dark:text-primary-400">
									{{ calculateDuration() }}
								</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Vérification de disponibilité -->
				<div class="card">
					<h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
						Disponibilité
					</h2>
					<div v-if="availabilityStatus === 'loading'" class="text-center py-4">
						<svg class="animate-spin mx-auto h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none"
							viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
							<path class="opacity-75" fill="currentColor"
								d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
							</path>
						</svg>
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Vérification de la disponibilité...</p>
					</div>
					<div v-else-if="availabilityStatus === 'available'" class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
						<div class="flex">
							<CheckCircleIcon class="h-5 w-5 text-green-400" />
							<div class="ml-3">
								<h3 class="text-sm font-medium text-green-800 dark:text-green-400">
									Véhicule disponible
								</h3>
								<div class="mt-2 text-sm text-green-700 dark:text-green-300">
									<p>Le véhicule est disponible pour cette période.</p>
								</div>
							</div>
						</div>
					</div>
					<div v-else-if="availabilityStatus === 'conflict'" class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
						<div class="flex">
							<ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
							<div class="ml-3">
								<h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
									Conflit de disponibilité
								</h3>
								<div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
									<p>Le véhicule n'est pas disponible pour cette période.</p>
									<p class="mt-1 text-xs">Veuillez choisir une autre période ou un autre véhicule.</p>
								</div>
							</div>
						</div>
					</div>
					<div v-else class="text-center py-4">
						<CalendarIcon class="mx-auto h-8 w-8 text-gray-400" />
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
							Remplissez les informations pour vérifier la disponibilité
						</p>
					</div>
				</div>

				<!-- Actions rapides -->
				<div class="card">
					<h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
						Actions rapides
					</h2>
					<div class="space-y-2">
						<button @click="duplicateReservation" type="button"
							class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							<DocumentDuplicateIcon class="w-4 h-4 mr-2" />
							Dupliquer
						</button>
						<button @click="resetForm" type="button"
							class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							<ArrowPathIcon class="w-4 h-4 mr-2" />
							Réinitialiser
						</button>
						<button @click="viewCalendar" type="button"
							class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-dark-600 dark:bg-dark-700 dark:text-gray-300 dark:hover:bg-dark-600">
							<CalendarDaysIcon class="w-4 h-4 mr-2" />
							Voir calendrier
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import {
	BriefcaseIcon,
	TruckIcon,
	UserIcon,
	ExclamationCircleIcon,
	CheckCircleIcon,
	ExclamationTriangleIcon,
	CalendarIcon,
	DocumentDuplicateIcon,
	ArrowPathIcon,
	CalendarDaysIcon
} from '@heroicons/vue/24/outline'
import type { Reservation } from '~/types/Reservation'
import { useReservationStore } from '~/stores/ReservationStore'
import { useMissionStore } from '~/stores/MissionStore'
import { useVehicleStore } from '~/stores/VehicleStore'
import { useUserStore } from '~/stores/UserStore'
import { ref, computed, reactive, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const reservationStore = useReservationStore()
const missionStore = useMissionStore()
const vehicleStore = useVehicleStore()
const userStore = useUserStore()

// Déterminer le mode (create ou edit)
const mode = computed(() => route.params.mode === 'edit' ? 'edit' : 'create')
const reservationId = computed(() => route.params.id as string)

const isPersisting = computed(() => reservationStore.isPersisting)
const validationErrors = computed(() => reservationStore.errors)
const availabilityStatus = ref<'idle' | 'loading' | 'available' | 'conflict'>('idle')

// Données du formulaire
const form = reactive({
	id: '',
	missionId: '',
	vehicleId: '',
	driverId: '',
	from: '',
	to: '',
	returnDate: ''
})

// Données pour les sélecteurs
const missions = computed(() => missionStore.missions || [])
const vehicles = computed(() => vehicleStore.vehicles || [])
const users = computed(() => userStore.users || [])

// Filtrer les conducteurs
const drivers = computed(() => {
	return users.value.filter(user =>
		user.role?.toLowerCase().includes('driver') ||
		user.role?.toLowerCase().includes('conducteur')
	)
})

// Filtrer les véhicules disponibles
const availableVehicles = computed(() => {
	return vehicles.value.filter(vehicle =>
		vehicle.status === 'Disponible' || vehicle.status === 'AVAILABLE'
	)
})

// Données sélectionnées pour l'affichage
const selectedMission = computed(() => {
	return missions.value.find(m => m.id === form.missionId)
})

const selectedVehicle = computed(() => {
	return vehicles.value.find(v => v.id === form.vehicleId)
})

const selectedDriver = computed(() => {
	return users.value.find(u => u.id === form.driverId)
})

// Méthodes utilitaires
const getStatusBadgeClass = (status: string) => {
	const statusMap: Record<string, string> = {
		'Disponible': 'text-green-600 dark:text-green-400',
		'AVAILABLE': 'text-green-600 dark:text-green-400',
		'Indisponible': 'text-red-600 dark:text-red-400',
		'UNAVAILABLE': 'text-red-600 dark:text-red-400',
		'En réparation': 'text-yellow-600 dark:text-yellow-400',
		'UNDER_REPAIR': 'text-yellow-600 dark:text-yellow-400',
		'Réservé': 'text-blue-600 dark:text-blue-400',
		'RESERVED': 'text-blue-600 dark:text-blue-400',
		'Suspendu': 'text-gray-600 dark:text-gray-400',
		'SUSPENDED': 'text-gray-600 dark:text-gray-400'
	}
	return statusMap[status] || 'text-gray-600 dark:text-gray-400'
}

const formatDateTimeForInput = (dateString: string) => {
	if (!dateString) return ''
	const date = new Date(dateString)
	const pad = (n: number) => n.toString().padStart(2, '0')
	return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

const formatDateTimeDisplay = (dateTimeString: string) => {
	if (!dateTimeString) return ''
	const date = new Date(dateTimeString)
	return date.toLocaleString('fr-FR', {
		day: '2-digit',
		month: '2-digit',
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	})
}

const calculateDuration = () => {
	if (!form.from || !form.to) return 'N/A'

	const start = new Date(form.from)
	const end = new Date(form.to)
	const diffMs = end.getTime() - start.getTime()

	const days = Math.floor(diffMs / (1000 * 60 * 60 * 24))
	const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
	const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))

	let duration = ''
	if (days > 0) duration += `${days}j `
	if (hours > 0) duration += `${hours}h `
	if (minutes > 0) duration += `${minutes}min`

	return duration || '0min'
}

// Vérifier la disponibilité
const checkAvailability = async () => {
	if (!form.vehicleId || !form.from || !form.to) {
		availabilityStatus.value = 'idle'
		return
	}

	availabilityStatus.value = 'loading'

	try {
		// Simuler une vérification d'API
		await new Promise(resolve => setTimeout(resolve, 1000))

		// Ici, vous implémenteriez l'appel API réel
		// Pour l'instant, simulons une réponse
		const isAvailable = Math.random() > 0.3
		availabilityStatus.value = isAvailable ? 'available' : 'conflict'
	} catch (error) {
		console.error('Erreur lors de la vérification de disponibilité:', error)
		availabilityStatus.value = 'conflict'
	}
}

// Actions principales
const save = async () => {
	try {
		if (mode.value === 'create') {
			await reservationStore.store(form as any)
			await router.push('/reservations')
		} else {
			await reservationStore.update(form as any)
			await router.push('/reservations')
		}
	} catch (error) {
		console.error('Erreur lors de la sauvegarde:', error)
	}
}

const cancel = () => {
	router.push('/reservations')
}

const resetForm = () => {
	Object.keys(form).forEach(key => {
		if (key !== 'id') {
			(form as any)[key] = ''
		}
	})
}

const duplicateReservation = () => {
	if (mode.value === 'edit') {
		form.id = ''
		// Garder les autres données pour la duplication
	}
}

const viewCalendar = () => {
	router.push('/calendar')
}

// Charger les données
const loadData = async () => {
	try {
		await Promise.all([
			missionStore.findAll(),
			vehicleStore.findAll(),
			userStore.findAll()
		])

		// Si mode édition, charger la réservation
		if (mode.value === 'edit' && reservationId.value) {
			const reservation = await reservationStore.findOne(reservationId.value)
			if (reservation) {
				Object.assign(form, {
					id: reservation.id || '',
					missionId: reservation.missionId || '',
					vehicleId: reservation.vehicleId || '',
					driverId: reservation.driverId || '',
					from: formatDateTimeForInput(reservation.from),
					to: formatDateTimeForInput(reservation.to),
					returnDate: reservation.returnDate ? formatDateTimeForInput(reservation.returnDate) : ''
				})
			}
		}
	} catch (error) {
		console.error('Erreur lors du chargement des données:', error)
	}
}

// Watchers
watch([() => form.vehicleId, () => form.from, () => form.to], () => {
	checkAvailability()
}, { deep: true })

// Lifecycle
onMounted(() => {
	loadData()
})
</script>