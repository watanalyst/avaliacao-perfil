<script setup>
import Modal from './Modal.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: 'Confirmar ação' },
  message: { type: String, default: '' },
  confirmLabel: { type: String, default: 'Confirmar' },
  cancelLabel: { type: String, default: 'Cancelar' },
  variant: { type: String, default: 'danger' }, // danger | success
})

const emit = defineEmits(['confirm', 'cancel'])

const variantClasses = {
  danger: {
    bar: 'bg-red-500',
    icon: 'bg-red-100 text-red-600',
    button: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
  },
  success: {
    bar: 'bg-green-500',
    icon: 'bg-green-100 text-green-600',
    button: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
  },
}
</script>

<template>
  <Modal :show="show" max-width="md" @close="emit('cancel')">
    <div class="h-1.5 rounded-t-lg" :class="variantClasses[variant].bar"></div>
    <div class="p-6">
      <div class="flex items-start gap-4">
        <!-- Ícone -->
        <div
          class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
          :class="variantClasses[variant].icon"
        >
          <!-- Danger: exclamation -->
          <svg v-if="variant === 'danger'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
          <!-- Success: check -->
          <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
        </div>

        <!-- Conteúdo -->
        <div class="flex-1">
          <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
          <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
        </div>
      </div>

      <!-- Botões -->
      <div class="mt-6 flex justify-end gap-3">
        <button
          type="button"
          @click="emit('cancel')"
          class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition"
        >
          {{ cancelLabel }}
        </button>
        <button
          type="button"
          @click="emit('confirm')"
          class="rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2"
          :class="variantClasses[variant].button"
        >
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </Modal>
</template>
