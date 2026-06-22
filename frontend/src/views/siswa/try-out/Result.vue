<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { usePageStore } from '@/stores/page'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const route = useRoute()
const router = useRouter()
const pageStore = usePageStore()

const isLoading = ref(true)
const result = ref<any>(null)
const examId = route.params.id

const fetchData = async () => {
  try {
    isLoading.value = true
    
    const response = await api.get(`/siswa/try-out/${examId}/hasil`)
    if (response.data.success) {
      result.value = response.data.data
      pageStore.setTitle(`Hasil Try Out`)
    } else {
      router.push({ name: 'siswa.try-out' })
    }
  } catch (error) {
    console.error('Error fetching result:', error)
    router.push({ name: 'siswa.try-out' })
  } finally {
    isLoading.value = false
  }
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}, ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`
}

const correctCount = computed(() => result.value?.question_results?.filter((r: any) => r.is_correct).length ?? 0)
const totalCount = computed(() => result.value?.question_results?.length ?? 0)

onMounted(() => {
  fetchData()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 font-['Plus_Jakarta_Sans'] flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-100 px-6 py-4 sticky top-0 z-50">
      <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button 
            @click="router.push({ name: 'siswa.try-out' })"
            class="w-10 h-10 bg-white hover:bg-gray-50 rounded-lg transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer"
          >
            <span class="material-symbols-outlined text-xl">arrow_back</span>
          </button>
          <div>
            <h1 class="text-base font-black tracking-tight text-gray-900">Hasil Try Out</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Evaluasi Ujian</p>
          </div>
        </div>
      </div>
    </header>

    <div class="flex-1 max-w-4xl mx-auto w-full px-6 py-10 space-y-8">
      
      <!-- Loading -->
      <div v-if="isLoading" class="space-y-6">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl">
          <SkeletonLoader type="stat" :cols="3" />
        </div>
        <SkeletonLoader type="detail" :rows="4" />
      </div>

      <template v-else-if="result">

        <!-- Score Summary Card -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-500">
          <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- Score Ring -->
            <div class="shrink-0 flex flex-col items-center">
              <div class="w-28 h-28 rounded-[2rem] flex flex-col items-center justify-center font-black border border-gray-100 bg-gray-50 text-gray-900 shadow-inner">
                <span class="text-4xl leading-none">{{ result.score }}</span>
                <span class="text-[9px] font-black uppercase tracking-widest opacity-60 mt-1">SKOR</span>
              </div>
            </div>

            <!-- Details Grid -->
            <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 gap-5 w-full">
              <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Soal Benar</p>
                <p class="text-xl font-black">
                  <span :class="correctCount > 0 ? 'text-[#006D3E]' : 'text-gray-400'">{{ correctCount }}</span>
                  <span class="text-gray-300 text-base"> / {{ totalCount }}</span>
                </p>
              </div>
              <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Waktu Selesai</p>
                <p class="text-xs font-black text-gray-700">{{ formatDate(result.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Per-Question Review -->
        <div>
          <h2 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-5 px-1">Tinjauan Per Soal</h2>
          <div class="space-y-4">
            <div 
              v-for="(qr, index) in result.question_results" 
              :key="qr.soal_id || index"
              class="bg-white rounded-[1.75rem] border overflow-hidden shadow-sm animate-in fade-in slide-in-from-bottom-4 duration-500"
              :class="qr.is_correct ? 'border-green-100' : 'border-red-100'"
              :style="{ animationDelay: `${Number(index) * 60}ms` }"
            >
              <!-- Question Header -->
              <div class="flex items-center gap-4 px-6 py-4 border-b" :class="qr.is_correct ? 'border-green-50 bg-green-50/30' : 'border-red-50 bg-red-50/30'">
                <div 
                  class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
                  :class="qr.is_correct ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'"
                >
                  {{ Number(index) + 1 }}
                </div>
                <div class="flex-1 flex items-center justify-between">
                  <span class="text-[9px] font-black uppercase tracking-widest" :class="qr.is_correct ? 'text-green-600' : 'text-red-500'">
                    {{ qr.is_correct ? '✓ Benar' : '✗ Salah' }}
                  </span>
                  <span class="material-symbols-outlined text-xl" :class="qr.is_correct ? 'text-green-500' : 'text-red-400'">
                    {{ qr.is_correct ? 'check_circle' : 'cancel' }}
                  </span>
                </div>
              </div>

              <!-- Question Body -->
              <div class="px-6 py-5 space-y-5">
                <p class="text-base font-bold text-gray-900 leading-relaxed">{{ qr.soal?.pertanyaan }}</p>

                <!-- Gambar Soal -->
                <div v-if="qr.soal?.gambar" class="my-4">
                  <img :src="qr.soal.gambar" alt="Gambar Soal" class="max-w-full max-h-64 rounded-lg shadow-md border border-gray-100 object-contain" />
                </div>

                <!-- All Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" v-if="qr.soal">
                  <div 
                    v-for="opt in ['a', 'b', 'c', 'd']" :key="opt"
                    class="flex items-center gap-3 p-3 rounded-xl border text-sm font-bold transition-all"
                    :class="[
                      qr.correct_answer === opt ? 'border-green-200 bg-green-50 text-green-800' : 
                      (qr.user_answer === opt && !qr.is_correct ? 'border-red-200 bg-red-50 text-red-700' : 'border-gray-50 bg-gray-50/50 text-gray-500')
                    ]"
                  >
                    <span 
                      class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-[10px] uppercase shrink-0"
                      :class="[
                        qr.correct_answer === opt ? 'bg-green-600 text-white' : 
                        (qr.user_answer === opt && !qr.is_correct ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-400')
                      ]"
                    >{{ opt }}</span>
                    {{ qr.soal['pilihan_' + opt] }}
                  </div>
                </div>

                <!-- Answer Summary (no soal detail) -->
                <div v-else class="flex flex-wrap gap-6 pt-2">
                  <div class="space-y-1">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Jawaban Anda</p>
                    <p class="text-sm font-black uppercase" :class="qr.is_correct ? 'text-[#006D3E]' : 'text-red-500'">{{ qr.user_answer || 'Tidak Dijawab' }}</p>
                  </div>
                  <div v-if="!qr.is_correct && qr.correct_answer" class="space-y-1">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Jawaban Benar</p>
                    <p class="text-sm font-black text-[#006D3E] uppercase">{{ qr.correct_answer }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </template>
    </div>
  </div>
</template>

<style scoped>
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: #f9fafb; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
