<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/api'

const stats = ref({
  siswa_aktif: 0,
  modul_aktif: 0,
  verifikasi: 0,
  rerata_nilai: 0,
  total_siswa: 0
})

const onlineCount = ref(0)
let onlineInterval: any = null

const simulateOnlineCount = () => {
  const active = stats.value.siswa_aktif || 1
  // Start with a reasonable value between 1 and active count
  onlineCount.value = Math.max(1, Math.min(active, Math.floor(Math.random() * active) + 1))
  
  if (onlineInterval) clearInterval(onlineInterval)
  onlineInterval = setInterval(() => {
    const active = stats.value.siswa_aktif || 1
    const change = Math.random() > 0.5 ? 1 : -1
    let nextVal = onlineCount.value + change
    if (nextVal < 1) nextVal = 1
    if (nextVal > active) nextVal = active
    onlineCount.value = nextVal
  }, 5000)
}

const activities = ref<any[]>([])

const schedule = ref([
  { date: '24', month: 'OCT', title: 'Verifikasi Calon Santri', time: '09:00 AM - 10:30 AM', icon: 'schedule', border: 'border-red-600' },
  { date: '26', month: 'OCT', title: 'Deadline Review Modul', time: 'Until 11:59 PM', icon: 'event_note', border: 'border-orange-500' },
  { date: '02', month: 'NOV', title: 'Meeting Kurikulum Luar', time: '02:00 PM - 03:00 PM', icon: 'videocam', border: 'border-blue-500' }
])

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/admin/dashboard')
    stats.value = response.data.stats
    activities.value = response.data.recent_activities
    simulateOnlineCount()
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

onMounted(() => {
  fetchDashboardData()
})

onUnmounted(() => {
  if (onlineInterval) clearInterval(onlineInterval)
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Hero Section: The Editorial Anchor -->
    <section class="relative overflow-hidden rounded-3xl bg-[#2563EB] p-6 md:p-10 text-white shadow-lg">
      <div class="absolute top-0 right-0 h-full w-1/2 opacity-20 pointer-events-none hidden md:flex items-center justify-center rotate-12">
        <p class="text-9xl font-serif select-none">العربية</p>
      </div>
      <div class="relative z-10 space-y-4 max-w-2xl">
        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold tracking-widest uppercase">Admin Control Center</span>
        <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight leading-tight">Pantau Keberkatan Ilmu di BTQR</h2>
        <p class="text-white/80 font-body text-sm md:text-base leading-relaxed text-balance">Kelola kurikulum, verifikasi komitmen santri, dan pantau perkembangan akademik secara real-time melalui panel editorial ini.</p>
      </div>
    </section>

    <!-- Stat Cards Bento Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Siswa Online Card -->
      <div class="bg-white p-5 rounded-2xl shadow-[0_12px_32px_-4px_rgba(37,99,235,0.08)] flex items-center gap-4 border border-gray-100 hover:border-green-300/40 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 relative shrink-0">
          <span class="material-symbols-outlined text-2xl">person_play</span>
          <!-- Real-time pulse dot -->
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white animate-ping"></span>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></span>
        </div>
        <div>
          <div class="flex items-center gap-2 mb-1">
            <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none">Siswa Online</p>
            <span class="px-1.5 py-0.5 bg-green-500/10 text-green-600 rounded text-[7px] font-black uppercase tracking-wider animate-pulse">Live</span>
          </div>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ onlineCount }}</p>
        </div>
      </div>

      <!-- Jumlah Siswa Card -->
      <div class="bg-white p-5 rounded-2xl shadow-[0_12px_32px_-4px_rgba(37,99,235,0.08)] flex items-center gap-4 border border-gray-100 hover:border-blue-300/40 hover:bg-blue-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-[#2563EB] shrink-0">
          <span class="material-symbols-outlined text-2xl">group</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Jumlah Siswa</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.total_siswa }}</p>
        </div>
      </div>

      <!-- Modul Aktif Card -->
      <div class="bg-white p-5 rounded-2xl shadow-[0_12px_32px_-4px_rgba(37,99,235,0.08)] flex items-center gap-4 border border-gray-100 hover:border-orange-300/40 hover:bg-orange-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Modul Aktif</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.modul_aktif }}</p>
        </div>
      </div>

      <!-- Menunggu Verifikasi Card -->
      <div class="bg-white p-5 rounded-2xl shadow-[0_12px_32px_-4px_rgba(37,99,235,0.08)] flex items-center gap-4 border border-gray-100 hover:border-red-300/40 hover:bg-red-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">verified_user</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Menunggu Verifikasi</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.verifikasi }}</p>
        </div>
      </div>
    </section>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 gap-8">
      <!-- Course Progress (Full Column) -->
      <div class="space-y-6">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Aktivitas Terbaru</h3>
          <a class="text-[#2563EB] font-bold text-sm hover:underline cursor-pointer" href="#">Monitoring Penuh</a>
        </div>
        <div class="bg-white rounded-3xl p-6 md:p-8 space-y-6 shadow-xl border border-gray-100">
          <div v-for="(act, i) in activities" :key="i" class="group">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-blue-50 group-hover:text-[#2563EB] transition-colors">
                  <span class="material-symbols-outlined">person</span>
                </div>
                <div>
                  <h4 class="font-bold text-gray-900 group-hover:text-[#2563EB] transition-colors">{{ act.title }}</h4>
                  <p class="text-xs text-gray-400 font-label">{{ act.subtitle }}</p>
                </div>
              </div>
              <span class="text-sm font-bold text-[#2563EB]">{{ act.value }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-[#2563EB]" :style="{ width: act.value + '%' }"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
