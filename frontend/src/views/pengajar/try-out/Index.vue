<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()
const exams = ref<any[]>([])
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

// Delete Confirmation State
const deleteConfirm = ref({
  show: false,
  id: null as number | null,
  title: ''
})

const confirmDelete = (id: number, title: string) => {
  deleteConfirm.value.id = id
  deleteConfirm.value.title = title
  deleteConfirm.value.show = true
}

const closeDeleteConfirm = () => {
  deleteConfirm.value.show = false
  deleteConfirm.value.id = null
  deleteConfirm.value.title = ''
}

const executeDelete = async () => {
  const id = deleteConfirm.value.id
  const title = deleteConfirm.value.title
  if (!id) return

  closeDeleteConfirm()

  try {
    isLoading.value = true
    const response = await api.delete(`/pengajar/try-out/${id}`)
    if (response.data.success) {
      showFlash(`Try Out "${title}" berhasil dihapus`, 'success')
      fetchExams()
    } else {
      showFlash(response.data.message || 'Gagal menghapus Try Out', 'error')
    }
  } catch (error: any) {
    console.error('Error deleting exam:', error)
    showFlash(error.response?.data?.message || 'Gagal menghapus Try Out', 'error')
  } finally {
    isLoading.value = false
  }
}

const fetchExams = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/pengajar/try-out')
    if (response.data.success) {
      exams.value = response.data.data.map((exam: any) => ({
        id: exam.id,
        title: exam.judul,
        desc: exam.deskripsi || 'No description provided.',
        duration: exam.durasi_menit,
        questions: exam.soals_count || 0,
        level: 'Intermediate', // Level bisa ditambahkan ke DB nanti
        levelBg: 'bg-[#BC4B4B] text-white',
        icon: 'menu_book',
        status: exam.is_active ? 'Upcoming' : 'Draft' // Status simulasi
      }))
    }
  } catch (error) {
    console.error('Gagal mengambil data ujian:', error)
    showFlash('Gagal mengambil data ujian', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchExams()
})

const goToManage = (id: number) => {
  router.push({ name: 'pengajar.try-out.manage', params: { id } })
}

const goToCreate = () => {
  router.push({ name: 'pengajar.try-out.create' })
}
</script>

<template>
  <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 relative">
    
    <!-- Premium Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[200] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-red-900/10 animate-in slide-in-from-right-8">
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

    <!-- Clean Delete Confirmation Dialog -->
    <div v-if="deleteConfirm.show" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
      <div class="bg-white rounded-[2rem] max-w-md w-full p-8 shadow-2xl border border-gray-200/80 space-y-5 text-center">
        <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center text-[#8B2323] mx-auto">
          <span class="material-symbols-outlined text-2xl">warning</span>
        </div>
        <div class="space-y-2">
          <h3 class="font-headline text-xl font-bold text-gray-900 leading-tight">Hapus Try Out?</h3>
          <p class="text-xs text-gray-500 leading-relaxed">Apakah Anda yakin ingin menghapus try out <strong>"{{ deleteConfirm.title }}"</strong>? Tindakan ini akan menghapus semua soal dan data hasil ujian siswa secara permanen.</p>
        </div>
        <div class="flex gap-3">
          <button 
            @click="closeDeleteConfirm"
            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold text-sm cursor-pointer"
          >
            Batal
          </button>
          <button 
            @click="executeDelete"
            class="flex-1 bg-[#8B2323] hover:bg-[#721c1c] text-white py-3 rounded-xl font-bold text-sm cursor-pointer"
          >
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- Hero Header -->
    <div class="space-y-2">
      <h2 class="text-3xl md:text-5xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Try Out</h2>
    </div>

    <!-- Main Content (Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Loading Skeleton Cards -->
      <template v-if="isLoading">
        <div v-for="i in 3" :key="'skeleton-' + i" class="animate-pulse bg-white p-8 rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(139,35,35,0.12)] border-t-4 border-gray-200 flex flex-col justify-between min-h-[300px]">
          <div class="flex justify-between items-start mb-8">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl"></div>
            <div class="w-24 h-6 bg-gray-100 rounded-full"></div>
          </div>
          <div class="space-y-3 flex-1 mb-8">
            <div class="h-6 bg-gray-100 rounded-lg w-3/4"></div>
            <div class="h-4 bg-gray-100 rounded-lg w-full"></div>
            <div class="h-4 bg-gray-100 rounded-lg w-5/6"></div>
          </div>
          <div class="w-full h-14 bg-gray-100 rounded-2xl"></div>
        </div>
      </template>

      <!-- Real Cards -->
      <template v-else>
        <div v-for="(exam, i) in exams" :key="i" class="group bg-white p-8 rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(139,35,35,0.12)] hover:bg-gray-50/70 transition-all border-t-4 border-[#8B2323] flex flex-col hover:border-[#8B2323]/25">
          <div class="flex justify-between items-start mb-8">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-[#8B2323] group-hover:bg-[#8B2323] group-hover:text-white transition-all duration-500 shadow-sm">
              <span class="material-symbols-outlined text-3xl">{{ exam.icon }}</span>
            </div>
            <span class="px-4 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest" :class="exam.levelBg">
              {{ exam.level }}
            </span>
          </div>
          <h3 class="text-xl font-headline font-extrabold text-gray-900 mb-2 truncate group-hover:text-[#8B2323] transition-colors">{{ exam.title }}</h3>
          <p class="text-sm text-gray-500 mb-8 leading-relaxed line-clamp-2">{{ exam.desc }}</p>
          <div class="flex gap-3 mt-auto">
            <button 
              @click="goToManage(exam.id)"
              class="flex-1 bg-[#8B2323] text-white py-4 rounded-2xl font-bold hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer"
            >
              Kelola Ujian
            </button>
            <button 
              @click="confirmDelete(exam.id, exam.title)"
              class="w-14 h-14 bg-gray-50 text-gray-400 hover:bg-black hover:text-white rounded-2xl transition-all flex items-center justify-center cursor-pointer shadow-sm shrink-0 border border-gray-100 hover:border-black active:scale-[0.95]"
              title="Hapus Ujian"
            >
              <span class="material-symbols-outlined text-2xl">delete</span>
            </button>
          </div>
        </div>
        <!-- Add Practice Card -->
        <div 
          @click="goToCreate"
          class="flex items-center justify-center p-8 rounded-[2.5rem] border-2 border-dashed border-gray-200 hover:border-[#8B2323]/50 hover:bg-red-50/10 transition-all group cursor-pointer text-center min-h-[300px]"
        >
          <div>
            <div class="w-16 h-16 bg-gray-50 mx-auto rounded-full flex items-center justify-center text-gray-300 group-hover:text-[#8B2323] group-hover:bg-red-50 transition-all mb-4">
              <span class="material-symbols-outlined text-4xl">add</span>
            </div>
            <p class="text-sm font-bold text-gray-400 group-hover:text-[#8B2323] uppercase tracking-widest">Tambah Ujian Baru</p>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
