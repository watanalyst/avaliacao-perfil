<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Alterar Senha" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
                    <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
                    <div class="p-6 sm:p-8">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Alterar Senha</h2>
                            <Link
                                :href="route('profile.edit')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                                Voltar
                            </Link>
                        </div>
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
