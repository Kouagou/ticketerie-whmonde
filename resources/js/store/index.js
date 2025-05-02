import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const response = await axios.post('/api/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                return true;
            } catch (error) {
                console.error('Login error:', error);
                return false;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                this.loading = false;
            }
        },

        async fetchUser() {
            if (!this.token) return;

            this.loading = true;
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                console.error('Fetch user error:', error);
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
            } finally {
                this.loading = false;
            }
        }
    }
});

export const useEventStore = defineStore('events', {
    state: () => ({
        events: [],
        event: null,
        loading: false
    }),

    actions: {
        async fetchEvents() {
            this.loading = true;
            try {
                const response = await axios.get('/api/events');
                this.events = response.data;
            } catch (error) {
                console.error('Fetch events error:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchEvent(id) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/events/${id}`);
                this.event = response.data;
                return this.event;
            } catch (error) {
                console.error('Fetch event error:', error);
                return null;
            } finally {
                this.loading = false;
            }
        },

        async createEvent(eventData) {
            this.loading = true;
            try {
                const response = await axios.post('/api/admin/events', eventData);
                return response.data;
            } catch (error) {
                console.error('Create event error:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateEvent(id, eventData) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/admin/events/${id}`, eventData);
                return response.data;
            } catch (error) {
                console.error('Update event error:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteEvent(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/admin/events/${id}`);
                this.events = this.events.filter(event => event.id !== id);
                return true;
            } catch (error) {
                console.error('Delete event error:', error);
                return false;
            } finally {
                this.loading = false;
            }
        }
    }
});
