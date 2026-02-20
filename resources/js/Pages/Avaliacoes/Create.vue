<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
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

// Montar o form com todos os campos
const form = useForm({
  numemp: '',
  tipcol: '',
  numcad: '',
  nomfun: '',
  data_avaliacao: '',
  observacao: '',
  status: 'RASCUNHO',

  // IFP (13 fatores)
  ifp: {
    assistencia: null,
    intracepcao: null,
    afago: null,
    autonomia: null,
    deferencia: null,
    afiliacao: null,
    dominancia: null,
    desempenho: null,
    exibicao: null,
    agressao: null,
    ordem: null,
    persistencia: null,
    mudanca: null,
  },

  // Profiler (4 % + 9 níveis)
  profiler: {
    executor: null,
    planejador: null,
    analista: null,
    comunicador: null,
    energia_nivel_id: null,
    exigencia_meio_nivel_id: null,
    aproveitamento_nivel_id: null,
    moral_nivel_id: null,
    positividade_nivel_id: null,
    flexibilidade_nivel_id: null,
    amplitude_nivel_id: null,
    automotivacao_nivel_id: null,
    incitabilidade_nivel_id: null,
  },

  // Âncoras (8 tipos com valor)
  ancoras: (props.ancoraTipos || []).map(t => ({
    tipo_id: t.ID ?? t.id,
    valor: null,
  })),

  // Forças (todas as forças com valor)
  forcas: (props.virtudes || []).flatMap(v =>
    (v.forcas || []).map(f => ({
      forca_id: f.ID ?? f.id,
      valor: null,
    }))
  ),
})

// ---- Helper: converte vírgula para ponto e retorna número ----
function parseDecimal(value) {
  if (value === null || value === undefined || value === '') return null
  const str = String(value).replace(',', '.')
  const num = parseFloat(str)
  return isNaN(num) ? null : num
}

function onDecimalInput(obj, key, event) {
  // Remove tudo que não é dígito
  let digits = event.target.value.replace(/\D/g, '')
  // Remove zeros à esquerda (mantém pelo menos "0")
  digits = digits.replace(/^0+/, '') || '0'
  // Limita a 10000 centésimos = 100,00
  let cents = parseInt(digits, 10)
  if (cents > 10000) cents = 10000
  // Formata com vírgula e 2 casas decimais
  const formatted = (cents / 100).toFixed(2).replace('.', ',')
  obj[key] = formatted
  event.target.value = formatted
}

function onDecimalBlur(obj, key, event) {
  const val = obj[key]
  if (val === null || val === undefined || val === '') return
  let num = parseDecimal(val)
  if (num === null) { obj[key] = '0,00'; event.target.value = '0,00'; return }
  if (num < 0) num = 0
  if (num > 100) num = 100
  const formatted = num.toFixed(2).replace('.', ',')
  obj[key] = formatted
  event.target.value = formatted
}

function submit(status) {
  // Converter todos os campos decimais antes de enviar
  for (const key of Object.keys(form.ifp)) {
    form.ifp[key] = parseDecimal(form.ifp[key])
  }
  for (const key of ['executor', 'planejador', 'analista', 'comunicador']) {
    form.profiler[key] = parseDecimal(form.profiler[key])
  }
  for (const a of form.ancoras) {
    a.valor = parseDecimal(a.valor)
  }
  for (const f of form.forcas) {
    f.valor = parseDecimal(f.valor)
  }

  form.status = status
  form.post(route('avaliacoes.store'))
}

// ---- Labels legíveis para IFP ----
const ifpLabels = {
  assistencia: 'Assistência',
  intracepcao: 'Intracepção',
  afago: 'Afago',
  autonomia: 'Autonomia',
  deferencia: 'Deferência',
  afiliacao: 'Afiliação',
  dominancia: 'Dominância',
  desempenho: 'Desempenho',
  exibicao: 'Exibição',
  agressao: 'Agressão',
  ordem: 'Ordem',
  persistencia: 'Persistência',
  mudanca: 'Mudança',
}

// ---- Labels para Profiler níveis ----
const profilerNivelLabels = {
  energia_nivel_id: 'Energia',
  exigencia_meio_nivel_id: 'Exigência do Meio',
  aproveitamento_nivel_id: 'Aproveitamento',
  moral_nivel_id: 'Moral',
  positividade_nivel_id: 'Positividade',
  flexibilidade_nivel_id: 'Flexibilidade',
  amplitude_nivel_id: 'Amplitude',
  automotivacao_nivel_id: 'Automotivação',
  incitabilidade_nivel_id: 'Incitabilidade',
}

// ---- Helper para achar valor de força por ID ----
function getForcaIndex(forcaId) {
  return form.forcas.findIndex(f => f.forca_id === forcaId)
}

// ---- Autocomplete Colaborador ----
const search = ref('')
const results = ref([])
const loading = ref(false)
const bloqueado = ref(false)
const ultimaAvaliacao = ref(null)
let timer = null

const selectedText = computed(() => {
  if (!form.numcad || !form.nomfun) return ''
  return `${form.numcad} - ${form.nomfun}`
})

function clearResults() { results.value = [] }
function clearSelection() {
  form.numemp = ''; form.tipcol = ''; form.numcad = ''; form.nomfun = ''
  bloqueado.value = false
  ultimaAvaliacao.value = null
}

async function selectColaborador(row) {
  const numemp = row.NUMEMP ?? row.numemp ?? ''
  const tipcol = row.TIPCOL ?? row.tipcol ?? ''
  const numcad = row.NUMCAD ?? row.numcad ?? ''
  const nomfun = (row.NOMFUN ?? row.nomfun ?? '').toString().toUpperCase()

  form.numemp = numemp
  form.tipcol = tipcol
  form.numcad = numcad
  form.nomfun = nomfun
  search.value = `${numcad} - ${nomfun}`
  clearResults()

  // Verificar regra dos 6 meses
  try {
    const url = route('colaboradores.check') + `?numcad=${encodeURIComponent(numcad)}`
    const resp = await fetch(url, { headers: { Accept: 'application/json' } })
    if (resp.ok) {
      const data = await resp.json()
      bloqueado.value = data.bloqueado
      ultimaAvaliacao.value = data.ultima
    }
  } catch { /* silently ignore */ }
}

function onSearch() {
  clearTimeout(timer)
  const q = (search.value || '').trim().toUpperCase()
  search.value = q

  if (!q) { clearSelection(); clearResults(); return }

  if (selectedText.value && q !== selectedText.value) { clearSelection() }

  if (q.length < 2 && !/^\d+$/.test(q)) { clearResults(); return }

  timer = setTimeout(async () => {
    loading.value = true
    try {
      const url = route('colaboradores.index') + `?q=${encodeURIComponent(q)}`
      const resp = await fetch(url, { headers: { Accept: 'application/json' } })
      if (!resp.ok) { clearResults(); return }
      results.value = await resp.json()
    } finally {
      loading.value = false
    }
  }, 250)
}
</script>

<template>
  <Head title="Nova Avaliação" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Título + Voltar -->
            <div class="mb-6 flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-900">Nova Avaliação</h2>
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

            <form @submit.prevent class="space-y-6">

              <!-- Abas -->
              <div class="border-b border-gray-200">
                <nav class="-mb-px flex flex-wrap gap-1">
                  <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    @click="activeTab = tab.key"
                    class="rounded-t-lg px-4 py-2.5 text-sm font-semibold transition"
                    :class="activeTab === tab.key
                      ? 'border-b-2 border-brand-600 text-brand-600 bg-brand-50/50'
                      : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                  >
                    {{ tab.label }}
                  </button>
                </nav>
              </div>

              <!-- ==================== GERAL ==================== -->
              <div v-show="activeTab === 'geral'" class="space-y-5">
                <!-- Autocomplete Colaborador -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Colaborador</label>
                  <div class="relative mt-1">
                    <input
                      v-model="search"
                      @input="onSearch"
                      @blur="() => setTimeout(clearResults, 150)"
                      type="text"
                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 uppercase"
                      placeholder="Digite matrícula ou nome..."
                      autocomplete="off"
                      spellcheck="false"
                    />
                    <div v-if="loading" class="mt-1 text-sm text-gray-500">Buscando...</div>
                    <div
                      v-if="results.length"
                      class="absolute z-10 mt-1 w-full overflow-hidden rounded-lg border bg-white shadow-lg"
                    >
                      <button
                        v-for="r in results"
                        :key="r.NUMCAD ?? r.numcad"
                        type="button"
                        class="block w-full px-4 py-2.5 text-left text-sm hover:bg-brand-50 transition-colors"
                        @click="selectColaborador(r)"
                      >
                        <span class="font-semibold text-gray-900">{{ r.NUMCAD ?? r.numcad }}</span>
                        <span class="text-gray-600"> — {{ r.NOMFUN ?? r.nomfun }}</span>
                      </button>
                    </div>
                  </div>
                  <div v-if="form.errors.numcad" class="mt-1 text-sm text-red-600">{{ form.errors.numcad }}</div>
                  <div v-if="form.errors.nomfun" class="mt-1 text-sm text-red-600">{{ form.errors.nomfun }}</div>
                  <input type="hidden" v-model="form.numcad" />
                  <input type="hidden" v-model="form.nomfun" />
                  <div v-if="selectedText" class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    {{ selectedText }}
                  </div>

                  <!-- Aviso: colaborador bloqueado (avaliação < 6 meses) -->
                  <div v-if="bloqueado" class="mt-3 rounded-lg border border-amber-300 bg-amber-50 p-3">
                    <div class="flex items-start gap-2">
                      <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                      </svg>
                      <div>
                        <p class="text-sm font-semibold text-amber-800">Avaliação bloqueada</p>
                        <p class="mt-0.5 text-sm text-amber-700">
                          Este colaborador possui uma avaliação finalizada há menos de 6 meses
                          <span v-if="ultimaAvaliacao">({{ ultimaAvaliacao }})</span>.
                          Aguarde o prazo para criar uma nova avaliação.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Data da Avaliação</label>
                    <input
                      v-model="form.data_avaliacao"
                      type="date"
                      class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    />
                    <div v-if="form.errors.data_avaliacao" class="mt-1 text-sm text-red-600">{{ form.errors.data_avaliacao }}</div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Observação</label>
                  <textarea
                    v-model="form.observacao"
                    rows="3"
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    placeholder="Opcional"
                  ></textarea>
                  <div v-if="form.errors.observacao" class="mt-1 text-sm text-red-600">{{ form.errors.observacao }}</div>
                </div>
              </div>

              <!-- ==================== IFP ==================== -->
              <div v-show="activeTab === 'ifp'" class="space-y-4">
                <p class="text-sm text-gray-500">
                  Preencha os valores do Inventário Fatorial de Personalidade (IFP).
                </p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <div v-for="(label, key) in ifpLabels" :key="key">
                    <label class="block text-sm font-medium text-gray-700">{{ label }}</label>
                    <input
                      :value="form.ifp[key]"
                      @input="onDecimalInput(form.ifp, key, $event)"
                      @blur="onDecimalBlur(form.ifp, key, $event)"
                      type="text"
                      inputmode="decimal"
                      class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                      placeholder="0,00 %"
                    />
                    <div v-if="form.errors[`ifp.${key}`]" class="mt-1 text-sm text-red-600">
                      {{ form.errors[`ifp.${key}`] }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- ==================== PROFILER ==================== -->
              <div v-show="activeTab === 'profiler'" class="space-y-6">
                <!-- Percentuais -->
                <div>
                  <h3 class="mb-3 text-sm font-semibold text-gray-800">Perfis (%)</h3>
                  <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Executor</label>
                      <input :value="form.profiler.executor" @input="onDecimalInput(form.profiler, 'executor', $event)"
                        @blur="onDecimalBlur(form.profiler, 'executor', $event)"
                        type="text" inputmode="decimal"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="0,00 %" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Planejador</label>
                      <input :value="form.profiler.planejador" @input="onDecimalInput(form.profiler, 'planejador', $event)"
                        @blur="onDecimalBlur(form.profiler, 'planejador', $event)"
                        type="text" inputmode="decimal"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="0,00 %" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Analista</label>
                      <input :value="form.profiler.analista" @input="onDecimalInput(form.profiler, 'analista', $event)"
                        @blur="onDecimalBlur(form.profiler, 'analista', $event)"
                        type="text" inputmode="decimal"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="0,00 %" />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Comunicador</label>
                      <input :value="form.profiler.comunicador" @input="onDecimalInput(form.profiler, 'comunicador', $event)"
                        @blur="onDecimalBlur(form.profiler, 'comunicador', $event)"
                        type="text" inputmode="decimal"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="0,00 %" />
                    </div>
                  </div>
                </div>

                <!-- Níveis -->
                <div>
                  <h3 class="mb-3 text-sm font-semibold text-gray-800">Indicadores de Nível</h3>
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(label, key) in profilerNivelLabels" :key="key">
                      <label class="block text-sm font-medium text-gray-700">{{ label }}</label>
                      <select
                        v-model="form.profiler[key]"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        :class="form.profiler[key] === null ? 'text-gray-400' : 'text-gray-900'"
                      >
                        <option :value="null" class="text-gray-400">Selecione...</option>
                        <option v-for="n in niveis" :key="n.ID ?? n.id" :value="n.ID ?? n.id" class="text-gray-900">
                          {{ n.DESCRICAO ?? n.descricao }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ==================== ÂNCORAS ==================== -->
              <div v-show="activeTab === 'ancoras'" class="space-y-4">
                <p class="text-sm text-gray-500">
                  Preencha o valor para cada âncora de carreira.
                </p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  <div
                    v-for="(tipo, idx) in ancoraTipos"
                    :key="tipo.ID ?? tipo.id"
                  >
                    <label class="block text-sm font-medium text-gray-700">{{ tipo.NOME ?? tipo.nome }}</label>
                    <input
                      :value="form.ancoras[idx].valor"
                      @input="onDecimalInput(form.ancoras[idx], 'valor', $event)"
                      @blur="onDecimalBlur(form.ancoras[idx], 'valor', $event)"
                      type="text"
                      inputmode="decimal"
                      class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                      placeholder="0,00 %"
                    />
                  </div>
                </div>
              </div>

              <!-- ==================== FORÇAS PESSOAIS ==================== -->
              <div v-show="activeTab === 'forcas'" class="space-y-6">
                <p class="text-sm text-gray-500">
                  Preencha o valor para cada força pessoal, agrupadas por virtude.
                </p>
                <div
                  v-for="virtude in virtudes"
                  :key="virtude.ID ?? virtude.id"
                  class="rounded-lg border border-gray-200 p-4"
                >
                  <h3 class="mb-3 text-sm font-semibold text-brand-700">{{ virtude.NOME ?? virtude.nome }}</h3>
                  <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="forca in virtude.forcas" :key="forca.ID ?? forca.id">
                      <label class="block text-sm font-medium text-gray-700">{{ forca.NOME ?? forca.nome }}</label>
                      <input
                        :value="form.forcas[getForcaIndex(forca.ID ?? forca.id)]?.valor"
                        @input="onDecimalInput(form.forcas[getForcaIndex(forca.ID ?? forca.id)], 'valor', $event)"
                        @blur="onDecimalBlur(form.forcas[getForcaIndex(forca.ID ?? forca.id)], 'valor', $event)"
                        type="text"
                        inputmode="decimal"
                        class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        placeholder="0,00 %"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- ==================== AÇÕES ==================== -->
              <div class="flex items-center gap-3 border-t border-gray-200 pt-6">
                <button
                  type="button"
                  @click="submit('RASCUNHO')"
                  :disabled="form.processing || !form.numcad || !form.nomfun || bloqueado"
                  class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 transition"
                >
                  <svg v-if="form.processing && form.status === 'RASCUNHO'" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  Salvar Rascunho
                </button>

                <button
                  type="button"
                  @click="submit('FINALIZADA')"
                  :disabled="form.processing || !form.numcad || !form.nomfun || bloqueado"
                  class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 disabled:opacity-50 transition"
                >
                  <svg v-if="form.processing && form.status === 'FINALIZADA'" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  Finalizar Avaliação
                </button>

                <span v-if="!form.numcad || !form.nomfun" class="text-sm text-gray-400">
                  Selecione um colaborador para habilitar.
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
