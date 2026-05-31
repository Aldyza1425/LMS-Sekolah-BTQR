<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const router = useRouter()

const siswaList = ref<any[]>([])
const isLoading = ref(true)

const fetchPenilaian = async () => {
  try {
    const response = await api.get('/pengajar/penilaian')
    siswaList.value = response.data
  } catch (error) {
    console.error('Failed to fetch penilaian:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchPenilaian()
})
</script>

<template>
  <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 font-['Plus_Jakarta_Sans']">
    <!-- Header -->
    <div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Penilaian Siswa</h2>
      <p class="text-gray-500 font-medium mt-1">Pantau dan evaluasi pencapaian belajar santri BTQR.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Siswa</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <!-- Loading Skeleton -->
            <tr v-if="isLoading" v-for="i in 4" :key="i" class="animate-pulse">
              <td class="px-6 py-5"><div class="h-4 bg-gray-100 rounded-full w-40"></div></td>
              <td class="px-6 py-5"><div class="h-4 bg-gray-100 rounded-full w-52"></div></td>
              <td class="px-6 py-5 text-center"><div class="h-6 bg-gray-100 rounded-full w-16 mx-auto"></div></td>
              <td class="px-6 py-5 text-right"><div class="h-9 bg-gray-100 rounded-xl w-10 ml-auto"></div></td>
            </tr>
            <!-- Empty State -->
            <tr v-else-if="siswaList.length === 0">
              <td colspan="4" class="px-6 py-20 text-center">
                <span class="material-symbols-outlined text-5xl text-gray-200 block mb-2">group_off</span>
                <p class="text-gray-400 font-medium">Belum ada data siswa.</p>
              </td>
            </tr>
            <!-- Data rows -->
            <tr 
              v-for="siswa in siswaList" 
              :key="siswa.id" 
              class="hover:bg-gray-50/50 transition-colors group"
            >
              <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center text-[#8B2323] font-black text-sm shadow-sm shrink-0">
                    {{ siswa.name?.charAt(0).toUpperCase() }}
                  </div>
                  <p class="font-bold text-gray-900 group-hover:text-[#8B2323] transition-colors">{{ siswa.name }}</p>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                  <span class="material-symbols-outlined text-sm text-gray-300">alternate_email</span>
                  {{ siswa.email }}
                </div>
              </td>
              <td class="px-6 py-5 text-center">
                <span :class="siswa.is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-orange-50 text-orange-600 border-orange-100'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border">
                  {{ siswa.is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
              </td>
              <td class="px-6 py-5 text-right">
                <button 
                  @click="router.push({ name: 'pengajar.penilaian.siswa', params: { id: siswa.id } })"
                  class="p-2 bg-teal-50 text-teal-600 rounded-xl hover:bg-teal-100 transition-colors"
                  title="Lihat Detail Penilaian"
                >
                  <span class="material-symbols-outlined text-xl">visibility</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
