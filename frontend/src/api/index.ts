import axios from 'axios';

const api = axios.create({
  // Gunakan variabel VITE_API_URL dari .env. Jika tidak ada fall back ke localhost.
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    // 'ngrok-skip-browser-warning': 'true' // Diperlukan jika server backend adalah ngrok gratisan agar tidak muncul peringatan halaman HTML
  }
});

// Middleware/Interceptor Request (Dipanggil setiap kali API dipanggil)
api.interceptors.request.use((config) => {
  // Tambahkan Token Auth secara dinamis (biasanya dari localStorage jika sudah login)
  const token = localStorage.getItem('btqr_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  
  // Header khusus supaya peringatan "Visit site" Ngrok gratisan langsung dilewati
  config.headers['ngrok-skip-browser-warning'] = 'true';
  
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Middleware/Interceptor Response — Tangani token expired / tidak valid
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Jika backend mengembalikan 401 (Unauthenticated), token tidak valid atau sudah expired
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('btqr_token')
      localStorage.removeItem('btqr_role')
      localStorage.removeItem('btqr_user')
      // Redirect ke login hanya jika belum di halaman login
      if (!window.location.pathname.startsWith('/login')) {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api;
