<template>
    <div class="event-form">
        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre de l'événement *</label>
                    <input
                        id="title"
                        v-model="form.title"
                        type="text"
                        placeholder="Ex: Conférence Tech 2024"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="loading"
                    >
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Décrivez votre événement..."
                        rows="5"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="loading"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                        <input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Heure de début *</label>
                        <input
                            id="start_time"
                            v-model="form.start_time"
                            type="time"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                        <input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Heure de fin *</label>
                        <input
                            id="end_time"
                            v-model="form.end_time"
                            type="time"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-1">Nombre maximum de participants *</label>
                        <input
                            id="max_participants"
                            v-model.number="form.max_participants"
                            type="number"
                            min="1"
                            placeholder="Ex: 100"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                            <option value="active">Actif</option>
                            <option value="expired">Expiré</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <button
                    type="button"
                    @click="$emit('cancel')"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
                    :disabled="loading"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    :disabled="loading"
                >
                    {{ loading ? (isEditing ? 'Mise à jour en cours...' : 'Création en cours...') : (isEditing ? 'Mettre à jour' : 'Créer l\'événement') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        event: {
            type: Object,
            default: null
        }
    },

    data() {
        return {
            form: {
                title: '',
                description: '',
                start_date: '',
                start_time: '',
                end_date: '',
                end_time: '',
                max_participants: '',
                status: 'active'
            },
            loading: false
        };
    },

    computed: {
        isEditing() {
            return !!this.event;
        }
    },

    created() {
        if (this.event) {
            this.initFormFromEvent();
        }
    },

    methods: {
        initFormFromEvent() {
            const startDate = new Date(this.event.start_date);
            const endDate = new Date(this.event.end_date);

            this.form = {
                title: this.event.title,
                description: this.event.description,
                start_date: this.formatDateForInput(startDate),
                start_time: this.formatTimeForInput(startDate),
                end_date: this.formatDateForInput(endDate),
                end_time: this.formatTimeForInput(endDate),
                max_participants: this.event.max_participants,
                status: this.event.status
            };
        },

        formatDateForInput(date) {
            return date.toISOString().split('T')[0];
        },

        formatTimeForInput(date) {
            return date.toTimeString().slice(0, 5);
        },

        async submitForm() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;

            try {
                const formData = this.prepareFormData();

                if (this.isEditing) {
                    await axios.put(`/api/admin/events/${this.event.id}`, formData);
                    this.$toast.success('Événement mis à jour avec succès');
                } else {
                    await axios.post('/api/admin/events', formData);
                    this.$toast.success('Événement créé avec succès');
                }

                this.$emit('success');
            } catch (error) {
                let errorMessage = this.isEditing ? 'Erreur lors de la mise à jour' : 'Erreur lors de la création';

                if (error.response && error.response.data && error.response.data.errors) {
                    const errors = error.response.data.errors;
                    errorMessage = Object.values(errors).flat().join(', ');
                }

                this.$toast.error(errorMessage);
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            // Validation simple
            if (!this.form.title || !this.form.description || !this.form.start_date ||
                !this.form.start_time || !this.form.end_date || !this.form.end_time ||
                !this.form.max_participants) {
                this.$toast.error('Veuillez remplir tous les champs obligatoires');
                return false;
            }

            // Vérifier que la date de fin est après la date de début
            const startDateTime = new Date(`${this.form.start_date}T${this.form.start_time}`);
            const endDateTime = new Date(`${this.form.end_date}T${this.form.end_time}`);

            if (endDateTime <= startDateTime) {
                this.$toast.error('La date de fin doit être postérieure à la date de début');
                return false;
            }

            return true;
        },

        prepareFormData() {
            // Combiner date et heure
            const startDateTime = new Date(`${this.form.start_date}T${this.form.start_time}`);
            const endDateTime = new Date(`${this.form.end_date}T${this.form.end_time}`);

            return {
                title: this.form.title,
                description: this.form.description,
                start_date: startDateTime.toISOString(),
                end_date: endDateTime.toISOString(),
                max_participants: this.form.max_participants,
                status: this.form.status
            };
        }
    }
};
</script>
