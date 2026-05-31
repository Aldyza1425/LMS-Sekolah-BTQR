<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from '@/composables/useToast'

const { success, warning } = useToast()

const lembaga = ref({
  nama: 'BTQR - Baca Tulis Qur\'an & Relevansi',
  telepon: '08123456789',
  email: 'info@btqr.ac.id',
  alamat: 'Jl. Pendidikan No. 1, Jakarta'
})

const pengaturan = ref({
  pendaftaran_terbuka: true,
  passing_grade: 70
})

const isSavingLembaga = ref(false)
const isSavingSystem = ref(false)
const isBackingUp = ref(false)
const isOptimizing = ref(false)

const saveLembaga = async () => {
  isSavingLembaga.value = true
  await new Promise(r => setTimeout(r, 800))
  isSavingLembaga.value = false
  success('Profil lembaga berhasil disimpan!')
}

const saveSystem = async () => {
  isSavingSystem.value = true
  await new Promise(r => setTimeout(r, 800))
  isSavingSystem.value = false
  success('Pengaturan sistem berhasil disimpan!')
}

const backupDatabase = async () => {
  isBackingUp.value = true
  await new Promise(r => setTimeout(r, 2000))
  isBackingUp.value = false
  success('Backup database berhasil dibuat! (simulasi)')
}

const optimizeDatabase = async () => {
  isOptimizing.value = true
  await new Promise(r => setTimeout(r, 2000))
  isOptimizing.value = false
  success('Database berhasil dioptimasi! (simulasi)')
}
</script>

<template>
  <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 max-w-4xl">
    <!-- Header -->
    <div>
      <div class="text-[10px] font-bold text-[#2563EB] uppercase tracking-widest mb-1">Admin Portal</div>
      <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight">Pengaturan Sistem</h2>
      <p class="text-gray-500 font-medium mt-1">Kelola konfigurasi dan profil lembaga BTQR.</p>
    </div>

    <!-- Profil Lembaga -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-[#2563EB] to-[#1d4ed8] p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">business</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Profil Lembaga</h3>
          <p class="text-white/70 text-sm">Informasi resmi lembaga BTQR</p>
        </div>
      </div>
      <div class="p-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Lembaga</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">school</span>
              <input v-model="lembaga.nama" type="text" class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 transition-all font-bold text-gray-900" />
            </div>
          </div>
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nomor Telepon</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">phone_iphone</span>
              <input v-model="lembaga.telepon" type="tel" class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 transition-all font-bold text-gray-900" />
            </div>
          </div>
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Email Resmi</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">alternate_email</span>
              <input v-model="lembaga.email" type="email" class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 transition-all font-bold text-gray-900" />
            </div>
          </div>
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Alamat</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#2563EB] transition-colors">location_on</span>
              <input v-model="lembaga.alamat" type="text" class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-blue-500 transition-all font-bold text-gray-900" />
            </div>
          </div>
        </div>
        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button @click="saveLembaga" :disabled="isSavingLembaga" class="px-8 py-3 bg-[#2563EB] hover:opacity-90 disabled:opacity-50 text-white rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer">
            <span v-if="isSavingLembaga" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            <span class="material-symbols-outlined text-sm" v-else>save</span>
            {{ isSavingLembaga ? 'Menyimpan...' : 'Simpan Profil' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Pengaturan Sistem -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">tune</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Pengaturan Sistem</h3>
          <p class="text-white/70 text-sm">Atur perilaku dan kebijakan default sistem</p>
        </div>
      </div>
      <div class="p-8 space-y-6">
        <!-- Toggle Pendaftaran -->
        <div class="flex items-center justify-between p-5 bg-gray-50 rounded-2xl">
          <div>
            <p class="font-bold text-gray-900 text-sm">Pendaftaran Siswa Baru</p>
            <p class="text-xs text-gray-500 mt-0.5">Izinkan siswa baru mendaftar ke platform</p>
          </div>
          <button 
            @click="pengaturan.pendaftaran_terbuka = !pengaturan.pendaftaran_terbuka" 
            class="w-14 h-7 rounded-full relative transition-colors duration-300 flex-shrink-0"
            :class="pengaturan.pendaftaran_terbuka ? 'bg-green-500' : 'bg-gray-300'"
          >
            <div 
              class="w-6 h-6 rounded-full bg-white shadow-md absolute top-0.5 transition-transform duration-300"
              :class="pengaturan.pendaftaran_terbuka ? 'translate-x-7' : 'translate-x-0.5'"
            ></div>
          </button>
        </div>

        <!-- Passing Grade -->
        <div class="p-5 bg-gray-50 rounded-2xl space-y-3">
          <div class="flex justify-between items-center">
            <div>
              <p class="font-bold text-gray-900 text-sm">Nilai Kelulusan Try Out (Passing Grade)</p>
              <p class="text-xs text-gray-500 mt-0.5">Nilai minimum untuk dinyatakan lulus</p>
            </div>
            <span class="text-2xl font-black text-purple-600">{{ pengaturan.passing_grade }}</span>
          </div>
          <input 
            v-model="pengaturan.passing_grade"
            type="range" 
            min="0" max="100" step="5"
            class="w-full h-2 bg-purple-200 rounded-full appearance-none cursor-pointer accent-purple-600"
          />
          <div class="flex justify-between text-[10px] text-gray-400 font-bold">
            <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button @click="saveSystem" :disabled="isSavingSystem" class="px-8 py-3 bg-purple-600 hover:opacity-90 disabled:opacity-50 text-white rounded-2xl font-bold text-sm shadow-lg shadow-purple-500/20 transition-all flex items-center gap-2 cursor-pointer">
            <span v-if="isSavingSystem" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            <span class="material-symbols-outlined text-sm" v-else>save</span>
            {{ isSavingSystem ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Pemeliharaan Database -->
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">database</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Pemeliharaan Database</h3>
          <p class="text-white/70 text-sm">Operasi backup dan optimasi database SQLite</p>
        </div>
      </div>
      <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="border border-gray-200 rounded-2xl p-6 space-y-3 hover:border-blue-200 hover:bg-blue-50/30 transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-[#2563EB] text-2xl">backup</span>
          </div>
          <div>
            <p class="font-bold text-gray-900 text-sm">Backup Data</p>
            <p class="text-xs text-gray-500 mt-0.5">Buat salinan cadangan seluruh data database</p>
          </div>
          <button @click="backupDatabase" :disabled="isBackingUp" class="w-full py-3 bg-[#2563EB] hover:opacity-90 disabled:opacity-60 text-white rounded-xl font-bold text-xs tracking-wide transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span v-if="isBackingUp" class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></span>
            <span class="material-symbols-outlined text-sm" v-else>download</span>
            {{ isBackingUp ? 'Memproses...' : 'Mulai Backup' }}
          </button>
        </div>

        <div class="border border-gray-200 rounded-2xl p-6 space-y-3 hover:border-green-200 hover:bg-green-50/30 transition-all group">
          <div class="w-12 h-12 rounded-2xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-green-600 text-2xl">speed</span>
          </div>
          <div>
            <p class="font-bold text-gray-900 text-sm">Optimasi Database</p>
            <p class="text-xs text-gray-500 mt-0.5">Jalankan VACUUM dan analisis performa database</p>
          </div>
          <button @click="optimizeDatabase" :disabled="isOptimizing" class="w-full py-3 bg-green-600 hover:opacity-90 disabled:opacity-60 text-white rounded-xl font-bold text-xs tracking-wide transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span v-if="isOptimizing" class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></span>
            <span class="material-symbols-outlined text-sm" v-else>bolt</span>
            {{ isOptimizing ? 'Memproses...' : 'Optimasi Sekarang' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
