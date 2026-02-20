<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Bem-vindo" />
    <div class="flex min-h-screen flex-col items-center justify-center bg-gray-100">
        <div class="w-full max-w-md px-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">
                    Avaliação de Perfil
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Sistema de avaliação de perfil comportamental
                </p>
            </div>

            <div class="mt-8 flex justify-center gap-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    Entrar
                </Link>
            </div>
        </div>
    </div>
</template>
