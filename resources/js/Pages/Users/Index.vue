<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  users: Object,
})

const showModal = ref(false)
const selectedUser = ref(null)

function openToggleModal(user) {
  selectedUser.value = user
  showModal.value = true
}

function confirmToggle() {
  router.patch(route('users.toggleAtivo', selectedUser.value.id), {}, {
    onFinish: () => {
      showModal.value = false
      selectedUser.value = null
    },
  })
}

function formatDateBr(value) {
  if (!value) return ''
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return String(value)
  return d.toLocaleDateString('pt-BR')
}
</script>

<template>
  <Head title="Usuários" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Título + Ação -->
            <div class="mb-6 flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-900">Usuários</h2>
              <Link
                :href="route('users.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 transition"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
                Novo Usuário
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="border-b border-gray-100 bg-gray-50/80 text-left">
                    <th class="px-4 py-3 font-semibold text-gray-600">Matrícula</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Nome</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">E-mail</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Perfil</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Criado em</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="u in users.data"
                    :key="u.id"
                    class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors"
                  >
                    <td class="px-4 py-3 text-gray-700">{{ u.numcad }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ u.name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ u.email }}</td>
                    <td class="px-4 py-3">
                      <span
                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                        :class="u.role === 'RH'
                          ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-600/20'
                          : 'bg-green-50 text-green-700 ring-1 ring-green-600/20'"
                      >
                        {{ u.role === 'RH' ? 'Avaliador / Admin' : 'Avaliador' }}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <span
                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                        :class="u.ativo === 'S'
                          ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                          : 'bg-red-50 text-red-700 ring-1 ring-red-600/20'"
                      >
                        {{ u.ativo === 'S' ? 'Ativo' : 'Inativo' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ formatDateBr(u.created_at) }}</td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <Link
                          :href="route('users.edit', u.id)"
                          class="inline-flex items-center justify-center rounded-lg border border-brand-300 bg-white p-1.5 text-brand-700 shadow-sm hover:bg-brand-50 transition"
                          title="Editar"
                        >
                          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                          </svg>
                        </Link>
                        <button
                          @click="openToggleModal(u)"
                          class="inline-flex items-center justify-center rounded-lg border bg-white p-1.5 shadow-sm transition"
                          :class="u.ativo === 'S'
                            ? 'border-red-300 text-red-600 hover:bg-red-50'
                            : 'border-green-300 text-green-600 hover:bg-green-50'"
                          :title="u.ativo === 'S' ? 'Inativar' : 'Ativar'"
                        >
                          <!-- user-x (inativar) -->
                          <svg v-if="u.ativo === 'S'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                          </svg>
                          <!-- user-check (ativar) -->
                          <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="!users.data || users.data.length === 0">
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                      Nenhum usuário cadastrado.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="users.links" class="mt-5 flex flex-wrap gap-2">
              <template v-for="(link, idx) in users.links" :key="idx">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  class="rounded-lg border px-3 py-1.5 text-sm font-medium transition"
                  :class="link.active ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                  v-html="link.label"
                />
                <span
                  v-else
                  class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-300"
                  v-html="link.label"
                />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação -->
    <ConfirmModal
      :show="showModal"
      :title="selectedUser?.ativo === 'S' ? 'Inativar usuário' : 'Ativar usuário'"
      :message="selectedUser
        ? (selectedUser.ativo === 'S'
          ? `O usuário ${selectedUser.name} não poderá mais acessar o sistema. Deseja continuar?`
          : `O usuário ${selectedUser.name} voltará a ter acesso ao sistema. Deseja continuar?`)
        : ''"
      :confirm-label="selectedUser?.ativo === 'S' ? 'Inativar' : 'Ativar'"
      :variant="selectedUser?.ativo === 'S' ? 'danger' : 'success'"
      @confirm="confirmToggle"
      @cancel="showModal = false"
    />
  </AuthenticatedLayout>
</template>
