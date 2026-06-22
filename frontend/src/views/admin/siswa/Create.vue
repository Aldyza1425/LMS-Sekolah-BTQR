<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { success, error } = useToast()

const form = ref({
  name: '',
  email: '',
  phone: '',
  domisili: '',
  password: ''
})

const isSubmitting = ref(false)
const showPassword = ref(false)
const isSuccess = ref(false)
const photoFile = ref<File | null>(null)
const photoPreview = ref<string | null>(null)
const createdCredentials = ref({
  name: '',
  email: '',
  password: ''
})

const generatePassword = () => {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
  let pass = ''
  for (let i = 0; i < 10; i++) {
    pass += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  form.value.password = pass
}

const handleCreateSiswa = async () => {
  if (!form.value.name || !form.value.email || !form.value.password) return
  
  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    formData.append('phone', form.value.phone)
    formData.append('domisili', form.value.domisili)
    formData.append('password', form.value.password)
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }

    const response = await api.post('/admin/siswa', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    createdCredentials.value = {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password
    }
    
    form.value = { name: '', email: '', phone: '', domisili: '', password: '' }
    photoFile.value = null
    photoPreview.value = null
    showPassword.value = false
    isSuccess.value = true
  } catch (err: any) {
    console.error('Failed to create student:', err)
    const msg = err.response?.data?.message || 'Gagal menambahkan siswa. Pastikan email belum terdaftar.'
    error(msg)
  } finally {
    isSubmitting.value = false
  }
}

const onPhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  photoFile.value = file
  photoPreview.value = URL.createObjectURL(file)
}

const copyToClipboard = () => {
  const text = `Detail Akun Santri BTQR:
Nama: ${createdCredentials.value.name}
Email: ${createdCredentials.value.email}
Password: ${createdCredentials.value.password}

Silakan login di http://localhost:5173/login`
  
  navigator.clipboard.writeText(text)
    .then(() => {
      success('Info akun berhasil disalin ke clipboard!')
    })
    .catch((err) => {
      console.error('Could not copy text: ', err)
      error('Gagal menyalin ke clipboard.')
    })
}
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-lg transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Tambah Siswa Baru</h2>
      </div>
    </div>

    <!-- Success State Card -->
    <div v-if="isSuccess" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 text-center space-y-6">
      <div class="w-16 h-16 bg-green-50 text-green-600 rounded-lg flex items-center justify-center mx-auto border border-green-100">
        <span class="material-symbols-outlined text-3xl font-bold">check_circle</span>
      </div>
      
      <div>
        <h3 class="text-2xl font-bold text-gray-900 font-headline mb-1">Akun Berhasil Dibuat!</h3>
        <p class="text-sm text-gray-500">Silakan salin kredensial berikut untuk diberikan kepada siswa:</p>
      </div>
      
      <div class="bg-gray-50 rounded-lg p-6 text-left border border-gray-100 space-y-4">
        <div>
          <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Nama Siswa</span>
          <span class="text-sm font-bold text-gray-800">{{ createdCredentials.name }}</span>
        </div>
        <div class="border-t border-gray-200/50 pt-3">
          <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Email / Username</span>
          <span class="text-sm font-bold text-gray-800 select-all">{{ createdCredentials.email }}</span>
        </div>
        <div class="border-t border-gray-200/50 pt-3">
          <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-0.5">Kata Sandi</span>
          <span class="text-sm font-bold text-gray-800 select-all font-mono bg-white px-2 py-0.5 rounded border border-gray-100 inline-block">{{ createdCredentials.password }}</span>
        </div>
      </div>
      
      <div class="flex flex-col gap-3">
        <button 
          @click="copyToClipboard" 
          class="w-full py-4 bg-primary hover:opacity-90 text-white font-bold rounded-lg shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer"
        >
          <span class="material-symbols-outlined text-lg">content_copy</span>
          Salin Detail Akun
        </button>
        <button 
          @click="isSuccess = false" 
          class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold rounded-lg transition-all cursor-pointer text-sm"
        >
          Buat Akun Lain
        </button>
      </div>
    </div>

    <!-- Form Card -->
    <div v-else class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 md:p-10 space-y-8">
      <form @submit.prevent="handleCreateSiswa" class="space-y-6">
        <!-- Foto Profil -->
        <div class="space-y-2">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Foto Profil (Opsional)</label>
          <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center flex-shrink-0">
              <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" alt="Preview foto" />
              <span v-else class="material-symbols-outlined text-3xl text-gray-400">person</span>
            </div>
            <div>
              <label class="cursor-pointer bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2 w-fit">
                <span class="material-symbols-outlined text-sm">upload</span>
                Pilih Foto
                <input type="file" accept="image/*" @change="onPhotoChange" class="hidden" />
              </label>
              <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, max. 2MB</p>
            </div>
          </div>
        </div>

        <!-- Inputs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nama Lengkap -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nama Lengkap</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                person
              </span>
              <input 
                v-model="form.name"
                type="text" 
                placeholder="Masukkan nama lengkap siswa"
                class="w-full pl-12 pr-4 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300"
                required
              />
            </div>
          </div>

          <!-- Email -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Email</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                alternate_email
              </span>
              <input 
                v-model="form.email"
                type="email" 
                placeholder="nama@email.com"
                class="w-full pl-12 pr-4 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300"
                required
              />
            </div>
          </div>

          <!-- Phone -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nomor Telepon / WA (Opsional)</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                phone_iphone
              </span>
              <input 
                v-model="form.phone"
                type="tel" 
                placeholder="Contoh: 08123456789"
                class="w-full pl-12 pr-4 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300"
              />
            </div>
          </div>

          <!-- Domisili -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Domisili (Opsional)</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">location_on</span>
              <input 
                v-model="form.domisili"
                type="text" 
                placeholder="Contoh: Jakarta Selatan"
                class="w-full pl-12 pr-4 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300"
              />
            </div>
          </div>

          <!-- Password -->
          <div class="space-y-1.5 md:col-span-2">
            <div class="flex justify-between items-center ml-1">
              <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">Kata Sandi</label>
              <button 
                type="button" 
                @click="generatePassword" 
                class="text-xs font-bold text-primary hover:underline flex items-center gap-1 cursor-pointer"
              >
                <span class="material-symbols-outlined text-sm">key</span> Generate Otomatis
              </button>
            </div>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">
                lock
              </span>
              <input 
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'" 
                placeholder="Masukkan kata sandi (min. 8 karakter)"
                class="w-full pl-12 pr-12 py-4 rounded-lg bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300"
                minlength="8"
                required
              />
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <span class="material-symbols-outlined text-lg">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 pt-6 border-t border-gray-100">
          <button 
            type="button" 
            @click="router.back()" 
            class="flex-1 py-4.5 rounded-lg font-bold text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all uppercase tracking-widest text-xs"
          >
            Batal
          </button>
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="flex-2 bg-primary hover:opacity-90 disabled:opacity-50 text-white py-4.5 rounded-lg font-bold shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase tracking-widest text-xs"
          >
            <span v-if="isSubmitting" class="animate-spin rounded-lg h-4 w-4 border-2 border-white border-t-transparent"></span>
            {{ isSubmitting ? 'Menyimpan...' : 'Simpan Akun' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
.bg-primary { background-color: #006D3E; }
.text-primary { color: #006D3E; }
.focus\:ring-primary:focus { --tw-ring-color: #006D3E; }
</style>
