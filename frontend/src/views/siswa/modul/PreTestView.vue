<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const materiId = route.params.mid
const slug = route.params.slug

const isLoading = ref(true)
const isSubmitting = ref(false)
const materi = ref<any>(null)
const modul = ref<any>(null)
const questions = ref<any[]>([])
const { error } = useToast()

const isQuizStarted = ref(false)
const activeQuestionIndex = ref(0)
const answers = ref<Record<number, string>>({})
const timeLeft = ref(0)
const timerInterval = ref<any>(null)

// ── computed ──────────────────────────────────────────────────────────────────
const soals = computed(() => questions.value)

const formattedTime = computed(() => {
  const mins = Math.floor(timeLeft.value / 60)
  const secs = timeLeft.value % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
})

const progressPercent = computed(() => {
  if (!soals.value.length) return 0
  return Math.round((Object.keys(answers.value).length / soals.value.length) * 100)
})

// ── fetch ─────────────────────────────────────────────────────────────────────
const fetchMateri = async () => {
  try {
    isLoading.value = true

    const modRes = await api.get(`/siswa/moduls/${slug}`)
    modul.value = modRes.data.data
    const courseContent = modul.value.materis.filter((m: any) => m.tipe !== 'post_test')

    const step = parseInt(materiId as string)
    if (!courseContent || !courseContent[step - 1]) {
      router.push({ name: 'siswa.modul.show', params: { slug } })
      return
    }

    const targetMateri = courseContent[step - 1]
    const response = await api.get(`/siswa/materis/${targetMateri.id}`)
    if (response.data.success) {
      materi.value = response.data.data
      questions.value = response.data.data.pre_tests || []
    }
  } catch (err) {
    console.error('Error fetching materi:', err)
    error('Gagal memuat Pre-Test')
    router.back()
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchMateri)
onUnmounted(() => { if (timerInterval.value) clearInterval(timerInterval.value) })

// ── quiz control ──────────────────────────────────────────────────────────────
const startQuiz = () => {
  isQuizStarted.value = true
  timeLeft.value = (materi.value?.durasi || 10) * 60

  timerInterval.value = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timerInterval.value)
      submitExam(true)
    }
  }, 1000)
}

const submitExam = async (isAuto = false) => {
  if (!isAuto && Object.keys(answers.value).length < soals.value.length) {
    if (!confirm('Anda belum menjawab semua soal. Kirim sekarang?')) return
  }

  if (isSubmitting.value) return

  try {
    isSubmitting.value = true
    if (timerInterval.value) clearInterval(timerInterval.value)

    const formattedAnswers: Record<number, string> = {}
    Object.keys(answers.value).forEach(key => {
      const val = answers.value[parseInt(key)]
      if (val !== undefined) formattedAnswers[parseInt(key)] = val
    })

    const response = await api.post(`/siswa/materis/${materi.value.id}/submit`, {
      answers: formattedAnswers,
      tipe_kuis: 'pre_test'
    })

    if (response.data.success) {
      router.replace({ name: 'siswa.modul.learning', params: { slug, mid: materiId } })
    }
  } catch (err) {
    console.error('Error submitting pre-test:', err)
    error('Terjadi kesalahan saat mengirim jawaban')
    isSubmitting.value = false
  }
}

const goBack = () => {
  if (isQuizStarted.value) {
    if (!confirm('Pre-Test sedang berlangsung. Keluar sekarang? Progres Anda tidak akan tersimpan.')) return
  }
  router.push({ name: 'siswa.modul.learning', params: { slug, mid: materiId } })
}
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
              Pre-Test: {{ materi?.judul || 'Loading...' }}
            </h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ modul?.judul || 'LMS Siswa' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="hidden sm:flex flex-col items-end">
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Bait Tahfizh Al-Qur'an</span>
            <span class="text-xs font-bold text-[#006D3E]">Pre-Test Materi</span>
          </div>
          <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-[#006D3E]">
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
          <p class="text-gray-400 font-bold text-xs animate-pulse uppercase tracking-widest">Menyiapkan Soal Pre-Test...</p>
        </div>

        <div v-else-if="materi" class="animate-in fade-in slide-in-from-bottom-4 duration-700">

          <!-- Start Screen -->
          <div v-if="!isQuizStarted" class="max-w-2xl mx-auto bg-white rounded-[2.5rem] p-10 md:p-16 text-center shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center">
            <div class="w-20 h-20 bg-green-50 text-[#006D3E] rounded-[2rem] flex items-center justify-center mb-8">
              <span class="material-symbols-outlined text-4xl">quiz</span>
            </div>
            <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Pre-Test Siap Dikerjakan</h2>
            <p class="text-gray-500 font-medium mb-10 leading-relaxed">
              Anda akan mengerjakan Pre-Test untuk materi<br/>
              <span class="text-gray-900 font-bold">{{ materi.judul }}</span>
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
                <p class="text-xl font-black text-gray-900">{{ materi.durasi || 10 }}'</p>
              </div>
            </div>

            <button @click="startQuiz" class="px-10 py-4 bg-[#006D3E] text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-[#005a33] transition-all active:scale-95">
              MULAI PRE-TEST SEKARANG
            </button>
          </div>

          <!-- Quiz UI -->
          <div v-else class="flex flex-col lg:flex-row gap-8 w-full">

            <!-- Left: Sidebar -->
            <aside class="w-full lg:w-72 shrink-0 space-y-6">

              <!-- Timer -->
              <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm text-center">
                <div class="flex items-center justify-center gap-2 text-gray-400 mb-3">
                  <span class="material-symbols-outlined text-lg">schedule</span>
                  <span class="text-[9px] font-black uppercase tracking-widest">SISA WAKTU</span>
                </div>
                <div
                  class="text-4xl font-mono font-black tracking-tighter text-gray-900 tabular-nums mb-3"
                  :class="timeLeft < 60 ? 'text-red-600 animate-pulse' : ''"
                >
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
                  <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">PROGRESS</span>
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
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 leading-relaxed">{{ soals[activeQuestionIndex]?.soal }}</h3>
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
                    @click="submitExam(false)"
                    :disabled="isSubmitting"
                    class="px-8 py-3.5 bg-[#006D3E] text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-[#005a33] transition-all"
                  >
                    {{ isSubmitting ? 'MEMPROSES...' : 'SELESAI' }}
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
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: #f9fafb; }
::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
