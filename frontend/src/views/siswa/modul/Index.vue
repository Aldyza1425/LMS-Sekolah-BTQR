<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

interface Modul {
  id: number
  judul: string
  arabic_title: string
  deskripsi: string
  thumbnail_url: string
  level: string
  materis_count?: number
  progress?: number
}

const moduls = ref<Modul[]>([])
const isLoading = ref(false)

const fetchModuls = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/siswa/moduls')
    if (response.data.success) {
      moduls.value = response.data.data
    } else {
      // Fallback for dev if siswa endpoint not ready
      const fallback = await api.get('/admin/moduls')
      if (fallback.data.success) {
        moduls.value = fallback.data.data
      }
    }
  } catch (error) {
    console.error('Error fetching moduls:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchModuls()
})

const goToModul = (id: number) => {
  router.push({ name: 'siswa.modul.show', params: { slug: id } })
}
</script>

<template>
  <div class="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-700 font-['Plus_Jakarta_Sans']">
    <!-- Hero Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
      <div>
        <h3 class="font-headline text-3xl md:text-4xl font-extrabold text-[#006D3E] mb-2 tracking-tight">Modul Belajar</h3>
        <p class="text-gray-500 max-w-2xl font-body text-sm md:text-base leading-relaxed">
          Koleksi modul pembelajaran bahasa Arab yang dirancang untuk membantu Anda menguasai literatur dan tata bahasa secara bertahap.
        </p>
      </div>
    </div>

    <!-- Loading State (Skeleton Cards) -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="i in 3" :key="i" class="bg-white rounded-xl overflow-hidden border-t-4 border-[#006D3E] animate-pulse">
        <div class="h-48 bg-gray-100"></div>
        <div class="p-6 space-y-4">
          <div class="h-4 bg-gray-100 rounded-full w-3/4"></div>
          <div class="h-3 bg-gray-100 rounded-full w-1/2"></div>
          <div class="h-3 bg-gray-100 rounded-full w-full"></div>
          <div class="h-3 bg-gray-100 rounded-full w-2/3"></div>
          <div class="space-y-2 pt-2">
            <div class="flex justify-between">
              <div class="h-3 bg-gray-100 rounded-full w-24"></div>
              <div class="h-3 bg-gray-100 rounded-full w-10"></div>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full w-full"></div>
          </div>
          <div class="flex justify-between items-center pt-2 border-t border-gray-50">
            <div class="h-3 bg-gray-100 rounded-full w-20"></div>
            <div class="w-10 h-10 bg-gray-100 rounded-xl"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modul Grid (Matched with Admin style) -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-10">
      <div 
        v-for="(mod, i) in moduls" 
        :key="i" 
        @click="goToModul(mod.id)"
        class="bg-white rounded-xl overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,109,62,0.08)] flex flex-col group transition-all hover:bg-green-50/30 border border-gray-100 hover:border-[#006D3E]/20 border-t-4 border-t-[#006D3E] cursor-pointer"
      >
        <!-- Thumbnail -->
        <div class="relative h-48 w-full bg-gray-50 overflow-hidden">
          <img v-if="mod.thumbnail_url" :src="mod.thumbnail_url" :alt="mod.judul" class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-90">
          <div v-else class="w-full h-full flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-gray-200">menu_book</span>
          </div>
          <!-- Level Badge -->
          <div class="absolute top-4 right-4 text-white text-[9px] font-black px-3 py-1.5 rounded-lg uppercase tracking-[0.15em] bg-[#006D3E] shadow-lg">{{ mod.level }}</div>
        </div>

        <div class="p-6 flex flex-col flex-1 space-y-4">
          <!-- Title & Arabic -->
          <div>
            <h4 class="font-headline text-lg font-extrabold text-gray-900 group-hover:text-[#006D3E] transition-colors leading-tight mb-2">{{ mod.judul }}</h4>
            <div class="mb-3">
              <span class="text-[#006D3E] font-extrabold text-2xl" dir="rtl">{{ mod.arabic_title }}</span>
            </div>
          </div>

          <!-- Description -->
          <p class="text-gray-500 text-xs font-medium leading-relaxed flex-1 line-clamp-3">{{ mod.deskripsi }}</p>
          
          <!-- Progress Bar -->
          <div class="space-y-2.5 pt-2">
            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-wider">
              <span class="text-gray-400">Progres Belajar</span>
              <span class="text-[#006D3E]">{{ mod.progress || 0 }}%</span>
            </div>
            <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
              <div 
                class="h-full bg-gradient-to-r from-[#006D3E] to-[#00955A] rounded-full transition-all duration-1000" 
                :style="{ width: (mod.progress || 0) + '%' }"
              ></div>
            </div>
          </div>

          <!-- Bottom Meta -->
          <div class="flex items-center justify-between pt-4 border-t border-gray-50">
            <div class="flex items-center gap-2 text-gray-400 text-[10px] font-black uppercase tracking-[0.1em]">
              <span class="material-symbols-outlined text-base">auto_stories</span>
              <span>{{ mod.materis_count || 0 }} Lessons</span>
            </div>
            <button class="w-10 h-10 rounded-xl bg-green-50 text-[#006D3E] group-hover:bg-[#006D3E] group-hover:text-white transition-all shadow-sm flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-xl">play_arrow</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!isLoading && moduls.length === 0" class="py-32 text-center">
      <span class="material-symbols-outlined text-6xl text-gray-200 mb-4">menu_book</span>
      <h3 class="text-xl font-bold text-gray-400">Belum ada modul tersedia</h3>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
