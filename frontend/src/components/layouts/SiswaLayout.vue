<script setup lang="ts">
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/api'
import { usePageStore } from '@/stores/page'
import ToastNotification from '@/components/ToastNotification.vue'
import NotificationBell from '@/components/NotificationBell.vue'

const route = useRoute()
const router = useRouter()
const pageStore = usePageStore()
const currentRouteName = computed(() => route.name)
const isSidebarOpen = ref(false)
const user = ref<any>(null)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const fetchUser = async () => {
  try {
    const response = await api.get('/user')
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch (e) {
    console.error('Logout error:', e)
  } finally {
    localStorage.removeItem('btqr_token')
    router.push('/login')
  }
}

watch(() => route.path, () => {
  pageStore.resetTitle()
})

onMounted(() => {
  fetchUser()
})
</script>

<template>
  <div class="min-h-screen bg-white font-['Manrope'] overflow-x-hidden">
    <ToastNotification />
    <!-- Mobile Overlay -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false"
      class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"
    ></div>

    <!-- SideNavBar -->
    <aside 
      class="bg-[#006D3E] h-screen w-64 fixed left-0 top-0 flex flex-col py-8 px-6 z-50 overflow-y-auto font-semibold shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] transition-transform duration-300 md:translate-x-0"
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="mb-10 px-4 flex justify-between items-center">
        <div>
          <h1 class="text-white font-['Plus_Jakarta_Sans'] italic text-2xl">BTQR</h1>
          <p class="text-white/60 text-xs font-normal mt-1 uppercase tracking-widest">Portal Siswa</p>
        </div>
        <button @click="isSidebarOpen = false" class="text-white md:hidden">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
 
      <nav class="flex-1 space-y-2">
        <RouterLink 
          to="/siswa" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'siswa.dashboard' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'siswa.dashboard' ? 'font-variation-settings: \'FILL\' 1' : ''">dashboard</span>
          <span>Dashboard</span>
        </RouterLink>
 
 
        <RouterLink 
          to="/siswa/modul" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'siswa.modul' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'siswa.modul' ? 'font-variation-settings: \'FILL\' 1' : ''">menu_book</span>
          <span>Modul Saya</span>
        </RouterLink>
 
        <RouterLink 
          to="/siswa/nilai" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'siswa.nilai' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'siswa.nilai' ? 'font-variation-settings: \'FILL\' 1' : ''">workspace_premium</span>
          <span>Nilai Saya</span>
        </RouterLink>
 
 
        <RouterLink 
          to="/siswa/try-out" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'siswa.try-out' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'siswa.try-out' ? 'font-variation-settings: \'FILL\' 1' : ''">quiz</span>
          <span>Try Out</span>
        </RouterLink>
      </nav>
 
      <div class="mt-auto pt-6 border-t border-white/10 space-y-1 text-white">
        <RouterLink 
          to="/siswa/profile" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all"
          :class="currentRouteName === 'siswa.profile' ? 'bg-white/15 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined text-xl">account_circle</span>
          <span>Profil Saya</span>
        </RouterLink>
        <button 
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-white/80 text-sm transition-all hover:bg-white/10 hover:text-white"
        >
          <span class="material-symbols-outlined text-xl">logout</span>
          <span>Keluar</span>
        </button>
 
        <div class="px-2 mt-4 pt-4 border-t border-white/10 flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white overflow-hidden shadow-inner">
             <span class="material-symbols-outlined">account_circle</span>
          </div>
          <div class="overflow-hidden">
            <p class="text-xs font-bold text-white truncate">{{ user?.name || 'Memuat...' }}</p>
            <p class="text-[10px] text-white/50 uppercase tracking-tighter">Siswa</p>
          </div>
        </div>
      </div>
    </aside>
 
    <!-- Main Canvas -->
    <main class="min-h-screen bg-white transition-all duration-300 md:ml-64">
      <!-- TopAppBar -->
      <header class="flex justify-between items-center w-full px-4 md:px-8 py-4 bg-white sticky top-0 z-40 border-b border-gray-100 md:border-none">
        <div class="flex items-center gap-3 md:gap-4 text-outline">
          <button @click="toggleSidebar" class="p-2 -ml-2 text-[#006D3E] md:hidden">
            <span class="material-symbols-outlined">menu</span>
          </button>
        </div>
        <div class="flex items-center gap-4 md:gap-6">
          <div class="relative flex items-center bg-gray-50 rounded-lg px-4 py-2 w-48 lg:w-64 focus-within:ring-2 focus-within:ring-[#006D3E]/20 transition-all group">
            <span class="material-symbols-outlined text-gray-400 group-focus-within:text-[#006D3E] text-lg">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-full font-medium" placeholder="Cari pelajaran..." type="text"/>
          </div>
          <NotificationBell 
            themeColor="#006D3E" 
            hoverColorClass="hover:text-[#006D3E]" 
            badgeColorClass="bg-green-700" 
          />
        </div>
      </header>

      <!-- Content Canvas -->
      <div class="p-4 md:p-8 max-w-7xl mx-auto">
        <router-view />
      </div>
    </main>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
