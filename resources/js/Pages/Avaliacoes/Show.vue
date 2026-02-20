<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  avaliacao: Object,
  ifp: Object,
  profiler: Object,
  ancoras: Array,
  forcas: Array,
  niveis: Array,
  ancoraTipos: Array,
  virtudes: Array,
})

const activeTab = ref('geral')

const tabs = [
  { key: 'geral',    label: 'Geral' },
  { key: 'ifp',      label: 'IFP' },
  { key: 'profiler', label: 'Profiler' },
  { key: 'ancoras',  label: 'Âncoras' },
  { key: 'forcas',   label: 'Forças Pessoais' },
]

// Helper: obter campo com fallback de case
function g(obj, upper, lower) {
  if (!obj) return null
  return obj[upper] ?? obj[lower] ?? null
}

// Formatar decimal com vírgula
function fmtDecimal(val) {
  if (val === null || val === undefined || val === '') return '—'
  const num = parseFloat(val)
  if (isNaN(num)) return '—'
  return num.toFixed(2).replace('.', ',')
}

// Formatar data BR
function fmtDate(val) {
  if (!val) return '—'
  const d = new Date(val)
  if (isNaN(d.getTime())) return String(val)
  return d.toLocaleDateString('pt-BR')
}

// Labels IFP
const ifpLabels = {
  ASSISTENCIA: 'Assistência', INTRACEPCAO: 'Intracepção', AFAGO: 'Afago',
  AUTONOMIA: 'Autonomia', DEFERENCIA: 'Deferência', AFILIACAO: 'Afiliação',
  DOMINANCIA: 'Dominância', DESEMPENHO: 'Desempenho', EXIBICAO: 'Exibição',
  AGRESSAO: 'Agressão', ORDEM: 'Ordem', PERSISTENCIA: 'Persistência', MUDANCA: 'Mudança',
}

// Labels Profiler %
const profilerPctLabels = {
  EXECUTOR: 'Executor', PLANEJADOR: 'Planejador', ANALISTA: 'Analista', COMUNICADOR: 'Comunicador',
}

// Labels Profiler Níveis
const profilerNivelKeys = {
  ENERGIA_NIVEL_ID: 'Energia', EXIGENCIA_MEIO_NIVEL_ID: 'Exigência do Meio',
  APROVEITAMENTO_NIVEL_ID: 'Aproveitamento', MORAL_NIVEL_ID: 'Moral',
  POSITIVIDADE_NIVEL_ID: 'Positividade', FLEXIBILIDADE_NIVEL_ID: 'Flexibilidade',
  AMPLITUDE_NIVEL_ID: 'Amplitude', AUTOMOTIVACAO_NIVEL_ID: 'Automotivação',
  INCITABILIDADE_NIVEL_ID: 'Incitabilidade',
}

// Buscar descrição do nível por ID
function getNivelDescricao(nivelId) {
  if (!nivelId) return '—'
  const n = props.niveis?.find(x => (x.ID ?? x.id) == nivelId)
  return n ? (n.DESCRICAO ?? n.descricao) : '—'
}

// Buscar valor da âncora por tipo_id
function getAncoraValor(tipoId) {
  const a = props.ancoras?.find(x => (x.ANCORA_TIPO_ID ?? x.ancora_tipo_id) == tipoId)
  return a ? fmtDecimal(a.VALOR ?? a.valor) : '—'
}

// Buscar valor da força por forca_id
function getForcaValor(forcaId) {
  const f = props.forcas?.find(x => (x.FORCA_ID ?? x.forca_id) == forcaId)
  return f ? fmtDecimal(f.VALOR ?? f.valor) : '—'
}

const page = usePage()
const av = computed(() => props.avaliacao)
const isRascunho = computed(() => g(av.value, 'STATUS', 'status') === 'RASCUNHO')
const canEdit = computed(() => isRascunho.value || page.props.auth.user.role === 'RH')

// Contagem de campos preenchidos por seção
const ifpKeys = Object.keys(ifpLabels)
const profilerAllKeys = [...Object.keys(profilerPctLabels), ...Object.keys(profilerNivelKeys)]

const ifpCount = computed(() => {
  if (!props.ifp) return 0
  return ifpKeys.filter(k => {
    const v = g(props.ifp, k, k.toLowerCase())
    return v !== null && v !== undefined && v !== ''
  }).length
})

const profilerCount = computed(() => {
  if (!props.profiler) return 0
  return profilerAllKeys.filter(k => {
    const v = g(props.profiler, k, k.toLowerCase())
    return v !== null && v !== undefined && v !== ''
  }).length
})

const ancorasCount = computed(() => props.ancoras?.length ?? 0)
const forcasCount = computed(() => props.forcas?.length ?? 0)

const tabCounts = computed(() => ({
  ifp: ifpCount.value,
  profiler: profilerCount.value,
  ancoras: ancorasCount.value,
  forcas: forcasCount.value,
}))
const finalizando = ref(false)
const showModalFinalizar = ref(false)

function confirmarFinalizar() {
  finalizando.value = true
  router.patch(route('avaliacoes.finalizar', g(av.value, 'ID', 'id')), {}, {
    onFinish: () => {
      finalizando.value = false
      showModalFinalizar.value = false
    },
  })
}
</script>

<template>
  <Head :title="`Avaliação #${g(av, 'ID', 'id')}`" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900">
                  Avaliação #{{ g(av, 'ID', 'id') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                  {{ g(av, 'NOMFUN', 'nomfun') }} — Matrícula {{ g(av, 'NUMCAD', 'numcad') }}
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                  :class="(g(av, 'STATUS', 'status')) === 'FINALIZADA'
                    ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                    : 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20'"
                >
                  {{ g(av, 'STATUS', 'status') }}
                </span>

                <Link
                  v-if="canEdit"
                  :href="route('avaliacoes.edit', g(av, 'ID', 'id'))"
                  class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-white px-4 py-2 text-sm font-semibold text-brand-700 shadow-sm hover:bg-brand-50 transition"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                  </svg>
                  Editar
                </Link>

                <button
                  v-if="isRascunho"
                  type="button"
                  @click="showModalFinalizar = true"
                  :disabled="finalizando"
                  class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 disabled:opacity-50 transition"
                >
                  <svg v-if="finalizando" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                  </svg>
                  Finalizar
                </button>

                <Link
                  :href="route('avaliacoes.index')"
                  class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
                >
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                  </svg>
                  Voltar
                </Link>
              </div>
            </div>

            <!-- Abas -->
            <div class="border-b border-gray-200">
              <nav class="-mb-px flex flex-wrap gap-1">
                <button
                  v-for="tab in tabs"
                  :key="tab.key"
                  type="button"
                  @click="activeTab = tab.key"
                  class="inline-flex items-center gap-1.5 rounded-t-lg px-4 py-2.5 text-sm font-semibold transition"
                  :class="activeTab === tab.key
                    ? 'border-b-2 border-brand-600 text-brand-600 bg-brand-50/50'
                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                >
                  {{ tab.label }}
                  <span
                    v-if="tab.key !== 'geral' && tabCounts[tab.key] !== undefined"
                    class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-bold"
                    :class="tabCounts[tab.key] > 0
                      ? 'bg-green-100 text-green-700'
                      : 'bg-gray-100 text-gray-400'"
                  >
                    {{ tabCounts[tab.key] }}
                  </span>
                </button>
              </nav>
            </div>

            <!-- ==================== GERAL ==================== -->
            <div v-show="activeTab === 'geral'" class="mt-6 space-y-4">
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                  <span class="block text-xs font-medium text-gray-500">Colaborador</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ g(av, 'NOMFUN', 'nomfun') }}</span>
                </div>
                <div>
                  <span class="block text-xs font-medium text-gray-500">Matrícula</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ g(av, 'NUMCAD', 'numcad') }}</span>
                </div>
                <div>
                  <span class="block text-xs font-medium text-gray-500">Data da Avaliação</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ fmtDate(g(av, 'DATA_AVALIACAO', 'data_avaliacao')) }}</span>
                </div>
                <div>
                  <span class="block text-xs font-medium text-gray-500">Status</span>
                  <span
                    class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                    :class="(g(av, 'STATUS', 'status')) === 'FINALIZADA'
                      ? 'bg-green-50 text-green-700 ring-1 ring-green-600/20'
                      : 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20'"
                  >
                    {{ g(av, 'STATUS', 'status') }}
                  </span>
                </div>
                <div>
                  <span class="block text-xs font-medium text-gray-500">Criado em</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ fmtDate(g(av, 'CREATED_AT', 'created_at')) }}</span>
                </div>
                <div>
                  <span class="block text-xs font-medium text-gray-500">Avaliador</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ g(av, 'AVALIADOR_NOME', 'avaliador_nome') ?? '—' }}</span>
                </div>
                <div v-if="g(av, 'STATUS', 'status') === 'FINALIZADA' && g(av, 'FINALIZADO_EM', 'finalizado_em')">
                  <span class="block text-xs font-medium text-gray-500">Finalizado em</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">{{ fmtDate(g(av, 'FINALIZADO_EM', 'finalizado_em')) }}</span>
                </div>
              </div>
              <div v-if="g(av, 'OBSERVACAO', 'observacao')">
                <span class="block text-xs font-medium text-gray-500">Observação</span>
                <p class="mt-1 text-sm text-gray-700">{{ g(av, 'OBSERVACAO', 'observacao') }}</p>
              </div>
            </div>

            <!-- ==================== IFP ==================== -->
            <div v-show="activeTab === 'ifp'" class="mt-6">
              <div v-if="ifpCount > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(label, key) in ifpLabels" :key="key">
                  <span class="block text-xs font-medium text-gray-500">{{ label }}</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">
                    {{ fmtDecimal(g(ifp, key, key.toLowerCase())) }}
                  </span>
                </div>
              </div>
              <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9.75m0 0 2.25 2.25M9.75 15l2.25-2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-400">Nenhum dado preenchido nesta seção.</p>
                <Link v-if="canEdit" :href="route('avaliacoes.edit', g(av, 'ID', 'id'))" class="mt-2 text-sm font-semibold text-brand-600 hover:text-brand-700">Preencher agora</Link>
              </div>
            </div>

            <!-- ==================== PROFILER ==================== -->
            <div v-show="activeTab === 'profiler'" class="mt-6 space-y-6">
              <template v-if="profilerCount > 0">
                <!-- Percentuais -->
                <div>
                  <h3 class="mb-3 text-sm font-semibold text-gray-800">Perfis (%)</h3>
                  <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div v-for="(label, key) in profilerPctLabels" :key="key">
                      <span class="block text-xs font-medium text-gray-500">{{ label }}</span>
                      <span class="mt-1 block text-sm font-semibold text-gray-900">
                        {{ fmtDecimal(g(profiler, key, key.toLowerCase())) }} %
                      </span>
                    </div>
                  </div>
                </div>
                <!-- Níveis -->
                <div>
                  <h3 class="mb-3 text-sm font-semibold text-gray-800">Indicadores de Nível</h3>
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(label, key) in profilerNivelKeys" :key="key">
                      <span class="block text-xs font-medium text-gray-500">{{ label }}</span>
                      <span class="mt-1 block text-sm font-semibold text-gray-900">
                        {{ getNivelDescricao(g(profiler, key, key.toLowerCase())) }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>
              <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9.75m0 0 2.25 2.25M9.75 15l2.25-2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-400">Nenhum dado preenchido nesta seção.</p>
                <Link v-if="canEdit" :href="route('avaliacoes.edit', g(av, 'ID', 'id'))" class="mt-2 text-sm font-semibold text-brand-600 hover:text-brand-700">Preencher agora</Link>
              </div>
            </div>

            <!-- ==================== ÂNCORAS ==================== -->
            <div v-show="activeTab === 'ancoras'" class="mt-6">
              <div v-if="ancorasCount > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="tipo in ancoraTipos" :key="tipo.ID ?? tipo.id">
                  <span class="block text-xs font-medium text-gray-500">{{ tipo.NOME ?? tipo.nome }}</span>
                  <span class="mt-1 block text-sm font-semibold text-gray-900">
                    {{ getAncoraValor(tipo.ID ?? tipo.id) }}
                  </span>
                </div>
              </div>
              <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9.75m0 0 2.25 2.25M9.75 15l2.25-2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-400">Nenhum dado preenchido nesta seção.</p>
                <Link v-if="canEdit" :href="route('avaliacoes.edit', g(av, 'ID', 'id'))" class="mt-2 text-sm font-semibold text-brand-600 hover:text-brand-700">Preencher agora</Link>
              </div>
            </div>

            <!-- ==================== FORÇAS PESSOAIS ==================== -->
            <div v-show="activeTab === 'forcas'" class="mt-6 space-y-6">
              <template v-if="forcasCount > 0">
                <div
                  v-for="virtude in virtudes"
                  :key="virtude.ID ?? virtude.id"
                  class="rounded-lg border border-gray-200 p-4"
                >
                  <h3 class="mb-3 text-sm font-semibold text-brand-700">{{ virtude.NOME ?? virtude.nome }}</h3>
                  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="forca in virtude.forcas" :key="forca.ID ?? forca.id">
                      <span class="block text-xs font-medium text-gray-500">{{ forca.NOME ?? forca.nome }}</span>
                      <span class="mt-1 block text-sm font-semibold text-gray-900">
                        {{ getForcaValor(forca.ID ?? forca.id) }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>
              <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9.75m0 0 2.25 2.25M9.75 15l2.25-2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="mt-3 text-sm font-medium text-gray-400">Nenhum dado preenchido nesta seção.</p>
                <Link v-if="canEdit" :href="route('avaliacoes.edit', g(av, 'ID', 'id'))" class="mt-2 text-sm font-semibold text-brand-600 hover:text-brand-700">Preencher agora</Link>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- Modal Finalizar -->
    <ConfirmModal
      :show="showModalFinalizar"
      title="Finalizar avaliação"
      :message="`Tem certeza que deseja finalizar a avaliação de ${g(av, 'NOMFUN', 'nomfun')}? Após finalizada, apenas usuários com perfil Admin poderão editá-la.`"
      confirm-label="Sim, finalizar"
      variant="success"
      @confirm="confirmarFinalizar"
      @cancel="showModalFinalizar = false"
    />
  </AuthenticatedLayout>
</template>
