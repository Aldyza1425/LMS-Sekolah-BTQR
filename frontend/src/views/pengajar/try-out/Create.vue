<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { success, error, warning } = useToast()

const form = ref({
  title: '',
  description: '',
  duration: 60,
  questions: 40,
  level: 'Intermediate',
  date: ''
})

const isLoading = ref(false)

const levels = [
  { label: 'Beginner', class: 'bg-green-100 text-green-700' },
  { label: 'Intermediate', class: 'bg-orange-100 text-orange-700' },
  { label: 'Advanced', class: 'bg-red-100 text-red-700' },
  { label: 'Mastery', class: 'bg-purple-100 text-purple-700' }
]

const saveExam = async () => {
  if (!form.value.title || !form.value.date) {
    warning('Judul dan tanggal ujian wajib diisi!')
    return
  }
  
  try {
    isLoading.value = true
    const response = await api.post('/pengajar/try-out', {
      judul: form.value.title,
      deskripsi: form.value.description,
      mulai_at: form.value.date,
      durasi_menit: form.value.duration,
      nilai_lulus: 70
    })

    if (response.data.success) {
      success('Ujian "' + form.value.title + '" berhasil dijadwalkan.')
      router.push({ name: 'pengajar.try-out' })
    }
  } catch (err: any) {
    console.error('Gagal menyimpan ujian:', err)
    if (err.response && err.response.status === 422) {
      const messages = Object.values(err.response.data.errors).flat().join(', ')
      error('Validasi Gagal: ' + messages)
    } else {
      error('Terjadi kesalahan saat menyimpan ujian. Silakan coba lagi.')
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <div class="text-[10px] font-bold text-[#8B2323] uppercase tracking-widest mb-1">Post Test Management</div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Tambah Ujian Baru</h2>
      </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-red-900/5 border-t-4 border-[#8B2323] p-8 md:p-12 space-y-10">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Side: Basic Info -->
        <div class="space-y-6">
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Judul Ujian</label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="Contoh: Nahwu Final Post Test"
              class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#8B2323] transition-all font-bold text-gray-900 placeholder:text-gray-300"
            >
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Deskripsi & Instruksi</label>
            <textarea 
              v-model="form.description"
              rows="4" 
              placeholder="Berikan gambaran singkat mengenai cakupan materi ujian ini..."
              class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#8B2323] transition-all font-medium text-gray-900 placeholder:text-gray-300 resize-none leading-relaxed"
            ></textarea>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Tanggal Pelaksanaan</label>
            <div class="relative">
               <input 
                v-model="form.date"
                type="date" 
                class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#8B2323] transition-all font-bold text-gray-900"
              >
            </div>
          </div>
        </div>

        <!-- Right Side: Specs -->
        <div class="space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Durasi (Menit)</label>
              <div class="relative">
                <input 
                  v-model="form.duration"
                  type="number" 
                  class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#8B2323] transition-all font-bold text-gray-900"
                >
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">MIN</span>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Jumlah Soal</label>
              <div class="relative">
                <input 
                  v-model="form.questions"
                  type="number" 
                  class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#8B2323] transition-all font-bold text-gray-900"
                >
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-gray-400">QS</span>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Tingkat Kesulitan</label>
            <div class="grid grid-cols-2 gap-3">
              <button 
                v-for="l in levels" :key="l.label"
                @click="form.level = l.label"
                class="py-4 px-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest transition-all border-2"
                :class="form.level === l.label ? 'border-[#8B2323] bg-red-50 text-[#8B2323]' : 'border-transparent bg-gray-50 text-gray-400 hover:bg-gray-100'"
              >
                {{ l.label }}
              </button>
            </div>
          </div>

          <div class="p-6 bg-red-50/50 rounded-3xl border border-red-100/50 mt-4">
            <p class="text-[10px] font-bold text-[#8B2323] uppercase tracking-[0.2em] mb-2 flex items-center gap-2">
              <span class="material-symbols-outlined text-sm">lightbulb</span>
              Penting
            </p>
            <p class="text-[11px] text-[#8B2323]/70 leading-relaxed font-medium">Ujian yang baru dibuat akan berstatus <span class="font-bold">Draft</span>. Anda perlu menambahkan butir soal di halaman pengelolaan sebelum mempublikasikannya.</p>
          </div>
        </div>
      </div>

      <div class="flex gap-4 pt-6 border-t border-gray-100">
        <button @click="router.back()" class="flex-1 py-5 rounded-2xl font-bold text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
          Batalkan
        </button>
        <button 
          @click="saveExam"
          class="flex-2 bg-[#8B2323] text-white px-12 py-5 rounded-2xl font-bold shadow-lg shadow-red-900/10 hover:shadow-xl active:scale-95 transition-all uppercase tracking-widest text-xs"
        >
          Buat Jadwal Ujian
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
