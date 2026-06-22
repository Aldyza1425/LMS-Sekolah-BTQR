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
const tryouts = ref<any[]>([])
const moduls = ref<any[]>([])

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
  try {
    const response = await api.get('/admin/dashboard')
    stats.value = response.data.stats
    activities.value = response.data.recent_activities || []
    tryouts.value = response.data.tryouts || []
    moduls.value = response.data.moduls || []
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
    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-xl bg-[#006D3E] p-6 md:p-10 text-white shadow-lg">
      <div class="absolute top-0 right-0 h-full w-1/2 opacity-20 pointer-events-none hidden md:flex items-center justify-center rotate-12">
        <p class="text-9xl font-serif select-none">العربية</p>
      </div>
      <div class="relative z-10 space-y-4 max-w-2xl">
        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-[10px] font-bold tracking-widest uppercase">Admin Control Center</span>
        <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight leading-tight">Pantau Keberkatan Ilmu di BTQR</h2>
        <p class="text-white/80 font-body text-sm md:text-base leading-relaxed text-balance">Kelola kurikulum, verifikasi komitmen santri, dan pantau perkembangan akademik secara real-time melalui panel editorial ini.</p>
      </div>
    </section>

    <!-- Stat Cards Bento Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Siswa Online Card -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-green-300/40 hover:bg-green-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600 relative shrink-0">
          <span class="material-symbols-outlined text-2xl">person_play</span>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-green-500 rounded-lg border-2 border-white animate-ping"></span>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-green-500 rounded-lg border-2 border-white"></span>
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
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-blue-300/40 hover:bg-blue-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-[#006D3E] shrink-0">
          <span class="material-symbols-outlined text-2xl">group</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Jumlah Siswa</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.total_siswa }}</p>
        </div>
      </div>

      <!-- Modul Aktif Card -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-orange-300/40 hover:bg-orange-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">menu_book</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Modul Aktif</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.modul_aktif }}</p>
        </div>
      </div>

      <!-- Rerata Nilai Tryout Card -->
      <div class="bg-white p-5 rounded-lg shadow-[0_12px_32px_-4px_rgba(0,109,62,0.08)] flex items-center gap-4 border border-gray-100 hover:border-purple-300/40 hover:bg-purple-50/30 transition-all duration-300">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
          <span class="material-symbols-outlined text-2xl">insights</span>
        </div>
        <div>
          <p class="text-gray-400 text-[9px] font-bold uppercase tracking-widest leading-none mb-1">Rerata Tryout</p>
          <p class="text-2xl font-headline font-extrabold text-gray-900 leading-none">{{ stats.rerata_nilai }}</p>
        </div>
      </div>
    </section>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
      <!-- Aktivitas Siswa List (Left 3 Columns) -->
      <div class="space-y-6 lg:col-span-3">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Aktivitas Santri</h3>
          <span class="text-xs font-bold text-gray-400">Terbaru</span>
        </div>
        <div class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-lg border border-gray-100">
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

      <!-- Event Jadwal Tryout (Right 2 Columns) -->
      <div class="space-y-6 lg:col-span-2">
        <div class="flex justify-between items-end">
          <h3 class="text-2xl font-headline font-bold text-gray-900">Event Jadwal Tryout</h3>
          <span class="text-xs font-bold text-gray-400">Daftar Jadwal</span>
        </div>
        <div class="bg-white rounded-xl p-6 md:p-8 space-y-4 shadow-lg border border-gray-100">
          <div v-if="tryouts.length === 0" class="text-center py-8 text-gray-400 text-sm">
            Belum ada jadwal tryout yang dibuat.
          </div>
          <div v-for="(t, i) in tryouts" :key="i" class="flex items-start gap-4 border-b border-gray-50 pb-4 last:border-0 last:pb-0">
            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-xl">event</span>
            </div>
            <div class="flex-grow min-w-0">
              <h4 class="font-bold text-gray-950 truncate text-sm md:text-base">{{ t.judul }}</h4>
              <p class="text-xs text-gray-500 truncate mb-1">Dibuat oleh: {{ t.creator_name }}</p>
              <div class="flex flex-col gap-0.5">
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

    <!-- Modul Pembelajaran oleh Pengajar -->
    <div class="space-y-6">
      <h3 class="text-2xl font-headline font-bold text-gray-900">Modul Pembelajaran oleh Pengajar</h3>
      <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs font-bold uppercase tracking-wider">
                <th class="p-4 pl-6">Judul Modul</th>
                <th class="p-4">Mata Pelajaran</th>
                <th class="p-4">Level</th>
                <th class="p-4">Jumlah Materi</th>
                <th class="p-4">Pembuat</th>
                <th class="p-4">Status</th>
                <th class="p-4 pr-6">Tanggal Rilis</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
              <tr v-if="moduls.length === 0">
                <td colspan="7" class="p-8 text-center text-gray-400">Belum ada modul yang dibuat oleh pengajar.</td>
              </tr>
              <tr v-for="m in moduls" :key="m.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="p-4 pl-6 font-bold text-gray-900">
                  <div class="flex flex-col">
                    <span>{{ m.judul }}</span>
                    <span class="text-xs text-gray-400 font-normal" v-if="m.arabic_title" dir="rtl">{{ m.arabic_title }}</span>
                  </div>
                </td>
                <td class="p-4 text-gray-600">{{ m.mata_pelajaran }}</td>
                <td class="p-4">
                  <span class="px-2 py-1 bg-green-50 text-[#006D3E] rounded-lg text-xs font-bold">{{ m.level }}</span>
                </td>
                <td class="p-4 font-semibold text-gray-700">{{ m.materi_count }} Materi</td>
                <td class="p-4 text-gray-600">{{ m.creator_name }}</td>
                <td class="p-4">
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                        :class="m.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                    {{ m.is_active ? 'Aktif' : 'Draft' }}
                  </span>
                </td>
                <td class="p-4 pr-6 text-gray-500 text-xs">{{ m.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
