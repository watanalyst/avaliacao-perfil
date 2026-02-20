<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    recentes: Array,
});

const cardCount = computed(() => {
    let n = 3; // Total, Rascunhos, Finalizadas
    if (props.stats.a_renovar > 0) n++;
    if (props.stats.total_usuarios !== undefined) n++;
    return n;
});

function titleCase(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
}

function formatDateBr(value) {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return String(value);
    return d.toLocaleDateString('pt-BR');
}
</script>

<template>
    <Head title="Painel" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Saudação -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Olá, {{ titleCase($page.props.auth.user.name) }}!
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Acompanhe as avaliações de perfil comportamental.
                    </p>
                </div>

                <!-- Cards de Estatísticas -->
                <div
                    class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2"
                    :class="{
                        'lg:grid-cols-3': cardCount === 3,
                        'lg:grid-cols-4': cardCount === 4,
                        'lg:grid-cols-5': cardCount === 5,
                    }"
                >
                    <!-- Total -->
                    <Link :href="route('avaliacoes.index')" class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 hover:shadow-xl hover:ring-brand-300 transition-all cursor-pointer">
                        <div class="h-1 bg-brand-600"></div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50">
                                <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_avaliacoes }}</p>
                            </div>
                        </div>
                    </Link>

                    <!-- Rascunhos -->
                    <Link :href="route('avaliacoes.index', { status: 'RASCUNHO' })" class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 hover:shadow-xl hover:ring-amber-300 transition-all cursor-pointer">
                        <div class="h-1 bg-amber-500"></div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Rascunhos</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.rascunhos }}</p>
                            </div>
                        </div>
                    </Link>

                    <!-- Finalizadas -->
                    <Link :href="route('avaliacoes.index', { status: 'FINALIZADA' })" class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 hover:shadow-xl hover:ring-green-300 transition-all cursor-pointer">
                        <div class="h-1 bg-green-500"></div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-50">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Finalizadas</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.finalizadas }}</p>
                            </div>
                        </div>
                    </Link>

                    <!-- A Renovar -->
                    <Link
                        v-if="stats.a_renovar > 0"
                        :href="route('avaliacoes.index', { status: 'RENOVAR' })"
                        class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 hover:shadow-xl hover:ring-yellow-300 transition-all cursor-pointer"
                    >
                        <div class="h-1 bg-yellow-500"></div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-50">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">A Renovar</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.a_renovar }}</p>
                            </div>
                        </div>
                    </Link>

                    <!-- Usuários (apenas RH) -->
                    <Link v-if="stats.total_usuarios !== undefined" :href="route('users.index')" class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5 hover:shadow-xl hover:ring-violet-300 transition-all cursor-pointer">
                        <div class="h-1 bg-violet-500"></div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50">
                                <svg class="h-6 w-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Usuários</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_usuarios }}</p>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Ações Rápidas -->
                <div class="mb-8">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900">Ações Rápidas</h2>
                    <div class="flex flex-wrap gap-3">
                        <Link
                            :href="route('avaliacoes.create')"
                            class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nova Avaliação
                        </Link>
                        <Link
                            :href="route('avaliacoes.index')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            Ver Todas
                        </Link>
                        <Link
                            v-if="$page.props.auth.user.role === 'RH'"
                            :href="route('users.create')"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 transition"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                            Novo Usuário
                        </Link>
                    </div>
                </div>

                <!-- Avaliações Recentes -->
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Avaliações Recentes</h2>
                        <Link
                            :href="route('avaliacoes.index')"
                            class="text-sm font-medium text-brand-600 hover:text-brand-700 transition"
                        >
                            Ver todas
                        </Link>
                    </div>
                    <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
                        <div class="h-1 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50/80 text-left">
                                        <th class="px-5 py-3.5 font-semibold text-gray-600">Matrícula</th>
                                        <th class="px-5 py-3.5 font-semibold text-gray-600">Colaborador</th>
                                        <th class="px-5 py-3.5 font-semibold text-gray-600">Data</th>
                                        <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                                        <th class="px-5 py-3.5 font-semibold text-gray-600">Avaliador</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="a in recentes"
                                        :key="a.ID ?? a.id"
                                        class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors"
                                    >
                                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ a.NUMCAD ?? a.numcad }}</td>
                                        <td class="px-5 py-3.5 text-gray-700">{{ a.NOMFUN ?? a.nomfun }}</td>
                                        <td class="px-5 py-3.5 text-gray-500">{{ formatDateBr(a.DATA_AVALIACAO ?? a.data_avaliacao) }}</td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                                :class="(a.STATUS ?? a.status) === 'FINALIZADA'
                                                    ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                                                    : 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20'"
                                            >
                                                {{ a.STATUS ?? a.status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-700">{{ a.avaliador_nome ?? '—' }}</td>
                                    </tr>

                                    <tr v-if="!recentes || recentes.length === 0">
                                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                            Nenhuma avaliação cadastrada ainda.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
