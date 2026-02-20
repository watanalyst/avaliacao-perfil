<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'

const form = useForm({
  numcad: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: null,
})

const submit = () => {
  form.post(route('users.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}

/**
 * Autocomplete Colaborador (Senior)
 */
const search = ref('')
const wrapper = ref(null)
const results = ref([])
const loading = ref(false)
let timer = null

const selectedText = computed(() => {
  if (!form.numcad || !form.name) return ''
  return `${form.numcad} - ${form.name}`
})

const showNoResults = computed(() => {
  const q = search.value.trim()
  if (form.numcad && form.name) return false
  return !loading.value && q.length >= 2 && results.value.length === 0
})

function handleClickOutside(event) {
  if (wrapper.value && !wrapper.value.contains(event.target)) {
    clearResults()
  }
}

function handleEsc(event) {
  if (event.key === 'Escape') clearResults()
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEsc)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEsc)
})

function clearResults() { results.value = [] }

function clearSelection() {
  form.numcad = ''
  form.name = ''
}

function selectColaborador(row) {
  const numcad = row.NUMCAD ?? row.numcad ?? ''
  const nomfun = (row.NOMFUN ?? row.nomfun ?? '').toString().toUpperCase()

  form.numcad = numcad
  form.name = nomfun

  search.value = `${numcad} - ${nomfun}`
  clearResults()
}

function onSearch() {
  clearTimeout(timer)

  const raw = search.value || ''
  const q = raw.toUpperCase()
  const qSearch = q.trim()

  search.value = q

  if (!qSearch) { clearSelection(); clearResults(); return }
  if (selectedText.value && qSearch !== selectedText.value) { clearSelection() }
  if (qSearch.length < 2 && !/^\d+$/.test(qSearch)) { clearResults(); return }

  timer = setTimeout(async () => {
    loading.value = true
    try {
      const url = route('colaboradores.index') + `?q=${encodeURIComponent(qSearch)}`
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
  <Head title="Novo Usuário" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Título + Voltar -->
            <div class="mb-6 flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-900">Novo Usuário</h2>
              <Link
                :href="route('users.index')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Voltar
              </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

              <!-- Colaborador autocomplete -->
              <div>
                <InputLabel for="search" value="Colaborador (Senior)" />

                <div ref="wrapper" class="relative">
                  <TextInput
                    id="search"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    v-model="search"
                    @input="onSearch"
                    @blur="() => setTimeout(clearResults, 150)"
                    placeholder="Digite matrícula ou nome..."
                    autocomplete="off"
                    spellcheck="false"
                  />

                  <div v-if="loading" class="mt-1 text-sm text-gray-500">
                    Buscando...
                  </div>

                  <div
                    v-if="results.length"
                    class="absolute z-10 mt-2 w-full overflow-hidden rounded-md border bg-white shadow-lg"
                  >
                    <button
                      v-for="r in results"
                      :key="r.NUMCAD ?? r.numcad"
                      type="button"
                      class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50"
                      @click="selectColaborador(r)"
                    >
                      <span class="font-semibold">{{ r.NUMCAD ?? r.numcad }}</span>
                      <span class="text-gray-600"> - {{ r.NOMFUN ?? r.nomfun }}</span>
                    </button>
                  </div>

                  <div v-if="showNoResults" class="mt-2 text-sm text-gray-500">
                    Nenhum colaborador encontrado.
                  </div>
                </div>

                <div v-if="selectedText" class="mt-1 text-xs text-gray-600">
                  Selecionado: <span class="font-semibold">{{ selectedText }}</span>
                </div>

                <InputError class="mt-2" :message="form.errors.numcad" />
              </div>

              <!-- E-mail -->
              <div>
                <InputLabel for="email" value="E-mail" />
                <TextInput
                  id="email"
                  type="email"
                  class="mt-1 block w-full"
                  v-model="form.email"
                  required
                  autocomplete="off"
                />
                <InputError class="mt-2" :message="form.errors.email" />
              </div>

              <!-- Perfil -->
              <div>
                <InputLabel for="role" value="Perfil" />
                <select
                  id="role"
                  v-model="form.role"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                  :class="form.role === null ? 'text-gray-400' : 'text-gray-900'"
                >
                  <option :value="null" class="text-gray-400">Selecione...</option>
                  <option value="AVALIADOR" class="text-gray-900">Avaliador</option>
                  <option value="RH" class="text-gray-900">Avaliador / Admin</option>
                </select>
                <InputError class="mt-2" :message="form.errors.role" />
              </div>

              <!-- Senha -->
              <div>
                <InputLabel for="password" value="Senha" />
                <TextInput
                  id="password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <!-- Confirmar Senha -->
              <div>
                <InputLabel for="password_confirmation" value="Confirmar Senha" />
                <TextInput
                  id="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password_confirmation"
                  required
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
              </div>

              <!-- Submit -->
              <div class="flex items-center gap-3">
                <PrimaryButton
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing || !form.numcad || !form.name"
                >
                  Criar Usuário
                </PrimaryButton>
                <span v-if="form.processing" class="text-sm text-gray-500">Salvando...</span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
