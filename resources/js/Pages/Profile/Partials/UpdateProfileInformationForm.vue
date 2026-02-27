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
            <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                <div class="flex items-center gap-5">
                    <div
                        class="relative h-20 w-20 flex-shrink-0 cursor-pointer overflow-hidden rounded-full shadow-md ring-2 ring-white hover:ring-brand-400 transition-all duration-200"
                        @click="selectFile"
                        title="Alterar foto"
                    >
                        <img
                            v-if="preview"
                            :src="preview"
                            alt="Foto de perfil"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-brand-100 text-2xl font-bold text-brand-700">
                            {{ user.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 hover:opacity-100 transition">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="selectFile"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-white px-3 py-1.5 text-xs font-semibold text-brand-700 shadow-sm transition-all duration-200 hover:bg-brand-50 hover:-translate-y-px"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                </svg>
                                Alterar foto
                            </button>
                            <button
                                v-if="user.foto_url"
                                type="button"
                                @click="removerFoto"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm transition-all duration-200 hover:bg-red-50 hover:-translate-y-px"
                                :disabled="formRemover.processing"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                Remover
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">JPG, PNG ou WebP. Tamanho máximo: 2 MB.</p>
                        <div v-if="formFoto.errors.foto" class="text-xs text-red-600">{{ formFoto.errors.foto }}</div>
                        <div v-if="formFoto.processing" class="flex items-center gap-1.5 text-xs text-brand-600">
                            <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Enviando...
                        </div>
                    </div>
                </div>
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    @change="onFileChange"
                />
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
