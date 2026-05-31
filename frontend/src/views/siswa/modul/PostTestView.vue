<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { usePageStore } from '@/stores/page'
import { useToast } from '@/composables/useToast'

const route = useRoute()
const router = useRouter()
const pageStore = usePageStore()
const { error } = useToast()

const modul = ref<any>(null)
const currentMateri = ref<any>(null)
const isLoading = ref(true)
const activeQuestionIndex = ref(0)

const submitting = ref(false)
const isQuizStarted = ref(false)
const timeLeft = ref(0)
const timerInterval = ref<any>(null)
const answers = ref<Record<number, string>>({})
const quizResult = ref<any>(null)

// Soal Post Test dari dalam materi
const soals = computed(() => currentMateri.value?.post_tests || [])

const formattedTime = computed(() => {
  const mins = Math.floor(timeLeft.value / 60)
  const secs = timeLeft.value % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
})

const progressPercent = computed(() => {
  if (!soals.value.length) return 0
  const answeredCount = Object.keys(answers.value).length
  return Math.round((answeredCount / soals.value.length) * 100)
})

const fetchPostTest = async () => {
  try {
    isLoading.value = true
    const modRes = await api.get(`/siswa/moduls/${route.params.slug}`)
    modul.value = modRes.data.data

    const step = parseInt(route.params.mid as string)
    const allMateris = modul.value?.materis || []
    const targetMateri = allMateris[step - 1]

    if (!targetMateri) {
      router.push({ name: 'siswa.modul.show', params: { slug: route.params.slug } })
      return
    }

    // Load detail materi (including post_tests soals)
    const matRes = await api.get(`/siswa/materis/${targetMateri.id}`)
    currentMateri.value = matRes.data.data
    pageStore.setTitle(`Post-Test: ${currentMateri.value.judul}`)

    // If no post_test soal, redirect back to materi
    if (!currentMateri.value.has_posttest || soals.value.length === 0) {
      router.push({ name: 'siswa.modul.learning', params: { slug: route.params.slug, mid: route.params.mid } })
    }
  } catch (error) {
    console.error('Error fetching Post Test:', error)
  } finally {
    isLoading.value = false
  }
}

const startQuiz = () => {
  isQuizStarted.value = true
  timeLeft.value = (currentMateri.value.durasi || 10) * 60

  timerInterval.value = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timerInterval.value)
      submitQuiz(true)
    }
  }, 1000)
}

const submitQuiz = async (isAuto = false) => {
  if (!isAuto && Object.keys(answers.value).length < soals.value.length) {
    if (!confirm('Anda belum menjawab semua soal. Kirim sekarang?')) return
  }

  try {
    submitting.value = true
    if (timerInterval.value) clearInterval(timerInterval.value)

    const timeUsed = Math.ceil(((currentMateri.value.durasi || 0) * 60 - timeLeft.value) / 60)

    const response = await api.post(`/siswa/materis/${currentMateri.value.id}/submit`, {
      answers: answers.value,
      time_used: timeUsed,
      tipe_kuis: 'post_test'
    })

    quizResult.value = response.data
  } catch (err: any) {
    console.error('Error submitting quiz:', err)
    error('Gagal mengirim jawaban: ' + (err.response?.data?.message || err.message))
    if (isAuto) {
      goBack()
    }
  } finally {
    submitting.value = false
  }
}

const goBack = () => {
  if (isQuizStarted.value && !quizResult.value) {
    if (!confirm('Ujian sedang berlangsung. Keluar sekarang? Progres Anda tidak akan tersimpan.')) return
  }
  router.push({ name: 'siswa.modul.learning', params: { slug: route.params.slug, mid: route.params.mid } })
}

const getCompletionTime = () => {
  const date = new Date()
  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}, ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`
}

onMounted(() => {
  fetchPostTest()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 font-['Plus_Jakarta_Sans'] flex flex-col text-gray-900">

    <!-- Header -->
    <header class="bg-white border-b border-gray-100 px-6 py-4 sticky top-0 z-50">
      <div class="max-w-[1200px] mx-auto flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="goBack" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
          </button>
          <div>
            <h1 class="text-base font-black tracking-tight leading-none text-gray-900">
              Post-Test: {{ currentMateri?.judul || 'Loading...' }}
            </h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ modul?.judul || 'LMS Siswa' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="hidden sm:flex flex-col items-end">
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Bait Tahfizh Al-Qur'an</span>
            <span class="text-xs font-bold text-[#006D3E]">Post-Test Materi</span>
          </div>
          <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">
            <span class="material-symbols-outlined">quiz</span>
          </div>
        </div>
      </div>
    </header>

    <div class="flex-1 flex flex-col items-center">
      <div class="w-full max-w-[1200px] p-6 md:p-8">

        <!-- Loading -->
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-32 space-y-4">
          <div class="animate-spin rounded-full h-10 w-10 border-4 border-[#006D3E] border-t-transparent"></div>
          <p class="text-gray-400 font-bold text-xs animate-pulse uppercase tracking-widest">Menyiapkan Soal Post-Test...</p>
        </div>

        <div v-else-if="currentMateri" class="animate-in fade-in slide-in-from-bottom-4 duration-700">

          <!-- Start Screen -->
          <div v-if="!isQuizStarted && !quizResult" class="max-w-2xl mx-auto bg-white rounded-[2.5rem] p-10 md:p-16 text-center shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center">
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-[2rem] flex items-center justify-center mb-8">
              <span class="material-symbols-outlined text-4xl">quiz</span>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Post-Test Siap Dikerjakan</h2>
            <p class="text-gray-500 font-medium mb-10 leading-relaxed">
              Anda akan mengerjakan Post-Test untuk materi<br/>
              <span class="text-gray-900 font-bold">{{ currentMateri.judul }}</span>
            </p>

            <div class="grid grid-cols-2 gap-4 w-full max-w-sm mb-10">
              <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                <span class="material-symbols-outlined text-[#006D3E] text-xl mb-1">assignment</span>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Soal</p>
                <p class="text-xl font-black text-gray-900">{{ soals.length }}</p>
              </div>
              <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                <span class="material-symbols-outlined text-[#006D3E] text-xl mb-1">schedule</span>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Waktu</p>
                <p class="text-xl font-black text-gray-900">{{ currentMateri.durasi || 10 }}'</p>
              </div>
            </div>

            <button @click="startQuiz" class="px-10 py-4 bg-[#006D3E] text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-[#005a33] transition-all active:scale-[0.98]">
              MULAI POST-TEST SEKARANG
            </button>
          </div>

          <!-- Result Screen -->
          <div v-else-if="quizResult" class="max-w-4xl mx-auto space-y-8 w-full">
            
            <!-- Score Summary Card (Tailored to match Tryout Result) -->
            <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-500">
              <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Score Ring -->
                <div class="shrink-0 flex flex-col items-center">
                  <div class="w-28 h-28 rounded-[2rem] flex flex-col items-center justify-center font-black border border-gray-100 bg-gray-50 text-gray-900 shadow-inner">
                    <span class="text-4xl leading-none">{{ quizResult.score }}</span>
                    <span class="text-[9px] font-black uppercase tracking-widest opacity-60 mt-1">SKOR</span>
                  </div>
                </div>

                <!-- Details Grid -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-5 w-full">
                  <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Soal Benar</p>
                    <p class="text-xl font-black">
                      <span :class="quizResult.correct > 0 ? 'text-[#006D3E]' : 'text-gray-400'">{{ quizResult.correct }}</span>
                      <span class="text-gray-300 text-base"> / {{ quizResult.total }}</span>
                    </p>
                  </div>
                  <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2">Waktu Selesai</p>
                    <p class="text-xs font-black text-gray-700 leading-snug">{{ getCompletionTime() }}</p>
                  </div>
                  <div class="p-4 flex items-center justify-center">
                    <button @click="goBack" class="w-full h-full py-4 px-6 bg-[#006D3E] text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-[#005a33] transition-all flex items-center justify-center gap-2">
                      Kembali ke Materi
                      <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Per-Question Review (Tailored to match Tryout Result) -->
            <div>
              <h2 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-5 px-1">Tinjauan Per Soal</h2>
              <div class="space-y-4">
                <div 
                  v-for="(qr, index) in quizResult.question_results" 
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
                    <p class="text-base font-bold text-gray-900 leading-relaxed">{{ qr.soal?.soal }}</p>

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
                        {{ qr.soal[opt] }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quiz UI -->
          <div v-else-if="isQuizStarted" class="flex flex-col lg:flex-row gap-8 w-full">

            <!-- Left: Sidebar -->
            <aside class="w-full lg:w-72 shrink-0 space-y-6">
              <!-- Timer -->
              <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm text-center">
                <div class="flex items-center justify-center gap-2 text-gray-400 mb-3">
                  <span class="material-symbols-outlined text-lg">schedule</span>
                  <span class="text-[9px] font-black uppercase tracking-widest">SISA WAKTU</span>
                </div>
                <div class="text-4xl font-mono font-black tracking-tighter text-gray-900 tabular-nums mb-3"
                     :class="timeLeft < 60 ? 'text-red-600 animate-pulse' : ''">
                  {{ formattedTime }}
                </div>
                <div class="flex items-center justify-center gap-1.5">
                  <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                  <span class="text-[9px] font-black text-green-600 uppercase tracking-widest">Waktu berjalan</span>
                </div>
              </div>

              <!-- Question Map -->
              <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-6 text-center">PETA SOAL</h4>
                <div class="grid grid-cols-4 gap-2">
                  <button
                    v-for="(soal, idx) in soals" :key="'map-'+idx"
                    @click="activeQuestionIndex = Number(idx)"
                    class="aspect-square rounded-xl flex items-center justify-center font-black text-[10px] transition-all border-2"
                    :class="[
                      activeQuestionIndex === idx
                        ? 'bg-[#006D3E] border-[#006D3E] text-white scale-105'
                        : (answers[soal.id]
                            ? 'bg-gray-100 border-gray-100 text-gray-500'
                            : 'bg-white border-gray-50 text-gray-300 hover:border-gray-100')
                    ]"
                  >
                    {{ Number(idx) + 1 }}
                  </button>
                </div>

                <div class="mt-6 pt-5 border-t border-gray-50 space-y-2">
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#006D3E]"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Aktif</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-gray-200"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Sudah Dijawab</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-white border border-gray-200"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Belum Dijawab</span>
                  </div>
                </div>
              </div>

              <!-- Progress -->
              <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-3">
                  <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Progress</span>
                  <span class="text-sm font-black text-[#006D3E]">{{ Object.keys(answers).length }}/{{ soals.length }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                  <div class="bg-[#006D3E] h-2.5 rounded-full transition-all duration-500" :style="{ width: progressPercent + '%' }"></div>
                </div>
              </div>
            </aside>

            <!-- Right: Question -->
            <div class="flex-1 space-y-6">
              <div class="bg-white rounded-3xl p-8 md:p-12 border border-gray-100 min-h-[450px] flex flex-col">
                <div class="flex-1 space-y-8">
                  <div class="flex items-start gap-5">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 font-black shrink-0 text-sm border border-gray-100">
                      {{ activeQuestionIndex + 1 }}
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 leading-relaxed">{{ soals[activeQuestionIndex].soal }}</h3>
                  </div>

                  <!-- Options -->
                  <div class="grid grid-cols-1 gap-3">
                    <label v-for="opt in ['a', 'b', 'c', 'd']" :key="opt"
                      class="relative flex items-center p-5 rounded-2xl border-2 transition-all cursor-pointer group"
                      :class="answers[soals[activeQuestionIndex].id] === opt
                        ? 'border-[#006D3E] bg-green-50/30'
                        : 'border-gray-50 bg-gray-50/30 hover:border-gray-200'"
                    >
                      <input type="radio" :name="'soal-'+soals[activeQuestionIndex].id" :value="opt" v-model="answers[soals[activeQuestionIndex].id]" class="sr-only">
                      <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black uppercase shrink-0 mr-5 transition-all border"
                        :class="answers[soals[activeQuestionIndex].id] === opt
                          ? 'bg-[#006D3E] border-[#006D3E] text-white'
                          : 'bg-white border-gray-100 text-gray-300 group-hover:text-gray-500'"
                      >
                        {{ opt }}
                      </div>
                      <span class="text-base font-bold" :class="answers[soals[activeQuestionIndex].id] === opt ? 'text-[#006D3E]' : 'text-gray-600'">
                        {{ soals[activeQuestionIndex][opt] }}
                      </span>
                      <div v-if="answers[soals[activeQuestionIndex].id] === opt" class="ml-auto">
                        <span class="material-symbols-outlined text-[#006D3E]">check_circle</span>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- Nav Buttons -->
                <div class="pt-8 flex items-center justify-between border-t border-gray-50">
                  <button
                    @click="activeQuestionIndex--"
                    :disabled="activeQuestionIndex === 0"
                    class="px-6 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all text-gray-400 hover:text-gray-900 disabled:opacity-0"
                  >
                    <span class="material-symbols-outlined text-sm align-middle mr-1">arrow_back</span> Sebelumnya
                  </button>

                  <button
                    v-if="activeQuestionIndex < soals.length - 1"
                    @click="activeQuestionIndex++"
                    class="px-8 py-3.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all"
                  >
                    Selanjutnya <span class="material-symbols-outlined text-sm align-middle ml-1">arrow_forward</span>
                  </button>

                  <button
                    v-else
                    @click="submitQuiz(false)"
                    :disabled="submitting"
                    class="px-8 py-3.5 bg-[#006D3E] text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-[#005a33] transition-all"
                  >
                    {{ submitting ? 'MEMPROSES...' : 'SELESAI' }}
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.scale-in {
  animation: scale-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scale-in {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: #f9fafb; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
