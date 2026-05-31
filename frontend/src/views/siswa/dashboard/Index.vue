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
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#006D3E] to-[#00955A] p-8 md:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Mata Pelajaran Aktif -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-green-300/40 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-4">
          <span class="material-symbols-outlined text-[#006D3E] text-xl">menu_book</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 leading-none mb-1">{{ stats.active_courses }}</p>
        <p class="text-xs text-gray-400 font-medium">Mata Pelajaran Aktif</p>
      </div>

      <!-- Modul Selesai -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-orange-300/40 hover:bg-orange-50/30 transition-all duration-300">
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center mb-4">
          <span class="material-symbols-outlined text-orange-500 text-xl">check_circle</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 leading-none mb-1">{{ stats.completed_modules }}</p>
        <p class="text-xs text-gray-400 font-medium">Modul Selesai</p>
      </div>

      <!-- Waktu Belajar -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-blue-300/40 hover:bg-blue-50/30 transition-all duration-300">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-4">
          <span class="material-symbols-outlined text-blue-500 text-xl">schedule</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 leading-none mb-1">{{ stats.learning_time }}</p>
        <p class="text-xs text-gray-400 font-medium">Waktu Belajar (Jam Online)</p>
      </div>

      <!-- Rata-rata Nilai -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-red-300/40 hover:bg-red-50/30 transition-all duration-300">
        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center mb-4">
          <span class="material-symbols-outlined text-red-400 text-xl">workspace_premium</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-900 leading-none mb-1">{{ stats.average_score || '-' }}</p>
        <p class="text-xs text-gray-400 font-medium">Rata-rata Nilai</p>
      </div>
    </div>

    <!-- Main Content Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Progres Mata Pelajaran -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-50">
          <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-base text-[#006D3E]">menu_book</span>
            Progres Mata Pelajaran
          </h3>
          <a href="/lms/siswa/modul" class="text-[#006D3E] font-bold text-xs hover:underline flex items-center gap-1">
            Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="loading" class="divide-y divide-gray-50">
          <div v-for="i in 2" :key="i" class="flex items-center gap-4 px-6 py-5 animate-pulse">
            <div class="w-16 h-12 bg-gray-100 rounded-xl flex-shrink-0"></div>
            <div class="flex-1 space-y-2">
              <div class="h-3.5 bg-gray-100 rounded w-1/2"></div>
              <div class="h-2.5 bg-gray-50 rounded w-1/3"></div>
              <div class="h-1.5 bg-gray-100 rounded-full w-full mt-3"></div>
            </div>
          </div>
        </div>

        <!-- Module Progress List -->
        <div v-else-if="modulProgress.length > 0" class="divide-y divide-gray-50">
          <div
            v-for="modul in modulProgress"
            :key="modul.id"
            class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/50 transition-colors group"
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
                <span class="text-sm font-extrabold text-[#006D3E] ml-4 flex-shrink-0">{{ modul.progress }}%</span>
              </div>
              <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden mt-2">
                <div
                  class="h-full bg-[#006D3E] rounded-full transition-all duration-700"
                  :style="{ width: modul.progress + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-16 text-center">
          <span class="material-symbols-outlined text-5xl text-gray-200 mb-3">auto_stories</span>
          <p class="text-gray-400 font-medium text-sm">Belum ada modul tersedia.</p>
        </div>
      </div>

      <!-- Jadwal Mendatang -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-50">
          <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-base text-[#006D3E]">calendar_month</span>
            Jadwal Mendatang
          </h3>
          <a href="#" class="text-[#006D3E] font-bold text-xs hover:underline flex items-center gap-1">
            Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>

        <div class="divide-y divide-gray-50">
          <div
            v-for="(item, i) in schedule"
            :key="i"
            class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors"
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
        </div>

        <div v-if="schedule.length === 0" class="py-12 text-center">
          <span class="material-symbols-outlined text-4xl text-gray-200 mb-2">event_busy</span>
          <p class="text-gray-400 text-xs font-medium">Tidak ada jadwal mendatang.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
