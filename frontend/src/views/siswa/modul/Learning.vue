<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { usePageStore } from '@/stores/page'
import { useToast } from '@/composables/useToast'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const route = useRoute()
const router = useRouter()
const pageStore = usePageStore()
const { warning } = useToast()

const modul = ref<any>(null)
const currentMateri = ref<any>(null)
const isLoading = ref(true)

const isArabicMode = ref(false)
const t = (indonesian: string, arabic: string) => {
  return isArabicMode.value ? arabic : indonesian
}

// All materis from modul (no type filter - post_test type removed)
const courseContent = computed(() => modul.value?.materis || [])
const isSidebarOpen = ref(false)

// Map of materi_id -> progress for gated locking
const progressMap = ref<Record<number, any>>({})


const fetchModulAndMateri = async () => {
  try {
    isLoading.value = true

    // Fetch full modul to get sidebar list (now includes progress per materi)
    const modRes = await api.get(`/siswa/moduls/${route.params.slug}`)
    modul.value = modRes.data.data

    // Pre-populate progressMap from server data (survives page refresh)
    if (modul.value?.materis) {
      modul.value.materis.forEach((m: any) => {
        if (m.progress) {
          progressMap.value[m.id] = m.progress
        }
      })
    }

    // Fetch current materi details
    const step = parseInt(route.params.mid as string)
    if (!courseContent.value || !courseContent.value[step - 1]) {
      router.push({ name: 'siswa.modul.show', params: { slug: route.params.slug } })
      return
    }

    const targetMateri = courseContent.value[step - 1]

    const matRes = await api.get(`/siswa/materis/${targetMateri.id}`)
    const materiData = matRes.data.data

    if (materiData.has_pretest && (!materiData.progress || !materiData.progress.is_pre_test_done)) {
      router.replace({ name: 'siswa.modul.pretest', params: { slug: route.params.slug, mid: route.params.mid } })
      return
    }

    currentMateri.value = materiData
    isArabicMode.value = materiData.is_arabic == 1 || materiData.is_arabic === true || materiData.is_arabic === '1'
    // Store/update progress in map with fresh data from detail endpoint
    if (materiData.progress) {
      progressMap.value[materiData.id] = materiData.progress
    }
    // Mark all materis BEFORE current as unlocked (they were already accessed)
    for (let i = 0; i < step - 1; i++) {
      const m = courseContent.value[i]
      if (m && !progressMap.value[m.id]) {
        progressMap.value[m.id] = { is_post_test_done: true, is_pre_test_done: true, is_content_read: true }
      }
    }
    pageStore.setTitle(currentMateri.value.judul)
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    isLoading.value = false
  }
}

const isMateriFullyCompleted = (materi: any) => {
  if (!materi || !materi.progress) return false
  if (materi.has_posttest && !materi.progress.is_post_test_done) return false
  // For now, post-test is the only blocker implemented. If no post-test, it's completed when content read.
  return materi.progress.is_post_test_done || materi.progress.is_content_read || true // currently returning true to not block if no post test since content read API isn't called yet
}

const isCurrentMateriFullyCompleted = computed(() => {
  if (!currentMateri.value) return false;
  if (currentMateri.value.has_posttest && (!currentMateri.value.progress || !currentMateri.value.progress.is_post_test_done)) {
    return false;
  }
  return true;
})

const currentIndex = computed(() => {
  if (!courseContent.value || !currentMateri.value) return -1;
  return courseContent.value.findIndex((m: any) => m.id === currentMateri.value.id);
})

const nextMateri = computed(() => {
  if (currentIndex.value >= 0 && currentIndex.value < courseContent.value.length - 1) {
    return courseContent.value[currentIndex.value + 1];
  }
  return null;
})

const hasNextMateri = computed(() => nextMateri.value !== null)

const isMateriLocked = (index: number): boolean => {
  if (index === 0) return false; // Materi pertama selalu terbuka

  // Materi yang sedang dibuka — tidak pernah terkunci
  if (currentIndex.value >= 0 && index === currentIndex.value) return false;

  // Materi sebelum atau sama dengan materi saat ini — sudah dilewati, tidak terkunci
  if (currentIndex.value >= 0 && index <= currentIndex.value) return false;

  // Materi SETELAH yang sedang dibuka — cek apakah previous sudah selesai
  const prevMateri = courseContent.value[index - 1];
  if (!prevMateri) return true;

  // Jika materi sebelumnya tidak punya post-test → langsung terbuka
  if (!prevMateri.has_posttest) return false;

  // Cek dari progressMap apakah post-test materi sebelumnya sudah selesai
  const prevProgress = progressMap.value[prevMateri.id];
  return !prevProgress || !prevProgress.is_post_test_done;
}

const switchMateri = (materi: any) => {
  const index = courseContent.value.findIndex((m: any) => m.id === materi.id)
  if (isMateriLocked(index)) {
     warning('Anda harus menyelesaikan materi sebelumnya terlebih dahulu!')
     return
  }
  const needsPretest = materi.has_pretest && (!materi.progress || !materi.progress.is_pre_test_done)
  if (needsPretest) {
    router.push({ name: 'siswa.modul.pretest', params: { slug: route.params.slug, mid: index + 1 } })
  } else {
    router.push({ name: 'siswa.modul.learning', params: { slug: route.params.slug, mid: index + 1 } })
  }
}

// Navigate to next materi directly (from Next button - no lock check needed since we already verified current is done)
const goToNextMateri = () => {
  if (!nextMateri.value) return
  const index = courseContent.value.findIndex((m: any) => m.id === nextMateri.value.id)
  
  const progress = progressMap.value[nextMateri.value.id] || nextMateri.value.progress
  const needsPretest = nextMateri.value.has_pretest && (!progress || !progress.is_pre_test_done)
  
  if (needsPretest) {
    router.push({ name: 'siswa.modul.pretest', params: { slug: route.params.slug, mid: index + 1 } })
  } else {
    router.push({ name: 'siswa.modul.learning', params: { slug: route.params.slug, mid: index + 1 } })
  }
}

onMounted(() => {
  fetchModulAndMateri()
})

// Watch for route changes to re-fetch when switching materis
// Re-fetch when mid changes (switching materis)
watch(() => route.params.mid, () => {
  fetchModulAndMateri()
})

// Also re-fetch when returning from PostTest (same mid, route changes)
watch(() => route.name, (newName, oldName) => {
  if (newName === 'siswa.modul.learning' && oldName === 'siswa.modul.post_test') {
    fetchModulAndMateri()
  }
})

const progressPercent = computed(() => {
  if (!modul.value || !modul.value.materis_count) return 0
  return 0 
})
</script>

<template>
  <div class="flex h-screen bg-white font-['Plus_Jakarta_Sans'] overflow-hidden">
    
    <!-- Sidebar: Course Navigation -->
    <aside 
      :class="[isSidebarOpen ? 'w-80' : 'w-0']"
      class="bg-gray-50 border-r border-gray-100 flex flex-col transition-all duration-300 overflow-hidden"
    >
      <div class="p-6 border-b border-gray-100 shrink-0">
        <button @click="router.push({ name: 'siswa.modul.show', params: { slug: route.params.slug } })" class="flex items-center gap-2 text-gray-400 hover:text-[#006D3E] mb-4 text-xs font-bold transition-colors">
          <span class="material-symbols-outlined text-sm">arrow_back</span>
          {{ t('KEMBALI KE MODUL', 'العودة إلى الوحدة') }}
        </button>
        <h2 class="text-sm font-black text-gray-900 line-clamp-1 uppercase tracking-wider">{{ modul?.judul }}</h2>
      </div>

      <div v-if="modul" class="flex-1 overflow-y-auto custom-scrollbar py-4 px-4 space-y-1">
        <div v-for="(materi, index) in courseContent" :key="materi.id" 
               @click="switchMateri(materi)"
               class="group relative flex items-center gap-4 p-4 rounded-lg cursor-pointer transition-all duration-300"
               :class="[
                  currentMateri?.id === materi.id ? 'bg-[#006D3E] text-white shadow-lg shadow-[#006D3E]/20' : 'hover:bg-[#006D3E]/5 hover:text-[#006D3E]',
                  isMateriLocked(Number(index)) ? 'opacity-50 grayscale cursor-not-allowed' : ''
               ]"
          >
            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 transition-all duration-300"
                 :class="currentMateri?.id === materi.id ? 'bg-white/20 text-white' : 'bg-white text-gray-400 group-hover:text-[#006D3E] shadow-sm'"
            >
              <span v-if="isMateriLocked(Number(index))" class="material-symbols-outlined text-sm">lock</span>
              <span v-else>{{ Number(index) + 1 }}</span>
            </div>
            <div class="flex-1 min-w-0" :dir="materi.is_arabic == 1 || materi.is_arabic === true ? 'rtl' : 'ltr'">
              <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-1">
                {{ (materi.is_arabic == 1 || materi.is_arabic === true) ? 'الدرس' : 'Pelajaran' }}
              </p>
              <p class="text-sm font-bold truncate leading-tight" :class="{'font-serif': materi.is_arabic == 1 || materi.is_arabic === true}">{{ materi.judul }}</p>
            </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
      
      <!-- Top Bar -->
      <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
          <button @click="isSidebarOpen = !isSidebarOpen" class="w-10 h-10 hover:bg-gray-50 rounded-xl transition-colors text-gray-400 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined">{{ isSidebarOpen ? 'menu_open' : 'menu' }}</span>
          </button>
          <h1 class="text-base font-black text-gray-900 hidden md:block" :class="{'font-serif': isArabicMode}" :dir="isArabicMode ? 'rtl' : 'ltr'">
          {{ t('Pelajaran', 'الدرس') }} {{ Number(courseContent.findIndex((c: any) => c.id === currentMateri?.id)) + 1 }}: {{ currentMateri?.judul }}
        </h1>
        </div>
        
        <div class="flex items-center gap-4">
           <div class="hidden sm:flex flex-col items-end">
              <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bait Tahfizh Al-Qur'an</span>
              <span class="text-xs font-bold text-[#006D3E]">Ridhallah</span>
           </div>
        </div>
      </header>

      <!-- Content Scroll -->
      <div class="flex-1 overflow-y-auto bg-gray-50/30 p-6 md:p-10 custom-scrollbar">
        <div class="max-w-4xl mx-auto space-y-8">
          
          <div v-if="isLoading" class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-gray-100 text-center text-gray-400 font-bold py-16">
            {{ t('Memuat materi...', 'جاري تحميل المادة...') }}
          </div>

          <div v-else-if="currentMateri" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
            
            <!-- Content Area -->
            <div>
              <!-- Video Section -->
            <div v-if="currentMateri.tipe === 'video'" class="bg-black rounded-[2.5rem] overflow-hidden shadow-2xl aspect-video mb-8 border-4 border-white">
               <iframe 
                 v-if="currentMateri.link_url"
                 :src="currentMateri.link_url.replace('watch?v=', 'embed/')" 
                 class="w-full h-full"
                 allowfullscreen
               ></iframe>
               <div v-else class="w-full h-full flex items-center justify-center text-white/20 flex-col gap-4">
                  <span class="material-symbols-outlined text-7xl">videocam_off</span>
                  <p class="font-bold tracking-widest uppercase text-xs">{{ t('Video tidak tersedia', 'الفيديو غير متوفر') }}</p>
               </div>
            </div>

            <!-- PDF / Content Section -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
               <!-- Header info -->
               <div class="p-8 md:p-10 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6" :dir="isArabicMode ? 'rtl' : 'ltr'">
                  <div>
                    <div class="flex items-center gap-3 mb-2">
                       <span class="px-3 py-1 bg-green-50 text-[#006D3E] text-[10px] font-black rounded-lg uppercase tracking-widest border border-green-100">
                         {{ currentMateri.tipe }}
                       </span>
                       <span class="text-[10px] font-black uppercase tracking-widest opacity-30">
                         {{ t('Pelajaran', 'الدرس') }} {{ Number(courseContent.findIndex((c: any) => c.id === currentMateri?.id)) + 1 }}
                       </span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight" :class="{'font-serif': isArabicMode}">{{ currentMateri.judul }}</h2>
                  </div>
                  <div v-if="currentMateri.tipe === 'post_test'" class="flex items-center gap-4 bg-gray-50 px-5 py-3 rounded-lg">
                     <span class="material-symbols-outlined text-[#006D3E]">timer</span>
                     <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ t('Batas Waktu', 'الوقت المحدد') }}</span>
                        <span class="text-sm font-black text-gray-900">{{ currentMateri.durasi || 0 }} {{ t('Menit', 'دقائق') }}</span>
                     </div>
                  </div>
               </div>
 
               <!-- Description/Text Content -->
               <div class="p-8 md:p-10 prose prose-teal max-w-none" :dir="isArabicMode ? 'rtl' : 'ltr'">
                  <div class="leading-relaxed font-medium" :class="{'font-serif text-2xl leading-loose': isArabicMode}" v-html="currentMateri.deskripsi"></div>
                  
                  <!-- PDF Embed (If file_path exists and is PDF) -->
                  <div v-if="currentMateri.file_path && currentMateri.tipe === 'dokumen'" class="mt-10 bg-gray-100 rounded-xl p-4 border border-gray-200">
                      <div class="flex items-center justify-between p-4 bg-white rounded-lg mb-4">
                         <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                               <span class="material-symbols-outlined text-3xl">picture_as_pdf</span>
                            </div>
                            <div>
                               <p class="text-sm font-black text-gray-900">{{ t('Dokumen Pendukung', 'المستند الداعم') }}</p>
                               <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">PDF Reader</p>
                            </div>
                         </div>
                         <a :href="currentMateri.file_path" target="_blank" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all">Download</a>
                      </div>
                      <iframe :src="currentMateri.file_path" class="w-full h-[600px] rounded-lg border-none"></iframe>
                  </div>
               </div>

               <!-- Final Actions -->
               <div class="p-8 md:p-10 border-t border-gray-50 bg-gray-50/50 flex flex-col gap-4">
                  
                  <div v-if="currentMateri.has_posttest" class="p-6 md:p-8 bg-white border border-gray-100 rounded-[2rem] flex flex-col items-center text-center shadow-sm">
                      <div class="w-16 h-16 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center mb-4">
                          <span class="material-symbols-outlined text-3xl">quiz</span>
                      </div>
                      <h3 class="text-lg font-black text-gray-900 mb-2" :class="{'font-serif': isArabicMode}">{{ t('Post-Test Materi', 'الاختبار البعدي للمادة') }}</h3>
                      <p class="text-sm text-gray-500 mb-6">{{ t('Uji pemahaman Anda setelah mempelajari materi ini.', 'اختبر فهمك بعد دراسة هذه المادة.') }}</p>
                      
                      <button v-if="!currentMateri.progress?.is_post_test_done"
                              @click="router.push({ name: 'siswa.modul.post_test', params: { slug: route.params.slug, mid: route.params.mid } })"
                              class="px-8 py-4 bg-[#006D3E] text-white rounded-lg font-black tracking-widest uppercase text-xs shadow-lg shadow-[#006D3E]/20 hover:-translate-y-1 transition-all">
                          {{ t('Kerjakan Post-Test', 'أداء الاختبار البعدي') }}
                      </button>
                      <div v-else class="px-8 py-4 bg-green-50 text-[#006D3E] border border-green-100 rounded-lg font-black tracking-widest flex items-center gap-2 text-sm">
                          <span class="material-symbols-outlined text-lg">check_circle</span>
                          {{ t('Selesai Dikerjakan', 'تم الانتهاء') }} ({{ t('Nilai', 'الدرجة') }}: {{ currentMateri.progress.post_test_score }})
                      </div>
                  </div>
                  
                  <button v-if="isCurrentMateriFullyCompleted && hasNextMateri"
                          @click="goToNextMateri()"
                          class="mt-4 px-8 py-5 bg-gray-900 text-white rounded-lg font-black tracking-widest uppercase text-sm w-full shadow-xl shadow-gray-900/20 hover:bg-black transition-all flex items-center justify-between">
                      <span>{{ t('Lanjut Materi Berikutnya', 'الذهاب إلى الدرس التالي') }}</span>
                      <span class="material-symbols-outlined" :class="{'rotate-180': isArabicMode}">arrow_forward</span>
                  </button>

                  <button v-if="isCurrentMateriFullyCompleted && !hasNextMateri"
                          @click="router.push({ name: 'siswa.modul.show', params: { slug: route.params.slug } })"
                          class="mt-4 px-8 py-5 bg-green-600 text-white rounded-lg font-black tracking-widest uppercase text-sm w-full shadow-xl shadow-green-600/20 hover:bg-green-700 transition-all flex items-center justify-between">
                      <span>{{ t('Selesai Belajar Modul Ini', 'إكمال دراسة هذه الوحدة') }}</span>
                      <span class="material-symbols-outlined" :class="{'rotate-180': isArabicMode}">done_all</span>
                  </button>
               </div>

            </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
