<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const moduleId = route.params.id
const materiId = route.params.mid
const isEdit = !!materiId

const form = ref({
  title: '',
  durasi: 0,
  content: '' // Used as Instructions
})

const hasQuiz = ref(true) // Always true for Post Test
const isLoading = ref(false)

interface QuizQuestion {
  id?: number;
  soal: string;
  a: string;
  b: string;
  c: string;
  d: string;
  jawaban: string;
  media_type: 'None' | 'Video' | 'PDF';
  video_type: 'Link' | 'Upload';
  video_link: string;
  video_file: File | null;
  pdf_file: File | null;
  [key: string]: any;
}

const quizzes = ref<QuizQuestion[]>([
  { 
    soal: '', a: '', b: '', c: '', d: '', jawaban: 'a',
    media_type: 'None', video_type: 'Link', video_link: '', video_file: null, pdf_file: null
  }
])

// Flash Message State
const flashMessage = ref({
  show: false,
  message: '',
  type: 'success',
  progress: 100
})

let flashTimer: any = null

const showFlash = (msg: string, type: 'success' | 'error' = 'success') => {
  flashMessage.value.message = msg
  flashMessage.value.type = type
  flashMessage.value.show = true
  flashMessage.value.progress = 100
  
  if (flashTimer) clearInterval(flashTimer)
  
  const duration = 3000
  const interval = 10
  const step = (interval / duration) * 100
  
  flashTimer = setInterval(() => {
    flashMessage.value.progress -= step
    if (flashMessage.value.progress <= 0) {
      flashMessage.value.show = false
      clearInterval(flashTimer)
    }
  }, interval)
}

const fetchPostTest = async () => {
  if (!isEdit) return
  try {
    isLoading.value = true
    const response = await api.get(`/pengajar/moduls/${moduleId}/materi/${materiId}`)
    if (response.data.success) {
      const data = response.data.data
      form.value.title = data.judul
      form.value.content = data.deskripsi || ''
      form.value.durasi = data.durasi || 0
      
      if (data.soals && data.soals.length > 0) {
        quizzes.value = data.soals.map((q: any) => ({
            ...q,
            media_type: q.media_type || 'None',
            video_type: q.video_link ? 'Link' : 'Upload',
            video_link: q.video_link || '',
            video_file: null,
            pdf_file: null
        }))
      }
    }
  } catch (error) {
    console.error('Error fetching Post Test:', error)
    showFlash('Gagal memuat data kuis', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchPostTest()
})

const addQuestion = () => {
  quizzes.value.push({ 
    soal: '', a: '', b: '', c: '', d: '', jawaban: 'a',
    media_type: 'None', video_type: 'Link', video_link: '', video_file: null, pdf_file: null
  })
}

const removeQuestion = (index: number) => {
  if (quizzes.value.length > 1) {
    quizzes.value.splice(index, 1)
  }
}

const handleMediaFile = (e: any, idx: number, type: 'Video' | 'PDF') => {
    const file = e.target.files[0]
    const q = quizzes.value[idx]
    if (q) {
        if (type === 'Video') {
            q.video_file = file
        } else {
            q.pdf_file = file
        }
    }
}

const savePostTest = async () => {
  if (!form.value.title) {
    showFlash('Judul Post Test wajib diisi!', 'error')
    return
  }

  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('judul', form.value.title)
    formData.append('deskripsi', form.value.content)
    formData.append('tipe', 'post_test')
    formData.append('durasi', form.value.durasi.toString())
    
    quizzes.value.forEach((q, index) => {
        formData.append(`quizzes[${index}][soal]`, q.soal)
        formData.append(`quizzes[${index}][a]`, q.a)
        formData.append(`quizzes[${index}][b]`, q.b)
        formData.append(`quizzes[${index}][c]`, q.c)
        formData.append(`quizzes[${index}][d]`, q.d)
        formData.append(`quizzes[${index}][jawaban]`, q.jawaban)
        
        formData.append(`quizzes[${index}][media_type]`, q.media_type)
        if (q.media_type === 'Video') {
            if (q.video_type === 'Link') {
                formData.append(`quizzes[${index}][video_link]`, q.video_link)
            } else if (q.video_file) {
                formData.append(`quizzes[${index}][video_file]`, q.video_file)
            }
        } else if (q.media_type === 'PDF' && q.pdf_file) {
            formData.append(`quizzes[${index}][pdf_file]`, q.pdf_file)
        }
    })
    
    const url = isEdit 
      ? `/pengajar/moduls/${moduleId}/materi/${materiId}`
      : `/pengajar/moduls/${moduleId}/materi`

    const response = await api.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (response.data.success) {
      showFlash('Post Test berhasil disimpan')
      setTimeout(() => router.back(), 500)
    }
  } catch (error: any) {
    console.error('Save error:', error)
    const errObj = error.response?.data?.errors;
    const errDetails = errObj ? JSON.stringify(errObj) : error.response?.data?.message || error.message;
    showFlash('Gagal: ' + errDetails, 'error')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 font-['Plus_Jakarta_Sans']">
    
    <!-- Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-blue-900/10 animate-in slide-in-from-right-8">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-blue-500' : 'bg-red-500'" class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'verified_user' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Post Test Alert</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div 
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-blue-500' : 'bg-red-500'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </button>
        <div>
          <div class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-0.5">Evaluation Builder</div>
          <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ isEdit ? 'Edit Post Test' : 'Tambah Post Test Baru' }}</h2>
        </div>
      </div>

      <div class="flex items-center gap-3">
         <button 
           @click="savePostTest"
           :disabled="isLoading"
           class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-900/20 hover:shadow-2xl transition-all disabled:opacity-50"
         >
           {{ isLoading ? 'Menyimpan...' : 'Simpan Kuis' }}
         </button>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-8">
      
      <!-- Primary Info -->
      <div class="bg-white rounded-[3rem] shadow-2xl shadow-blue-900/5 border border-gray-100 p-8 md:p-12 space-y-10">
         <div class="space-y-4">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Post Test / Kuis</label>
            <input v-model="form.title" type="text" placeholder="Contoh: Kuis Akhir Modul 1" class="w-full text-3xl font-black text-gray-900 placeholder:text-gray-100 outline-none border-none bg-transparent tracking-tight">
         </div>

         <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Instruksi Pengerjaan</label>
                <textarea v-model="form.content" rows="4" class="w-full px-8 py-6 rounded-3xl bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-medium text-gray-900 outline-none resize-none leading-relaxed" placeholder="Berikan instruksi atau arahan sebelum kuis dimulai..."></textarea>
            </div>
            <div class="space-y-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Batas Waktu (Menit)</label>
                <div class="relative">
                    <input v-model.number="form.durasi" type="number" class="w-full px-8 py-6 rounded-3xl bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-black text-2xl text-gray-900 outline-none">
                    <span class="absolute right-8 top-1/2 -translate-y-1/2 font-black text-gray-300 text-[10px] uppercase">Menit</span>
                </div>
                <p class="text-[9px] text-gray-400 italic">Siswa akan memiliki waktu terbatas untuk menjawab semua pertanyaan.</p>
            </div>
         </div>
      </div>

      <!-- Questions List -->
      <div class="space-y-6">
          <div class="flex items-center gap-4 px-2">
             <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined">format_list_numbered</span>
             </div>
             <h3 class="font-black text-gray-900 text-lg">Daftar Pertanyaan</h3>
             <span class="ml-auto text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ quizzes.length }} Pertanyaan Dibuat</span>
          </div>

          <div v-for="(q, idx) in quizzes" :key="idx" class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-blue-900/5 p-8 md:p-10 space-y-8 animate-in slide-in-from-bottom-4 duration-300">
              <div class="flex items-start justify-between gap-4">
                  <div class="flex items-center gap-4">
                      <div class="w-12 h-12 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-black text-lg shadow-lg">
                          {{ idx + 1 }}
                      </div>
                      <h4 class="font-black text-gray-400 text-[10px] uppercase tracking-widest">Pertanyaan Ke-{{ idx + 1 }}</h4>
                  </div>
                  <button @click="removeQuestion(idx)" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                      <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                  <!-- Left Side: Text and Options -->
                  <div class="lg:col-span-7 space-y-8">
                    <textarea v-model="q.soal" placeholder="Tuliskan pertanyaan Anda di sini..." class="w-full px-8 py-6 rounded-3xl bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-lg text-gray-900 outline-none resize-none leading-relaxed min-h-[120px]"></textarea>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center gap-3">
                                <button @click="q.jawaban = opt" class="w-8 h-8 rounded-lg font-black text-[10px] uppercase border-2 flex items-center justify-center transition-all" :class="q.jawaban === opt ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-900/20' : 'bg-white text-gray-300 border-gray-100 group-hover:border-blue-200'">{{ opt }}</button>
                            </div>
                            <input v-model="q[opt]" type="text" :placeholder="'Opsi ' + opt.toUpperCase()" class="w-full pl-20 pr-8 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all font-bold text-gray-900 outline-none shadow-sm">
                        </div>
                    </div>
                  </div>

                  <!-- Right Side: Media Attachments -->
                  <div class="lg:col-span-5 bg-gray-50/50 rounded-3xl p-6 border border-gray-100 space-y-6">
                      <div class="flex items-center gap-2 mb-2">
                          <span class="material-symbols-outlined text-blue-500">attachment</span>
                          <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Lampiran Media Soal</span>
                      </div>

                      <div class="flex gap-2 p-1 bg-white rounded-2xl border border-gray-100">
                          <button v-for="type in ['None', 'Video', 'PDF']" :key="type" @click="q.media_type = type as any" class="flex-1 py-3 rounded-xl font-black text-[9px] uppercase transition-all flex items-center justify-center gap-2" :class="q.media_type === type ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600'">
                             <span class="material-symbols-outlined text-lg">{{ type === 'Video' ? 'movie' : (type === 'PDF' ? 'picture_as_pdf' : 'block') }}</span>
                             {{ type }}
                          </button>
                      </div>

                      <div v-if="q.media_type === 'Video'" class="space-y-4 animate-in fade-in duration-300">
                          <div class="flex gap-2">
                             <button @click="q.video_type = 'Link'" class="flex-1 py-2 rounded-lg font-black text-[8px] uppercase border-2 transition-all" :class="q.video_type === 'Link' ? 'bg-white border-blue-500 text-blue-500' : 'border-transparent text-gray-400'">YouTube/Link</button>
                             <button @click="q.video_type = 'Upload'" class="flex-1 py-2 rounded-lg font-black text-[8px] uppercase border-2 transition-all" :class="q.video_type === 'Upload' ? 'bg-white border-blue-500 text-blue-500' : 'border-transparent text-gray-400'">File Video</button>
                          </div>
                          <input v-if="q.video_type === 'Link'" v-model="q.video_link" type="text" placeholder="https://..." class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-xs">
                          <input v-else @change="(e) => handleMediaFile(e, idx, 'Video')" type="file" accept="video/*" class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-xs">
                      </div>

                      <div v-if="q.media_type === 'PDF'" class="space-y-3 animate-in fade-in duration-300">
                          <label class="text-[8px] font-black text-gray-400 uppercase tracking-widest ml-1">Upload Dokumen PDF</label>
                          <input @change="(e) => handleMediaFile(e, idx, 'PDF')" type="file" accept="application/pdf" class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-xs">
                      </div>

                      <div v-if="q.media_type === 'None'" class="h-32 flex flex-col items-center justify-center text-center px-4 space-y-2 opacity-40">
                          <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                          <p class="text-[9px] font-bold text-gray-400 uppercase leading-relaxed">Tambahkan Video atau PDF untuk memperjelas pertanyaan ini</p>
                      </div>
                  </div>
              </div>
          </div>

          <button @click="addQuestion" class="w-full py-8 border-4 border-dashed border-gray-100 rounded-[2.5rem] text-gray-300 hover:text-blue-500 hover:border-blue-100 hover:bg-blue-50/30 transition-all font-black text-xs uppercase tracking-[0.3em] flex flex-col items-center justify-center gap-3">
              <span class="material-symbols-outlined text-4xl">add_circle</span>
              Tambah Pertanyaan Baru
          </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
