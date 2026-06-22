<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

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
            <div :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#006D3E]'" class="w-10 h-10 rounded-lg flex items-center justify-center text-white shrink-0 shadow-lg">
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
            :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#006D3E]'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal (Materi) -->
    <div v-if="deleteConfirmMateri.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-transparent" @click="closeDeleteConfirmMateri"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-lg flex items-center justify-center mx-auto mb-6">
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
        <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-lg transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </button>
        <div>
          <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-none">Pengelolaan Modul</h2>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 p-1.5 bg-gray-100/50 rounded-lg w-fit">
      <button 
        v-for="tab in ['overview', 'content']" 
        :key="tab"
        @click="activeTab = tab"
        :class="[
          activeTab === tab 
            ? 'bg-white text-[#006D3E] shadow-md' 
            : 'text-gray-400 hover:text-gray-600'
        ]"
        class="px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all"
      >
        {{ tab }}
      </button>
    </div>

    <div v-if="isLoading && moduleData.id === 0" class="space-y-10">
      <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-12 space-y-8">
        <SkeletonLoader type="form" :rows="3" />
      </div>
      <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-12 space-y-6">
        <SkeletonLoader type="list" :rows="3" />
      </div>
    </div>

    <div v-else class="space-y-8">
      
      <!-- Overview Tab Content -->
      <div v-if="activeTab === 'overview'" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
         <div class="bg-white rounded-xl shadow-xl shadow-red-900/5 border border-gray-50 p-8 md:p-12 space-y-8">
            <div class="flex items-center justify-between">
               <h3 class="text-xl font-black text-gray-900 flex items-center gap-3">
                  <span class="w-1.5 h-6 bg-[#006D3E] rounded-lg"></span>
                  Overview Modul
               </h3>
               <button 
                 @click="updateOverview"
                 :disabled="isSaving"
                 class="px-8 py-3 bg-[#006D3E] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-red-900/10 disabled:opacity-50"
               >
                 {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
               </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
               <div class="space-y-3">
                  <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Modul</label>
                  <input v-model="moduleData.judul" type="text" class="w-full px-6 py-4 rounded-lg bg-gray-50 border-2 border-transparent focus:border-[#006D3E] focus:bg-white transition-all font-bold text-gray-900 outline-none">
               </div>
               <div class="space-y-3">
                  <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Bahasa Arab</label>
                  <input v-model="moduleData.arabic_title" type="text" dir="rtl" class="w-full px-6 py-4 rounded-lg bg-gray-50 border-2 border-transparent focus:border-[#006D3E] focus:bg-white transition-all font-bold text-gray-900 outline-none text-right">
               </div>
            </div>

            <div class="space-y-3">
               <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi / Penjelasan Pelatihan</label>
               <textarea 
                 v-model="moduleData.deskripsi" 
                 rows="10" 
                 class="w-full px-8 py-6 rounded-xl bg-gray-50 border-2 border-transparent focus:border-[#006D3E] focus:bg-white transition-all font-medium text-gray-900 outline-none resize-none leading-relaxed"
                 placeholder="Jelaskan apa yang akan dipelajari siswa di modul ini..."
               ></textarea>
            </div>
         </div>
      </div>

      <!-- Course Content Tab Content -->
      <div v-if="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6">
            <div class="flex items-center justify-between">
               <h3 class="text-xl font-black text-gray-900 px-2 border-l-4 border-[#006D3E] pl-4">Materi Pembelajaran</h3>
               <button @click="goToAddLesson" class="bg-[#006D3E] text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-red-900/10 hover:shadow-xl transition-all flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">add</span>
                  Tambah Materi
               </button>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75">
                      <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-16 text-center">No</th>
                      <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Judul Materi</th>
                      <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32">Tipe</th>
                      <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32">Durasi</th>
                      <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32 text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="(lesson, i) in courses" :key="lesson.id" class="hover:bg-gray-50/50 transition-colors">
                      <td class="py-4 px-6 text-center font-bold text-gray-600">{{ i + 1 }}</td>
                      <td class="py-4 px-6">
                        <div class="flex items-center gap-4">
                          <span class="material-symbols-outlined text-gray-400">{{ lesson.tipe === 'video' ? 'play_circle' : 'article' }}</span>
                          <span class="font-bold text-gray-900">{{ lesson.judul }}</span>
                        </div>
                      </td>
                      <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest" :class="lesson.tipe === 'video' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600'">
                          {{ lesson.tipe }}
                        </span>
                      </td>
                      <td class="py-4 px-6 font-semibold text-gray-600">
                        {{ lesson.durasi && lesson.durasi > 0 ? lesson.durasi + ' Menit' : '-' }}
                      </td>
                      <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                          <button @click="editMaterial(lesson)" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-[#006D3E] hover:text-white transition-all border border-gray-200 shadow-sm" title="Edit">
                            <span class="material-symbols-outlined text-sm">edit</span>
                          </button>
                          <button @click="deleteMaterial(lesson.id, lesson.judul)" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white transition-all border border-gray-200 shadow-sm" title="Hapus">
                            <span class="material-symbols-outlined text-sm">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="courses.length === 0">
                      <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-sm">
                        Belum ada materi pembelajaran.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
         </div>



    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
