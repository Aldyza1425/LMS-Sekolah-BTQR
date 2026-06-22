<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/api'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const isLoading = ref(true)

const stats = ref({
  siswa_aktif: 0,
  modul_aktif: 0,
  siswa_selesai_tryout: 0,
  nilai_tertinggi: 0
})

const activities = ref<any[]>([])
const tryouts = ref<any[]>([])

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchDashboardData = async () => {
  isLoading.value = true
  try {
    const response = await api.get('/pengajar/dashboard')
    stats.value = response.data.stats
    activities.value = response.data.recent_activities || []
    tryouts.value = response.data.tryouts || []
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-xl bg-[#006D3E] p-6 md:p-10 text-white shadow-lg">
      <div class="absolute top-0 right-0 h-full w-1/2 opacity-20 pointer-events-none hidden md:flex items-center justify-center rotate-12">
        <p class="text-9xl font-serif select-none">العربية</p>
      </div>
      <div class="relative z-10 space-y-4 max-w-2xl">
        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-bold tracking-widest uppercase">Panel Pengajar</span>
        <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight leading-tight">Pantau Keberkatan Ilmu di BTQR</h2>
        <p class="text-white/80 font-body text-sm md:text-base leading-relaxed text-balance">Kelola kurikulum, verifikasi komitmen santri, dan pantau perkembangan akademik secara real-time melalui panel editorial ini.</p>
      </div>
    </section>

    <!-- Stat Cards Skeleton -->
    <section v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <SkeletonLoader type="stat" :cols="4" />
    </section>

    <!-- Stat Cards Bento Grid -->
    <section v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-[#006D3E]/20 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-[#006D3E]">
          <span class="material-symbols-outlined text-2xl">group</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Siswa Aktif</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.siswa_aktif }}</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-orange-300/40 hover:bg-orange-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
          <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Modul Aktif</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.modul_aktif }}</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-blue-300/40 hover:bg-blue-50/30 transition-all duration-300 text-blue-600">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
          <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Siswa Selesai Try Out</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.siswa_selesai_tryout }}</p>
        </div>
      </div>
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-green-300/40 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
          <span class="material-symbols-outlined text-2xl">emoji_events</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Nilai Tertinggi Try Out</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.nilai_tertinggi }}</p>
        </div>
      </div>
    </section>

    <!-- Main Content Layout (Asymmetric Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Aktivitas Siswa List (Left 2 Columns) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Aktivitas Santri</h3>
          <span class="text-xs font-bold text-gray-400">Terbaru</span>
        </div>
        <div v-if="isLoading" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
          <SkeletonLoader type="list" :rows="4" />
        </div>
        <div v-else class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-xl border border-gray-100">
          <div v-if="activities.length === 0" class="text-center py-8 text-gray-400 text-sm">
            Belum ada aktivitas terbaru dari siswa.
          </div>
          <div v-for="(act, i) in activities" :key="i" class="flex items-start justify-between border-b border-gray-50 pb-4 last:border-0 last:pb-0 group">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                   :class="{
                     'bg-blue-50 text-blue-600': act.type === 'registration',
                     'bg-purple-50 text-purple-600': act.type === 'tryout',
                     'bg-emerald-50 text-emerald-600': act.type === 'progress'
                   }">
                <span class="material-symbols-outlined text-xl">
                  {{ act.type === 'registration' ? 'person_add' : (act.type === 'tryout' ? 'assignment' : 'menu_book') }}
                </span>
              </div>
              <div>
                <h4 class="font-bold text-gray-900 group-hover:text-[#006D3E] transition-colors text-sm md:text-base">{{ act.title }}</h4>
                <p class="text-xs md:text-sm text-gray-500 leading-relaxed">{{ act.subtitle }}</p>
                <div class="flex items-center gap-3 mt-1.5">
                  <span class="text-[10px] text-gray-400 font-medium">{{ act.time }}</span>
                  <span v-if="act.score !== null" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700">
                    Nilai: {{ act.score }}
                  </span>
                </div>
              </div>
            </div>
            
            <div>
              <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg"
                    :class="{
                      'bg-green-100 text-green-700': act.badge_type === 'success',
                      'bg-amber-100 text-amber-700': act.badge_type === 'warning',
                      'bg-red-100 text-red-700': act.badge_type === 'danger',
                      'bg-blue-100 text-blue-700': act.badge_type === 'info',
                    }">
                {{ act.badge }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Jadwal Tryout Saya (Right 1 Column) -->
      <div class="space-y-6">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Jadwal Tryout Saya</h3>
          <span class="text-xs font-bold text-gray-400">Jadwal Aktif</span>
        </div>
        <div v-if="isLoading" class="bg-white rounded-xl border border-gray-100 overflow-hidden">
          <SkeletonLoader type="list" :rows="3" />
        </div>
        <div v-else class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-xl border border-gray-100">
          <div v-if="tryouts.length === 0" class="text-center py-8 text-gray-400 text-sm">
            Belum ada jadwal tryout yang Anda buat.
          </div>
          <div v-for="(t, i) in tryouts" :key="i" class="flex items-start gap-4 border-b border-gray-50 pb-4 last:border-0 last:pb-0">
            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-xl">event</span>
            </div>
            <div class="flex-grow min-w-0">
              <h4 class="font-bold text-gray-950 truncate text-sm md:text-base">{{ t.judul }}</h4>
              <div class="flex flex-col gap-0.5 mt-1">
                <span class="text-[10px] text-gray-600 flex items-center gap-1 font-medium">
                  <span class="material-symbols-outlined text-[12px]">play_circle</span>
                  Mulai: {{ formatDate(t.mulai_at) }}
                </span>
                <span class="text-[10px] text-gray-600 flex items-center gap-1 font-medium">
                  <span class="material-symbols-outlined text-[12px]">stop_circle</span>
                  Selesai: {{ formatDate(t.selesai_at) }}
                </span>
              </div>
            </div>
            <div class="flex-shrink-0 text-right">
              <span class="px-2 py-0.5 rounded text-[9px] font-bold"
                    :class="t.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                {{ t.is_active ? 'Aktif' : 'Draft' }}
              </span>
              <p class="text-xs font-bold text-gray-600 mt-2">{{ t.durasi_menit }} mnt</p>
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
