<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from '@/composables/useToast'
import api from '@/api'

const { success, error: showError } = useToast()

const lembaga = ref({
  nama: '',
  telepon: '',
  email: '',
  alamat: ''
})

const pengaturan = ref({
  passing_grade: 70
})

const isSavingLembaga = ref(false)
const isSavingSystem = ref(false)

// Backup states per type
const backupTypes = [
  { key: 'siswa',   label: 'Data Siswa',        icon: 'group',       color: 'blue',   desc: 'Seluruh data akun & profil siswa' },
  { key: 'modul',   label: 'Modul & Materi',     icon: 'menu_book',   color: 'orange', desc: 'Semua modul beserta konten materi' },
  { key: 'try_out', label: 'Try Out & Soal',     icon: 'quiz',        color: 'purple', desc: 'Bank soal dan konfigurasi try out' },
  { key: 'nilai',   label: 'Nilai & Hasil Ujian',icon: 'workspace_premium', color: 'green', desc: 'Rekap nilai dan hasil try out siswa' },
  { key: 'all',     label: 'Full Backup',         icon: 'backup',      color: 'gray',   desc: 'Seluruh data sistem sekaligus' },
]
const backingUp = ref<Record<string, boolean>>({})

// ── Fetch Data ──────────────────────────────────────────────
const fetchSettings = async () => {
  try {
    const [lRes, sRes] = await Promise.all([
      api.get('/admin/settings/lembaga'),
      api.get('/admin/settings/system'),
    ])
    lembaga.value = lRes.data
    pengaturan.value = sRes.data
  } catch (e) {
    console.error('Failed to fetch settings', e)
  }
}

onMounted(fetchSettings)

// ── Save Lembaga ────────────────────────────────────────────
const saveLembaga = async () => {
  isSavingLembaga.value = true
  try {
    await api.post('/admin/settings/lembaga', lembaga.value)
    success('Profil lembaga berhasil disimpan!')
  } catch (e: any) {
    showError(e.response?.data?.message || 'Gagal menyimpan profil lembaga.')
  } finally {
    isSavingLembaga.value = false
  }
}

// ── Save System ─────────────────────────────────────────────
const saveSystem = async () => {
  isSavingSystem.value = true
  try {
    await api.post('/admin/settings/system', pengaturan.value)
    success('Pengaturan sistem berhasil disimpan!')
  } catch (e: any) {
    showError(e.response?.data?.message || 'Gagal menyimpan pengaturan sistem.')
  } finally {
    isSavingSystem.value = false
  }
}

// ── Backup ──────────────────────────────────────────────────
const doBackup = async (type: string, label: string) => {
  backingUp.value[type] = true
  try {
    const response = await api.post('/admin/settings/backup', { type }, { responseType: 'blob' })
    const timestamp = new Date().toISOString().slice(0, 10)
    const filename  = `backup_${type}_${timestamp}.json`
    const url = URL.createObjectURL(new Blob([response.data], { type: 'application/json' }))
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    a.click()
    URL.revokeObjectURL(url)
    success(`Backup ${label} berhasil diunduh!`)
  } catch (e: any) {
    showError(`Gagal backup ${label}.`)
  } finally {
    backingUp.value[type] = false
  }
}

// ── Ganti Password ───────────────────────────────────────
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})
const isChangingPassword = ref(false)
const showCurrentPw = ref(false)
const showNewPw = ref(false)
const showConfirmPw = ref(false)

const changePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    showError('Konfirmasi password baru tidak cocok.')
    return
  }
  if (passwordForm.value.new_password.length < 8) {
    showError('Password baru minimal 8 karakter.')
    return
  }
  isChangingPassword.value = true
  try {
    await api.post('/admin/settings/change-password', passwordForm.value)
    success('Password berhasil diubah!')
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' }
  } catch (e: any) {
    showError(e.response?.data?.message || 'Gagal mengubah password.')
  } finally {
    isChangingPassword.value = false
  }
}
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 max-w-4xl">
    <!-- Header -->
    <div>
      <div class="text-[10px] font-bold text-[#006D3E] uppercase tracking-widest mb-1">Admin Portal</div>
      <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight">Pengaturan Sistem</h2>
      <p class="text-gray-500 font-medium mt-1">Kelola konfigurasi dan profil lembaga BTQR.</p>
    </div>

    <!-- ─── Profil Lembaga ─────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-[#006D3E] to-[#00955A] p-6 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">business</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Profil Lembaga</h3>
          <p class="text-white/70 text-sm">Informasi resmi lembaga BTQR</p>
        </div>
      </div>
      <div class="p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Nama -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Lembaga</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#006D3E] transition-colors text-xl">school</span>
              <input v-model="lembaga.nama" type="text"
                class="w-full pl-11 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-[#006D3E]/40 focus:ring-2 focus:ring-[#006D3E]/10 outline-none transition-all font-semibold text-gray-900 text-sm" />
            </div>
          </div>
          <!-- Telepon -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nomor Telepon</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#006D3E] transition-colors text-xl">phone_iphone</span>
              <input v-model="lembaga.telepon" type="tel"
                class="w-full pl-11 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-[#006D3E]/40 focus:ring-2 focus:ring-[#006D3E]/10 outline-none transition-all font-semibold text-gray-900 text-sm" />
            </div>
          </div>
          <!-- Email -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Email Resmi</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#006D3E] transition-colors text-xl">alternate_email</span>
              <input v-model="lembaga.email" type="email"
                class="w-full pl-11 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-[#006D3E]/40 focus:ring-2 focus:ring-[#006D3E]/10 outline-none transition-all font-semibold text-gray-900 text-sm" />
            </div>
          </div>
          <!-- Alamat -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Alamat</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#006D3E] transition-colors text-xl">location_on</span>
              <input v-model="lembaga.alamat" type="text"
                class="w-full pl-11 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-[#006D3E]/40 focus:ring-2 focus:ring-[#006D3E]/10 outline-none transition-all font-semibold text-gray-900 text-sm" />
            </div>
          </div>
        </div>
        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button @click="saveLembaga" :disabled="isSavingLembaga"
            class="px-7 py-2.5 bg-[#006D3E] hover:bg-[#005530] disabled:opacity-50 text-white rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2 cursor-pointer">
            <span v-if="isSavingLembaga" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-lg"></span>
            <span class="material-symbols-outlined text-sm" v-else>save</span>
            {{ isSavingLembaga ? 'Menyimpan...' : 'Simpan Profil' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ─── Pengaturan Sistem ───────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">tune</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Pengaturan Sistem</h3>
          <p class="text-white/70 text-sm">Atur perilaku dan kebijakan default sistem</p>
        </div>
      </div>
      <div class="p-6 space-y-5">
        <!-- Passing Grade -->
        <div class="p-5 bg-gray-50 rounded-lg space-y-3 border border-gray-100">
          <div class="flex justify-between items-center">
            <div>
              <p class="font-bold text-gray-900 text-sm">Nilai Kelulusan Try Out (Passing Grade)</p>
              <p class="text-xs text-gray-500 mt-0.5">Nilai minimum untuk dinyatakan lulus ujian</p>
            </div>
            <span class="text-2xl font-black text-purple-600">{{ pengaturan.passing_grade }}</span>
          </div>
          <input
            v-model="pengaturan.passing_grade"
            type="range" min="0" max="100" step="5"
            class="w-full h-2 bg-purple-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
          />
          <div class="flex justify-between text-[10px] text-gray-400 font-bold">
            <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button @click="saveSystem" :disabled="isSavingSystem"
            class="px-7 py-2.5 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2 cursor-pointer">
            <span v-if="isSavingSystem" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-lg"></span>
            <span class="material-symbols-outlined text-sm" v-else>save</span>
            {{ isSavingSystem ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ─── Pemeliharaan Database ──────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">database</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Pemeliharaan Database</h3>
          <p class="text-white/70 text-sm">Unduh backup data per kategori dalam format JSON</p>
        </div>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="bt in backupTypes" :key="bt.key"
            class="border border-gray-200 rounded-lg p-5 space-y-3 hover:border-[#006D3E]/30 hover:bg-green-50/20 transition-all group"
          >
            <div :class="`w-11 h-11 rounded-lg bg-${bt.color}-50 group-hover:bg-${bt.color}-100 flex items-center justify-center transition-colors`">
              <span :class="`material-symbols-outlined text-${bt.color}-600 text-xl`">{{ bt.icon }}</span>
            </div>
            <div>
              <p class="font-bold text-gray-900 text-sm">{{ bt.label }}</p>
              <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ bt.desc }}</p>
            </div>
            <button
              @click="doBackup(bt.key, bt.label)"
              :disabled="backingUp[bt.key]"
              class="w-full py-2.5 bg-[#006D3E] hover:bg-[#005530] disabled:opacity-60 text-white rounded-lg font-bold text-xs tracking-wide transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
              <span v-if="backingUp[bt.key]" class="animate-spin h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-lg"></span>
              <span class="material-symbols-outlined text-sm" v-else>download</span>
              {{ backingUp[bt.key] ? 'Memproses...' : 'Unduh Backup' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── Ganti Password ────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-slate-700 to-slate-800 p-6 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg bg-white/20 flex items-center justify-center">
          <span class="material-symbols-outlined text-white text-2xl">lock_reset</span>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Ganti Password</h3>
          <p class="text-white/70 text-sm">Perbarui kata sandi akun admin Anda</p>
        </div>
      </div>
      <div class="p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

          <!-- Password Lama -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Password Saat Ini</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-slate-700 transition-colors text-xl">lock</span>
              <input
                v-model="passwordForm.current_password"
                :type="showCurrentPw ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full pl-11 pr-10 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-slate-400/50 focus:ring-2 focus:ring-slate-200 outline-none transition-all text-sm text-gray-900 font-medium"
              />
              <button type="button" @click="showCurrentPw = !showCurrentPw"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined text-lg">{{ showCurrentPw ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Password Baru -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Password Baru</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-slate-700 transition-colors text-xl">lock_open</span>
              <input
                v-model="passwordForm.new_password"
                :type="showNewPw ? 'text' : 'password'"
                placeholder="Min. 8 karakter"
                class="w-full pl-11 pr-10 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-slate-400/50 focus:ring-2 focus:ring-slate-200 outline-none transition-all text-sm text-gray-900 font-medium"
              />
              <button type="button" @click="showNewPw = !showNewPw"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined text-lg">{{ showNewPw ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <!-- Konfirmasi Password Baru -->
          <div class="space-y-1.5">
            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Konfirmasi Password Baru</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-slate-700 transition-colors text-xl">check_circle</span>
              <input
                v-model="passwordForm.new_password_confirmation"
                :type="showConfirmPw ? 'text' : 'password'"
                placeholder="Ulangi password baru"
                class="w-full pl-11 pr-10 py-3 rounded-lg bg-gray-50 border border-gray-200/70 focus:border-slate-400/50 focus:ring-2 focus:ring-slate-200 outline-none transition-all text-sm text-gray-900 font-medium"
                :class="passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : ''"
              />
              <button type="button" @click="showConfirmPw = !showConfirmPw"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined text-lg">{{ showConfirmPw ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
            <p v-if="passwordForm.new_password_confirmation && passwordForm.new_password !== passwordForm.new_password_confirmation"
              class="text-[11px] text-red-500 font-semibold mt-1">
              Password tidak cocok
            </p>
          </div>

        </div>

        <!-- Strength Indicator -->
        <div v-if="passwordForm.new_password" class="flex items-center gap-3">
          <div class="flex gap-1">
            <div v-for="i in 4" :key="i"
              class="h-1.5 w-10 rounded transition-all duration-300"
              :class="[
                i <= (passwordForm.new_password.length >= 12 ? 4 : passwordForm.new_password.length >= 10 ? 3 : passwordForm.new_password.length >= 8 ? 2 : 1)
                  ? (passwordForm.new_password.length >= 12 ? 'bg-green-500' : passwordForm.new_password.length >= 10 ? 'bg-yellow-400' : 'bg-orange-400')
                  : 'bg-gray-200'
              ]"
            ></div>
          </div>
          <span class="text-[11px] font-semibold text-gray-400">
            {{ passwordForm.new_password.length >= 12 ? 'Kuat' : passwordForm.new_password.length >= 10 ? 'Sedang' : passwordForm.new_password.length >= 8 ? 'Lemah' : 'Terlalu pendek' }}
          </span>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
          <button
            @click="changePassword"
            :disabled="isChangingPassword || !passwordForm.current_password || !passwordForm.new_password || !passwordForm.new_password_confirmation"
            class="px-7 py-2.5 bg-slate-700 hover:bg-slate-800 disabled:opacity-40 text-white rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2 cursor-pointer"
          >
            <span v-if="isChangingPassword" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-lg"></span>
            <span class="material-symbols-outlined text-sm" v-else>key</span>
            {{ isChangingPassword ? 'Memproses...' : 'Ubah Password' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
