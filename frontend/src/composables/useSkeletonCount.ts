import { ref } from 'vue'

/**
 * useSkeletonCount — menyimpan jumlah item terakhir per halaman
 * agar skeleton loading menampilkan jumlah yang sesuai dengan data nyata.
 *
 * @param key      - kunci unik per halaman (misal 'admin.siswa', 'pengajar.modul')
 * @param fallback - jumlah default jika belum pernah ada data (default 3)
 */
export function useSkeletonCount(key: string, fallback = 3) {
  const stored = parseInt(localStorage.getItem(`skeleton_count_${key}`) || String(fallback))
  const skeletonCount = ref(stored > 0 ? stored : fallback)

  const updateSkeletonCount = (count: number) => {
    if (count > 0) {
      skeletonCount.value = count
      localStorage.setItem(`skeleton_count_${key}`, String(count))
    }
  }

  return { skeletonCount, updateSkeletonCount }
}
