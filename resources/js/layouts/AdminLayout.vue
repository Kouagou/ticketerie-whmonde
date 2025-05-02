<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="hidden md:flex w-64 flex-col bg-white border-r">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold">Ticketerie Admin</h2>
            </div>
            <div class="flex-1 py-4">
                <nav class="space-y-1 px-2">
                    <router-link
                        to="/admin/dashboard"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                        :class="isActive('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <i class="fas fa-tachometer-alt mr-3 h-5 w-5"></i>
                        Tableau de bord
                    </router-link>
                    <router-link
                        to="/admin/events"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                        :class="isActive('events') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <i class="fas fa-calendar-alt mr-3 h-5 w-5"></i>
                        Événements
                    </router-link>
                    <router-link
                        to="/admin/statistics"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md"
                        :class="isActive('statistics') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                    >
                        <i class="fas fa-chart-bar mr-3 h-5 w-5"></i>
                        Statistiques
                    </router-link>
                </nav>
            </div>
            <div class="p-4 border-t">
                <button @click="logout" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-sign-out-alt mr-3 h-5 w-5"></i>
                    Déconnexion
                </button>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b p-4 flex justify-between items-center">
                <h1 class="text-xl font-semibold">{{ pageTitle }}</h1>
                <div class="flex items-center space-x-4">
                    <router-link v-if="showCreateButton" to="/admin/events/create">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvel événement
                        </button>
                    </router-link>
                    <button @click="logout" class="md:hidden px-3 py-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-sign-out-alt h-5 w-5"></i>
                    </button>
                </div>
            </header>

            <main class="flex-1 p-6">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/store';
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default {
    name: 'AdminLayout',
    setup() {
        const authStore = useAuthStore();
        const route = useRoute();
        const router = useRouter();

        const pageTitle = computed(() => {
            switch (route.name) {
                case 'dashboard': return 'Tableau de bord';
                case 'admin-events': return 'Gestion des événements';
                case 'event-create': return 'Créer un nouvel événement';
                case 'event-edit': return 'Modifier l\'événement';
                case 'event-participants': return 'Participants';
                case 'statistics': return 'Statistiques';
                default: return 'Administration';
            }
        });

        const showCreateButton = computed(() => {
            return route.name === 'admin-events';
        });

        const isActive = (routeName) => {
            if (routeName === 'dashboard' && route.name === 'dashboard') return true;
            if (routeName === 'events' && ['admin-events', 'event-create', 'event-edit', 'event-participants'].includes(route.name)) return true;
            if (routeName === 'statistics' && route.name === 'statistics') return true;
            return false;
        };

        const logout = async () => {
            await authStore.logout();
            router.push('/admin/login');
        };

        return {
            pageTitle,
            showCreateButton,
            isActive,
            logout
        };
    }
}
</script>
