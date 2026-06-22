<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/api'

const user = ref<any>(null)
const loading = ref(false)
const message = ref({ type: '', text: '' })

const form = ref({
  name: '',
  email: '',
  phone: '',
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const fetchUser = async () => {
  try {
    const response = await api.get('/user')
    user.value = response.data
    form.value.name = user.value.name
    form.value.email = user.value.email
    form.value.phone = user.value.phone || ''
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const handleUpdateProfile = async () => {
  loading.value = true
  message.value = { type: '', text: '' }
  try {
    await api.post('/siswa/update-profile', {
      name: form.value.name,
      email: form.value.email,
      phone: form.value.phone
    })
    message.value = { type: 'success', text: 'Profil berhasil diperbarui!' }
    fetchUser()
  } catch (error: any) {
    message.value = { type: 'error', text: error.response?.data?.message || 'Gagal memperbarui profil.' }
  } finally {
    loading.value = false
  }
}

const handleChangePassword = async () => {
  loading.value = true
  message.value = { type: '', text: '' }
  try {
    await api.post('/siswa/change-password', {
      current_password: form.value.current_password,
      password: form.value.new_password,
      password_confirmation: form.value.new_password_confirmation
    })
    message.value = { type: 'success', text: 'Password berhasil diubah!' }
    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''
  } catch (error: any) {
    message.value = { type: 'error', text: error.response?.data?.message || 'Gagal mengubah password.' }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchUser()
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 font-['Plus_Jakarta_Sans']">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Akun</h2>
        <p class="text-gray-500 font-medium mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
      </div>
    </div>

    <!-- Feedback Message -->
    <div v-if="message.text" 
         :class="message.type === 'success' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100'"
         class="p-4 rounded-lg border font-bold text-sm flex items-center gap-3 animate-in zoom-in duration-300">
      <span class="material-symbols-outlined">{{ message.type === 'success' ? 'check_circle' : 'error' }}</span>
      {{ message.text }}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Profile Information -->
      <div class="lg:col-span-2 space-y-6">
        <section class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-gray-100 space-y-8">
          <h3 class="text-xl font-extrabold text-gray-900 flex items-center gap-3">
            <span class="w-1.5 h-6 bg-[#006D3E] rounded-lg"></span>
            Informasi Profil
          </h3>

          <form @submit.prevent="handleUpdateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
              <input 
                v-model="form.name"
                type="text" 
                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
                required
              />
            </div>

            <div class="space-y-1.5">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
              <input 
                v-model="form.email"
                type="email" 
                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
                required
              />
            </div>

            <div class="space-y-1.5 md:col-span-2">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nomor WhatsApp</label>
              <input 
                v-model="form.phone"
                type="tel" 
                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
              />
            </div>

            <div class="md:col-span-2 pt-4">
              <button 
                type="submit" 
                :disabled="loading"
                class="px-8 py-4 bg-[#006D3E] text-white rounded-lg font-black uppercase tracking-widest text-xs hover:bg-[#005a33] transition-all shadow-xl shadow-[#006D3E]/20 active:scale-95 disabled:opacity-50"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </section>

        <!-- Security Section -->
        <section class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-gray-100 space-y-8">
          <h3 class="text-xl font-extrabold text-gray-900 flex items-center gap-3">
            <span class="w-1.5 h-6 bg-[#006D3E] rounded-lg"></span>
            Keamanan Akun
          </h3>

          <form @submit.prevent="handleChangePassword" class="space-y-6">
            <div class="space-y-1.5">
              <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Saat Ini</label>
              <input 
                v-model="form.current_password"
                type="password" 
                placeholder="••••••••"
                class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
                required
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Baru</label>
                <input 
                  v-model="form.new_password"
                  type="password" 
                  placeholder="••••••••"
                  class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
                  required
                />
              </div>
              <div class="space-y-1.5">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Password Baru</label>
                <input 
                  v-model="form.new_password_confirmation"
                  type="password" 
                  placeholder="••••••••"
                  class="w-full px-5 py-4 bg-gray-50/50 border border-gray-100 rounded-lg focus:bg-white focus:border-[#006D3E] focus:ring-4 focus:ring-[#006D3E]/5 outline-none transition-all text-sm font-semibold"
                  required
                />
              </div>
            </div>

            <div class="pt-4">
              <button 
                type="submit" 
                :disabled="loading"
                class="px-8 py-4 bg-gray-900 text-white rounded-lg font-black uppercase tracking-widest text-xs hover:bg-black transition-all shadow-xl shadow-black/10 active:scale-95 disabled:opacity-50"
              >
                Ganti Password
              </button>
            </div>
          </form>
        </section>
      </div>

      <!-- Right Column: Sidebar info -->
      <div class="space-y-8">
        <div class="bg-[#006D3E] rounded-[2.5rem] p-10 text-white shadow-2xl shadow-[#006D3E]/20 relative overflow-hidden">
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-lg blur-3xl"></div>
          <div class="relative z-10 space-y-6">
            <div class="w-16 h-16 rounded-lg bg-white/20 flex items-center justify-center backdrop-blur-md">
              <span class="material-symbols-outlined text-3xl">shield</span>
            </div>
            <h4 class="text-2xl font-extrabold leading-tight">Privasi & Keamanan</h4>
            <p class="text-white/70 text-sm leading-relaxed">Data Anda tersimpan dengan aman di sistem kami. Gunakan password yang kuat dan unik untuk melindungi akun Anda.</p>
          </div>
        </div>

        <div class="bg-gray-50 rounded-[2.5rem] p-8 border border-gray-100 text-center">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Butuh Bantuan?</p>
          <p class="text-sm text-gray-600 font-medium mb-6">Hubungi admin jika Anda mengalami kesulitan dalam mengelola akun.</p>
          <a href="https://wa.me/628123456789" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-900 hover:bg-gray-50 transition-all">
            <span class="material-symbols-outlined text-green-600 text-sm">chat</span>
            Hubungi CS
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
