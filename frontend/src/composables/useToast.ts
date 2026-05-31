import { ref } from 'vue'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface Toast {
  id: number
  type: ToastType
  message: string
  duration?: number
}

const toasts = ref<Toast[]>([])
let counter = 0

const icons: Record<ToastType, string> = {
  success: 'check_circle',
  error: 'cancel',
  warning: 'warning',
  info: 'info'
}

const add = (message: string, type: ToastType = 'info', duration = 3500) => {
  const id = ++counter
  toasts.value.push({ id, type, message, duration })
  setTimeout(() => remove(id), duration)
}

const remove = (id: number) => {
  const idx = toasts.value.findIndex(t => t.id === id)
  if (idx !== -1) toasts.value.splice(idx, 1)
}

export const useToast = () => ({
  toasts,
  icons,
  add,
  remove,
  success: (msg: string, duration?: number) => add(msg, 'success', duration),
  error:   (msg: string, duration?: number) => add(msg, 'error',   duration ?? 5000),
  warning: (msg: string, duration?: number) => add(msg, 'warning', duration),
  info:    (msg: string, duration?: number) => add(msg, 'info',    duration),
})
