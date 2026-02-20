<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
                    <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
                    <div class="p-6 sm:p-8">
                        <h2 class="mb-6 text-xl font-bold text-gray-900">Perfil</h2>
                        <UpdateProfileInformationForm class="max-w-xl" />
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Segurança</h2>
                                <p class="mt-1 text-sm text-gray-600">Gerencie a senha da sua conta.</p>
                            </div>
                            <Link
                                :href="route('password.edit')"
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Alterar Senha
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
