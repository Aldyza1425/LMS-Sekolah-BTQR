<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api';
import { useToast } from '@/composables/useToast';
import ToastNotification from '@/components/ToastNotification.vue';

const router = useRouter();
const { success, error: showError } = useToast();
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const isLoading = ref(false);

// Bersihkan sesi lama saat halaman login dibuka
// Ini memastikan siapapun yang membuka halaman login harus autentikasi ulang
onMounted(() => {
  localStorage.removeItem('btqr_token');
  localStorage.removeItem('btqr_role');
  localStorage.removeItem('btqr_user');
});

const handleLogin = async () => {
  if (!email.value || !password.value) return;
  
  try {
    isLoading.value = true;
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    });

    if (response.data.success) {
      const { token, role } = response.data.data;
      
      // Simpan Token & Role
      localStorage.setItem('btqr_token', token);
      localStorage.setItem('btqr_role', role);
      
      success(`Selamat datang! Anda login sebagai ${role}.`)

      // Navigasi sesuai role
      if (role === 'admin') {
        router.push('/admin');
      } else if (role === 'pengajar') {
        router.push('/pengajar');
      } else {
        router.push('/siswa');
      }
    }
  } catch (err: any) {
    console.error('Login error:', err);
    const message = err.response?.data?.message || 'Login gagal. Periksa kembali email dan password Anda.';
    showError(message);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-surface flex items-center justify-center p-6">
    <ToastNotification />
    <div class="max-w-5xl w-full bg-white rounded-xl shadow-xl shadow-primary/5 overflow-hidden flex flex-col md:flex-row border border-gray-100">
      
      <!-- Left: Visual/Branding -->
      <div class="md:w-1/2 bg-primary p-12 text-white flex flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
          <div class="absolute top-[-10%] left-[-10%] w-64 h-64 border-4 border-white rounded-lg"></div>
          <div class="absolute bottom-[-20%] right-[-10%] w-96 h-96 border-4 border-white rounded-lg"></div>
        </div>
        
        
        <div class="relative z-10 flex-1 flex flex-col justify-center">
          <h1 class="text-4xl font-bold font-headline mb-4 leading-tight">
            Selamat Datang di <br/> <span class="text-primary-fixed">LMS BTQR</span>
          </h1>
          <p class="text-white/80 text-lg leading-relaxed">
            Platform pembelajaran digital untuk mencetak generasi Qur'ani yang unggul dan berakhlak mulia.
          </p>
        </div>

      </div>

      <!-- Right: Form -->
      <div class="md:w-1/2 p-8 md:p-16 flex flex-col justify-center">
        <div class="mb-10 text-center md:text-left">
          <h2 class="text-3xl font-bold text-on-surface mb-2">Masuk ke Akun</h2>
          <p class="text-gray-500">Silakan masukkan detail akun Anda untuk melanjutkan.</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 ml-1">Email atau Username</label>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors duration-300 ease-out">
                person
              </span>
              <input 
                v-model="email"
                type="text" 
                placeholder="masukkan email/username"
                class="w-full pl-12 pr-5 py-3.5 rounded-lg border border-gray-200/70 hover:border-primary/20 focus:border-primary/50 focus:ring-2 focus:ring-primary/10 outline-none transition-all duration-300 ease-out placeholder:text-gray-300"
                required
              />
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex justify-between items-center ml-1">
              <label class="text-sm font-bold text-gray-700">Kata Sandi</label>
              <a href="#" class="text-sm font-bold text-primary hover:text-[#005530] transition-colors duration-300 ease-out">Lupa Password?</a>
            </div>
            <div class="relative group">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors duration-300 ease-out">
                lock
              </span>
              <input 
                v-model="password"
                :type="showPassword ? 'text' : 'password'" 
                placeholder="masukkan kata sandi"
                class="w-full pl-12 pr-12 py-3.5 rounded-lg border border-gray-200/70 hover:border-primary/20 focus:border-primary/50 focus:ring-2 focus:ring-primary/10 outline-none transition-all duration-300 ease-out placeholder:text-gray-300"
                required
              />
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors duration-300 ease-out"
              >
                <span class="material-symbols-outlined">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
              </button>
            </div>
          </div>

          <button 
            type="submit" 
            class="w-full bg-primary text-white py-4 rounded-lg font-bold text-lg shadow-lg shadow-primary/20 hover:bg-[#005530] hover:shadow-xl hover:shadow-primary/30 transition-all duration-300 ease-out active:scale-98 cursor-pointer"
          >
            Masuk Sekarang
          </button>
        </form>
      </div>

    </div>
  </div>
</template>

<style scoped>
.font-headline { font-family: var(--font-headline); }
.text-primary { color: var(--color-primary); }
.bg-primary { background-color: var(--color-primary); }
.text-primary-fixed { color: var(--color-primary-fixed); }
.bg-primary-fixed { background-color: var(--color-primary-fixed); }
.bg-surface { background-color: var(--color-surface); }
.text-on-surface { color: var(--color-on-surface); }
</style>
