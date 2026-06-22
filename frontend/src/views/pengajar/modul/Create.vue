<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { success, error, warning } = useToast()
const form = ref({
  title: '',
  arabic: '',
  level: 'Beginner',
  description: '',
  mata_pelajaran: 'Bahasa Arab' // Default
})

const levels = ['Beginner', 'Intermediate', 'Advanced']
const fileInput = ref<HTMLInputElement | null>(null)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const isLoading = ref(false)

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleImageChange = (e: any) => {
  const file = e.target.files[0]
  if (file) {
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const saveModule = async () => {
  if (!form.value.title) {
    warning('Judul modul wajib diisi!')
    return
  }

  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('judul', form.value.title)
    formData.append('arabic_title', form.value.arabic)
    formData.append('level', form.value.level)
    formData.append('deskripsi', form.value.description)
    formData.append('mata_pelajaran', form.value.mata_pelajaran)
    
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    const response = await api.post('/pengajar/moduls', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (response.data.success) {
      success('Modul berhasil disimpan.')
      router.push({ name: 'pengajar.modul' })
    }
  } catch (err: any) {
    console.error('Error saving module:', err)
    const message = err.response?.data?.message || 'Gagal menyimpan modul. Silakan periksa kembali data Anda.'
    error(message)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-lg transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <h2 class="text-2xl font-headline font-extrabold text-[#006D3E] tracking-tight">Tambah Modul</h2>
        <p class="text-gray-400 font-medium text-xs">Lengkapi detail informasi untuk kurikulum santri baru.</p>
      </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl shadow-xl shadow-red-900/5 border border-gray-100 overflow-hidden">
      <div class="p-6 md:p-10 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-[0.2em] ml-1">Judul Modul (Indonesia)</label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="Contoh: Nahwu Dasar"
              class="w-full px-6 py-4 rounded-lg bg-gray-100/50 border-2 border-transparent focus:border-[#006D3E] focus:bg-white transition-all font-extrabold text-gray-900 placeholder:text-gray-400"
            >
          </div>
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-[0.2em] ml-1">Judul Modul (Arab)</label>
            <input 
              v-model="form.arabic"
              type="text" 
              placeholder="Contoh: النحو الأساسي"
              dir="rtl"
              class="w-full px-6 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-[#006D3E] transition-all font-bold text-[#006D3E] text-xl placeholder:text-gray-400"
            >
          </div>
        </div>

        <!-- Level & Image -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-[0.2em] ml-1">Tingkat Kesulitan</label>
            <div class="flex gap-2 p-1.5 bg-gray-50 rounded-lg">
              <button 
                v-for="lvl in levels" :key="lvl"
                @click="form.level = lvl"
                class="flex-1 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition-all"
                :class="form.level === lvl ? 'bg-[#006D3E] text-white shadow-lg' : 'text-gray-500 hover:text-gray-700'"
              >
                {{ lvl }}
              </button>
            </div>
          </div>
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-[0.2em] ml-1">Foto Sampul (File)</label>
            <div 
              class="relative group border-2 border-dashed border-gray-100 rounded-[2rem] p-4 hover:border-[#006D3E] transition-all cursor-pointer bg-gray-50/50"
              @click="triggerFileInput"
            >
              <input 
                ref="fileInput"
                type="file" 
                @change="handleImageChange"
                accept="image/*"
                class="hidden"
              >
              
              <div v-if="imagePreview" class="relative h-48 w-full rounded-lg overflow-hidden shadow-md">
                <img :src="imagePreview" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="text-white font-bold text-xs">Ganti Foto</span>
                </div>
              </div>
              
              <div v-else class="h-48 w-full flex flex-col items-center justify-center space-y-4">
                <div class="w-16 h-16 rounded-lg bg-white shadow-sm flex items-center justify-center text-gray-300 group-hover:text-[#006D3E] transition-colors">
                  <span class="material-symbols-outlined text-4xl">menu_book</span>
                </div>
                <div class="text-center">
                  <p class="text-xs font-bold text-gray-400 group-hover:text-gray-600 transition-colors">Klik untuk upload foto sampul</p>
                  <p class="text-[10px] text-gray-300 mt-1">Logo modul akan digunakan jika kosong</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-[0.2em] ml-1">Deskripsi Singkat</label>
          <textarea 
            v-model="form.description"
            rows="4" 
            placeholder="Jelaskan apa yang akan dipelajari santri dalam modul ini..."
            class="w-full px-6 py-4 rounded-lg bg-gray-100/50 border-2 border-transparent focus:border-[#006D3E] focus:bg-white transition-all font-bold text-gray-900 placeholder:text-gray-400 resize-none"
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row gap-4 justify-end">
          <button @click="router.back()" class="px-10 py-4 rounded-lg font-bold text-gray-600 hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
            Batal
          </button>
          <button 
            @click="saveModule"
            :disabled="isLoading"
            class="px-10 py-4 rounded-lg font-bold bg-[#006D3E] text-white shadow-lg shadow-red-900/20 hover:shadow-xl active:scale-95 transition-all uppercase tracking-widest text-xs disabled:opacity-50"
          >
            {{ isLoading ? 'Menyimpan...' : 'Simpan Modul' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Info Tip -->
    <div class="bg-red-50 p-8 rounded-[2rem] flex gap-6 items-start border border-red-100/50">
      <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center text-[#006D3E] shadow-sm flex-shrink-0">
        <span class="material-symbols-outlined">lightbulb</span>
      </div>
      <div>
        <h4 class="font-bold text-[#006D3E] mb-1">Tips Pengajar</h4>
        <p class="text-sm text-[#006D3E]/70 leading-relaxed font-medium">Pastikan gambar sampel memiliki resolusi minimal 800x600 piksel untuk tampilan kartu yang optimal di dashboard siswa.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
