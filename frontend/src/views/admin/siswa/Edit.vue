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
const photoPreview = ref<string | null>(null)
const photoFile = ref<File | null>(null)

const form = ref({
  name: '',
  email: '',
  phone: '',
  domisili: '',
  password: ''
})

const fetchSiswa = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/admin/siswa/${id}`)
    const data = response.data.data
    form.value.name = data.name
    form.value.email = data.email
    form.value.phone = data.phone || ''
    form.value.domisili = data.domisili || ''
    if (data.photo) {
      photoPreview.value = `/storage/${data.photo}`
    }
  } catch (err) {
    console.error('Failed to fetch siswa:', err)
    error('Gagal memuat data siswa.')
    router.push({ name: 'admin.siswa' })
  } finally {
    isLoading.value = false
  }
}

const onPhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  photoFile.value = file
  photoPreview.value = URL.createObjectURL(file)
}

const handleUpdate = async () => {
  if (!form.value.name || !form.value.email) return

  isSubmitting.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    formData.append('phone', form.value.phone)
    formData.append('domisili', form.value.domisili)
    if (form.value.password) {
      formData.append('password', form.value.password)
    }
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }

    await api.post(`/admin/siswa/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    success('Data siswa berhasil diperbarui!')
    router.push({ name: 'admin.siswa' })
  } catch (err: any) {
    const msg = err.response?.data?.message || 'Gagal memperbarui data siswa.'
    error(msg)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchSiswa()
})
</script>

<template>
  <div class="max-w-xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button @click="router.push({ name: 'admin.siswa' })" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <div class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Siswa Management</div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight leading-none">Edit Siswa</h2>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Form -->
    <div v-else class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 md:p-10 space-y-8">
      <form @submit.prevent="handleUpdate" class="space-y-6">
        
        <!-- Foto Profil -->
        <div class="space-y-2">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Foto Profil (Opsional)</label>
          <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center flex-shrink-0">
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

        <!-- Nama Lengkap -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nama Lengkap</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">person</span>
            <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap" class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300" required />
          </div>
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Email</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">alternate_email</span>
            <input v-model="form.email" type="email" placeholder="nama@email.com" class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300" required />
          </div>
        </div>

        <!-- Phone -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Nomor Telepon (Opsional)</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">phone_iphone</span>
            <input v-model="form.phone" type="tel" placeholder="Contoh: 08123456789" class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300" />
          </div>
        </div>

        <!-- Domisili -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Domisili (Opsional)</label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">location_on</span>
            <input v-model="form.domisili" type="text" placeholder="Contoh: Jakarta Selatan" class="w-full pl-12 pr-4 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300" />
          </div>
        </div>

        <!-- Password (opsional) -->
        <div class="space-y-1.5">
          <label class="text-[10px] font-bold text-gray-700 uppercase tracking-widest ml-1">Kata Sandi Baru <span class="text-gray-400 normal-case font-medium">(kosongkan jika tidak diubah)</span></label>
          <div class="relative group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors">lock</span>
            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" class="w-full pl-12 pr-12 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-primary transition-all font-bold text-gray-900 placeholder:text-gray-300" minlength="8" />
            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
              <span class="material-symbols-outlined text-lg">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 pt-6 border-t border-gray-100">
          <button type="button" @click="router.push({ name: 'admin.siswa' })" class="flex-1 py-4 rounded-2xl font-bold text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all uppercase tracking-widest text-xs">
            Batal
          </button>
          <button type="submit" :disabled="isSubmitting" class="flex-2 bg-primary hover:opacity-90 disabled:opacity-50 text-white py-4 px-8 rounded-2xl font-bold shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase tracking-widest text-xs">
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
.bg-primary { background-color: #2563EB; }
.text-primary { color: #2563EB; }
.focus\:ring-primary:focus { --tw-ring-color: #2563EB; }
</style>
