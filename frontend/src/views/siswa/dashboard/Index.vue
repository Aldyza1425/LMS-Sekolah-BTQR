<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/api'

const user = ref<any>(null)
const stats = ref({
  active_courses: 0,
  completed_modules: 0,
  learning_time: '0j 0m',
  average_score: 0
})
const modulProgress = ref<any[]>([])
const schedule = ref<any[]>([])
const loading = ref(true)

const fetchDashboard = async () => {
  try {
    const [userRes, dashRes] = await Promise.all([
      api.get('/user'),
      api.get('/siswa/dashboard')
    ])
    user.value = userRes.data
    stats.value = dashRes.data.stats
    modulProgress.value = dashRes.data.modul_progress
    schedule.value = dashRes.data.schedule
  } catch (error) {
    console.error('Failed to fetch dashboard:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboard()
})
</script>

<template>
  <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500 font-['Plus_Jakarta_Sans']">

    <!-- Hero Banner -->
    <section class="relative overflow-hidden rounded-lg bg-gradient-to-br from-[#006D3E] to-[#00955A] p-8 md:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 80% 50%, white 0%, transparent 60%)"></div>
      <div class="relative z-10 space-y-2">
        <h2 class="text-2xl md:text-3xl font-extrabold leading-tight">
          Selamat Datang, {{ user?.name || 'Siswa' }}!
        </h2>
        <p class="text-white/70 text-sm max-w-md leading-relaxed">
          Lanjutkan perjalanan belajar bahasa Arab Anda. Tetap semangat dan konsisten dalam menuntut ilmu.
        </p>
      </div>
      <div class="relative z-10 hidden md:block text-right opacity-50 select-none" dir="rtl">
        <p class="text-3xl font-serif leading-relaxed tracking-widest">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
      </div>
    </section>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Mata Pelajaran Aktif -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-green-300/40 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-[#006D3E] shrink-0">
          <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Mata Pelajaran Aktif</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.active_courses }}</p>
        </div>
      </div>

      <!-- Modul Selesai -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-orange-300/40 hover:bg-orange-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Modul Selesai</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.completed_modules }}</p>
        </div>
      </div>

      <!-- Waktu Belajar -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-blue-300/40 hover:bg-blue-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">schedule</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Waktu Belajar (Jam Online)</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.learning_time }}</p>
        </div>
      </div>

      <!-- Rata-rata Nilai -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-red-300/40 hover:bg-red-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 shrink-0">
          <span class="material-symbols-outlined text-2xl">workspace_premium</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Rata-rata Nilai</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.average_score || '-' }}</p>
        </div>
      </div>
    </div>

    <!-- Main Content Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Progres Mata Pelajaran -->
      <div class="lg:col-span-2 space-y-6">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Progres Mata Pelajaran</h3>
          <RouterLink to="/siswa/modul" class="text-[#006D3E] font-bold text-xs hover:underline flex items-center gap-1">
            Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </RouterLink>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="loading" class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8 space-y-4">
          <div v-for="i in 2" :key="i" class="flex items-center gap-4 animate-pulse">
            <div class="w-16 h-12 bg-gray-100 rounded-xl flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="h-3.5 bg-gray-100 rounded w-1/2"></div>
              <div class="h-2.5 bg-gray-50 rounded w-1/3"></div>
            </div>
          </div>
        </div>

        <!-- Module Progress List -->
        <div v-else-if="modulProgress.length > 0" class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-lg border border-gray-100">
          <RouterLink
            v-for="modul in modulProgress"
            :key="modul.id"
            :to="modul.slug ? `/siswa/modul/${modul.slug}` : '/siswa/modul'"
            class="flex items-center gap-4 pb-4 last:pb-0 border-b border-gray-50 last:border-0 group cursor-pointer"
          >
            <!-- Thumbnail -->
            <div class="w-16 h-12 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden">
              <img
                v-if="modul.thumbnail"
                :src="modul.thumbnail"
                :alt="modul.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <span class="material-symbols-outlined text-2xl">auto_stories</span>
              </div>
            </div>

            <!-- Info + Progress -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <div class="min-w-0">
                  <h4 class="text-sm font-bold text-gray-900 truncate group-hover:text-[#006D3E] transition-colors">{{ modul.title }}</h4>
                  <p class="text-xs text-gray-400 font-medium mt-0.5">{{ modul.subtitle }}</p>
                </div>
                <span
                  class="text-sm font-extrabold ml-4 flex-shrink-0 flex items-center gap-1"
                  :class="modul.progress === 100 ? 'text-emerald-600' : 'text-[#006D3E]'"
                >
                  <span v-if="modul.progress === 100" class="material-symbols-outlined text-base">check_circle</span>
                  {{ modul.progress }}%
                </span>
              </div>
              <div class="w-full h-1.5 bg-gray-100 rounded-lg overflow-hidden mt-2">
                <div
                  class="h-full rounded-lg transition-all duration-700"
                  :class="modul.progress === 100 ? 'bg-emerald-500' : 'bg-[#006D3E]'"
                  :style="{ width: modul.progress + '%' }"
                ></div>
              </div>
            </div>
          </RouterLink>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-xl p-8 shadow-lg border border-gray-100 py-16 text-center">
          <span class="material-symbols-outlined text-5xl text-gray-200 mb-3">auto_stories</span>
          <p class="text-gray-400 font-medium text-sm">Belum ada modul tersedia.</p>
        </div>
      </div>

      <!-- Jadwal Mendatang -->
      <div class="space-y-6">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Jadwal Mendatang</h3>
          <span class="text-xs font-bold text-gray-400">Jadwal</span>
        </div>

        <div class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-lg border border-gray-100">
          <div
            v-for="(item, i) in schedule"
            :key="i"
            class="flex items-start gap-4 pb-4 last:pb-0 border-b border-gray-50 last:border-0"
          >
            <!-- Date Badge -->
            <div class="flex-shrink-0 text-center bg-green-50 rounded-xl px-3 py-2 min-w-[48px]">
              <p class="text-lg font-extrabold text-[#006D3E] leading-none">{{ item.date }}</p>
              <p class="text-[10px] font-bold text-[#006D3E]/60 uppercase tracking-wider">{{ item.month }}</p>
            </div>
            <!-- Event Info -->
            <div class="min-w-0">
              <p class="text-sm font-bold text-gray-900 leading-snug">{{ item.title }}</p>
              <p class="text-xs text-gray-400 font-medium mt-1 leading-relaxed">{{ item.desc }}</p>
            </div>
          </div>
          
          <div v-if="schedule.length === 0" class="py-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-200 mb-2">event_busy</span>
            <p class="text-gray-400 text-xs font-medium">Tidak ada jadwal mendatang.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
