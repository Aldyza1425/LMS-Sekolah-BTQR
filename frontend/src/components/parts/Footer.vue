<script setup lang="ts">
import { RouterLink } from 'vue-router';
import TransparentLogo from './TransparentLogo.vue';
</script>

<template>
  <footer class="bg-primary text-white pt-20 pb-10 px-6">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between gap-12">
      <!-- About & Socials -->
      <div class="space-y-6 md:max-w-sm w-full">
        <div class="flex items-center gap-3">
          <TransparentLogo container-class="h-18 w-18" />
          <h2 class="text-2xl font-bold font-headline">BTQR</h2>
        </div>
        <p class="text-white/80 leading-relaxed text-sm">
          Bait Tahfizh Al-Qur'an Ridhallah berkomitmen mencetak generasi Qur'ani yang berakhlak mulia, cerdas, dan mandiri melalui pendidikan berkualitas yang berlandaskan Al-Qur'an dan Sunnah.
        </p>
        <div class="flex gap-4">
          <!-- INSTAGRAM -->
          <a href="https://www.instagram.com/btqr.official/" target="_blank" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors" title="Instagram">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </a>
          <!-- TIKTOK -->
          <a href="https://www.tiktok.com/@btqr.official?_r=1&_t=ZS-95dVk2rarcQ" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors" title="TikTok">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 15.68a6.34 6.34 0 006.33 6.32 6.32 6.32 0 006.22-5.46c.05-.51.05-1.12.05-11.23a8.38 8.38 0 003.88 1.83V3.66a5.55 5.55 0 01-1.89-.97z"/></svg>
          </a>
          <!-- FACEBOOK -->
          <a href="https://www.facebook.com/share/18pimERGrU/" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors" title="Facebook">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
          </a>
        </div>
      </div>

      <!-- Kontak Kami -->
      <div class="md:w-1/3">
        <h3 class="text-xl font-bold font-headline mb-6">Kontak Kami</h3>
        <ul class="space-y-4">
          <li class="flex items-start gap-3 text-white/80">
            <span class="material-symbols-outlined mt-0.5 text-white/60">location_on</span>
            <span class="text-sm">Jl. Hadiah No 1 Lam Bheu,<br/>Darul Imarah, Aceh Besar</span>
          </li>
          <li class="flex items-center gap-3 text-white/80">
            <span class="material-symbols-outlined text-white/60">phone</span>
            <span class="text-sm">0852-4662 1617 (Ummi Inong)</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="max-w-7xl mx-auto mt-20 pt-8 border-t border-white/10 text-center text-white/60 text-sm">
      <p>&copy; {{ new Date().getFullYear() }} Bait Tahfiz Al-Qur'an Ridhallah. All rights reserved.</p>
    </div>
  </footer>
</template>

<style scoped>
.bg-primary {
  background-color: var(--color-primary);
}
</style>
