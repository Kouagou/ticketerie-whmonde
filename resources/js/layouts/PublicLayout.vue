<template>
    <div class="flex flex-col min-h-screen">
        <header class="bg-white border-b">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <router-link to="/">
                    <h1 class="text-2xl font-bold text-gray-800">Ticketerie</h1>
                </router-link>
                <router-link to="/admin/login">
                    <button class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Espace Admin
                    </button>
                </router-link>
            </div>
        </header>

        <main class="flex-1">
            <router-view></router-view>
        </main>

        <footer class="bg-gray-100 py-6">
            <div class="container mx-auto px-4 text-center text-gray-600">
                <p>© {{ new Date().getFullYear() }} Ticketerie - Tous droits réservés</p>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'PublicLayout'
}
</script>
