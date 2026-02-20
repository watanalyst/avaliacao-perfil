<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const fileInput = ref(null);
const preview = ref(user.foto_url);

const formFoto = useForm({ foto: null });
const formRemover = useForm({});

function titleCase(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
}

function selectFile() {
    fileInput.value.click();
}

function onFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    formFoto.foto = file;
    preview.value = URL.createObjectURL(file);
    formFoto.post(route('profile.updateFoto'), {
        preserveScroll: true,
        errorBag: 'updateFoto',
        forceFormData: true,
        onSuccess: () => { formFoto.reset(); },
    });
}

function removerFoto() {
    formRemover.delete(route('profile.destroyFoto'), {
        preserveScroll: true,
        onSuccess: () => { preview.value = null; },
    });
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Informações do Perfil
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Dados do seu cadastro gerenciados pelo RH.
            </p>
        </header>

        <div class="mt-6 space-y-4">
            <!-- Foto de perfil -->
            <div>
                <p class="text-sm font-medium text-gray-500">Foto</p>
                <div class="mt-2 flex items-center gap-4">
                    <div
                        class="relative h-16 w-16 flex-shrink-0 cursor-pointer overflow-hidden rounded-full ring-2 ring-gray-200 hover:ring-brand-400 transition"
                        @click="selectFile"
                        title="Alterar foto"
                    >
                        <img
                            v-if="preview"
                            :src="preview"
                            alt="Foto de perfil"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-brand-100 text-xl font-bold text-brand-700">
                            {{ user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 hover:opacity-100 transition">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <button
                            type="button"
                            @click="selectFile"
                            class="text-sm font-medium text-brand-600 hover:text-brand-700 transition text-left"
                        >
                            Alterar foto
                        </button>
                        <button
                            v-if="user.foto_url"
                            type="button"
                            @click="removerFoto"
                            class="text-sm font-medium text-red-500 hover:text-red-600 transition"
                            :disabled="formRemover.processing"
                        >
                            Remover
                        </button>
                        <p class="text-xs text-gray-400">JPG, PNG ou WebP. Máx 2 MB.</p>
                    </div>
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        @change="onFileChange"
                    />
                </div>
                <div v-if="formFoto.errors.foto" class="mt-1 text-sm text-red-600">{{ formFoto.errors.foto }}</div>
                <div v-if="formFoto.processing" class="mt-1 text-sm text-gray-500">Enviando...</div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Nome</p>
                <p class="mt-1 text-base text-gray-900">{{ titleCase(user.name) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">E-mail</p>
                <p class="mt-1 text-base text-gray-900">{{ user.email }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Perfil</p>
                <span
                    class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                    :class="user.role === 'RH'
                        ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-600/20'
                        : 'bg-green-50 text-green-700 ring-1 ring-green-600/20'"
                >
                    {{ user.role === 'RH' ? 'Avaliador / Admin' : 'Avaliador' }}
                </span>
            </div>
        </div>
    </section>
</template>
