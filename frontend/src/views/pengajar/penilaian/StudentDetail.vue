<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const route = useRoute()
const router = useRouter()
const { success, error, warning } = useToast()

const siswa = ref<any>(null)
const preTests = ref<any[]>([])
const postTests = ref<any[]>([])
const tryOutHasil = ref<any[]>([])
const allMateris = ref<any[]>([])
const allTryouts = ref<any[]>([])
const isLoading = ref(true)

// Modal states & forms
const isKuisModalOpen = ref(false)
const isTryOutModalOpen = ref(false)
const isEditMode = ref(false)
const selectedItemId = ref<number | null>(null)

const kuisForm = ref({
  materi_id: '',
  score: 0,
  tipe_kuis: 'pre_test'
})

const tryOutForm = ref({
  try_out_id: '',
  nilai: 0
})

const fetchDetail = async () => {
  try {
    isLoading.value = true
    const response = await api.get(`/pengajar/penilaian/siswa/${route.params.id}`)
    if (response.data.success) {
      siswa.value = response.data.data.siswa
      preTests.value = response.data.data.pre_tests
      postTests.value = response.data.data.post_tests
      tryOutHasil.value = response.data.data.try_out_hasil
      allMateris.value = response.data.data.all_materis
      allTryouts.value = response.data.data.all_tryouts
    }
  } catch (error) {
    console.error('Failed to fetch student details:', error)
  } finally {
    isLoading.value = false
  }
}

const openAddKuis = (tipe: 'pre_test' | 'post_test') => {
  isEditMode.value = false
  selectedItemId.value = null
  kuisForm.value = {
    materi_id: '',
    score: 0,
    tipe_kuis: tipe
  }
  isKuisModalOpen.value = true
}

const openEditKuis = (item: any) => {
  isEditMode.value = true
  selectedItemId.value = item.id
  kuisForm.value = {
    materi_id: item.materi_id,
    score: item.score,
    tipe_kuis: item.tipe_kuis
  }
  isKuisModalOpen.value = true
}

const saveKuis = async () => {
  if (!kuisForm.value.materi_id && !isEditMode.value) {
    warning('Silakan pilih materi/kuis!')
    return
  }

  try {
    if (isEditMode.value && selectedItemId.value) {
      await api.put(`/pengajar/penilaian/kuis/${selectedItemId.value}`, {
        score: kuisForm.value.score
      })
      success('Nilai kuis berhasil diubah!')
    } else {
      await api.post('/pengajar/penilaian/kuis', {
        user_id: route.params.id,
        materi_id: kuisForm.value.materi_id,
        tipe_kuis: kuisForm.value.tipe_kuis,
        score: kuisForm.value.score
      })
      success('Nilai kuis berhasil ditambahkan!')
    }
    isKuisModalOpen.value = false
    fetchDetail()
  } catch (err: any) {
    error(err.response?.data?.message || 'Gagal menyimpan nilai kuis')
  }
}

const deleteKuis = async (id: number) => {
  if (!confirm('Apakah Anda yakin ingin menghapus nilai kuis ini?')) return
  try {
    await api.delete(`/pengajar/penilaian/kuis/${id}`)
    success('Nilai kuis berhasil dihapus!')
    fetchDetail()
  } catch (err) {
    error('Gagal menghapus nilai kuis')
  }
}

const openAddTryOut = () => {
  isEditMode.value = false
  selectedItemId.value = null
  tryOutForm.value = {
    try_out_id: '',
    nilai: 0
  }
  isTryOutModalOpen.value = true
}

const openEditTryOut = (item: any) => {
  isEditMode.value = true
  selectedItemId.value = item.id
  tryOutForm.value = {
    try_out_id: item.try_out_id,
    nilai: item.nilai
  }
  isTryOutModalOpen.value = true
}

const saveTryOut = async () => {
  if (!tryOutForm.value.try_out_id && !isEditMode.value) {
    warning('Silakan pilih try out!')
    return
  }

  try {
    if (isEditMode.value && selectedItemId.value) {
      await api.put(`/pengajar/penilaian/try-out/${selectedItemId.value}`, {
        nilai: tryOutForm.value.nilai
      })
      success('Nilai try out berhasil diubah!')
    } else {
      await api.post('/pengajar/penilaian/try-out', {
        user_id: route.params.id,
        try_out_id: tryOutForm.value.try_out_id,
        nilai: tryOutForm.value.nilai
      })
      success('Nilai try out berhasil ditambahkan!')
    }
    isTryOutModalOpen.value = false
    fetchDetail()
  } catch (err: any) {
    error(err.response?.data?.message || 'Gagal menyimpan nilai try out')
  }
}

const deleteTryOut = async (id: number) => {
  if (!confirm('Apakah Anda yakin ingin menghapus nilai try out ini?')) return
  try {
    await api.delete(`/pengajar/penilaian/try-out/${id}`)
    success('Nilai try out berhasil dihapus!')
    fetchDetail()
  } catch (err) {
    error('Gagal menghapus nilai try out')
  }
}

const approveRetake = async (tryOutId: number) => {
  if (!confirm('Apakah Anda yakin ingin menyetujui ujian ulang ini? Riwayat nilai akan dihapus secara permanen.')) {
    return
  }
  
  try {
    const response = await api.post(`/pengajar/penilaian/try-out/${tryOutId}/siswa/${siswa.value.id}/approve-retake`)
    if (response.data.success) {
      success('Ujian ulang disetujui.')
      fetchDetail()
    }
  } catch (err: any) {
    error(err.response?.data?.message || 'Gagal menyetujui ujian ulang')
    console.error(err)
  }
}

onMounted(() => {
  fetchDetail()
})
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <button @click="router.back()" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </button>
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Detail Penilaian Siswa</h2>
        <p class="text-gray-500 text-sm" v-if="siswa">{{ siswa.name }} ({{ siswa.email }})</p>
      </div>
    </div>

    <div v-if="isLoading" class="p-8 text-center text-gray-500 bg-white rounded-xl shadow-sm border border-gray-100">
      Memuat data...
    </div>

    <div v-else class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
      
      <!-- 1. PRE-TEST TABLE -->
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-800 font-headline flex items-center gap-2">
            <span class="material-symbols-outlined text-orange-500">assignment</span>
            Penilaian Pre-Test
          </h3>
          <button 
            @click="openAddKuis('pre_test')"
            class="flex items-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm shadow-orange-500/10 active:scale-95"
          >
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Nilai
          </button>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/20">
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kuis / Materi</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Modul</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Nilai</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="preTests.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                  Belum ada data nilai Pre-Test.
                </td>
              </tr>
              <tr v-for="test in preTests" :key="test.id" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4 font-bold text-gray-900">{{ test.materi?.judul || 'Manual Input' }}</td>
                <td class="px-6 py-4 text-gray-500 font-medium text-xs">{{ test.materi?.modul?.judul || '-' }}</td>
                <td class="px-6 py-4 text-center font-black text-base text-gray-800">{{ test.score }}</td>
                <td class="px-6 py-4 text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border"
                    :class="test.passed ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'"
                  >
                    {{ test.passed ? 'Lulus' : 'Remedi' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 font-medium text-xs">{{ new Date(test.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end gap-1.5">
                    <button 
                      @click="openEditKuis(test)"
                      class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer"
                      title="Edit Nilai"
                    >
                      <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button 
                      @click="deleteKuis(test.id)"
                      class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                      title="Hapus"
                    >
                      <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 2. POST-TEST TABLE -->
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-800 font-headline flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-600">assignment_turned_in</span>
            Penilaian Post-Test
          </h3>
          <button 
            @click="openAddKuis('post_test')"
            class="flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm shadow-teal-600/10 active:scale-95"
          >
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Nilai
          </button>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/20">
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kuis / Materi</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Modul</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Nilai</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="postTests.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                  Belum ada data nilai Post-Test.
                </td>
              </tr>
              <tr v-for="test in postTests" :key="test.id" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4 font-bold text-gray-900">{{ test.materi?.judul || 'Manual Input' }}</td>
                <td class="px-6 py-4 text-gray-500 font-medium text-xs">{{ test.materi?.modul?.judul || '-' }}</td>
                <td class="px-6 py-4 text-center font-black text-base text-gray-800">{{ test.score }}</td>
                <td class="px-6 py-4 text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border"
                    :class="test.passed ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'"
                  >
                    {{ test.passed ? 'Lulus' : 'Remedi' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 font-medium text-xs">{{ new Date(test.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end gap-1.5">
                    <button 
                      @click="openEditKuis(test)"
                      class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer"
                      title="Edit Nilai"
                    >
                      <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button 
                      @click="deleteKuis(test.id)"
                      class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                      title="Hapus"
                    >
                      <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 3. TRY OUT TABLE -->
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-800 font-headline flex items-center gap-2">
            <span class="material-symbols-outlined text-[#8B2323]">quiz</span>
            Penilaian Try Out
          </h3>
          <button 
            @click="openAddTryOut"
            class="flex items-center gap-1.5 px-4 py-2 bg-primary hover:opacity-90 text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm active:scale-95"
          >
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Nilai
          </button>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-gray-50/20">
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Judul Try Out</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Nilai</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal Selesai</th>
                <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="tryOutHasil.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                  Belum ada data nilai Try Out.
                </td>
              </tr>
              <tr v-for="hasil in tryOutHasil" :key="hasil.id" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4 font-bold text-gray-900">{{ hasil.try_out?.judul || 'Manual Input' }}</td>
                <td class="px-6 py-4 text-center font-black text-base text-gray-800">{{ hasil.nilai }}</td>
                <td class="px-6 py-4 text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border"
                    :class="hasil.lulus ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'"
                  >
                    {{ hasil.lulus ? 'Lulus' : 'Remedi' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400 font-medium text-xs">
                  {{ hasil.selesai_at ? new Date(hasil.selesai_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-' }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end gap-1.5 items-center">
                    <button 
                      v-if="hasil.retake_status === 'requested'"
                      @click="approveRetake(hasil.try_out_id)"
                      class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors font-bold text-[10px] flex items-center gap-1 uppercase tracking-wider cursor-pointer"
                      title="Setujui Ujian Ulang"
                    >
                      <span class="material-symbols-outlined text-xs">check_circle</span>
                      Setujui Ulang
                    </button>
                    <button 
                      @click="openEditTryOut(hasil)"
                      class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer"
                      title="Edit Nilai"
                    >
                      <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button 
                      @click="deleteTryOut(hasil.id)"
                      class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors cursor-pointer"
                      title="Hapus"
                    >
                      <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- MODAL CRUD KUIS (PRE-TEST / POST-TEST) -->
    <div 
      v-if="isKuisModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in"
    >
      <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden border border-gray-100 shadow-2xl animate-scale-in">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">assignment</span>
            <h3 class="text-base font-bold text-gray-900 font-headline">
              {{ isEditMode ? 'Edit Nilai Kuis' : 'Tambah Nilai Kuis' }}
              <span class="text-xs text-gray-400 font-semibold block">
                ({{ kuisForm.tipe_kuis === 'pre_test' ? 'Pre-Test' : 'Post-Test' }})
              </span>
            </h3>
          </div>
          <button @click="isKuisModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full transition-all cursor-pointer">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>

        <form @submit.prevent="saveKuis" class="p-6 space-y-4">
          <div v-if="!isEditMode" class="space-y-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Pilih Materi / Kuis</label>
            <select 
              v-model="kuisForm.materi_id" 
              class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary outline-none text-sm bg-white"
              required
            >
              <option value="" disabled>-- Pilih Kuis --</option>
              <option 
                v-for="materi in allMateris" 
                :key="materi.id" 
                :value="materi.id"
              >
                {{ materi.modul?.judul }}: {{ materi.judul }}
              </option>
            </select>
          </div>
          <div v-else class="bg-gray-50 p-3 rounded-xl border border-gray-100">
            <span class="text-[10px] font-black text-gray-400 uppercase block tracking-wider mb-0.5">Materi / Kuis</span>
            <span class="text-sm font-bold text-gray-800">
              {{ allMateris.find(m => m.id === kuisForm.materi_id)?.judul || 'Kuis Terpilih' }}
            </span>
          </div>

          <div class="space-y-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Nilai (0 - 100)</label>
            <input 
              v-model.number="kuisForm.score" 
              type="number" 
              min="0" 
              max="100" 
              class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary outline-none text-sm" 
              required
            />
          </div>

          <div class="flex gap-2 pt-2">
            <button type="button" @click="isKuisModalOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all cursor-pointer">
              Batal
            </button>
            <button type="submit" class="flex-1 py-2 bg-primary text-white font-bold rounded-xl text-sm transition-all hover:opacity-90 shadow cursor-pointer">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL CRUD TRY OUT -->
    <div 
      v-if="isTryOutModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in"
    >
      <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden border border-gray-100 shadow-2xl animate-scale-in">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">quiz</span>
            <h3 class="text-base font-bold text-gray-900 font-headline">
              {{ isEditMode ? 'Edit Nilai Try Out' : 'Tambah Nilai Try Out' }}
            </h3>
          </div>
          <button @click="isTryOutModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-full transition-all cursor-pointer">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>

        <form @submit.prevent="saveTryOut" class="p-6 space-y-4">
          <div v-if="!isEditMode" class="space-y-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Pilih Try Out</label>
            <select 
              v-model="tryOutForm.try_out_id" 
              class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary outline-none text-sm bg-white"
              required
            >
              <option value="" disabled>-- Pilih Ujian Try Out --</option>
              <option 
                v-for="to in allTryouts" 
                :key="to.id" 
                :value="to.id"
              >
                {{ to.judul }}
              </option>
            </select>
          </div>
          <div v-else class="bg-gray-50 p-3 rounded-xl border border-gray-100">
            <span class="text-[10px] font-black text-gray-400 uppercase block tracking-wider mb-0.5">Ujian Try Out</span>
            <span class="text-sm font-bold text-gray-800">
              {{ allTryouts.find(t => t.id === tryOutForm.try_out_id)?.judul || 'Try Out Terpilih' }}
            </span>
          </div>

          <div class="space-y-1">
            <label class="text-xs font-black text-gray-500 uppercase tracking-wider">Nilai (0 - 100)</label>
            <input 
              v-model.number="tryOutForm.nilai" 
              type="number" 
              min="0" 
              max="100" 
              class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:border-primary outline-none text-sm" 
              required
            />
          </div>

          <div class="flex gap-2 pt-2">
            <button type="button" @click="isTryOutModalOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition-all cursor-pointer">
              Batal
            </button>
            <button type="submit" class="flex-1 py-2 bg-primary text-white font-bold rounded-xl text-sm transition-all hover:opacity-90 shadow cursor-pointer">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
.bg-primary { background-color: var(--color-primary); }
.text-primary { color: var(--color-primary); }

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}

.animate-scale-in {
  animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
