<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const tryoutId = route.params.id
const soalId = route.params.sid
const isEdit = !!soalId

const soals = ref([
  {
    pertanyaan: '',
    gambar: '' as string,
    tipe: 'pilihan_ganda',
    pilihan_a: '',
    pilihan_b: '',
    pilihan_c: '',
    pilihan_d: '',
    jawaban: 'a',
    bobot: 1,
    urutan: null as number | null,
    is_arabic: false
  }
])

const addQuestion = () => {
  soals.value.push({
    pertanyaan: '',
    gambar: '',
    tipe: 'pilihan_ganda',
    pilihan_a: '',
    pilihan_b: '',
    pilihan_c: '',
    pilihan_d: '',
    jawaban: 'a',
    bobot: 1,
    urutan: null,
    is_arabic: false
  })
}

const removeQuestion = (index: number) => {
  if (soals.value.length > 1) {
    soals.value.splice(index, 1)
  }
}

const isLoading = ref(false)
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
const uploadingIdx = ref<number | null>(null)

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

// Auto Scroll to Top
const showScrollTop = ref(false)

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 300
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  fetchSoal()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

// Upload gambar soal
const handleImageUpload = async (e: Event, idx: number) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  uploadingIdx.value = idx
  try {
    const formData = new FormData()
    formData.append('image', file)
    const res = await api.post('/pengajar/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (res.data.success) {
      const soal = soals.value[idx]
      if (soal) soal.gambar = res.data.url
      showFlash('Gambar berhasil diunggah')
    }
  } catch (err) {
    showFlash('Gagal mengunggah gambar', 'error')
  } finally {
    uploadingIdx.value = null
    target.value = ''
  }
}

const removeImage = (idx: number) => {
  const soal = soals.value[idx]
  if (soal) soal.gambar = ''
}

const fetchSoal = async () => {
  if (!isEdit) return
  try {
    isLoading.value = true
    const response = await api.get(`/pengajar/try-out/${tryoutId}`)
    if (response.data.success) {
      const dbSoals = response.data.data.soals || []
      const soal = dbSoals.find((s: any) => s.id == soalId)
      if (soal) {
        soals.value[0] = {
          pertanyaan: soal.pertanyaan,
          gambar: soal.gambar || '',
          tipe: soal.tipe,
          pilihan_a: soal.pilihan_a || '',
          pilihan_b: soal.pilihan_b || '',
          pilihan_c: soal.pilihan_c || '',
          pilihan_d: soal.pilihan_d || '',
          jawaban: soal.jawaban,
          bobot: soal.bobot,
          urutan: soal.urutan,
          is_arabic: !!soal.is_arabic
        }
      } else {
        showFlash('Soal tidak ditemukan', 'error')
        router.back()
      }
    }
  } catch (error) {
    console.error('Error fetching soal:', error)
    showFlash('Gagal memuat data soal', 'error')
  } finally {
    isLoading.value = false
  }
}

const saveSoal = async () => {
  const invalid = soals.value.find(s => !s.pertanyaan)
  if (invalid) {
    showFlash('Ada pertanyaan yang masih kosong!', 'error')
    return
  }

  try {
    isLoading.value = true
    
    if (isEdit) {
      const url = `/pengajar/try-out/${tryoutId}/soal/${soalId}`
      const response = await api.put(url, soals.value[0])
      if (response.data.success) {
        showFlash('Soal berhasil diperbarui')
        setTimeout(() => router.back(), 1000)
      }
    } else {
      const url = `/pengajar/try-out/${tryoutId}/soal/batch`
      const payload = { soals: soals.value }
      const response = await api.post(url, payload)
      if (response.data.success) {
        showFlash('Semua soal berhasil ditambahkan')
        setTimeout(() => router.back(), 1000)
      }
    }
  } catch (error: any) {
    console.error('Save error:', error)
    if (error.response && error.response.status === 422) {
      showFlash('Data tidak valid, periksa kembali inputan Anda', 'error')
    } else {
      showFlash('Gagal menyimpan soal', 'error')
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 font-['Plus_Jakarta_Sans']">
    
    <!-- Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-xl bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-blue-900/10 animate-in slide-in-from-right-8">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-[#006D3E]' : 'bg-red-500'" class="w-10 h-10 rounded-lg flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'verified_user' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Soal Alert</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div 
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-[#006D3E]' : 'bg-red-500'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-lg transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </button>
        <div>
          <div class="text-[9px] font-black text-[#006D3E] uppercase tracking-widest mb-0.5">Soal Builder</div>
          <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ isEdit ? 'Edit Soal' : 'Tambah Soal Baru' }}</h2>
        </div>
      </div>

      <div class="flex items-center gap-3">
         <button 
           @click="saveSoal"
           :disabled="isLoading"
           class="bg-[#006D3E] text-white px-8 py-3 rounded-lg font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:shadow-2xl transition-all disabled:opacity-50"
         >
           {{ isLoading ? 'Menyimpan...' : 'Simpan Soal' }}
         </button>
      </div>
    </div>

    <div class="space-y-8">
      <div v-for="(form, idx) in soals" :key="idx" class="bg-white rounded-2xl shadow-2xl shadow-red-900/5 border-t-4 border-[#006D3E] p-8 md:p-12 space-y-10 relative">
        
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-lg bg-gray-900 text-white flex items-center justify-center font-black text-lg shadow-lg">
                  {{ idx + 1 }}
              </div>
              <h4 class="font-black text-gray-400 text-[10px] uppercase tracking-widest">Pertanyaan Ke-{{ idx + 1 }}</h4>
          </div>
          <div class="flex items-center gap-3">
             <button 
               @click="form.is_arabic = !form.is_arabic"
               class="px-4 py-2 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all cursor-pointer shadow-sm flex items-center gap-2 border-2"
               :class="form.is_arabic ? 'bg-[#006D3E] text-white border-[#006D3E]' : 'bg-white text-[#006D3E] border border-[#006D3E]'"
             >
               <span class="material-symbols-outlined text-xs">language</span>
               {{ form.is_arabic ? 'Bahasa: Arab' : 'Bahasa: Indo' }}
             </button>
             <button v-if="!isEdit && soals.length > 1" @click="removeQuestion(idx)" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border-2 border-transparent">
                 <span class="material-symbols-outlined text-xl">delete</span>
             </button>
          </div>
        </div>

        <!-- Pertanyaan -->
        <div class="space-y-4">
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pertanyaan</label>
          <textarea v-model="form.pertanyaan" :dir="form.is_arabic ? 'rtl' : 'ltr'" :placeholder="form.is_arabic ? 'Tuliskan pertanyaan Anda di sini (Bahasa Arab)...' : 'Tuliskan pertanyaan Anda di sini...'" rows="5" class="w-full px-8 py-6 rounded-xl bg-gray-50 border-2 border-gray-200 focus:border-[#006D3E] focus:bg-white transition-all font-bold text-lg text-gray-900 outline-none resize-none leading-relaxed min-h-[120px]"></textarea>
        </div>

        <!-- Upload Gambar Soal -->
        <div class="space-y-4">
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Gambar Soal <span class="normal-case font-medium text-gray-300">(Opsional)</span></label>
          
          <!-- Preview Gambar -->
          <div v-if="form.gambar" class="relative group inline-block">
            <img :src="form.gambar" alt="Gambar Soal" class="max-h-64 rounded-lg shadow-lg border border-gray-100 object-contain" />
            <button 
              @click="removeImage(idx)" 
              class="absolute top-3 right-3 w-8 h-8 bg-red-500 text-white rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-lg hover:bg-red-600"
            >
              <span class="material-symbols-outlined text-base">close</span>
            </button>
          </div>

          <!-- Upload Button -->
          <div v-else>
            <label 
              :for="'img-upload-' + idx"
              class="flex flex-col items-center gap-3 w-full py-8 border-2 border-dashed border-gray-200 rounded-lg cursor-pointer hover:border-[#006D3E]/40 hover:bg-red-50/20 transition-all group"
            >
              <div v-if="uploadingIdx === idx" class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 border-4 border-gray-100 border-t-[#006D3E] rounded-lg animate-spin"></div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Mengunggah...</span>
              </div>
              <template v-else>
                <span class="material-symbols-outlined text-3xl text-gray-300 group-hover:text-[#006D3E] transition-colors">add_photo_alternate</span>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-[#006D3E] transition-colors">Unggah Gambar Soal</span>
                <span class="text-[9px] text-gray-300">PNG, JPG, GIF (Max 2MB)</span>
              </template>
            </label>
            <input 
              :id="'img-upload-' + idx" 
              type="file" 
              accept="image/*" 
              class="hidden" 
              @change="handleImageUpload($event, idx)"
              :disabled="uploadingIdx !== null"
            />
          </div>
        </div>

        <!-- Pilihan Ganda -->
        <div v-if="form.tipe === 'pilihan_ganda'" class="space-y-6">
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilihan Jawaban</label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="relative group">
                  <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center gap-3">
                      <button @click="form.jawaban = opt" type="button" class="w-8 h-8 rounded-lg font-black text-[10px] uppercase border-2 flex items-center justify-center transition-all" :class="form.jawaban === opt ? 'bg-[#006D3E] text-white border-[#006D3E] shadow-lg shadow-red-900/20' : 'bg-white text-gray-300 border-gray-100 group-hover:border-red-200'">
                        {{ form.is_arabic ? getArabicLabel(opt) : opt }}
                      </button>
                  </div>
                  <input v-model="(form as any)['pilihan_' + opt]" type="text" :dir="form.is_arabic ? 'rtl' : 'ltr'" :placeholder="form.is_arabic ? 'Opsi ' + getArabicLabel(opt) : 'Opsi ' + opt.toUpperCase()" class="w-full pl-20 pr-8 py-4 rounded-lg bg-gray-50 border-2 border-gray-200 focus:border-[#006D3E] focus:bg-white transition-all font-bold text-gray-900 outline-none shadow-sm">
              </div>
          </div>
          <p class="text-[10px] text-gray-400 font-bold ml-2 italic">* Klik huruf untuk menentukan Kunci Jawaban yang benar.</p>
        </div>

      </div>

      <button v-if="!isEdit" @click="addQuestion" class="w-full py-8 border-4 border-dashed border-gray-200 rounded-2xl text-gray-400 hover:text-[#006D3E] hover:border-red-100 hover:bg-red-50/30 transition-all font-black text-xs uppercase tracking-[0.3em] flex flex-col items-center justify-center gap-3">
          <span class="material-symbols-outlined text-4xl">add_circle</span>
          Tambah Soal Lain
      </button>
    </div>
  </div>

  <!-- Scroll to Top Button -->
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-4 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <button
      v-if="showScrollTop"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 z-50 w-12 h-12 bg-[#006D3E] text-white rounded-lg shadow-2xl shadow-red-900/30 flex items-center justify-center hover:bg-red-900 active:scale-95 transition-all"
      title="Scroll ke atas"
    >
      <span class="material-symbols-outlined text-xl">arrow_upward</span>
    </button>
  </transition>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
