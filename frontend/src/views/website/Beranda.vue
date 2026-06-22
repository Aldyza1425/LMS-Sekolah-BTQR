<script setup lang="ts">
import { RouterLink } from 'vue-router';
// Import path from generated image
const heroImage = '/hero_bg.png';
</script>

<template>
  <div class="overflow-x-hidden pt-0 scroll-smooth">
    <!-- 1. Hero Section -->
    <section class="relative min-h-[85vh] flex items-center py-20">
      <div class="absolute inset-0 z-0 bg-black">
        <img :src="heroImage" class="w-full h-full object-cover opacity-60" alt="BTQR Campus" />
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-primary-container/40"></div>
      </div>
      
      <div class="container mx-auto px-6 relative z-10 text-white">
        <div class="max-w-2xl space-y-6 animate-fade-in-up">
          <div class="inline-block px-4 py-1.5 bg-primary-fixed/20 border border-primary-fixed backdrop-blur-sm rounded-lg text-[10px] font-bold tracking-widest uppercase mb-2">
            Bait Tahfizh Al-Qur'an Ridhallah
          </div>
          <h1 class="text-4xl md:text-5xl font-bold font-headline leading-tight">
            Membangun Generasi <br/><span class="text-primary-fixed">Cinta Al-Qur'an</span> Sejak Dini
          </h1>
          <p class="text-lg md:text-xl text-white/90 font-body max-w-xl leading-relaxed">
            Menanamkan rasa bersahabat dengan Al-Qur'an menggunakan metode TABARAK yang terbukti efektif dan interaktif untuk ananda tercinta.
          </p>
          <div class="pt-4">
            <a href="#kontak" class="px-8 py-3.5 bg-primary-fixed text-primary rounded-lg font-bold text-base hover:bg-white transition-colors shadow-xl shadow-primary-fixed/20 inline-block">
              Mulai Perjalanan Ananda
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Tentang Kami -->
    <section class="py-16 bg-surface" id="tentang-kami">
      <div class="container mx-auto px-6">
        <div class="flex flex-col gap-12">
          <div class="w-full space-y-6">
            <div class="space-y-3">
              <p class="text-primary font-bold tracking-widest uppercase text-[10px]">PROFIL LEMBAGA</p>
              <h2 class="text-3xl font-bold font-headline text-gray-900">Tentang BTQR</h2>
              <div class="h-1.5 w-20 bg-primary-container rounded-lg"></div>
            </div>
            <p class="text-lg text-gray-600 leading-relaxed text-justify">
              Berdiri sejak tahun <strong>2018</strong>, <strong>Bait Tahfizh Al-Qur'an Ridhallah (BTQR)</strong> telah mendedikasikan diri untuk melahirkan generasi penghafal Al-Qur'an. Hingga saat ini, kami bangga telah meluluskan <strong>mendekati 100 alumni</strong> yang kokoh secara spiritual dan kini melanjutkan pendidikan ke tingkat jenjang SD/MI.
            </p>
            <div class="bg-primary/5 p-8 rounded-xl">
              <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary mb-1">auto_stories</span>
                <h3 class="text-xl font-bold text-primary">Metode TABARAK</h3>
              </div>
              <p class="text-gray-700 leading-relaxed">
                Kami menerapkan <strong class="text-primary">Metode TABARAK</strong>, yang digagas langsung oleh <strong>Syekh Kameel el Laboudy</strong> dari Mesir. Metode ini secara khusus dirancang untuk menciptakan kesan mendalam dan menyenangkan bagi anak-anak agar mereka cepat menghafal dan sangat bersahabat dengan Al-Qur'an di usia dini.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. Program Kami -->
    <section class="py-16 bg-white" id="program">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16 space-y-3">
          <p class="text-primary font-bold tracking-widest uppercase text-[10px]">KELAS PEMBELAJARAN</p>
          <h2 class="text-3xl font-bold font-headline text-gray-900">Program Utama Kami</h2>
          <div class="h-1.5 w-20 bg-primary-container mx-auto rounded-lg"></div>
        </div>
        
        <!-- Kategori Reguler -->
        <div class="max-w-5xl mx-auto mb-16">
          <div class="flex items-center gap-6 mb-8">
             <div class="h-px bg-gray-200 flex-grow"></div>
             <h3 class="text-lg font-bold font-headline text-primary tracking-widest uppercase flex-shrink-0">Reguler</h3>
             <div class="h-px bg-gray-200 flex-grow"></div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Program 1 -->
            <div class="p-8 md:p-10 rounded-xl bg-surface border border-gray-100 hover:border-primary/30 hover:bg-gray-50 transition-all duration-300 group shadow-lg flex flex-col justify-between">
              <div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-white shadow-lg text-primary rounded-lg flex items-center justify-center mb-6 md:mb-8 transition-colors duration-300">
                  <span class="material-symbols-outlined text-4xl md:text-5xl">toys_and_games</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-2 font-headline text-gray-900">PAUDQ</h3>
                <div class="mb-6">
                  <span class="inline-block px-4 py-1.5 bg-primary-fixed/30 text-primary rounded-lg font-bold text-sm">
                    Untuk Usia 4 - 6 Tahun
                  </span>
                </div>
                <p class="text-gray-600 leading-relaxed text-base md:text-lg">
                  Pendidikan Anak Usia Dini Al-Qur'an difokuskan untuk mengenalkan huruf hijaiyah, menanamkan rasa cinta kepada Al-Qur'an, dan bimbingan hafalan surat-surat pendek melalui pendekatan yang menyenangkan bagi anak-anak.
                </p>
              </div>
            </div>
            
            <!-- Program 2 -->
            <div class="p-8 md:p-10 rounded-xl bg-surface border border-gray-100 hover:border-primary/30 hover:bg-gray-50 transition-all duration-300 group shadow-lg flex flex-col justify-between">
              <div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-white shadow-lg text-primary rounded-lg flex items-center justify-center mb-6 md:mb-8 transition-colors duration-300">
                  <span class="material-symbols-outlined text-4xl md:text-5xl">school</span>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold mb-2 font-headline text-gray-900">RTQ</h3>
                <div class="mb-6">
                  <span class="inline-block px-4 py-1.5 bg-primary-fixed/30 text-primary rounded-lg font-bold text-sm">
                    Untuk Usia 7 - 12 Tahun
                  </span>
                </div>
                <p class="text-gray-600 leading-relaxed text-base md:text-lg">
                  Rumah Tahfizh Qur'an adalah program lanjutan untuk memperkuat dan menambah hafalan Al-Qur'an, edukasi adab, tajwid, serta membangun karakter kemandirian islami yang kuat sejak masa sekolah dasar.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Kategori Non Reguler -->
        <div class="max-w-5xl mx-auto">
          <div class="flex items-center gap-6 mb-8">
             <div class="h-px bg-gray-200 flex-grow"></div>
             <h3 class="text-lg font-bold font-headline text-primary tracking-widest uppercase flex-shrink-0">Non Reguler</h3>
             <div class="h-px bg-gray-200 flex-grow"></div>
          </div>

          <div class="max-w-4xl mx-auto">
            <!-- Program 3: Bimbingan Bahasa Arab -->
            <div class="p-8 md:p-10 rounded-xl bg-gradient-to-br from-white to-primary/5 hover:from-gray-50 hover:to-primary/10 hover:border-primary/20 border border-gray-100 transition-all duration-300 group shadow-lg relative overflow-hidden flex flex-col md:flex-row gap-6 md:gap-8 items-start md:items-center">
              <!-- Decorative elements -->
              <span class="material-symbols-outlined absolute -right-4 -bottom-8 text-[9rem] text-primary/5 pointer-events-none transform -rotate-12">language</span>
              <div class="w-16 h-16 md:w-20 md:h-20 bg-white shadow-lg text-primary rounded-lg flex items-center justify-center transition-colors duration-300 relative z-10 flex-shrink-0">
                <span class="material-symbols-outlined text-4xl md:text-5xl">flight_takeoff</span>
              </div>
              <div class="relative z-10 flex-grow">
                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-3">
                  <h3 class="text-2xl md:text-3xl font-bold font-headline text-gray-900">Bimbingan Bahasa Arab</h3>
                  <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary text-white rounded-lg font-bold text-xs shadow-md">
                      <span class="material-symbols-outlined text-sm">public</span>
                      Persiapan Kuliah ke Timur Tengah
                    </span>
                  </div>
                </div>
                <p class="text-gray-700 leading-relaxed text-base md:text-lg mb-2">
                  Wujudkan impian menapakkan kaki di Al-Azhar (Mesir), Yaman, Maroko, atau Yordania! Ini adalah bimbingan eksklusif hasil <strong>kolaborasi dengan Fakultas Adab & Humaniora UIN Ar-Raniry</strong>.
                </p>
                <p class="text-gray-600 leading-relaxed text-sm">
                  Peserta akan dibimbing langsung secara intensif oleh Dosen Bahasa & Sastra Arab lulusan Universitas Al-Azhar Mesir.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. Visi & Misi -->
    <section class="py-16 bg-primary text-white relative overflow-hidden" id="visi-misi">
      <div class="absolute inset-0 opacity-5 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>

      <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-5xl mx-auto space-y-20">
          
          <!-- Visi -->
          <div class="text-center space-y-8">
            <h2 class="text-3xl md:text-4xl font-black font-headline text-primary-fixed uppercase tracking-widest drop-shadow-md">Visi Kami</h2>
            <div class="text-2xl md:text-3xl font-light leading-relaxed italic border-y border-white/20 py-10">
              "Melahirkan generasi cinta Al-Qur'an sejak usia dini, membentuk pemimpin yang ber-Akhlaq Qur'ani, dan menyiapkan generasi Hafizh Qur'an yang berkarakter Islami."
            </div>
          </div>

          <!-- Misi -->
          <div class="bg-white/10 backdrop-blur-md rounded-xl p-8 md:p-14 border border-white/20 shadow-2xl">
            <h2 class="text-3xl font-black font-headline text-primary-fixed mb-12 text-center uppercase tracking-widest">Misi Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">1</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Menyiapkan peserta didik yang mampu membaca Al-Qur'an dengan baik serta memiliki hafalan sejak usia dini.</p>
              </div>
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">2</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Membiasakan peserta didik meneladani Rasulullah Saw. dalam bersikap dan bertutur kata di kehidupan sehari-hari.</p>
              </div>
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">3</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Membiasakan melakukan yang benar, bukan membenarkan kebiasaan.</p>
              </div>
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">4</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Mewujudkan generasi muslim penghafal Al-Qur'an yang disiplin, berilmu, tanggung jawab, jujur, religius, kreatif, dan mandiri.</p>
              </div>
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">5</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Membiasakan berperilaku sesuai ajaran agama dan norma yang berlaku di masyarakat.</p>
              </div>
              <div class="flex gap-5 items-start">
                <div class="w-12 h-12 rounded-lg bg-primary-fixed text-primary flex items-center justify-center flex-shrink-0 font-black text-xl shadow-lg">6</div>
                <p class="text-white/90 text-lg leading-relaxed pt-1">Melibatkan orang tua secara aktif untuk kerjasama dalam menjalankan proses pendidikan di rumah.</p>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </section>

    <!-- 5. Mengapa Memilih BTQR -->
    <section class="py-16 bg-surface" id="keunggulan">
      <div class="container mx-auto px-6">
        <div class="text-center mb-16 space-y-3">
          <h2 class="text-3xl font-bold font-headline text-primary">Mengapa Memilih BTQR?</h2>
          <div class="h-1.5 w-20 bg-primary-container mx-auto rounded-lg"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="(item, i) in whyChooseUs" :key="i" class="bg-white p-8 rounded-xl border border-gray-100 hover:border-primary/25 hover:bg-gray-50 shadow-md transition-all duration-300 text-center group">
            <div class="w-20 h-20 bg-primary-container text-primary rounded-lg flex items-center justify-center mx-auto mb-6 transition-colors duration-300">
              <span class="material-symbols-outlined text-4xl">{{ item.icon }}</span>
            </div>
            <h3 class="text-xl font-bold mb-4 font-headline text-gray-900">{{ item.title }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </section>


    <!-- 7. Kutipan Islami -->
    <section class="py-16 bg-surface border-y border-gray-200 relative overflow-hidden">
      <!-- Watermark icon behind -->
      <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[20rem] text-primary/5 blur-sm z-0 pointer-events-none">auto_stories</span>
      
      <div class="container mx-auto px-6 text-center max-w-4xl relative z-10">
        <span class="material-symbols-outlined text-5xl text-primary mb-8 bg-primary/10 p-4 rounded-lg">format_quote</span>
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-10 leading-relaxed font-arabic" dir="rtl" style="font-family: serif; line-height: 1.6;">
          خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ
        </h2>
        <p class="text-2xl font-medium text-gray-700 italic mb-6">
          "Sebaik-baik orang di antara kamu adalah orang yang belajar Al-Qur'an dan mengajarkannya."
        </p>
        <p class="text-primary font-bold tracking-widest uppercase bg-primary/10 inline-block px-4 py-2 rounded-lg">
          (HR. Bukhari)
        </p>
      </div>
    </section>

    <!-- 8. Kontak & Lokasi -->
    <section class="py-16 bg-white" id="kontak">
      <div class="container mx-auto px-6">
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-12">
          <div class="w-full lg:w-1/2 space-y-8">
            <div class="space-y-3">
              <p class="text-primary font-bold tracking-widest uppercase text-[10px]">SILATURAHMI</p>
              <h2 class="text-3xl font-bold font-headline text-gray-900">Hubungi & Kunjungi Kami</h2>
              <div class="h-1.5 w-20 bg-primary-container rounded-lg"></div>
            </div>
            
            <p class="text-gray-600 text-lg leading-relaxed">
              Kami menyambut dengan hangat silaturahmi dari Ayah/Bunda untuk berdiskusi langsung mengenai pendidikan Ananda tersayang. Hubungi nomor di bawah ini atau kunjungi alamat kami kapan pun Anda membutuhkan bantuan.
            </p>
            
            <div class="space-y-8 bg-surface p-8 rounded-xl border border-gray-100">
              <div class="flex items-start gap-5">
                <div class="w-14 h-14 bg-white shadow text-primary rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-2xl">location_on</span>
                </div>
                <div class="pt-1">
                  <h4 class="font-bold text-gray-900 text-lg mb-1">Alamat Lengkap</h4>
                  <p class="text-gray-600 leading-relaxed">
                    Jl. Hadiah No 1 Lam Bheu,<br/>
                    Darul Imarah, Aceh Besar
                  </p>
                </div>
              </div>
              
              <div class="flex items-start gap-5">
                <div class="w-14 h-14 bg-white shadow text-primary rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-2xl">phone_iphone</span>
                </div>
                <div class="pt-1">
                  <h4 class="font-bold text-gray-900 text-lg mb-1">Telepon & WhatsApp</h4>
                  <p class="text-gray-600 leading-relaxed font-medium">0852-4662 1617 <span class="text-gray-400 font-normal">(Ummi Inong)</span></p>
                </div>
              </div>
              
              <div class="flex items-start gap-5">
                <div class="w-14 h-14 bg-white shadow text-primary rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-2xl">alternate_email</span>
                </div>
                <div class="pt-1">
                  <h4 class="font-bold text-gray-900 text-lg mb-1">Instagram</h4>
                  <a href="https://www.instagram.com/btqr.official/" target="_blank" class="text-primary hover:underline leading-relaxed font-medium">@baittahfizhalquranridhallah</a>
                </div>
              </div>
            </div>
          </div>
          
          <div class="w-full lg:w-1/2">
            <div class="w-full h-full min-h-[400px] bg-primary/5 rounded-[2.5rem] p-3 border border-primary/10 shadow-lg">
              <div class="w-full h-full rounded-[2rem] bg-gray-200 overflow-hidden relative shadow-inner">
                  <iframe 
                    src="https://maps.google.com/maps?q=Jl.%20Hadiah%20No%201%20Lam%20Bheu,%20Darul%20Imarah,%20Aceh%20Besar&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                    class="absolute inset-0 w-full h-full border-0 grayscale-[20%] hover:grayscale-0 transition duration-500"
                    allowfullscreen="false" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script lang="ts">
const whyChooseUs = [
  { icon: 'public', title: 'Metode Global TABARAK', desc: 'Menggunakan Metode TABARAK dari Mesir yang terbukti efektif, interaktif, dan bersahabat dengan anak-anak.' },
  { icon: 'verified', title: 'Telah Terbukti Nyata', desc: 'Melahirkan mendekati 100 alumni tangguh yang kini berhasil melanjutkan pendidikan formal dengan fondasi agama yang kuat.' },
  { icon: 'psychology_alt', title: 'Pendidikan Berkarakter', desc: 'Tidak hanya fokus hafalan, melainkan juga menekankan pada penanaman adab, akhlak mulia dan kedisiplinan islami.' },
  { icon: 'diversity_1', title: 'Sinergi Orang Tua', desc: 'Kami secara aktif senantiasa melibatkan orang tua dari santri untuk menjaga keselarasan visi asuhan di rumah dan di sekolah.' }
];

export default {
  data() {
    return { whyChooseUs }
  }
}
</script>

<style scoped>
.text-primary { color: var(--color-primary); }
.bg-primary { background-color: var(--color-primary); }
.bg-primary-dark { background-color: #1a6e43; } /* Slightly darker primary assumption */
.bg-surface { background-color: var(--color-surface); }
.bg-primary-container { background-color: var(--color-primary-container); }
.text-primary-fixed { color: var(--color-primary-fixed); }
.bg-primary-fixed { background-color: var(--color-primary-fixed); }

.animate-fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

html {
  scroll-behavior: smooth;
}
</style>
