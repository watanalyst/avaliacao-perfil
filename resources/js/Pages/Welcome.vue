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
                    class="rounded-lg px-6 py-3 text-sm font-semibold text-white shadow-[0_4px_14px_0_rgba(9,63,135,0.35)] transition-all duration-200 hover:-translate-y-px hover:brightness-110"
                    style="background: linear-gradient(135deg, #093F87 0%, #0B56B3 100%)"
                >
                    Entrar
                </Link>
            </div>
        </div>
    </div>
</template>
