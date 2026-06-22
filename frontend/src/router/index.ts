import { createRouter, createWebHistory } from 'vue-router'
import SiswaLayout from '../components/layouts/SiswaLayout.vue'
import WebsiteLayout from '../components/layouts/WebsiteLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  },
  routes: [
    // --- Public Website Routes ---
    {
      path: '/',
      component: WebsiteLayout,
      children: [
        {
          path: '',
          name: 'website.home',
          component: () => import('../views/website/Beranda.vue')
        }
      ]
    },
    {
      path: '/login',
      name: 'lms.login',
      component: () => import('../views/lms/Login.vue')
    },
    {
      path: '/lms',
      redirect: '/login'
    },


    // --- LMS Admin Routes ---
    // --- LMS Admin Routes ---
    {
      path: '/admin',
      component: () => import('../components/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        {
          path: '',
          name: 'admin.dashboard',
          component: () => import('../views/admin/dashboard/Index.vue')
        },
        {
          path: 'pengajar',
          name: 'admin.pengajar',
          component: () => import('../views/admin/pengajar/Index.vue')
        },
        {
          path: 'pengajar/tambah',
          name: 'admin.pengajar.create',
          component: () => import('../views/admin/pengajar/Create.vue')
        },
        {
          path: 'pengajar/:id/edit',
          name: 'admin.pengajar.edit',
          component: () => import('../views/admin/pengajar/Edit.vue')
        },
        {
          path: 'siswa',
          name: 'admin.siswa',
          component: () => import('../views/admin/siswa/Index.vue')
        },
        {
          path: 'siswa/tambah',
          name: 'admin.siswa.create',
          component: () => import('../views/admin/siswa/Create.vue')
        },
        {
          path: 'siswa/:id/edit',
          name: 'admin.siswa.edit',
          component: () => import('../views/admin/siswa/Edit.vue')
        },
        {
          path: 'settings',
          name: 'admin.settings',
          component: () => import('../views/admin/settings/Index.vue')
        }
      ]
    },
    // --- LMS Pengajar Routes ---
    {
      path: '/pengajar',
      component: () => import('../components/layouts/PengajarLayout.vue'),
      meta: { requiresAuth: true, role: 'pengajar' },
      children: [
        {
          path: '',
          name: 'pengajar.dashboard',
          component: () => import('../views/pengajar/dashboard/Index.vue')
        },
        {
          path: 'modul',
          name: 'pengajar.modul',
          component: () => import('../views/pengajar/modul/Index.vue')
        },
        {
          path: 'modul/tambah',
          name: 'pengajar.modul.create',
          component: () => import('../views/pengajar/modul/Create.vue')
        },
        {
          path: 'modul/:id/kelola',
          name: 'pengajar.modul.manage',
          component: () => import('../views/pengajar/modul/Manage.vue')
        },
        {
          path: 'modul/:id/kelola/tambah-materi',
          name: 'pengajar.modul.lesson.create',
          component: () => import('../views/pengajar/modul/LessonForm.vue')
        },
        {
          path: 'modul/:id/kelola/edit-materi/:mid',
          name: 'pengajar.modul.lesson.edit',
          component: () => import('../views/pengajar/modul/LessonForm.vue')
        },
        {
          path: 'modul/:id/kelola/tambah-post-test',
          name: 'pengajar.modul.post_test.create',
          component: () => import('../views/pengajar/modul/PostTestForm.vue')
        },
        {
          path: 'modul/:id/kelola/edit-post-test/:mid',
          name: 'pengajar.modul.post_test.edit',
          component: () => import('../views/pengajar/modul/PostTestForm.vue')
        },
        {
          path: 'try-out',
          name: 'pengajar.try-out',
          component: () => import('../views/pengajar/try-out/Index.vue')
        },
        {
          path: 'try-out/tambah',
          name: 'pengajar.try-out.create',
          component: () => import('../views/pengajar/try-out/Create.vue')
        },
        {
          path: 'try-out/:id/kelola',
          name: 'pengajar.try-out.manage',
          component: () => import('../views/pengajar/try-out/Manage.vue')
        },
        {
          path: 'try-out/:id/kelola/tambah-soal',
          name: 'pengajar.try-out.soal.create',
          component: () => import('../views/pengajar/try-out/SoalForm.vue')
        },
        {
          path: 'try-out/:id/kelola/edit-soal/:sid',
          name: 'pengajar.try-out.soal.edit',
          component: () => import('../views/pengajar/try-out/SoalForm.vue')
        },
        {
          path: 'penilaian',
          name: 'pengajar.penilaian',
          component: () => import('../views/pengajar/penilaian/Index.vue')
        },
        {
          path: 'penilaian/siswa/:id',
          name: 'pengajar.penilaian.siswa',
          component: () => import('../views/pengajar/penilaian/StudentDetail.vue')
        },
        {
          path: 'profile',
          name: 'pengajar.profile',
          component: () => import('../views/pengajar/profile/Index.vue')
        }
      ]
    },
    // --- LMS Siswa Routes ---
    {
      path: '/siswa',
      component: SiswaLayout,
      meta: { requiresAuth: true, role: 'siswa' },
      children: [
        {
          path: '',
          name: 'siswa.dashboard',
          component: () => import('../views/siswa/dashboard/Index.vue'),
          meta: { title: 'Dashboard' }
        },
        {
          path: 'profile',
          name: 'siswa.profile',
          component: () => import('../views/siswa/profile/Index.vue'),
          meta: { title: 'Profile' }
        },

        {
          path: 'modul',
          name: 'siswa.modul',
          component: () => import('../views/siswa/modul/Index.vue'),
          meta: { title: 'Modul Saya' }
        },
        {
          path: 'nilai',
          name: 'siswa.nilai',
          component: () => import('../views/siswa/nilai/Index.vue'),
          meta: { title: 'Nilai Saya' }
        },
        {
          path: 'modul/:slug',
          name: 'siswa.modul.show',
          component: () => import('../views/siswa/modul/Show.vue'),
          meta: { title: 'Detail Modul' }
        },
        {
          path: 'modul/:slug/materi/:mid',
          name: 'siswa.modul.learning',
          component: () => import('../views/siswa/modul/Learning.vue'),
          meta: { title: 'Belajar' }
        },

        {
          path: 'try-out',
          name: 'siswa.try-out',
          component: () => import('../views/siswa/try-out/Index.vue')
        }
      ]
    },
    {
      path: '/siswa/modul/:slug/pretest/:mid',
      name: 'siswa.modul.pretest',
      component: () => import('../views/siswa/modul/PreTestView.vue'),
      meta: { title: 'Pre Test', requiresAuth: true, role: 'siswa' }
    },
    {
      path: '/siswa/modul/:slug/post-test/:mid',
      name: 'siswa.modul.post_test',
      component: () => import('../views/siswa/modul/PostTestView.vue'),
      meta: { title: 'Ujian', requiresAuth: true, role: 'siswa' }
    },
    {
      path: '/siswa/modul/:slug/post-test/:mid/result',
      name: 'siswa.modul.post_test.result',
      component: () => import('../views/siswa/modul/PostTestResult.vue'),
      meta: { title: 'Hasil Ujian', requiresAuth: true, role: 'siswa' }
    },
    {
      path: '/siswa/modul/:slug/pre-test/:mid/result',
      name: 'siswa.modul.pre_test.result',
      component: () => import('../views/siswa/modul/PostTestResult.vue'),
      meta: { title: 'Hasil Pre-Test', requiresAuth: true, role: 'siswa' }
    },
    {
      path: '/siswa/try-out/:id',
      name: 'siswa.try-out.show',
      component: () => import('../views/siswa/try-out/Show.vue'),
      meta: { title: 'Ujian Try Out', requiresAuth: true, role: 'siswa' }
    },
    {
      path: '/siswa/try-out/:id/result',
      name: 'siswa.try-out.result',
      component: () => import('../views/siswa/try-out/Result.vue'),
      meta: { title: 'Hasil Try Out', requiresAuth: true, role: 'siswa' }
    }
  ]
})

// --- Global Navigation Guard ---
router.beforeEach((to) => {
  const token = localStorage.getItem('btqr_token')
  const role = localStorage.getItem('btqr_role')

  // Rute yang memerlukan autentikasi
  if (to.meta.requiresAuth) {
    if (!token) {
      // Belum login → paksa ke halaman login
      return { name: 'lms.login' }
    }

    // Token ada, cek apakah role cocok dengan rute yang dituju
    if (to.meta.role && to.meta.role !== role) {
      // Role tidak cocok → arahkan ke dashboard role masing-masing
      if (role === 'admin') return { name: 'admin.dashboard' }
      if (role === 'pengajar') return { name: 'pengajar.dashboard' }
      if (role === 'siswa') return { name: 'siswa.dashboard' }
      // Role tidak dikenal → paksa login ulang
      localStorage.removeItem('btqr_token')
      localStorage.removeItem('btqr_role')
      localStorage.removeItem('btqr_user')
      return { name: 'lms.login' }
    }
  }

  // Halaman login selalu bisa diakses — tidak ada auto-redirect ke dashboard.
  // Validasi token dilakukan saat API pertama kali dipanggil (interceptor 401).
})

export default router
