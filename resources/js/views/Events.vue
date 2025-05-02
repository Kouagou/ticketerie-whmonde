<template>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-8">Événements disponibles</h2>

        <div v-if="loading" class="text-center py-12">
            <p class="text-xl">Chargement des événements...</p>
        </div>

        <div v-else-if="events.length === 0" class="text-center py-12">
            <p class="text-xl">Aucun événement disponible pour le moment.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="event in events" :key="event.id" class="flex flex-col h-full">
                <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-semibold">{{ event.title }}</h3>
                            <span
                                class="px-2 py-1 text-xs rounded-full"
                                :class="event.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                            >
                {{ event.status === 'active' ? 'Actif' : 'Expiré' }}
              </span>
                        </div>
                        <p class="text-gray-600 mb-4">{{ event.description }}</p>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Du {{ formatDate(event.start_date) }} au {{ formatDate(event.end_date) }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-users mr-2"></i>
                                <span>
                  {{ event.current_participants }} / {{ event.max_participants }} participants
                  <span
                      v-if="event.current_participants >= event.max_participants"
                      class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full"
                  >
                    Complet
                  </span>
                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto p-4 bg-gray-50 border-t">
                        <router-link
                            :to="{ name: 'event-detail', params: { id: event.id }}"
                            class="w-full inline-block text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            :class="{
                'opacity-50 cursor-not-allowed': event.current_participants >= event.max_participants || event.status !== 'active'
              }"
                        >
                            {{
                                event.current_participants >= event.max_participants
                                    ? 'Événement complet'
                                    : event.status !== 'active'
                                        ? 'Événement expiré'
                                        : 'Participer'
                            }}
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useEventStore } from '@/store';
import { onMounted, ref } from 'vue';

export default {
    name: 'Events',
    setup() {
        const eventStore = useEventStore();
        const events = ref([]);
        const loading = ref(true);

        onMounted(async () => {
            await eventStore.fetchEvents();
            events.value = eventStore.events;
            loading.value = false;
        });

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        return {
            events,
            loading,
            formatDate
        };
    }
}
</script>
