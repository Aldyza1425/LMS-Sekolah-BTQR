<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { success, error } = useToast()
const pengajars = ref<any[]>([])
const loading = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1
})

const fetchPengajars = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/admin/pengajar?page=${page}`)
    pengajars.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page
    }
  } catch (err) {
    console.error('Failed to fetch pengajars:', err)
  } finally {
    loading.value = false
  }
}

const verifyPengajar = async (id: number) => {
  if (!confirm('Verifikasi pengajar ini?')) return
  
  try {
    await api.post(`/admin/pengajar/${id}/verify`)
    success('Pengajar berhasil diverifikasi!')
    fetchPengajars(pagination.value.current_page)
  } catch (err) {
    error('Gagal memverifikasi pengajar.')
  }
}

const deletePengajar = async (id: number) => {
  if (!confirm('Hapus data pengajar ini?')) return
  
  try {
    await api.delete(`/admin/pengajar/${id}`)
    success('Pengajar berhasil dihapus!')
    fetchPengajars(pagination.value.current_page)
  } catch (err) {
    error('Gagal menghapus pengajar.')
  }
}

onMounted(() => {
  fetchPengajars()
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-3xl font-headline font-extrabold text-gray-900 tracking-tight">Manajemen Pengajar</h2>
        <p class="text-gray-500 font-medium mt-1">Verifikasi dan kelola data pengajar BTQR.</p>
      </div>
      <button 
        @click="router.push({ name: 'admin.pengajar.create' })"
        class="flex items-center gap-2 px-6 py-3.5 bg-[#2563EB] text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-blue-500/20 hover:opacity-90 transition-all cursor-pointer active:scale-95 self-start md:self-auto"
      >
        <span class="material-symbols-outlined">person_add</span>
        Tambah Pengajar
      </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">pengajar</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading" v-for="i in 3" :key="i" class="animate-pulse">
              <td colspan="4" class="px-6 py-8"><div class="h-4 bg-gray-100 rounded w-full"></div></td>
            </tr>
            <tr v-else-if="pengajars.length === 0">
              <td colspan="4" class="px-6 py-20 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-200 mb-2">person_off</span>
                <p class="text-gray-400 font-medium">Belum ada data pengajar.</p>
              </td>
            </tr>
            <tr v-for="pengajar in pengajars" :key="pengajar.id" class="hover:bg-gray-50/50 transition-colors group">
              <td class="px-6 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-[#2563EB] font-bold shadow-sm">
                    {{ pengajar.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-bold text-gray-900 leading-none mb-1 group-hover:text-[#2563EB] transition-colors">{{ pengajar.name }}</p>
                    <p class="text-xs text-gray-400 font-medium">Terdaftar: {{ new Date(pengajar.created_at).toLocaleDateString() }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="space-y-1">
                  <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                    <span class="material-symbols-outlined text-sm">alternate_email</span>
                    {{ pengajar.email }}
                  </div>
                  <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                    <span class="material-symbols-outlined text-sm">phone_iphone</span>
                    {{ pengajar.phone || '-' }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 text-center">
                <span 
                  v-if="pengajar.is_active" 
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
                    @click="router.push({ name: 'admin.pengajar.edit', params: { id: pengajar.id } })"
                    class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-colors"
                    title="Edit pengajar"
                  >
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </button>
                  <button 
                    v-if="!pengajar.is_active"
                    @click="verifyPengajar(pengajar.id)"
                    class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors"
                    title="Verifikasi pengajar"
                  >
                    <span class="material-symbols-outlined text-xl">verified_user</span>
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
            @click="fetchPengajars(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold disabled:opacity-50 hover:bg-gray-50 transition-colors"
          >
            Prev
          </button>
          <button 
            @click="fetchPengajars(pagination.current_page + 1)"
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
</style>
