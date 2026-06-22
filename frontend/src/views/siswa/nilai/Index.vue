<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const router = useRouter()

const grades = ref<any[]>([])
const tryoutGrades = ref<any[]>([])
const isLoading = ref(true)

const fetchGrades = async () => {
  try {
    isLoading.value = true
    const [kuisRes, tryoutRes] = await Promise.all([
      api.get('/siswa/grades'),
      api.get('/siswa/try-out-grades')
    ])
    
    grades.value = kuisRes.data.data || []
    tryoutGrades.value = tryoutRes.data.data || []
  } catch (error) {
    console.error('Error fetching grades:', error)
  } finally {
    isLoading.value = false
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// Group kuis grades by Modul
const groupedGrades = computed(() => {
  const groups: Record<number, { modul: any, materis: any[] }> = {}
  
  grades.value.forEach(grade => {
    const modul = grade.materi?.modul
    if (!modul) return
    const mid = modul.id
    if (!groups[mid]) {
      groups[mid] = {
        modul: modul,
        materis: []
      }
    }
    
    // If pre-test was done, push pre-test grade
    if (grade.is_pre_test_done && grade.pre_test_score !== null) {
      groups[mid].materis.push({
        id: `pre-${grade.id}`,
        materi_id: grade.materi_id,
        judul: `${grade.materi?.judul} (Pre-Test)`,
        skor: grade.pre_test_score,
        updated_at: grade.updated_at,
        tipe: 'pre_test',
        materi: grade.materi,
        modul: modul
      })
    }
    
    // If post-test was done, push post-test grade
    if (grade.is_post_test_done && grade.post_test_score !== null) {
      groups[mid].materis.push({
        id: `post-${grade.id}`,
        materi_id: grade.materi_id,
        judul: `${grade.materi?.judul} (Post-Test)`,
        skor: grade.post_test_score,
        updated_at: grade.updated_at,
        tipe: 'post_test',
        materi: grade.materi,
        modul: modul
      })
    }
  })
  
  return Object.values(groups)
})

const getMateriIndex = (grade: any) => {
  const modulObj = grade.modul || grade.materi?.modul
  if (!modulObj?.materis) return 1
  const idx = modulObj.materis.findIndex((m: any) => m.id === grade.materi_id)
  return idx !== -1 ? idx + 1 : 1
}

const goToDetail = (grade: any, slug: string) => {
  const mid = getMateriIndex(grade)
  if (grade.tipe === 'pre_test') {
    router.push({ name: 'siswa.modul.pre_test.result', params: { slug, mid } })
  } else {
    router.push({ name: 'siswa.modul.post_test.result', params: { slug, mid } })
  }
}

onMounted(() => {
  fetchGrades()
})
</script>

<template>
  <div class="space-y-12 animate-in fade-in duration-700">
    
    <!-- No large header, just a clean title -->
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm border border-gray-100 text-[#006D3E]">
        <span class="material-symbols-outlined text-2xl">workspace_premium</span>
      </div>
      <div>
        <h1 class="text-2xl font-black tracking-tight text-gray-900">Nilai Saya</h1>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Portal Prestasi Siswa</p>
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="isLoading" class="space-y-12">
      <!-- Tryout skeleton -->
      <div class="space-y-4">
        <div class="skeleton-text h-5 w-40 rounded bg-gray-200 animate-pulse"></div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <SkeletonLoader type="list" :rows="3" />
        </div>
      </div>
      <!-- Kuis skeleton -->
      <div class="space-y-4">
        <div class="skeleton-text h-5 w-40 rounded bg-gray-200 animate-pulse"></div>
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
          <SkeletonLoader type="list" :rows="4" />
        </div>
      </div>
    </div>

    <div v-else class="space-y-12">
      
      <!-- Nilai Tryout Section -->
      <section class="space-y-6">
        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight flex items-center gap-2">
          <span class="material-symbols-outlined text-[#006D3E]">assignment</span> Nilai Tryout
        </h2>

        <div v-if="tryoutGrades.length === 0" class="bg-white rounded-[2rem] p-10 text-center border-2 border-dashed border-gray-100">
          <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl text-gray-200">history_edu</span>
          </div>
          <h3 class="text-base font-bold text-gray-900 mb-1">Belum ada nilai Tryout</h3>
          <p class="text-xs text-gray-400 max-w-sm mx-auto">Selesaikan Tryout untuk melihat hasil evaluasi Anda di sini.</p>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div v-for="grade in tryoutGrades" :key="'to-'+grade.id" class="group bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 hover:bg-[#006D3E]/5 hover:border-[#006D3E]/20 transition-all duration-500 flex items-center gap-6">
            
            <div class="w-16 h-16 rounded-lg bg-gray-50 flex flex-col items-center justify-center border border-gray-100 shrink-0">
              <span class="text-2xl font-black text-gray-900 leading-none">{{ grade.nilai }}</span>
              <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Skor</span>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ formatDate(grade.selesai_at) }}</span>
              </div>
              <h4 class="text-lg font-black text-gray-900 truncate">{{ grade.try_out?.judul || grade.tryOut?.judul || 'Tryout' }}</h4>
            </div>

            <button 
              @click="router.push({ name: 'siswa.try-out.result', params: { id: grade.try_out_id } })"
              class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#006D3E] group-hover:text-white transition-all shrink-0 shadow-sm border border-gray-100"
              title="Lihat Detail Ujian"
            >
              <span class="material-symbols-outlined">visibility</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Divider -->
      <div class="h-px bg-gray-100 w-full"></div>

      <!-- Nilai Kuis Section (Grouped by Modul) -->
      <section class="space-y-6">
        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight flex items-center gap-2">
          <span class="material-symbols-outlined text-[#006D3E]">quiz</span> Nilai Kuis Modul
        </h2>

        <div v-if="groupedGrades.length === 0" class="bg-white rounded-[2rem] p-10 text-center border-2 border-dashed border-gray-100">
          <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl text-gray-200">history_edu</span>
          </div>
          <h3 class="text-base font-bold text-gray-900 mb-1">Belum ada nilai Kuis</h3>
          <p class="text-xs text-gray-400 max-w-sm mx-auto">Selesaikan kuis di dalam modul untuk melihat hasil evaluasi Anda di sini.</p>
        </div>

        <div v-else class="space-y-8">
          <div v-for="group in groupedGrades" :key="'modul-'+group.modul.id" class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
            
            <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
              <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                <span class="material-symbols-outlined text-[#006D3E] text-base">auto_stories</span>
                Modul: {{ group.modul.judul }}
              </h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-for="grade in group.materis" :key="'kuis-'+grade.id" class="group bg-white rounded-[1.5rem] p-4 border border-gray-100 hover:bg-[#006D3E]/5 hover:border-[#006D3E]/20 transition-all flex items-center gap-5">
                
                <div class="w-14 h-14 rounded-lg bg-gray-50 flex flex-col items-center justify-center border border-gray-100 shrink-0">
                  <span class="text-xl font-black text-gray-900 leading-none">{{ grade.skor }}</span>
                  <span class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Skor</span>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{{ formatDate(grade.updated_at) }}</span>
                  </div>
                  <h4 class="text-base font-black text-gray-900 truncate">{{ grade.judul || grade.materi?.judul || 'Kuis' }}</h4>
                </div>
                
                <button 
                  @click="goToDetail(grade, group.modul.slug)"
                  class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#006D3E] group-hover:text-white transition-all shrink-0 shadow-sm border border-gray-100"
                  title="Lihat Detail Ujian"
                >
                  <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
              </div>
            </div>
            
          </div>
        </div>
      </section>

    </div>
  </div>
</template>
