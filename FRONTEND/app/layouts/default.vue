<template>
	<div class="min-h-screen bg-gray-50 dark:bg-dark-900 transition-colors duration-200">
		<!-- Sidebar pour mobile -->
		<div v-if="isMobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden">
			<div class="fixed inset-0 bg-black bg-opacity-25" @click="toggleMobileSidebar"></div>
			<div class="fixed inset-y-0 left-0 flex w-64 max-w-full flex-col bg-white dark:bg-dark-800 shadow-xl">
				<div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-dark-700">
					<div class="flex items-center space-x-3">
						<div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center">
							<span class="text-white font-bold text-lg">AT</span>
						</div>
						<div>
							<h2 class="text-lg font-bold text-gray-900 dark:text-white">AutoParc TG</h2>
							<p class="text-xs text-gray-500 dark:text-gray-400">Service Public</p>
						</div>
					</div>
					<button @click="toggleMobileSidebar"
						class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
						<XMarkIcon class="w-6 h-6" />
					</button>
				</div>
				<div class="flex-1 overflow-y-auto p-4">
					<Sidebar />
				</div>
			</div>
		</div>

		<!-- Sidebar pour desktop -->
		<div :class="[
			'hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col transition-all duration-300',
			isSidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
		]">
			<div
				class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800 px-6 pb-4">
				<div class="flex h-16 shrink-0 items-center">
					<div class="flex items-center space-x-3">
						<div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center">
							<span class="text-white font-bold text-lg">AT</span>
						</div>
						<div v-if="!isSidebarCollapsed" class="transition-opacity duration-300">
							<h2 class="text-lg font-bold text-gray-900 dark:text-white">AutoParc TG</h2>
							<p class="text-xs text-gray-500 dark:text-gray-400">Service Public</p>
						</div>
					</div>
				</div>
				<Sidebar :collapsed="isSidebarCollapsed" />
				<div v-if="!isSidebarCollapsed" class="mt-auto p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
					<p class="text-xs text-primary-700 dark:text-primary-300">
						Version 1.0.0
					</p>
					<p class="text-xs text-primary-600 dark:text-primary-400 mt-1">
						© {{ currentYear }} Gouvernement Togolais
					</p>
				</div>
			</div>
		</div>

		<!-- Main content -->
		<div :class="[
			'flex flex-col min-h-screen transition-all duration-300',
			isSidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'
		]">
			<!-- Header -->
			<header
				class="sticky top-0 z-30 flex h-16 items-center gap-x-4 border-b border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800 px-4 shadow-sm sm:px-6 lg:px-8">
				<!-- ... -->

				<div class="flex flex-1 justify-between items-center">
					<div>
						<h1 class="text-lg font-semibold text-gray-900 dark:text-white">
							{{ pageTitle }}
						</h1>
						<p v-if="pageDescription" class="text-sm text-gray-500 dark:text-gray-400">
							{{ pageDescription }}
						</p>
					</div>

					<div class="flex items-center gap-x-4">
						<!-- Sélecteur de thème -->
						<div class="relative" @click.stop>
							<button @click="toggleThemeMenu"
								class="flex items-center gap-x-1.5 rounded-lg p-2 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-dark-700">
								<SunIcon v-if="!isDarkMode" class="w-5 h-5 text-yellow-500" />
								<MoonIcon v-else class="w-5 h-5 text-blue-400" />
								<span class="hidden sm:inline">
									{{ isDarkMode ? 'Sombre' : 'Clair' }}
								</span>
								<ChevronDownIcon class="w-4 h-4" />
							</button>

							<!-- Menu déroulant du thème -->
							<Transition enter-active-class="transition ease-out duration-100"
								enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
								leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
								leave-to-class="transform opacity-0 scale-95">
								<div v-if="isThemeMenuOpen"
									class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white dark:bg-dark-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
									<div class="py-1">
										<button @click="setTheme('light')"
											class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
											<SunIcon class="w-4 h-4 mr-3 text-yellow-500" />
											Mode clair
										</button>
										<button @click="setTheme('dark')"
											class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
											<MoonIcon class="w-4 h-4 mr-3 text-blue-400" />
											Mode sombre
										</button>
										<button @click="setTheme('system')"
											class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
											<ComputerDesktopIcon class="w-4 h-4 mr-3 text-gray-500" />
											Système
										</button>
									</div>
								</div>
							</Transition>
						</div>

						<!-- Séparateur -->
						<div class="h-6 w-px bg-gray-200 dark:bg-dark-700" />

						<!-- Menu utilisateur -->
						<div class="relative" @click.stop>
							<button @click="toggleUserMenu"
								class="flex items-center gap-x-3 rounded-lg p-2 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-dark-700">
								<div class="flex items-center space-x-3">
									<div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center">
										<span class="text-white font-bold text-sm">{{ userInitials }}</span>
									</div>
									<div class="hidden sm:block text-left">
										<p class="text-sm font-medium text-gray-900 dark:text-white">{{ fullName }}</p>
										<p class="text-xs text-gray-500 dark:text-gray-400">{{ user?.role }}</p>
									</div>
								</div>
								<ChevronDownIcon class="w-4 h-4" />
							</button>

							<!-- Menu déroulant utilisateur -->
							<Transition enter-active-class="transition ease-out duration-100"
								enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
								leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
								leave-to-class="transform opacity-0 scale-95">
								<div v-if="isUserMenuOpen"
									class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-dark-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
									<div class="py-1">
										<a href="#"
											class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
											<UserCircleIcon class="w-4 h-4 mr-3" />
											Mon profil
										</a>
										<a href="#"
											class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
											<Cog6ToothIcon class="w-4 h-4 mr-3" />
											Paramètres
										</a>
										<div class="border-t border-gray-100 dark:border-dark-700"></div>
										<button @click="logout"
											class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-dark-700">
											<ArrowRightOnRectangleIcon class="w-4 h-4 mr-3" />
											Déconnexion
										</button>
									</div>
								</div>
							</Transition>
						</div>
					</div>
				</div>
			</header>

			<!-- Main content area -->
			<main class="flex-1">
				<div class="py-6 px-4 sm:px-6 lg:px-8">
					<slot />
				</div>
			</main>

			<!-- Footer -->
			<footer class="border-t border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800 py-4 px-4 sm:px-6 lg:px-8">
				<div class="flex flex-col sm:flex-row justify-between items-center">
					<div class="mb-4 sm:mb-0">
						<p class="text-sm text-gray-500 dark:text-gray-400">
							© {{ currentYear }} AutoParc TG - Service Public Togolais
						</p>
					</div>
					<div class="flex items-center space-x-6">
						<a href="#"
							class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
							Confidentialité
						</a>
						<a href="#"
							class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
							Conditions
						</a>
						<a href="#"
							class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
							Support
						</a>
					</div>
				</div>
				<div class="mt-4 text-center">
					<p class="text-xs text-gray-400 dark:text-gray-500">
						Version 1.0.0 • Ministère des Transports
					</p>
				</div>
			</footer>
		</div>
	</div>
</template>

<script setup lang="ts">
import {
	Bars3Icon,
	XMarkIcon,
	ChevronLeftIcon,
	ChevronRightIcon,
	ChevronDownIcon,
	SunIcon,
	MoonIcon,
	ComputerDesktopIcon,
	UserCircleIcon,
	Cog6ToothIcon,
	ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline'
import Sidebar from '~/components/partials/Sidebar.vue';
import { useAuthStore } from '~/stores/AuthStore';

const pageTitle = useState('pageTitle', () => 'Tableau de bord')
const pageDescription = useState('pageDescription', () => 'Bienvenue sur votre tableau de bord');

const authStore = useAuthStore();
const { user, fullName, userInitials } = storeToRefs(authStore);

// États pour les menus
const isMobileSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)
const isThemeMenuOpen = ref(false)
const isSidebarCollapsed = ref(false)
const isDarkMode = ref(false)

// Année courante pour le footer
const currentYear = new Date().getFullYear()

// Gestion du thème
const toggleThemeMenu = () => {
	isThemeMenuOpen.value = !isThemeMenuOpen.value
	isUserMenuOpen.value = false
}

const toggleUserMenu = () => {
	isUserMenuOpen.value = !isUserMenuOpen.value
	isThemeMenuOpen.value = false
}

const toggleMobileSidebar = () => {
	isMobileSidebarOpen.value = !isMobileSidebarOpen.value
}

const toggleSidebar = () => {
	isSidebarCollapsed.value = !isSidebarCollapsed.value
}

const setTheme = (theme: string) => {
	if (process.client) {
		const root = document.documentElement
		if (theme === 'dark') {
			root.classList.add('dark')
			isDarkMode.value = true
		} else if (theme === 'light') {
			root.classList.remove('dark')
			isDarkMode.value = false
		} else {
			// Système
			if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
				root.classList.add('dark')
				isDarkMode.value = true
			} else {
				root.classList.remove('dark')
				isDarkMode.value = false
			}
		}
		localStorage.setItem('theme', theme)
		isThemeMenuOpen.value = false
	}
}

// Initialisation du thème
onMounted(() => {
	const savedTheme = localStorage.getItem('theme') || 'system'
	setTheme(savedTheme)
})

// Fermer les menus quand on clique ailleurs
const closeAllMenus = () => {
	isUserMenuOpen.value = false
	isThemeMenuOpen.value = false
}

const logout = async () => {
	await authStore.logout();
	navigateTo(AppUrl.LOGIN);
}

// Écouteur pour fermer les menus
onMounted(() => {
	const savedTheme = localStorage.getItem('theme') || 'system'
	setTheme(savedTheme)

	// Ajouter l'écouteur de clics
	document.addEventListener('click', closeAllMenus)
})

onUnmounted(() => {
	document.removeEventListener('click', closeAllMenus)
})
</script>