<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { InputError, InputLabel, TextInput } from '@jagua/ui';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir Senha" />

        <!-- Estado: senha redefinida com sucesso -->
        <div v-if="status" class="text-center">
            <div class="mb-4 flex justify-center">
                <svg class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="mb-2 text-lg font-semibold text-gray-800">
                Senha redefinida!
            </h2>

            <p class="mb-6 text-sm text-gray-600">
                Sua senha foi alterada com sucesso.
                Agora você pode fazer login com a nova senha.
            </p>

            <Link
                :href="route('login')"
                class="inline-block rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-[0_4px_14px_0_rgba(9,63,135,0.35)] transition-all duration-200 hover:-translate-y-px hover:brightness-110"
                style="background: linear-gradient(135deg, #093F87 0%, #0B56B3 100%)"
            >
                Ir para o Login
            </Link>
        </div>

        <!-- Estado: formulário de redefinição -->
        <div v-else>
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Redefinir Senha</h2>
                <p class="mt-1 text-sm text-gray-500">Defina sua nova senha abaixo.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="email" value="E-mail" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-gray-100"
                        v-model="form.email"
                        required
                        disabled
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Nova Senha" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirmar Nova Senha" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-[0_4px_14px_0_rgba(9,63,135,0.35)] transition-all duration-200 hover:-translate-y-px hover:brightness-110 disabled:opacity-50 disabled:hover:translate-y-0"
                    style="background: linear-gradient(135deg, #093F87 0%, #0B56B3 100%)"
                >
                    {{ form.processing ? 'Redefinindo...' : 'Redefinir Senha' }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
