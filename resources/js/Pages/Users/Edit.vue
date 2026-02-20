<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  userEdit: Object,
})

const form = useForm({
  role: props.userEdit.role,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put(route('users.update', props.userEdit.id), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <Head title="Editar Usuário" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/5">
          <div class="h-1.5 bg-gradient-to-r from-brand-600 via-brand-500 to-brand-700"></div>
          <div class="p-6">

            <!-- Título + Voltar -->
            <div class="mb-6 flex items-center justify-between">
              <h2 class="text-xl font-bold text-gray-900">Editar Usuário</h2>
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

              <!-- Colaborador (somente leitura) -->
              <div>
                <InputLabel value="Colaborador" />
                <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                  <span class="font-semibold">{{ userEdit.numcad }}</span> — {{ userEdit.name }}
                </div>
              </div>

              <!-- E-mail (somente leitura) -->
              <div>
                <InputLabel value="E-mail" />
                <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                  {{ userEdit.email }}
                </div>
              </div>

              <!-- Perfil -->
              <div>
                <InputLabel for="role" value="Perfil" />
                <select
                  id="role"
                  v-model="form.role"
                  class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-gray-900"
                >
                  <option value="AVALIADOR">Avaliador</option>
                  <option value="RH">Avaliador / Admin</option>
                </select>
                <InputError class="mt-2" :message="form.errors.role" />
              </div>

              <!-- Nova Senha (opcional) -->
              <div>
                <InputLabel for="password" value="Nova Senha (opcional)" />
                <TextInput
                  id="password"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password"
                  autocomplete="new-password"
                  placeholder="Deixe em branco para manter a atual"
                />
                <InputError class="mt-2" :message="form.errors.password" />
              </div>

              <!-- Confirmar Nova Senha -->
              <div v-if="form.password">
                <InputLabel for="password_confirmation" value="Confirmar Nova Senha" />
                <TextInput
                  id="password_confirmation"
                  type="password"
                  class="mt-1 block w-full"
                  v-model="form.password_confirmation"
                  autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
              </div>

              <!-- Submit -->
              <div class="flex items-center gap-3">
                <PrimaryButton
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Salvar Alterações
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
