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
    // Jika gagal fetch user (token kadaluwarsa), lempar ke login
    // router.push('/lms/login')
  }
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch (e) {
    console.error('Logout error:', e)
  } finally {
    localStorage.removeItem('btqr_token')
    router.push('/lms/login')
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
      class="bg-[#2563EB] h-screen w-60 fixed left-0 top-0 flex flex-col py-6 px-5 z-50 overflow-y-auto font-semibold shadow-[0_12px_32px_-4px_rgba(37,99,235,0.08)] transition-transform duration-300 md:translate-x-0"
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="mb-10 px-4 flex justify-between items-center">
        <div>
          <h1 class="text-white font-['Plus_Jakarta_Sans'] italic text-xl">BTQR</h1>
          <p class="text-white/60 text-[10px] font-normal mt-0.5 uppercase tracking-widest">Admin Portal</p>
        </div>
        <button @click="isSidebarOpen = false" class="text-white md:hidden">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <nav class="flex-1 space-y-2">
        <!-- Dashboard -->
        <RouterLink 
          to="/lms/admin" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-2.5 rounded-full transition-all group text-sm"
          exact-active-class="bg-white text-[#2563EB] md:-mr-4 shadow-sm"
          :class="currentRouteName !== 'admin.dashboard' ? 'text-white/80 hover:bg-white/10 hover:text-white' : ''"
        >
          <span class="material-symbols-outlined" :style="currentRouteName === 'admin.dashboard' ? 'font-variation-settings: \'FILL\' 1' : ''">dashboard</span>
          <span>Dashboard</span>
        </RouterLink>


        <!-- Pengajar -->
        <RouterLink 
          to="/lms/admin/pengajar" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-full transition-all group"
          :class="route.path.startsWith('/lms/admin/pengajar') ? 'bg-white text-[#2563EB] md:-mr-4 shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined" :style="route.path.startsWith('/lms/admin/pengajar') ? 'font-variation-settings: \'FILL\' 1' : ''">badge</span>
          <span>Pengajar</span>
        </RouterLink>

        <!-- Siswa -->
        <RouterLink 
          to="/lms/admin/siswa" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-3 rounded-full transition-all group"
          :class="route.path.startsWith('/lms/admin/siswa') ? 'bg-white text-[#2563EB] md:-mr-4 shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined" :style="route.path.startsWith('/lms/admin/siswa') ? 'font-variation-settings: \'FILL\' 1' : ''">person</span>
          <span>Siswa</span>
        </RouterLink>

      </nav>

      <div class="mt-auto pt-6 border-t border-white/10 space-y-1 text-white">
        <RouterLink 
          to="/lms/admin/settings" 
          @click="isSidebarOpen = false"
          class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm transition-all"
          :class="route.path === '/lms/admin/settings' ? 'bg-white/15 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined text-xl">settings</span>
          <span>Settings</span>
        </RouterLink>
        <button 
          @click="handleLogout"
          class="w-full flex items-center gap-3 px-4 py-2.5 rounded-full text-white/80 text-sm transition-all hover:bg-white/10 hover:text-white"
        >
          <span class="material-symbols-outlined text-xl">logout</span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Canvas -->
    <main class="min-h-screen bg-white transition-all duration-300 md:ml-60">
      <!-- TopAppBar -->
      <header class="flex justify-between items-center w-full px-4 md:px-8 py-3 bg-white sticky top-0 z-40 border-b border-gray-100 md:border-none">
        <div class="flex items-center gap-3 md:gap-4 text-outline">
          <button @click="toggleSidebar" class="p-2 -ml-2 text-[#2563EB] md:hidden">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <span class="text-xs font-bold uppercase tracking-widest opacity-40 hidden sm:inline">Admin</span>
          <span class="material-symbols-outlined text-xs hidden sm:inline">chevron_right</span>
          <span class="text-[#2563EB] font-bold border-b-2 border-[#2563EB] capitalize text-sm">{{ route.path.split('/').pop() || 'Dashboard' }}</span>
        </div>
        <div class="flex items-center gap-4 md:gap-6">
          <NotificationBell 
            themeColor="#2563EB" 
            hoverColorClass="hover:text-[#2563EB]" 
            badgeColorClass="bg-red-600" 
          />
          <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-bold text-gray-900 leading-none">{{ user?.name || 'Loading...' }}</p>
              <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter mt-1">{{ user?.role || 'User' }}</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-[#2563EB] shadow-sm overflow-hidden">
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
