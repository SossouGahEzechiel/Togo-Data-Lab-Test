<template>
	<div class="space-y-6">
		<!-- En-tête du dashboard -->
		<div class="mb-8">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
						Tableau de bord
					</h1>
					<p class="text-gray-600 dark:text-gray-400 mt-1">
						Bienvenue, {{ authStore.fullName }}. Voici un aperçu de votre activité.
					</p>
				</div>
				<div class="flex items-center space-x-4">
					<div class="text-sm text-gray-500 dark:text-gray-400">
						{{ currentDate }}
					</div>
				</div>
			</div>
			<div class="mt-4 border-b border-gray-200 dark:border-dark-600"></div>
		</div>

		<!-- Statistiques globales -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
			<!-- Réservations en attente -->
			<NuxtLink :to="AppUrl.RESERVATIONS_LIST" class="group">
				<div class="card hover:shadow-lg transition-shadow duration-200">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
								<ClockIcon class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
							</div>
						</div>
						<div class="ml-4">
							<p
								class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
								Réservations en attente
							</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">
								{{ pendingReservationsCount }}
							</p>
						</div>
						<div class="ml-auto">
							<ChevronRightIcon
								class="h-5 w-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
						</div>
					</div>
					<div class="mt-4">
						<p class="text-xs text-gray-500 dark:text-gray-400">
							{{ pendingReservationsCount === 0 ? 'Aucune réservation en attente' :
								`${pendingReservationsCount} réservation${pendingReservationsCount > 1 ? 's' : ''}
							nécessite${pendingReservationsCount > 1 ? 'nt' : ''} votre validation` }}
						</p>
					</div>
				</div>
			</NuxtLink>

			<!-- Véhicules disponibles -->
			<NuxtLink :to="AppUrl.VEHICLES_LISTE" class="group">
				<div class="card hover:shadow-lg transition-shadow duration-200">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
								<TruckIcon class="h-6 w-6 text-green-600 dark:text-green-400" />
							</div>
						</div>
						<div class="ml-4">
							<p
								class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
								Véhicules disponibles
							</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">
								{{ availableVehiclesCount }}
							</p>
						</div>
						<div class="ml-auto">
							<ChevronRightIcon
								class="h-5 w-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
						</div>
					</div>
					<div class="mt-4">
						<p class="text-xs text-gray-500 dark:text-gray-400">
							Sur {{ totalVehiclesCount }} véhicule(s) au total
						</p>
					</div>
				</div>
			</NuxtLink>

			<!-- Missions en cours -->
			<NuxtLink :to="AppUrl.MISSIONS_LIST" class="group">
				<div class="card hover:shadow-lg transition-shadow duration-200">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
								<BriefcaseIcon class="h-6 w-6 text-blue-600 dark:text-blue-400" />
							</div>
						</div>
						<div class="ml-4">
							<p
								class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
								Missions en cours
							</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">
								{{ activeMissionsCount }}
							</p>
						</div>
						<div class="ml-auto">
							<ChevronRightIcon
								class="h-5 w-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
						</div>
					</div>
					<div class="mt-4">
						<p class="text-xs text-gray-500 dark:text-gray-400">
							{{ completedMissionsCount }} mission(s) terminée(s)
						</p>
					</div>
				</div>
			</NuxtLink>

			<!-- Utilisateurs actifs -->
			<NuxtLink to="/users" class="group">
				<div class="card hover:shadow-lg transition-shadow duration-200">
					<div class="flex items-center">
						<div class="flex-shrink-0">
							<div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
								<UserGroupIcon class="h-6 w-6 text-purple-600 dark:text-purple-400" />
							</div>
						</div>
						<div class="ml-4">
							<p
								class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
								Utilisateurs actifs
							</p>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">
								{{ activeUsersCount }}
							</p>
						</div>
						<div class="ml-auto">
							<ChevronRightIcon
								class="h-5 w-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
						</div>
					</div>
					<div class="mt-4">
						<p class="text-xs text-gray-500 dark:text-gray-400">
							{{ driversCount }} conducteur(s)
						</p>
					</div>
				</div>
			</NuxtLink>
		</div>

		<!-- Section principale à 2 colonnes -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Colonne gauche : Réservations en attente + Actions rapides -->
			<div class="lg:col-span-2 space-y-6">
				<!-- Réservations en attente (détaillé) -->
				<div class="card">
					<div class="flex items-center justify-between mb-6">
						<div>
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">
								Réservations en attente de validation
							</h2>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
								{{ pendingReservationsCount }} réservation(s) nécessite(nt) votre attention
							</p>
						</div>
						<NuxtLink :to="AppUrl.RESERVATIONS_LIST"
							class="text-sm font-medium text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
							Tout voir →
						</NuxtLink>
					</div>

					<div v-if="isLoading" class="flex justify-center items-center py-8">
						<Spinner :is-loading="true" size="md" />
					</div>

					<div v-else-if="pendingReservations.length === 0" class="text-center py-8">
						<CheckCircleIcon class="mx-auto h-12 w-12 text-green-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune réservation en attente</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							Toutes les réservations ont été traitées
						</p>
					</div>

					<div v-else class="space-y-4">
						<div v-for="reservation in pendingReservations.slice(0, 5)" :key="reservation.id"
							class="border border-gray-200 dark:border-dark-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
							<div class="flex items-start justify-between">
								<div class="flex-1">
									<div class="flex items-center space-x-3">
										<div class="flex-shrink-0">
											<div
												class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
												<BriefcaseIcon class="h-5 w-5 text-primary-600 dark:text-primary-400" />
											</div>
										</div>
										<div class="min-w-0 flex-1">
											<p class="text-sm font-medium text-gray-900 dark:text-white truncate">
												{{ reservation.mission?.label || 'Mission non spécifiée' }}
											</p>
											<div class="mt-1 flex items-center space-x-2">
												<p class="text-xs text-gray-500 dark:text-gray-400">
													{{ reservation.vehicle?.brand }} {{ reservation.vehicle?.model }}
												</p>
												<span class="text-xs text-gray-400 dark:text-gray-600">•</span>
												<p class="text-xs text-gray-500 dark:text-gray-400">
													{{ formatDate(reservation.from) }} → {{ formatDate(reservation.to) }}
												</p>
											</div>
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
												Demandé par: {{ reservation.user?.fullName || 'N/A' }}
											</p>
										</div>
									</div>
								</div>
								<div class="ml-4 flex items-center space-x-2">
									<button @click.stop="validateReservation(reservation)"
										class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
										<CheckIcon class="h-3 w-3 mr-1" />
										Valider
									</button>
									<button @click.stop="rejectReservation(reservation)"
										class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
										<XMarkIcon class="h-3 w-3 mr-1" />
										Rejeter
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Prochaines réservations -->
				<div class="card">
					<div class="flex items-center justify-between mb-6">
						<div>
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">
								Prochaines réservations
							</h2>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
								Réservations à venir dans les 7 prochains jours
							</p>
						</div>
						<NuxtLink :to="AppUrl.RESERVATIONS_LIST"
							class="text-sm font-medium text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
							Tout voir →
						</NuxtLink>
					</div>

					<div v-if="upcomingReservations.length === 0" class="text-center py-8">
						<CalendarIcon class="mx-auto h-12 w-12 text-gray-400" />
						<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucune réservation à venir</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							Aucune réservation programmée cette semaine
						</p>
					</div>

					<div v-else class="space-y-3">
						<div v-for="reservation in upcomingReservations.slice(0, 5)" :key="reservation.id"
							class="flex items-center justify-between p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
							<div class="flex items-center space-x-3">
								<div class="flex-shrink-0">
									<div :class="[
										'h-10 w-10 rounded-full flex items-center justify-center',
										reservation.status === 'Validée' ? 'bg-green-100 dark:bg-green-900' : 'bg-yellow-100 dark:bg-yellow-900'
									]">
										<CalendarDaysIcon class="h-5 w-5 text-gray-600 dark:text-gray-400" />
									</div>
								</div>
								<div>
									<p class="text-sm font-medium text-gray-900 dark:text-white">
										{{ reservation.mission?.label || 'Mission' }}
									</p>
									<div class="flex items-center space-x-2 mt-1">
										<p class="text-xs text-gray-500 dark:text-gray-400">
											{{ reservation.vehicle?.brand }} {{ reservation.vehicle?.model }}
										</p>
										<span class="text-xs text-gray-400 dark:text-gray-600">•</span>
										<p class="text-xs text-gray-500 dark:text-gray-400">
											{{ formatDate(reservation.from) }}
										</p>
									</div>
								</div>
							</div>
							<div class="flex items-center space-x-2">
								<ReservationStatusBadge :status="reservation.status" />
								<ChevronRightIcon class="h-4 w-4 text-gray-400" />
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Colonne droite : Actions rapides + Statistiques -->
			<div class="space-y-6">
				<!-- Actions rapides -->
				<div class="card">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						Actions rapides
					</h2>
					<div class="space-y-3">
						<NuxtLink :to="AppUrl.RESERVATION_CREATE" class="group">
							<div
								class="flex items-center p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-300 dark:hover:border-primary-700 transition-all">
								<div class="flex-shrink-0">
									<div
										class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
										<PlusCircleIcon
											class="h-5 w-5 text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform" />
									</div>
								</div>
								<div class="ml-3">
									<p
										class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-primary-700 dark:group-hover:text-primary-300">
										Nouvelle réservation
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										Créer une nouvelle réservation
									</p>
								</div>
							</div>
						</NuxtLink>

						<NuxtLink :to="AppUrl.VEHICLES_LISTE" class="group">
							<div
								class="flex items-center p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-300 dark:hover:border-green-700 transition-all">
								<div class="flex-shrink-0">
									<div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
										<TruckIcon
											class="h-5 w-5 text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform" />
									</div>
								</div>
								<div class="ml-3">
									<p
										class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-green-700 dark:group-hover:text-green-300">
										Ajouter un véhicule
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										Ajouter un véhicule au parc
									</p>
								</div>
							</div>
						</NuxtLink>

						<NuxtLink :to="AppUrl.MISSIONS_LIST" class="group">
							<div
								class="flex items-center p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition-all">
								<div class="flex-shrink-0">
									<div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
										<BriefcaseIcon
											class="h-5 w-5 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform" />
									</div>
								</div>
								<div class="ml-3">
									<p
										class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300">
										Créer une mission
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										Définir une nouvelle mission
									</p>
								</div>
							</div>
						</NuxtLink>

						<NuxtLink :to="AppUrl.HISTORICAL" class="group">
							<div
								class="flex items-center p-3 border border-gray-200 dark:border-dark-700 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-300 dark:hover:border-purple-700 transition-all">
								<div class="flex-shrink-0">
									<div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
										<ArchiveBoxIcon
											class="h-5 w-5 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform" />
									</div>
								</div>
								<div class="ml-3">
									<p
										class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-700 dark:group-hover:text-purple-300">
										Historique des réservations
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										Consulter l'historique complet
									</p>
								</div>
							</div>
						</NuxtLink>
					</div>
				</div>

				<!-- Statistiques mensuelles -->
				<div class="card">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
						Activité du mois
					</h2>
					<div class="space-y-4">
						<div class="flex items-center justify-between">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<CalendarIcon class="h-5 w-5 text-gray-400" />
								</div>
								<div class="ml-2">
									<p class="text-sm text-gray-600 dark:text-gray-400">Réservations ce mois</p>
								</div>
							</div>
							<p class="text-sm font-medium text-gray-900 dark:text-white">{{ monthlyReservationsCount }}</p>
						</div>
						<div class="flex items-center justify-between">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<CheckCircleIcon class="h-5 w-5 text-green-400" />
								</div>
								<div class="ml-2">
									<p class="text-sm text-gray-600 dark:text-gray-400">Validées</p>
								</div>
							</div>
							<p class="text-sm font-medium text-gray-900 dark:text-white">{{ monthlyValidatedCount }}</p>
						</div>
						<div class="flex items-center justify-between">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<ClockIcon class="h-5 w-5 text-yellow-400" />
								</div>
								<div class="ml-2">
									<p class="text-sm text-gray-600 dark:text-gray-400">En attente</p>
								</div>
							</div>
							<p class="text-sm font-medium text-gray-900 dark:text-white">{{ monthlyPendingCount }}</p>
						</div>
						<div class="flex items-center justify-between">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<XCircleIcon class="h-5 w-5 text-red-400" />
								</div>
								<div class="ml-2">
									<p class="text-sm text-gray-600 dark:text-gray-400">Rejetées</p>
								</div>
							</div>
							<p class="text-sm font-medium text-gray-900 dark:text-white">{{ monthlyRejectedCount }}</p>
						</div>
					</div>
				</div>

				<!-- Véhicules les plus utilisés -->
				<div class="card">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-lg font-semibold text-gray-900 dark:text-white">
							Véhicules les plus utilisés
						</h2>
						<NuxtLink to="/vehicles"
							class="text-sm font-medium text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
							Tout voir →
						</NuxtLink>
					</div>
					<div v-if="popularVehicles.length === 0" class="text-center py-4">
						<p class="text-sm text-gray-500 dark:text-gray-400">Aucune donnée disponible</p>
					</div>
					<div v-else class="space-y-3">
						<div v-for="vehicle in popularVehicles.slice(0, 3)" :key="vehicle.id"
							class="flex items-center justify-between p-2 hover:bg-gray-50 dark:hover:bg-dark-700 rounded transition-colors">
							<div class="flex items-center space-x-3">
								<div class="flex-shrink-0">
									<div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
										<TruckIcon class="h-4 w-4 text-primary-600 dark:text-primary-400" />
									</div>
								</div>
								<div>
									<p class="text-sm font-medium text-gray-900 dark:text-white">
										{{ vehicle.brand }} {{ vehicle.model }}
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">
										{{ vehicle.reservationCount }} réservation(s)
									</p>
								</div>
							</div>
							<ReservationStatusBadge :status="vehicle.status" size="sm" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modals de validation/rejet -->
		<ReservationValidateModal v-if="reservationToValidate" :is-open="isValidateModalOpen"
			:reservation="reservationToValidate" @close="closeValidateModal" @confirmed="handleValidateConfirmed" />

		<ReservationRejectModal v-if="reservationToReject" :is-open="isRejectModalOpen" :reservation="reservationToReject"
			@close="closeRejectModal" @confirmed="handleRejectConfirmed" />
	</div>
</template>

<script setup lang="ts">
import {
	ClockIcon,
	TruckIcon,
	BriefcaseIcon,
	UserGroupIcon,
	ChevronRightIcon,
	CheckCircleIcon,
	XMarkIcon,
	CalendarIcon,
	CalendarDaysIcon,
	PlusCircleIcon,
	ArchiveBoxIcon,
	CheckIcon,
	XCircleIcon,
	ChartBarIcon
} from '@heroicons/vue/24/outline'
import { useReservationStore } from '~/stores/ReservationStore'
import { useVehicleStore } from '~/stores/VehicleStore'
import { useMissionStore } from '~/stores/MissionStore'
import { useUserStore } from '~/stores/UserStore'
import { useAuthStore } from '~/stores/AuthStore'
import ReservationStatusBadge from '~/components/reservations/ReservationStatusBadge.vue'
import Spinner from '~/components/partials/Spinner.vue'
import { ref, computed, onMounted } from 'vue'
import type { Reservation } from '~/types/Reservation'
import { ReservationStatusEnum } from '~/types/ReservationEnum'
import { UserRoleEnum } from '~/types/UserRoleEnum'
import ReservationValidateModal from '~/components/reservations/ReservationValidateModal.vue'
import ReservationRejectModal from '~/components/reservations/ReservationRejectModal.vue'

const { setPageTitle } = usePageTitle()

const reservationStore = useReservationStore()
const vehicleStore = useVehicleStore()
const missionStore = useMissionStore()
const userStore = useUserStore()
const authStore = useAuthStore()

const isLoading = ref(false)
const isValidateModalOpen = ref(false)
const isRejectModalOpen = ref(false)
const reservationToValidate = ref<Reservation | null>(null)
const reservationToReject = ref<Reservation | null>(null)

const currentDate = computed(() => {
	return new Date().toLocaleDateString('fr-FR', {
		weekday: 'long',
		year: 'numeric',
		month: 'long',
		day: 'numeric'
	})
})

// Computed properties pour les statistiques
const reservations = computed(() => reservationStore.reservations || [])
const vehicles = computed(() => vehicleStore.vehicles || [])
const missions = computed(() => missionStore.missions || [])
const users = computed(() => userStore.users || [])

// Réservations en attente
const pendingReservations = computed(() => {
	return reservations.value.filter(r => r.status === 'En attente')
})

const pendingReservationsCount = computed(() => pendingReservations.value.length)

// Réservations à venir (7 prochains jours)
const upcomingReservations = computed(() => {
	const today = new Date()
	const nextWeek = new Date()
	nextWeek.setDate(today.getDate() + 7)

	return reservations.value.filter(r => {
		const fromDate = new Date(r.from)
		return fromDate >= today && fromDate <= nextWeek && r.status === 'Validée'
	}).sort((a, b) => new Date(a.from).getTime() - new Date(b.from).getTime())
})

// Véhicules disponibles
const availableVehiclesCount = computed(() => {
	return vehicles.value.filter(v => v.status === 'Disponible').length
})

const totalVehiclesCount = computed(() => vehicles.value.length)

// Missions actives
const activeMissionsCount = computed(() => {
	return missions.value.filter(m => !m.to).length
})

const completedMissionsCount = computed(() => {
	return missions.value.filter(m => m.to).length
})

// Utilisateurs actifs
const activeUsersCount = computed(() => users.value.length)

const driversCount = computed(() => {
	return users.value.filter(u =>
		u.role === UserRoleEnum.DRIVER
	).length
})

// Statistiques mensuelles
const monthlyReservationsCount = computed(() => {
	const currentMonth = new Date().getMonth()
	const currentYear = new Date().getFullYear()

	return reservations.value.filter(r => {
		const date = new Date(r.from)
		return date.getMonth() === currentMonth && date.getFullYear() === currentYear
	}).length
})

const monthlyValidatedCount = computed(() => {
	const currentMonth = new Date().getMonth()
	const currentYear = new Date().getFullYear()

	return reservations.value.filter(r => {
		const date = new Date(r.from)
		return date.getMonth() === currentMonth &&
			date.getFullYear() === currentYear &&
			r.status === 'Validée'
	}).length
})

const monthlyPendingCount = computed(() => {
	const currentMonth = new Date().getMonth()
	const currentYear = new Date().getFullYear()

	return reservations.value.filter(r => {
		const date = new Date(r.from)
		return date.getMonth() === currentMonth &&
			date.getFullYear() === currentYear &&
			r.status === 'En attente'
	}).length
})

const monthlyRejectedCount = computed(() => {
	const currentMonth = new Date().getMonth()
	const currentYear = new Date().getFullYear()

	return reservations.value.filter(r => {
		const date = new Date(r.from)
		return date.getMonth() === currentMonth &&
			date.getFullYear() === currentYear &&
			r.status === 'Rejetée'
	}).length
})

const validateReservation = (reservation: Reservation) => {
	reservationToValidate.value = reservation
	isValidateModalOpen.value = true
}

const rejectReservation = (reservation: Reservation) => {
	reservationToReject.value = reservation
	isRejectModalOpen.value = true
}

const closeValidateModal = () => {
	isValidateModalOpen.value = false
	reservationToValidate.value = null
}

const closeRejectModal = () => {
	isRejectModalOpen.value = false
	reservationToReject.value = null
}

// Véhicules les plus utilisés
const popularVehicles = computed(() => {
	const vehicleUsage = new Map()

	reservations.value.forEach(reservation => {
		if (reservation.vehicle) {
			const vehicleId = reservation.vehicle.id
			const currentCount = vehicleUsage.get(vehicleId) || 0
			vehicleUsage.set(vehicleId, currentCount + 1)
		}
	})

	return Array.from(vehicleUsage.entries())
		.map(([vehicleId, reservationCount]) => {
			const vehicle = vehicles.value.find(v => v.id === vehicleId)
			return {
				...vehicle,
				reservationCount
			}
		})
		.filter(v => v)
		.sort((a, b) => b.reservationCount - a.reservationCount)
})

// Méthodes utilitaires
const formatDate = (dateString: string) => {
	return new Date(dateString).toLocaleDateString('fr-FR', {
		day: '2-digit',
		month: '2-digit',
		year: 'numeric'
	})
}

const handleValidateConfirmed = async () => {
	if (reservationToValidate.value) {
		try {
			await reservationStore.applyDecision(reservationToValidate.value.id, ReservationStatusEnum.VALIDATED);
		} catch (error) {
			console.error('Erreur lors de la validation:', error)
		}
	}
	closeValidateModal()
}

const handleRejectConfirmed = async () => {
	if (reservationToReject.value) {
		try {
			await reservationStore.applyDecision(reservationToReject.value.id, ReservationStatusEnum.REJECTED);
		} catch (error) {
			console.error('Erreur lors du rejet:', error)
		}
	}
	closeRejectModal()
}

// Charger les données
const loadData = async () => {
	isLoading.value = true
	try {
		await Promise.all([
			reservationStore.findAll(),
			vehicleStore.findAll(),
			missionStore.findAll(),
			userStore.findAll()
		])
	} catch (error) {
		console.error('Erreur lors du chargement des données:', error)
	} finally {
		isLoading.value = false
	}
}

// Lifecycle
onMounted(() => {
	setPageTitle('Tableau de bord', 'Vue d\'ensemble de votre activité')
	loadData()
})
</script>