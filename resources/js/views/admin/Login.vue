<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 space-y-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold">Espace Administrateur</h2>
                        <p class="text-gray-600">Connectez-vous pour gérer les événements</p>
                    </div>

                    <form @submit.prevent="login" class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="admin@example.com"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="loading"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="loading"
                            >
                        </div>

                        <div v-if="error" class="p-3 bg-red-100 text-red-800 rounded-md">
                            {{ error }}
                        </div>

                        <button
                            type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            :disabled="loading"
                        >
                            {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
                        </button>
                    </form>

                    <div class="text-center">
                        <router-link to="/" class="text-sm text-gray-600 hover:text-gray-900">
                            Retour à l'accueil
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/store';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

export default {
    name: 'Login',
    setup() {
        const authStore = useAuthStore();
        const router = useRouter();

        const form = ref({
            email: '',
            password: ''
        });

        const loading = ref(false);
        const error = ref('');

        const login = async () => {
            error.value = '';
            loading.value = true;

            try {
                const success = await authStore.login(form.value);

                if (success) {
                    router.push('/admin/dashboard');
                } else {
                    error.value = 'Identifiants incorrects';
                }
            } catch (err) {
                error.value = 'Une erreur est survenue lors de la connexion';
                console.error(err);
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            error,
            login
        };
    }
}
</script>
