<template>
	<nav class="flex flex-col space-y-1">
		<NuxtLink v-for="item in sidebarMenu" :key="item.id" :to="item.url" :title="collapsed ? item.name : ''"
			class="group flex items-center rounded-lg px-3 py-3 text-sm font-medium transition-colors duration-200"
			active-class="bg-primary-500 text-white shadow-sm"
			inactive-class="text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-700">
			<component :is="iconComponents[item.icon]" :class="[
				'flex-shrink-0 transition-colors duration-200',
				collapsed ? 'w-5 h-5 mx-auto' : 'w-5 h-5 mr-3'
			]" />
			<span v-if="!collapsed" class="truncate transition-opacity duration-200">
				{{ item.name }}
			</span>
			<div v-if="collapsed" class="absolute left-full ml-2 hidden group-hover:flex">
				<div
					class="relative z-50 rounded-md bg-gray-900 dark:bg-dark-700 px-3 py-2 text-xs font-medium text-white shadow-lg">
					{{ item.name }}
					<div class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 bg-gray-900 dark:bg-dark-700"></div>
				</div>
			</div>
		</NuxtLink>
	</nav>
</template>

<script setup lang="ts">
import {
	HomeIcon,
	ClipboardDocumentListIcon,
	PlusCircleIcon,
	ClockIcon,
	TruckIcon,
	UsersIcon,
	BriefcaseIcon,
	ChartBarIcon,
	DocumentTextIcon,
	Cog6ToothIcon,
	BuildingOfficeIcon
} from '@heroicons/vue/24/outline'

interface Props {
	collapsed?: boolean
}

defineProps<Props>()

// Configuration du menu
const sidebarMenu = [
	{ id: 1, name: 'Tableau de bord', url: '/', icon: 'HomeIcon' },
	{ id: 2, name: 'Véhicules', url: '/vehicles', icon: 'TruckIcon' },
	{ id: 3, name: 'Réservations', url: '/reservations', icon: 'ClipboardDocumentListIcon' },
	{ id: 4, name: 'Nouvelle réservation', url: '/reservations/new', icon: 'PlusCircleIcon' },
	{ id: 5, name: 'Historique', url: '/history', icon: 'ClockIcon' },
	{ id: 6, name: 'Utilisateurs', url: '/users', icon: 'UsersIcon' },
	{ id: 7, name: 'Employés', url: '/employees', icon: 'BriefcaseIcon' },
	{ id: 8, name: 'Services', url: '/services', icon: 'BuildingOfficeIcon' },
	{ id: 9, name: 'Rapports', url: '/reports', icon: 'ChartBarIcon' },
	{ id: 10, name: 'Documents', url: '/documents', icon: 'DocumentTextIcon' },
	{ id: 11, name: 'Paramètres', url: '/settings', icon: 'Cog6ToothIcon' },
]

// Map des icônes
const iconComponents = {
	HomeIcon,
	ClipboardDocumentListIcon,
	PlusCircleIcon,
	ClockIcon,
	TruckIcon,
	UsersIcon,
	BriefcaseIcon,
	ChartBarIcon,
	DocumentTextIcon,
	Cog6ToothIcon,
	BuildingOfficeIcon
}
</script>
