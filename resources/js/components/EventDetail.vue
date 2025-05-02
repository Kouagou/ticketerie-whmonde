<template>
    <div v-if="loading" class="text-center py-12">
        <p class="text-xl">Chargement de l'événement...</p>
    </div>

    <div v-else-if="error" class="text-center py-12">
        <p class="text-xl text-red-600">{{ error }}</p>
        <router-link to="/events" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md">
            Retour aux événements
        </router-link>
    </div>

    <div v-else class="event-detail">
        <div class="mb-6">
            <router-link to="/events" class="flex items-center text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour aux événements
            </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-2xl font-bold">{{ event.title }}</h2>
                            <span
                                class="px-2 py-1 text-xs rounded-full"
                                :class="event.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                            >
                {{ event.status === 'active' ? 'Actif' : 'Expiré' }}
              </span>
                        </div>

                        <p class="text-gray-700 mb-6">{{ event.description }}</p>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-3 text-gray-500"></i>
                                <span>Du {{ formatDate(event.start_date) }} au {{ formatDate(event.end_date) }}</span>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 text-gray-500"></i>
                                <span>
                  {{ event.current_participants }} / {{ event.max_participants }} participants
                  <span
                      v-if="isEventFull"
                      class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full"
                  >
                    Complet
                  </span>
                </span>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-3 text-gray-500"></i>
                                <span>{{ event.location || 'Lieu à confirmer' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4">Participer à cet événement</h3>

                        <p v-if="isEventFull" class="text-red-600 mb-4">
                            Cet événement est complet.
                        </p>

                        <p v-else-if="!isEventActive" class="text-gray-600 mb-4">
                            Cet événement est expiré.
                        </p>

                        <p v-else class="text-gray-600 mb-4">
                            Remplissez le formulaire pour vous inscrire.
                        </p>

                        <form @submit.prevent="registerForEvent" v-if="isEventActive && !isEventFull">
                            <div class="space-y-4">
                                <div>
                                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                    <input
                                        id="firstName"
                                        v-model="form.firstName"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :disabled="submitting"
                                    >
                                </div>

                                <div>
                                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                    <input
                                        id="lastName"
                                        v-model="form.lastName"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :disabled="submitting"
                                    >
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :disabled="submitting"
                                    >
                                </div>

                                <button
                                    type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                                    :disabled="submitting"
                                >
                                    {{ submitting ? 'Traitement en cours...' : 'S\'inscrire' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        id: {
            type: [String, Number],
            required: true
        }
    },

    data() {
        return {
            event: null,
            loading: true,
            error: null,
            form: {
                firstName: '',
                lastName: '',
                email: ''
            },
            submitting: false,
            registrationSuccess: false
        };
    },

    computed: {
        isEventFull() {
            return this.event && this.event.current_participants >= this.event.max_participants;
        },

        isEventActive() {
            return this.event && this.event.status === 'active';
        }
    },

    created() {
        this.fetchEvent();
    },

    methods: {
        async fetchEvent() {
            try {
                this.loading = true;
                const response = await axios.get(`/api/events/${this.id}`);
                this.event = response.data;
            } catch (error) {
                this.error = 'Erreur lors du chargement de l\'événement';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async registerForEvent() {
            if (this.isEventFull || !this.isEventActive) {
                return;
            }

            this.submitting = true;

            try {
                const response = await axios.post('/api/register', {
                    event_id: this.id,
                    first_name: this.form.firstName,
                    last_name: this.form.lastName,
                    email: this.form.email
                });

                this.registrationSuccess = true;
                this.$toast.success('Inscription réussie ! Un email contenant votre ticket a été envoyé.');

                // Redirection après quelques secondes
                setTimeout(() => {
                    this.$router.push('/events');
                }, 3000);
            } catch (error) {
                let errorMessage = 'Erreur lors de l\'inscription';

                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = error.response.data.error;
                }

                this.$toast.error(errorMessage);
                console.error(error);
            } finally {
                this.submitting = false;
            }
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
};
</script>
