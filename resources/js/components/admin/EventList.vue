
<template>
    <div class="event-list">
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
                <input
                    v-model="searchTerm"
                    type="text"
                    placeholder="Rechercher un événement..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div class="w-full md:w-48">
                <select
                    v-model="statusFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="all">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="expired">Expirés</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="text-center py-8">
            <p class="text-lg">Chargement des événements...</p>
        </div>

        <div v-else-if="filteredEvents.length === 0" class="text-center py-8 bg-white rounded-lg shadow">
            <p class="text-lg">Aucun événement ne correspond à votre recherche.</p>
        </div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="text-left py-3 px-4">Titre</th>
                    <th class="text-left py-3 px-4 hidden md:table-cell">Dates</th>
                    <th class="text-left py-3 px-4">Statut</th>
                    <th class="text-left py-3 px-4 hidden md:table-cell">Participants</th>
                    <th class="text-right py-3 px-4">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="event in filteredEvents" :key="event.id" class="border-b">
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-medium">{{ event.title }}</p>
                            <p class="text-sm text-gray-500 md:hidden">
                                {{ formatDate(event.start_date) }}
                            </p>
                        </div>
                    </td>
                    <td class="py-3 px-4 hidden md:table-cell">
                        <div>
                            <p>Début: {{ formatDate(event.start_date) }}</p>
                            <p>Fin: {{ formatDate(event.end_date) }}</p>
                        </div>
                    </td>
                    <td class="py-3 px-4">
              <span
                  class="px-2 py-1 rounded-full text-xs"
                  :class="event.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
              >
                {{ event.status === 'active' ? 'Actif' : 'Expiré' }}
              </span>
                    </td>
                    <td class="py-3 px-4 hidden md:table-cell">
                        <div>
                            <p>{{ event.current_participants }} / {{ event.max_participants }}</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div
                                    class="bg-blue-600 h-2 rounded-full"
                                    :style="{ width: `${(event.current_participants / event.max_participants) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex justify-end gap-2">
                            <router-link :to="`/admin/events/${event.id}/participants`">
                                <button class="p-1 text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-users"></i>
                                    <span class="sr-only md:not-sr-only md:ml-2">Participants</span>
                                </button>
                            </router-link>
                            <button @click="$emit('edit', event)" class="p-1 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-edit"></i>
                                <span class="sr-only md:not-sr-only md:ml-2">Modifier</span>
                            </button>
                            <button @click="confirmDelete(event)" class="p-1 text-gray-600 hover:text-gray-900">
                                <i class="fas fa-trash"></i>
                                <span class="sr-only md:not-sr-only md:ml-2">Supprimer</span>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal de confirmation de suppression -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
                <p class="mb-6">
                    Êtes-vous sûr de vouloir supprimer l'événement "{{ eventToDelete?.title }}" ?
                    Cette action est réversible mais l'événement ne sera plus visible pour les utilisateurs.
                </p>
                <div class="flex justify-end gap-4">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    >
                        Annuler
                    </button>
                    <button
                        @click="deleteEvent"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        events: {
            type: Array,
            required: true
        },
        loading: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            searchTerm: '',
            statusFilter: 'all',
            showDeleteModal: false,
            eventToDelete: null
        };
    },

    computed: {
        filteredEvents() {
            return this.events.filter(event => {
                const matchesSearch = event.title.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    event.description.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesStatus = this.statusFilter === 'all' || event.status === this.statusFilter;
                return matchesSearch && matchesStatus;
            });
        }
    },

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        confirmDelete(event) {
            this.eventToDelete = event;
            this.showDeleteModal = true;
        },

        async deleteEvent() {
            try {
                await axios.delete(`/api/admin/events/${this.eventToDelete.id}`);
                this.$emit('deleted', this.eventToDelete.id);
                this.$toast.success(`L'événement "${this.eventToDelete.title}" a été supprimé avec succès.`);
            } catch (error) {
                this.$toast.error('Erreur lors de la suppression de l\'événement');
                console.error(error);
            } finally {
                this.showDeleteModal = false;
                this.eventToDelete = null;
            }
        }
    }
};
</script>
