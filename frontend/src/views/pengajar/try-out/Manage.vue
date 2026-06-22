<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const examId = route.params.id

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

interface TryOutSoal {
  id: number;
  pertanyaan: string;
  tipe: string;
  jawaban: string;
  bobot: number;
  [key: string]: any;
}

const examData = ref<{ title: string, questions: TryOutSoal[], mulai_at?: string, selesai_at?: string }>({
  title: 'Loading...',
  questions: [],
  mulai_at: '',
  selesai_at: ''
})

const stats = ref({
  total_soal: 0,
  sudah_mengerjakan: 0,
  nilai_tertinggi: null as { nama: string, nilai: number } | null,
  durasi_menit: 0
})

const formatDate = (dateStr?: string) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }) + ' WIB'
}

const fetchExam = async () => {
  try {
    const response = await api.get(`/pengajar/try-out/${examId}`)
    if (response.data.success) {
      examData.value = {
        title: response.data.data.judul,
        questions: response.data.data.soals || [],
        mulai_at: response.data.data.mulai_at,
        selesai_at: response.data.data.selesai_at
      }
      if (response.data.stats) {
        stats.value = response.data.stats
      } else {
        stats.value.durasi_menit = response.data.data.durasi_menit || 0
      }
    }
  } catch (error) {
    console.error('Error fetching tryout:', error)
  }
}

onMounted(() => {
  fetchExam()
})

const addQuestion = () => {
  router.push({ name: 'pengajar.try-out.soal.create', params: { id: examId } })
}

const editQuestion = (soalId: number) => {
  router.push({ name: 'pengajar.try-out.soal.edit', params: { id: examId, sid: soalId } })
}

// Delete Confirm Modal
const deleteConfirmSoal = ref({
  show: false,
  id: null as number | null
})

const openDeleteConfirmSoal = (soalId: number) => {
  deleteConfirmSoal.value = { show: true, id: soalId }
}

const closeDeleteConfirmSoal = () => {
  deleteConfirmSoal.value = { show: false, id: null }
}

const deleteQuestion = async (soalId: number) => {
  openDeleteConfirmSoal(soalId)
}

const confirmDeleteSoal = async () => {
  if (!deleteConfirmSoal.value.id) return
  const soalId = deleteConfirmSoal.value.id
  closeDeleteConfirmSoal()
  try {
    const response = await api.delete(`/pengajar/try-out/${examId}/soal/${soalId}`)
    if (response.data.success) {
      showFlash('Soal berhasil dihapus')
      fetchExam()
    }
  } catch (err) {
    console.error('Error deleting soal:', err)
    showFlash('Gagal menghapus soal', 'error')
  }
}
</script>

<template>
  <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Premium Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-red-900/10">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-[#006D3E]' : 'bg-red-500'" class="w-10 h-10 rounded-lg flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'verified_user' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Notifikasi</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div class="h-full transition-all duration-100 ease-linear" :class="flashMessage.type === 'success' ? 'bg-[#006D3E]' : 'bg-red-500'" :style="{ width: flashMessage.progress + '%' }"></div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal (Soal) -->
    <div v-if="deleteConfirmSoal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-transparent" @click="closeDeleteConfirmSoal"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-lg flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 mb-2">Hapus Soal?</h3>
        <p class="text-sm text-center text-gray-500 mb-8 leading-relaxed">
          Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex gap-4">
          <button @click="closeDeleteConfirmSoal" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="confirmDeleteSoal" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-red-500 hover:bg-red-600 transition-all shadow-md flex items-center justify-center gap-2">
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
          <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
            <span>Ujian</span>
            <span class="material-symbols-outlined text-[10px]">chevron_right</span>
            <span class="text-[#006D3E]">{{ examData.title }}</span>
          </div>
          <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Kelola Detail Ujian</h2>
        </div>
      </div>
      <div class="flex gap-3 w-full md:w-auto">
        <button @click="addQuestion" class="flex-1 md:flex-none bg-[#006D3E] text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-red-900/10 hover:shadow-xl active:scale-95 transition-all flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-sm">add</span>
          Tambah Soal
        </button>
      </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Total Soal -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-50 text-blue-600">
          <span class="material-symbols-outlined">quiz</span>
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Soal</p>
          <p class="text-xl font-bold text-gray-900">{{ examData.questions.length }} Soal</p>
        </div>
      </div>

      <!-- Sudah Mengerjakan -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-50 text-green-600">
          <span class="material-symbols-outlined">fact_check</span>
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sudah Mengerjakan</p>
          <p class="text-xl font-bold text-gray-900">{{ stats.sudah_mengerjakan }} Siswa</p>
        </div>
      </div>

      <!-- Nilai Tertinggi -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4 min-w-0">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-yellow-50 text-yellow-600 flex-shrink-0">
          <span class="material-symbols-outlined">emoji_events</span>
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nilai Tertinggi</p>
          <p class="text-sm font-bold text-gray-900 truncate" :title="stats.nilai_tertinggi ? `${stats.nilai_tertinggi.nama} (${stats.nilai_tertinggi.nilai})` : 'Belum ada'">
            {{ stats.nilai_tertinggi ? `${stats.nilai_tertinggi.nama} (${stats.nilai_tertinggi.nilai})` : '-' }}
          </p>
        </div>
      </div>

      <!-- Durasi Ujian -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-orange-50 text-orange-600">
          <span class="material-symbols-outlined">timer</span>
        </div>
        <div>
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Durasi Ujian</p>
          <p class="text-xl font-bold text-gray-900">{{ stats.durasi_menit }} Min</p>
        </div>
      </div>
    </div>

    <!-- Jadwal Pelaksanaan Warning Note -->
    <div v-if="examData.mulai_at" class="bg-amber-50/70 border border-amber-200/50 p-6 rounded-xl flex gap-4 items-start animate-in fade-in slide-in-from-bottom-4 duration-500">
      <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-600 shrink-0">
        <span class="material-symbols-outlined text-xl">warning</span>
      </div>
      <div class="space-y-1">
        <h4 class="font-bold text-amber-900 text-sm">Peringatan Jadwal Pelaksanaan</h4>
        <p class="text-xs text-amber-700/95 leading-relaxed font-medium">
          Ujian ini dijadwalkan aktif mulai dari <span class="font-bold text-amber-900">{{ formatDate(examData.mulai_at) }}</span> hingga <span class="font-bold text-amber-900">{{ formatDate(examData.selesai_at) }}</span>. 
          Siswa hanya dapat mengakses dan mengerjakan soal selama jendela waktu tersebut. Pastikan seluruh butir soal sudah siap sebelum waktu mulai!
        </p>
      </div>
    </div>

    <!-- Question Bank List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="p-6 md:p-8">
        <div class="flex items-center justify-between mb-8 px-2 border-l-4 border-[#006D3E] pl-4">
           <h3 class="text-xl font-headline font-bold text-gray-900">Bank Soal Ujian</h3>
           <span class="text-[10px] font-extrabold px-3 py-1 bg-gray-50 text-gray-400 rounded-lg border border-gray-100 uppercase tracking-widest">{{ examData.questions.length }} Total Soal</span>
        </div>
        
        <div class="overflow-x-auto border border-gray-100 rounded-xl">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-gray-200 bg-gray-50/75">
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-16 text-center">No</th>
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Pertanyaan</th>
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32">Tipe</th>
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32">Kunci</th>
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-24 text-center">Bobot</th>
                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 w-32 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(q, i) in examData.questions" :key="q.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="py-4 px-6 text-center font-bold text-gray-600">{{ i + 1 }}</td>
                <td class="py-4 px-6">
                  <div class="space-y-2">
                    <p class="font-bold text-gray-900 leading-relaxed">{{ q.pertanyaan }}</p>
                    <div v-if="q.gambar" class="mt-2">
                      <img :src="q.gambar" alt="Gambar Soal" class="h-16 rounded-lg border border-gray-200 object-contain" />
                    </div>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <span class="px-2.5 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest" :class="q.tipe === 'pilihan_ganda' ? 'bg-teal-50 text-[#006D3E]' : 'bg-blue-50 text-blue-600'">
                    {{ q.tipe === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                  </span>
                </td>
                <td class="py-4 px-6 font-bold text-gray-700 uppercase">
                  {{ q.jawaban }}
                </td>
                <td class="py-4 px-6 text-center font-bold text-blue-600">
                  {{ q.bobot }}
                </td>
                <td class="py-4 px-6">
                  <div class="flex items-center justify-center gap-2">
                    <button @click="editQuestion(q.id)" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-[#006D3E] hover:text-white transition-all border border-gray-200 shadow-sm" title="Edit">
                      <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button @click="deleteQuestion(q.id)" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white transition-all border border-gray-200 shadow-sm" title="Hapus">
                      <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="examData.questions.length === 0">
                <td colspan="6" class="py-12 text-center text-gray-400 font-bold text-sm">
                  Belum ada butir soal ujian.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Add Question Placeholder -->
        <div 
          @click="addQuestion"
          class="mt-8 border-2 border-dashed border-gray-200 p-8 rounded-xl flex flex-col items-center justify-center text-center cursor-pointer hover:border-[#006D3E]/50 hover:bg-red-50/10 transition-all group"
        >
          <div class="w-16 h-16 bg-gray-50 rounded-lg flex items-center justify-center text-gray-300 group-hover:text-[#006D3E] transition-colors mb-4">
            <span class="material-symbols-outlined text-4xl">add_task</span>
          </div>
          <p class="font-bold text-gray-500 group-hover:text-[#006D3E] uppercase tracking-[0.2em] text-xs">Tambahkan Butir Soal Baru</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
