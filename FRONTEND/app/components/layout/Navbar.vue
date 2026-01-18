<template>
  <nav class="bg-white dark:bg-gray-800 shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <!-- Logo et navigation principale -->
        <div class="flex items-center space-x-8">
          <NuxtLink to="/" class="text-xl font-bold text-primary-600 dark:text-primary-400">
            VehicleReserve
          </NuxtLink>

          <div class="hidden md:flex space-x-4">
            <NuxtLink
              to="/vehicles"
              class="nav-link"
              :class="{ 'active': isActive('/vehicles') }"
            >
              Véhicules
            </NuxtLink>
            <NuxtLink
              to="/missions"
              class="nav-link"
              :class="{ 'active': isActive('/missions') }"
            >
              Missions
            </NuxtLink>
            <NuxtLink
              to="/reservations"
              class="nav-link"
              :class="{ 'active': isActive('/reservations') }"
            >
              Réservations
            </NuxtLink>
            <NuxtLink
              v-if="authStore.isAdmin || authStore.isModerator"
              to="/admin"
              class="nav-link"
              :class="{ 'active': isActive('/admin') }"
            >
              Administration
            </NuxtLink>
          </div>
        </div>

        <!-- Actions utilisateur -->
        <div class="flex items-center space-x-4">
          <!-- Toggle thème sombre/clair -->
          <ClientOnly>
            <button
              @click="toggleTheme"
              class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
              aria-label="Changer de thème"
            >
              <Icon
                :name="colorMode.preference === 'dark' ? 'heroicons:sun' : 'heroicons:moon'"
                class="w-5 h-5 text-gray-600 dark:text-gray-300"
              />
            </button>
          </ClientOnly>

          <!-- Menu utilisateur connecté -->
          <div v-if="authStore.isAuthenticated" class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                <span class="text-primary-600 dark:text-primary-300 font-medium">
                  {{ userInitials }}
                </span>
              </div>
              <span class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ authStore.fullName }}
              </span>
              <Icon
                name="heroicons:chevron-down"
                class="w-4 h-4 text-gray-500 dark:text-gray-400"
              />
            </button>

            <!-- Dropdown menu -->
            <div
              v-if="userMenuOpen"
              class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700"
            >
              <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ authStore.fullName }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  {{ authStore.user?.email }}
                </p>
              </div>
              <NuxtLink
                to="/profile"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="userMenuOpen = false"
              >
                Mon profil
              </NuxtLink>
              <NuxtLink
                to="/my-reservations"
                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="userMenuOpen = false"
              >
                Mes réservations
              </NuxtLink>
              <div class="border-t border-gray-100 dark:border-gray-700">
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                  Déconnexion
                </button>
              </div>
            </div>
          </div>

          <!-- Boutons de connexion/déconnexion -->
          <div v-else class="flex items-center space-x-2">
            <NuxtLink
              to="/login"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >
              Connexion
            </NuxtLink>
            <NuxtLink
              to="/register"
              class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors"
            >
              Inscription
            </NuxtLink>
          </div>

          <!-- Menu mobile -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <Icon
              :name="mobileMenuOpen ? 'heroicons:x-mark' : 'heroicons:bars-3'"
              class="w-6 h-6 text-gray-600 dark:text-gray-300"
            />
          </button>
        </div>
      </div>

      <!-- Menu mobile (dropdown) -->
      <div
        v-if="mobileMenuOpen"
        class="md:hidden border-t border-gray-200 dark:border-gray-700 py-2"
      >
        <div class="flex flex-col space-y-1">
          <NuxtLink
            to="/vehicles"
            class="mobile-nav-link"
            :class="{ 'active': isActive('/vehicles') }"
            @click="mobileMenuOpen = false"
          >
            Véhicules
          </NuxtLink>
          <NuxtLink
            to="/missions"
            class="mobile-nav-link"
            :class="{ 'active': isActive('/missions') }"
            @click="mobileMenuOpen = false"
          >
            Missions
          </NuxtLink>
          <NuxtLink
            to="/reservations"
            class="mobile-nav-link"
            :class="{ 'active': isActive('/reservations') }"
            @click="mobileMenuOpen = false"
          >
            Réservations
          </NuxtLink>
          <NuxtLink
            v-if="authStore.isAdmin || authStore.isModerator"
            to="/admin"
            class="mobile-nav-link"
            :class="{ 'active': isActive('/admin') }"
            @click="mobileMenuOpen = false"
          >
            Administration
          </NuxtLink>

          <div v-if="authStore.isAuthenticated" class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
            <NuxtLink
              to="/profile"
              class="mobile-nav-link"
              @click="mobileMenuOpen = false"
            >
              Mon profil
            </NuxtLink>
            <NuxtLink
              to="/my-reservations"
              class="mobile-nav-link"
              @click="mobileMenuOpen = false"
            >
              Mes réservations
            </NuxtLink>
            <button
              @click="logout"
              class="w-full text-left mobile-nav-link text-red-600 dark:text-red-400"
            >
              Déconnexion
            </button>
          </div>
          <div v-else class="flex flex-col space-y-2 pt-2 mt-2 border-t border-gray-200 dark:border-gray-700">
            <NuxtLink
              to="/login"
              class="mobile-nav-link"
              @click="mobileMenuOpen = false"
            >
              Connexion
            </NuxtLink>
            <NuxtLink
              to="/register"
              class="mobile-nav-link bg-primary-600 text-white hover:bg-primary-700 rounded-lg text-center"
              @click="mobileMenuOpen = false"
            >
              Inscription
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth.store'

const route = useRoute()
const authStore = useAuthStore()
const colorMode = useColorMode()
const userMenuOpen = ref(false)
const mobileMenuOpen = ref(false)

// Initialiser l'auth au chargement
onMounted(() => {
  authStore.initialize()
})

// Calcul des initiales de l'utilisateur
const userInitials = computed(() => {
  if (!authStore.user) return '?'
  const { firstName, lastName } = authStore.user
  return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase()
})

// Vérifier si la route est active
const isActive = (path: string) => {
  return route.path.startsWith(path)
}

// Toggle du thème
const toggleTheme = () => {
  colorMode.preference = colorMode.preference === 'dark' ? 'light' : 'dark'
}

// Toggle du menu utilisateur
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

// Toggle du menu mobile
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  userMenuOpen.value = false
}

// Déconnexion
const logout = async () => {
  await authStore.logout()
  userMenuOpen.value = false
  mobileMenuOpen.value = false
}

// Fermer les menus en cliquant à l'extérieur
const closeMenus = () => {
  userMenuOpen.value = false
  mobileMenuOpen.value = false
}

// Fermer les menus quand on clique à l'extérieur
onClickOutside(document.body, closeMenus)
</script>

<style scoped>
.nav-link {
  @apply px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
         hover:text-primary-600 dark:hover:text-primary-400
         transition-colors rounded-lg;
}

.nav-link.active {
  @apply text-primary-600 dark:text-primary-400
         bg-primary-50 dark:bg-primary-900/20;
}

.mobile-nav-link {
  @apply px-4 py-3 text-base font-medium text-gray-700 dark:text-gray-300
         hover:text-primary-600 dark:hover:text-primary-400
         hover:bg-gray-50 dark:hover:bg-gray-700
         transition-colors rounded-lg;
}

.mobile-nav-link.active {
  @apply text-primary-600 dark:text-primary-400
         bg-primary-50 dark:bg-primary-900/20;
}
</style>
