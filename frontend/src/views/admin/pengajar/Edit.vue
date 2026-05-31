<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const { success, error } = useToast()

const id = route.params.id as string
const isLoading = ref(true)
const isSubmitting = ref(false)
const showPassword = ref(false)

const form = ref({
  name: '',
  email: '',
  phone: '',
  password: ''
})

const photoFile = ref<File | null>(null)
const photoPreview = ref<string>('')
const currentPhoto = ref<string>('')

const fetchPengajar = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/admin/pengajar/${id}`)
    const data = response.data.data
    form.value.name = data.name
    form.value.email = data.email
    form.value.phone = data.phone || ''
    if (data.photo) {
      currentPhoto.value = `/storage/${data.photo}`
    }
  } catch (err) {
    console.error('Failed to fetch pengajar:', err)
    error('Gagal memuat data pengajar.')
    router.push({ name: 'admin.pengajar' })
  } finally {
    isLoading.value = false
  }
}

const handlePhotoChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  photoFile.value = file
  const reader = new FileReader()
  reader.onload = (ev) => {
    photoPreview.value = ev.target?.result as string
  }
  reader.readAsDataURL(file)
}

const removePhoto = () => {
  photoFile.value = null
  photoPreview.value = ''
}

const handleUpdate = async () => {
  if (!form.value.name || !form.value.email) return

  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    formData.append('phone', form.value.phone)
    if (form.value.password) {
      formData.append('password', form.value.password)
    }
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }

    await api.post(`/admin/pengajar/${id}/update`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    success('Data pengajar berhasil diperbarui!')
    router.push({ name: 'admin.pengajar' })
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Gagal memperbarui data pengajar.'
    error(msg)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchPengajar()
})
</script>

<template>
  <div class="max-w-xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.push({ name: 'admin.pengajar' })" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <div class="text-[10px] font-bold text-[#2563EB] uppercase tracking-widest mb-1">Pengajar Management</div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Edit Pengajar</h2>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2563EB]"></div>
    </div>

    <!-- Form -->
    <div v-else class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 md:p-10 space-y-8">
      <!-- Photo Profile Upload -->
      <div class="flex flex-col items-center gap-4">
        <div class="relative group">
          <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-xl bg-gray-100 flex items-center justify-center">
            <img 
              v-if="photoPreview || currentPhoto" 
              :src="photoPreview || currentPhoto" 
              alt="Foto Profil" 
              class="w-full h-full object-cover"
            />
            <span v-else class="material-symbols-outlined text-4xl text-gray-300">person</span>
          </div>
          <label 
            for="photo-upload"
            class="absolute bottom-0 right-0 w-8 h-8 bg-[#2563EB] rounded-full flex items-center justify-center cursor-pointer shadow-lg hover:bg-blue-700 transition-colors"
          >
            <span class="material-symbols-outlined text-white text-sm">photo_camera</span>
          </label>
          <input id="photo-upload" type="file" accept="image/*" class="hidden" @change="handlePhotoChange" />
        </div>
        <div class="text-center">
          <p class="text-sm font-bold text-gray-700">Foto Profil</p>
          <p class="text-xs text-gray-400">Klik ikon kamera untuk mengganti foto</p>
          <button v-if="photoPreview" @click="removePhoto" class="mt-2 text-xs text-red-500 hover:text-red-700 font-bold transition-colors">
            Batalkan Perubahan Foto
          </button>
        </div>
      </div>

      <form @submit.prevent="handleUpdate" class="space-y-6">
        <!-- Nama Lengkap -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nama Lengkap</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">person</span>
            <input 
              v-model="form.name"
              type="text" 
              placeholder="Masukkan nama lengkap"
              class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#2563EB] transition-all font-bold text-gray-900 placeholder:text-gray-300"
              required
            />
          </div>
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Email</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">alternate_email</span>
            <input 
              v-model="form.email"
              type="email" 
              placeholder="nama@email.com"
              class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#2563EB] transition-all font-bold text-gray-900 placeholder:text-gray-300"
              required
            />
          </div>
        </div>

        <!-- Phone -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nomor Telepon (Opsional)</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">phone_iphone</span>
            <input 
              v-model="form.phone"
              type="tel" 
              placeholder="Contoh: 08123456789"
              class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#2563EB] transition-all font-bold text-gray-900 placeholder:text-gray-300"
            />
          </div>
        </div>

        <!-- Password (opsional) -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Kata Sandi Baru <span class="text-gray-400 normal-case font-medium">(kosongkan jika tidak diubah)</span></label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">lock</span>
            <input 
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'" 
              placeholder="••••••••"
              class="w-full pl-12 pr-12 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-[#2563EB] transition-all font-bold text-gray-900 placeholder:text-gray-300"
              minlength="8"
            />
            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
              <span class="material-symbols-outlined text-lg">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 pt-6 border-t border-gray-100">
          <button 
            type="button" 
            @click="router.push({ name: 'admin.pengajar' })" 
            class="flex-1 py-4 rounded-2xl font-bold text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all uppercase tracking-widest text-xs"
          >
            Batal
          </button>
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="flex-2 bg-[#2563EB] hover:opacity-90 disabled:opacity-50 text-white py-4 px-8 rounded-2xl font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase tracking-widest text-xs"
          >
            <span v-if="isSubmitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
