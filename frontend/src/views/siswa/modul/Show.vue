<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { usePageStore } from '@/stores/page'

const route = useRoute()
const router = useRouter()
const pageStore = usePageStore()
const modul = ref<any>(null)
const isLoading = ref(true)
const activeTab = ref('overview')

const fetchModul = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/siswa/moduls/${route.params.slug}`)
    if (response.data.success) {
      modul.value = response.data.data
      pageStore.setTitle(modul.value.judul)
    }
  } catch (error) {
    console.error('Error fetching modul:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchModul()
})

const goBack = () => {
  router.push({ name: 'siswa.modul' })
}

const courseContent = computed(() => {
  if (!modul.value?.materis) return []
  return modul.value.materis
})
const navigateToMateri = (materi: any, index: string | number) => {
  const needsPretest = materi.has_pretest && (!materi.progress || !materi.progress.is_pre_test_done)
  if (needsPretest) {
    router.push({ 
      name: 'siswa.modul.pretest', 
      params: { slug: modul.value.slug, mid: Number(index) + 1 } 
    })
  } else {
    router.push({ 
      name: 'siswa.modul.learning', 
      params: { slug: modul.value.slug, mid: Number(index) + 1 } 
    })
  }
}
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 font-['Plus_Jakarta_Sans'] pb-20">
    <!-- Top Header & Back -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <button @click="goBack" class="flex items-center gap-2 text-gray-400 hover:text-[#006D3E] transition-colors font-bold text-sm group w-fit">
        <span class="material-symbols-outlined transition-transform group-hover:-translate-x-1">arrow_back</span>
        Kembali ke Daftar Modul
      </button>
      <div v-if="modul" class="flex items-center gap-4">
         <span class="px-4 py-1.5 bg-[#006D3E]/5 text-[#006D3E] rounded-full text-[10px] font-black uppercase tracking-widest border border-[#006D3E]/10">
           {{ modul.level }}
         </span>
      </div>
    </div>

    <div v-if="isLoading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-[#006D3E] border-t-transparent"></div>
    </div>

    <div v-else-if="modul" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
      
      <!-- Sidebar / Info Card -->
      <div class="lg:col-span-4 space-y-8">
        <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100 flex flex-col h-fit sticky top-8">
          <div class="h-56 relative group">
            <img v-if="modul.thumbnail_url" :src="modul.thumbnail_url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div v-else class="w-full h-full bg-gray-50 flex items-center justify-center text-gray-200">
              <span class="material-symbols-outlined text-7xl">menu_book</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-6 left-8 right-8">
               <div class="text-white/80 font-bold text-3xl mb-1" dir="rtl">{{ modul.arabic_title }}</div>
               <h1 class="text-white text-xl font-black leading-tight">{{ modul.judul }}</h1>
            </div>
          </div>


        </div>
      </div>

      <!-- Main Content Area -->
      <div class="lg:col-span-8 space-y-8">
        <!-- Tab Navigation -->
        <div class="flex items-center gap-2 p-1.5 bg-gray-100/50 rounded-2xl w-fit">
          <button 
            v-for="tab in ['overview', 'content']" 
            :key="tab"
            @click="activeTab = tab"
            :class="[
              activeTab === tab 
                ? 'bg-white text-[#006D3E] shadow-sm' 
                : 'text-gray-400 hover:text-gray-600'
            ]"
            class="px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all"
          >
            {{ tab }}
          </button>
        </div>

        <!-- Tab Content -->
        <div class="min-h-[400px]">
          
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-8">
             <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-50">
                <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-3">
                   <span class="w-1.5 h-6 bg-[#006D3E] rounded-full"></span>
                   Tentang Pelatihan
                </h3>
                <p class="text-gray-600 leading-relaxed font-medium whitespace-pre-wrap">{{ modul.deskripsi || 'Belum ada deskripsi untuk modul ini.' }}</p>
             </div>
          </div>

          <!-- Course Content Tab -->
          <div v-if="activeTab === 'content'" class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6">
             <div v-for="(materi, index) in courseContent" :key="materi.id" 
                  @click="navigateToMateri(materi, index)"
                  class="bg-white rounded-3xl p-6 flex items-center gap-6 shadow-sm border border-gray-50 hover:bg-[#006D3E]/5 hover:border-[#006D3E]/20 transition-all group cursor-pointer"
             >
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center font-black text-gray-300 group-hover:bg-[#006D3E] group-hover:text-white transition-all shrink-0">
                  {{ Number(index) + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-900 group-hover:text-[#006D3E] transition-colors truncate">{{ materi.judul }}</h4>
                  <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <div class="flex items-center gap-1.5 text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                      <span class="material-symbols-outlined text-sm">{{ materi.tipe === 'video' ? 'play_circle' : 'article' }}</span>
                      {{ materi.tipe }}
                    </div>
                    <span v-if="materi.has_posttest" class="px-2 py-0.5 bg-orange-50 text-orange-500 border border-orange-100 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                      <span class="material-symbols-outlined text-[10px]">quiz</span>
                      Post-Test
                    </span>
                  </div>
                </div>
                <button class="w-10 h-10 rounded-xl bg-green-50 text-[#006D3E] flex items-center justify-center group-hover:bg-[#006D3E] group-hover:text-white transition-all shrink-0">
                  <span class="material-symbols-outlined">play_arrow</span>
                </button>
             </div>

             <div v-if="courseContent.length === 0" class="py-20 text-center bg-gray-50/50 rounded-[2.5rem] border border-dashed border-gray-200">
                <span class="material-symbols-outlined text-4xl text-gray-200 mb-2">auto_stories</span>
                <p class="text-gray-400 font-bold text-sm">Belum ada materi pembelajaran.</p>
             </div>
          </div>



        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
