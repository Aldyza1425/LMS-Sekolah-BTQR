<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import api from '@/api'

const props = defineProps({
  themeColor: {
    type: String,
    default: '#006D3E' // Default admin blue
  },
  hoverColorClass: {
    type: String,
    default: 'hover:text-[#006D3E]'
  },
  badgeColorClass: {
    type: String,
    default: 'bg-red-600'
  }
})

const notifications = ref<any[]>([])
const isOpen = ref(false)
const bellRef = ref<HTMLElement | null>(null)

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.is_read).length
})

const fetchNotifications = async () => {
  try {
    const res = await api.get('/notifications')
    if (res.data.success) {
      notifications.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to fetch notifications:', err)
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    fetchNotifications()
  }
}

const markAsRead = async (id: number) => {
  try {
    const res = await api.post(`/notifications/${id}/read`)
    if (res.data.success) {
      const notif = notifications.value.find(n => n.id === id)
      if (notif) notif.is_read = true
    }
  } catch (err) {
    console.error('Failed to mark notification as read:', err)
  }
}

const markAllRead = async () => {
  try {
    const res = await api.post('/notifications/read-all')
    if (res.data.success) {
      notifications.value.forEach(n => n.is_read = true)
    }
  } catch (err) {
    console.error('Failed to mark all as read:', err)
  }
}

const handleClickOutside = (e: MouseEvent) => {
  if (bellRef.value && !bellRef.value.contains(e.target as Node)) {
    isOpen.value = false
  }
}

const timeAgo = (dateStr: string) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffMs = now.getTime() - date.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  
  if (diffMins < 1) return 'Baru saja'
  if (diffMins < 60) return `${diffMins} menit lalu`
  
  const diffHours = Math.floor(diffMins / 60)
  if (diffHours < 24) return `${diffHours} jam lalu`
  
  const diffDays = Math.floor(diffHours / 24)
  return `${diffDays} hari lalu`
}

let pollInterval: any = null

onMounted(() => {
  fetchNotifications()
  document.addEventListener('click', handleClickOutside)
  
  // Poll every 30 seconds for live feel
  pollInterval = setInterval(fetchNotifications, 30000)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  if (pollInterval) clearInterval(pollInterval)
})
</script>

<template>
  <div class="relative inline-block text-left" ref="bellRef">
    <!-- Bell Icon Button -->
    <button 
      @click="toggleDropdown"
      class="text-gray-400 transition-colors relative p-2 rounded-xl hover:bg-gray-50 flex items-center justify-center cursor-pointer active:scale-95"
      :class="props.hoverColorClass"
    >
      <span class="material-symbols-outlined text-2xl" :style="isOpen ? 'font-variation-settings: \'FILL\' 1' : ''">notifications</span>
      
      <!-- Red/Green Pulse Badge -->
      <span 
        v-if="unreadCount > 0" 
        class="absolute top-2 right-2 w-2.5 h-2.5 rounded-lg border-2 border-white animate-ping"
        :class="props.badgeColorClass"
      ></span>
      <span 
        v-if="unreadCount > 0" 
        class="absolute top-2 right-2 w-2.5 h-2.5 rounded-lg border-2 border-white"
        :class="props.badgeColorClass"
      ></span>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95 translate-y-2"
      enter-to-class="transform opacity-100 scale-100 translate-y-0"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100 translate-y-0"
      leave-to-class="transform opacity-0 scale-95 translate-y-2"
    >
      <div 
        v-if="isOpen" 
        class="absolute right-0 mt-3 w-80 sm:w-96 bg-white/95 backdrop-blur-xl border border-gray-100 rounded-xl shadow-2xl shadow-gray-900/10 z-[100] overflow-hidden"
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
          <div>
            <h3 class="text-sm font-black text-gray-900">Notifikasi</h3>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{{ unreadCount }} Belum Dibaca</p>
          </div>
          <button 
            v-if="unreadCount > 0"
            @click="markAllRead"
            class="text-[10px] font-black uppercase tracking-wider hover:underline cursor-pointer"
            :style="{ color: props.themeColor }"
          >
            Tandai Semua Dibaca
          </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50/70 scrollbar-thin">
          <div v-if="notifications.length === 0" class="py-12 text-center text-gray-400">
            <span class="material-symbols-outlined text-4xl text-gray-200 mb-2">notifications_off</span>
            <p class="text-xs font-bold">Tidak ada notifikasi baru.</p>
          </div>

          <template v-else>
            <div 
              v-for="notif in notifications" 
              :key="notif.id"
              class="px-6 py-4 flex gap-4 transition-colors hover:bg-gray-50/60 relative group"
              :class="!notif.is_read ? 'bg-blue-50/10' : ''"
            >
              <!-- Icon Indicator based on unread status -->
              <div class="shrink-0 pt-0.5">
                <div 
                  class="w-8 h-8 rounded-xl flex items-center justify-center"
                  :class="!notif.is_read ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-400'"
                  :style="!notif.is_read ? { backgroundColor: props.themeColor + '10', color: props.themeColor } : {}"
                >
                  <span class="material-symbols-outlined text-lg">{{ !notif.is_read ? 'notifications_active' : 'notifications' }}</span>
                </div>
              </div>

              <!-- Message details -->
              <div class="flex-1 space-y-1">
                <div class="flex justify-between items-start gap-2">
                  <h4 class="text-xs font-bold text-gray-900 leading-snug" :class="!notif.is_read ? 'font-black' : ''">
                    {{ notif.title }}
                  </h4>
                  <span class="text-[9px] text-gray-400 whitespace-nowrap pt-0.5">{{ timeAgo(notif.created_at) }}</span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed font-medium">
                  {{ notif.message }}
                </p>
                <div class="flex items-center gap-2 pt-1" v-if="!notif.is_read">
                  <button 
                    @click="markAsRead(notif.id)"
                    class="text-[9px] font-black uppercase tracking-wider hover:underline flex items-center gap-1 cursor-pointer"
                    :style="{ color: props.themeColor }"
                  >
                    <span class="material-symbols-outlined text-[10px]">done</span> Tandai dibaca
                  </button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #f1f1f1;
  border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #e5e5e5;
}
</style>
