<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const moduleId = route.params.id

interface Materi {
  id: number
  judul: string
  tipe: string
  deskripsi: string
  file_path: string
  link_url: string
  durasi: number
  soals_count?: number
}

const moduleData = ref({
  id: 0,
  judul: '',
  arabic_title: '',
  deskripsi: '',
  materis: [] as Materi[]
})

const isLoading = ref(false)
const isSaving = ref(false)
const activeTab = ref('content') // Default to content management

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

const fetchModule = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/pengajar/moduls/${moduleId}`)
    if (response.data.success) {
      moduleData.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching module:', error)
    showFlash('Gagal memuat modul', 'error')
  } finally {
    isLoading.value = false
  }
}

const updateOverview = async () => {
  try {
    isSaving.value = true
    await api.post(`/pengajar/moduls/${moduleId}`, {
      _method: 'PUT',
      judul: moduleData.value.judul,
      arabic_title: moduleData.value.arabic_title,
      deskripsi: moduleData.value.deskripsi
    })
    showFlash('Overview berhasil diperbarui')
  } catch (error) {
    console.error('Error updating overview:', error)
    showFlash('Gagal memperbarui overview', 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchModule()
})

const goToAddLesson = () => {
  router.push({ name: 'pengajar.modul.lesson.create', params: { id: moduleId } })
}

const deleteMaterial = async (id: number, title: string) => {
  deleteConfirmMateri.value = { show: true, id, title }
}

const closeDeleteConfirmMateri = () => {
  deleteConfirmMateri.value = { show: false, id: null, title: '' }
}

const confirmDeleteMateri = async () => {
  if (!deleteConfirmMateri.value.id) return
  const id = deleteConfirmMateri.value.id
  closeDeleteConfirmMateri()
  try {
    isLoading.value = true
    const response = await api.delete(`/pengajar/moduls/${moduleId}/materi/${id}`)
    if (response.data.success) {
      showFlash('Materi berhasil dihapus')
      fetchModule()
    }
  } catch (error) {
    console.error('Error deleting material:', error)
    showFlash('Gagal menghapus materi', 'error')
  } finally {
    isLoading.value = false
  }
}

const editMaterial = (m: Materi) => {
  router.push({ name: 'pengajar.modul.lesson.edit', params: { id: moduleId, mid: m.id } })
}

// Delete Confirm Modal for Materi
const deleteConfirmMateri = ref({
  show: false,
  id: null as number | null,
  title: ''
})

const courses = computed(() => {
  return moduleData.value.materis || []
})
</script>

<template>
  <div class="relative min-h-[calc(100vh-120px)] space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 font-['Plus_Jakarta_Sans']">
    
    <!-- Premium Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-red-900/10 animate-in slide-in-from-right-8">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#8B2323]'" class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Notification</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div 
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#8B2323]'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal (Materi) -->
    <div v-if="deleteConfirmMateri.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeDeleteConfirmMateri"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 mb-2">Hapus Materi?</h3>
        <p class="text-sm text-center text-gray-500 mb-8 leading-relaxed">
          Apakah Anda yakin ingin menghapus materi <strong class="text-gray-800">{{ deleteConfirmMateri.title }}</strong>?
        </p>
        <div class="flex gap-4">
          <button @click="closeDeleteConfirmMateri" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="confirmDeleteMateri" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-red-500 hover:bg-red-600 transition-all shadow-md flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">delete</span>
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </button>
        <div>
          <div class="flex items-center gap-2 text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">
            <span>Manajemen Modul</span>
            <span class="material-symbols-outlined text-[9px]">chevron_right</span>
            <span class="text-[#8B2323]">{{ moduleData.judul }}</span>
          </div>
          <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-none">Pengelolaan Modul</h2>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 p-1.5 bg-gray-100/50 rounded-2xl w-fit">
      <button 
        v-for="tab in ['overview', 'content']" 
        :key="tab"
        @click="activeTab = tab"
        :class="[
          activeTab === tab 
            ? 'bg-white text-[#8B2323] shadow-md' 
            : 'text-gray-400 hover:text-gray-600'
        ]"
        class="px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all"
      >
        {{ tab }}
      </button>
    </div>

    <div v-if="isLoading && moduleData.id === 0" class="flex flex-col items-center justify-center py-32 space-y-4">
        <div class="w-12 h-12 border-4 border-gray-100 border-t-[#8B2323] rounded-full animate-spin"></div>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 animate-pulse">Memuat Data Modul...</p>
    </div>

    <div v-else class="space-y-8">
      
      <!-- Overview Tab Content -->
      <div v-if="activeTab === 'overview'" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
         <div class="bg-white rounded-[2.5rem] shadow-xl shadow-red-900/5 border border-gray-50 p-8 md:p-12 space-y-8">
            <div class="flex items-center justify-between">
               <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                  <span class="w-1.5 h-6 bg-[#8B2323] rounded-full"></span>
                  Overview Modul
               </h3>
               <button 
                 @click="updateOverview"
                 :disabled="isSaving"
                 class="px-8 py-3 bg-[#8B2323] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-red-900/10 disabled:opacity-50"
               >
                 {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
               </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-3">
                  <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Modul</label>
                  <input v-model="moduleData.judul" type="text" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-[#8B2323] focus:bg-white transition-all font-bold text-gray-900 outline-none">
               </div>
               <div class="space-y-3">
                  <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Bahasa Arab</label>
                  <input v-model="moduleData.arabic_title" type="text" dir="rtl" class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-[#8B2323] focus:bg-white transition-all font-bold text-gray-900 outline-none text-right">
               </div>
            </div>

            <div class="space-y-3">
               <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi / Penjelasan Pelatihan</label>
               <textarea 
                 v-model="moduleData.deskripsi" 
                 rows="10" 
                 class="w-full px-8 py-6 rounded-3xl bg-gray-50 border-2 border-transparent focus:border-[#8B2323] focus:bg-white transition-all font-medium text-gray-900 outline-none resize-none leading-relaxed"
                 placeholder="Jelaskan apa yang akan dipelajari siswa di modul ini..."
               ></textarea>
            </div>
         </div>
      </div>

      <!-- Course Content Tab Content -->
      <div v-if="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6">
            <div class="flex items-center justify-between">
               <h3 class="text-xl font-black text-gray-900 mb-6 px-2 border-l-4 border-[#8B2323] pl-4">Materi Pembelajaran</h3>
               <button @click="goToAddLesson" class="bg-[#8B2323] text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-900/10 hover:shadow-xl transition-all flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">add</span>
                  Tambah Materi
               </button>
            </div>
            
            <div class="space-y-4">
              <div v-for="(lesson, i) in courses" :key="lesson.id" class="group bg-gray-50/50 p-5 rounded-3xl border border-gray-100 hover:bg-gray-100/50 hover:border-gray-200 transition-all flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6 flex-1 w-full">
                  <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-[#8B2323] shrink-0">
                    <span class="material-symbols-outlined">{{ lesson.tipe === 'video' ? 'play_circle' : 'article' }}</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-gray-900 group-hover:text-[#8B2323] transition-colors truncate">{{ lesson.judul }}</h4>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                      {{ lesson.tipe }}<span v-if="lesson.durasi && lesson.durasi > 0"> • {{ lesson.durasi }} Menit</span>
                    </p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3 shrink-0">
                  <button @click="editMaterial(lesson)" class="p-3 rounded-xl bg-white text-gray-400 hover:bg-[#8B2323] hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg">edit</span>
                  </button>
                  <button @click="deleteMaterial(lesson.id, lesson.judul)" class="p-3 rounded-xl bg-white text-gray-400 hover:bg-black hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg">delete</span>
                  </button>
                </div>
              </div>

              <div v-if="courses.length === 0" class="py-20 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                 <span class="material-symbols-outlined text-4xl text-gray-200 mb-2">post_add</span>
                 <p class="text-gray-400 font-bold text-sm">Belum ada materi pembelajaran.</p>
              </div>
            </div>
         </div>



    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
