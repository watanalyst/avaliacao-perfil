<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  avaliacoes: Object,
  filters: Object,
})

const busca = ref(props.filters?.busca ?? '')
const activeStatus = ref(props.filters?.status ?? '')

let debounceTimer = null

function applyFilters() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    router.get(route('avaliacoes.index'), {
      busca: busca.value || undefined,
      status: activeStatus.value || undefined,
    }, {
      preserveState: true,
      replace: true,
    })
  }, 300)
}

watch(busca, applyFilters)

function clearStatusFilter() {
  activeStatus.value = ''
  applyFilters()
}

function formatDateBr(value) {
  if (!value) return ''
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return String(value)
  return d.toLocaleDateString('pt-BR')
}
</script>

<template>
  <Head title="Avaliações" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Título + Busca + Ação -->
            <div class="mb-6 flex items-center gap-4">
              <h2 class="text-xl font-bold text-gray-900 shrink-0">Avaliações</h2>
              <div class="relative flex-1 max-w-sm ml-auto">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                  v-model="busca"
                  type="text"
                  placeholder="Buscar por matrícula ou nome..."
                  class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 uppercase focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition"
                />
              </div>
              <Link
                :href="route('avaliacoes.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 transition shrink-0"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nova avaliação
              </Link>
            </div>

            <!-- Chip de filtro ativo -->
            <div v-if="activeStatus" class="mb-4 flex items-center gap-2">
              <span class="text-sm text-gray-500">Filtro:</span>
              <span
                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold"
                :class="{
                  'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20': activeStatus === 'RASCUNHO',
                  'bg-green-50 text-green-700 ring-1 ring-green-600/20': activeStatus === 'FINALIZADA',
                  'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20': activeStatus === 'RENOVAR',
                }"
              >
                {{ activeStatus === 'RENOVAR' ? 'A Renovar' : activeStatus }}
                <button
                  @click="clearStatusFilter"
                  class="ml-0.5 rounded-full p-0.5 hover:bg-black/10 transition"
                >
                  <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="border-b border-gray-100 bg-gray-50/80 text-left">
                    <th class="px-4 py-3 font-semibold text-gray-600">Matrícula</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Colaborador</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Data</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Avaliador</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="a in avaliacoes.data"
                    :key="a.ID ?? a.id"
                    class="border-b border-gray-50 last:border-0 transition-colors cursor-pointer"
                    :class="(a.STATUS ?? a.status) === 'RASCUNHO'
                      ? 'bg-amber-50/40 hover:bg-amber-50/70'
                      : 'hover:bg-gray-50/50'"
                    @click="router.visit(route('avaliacoes.show', a.ID ?? a.id))"
                  >
                    <td class="px-4 py-3 font-medium text-gray-900">{{ a.NUMCAD ?? a.numcad }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ a.NOMFUN ?? a.nomfun }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ formatDateBr(a.DATA_AVALIACAO ?? a.data_avaliacao) }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ a.avaliador_nome ?? '—' }}</td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-1.5">
                        <span
                          class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                          :class="(a.STATUS ?? a.status) === 'FINALIZADA'
                            ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                            : 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20'"
                        >
                          {{ a.STATUS ?? a.status }}
                        </span>
                        <span
                          v-if="a.vencida"
                          class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-semibold text-yellow-700 ring-1 ring-yellow-600/20"
                        >
                          <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                          </svg>
                          Renovar
                        </span>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="!avaliacoes.data || avaliacoes.data.length === 0">
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                      {{ busca ? 'Nenhuma avaliação encontrada para a busca.' : 'Nenhuma avaliação cadastrada.' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="avaliacoes.links" class="mt-5 flex flex-wrap gap-2">
              <template v-for="(link, idx) in avaliacoes.links" :key="idx">
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
  </AuthenticatedLayout>
</template>
