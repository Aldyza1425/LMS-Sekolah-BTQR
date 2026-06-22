<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/api'
import SkeletonLoader from '@/components/SkeletonLoader.vue'

const user = ref<any>(null)
const isLoading = ref(true)

const fetchProfile = async () => {
  try {
    isLoading.value = true
    const response = await api.get('/user')
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch profile:', error)
  } finally {
    isLoading.value = false
  }
}

const getPhotoUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const rootUrl = baseUrl.replace('/api', '')
  return `${rootUrl}/storage/${path}`
}

onMounted(() => {
  fetchProfile()
})
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 font-['Plus_Jakarta_Sans']">
    <div class="flex items-center justify-between">
      <h2 class="font-headline text-3xl font-black text-[#006D3E]">Profil Saya</h2>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="isLoading" class="bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
      <div class="h-40 bg-gray-100 animate-pulse"></div>
      <SkeletonLoader type="profile" :rows="4" />
    </div>

    <div v-else-if="user" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
      <!-- Header/Banner -->
      <div class="h-40 bg-gradient-to-r from-[#006D3E] to-[#128a55] relative"></div>
      
      <div class="px-8 md:px-12 pb-12 relative">
        <!-- Avatar -->
        <div class="absolute -top-16 left-8 md:left-12 h-32 w-32 rounded-xl border-4 border-white shadow-xl overflow-hidden bg-gray-50 flex items-center justify-center">
          <img 
            v-if="user.photo" 
            :src="getPhotoUrl(user.photo)" 
            alt="Foto Profil" 
            class="w-full h-full object-cover"
          />
          <span v-else class="material-symbols-outlined text-7xl text-gray-300">person</span>
        </div>

        <div class="pt-20 space-y-8">
          <div>
            <h3 class="text-2xl font-black text-gray-900">{{ user.name }}</h3>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">ID Siswa: BTQR-S-{{ user.id.toString().padStart(3, '0') }}</p>
          </div>

          <div class="border-t border-gray-100 pt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nama Lengkap</label>
                <p class="text-base font-bold text-gray-800">{{ user.name }}</p>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Alamat Email</label>
                <p class="text-base font-bold text-gray-800">{{ user.email }}</p>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nomor Telepon</label>
                <p class="text-base font-bold text-gray-800">{{ user.phone || 'Belum diatur' }}</p>
              </div>
            </div>
            
            <div class="space-y-6">
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Peran / Role</label>
                <span class="inline-block px-4 py-1.5 bg-green-50 text-[#006D3E] text-xs font-black rounded-lg uppercase tracking-wider border border-green-100">
                  Peserta Didik
                </span>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Domisili</label>
                <p class="text-base font-bold text-gray-800">{{ user.domisili || 'Belum diatur' }}</p>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status Akun</label>
                <p class="flex items-center gap-2 text-green-600 font-bold text-sm">
                  <span class="h-2.5 w-2.5 rounded-lg bg-green-500 animate-pulse"></span>
                  {{ user.is_active ? 'Aktif' : 'Menunggu Verifikasi' }}
                </p>
              </div>
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
