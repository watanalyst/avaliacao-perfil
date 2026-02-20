<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'

const form = useForm({
  name: '',
  numcad: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
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

  // 🔥 se já selecionou alguém, NÃO mostra mensagem
  if (form.numcad && form.name) return false

  return !loading.value && q.length >= 2 && results.value.length === 0
})

function handleClickOutside(event) {
  if (wrapper.value && !wrapper.value.contains(event.target)) {
    clearResults()
  }
}

function handleEsc(event) {
  if (event.key === 'Escape') {
    clearResults()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEsc)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEsc)
})

function clearResults() {
  results.value = []
}

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

  if (!qSearch) {
    clearSelection()
    clearResults()
    return
  }

  if (selectedText.value && qSearch !== selectedText.value) {
    clearSelection()
  }

  if (qSearch.length < 2 && !/^\d+$/.test(qSearch)) {
    clearResults()
    return
  }

  timer = setTimeout(async () => {
    loading.value = true
    try {
      const url =
        route('colaboradores.index') +
        `?q=${encodeURIComponent(qSearch)}`

      const resp = await fetch(url, {
        headers: { Accept: 'application/json' },
      })

      if (!resp.ok) {
        clearResults()
        return
      }

      results.value = await resp.json()
    } finally {
      loading.value = false
    }
  }, 250)
}
</script>

<template>
  <GuestLayout>
    <Head title="Registrar" />

    <form @submit.prevent="submit">
      <!-- Colaborador -->
      <div>
        <InputLabel for="search" value="Colaborador (Senior)" />

        <!-- 🔥 AQUI estava faltando o ref -->
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
            required
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
              <span class="font-semibold">
                {{ r.NUMCAD ?? r.numcad }}
              </span>
              <span class="text-gray-600">
                - {{ r.NOMFUN ?? r.nomfun }}
              </span>
            </button>
          </div>

          <div
            v-if="showNoResults"
            class="mt-2 text-sm text-gray-500"
          >
            Nenhum colaborador encontrado.
          </div>
        </div>

        <InputError class="mt-2" :message="form.errors.name" />

      </div>

      <!-- Email -->
      <div class="mt-4">
        <InputLabel for="email" value="E-mail" />

        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autocomplete="username"
        />

        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <!-- Senha -->
      <div class="mt-4">
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
      <div class="mt-4">
        <InputLabel
          for="password_confirmation"
          value="Confirmar senha"
        />

        <TextInput
          id="password_confirmation"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password_confirmation"
          required
          autocomplete="new-password"
        />

        <InputError
          class="mt-2"
          :message="form.errors.password_confirmation"
        />
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link
          :href="route('login')"
          class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
        >
          Já possui cadastro?
        </Link>

        <PrimaryButton
          class="ms-4"
          :disabled="
            form.processing || !form.name || !form.numcad
          "
        >
          Registrar
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
