<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Painel esquerdo — branding -->
        <div class="hidden lg:flex lg:w-1/2 lg:flex-col lg:items-center lg:justify-center relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-900">

            <div class="flex flex-col items-center px-12 text-center">
                <div class="mb-10 rounded-3xl bg-white/95 p-8 shadow-2xl backdrop-blur-sm">
                    <img src="/img/btz.png" alt="Grupo BTZ" class="h-28 w-auto" />
                </div>
                <h1 class="text-3xl font-bold text-white mb-3">Avaliação de Perfil</h1>
                <div class="mx-auto mb-6 h-1 w-16 rounded-full bg-brand-400"></div>
                <p class="text-brand-200 text-sm max-w-xs leading-relaxed">
                    Sistema de avaliação de perfil comportamental do Grupo BTZ.
                </p>
            </div>

            <p class="absolute bottom-6 text-xs text-brand-300/60">
                &copy; {{ new Date().getFullYear() }} Grupo BTZ
            </p>
        </div>

        <!-- Painel direito — formulário -->
        <div class="flex w-full flex-col items-center justify-center bg-gray-100 px-4 sm:px-6 lg:w-1/2">
            <!-- Logo mobile -->
            <div class="mb-8 flex flex-col items-center lg:hidden">
                <Link href="/" class="flex flex-col items-center gap-3">
                    <img src="/img/btz.png" alt="Grupo BTZ" class="h-16 w-auto" />
                    <span class="text-xl font-bold text-brand-700">Avaliação de Perfil</span>
                </Link>
            </div>

            <div class="w-full max-w-md">
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-gray-900/5">
                    <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
                    <div class="px-8 py-8">
                        <slot />
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-400 lg:hidden">
                    &copy; {{ new Date().getFullYear() }} Grupo BTZ. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>
</template>
