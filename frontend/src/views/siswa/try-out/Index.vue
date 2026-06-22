<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import { useSkeletonCount } from '@/composables/useSkeletonCount'

const router = useRouter()
const tryouts = ref<any[]>([])
const isLoading = ref(true)
const { skeletonCount, updateSkeletonCount } = useSkeletonCount('siswa.tryout', 4)

const fetchTryOuts = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/siswa/try-out')
    if (response.data.success) {
       tryouts.value = response.data.data
       updateSkeletonCount(tryouts.value.length)
    }
  } catch (error) {
    console.error('Error fetching tryouts:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchTryOuts()
})

const startTryOut = (id: number) => {
  router.push({ name: 'siswa.try-out.show', params: { id } })
}

const isRequesting = ref(false)

const requestRetake = async (id: number) => {
  if (isRequesting.value) return
  try {
    isRequesting.value = true
    const response = await api.post(`/siswa/try-out/${id}/request-retake`)
    if (response.data.success) {
      fetchTryOuts()
    }
  } catch (error) {
    console.error('Error requesting retake:', error)
  } finally {
    isRequesting.value = false
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="header-section">
      <h2 class="font-headline text-3xl font-bold text-[#005344]">Try Out & Ujian</h2>
      <p class="text-outline">Evaluasi pemahaman bahasa Arab Anda melalui ujian online.</p>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <SkeletonLoader type="card" :cols="skeletonCount" />
    </div>

    <div v-else-if="tryouts.length === 0" class="text-center py-20 bg-surface-container-lowest rounded-lg border border-outline-variant/15">
      <span class="material-symbols-outlined text-6xl text-outline mb-4">quiz</span>
      <h3 class="text-xl font-bold text-on-surface">Belum ada Try Out</h3>
      <p class="text-on-surface-variant">Belum ada try out yang tersedia saat ini.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div v-for="t in tryouts" :key="t.id" class="group bg-white rounded-[2rem] shadow-xl shadow-teal-900/5 border border-gray-100 hover:border-[#005344]/20 overflow-hidden flex flex-col hover:bg-[#005344]/5 transition-all duration-300">
        <div class="p-8 space-y-6 flex-1">
          <div class="flex justify-between items-start">
            <div class="h-14 w-14 rounded-lg bg-teal-50 flex items-center justify-center text-[#005344] group-hover:bg-[#005344] group-hover:text-white transition-colors duration-300">
              <span class="material-symbols-outlined text-3xl">quiz</span>
            </div>
            <span v-if="t.sudah_dikerjakan" class="px-4 py-1.5 bg-gray-100 text-gray-500 text-[10px] font-black tracking-widest rounded-lg uppercase border border-gray-200">Selesai</span>
            <span v-else-if="t.status_jadwal === 'terjadwal'" class="px-4 py-1.5 bg-orange-50 text-orange-600 text-[10px] font-black tracking-widest rounded-lg uppercase border border-orange-100">Terjadwal</span>
            <span v-else-if="t.status_jadwal === 'kedaluwarsa'" class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] font-black tracking-widest rounded-lg uppercase border border-red-100">Kedaluwarsa</span>
            <span v-else class="px-4 py-1.5 bg-green-50 text-green-600 text-[10px] font-black tracking-widest rounded-lg uppercase border border-green-100">Tersedia</span>
          </div>
          <div>
            <h4 class="font-headline font-extrabold text-2xl text-gray-900 group-hover:text-[#005344] transition-colors">{{ t.judul }}</h4>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ t.deskripsi }}</p>
            <p v-if="!t.sudah_dikerjakan && t.status_jadwal === 'terjadwal' && t.mulai_at" class="text-xs text-orange-600 font-bold mt-3 flex items-center gap-2">
              <span class="material-symbols-outlined text-sm">schedule</span>
              Ujian dijadwalkan pada: {{ formatDate(t.mulai_at) }}
            </p>
          </div>
          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-teal-50 group-hover:text-[#005344] transition-colors">
                <span class="material-symbols-outlined text-sm">timer</span>
              </div>
              <span class="text-xs font-bold text-gray-600">{{ t.durasi_menit }} Menit</span>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-teal-50 group-hover:text-[#005344] transition-colors">
                <span class="material-symbols-outlined text-sm">fact_check</span>
              </div>
              <span class="text-xs font-bold text-gray-600">{{ t.soals_count || 0 }} Soal</span>
            </div>
          </div>
        </div>
        <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
          <template v-if="t.sudah_dikerjakan">
            <button v-if="t.retake_status === 'requested'" disabled class="bg-orange-50 text-orange-600 border border-orange-200 px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase cursor-wait flex items-center gap-2">
              Menunggu Persetujuan
              <span class="material-symbols-outlined text-sm">pending</span>
            </button>
            <button v-else @click="requestRetake(t.id)" :disabled="isRequesting" class="bg-white text-gray-700 border border-gray-200 px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-gray-100 transition-all flex items-center gap-2 disabled:opacity-50">
              Ajukan Ujian Ulang
              <span class="material-symbols-outlined text-sm">replay</span>
            </button>
            <button disabled class="bg-gray-200 text-gray-500 px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase cursor-not-allowed flex items-center gap-2">
              Sudah Dikerjakan
              <span class="material-symbols-outlined text-sm">check_circle</span>
            </button>
          </template>
          <template v-else-if="t.status_jadwal === 'terjadwal'">
            <button disabled class="bg-gray-100 text-gray-400 border border-gray-200 px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase cursor-not-allowed flex items-center gap-2">
              Belum Dimulai
              <span class="material-symbols-outlined text-sm">lock</span>
            </button>
          </template>
          <template v-else-if="t.status_jadwal === 'kedaluwarsa'">
            <button disabled class="bg-gray-100 text-gray-400 border border-gray-200 px-6 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase cursor-not-allowed flex items-center gap-2">
              Waktu Berakhir
              <span class="material-symbols-outlined text-sm">lock</span>
            </button>
          </template>
          <button v-else @click="startTryOut(t.id)" class="bg-[#005344] text-white px-8 py-3 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-teal-900 transition-all flex items-center gap-2">
            Ikuti Ujian
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
