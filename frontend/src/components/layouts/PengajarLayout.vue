<script setup lang="ts">
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { computed, ref, onMounted } from 'vue'
import api from '@/api'
import ToastNotification from '@/components/ToastNotification.vue'
import NotificationBell from '@/components/NotificationBell.vue'

const route = useRoute()
const router = useRouter()
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
    localStorage.removeItem('btqr_role')
    router.push('/login')
  }
}

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
      class="bg-[#006D3E] h-screen w-60 fixed left-0 top-0 flex flex-col py-6 px-5 z-50 overflow-y-auto font-semibold shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] transition-transform duration-300 md:translate-x-0"
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="mb-10 px-4 flex justify-between items-center">
        <div>
          <h1 class="text-white font-['Plus_Jakarta_Sans'] italic text-xl">BTQR</h1>
          <p class="text-white/60 text-[10px] font-normal mt-0.5 uppercase tracking-widest">Portal Pengajar</p>
        </div>
        <button @click="isSidebarOpen = false" class="text-white md:hidden">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
 
      <nav class="flex-1 space-y-2">
        <!-- Dashboard -->
        <RouterLink 
          to="/pengajar" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all group text-sm"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'pengajar.dashboard' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'pengajar.dashboard' ? 'font-variation-settings: \'FILL\' 1' : ''">dashboard</span>
          <span>Dashboard</span>
        </RouterLink>
 
        <!-- Modul -->
        <RouterLink 
          to="/pengajar/modul" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all group text-sm"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'pengajar.modul' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'pengajar.modul' ? 'font-variation-settings: \'FILL\' 1' : ''">menu_book</span>
          <span>Modul</span>
        </RouterLink>
 
        <!-- Try Out -->
        <RouterLink 
          to="/pengajar/try-out" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'pengajar.try-out' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'pengajar.try-out' ? 'font-variation-settings: \'FILL\' 1' : ''">quiz</span>
          <span>Try Out</span>
        </RouterLink>
 
        <!-- Penilaian Siswa -->
        <RouterLink 
          to="/pengajar/penilaian" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group"
          exact-active-class="bg-white text-[#006D3E] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'pengajar.penilaian' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'pengajar.penilaian' ? 'font-variation-settings: \'FILL\' 1' : ''">grading</span>
          <span>Penilaian Siswa</span>
        </RouterLink>
 
      </nav>
 
      <div class="mt-auto pt-6 border-t border-white/10 space-y-1 text-white">
        <!-- Profile -->
        <RouterLink 
          to="/pengajar/profile" 
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all"
          :class="route.path.startsWith('/pengajar/profile') ? 'bg-white/15 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white'"
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
      </div>
    </aside>
 
    <!-- Main Canvas -->
    <main class="min-h-screen bg-white transition-all duration-300 md:ml-60">
      <!-- TopAppBar -->
      <header class="flex justify-between items-center w-full px-4 md:px-8 py-3 bg-white sticky top-0 z-40 border-b border-gray-100 md:border-none">
        <div class="flex items-center gap-3 md:gap-4 text-outline">
          <button @click="toggleSidebar" class="p-2 -ml-2 text-[#006D3E] md:hidden">
            <span class="material-symbols-outlined">menu</span>
          </button>
        </div>
        <div class="flex items-center gap-4 md:gap-6">
          <NotificationBell 
            themeColor="#006D3E" 
            hoverColorClass="hover:text-[#006D3E]" 
            badgeColorClass="bg-red-600" 
          />
          <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-bold text-gray-900 leading-none">{{ user?.name || 'Memuat...' }}</p>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1">{{ user?.role || 'Pengajar' }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-[#006D3E] shadow-sm overflow-hidden">
               <span class="material-symbols-outlined">shield_person</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Content Canvas -->
      <div class="p-4 md:p-8 max-w-7xl mx-auto">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
