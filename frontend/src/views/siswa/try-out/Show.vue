<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const router = useRouter()
const route = useRoute()
const examId = route.params.id
const { error } = useToast()

const isArabicMode = ref(false)
const getArabicLabel = (opt: string) => {
  const map: Record<string, string> = {
    a: 'أ',
    b: 'ب',
    c: 'ج',
    d: 'د'
  }
  return map[opt] || opt
}

const isLoading = ref(true)
const isSubmitting = ref(false)
const exam = ref<any>({})
const questions = ref<any[]>([])

const showConfirmModal = ref(false)

const currentQuestionIndex = ref(0)
const answers = ref<Record<number, string>>({}) // soal_id -> jawaban
const timeLeft = ref(0)
let timerInterval: any = null

const fetchExam = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/siswa/try-out/${examId}`)
    if (response.data.success) {
      exam.value = response.data.data.tryout
      questions.value = response.data.data.soals
      
      // Initialize timer (durasi_menit to seconds)
      timeLeft.value = (exam.value.durasi_menit || 60) * 60
      startTimer()
    }
  } catch (err) {
    console.error('Error fetching exam:', err)
    error('Gagal memuat ujian')
    router.back()
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchExam()
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

const startTimer = () => {
  if (timerInterval) clearInterval(timerInterval)
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timerInterval)
      // Auto submit when time is up
      submitExam()
    }
  }, 1000)
}

const formattedTime = computed(() => {
  const m = Math.floor(timeLeft.value / 60)
  const s = timeLeft.value % 60
  return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`
})

const currentQuestion = computed(() => {
  return questions.value[currentQuestionIndex.value] || null
})

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++
  }
}

const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const selectAnswer = (jawaban: string) => {
  if (currentQuestion.value) {
    answers.value[currentQuestion.value.id] = jawaban
  }
}

const isAnswered = (index: number) => {
  const q = questions.value[index]
  return q && answers.value[q.id] !== undefined
}

const submitExam = async () => {
  if (isSubmitting.value) return
  
  if (timeLeft.value > 0 && !showConfirmModal.value) {
    showConfirmModal.value = true
    return
  }
  
  showConfirmModal.value = false

  try {
    isSubmitting.value = true
    if (timerInterval) clearInterval(timerInterval)

    // Format answers array for backend
    const payloadAnswers = Object.keys(answers.value).map(soalId => ({
      soal_id: parseInt(soalId),
      jawaban: answers.value[parseInt(soalId)]
    }))

    const response = await api.post(`/siswa/try-out/${examId}/submit`, {
      answers: payloadAnswers
    })

    if (response.data.success) {
      router.push({ name: 'siswa.nilai' })
    }
  } catch (err) {
    console.error('Error submitting exam:', err)
    error('Terjadi kesalahan saat mengirim jawaban')
    isSubmitting.value = false
  }
}

const cancelSubmit = () => {
  showConfirmModal.value = false
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-['Plus_Jakarta_Sans']">
    
    <!-- Top Navbar -->
    <header class="bg-white border-b border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-50">
      <div class="flex items-center gap-4">
        <div>
          <h1 class="font-extrabold text-gray-900 text-lg tracking-tight">{{ exam.judul || 'Memuat...' }}</h1>
          <p class="text-[10px] font-black uppercase tracking-widest text-[#005344] mt-0.5">Try Out Ujian</p>
        </div>
      </div>
      <div class="flex items-center gap-6">
        <div class="flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-xl font-bold">
          <span class="material-symbols-outlined text-xl">timer</span>
          <span class="text-lg tracking-wider">{{ formattedTime }}</span>
        </div>
      </div>
    </header>

    <div v-if="isLoading" class="flex-1 max-w-[1200px] w-full mx-auto px-6 py-8 flex gap-8">
      <aside class="w-72 flex-shrink-0 hidden lg:block">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 space-y-6">
          <SkeletonLoader type="list" :rows="2" />
        </div>
      </aside>
      <div class="flex-1 bg-white rounded-xl p-8 shadow-sm border border-gray-100">
        <SkeletonLoader type="detail" :rows="3" />
      </div>
    </div>

    <main v-else class="flex-1 max-w-[1200px] w-full mx-auto px-6 py-8 flex gap-8">
      
      <!-- Left Sidebar: Question Navigator -->
      <aside class="w-72 flex-shrink-0 space-y-6 hidden lg:block">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
          <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Navigasi Soal</h3>
          <div class="grid grid-cols-5 gap-3">
            <button 
              v-for="(q, idx) in questions" 
              :key="q.id"
              @click="currentQuestionIndex = idx"
              class="w-10 h-10 rounded-xl font-bold text-sm flex items-center justify-center transition-all border-2"
              :class="[
                currentQuestionIndex === idx 
                  ? 'border-[#005344] bg-[#005344] text-white shadow-lg shadow-teal-900/20' 
                  : isAnswered(idx)
                    ? 'border-[#005344] text-[#005344] bg-teal-50'
                    : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:bg-gray-50'
              ]"
            >
              {{ idx + 1 }}
            </button>
          </div>
          
          <div class="mt-8 pt-6 border-t border-gray-100 space-y-3">
            <div class="flex items-center gap-3 text-xs font-bold text-gray-500">
              <div class="w-4 h-4 rounded-md bg-[#005344]"></div> Sedang Dikerjakan
            </div>
            <div class="flex items-center gap-3 text-xs font-bold text-gray-500">
              <div class="w-4 h-4 rounded-md bg-teal-50 border-2 border-[#005344]"></div> Sudah Terjawab
            </div>
            <div class="flex items-center gap-3 text-xs font-bold text-gray-500">
              <div class="w-4 h-4 rounded-md bg-white border-2 border-gray-100"></div> Belum Terjawab
            </div>
          </div>
        </div>
      </aside>

      <!-- Main Content: Current Question -->
      <div class="flex-1 space-y-6">
        <div v-if="currentQuestion" class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 min-h-[400px] flex flex-col">
          <div class="flex items-center gap-3 mb-8">
            <span class="px-4 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-gray-100">
              Soal Ke-{{ currentQuestionIndex + 1 }}
            </span>
          </div>

          <p class="text-xl text-gray-900 font-medium leading-relaxed mb-6" :dir="currentQuestion.is_arabic ? 'rtl' : 'ltr'">
            {{ currentQuestion.pertanyaan }}
          </p>

          <!-- Gambar Soal -->
          <div v-if="currentQuestion.gambar" class="mb-8">
            <img :src="currentQuestion.gambar" alt="Gambar Soal" class="max-w-full max-h-72 rounded-lg shadow-md border border-gray-100 object-contain" />
          </div>

          <div class="space-y-4 flex-1">
            <button 
              v-for="opt in ['a', 'b', 'c', 'd']" 
              :key="opt"
              @click="selectAnswer(opt)"
              class="w-full text-left p-5 rounded-lg border-2 transition-all group flex items-start gap-4"
              :class="answers[currentQuestion.id] === opt ? 'border-[#005344] bg-teal-50 shadow-md' : 'border-gray-100 hover:border-[#005344]/30 hover:bg-gray-50'"
              :dir="currentQuestion.is_arabic ? 'rtl' : 'ltr'"
            >
              <div class="w-8 h-8 rounded-lg border-2 flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors"
                   :class="answers[currentQuestion.id] === opt ? 'border-[#005344] bg-[#005344] text-white' : 'border-gray-300 text-gray-400 group-hover:border-[#005344]/50'">
                <span class="text-xs font-black uppercase">{{ currentQuestion.is_arabic ? getArabicLabel(opt) : opt }}</span>
              </div>
              <span class="text-gray-700 font-medium leading-relaxed">{{ currentQuestion['pilihan_' + opt] }}</span>
            </button>
          </div>

          <div class="flex items-center justify-between mt-10 pt-8 border-t border-gray-100">
            <button 
              @click="prevQuestion" 
              :disabled="currentQuestionIndex === 0"
              class="px-6 py-3 rounded-xl font-bold text-sm transition-all flex items-center gap-2"
              :class="currentQuestionIndex === 0 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-100'"
            >
              <span class="material-symbols-outlined text-lg">arrow_back</span>
              Sebelumnya
            </button>
            <button 
              v-if="currentQuestionIndex < questions.length - 1"
              @click="nextQuestion" 
              class="px-6 py-3 rounded-xl font-bold text-sm bg-gray-900 text-white hover:bg-black transition-all shadow-md flex items-center gap-2"
            >
              Selanjutnya
              <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
            <button 
              v-else
              @click="submitExam" 
              class="px-6 py-3 rounded-xl font-bold text-sm bg-[#005344] text-white hover:bg-teal-900 transition-all shadow-md flex items-center gap-2"
            >
              Selesai Ujian
              <span class="material-symbols-outlined text-lg">check_circle</span>
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Custom Confirm Modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="cancelSubmit"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl">help</span>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 mb-2">Selesaikan Ujian?</h3>
        <p class="text-sm text-center text-gray-500 mb-8 leading-relaxed">
          Apakah Anda yakin ingin menyelesaikan ujian ini sekarang? Jawaban yang sudah dikirim tidak dapat diubah lagi.
        </p>
        <div class="flex gap-4">
          <button @click="cancelSubmit" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
            Kembali
          </button>
          <button @click="submitExam" :disabled="isSubmitting" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-[#005344] hover:bg-teal-900 transition-all shadow-md disabled:opacity-50 flex items-center justify-center gap-2">
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-lg animate-spin"></span>
            Selesai
          </button>
        </div>
      </div>
    </div>

  </div>
</template>
