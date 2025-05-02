<template>
    <div class="container mx-auto px-4 py-8">
        <section class="mb-12 text-center">
            <h2 class="text-4xl font-bold mb-4">Bienvenue sur notre plateforme d'événements</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Découvrez et participez à des événements exceptionnels. Aucune inscription requise !
            </p>
        </section>

        <div class="text-center mb-12">
            <router-link to="/events">
                <button class="px-8 py-6 text-lg bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Voir tous les événements
                </button>
            </router-link>
        </div>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Parcourez les événements</h3>
                <p class="text-gray-600">
                    Explorez notre catalogue d'événements variés et trouvez ceux qui vous intéressent.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Inscrivez-vous facilement</h3>
                <p class="text-gray-600">
                    Participez à n'importe quel événement en quelques clics, sans avoir besoin de créer un compte.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Recevez votre ticket</h3>
                <p class="text-gray-600">
                    Après inscription, recevez instantanément votre ticket unique par email.
                </p>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'Home'
}
</script>
