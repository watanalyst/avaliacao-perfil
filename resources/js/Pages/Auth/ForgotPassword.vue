<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Senha" />

        <!-- Estado: link enviado com sucesso -->
        <div v-if="status" class="text-center">
            <div class="mb-4 flex justify-center">
                <svg class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>

            <h2 class="mb-2 text-lg font-semibold text-gray-800">
                E-mail enviado!
            </h2>

            <p class="mb-6 text-sm text-gray-600">
                Enviamos um link de recuperação para o seu e-mail.
                Verifique sua caixa de entrada e a pasta de spam.
            </p>

            <Link
                :href="route('login')"
                class="inline-block rounded-md bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
            >
                Voltar ao Login
            </Link>
        </div>

        <!-- Estado: formulário de recuperação -->
        <div v-else>
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">Recuperar Senha</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Informe seu e-mail e enviaremos um link para redefinir sua senha.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="email" value="E-mail" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="seu@email.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {{ form.processing ? 'Enviando...' : 'Enviar Link' }}
                </button>

                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium text-brand-600 hover:text-brand-700 transition"
                    >
                        Voltar ao Login
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
