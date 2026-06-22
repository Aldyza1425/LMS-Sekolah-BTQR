<script setup lang="ts">
import { useToast } from '@/composables/useToast'
const { toasts, icons, remove } = useToast()

const colorMap = {
  success: {
    bg: 'bg-white border-l-4 border-green-500',
    icon: 'text-green-500',
    progress: 'bg-green-500',
    title: 'text-green-700'
  },
  error: {
    bg: 'bg-white border-l-4 border-red-500',
    icon: 'text-red-500',
    progress: 'bg-red-500',
    title: 'text-red-700'
  },
  warning: {
    bg: 'bg-white border-l-4 border-amber-400',
    icon: 'text-amber-500',
    progress: 'bg-amber-400',
    title: 'text-amber-700'
  },
  info: {
    bg: 'bg-white border-l-4 border-blue-500',
    icon: 'text-blue-500',
    progress: 'bg-blue-500',
    title: 'text-blue-700'
  }
}

const titleMap = {
  success: 'Berhasil',
  error: 'Gagal',
  warning: 'Perhatian',
  info: 'Informasi'
}
</script>

<template>
  <!-- Toast Container: Fixed, top-right -->
  <Teleport to="body">
    <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
      <TransitionGroup
        name="toast"
        tag="div"
        class="flex flex-col gap-3"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex flex-col overflow-hidden rounded-lg shadow-2xl shadow-black/10"
          :class="colorMap[toast.type].bg"
        >
          <!-- Main Content -->
          <div class="flex items-start gap-4 p-4 pr-5">
            <!-- Icon -->
            <div class="shrink-0 mt-0.5">
              <span
                class="material-symbols-outlined text-2xl"
                :class="colorMap[toast.type].icon"
              >
                {{ icons[toast.type] }}
              </span>
            </div>

            <!-- Text -->
            <div class="flex-1 min-w-0">
              <p class="text-[11px] font-black uppercase tracking-widest mb-0.5" :class="colorMap[toast.type].title">
                {{ titleMap[toast.type] }}
              </p>
              <p class="text-sm font-medium text-gray-700 leading-relaxed break-words">
                {{ toast.message }}
              </p>
            </div>

            <!-- Close Button -->
            <button
              @click="remove(toast.id)"
              class="shrink-0 p-1 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-600"
            >
              <span class="material-symbols-outlined text-base">close</span>
            </button>
          </div>

          <!-- Progress Bar -->
          <div
            class="h-1 w-full"
            :class="colorMap[toast.type].progress"
            :style="{ 
              animation: `shrink ${toast.duration ?? 3500}ms linear forwards`
            }"
          ></div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
/* Slide in from right */
.toast-enter-active {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-leave-active {
  transition: all 0.25s ease-in;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(110%);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(110%);
}

/* Progress bar shrink animation */
@keyframes shrink {
  from { width: 100%; }
  to { width: 0%; }
}
</style>
