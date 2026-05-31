<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

const modules = ref<any[]>([])
const isLoading = ref(false)

// Flash Message State
const flashMessage = ref({
  show: false,
  message: '',
  type: 'success', // 'success' | 'error'
  progress: 100
})

let flashTimer: any = null

const showFlash = (msg: string, type: 'success' | 'error' = 'success') => {
  flashMessage.value.message = msg
  flashMessage.value.type = type
  flashMessage.value.show = true
  flashMessage.value.progress = 100
  
  if (flashTimer) clearInterval(flashTimer)
  
  const duration = 3000 // 3 seconds
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

const fetchModules = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/pengajar/moduls')

    if (response.data.success) {
      modules.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching modules:', error)
    showFlash('Gagal memuat modul', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchModules()
})

const goToManage = (id: number) => {
  router.push({ name: 'pengajar.modul.manage', params: { id } })
}

const deleteModule = async (id: number, title: string) => {
  if (!confirm(`Apakah Anda yakin ingin menghapus modul "${title}"?`)) return

  try {
    isLoading.value = true
    const response = await api.delete(`/pengajar/moduls/${id}`)
    if (response.data.success) {
      showFlash(`Modul "${title}" berhasil dihapus`)
      fetchModules()
    }
  } catch (error) {
    console.error('Error deleting module:', error)
    showFlash('Gagal menghapus modul', 'error')
  } finally {
    isLoading.value = false
  }
}

const goToCreate = () => {
  router.push({ name: 'pengajar.modul.create' })
}

// Delete Confirm Modal
const deleteConfirm = ref({
  show: false,
  id: null as number | null,
  title: ''
})

const openDeleteConfirm = (id: number, title: string) => {
  deleteConfirm.value = { show: true, id, title }
}

const closeDeleteConfirm = () => {
  deleteConfirm.value = { show: false, id: null, title: '' }
}

const confirmDeleteModule = async () => {
  if (!deleteConfirm.value.id) return
  const id = deleteConfirm.value.id
  const title = deleteConfirm.value.title
  closeDeleteConfirm()
  try {
    isLoading.value = true
    const response = await api.delete(`/pengajar/moduls/${id}`)
    if (response.data.success) {
      showFlash(`Modul "${title}" berhasil dihapus`)
      fetchModules()
    }
  } catch (error) {
    console.error('Error deleting module:', error)
    showFlash('Gagal menghapus modul', 'error')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-700 relative">
    
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
        <!-- Progress Bar -->
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div 
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#8B2323]'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteConfirm.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeDeleteConfirm"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 mb-2">Hapus Modul?</h3>
        <p class="text-sm text-center text-gray-500 mb-8 leading-relaxed">
          Apakah Anda yakin ingin menghapus modul <strong class="text-gray-800">{{ deleteConfirm.title }}</strong>? Semua materi di dalamnya juga akan terhapus.
        </p>
        <div class="flex gap-4">
          <button @click="closeDeleteConfirm" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="confirmDeleteModule" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-red-500 hover:bg-red-600 transition-all shadow-md flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">delete</span>
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Hero Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
      <div>
        <h3 class="font-headline text-3xl md:text-4xl font-extrabold text-[#8B2323] mb-2 tracking-tight">Modul</h3>
        <p class="text-gray-500 max-w-2xl font-body text-sm md:text-base leading-relaxed">Mengelola modul belajar bahasa Arab, dan bahan bacaan untuk siswa secara praktis</p>
      </div>
      <button 
        @click="goToCreate"
        class="w-full md:w-auto bg-[#8B2323] text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:shadow-2xl active:scale-95 transition-all text-center flex items-center justify-center gap-2"
      >
        <span class="material-symbols-outlined text-lg">add_circle</span>
        Tambah Modul
      </button>
    </div>

    <!-- Loading State (Skeleton Cards) -->
    <div v-if="isLoading && modules.length === 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="i in 3" :key="i" class="bg-white rounded-[2.5rem] overflow-hidden border-t-4 border-[#8B2323] animate-pulse">
        <div class="h-56 bg-gray-100"></div>
        <div class="p-8 space-y-4">
          <div class="h-4 bg-gray-100 rounded-full w-3/4"></div>
          <div class="h-3 bg-gray-100 rounded-full w-1/2"></div>
          <div class="h-3 bg-gray-100 rounded-full w-full"></div>
          <div class="h-3 bg-gray-100 rounded-full w-2/3"></div>
          <div class="flex justify-between items-center pt-4 border-t border-gray-50">
            <div class="h-3 bg-gray-100 rounded-full w-1/4"></div>
            <div class="flex gap-2">
              <div class="w-10 h-10 bg-gray-100 rounded-xl"></div>
              <div class="w-10 h-10 bg-gray-100 rounded-xl"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-10">
      <div 
        v-for="(mod, i) in modules" 
        :key="i" 
        @click="goToManage(mod.id)"
        class="bg-white rounded-[2.5rem] overflow-hidden shadow-[0_20px_50px_-12px_rgba(139,35,35,0.08)] flex flex-col group transition-all hover:bg-red-50/30 border border-gray-100 hover:border-[#8B2323]/20 cursor-pointer"
      >
        <div class="relative h-56 w-full bg-gray-50 p-6 overflow-hidden">
          <img v-if="mod.thumbnail_url" :src="mod.thumbnail_url" :alt="mod.judul" class="w-full h-full object-cover rounded-3xl shadow-lg transition-opacity duration-300 group-hover:opacity-90">
          <div v-else class="w-full h-full flex items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl">
            <span class="material-symbols-outlined text-4xl text-gray-200">menu_book</span>
          </div>
          <div class="absolute top-8 right-8 text-white text-[9px] font-black px-4 py-2 rounded-xl uppercase tracking-widest bg-[#8B2323]/80 backdrop-blur-md shadow-xl">{{ mod.level }}</div>
        </div>
        <div class="p-8 flex flex-col flex-1">
          <h4 class="font-headline text-xl font-black text-gray-900 mb-2 group-hover:text-[#8B2323] transition-colors leading-tight">{{ mod.judul }}</h4>
          <div class="mb-5">
            <span class="text-[#8B2323] font-black text-2xl" dir="rtl">{{ mod.arabic_title }}</span>
          </div>
          <p class="text-gray-400 text-sm mb-8 flex-1 line-clamp-3 leading-relaxed">{{ mod.deskripsi }}</p>
          <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
            <div class="flex items-center gap-3 text-[#8B2323]/40 text-[10px] font-black uppercase tracking-widest">
              <span class="material-symbols-outlined text-lg">auto_stories</span>
              <span>{{ mod.materis_count || 0 }} Materi</span>
            </div>
            <div class="flex gap-2">
              <button class="bg-gray-50 w-10 h-10 rounded-xl text-gray-400 hover:bg-[#8B2323] hover:text-white transition-all shadow-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">edit</span>
              </button>
              <button @click.stop="openDeleteConfirm(mod.id, mod.judul)" class="bg-gray-50 w-10 h-10 rounded-xl text-gray-400 hover:bg-black hover:text-white transition-all shadow-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!isLoading && modules.length === 0" class="py-32 flex flex-col items-center text-center space-y-6">
        <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200">
            <span class="material-symbols-outlined text-5xl">folder_off</span>
        </div>
        <div class="space-y-2">
            <h4 class="font-black text-gray-900 uppercase tracking-widest">Belum ada Modul</h4>
            <p class="text-sm text-gray-400">Mulailah dengan membuat modul baru untuk siswa Anda.</p>
        </div>
    </div>

    <!-- Footer Info -->
    <div v-if="modules.length > 0" class="mt-16 flex flex-col items-center">
       <p class="text-gray-300 text-[10px] font-black uppercase tracking-[0.3em]">Menampilkan {{ modules.length }} Masterpiece</p>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
