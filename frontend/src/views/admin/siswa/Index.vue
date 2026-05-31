<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()
const students = ref<any[]>([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1
})

// Flash Message State
const flashMessage = ref({
  show: false,
  message: '',
  type: 'success',
  progress: 100
})

let flashTimer: any = null

const showFlash = (msg: string, type: 'success' | 'error' = 'success') => {
  flashMessage.value.message = msg
  flashMessage.value.type = type
  flashMessage.value.show = true
  flashMessage.value.progress = 100
  if (flashTimer) clearInterval(flashTimer)
  const duration = 3000
  const interval = 10
  const step = (interval / duration) * 100
  flashTimer = setInterval(() => {
    flashMessage.value.progress -= step
    if (flashMessage.value.progress <= 0) {
      flashMessage.value.show = false
      clearInterval(flashTimer)
    }
  }, interval)
}

// Delete Confirm Modal
const deleteConfirm = ref({
  show: false,
  id: null as number | null,
  name: ''
})

const openDeleteConfirm = (id: number, name: string) => {
  deleteConfirm.value = { show: true, id, name }
}

const closeDeleteConfirm = () => {
  deleteConfirm.value = { show: false, id: null, name: '' }
}

const fetchStudents = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/admin/siswa?page=${page}`)
    students.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page
    }
  } catch (error) {
    console.error('Failed to fetch students:', error)
  } finally {
    loading.value = false
  }
}

const verifyStudent = async (id: number) => {
  if (!confirm('Verifikasi siswa ini?')) return
  try {
    await api.post(`/admin/siswa/${id}/verify`)
    showFlash('Siswa berhasil diverifikasi!')
    fetchStudents(pagination.value.current_page)
  } catch (err) {
    showFlash('Gagal memverifikasi siswa.', 'error')
  }
}

const confirmDelete = async () => {
  if (!deleteConfirm.value.id) return
  const id = deleteConfirm.value.id
  closeDeleteConfirm()
  try {
    await api.delete(`/admin/siswa/${id}`)
    showFlash('Siswa berhasil dihapus!')
    fetchStudents(pagination.value.current_page)
  } catch (err) {
    showFlash('Gagal menghapus siswa.', 'error')
  }
}

onMounted(() => {
  fetchStudents()
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">

    <!-- Premium Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-blue-900/10">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-[#2563EB]' : 'bg-red-500'" class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Notifikasi</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-[#2563EB]' : 'bg-red-500'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteConfirm.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="closeDeleteConfirm"></div>
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl">delete_forever</span>
        </div>
        <h3 class="text-xl font-black text-center text-gray-900 mb-2">Hapus Siswa?</h3>
        <p class="text-sm text-center text-gray-500 mb-8 leading-relaxed">
          Apakah Anda yakin ingin menghapus data siswa <strong class="text-gray-800">{{ deleteConfirm.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex gap-4">
          <button @click="closeDeleteConfirm" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
            Batal
          </button>
          <button @click="confirmDelete" class="flex-1 py-3.5 rounded-xl font-bold text-sm text-white bg-red-500 hover:bg-red-600 transition-all shadow-md flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">delete</span>
            Hapus
          </button>
        </div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight">Manajemen Siswa</h2>
        <p class="text-gray-500 font-medium mt-1">Verifikasi dan kelola data santri BTQR.</p>
      </div>
      <button 
        @click="router.push({ name: 'admin.siswa.create' })"
        class="flex items-center gap-2 px-6 py-3.5 bg-[#2563EB] text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-blue-600/20 hover:opacity-90 transition-all cursor-pointer active:scale-95 self-start md:self-auto"
      >
        <span class="material-symbols-outlined">person_add</span>
        Tambah Siswa
      </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Siswa</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading" v-for="i in 3" :key="i" class="animate-pulse">
              <td colspan="4" class="px-6 py-8"><div class="h-4 bg-gray-100 rounded w-full"></div></td>
            </tr>
            <tr v-else-if="students.length === 0">
              <td colspan="4" class="px-6 py-20 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-200 mb-2">person_off</span>
                <p class="text-gray-400 font-medium">Belum ada data siswa.</p>
              </td>
            </tr>
            <tr v-for="siswa in students" :key="siswa.id" class="hover:bg-gray-50/50 transition-colors group">
              <td class="px-6 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-[#2563EB] font-bold shadow-sm">
                    {{ siswa.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-bold text-gray-900 leading-none mb-1 group-hover:text-[#2563EB] transition-colors">{{ siswa.name }}</p>
                    <p class="text-xs text-gray-400 font-medium">Terdaftar: {{ new Date(siswa.created_at).toLocaleDateString() }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="space-y-1">
                  <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                    <span class="material-symbols-outlined text-sm">alternate_email</span>
                    {{ siswa.email }}
                  </div>
                  <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                    <span class="material-symbols-outlined text-sm">phone_iphone</span>
                    {{ siswa.phone || '-' }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 text-center">
                <span 
                  v-if="siswa.is_active" 
                  class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100"
                >
                  Aktif
                </span>
                <span 
                  v-else 
                  class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100"
                >
                  Verifikasi
                </span>
              </td>
              <td class="px-6 py-5 text-right">
                <div class="flex justify-end gap-2">
                  <button 
                    @click="router.push({ name: 'admin.siswa.edit', params: { id: siswa.id } })"
                    class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-colors"
                    title="Edit Siswa"
                  >
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </button>
                  <button 
                    v-if="!siswa.is_active"
                    @click="verifyStudent(siswa.id)"
                    class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
                    title="Verifikasi Siswa"
                  >
                    <span class="material-symbols-outlined text-xl">verified_user</span>
                  </button>
                  <button 
                    @click="openDeleteConfirm(siswa.id, siswa.name)"
                    class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors"
                    title="Hapus"
                  >
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination (only show if more than 1 page) -->
      <div v-if="pagination.last_page > 1" class="p-6 bg-gray-50/50 border-t border-gray-50 flex justify-between items-center">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}</p>
        <div class="flex gap-2">
          <button 
            @click="fetchStudents(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold disabled:opacity-50 hover:bg-gray-50 transition-colors"
          >
            Prev
          </button>
          <button 
            @click="fetchStudents(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold disabled:opacity-50 hover:bg-gray-50 transition-colors"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
.bg-primary { background-color: #2563EB; }
.text-primary { color: #2563EB; }
</style>
