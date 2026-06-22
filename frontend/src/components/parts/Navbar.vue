<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import TransparentLogo from './TransparentLogo.vue';

const route = useRoute();
const isHomePage = computed(() => route.path === '/');

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300 px-6 py-4',
      (isScrolled || !isHomePage) ? 'bg-white shadow-md' : 'bg-transparent'
    ]"
  >
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <!-- Logo -->
      <RouterLink to="/" class="flex items-center">
        <TransparentLogo container-class="h-14 w-14" />
      </RouterLink>

      <!-- Desktop Menu / Button -->
      <div class="hidden md:flex items-center gap-6">
        <a 
          href="/login"
          class="px-7 py-2.5 rounded-lg font-bold text-sm transition-all duration-300 active:scale-95 shadow-lg"
          :class="(isScrolled || !isHomePage) ? 'bg-primary text-white shadow-primary/20 hover:opacity-80' : 'bg-white text-primary hover:bg-primary hover:text-white'"
        >
          LMS
        </a>
      </div>

      <!-- Mobile Toggle -->
      <button 
        @click="isMobileMenuOpen = !isMobileMenuOpen"
        class="md:hidden p-2 transition-colors duration-300"
        :class="(isScrolled || !isHomePage) ? 'text-on-surface' : 'text-white'"
      >
        <span class="material-symbols-outlined">
          {{ isMobileMenuOpen ? 'close' : 'menu' }}
        </span>
      </button>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="translate-y-[-20px] opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-[-20px] opacity-0"
    >
      <div 
        v-if="isMobileMenuOpen"
        class="absolute top-full left-0 right-0 bg-white shadow-xl border-t border-gray-100 md:hidden overflow-hidden"
      >
        <div class="flex flex-col p-6 gap-4">
          <a 
            href="/login"
            class="text-lg font-bold text-center px-6 py-3 rounded-[25px] transition-all duration-300 bg-primary text-white shadow-lg shadow-primary/20 active:scale-95 hover:opacity-80"
            @click="isMobileMenuOpen = false"
          >
            LMS
          </a>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<style scoped>
.text-primary {
  color: var(--color-primary);
}
.bg-primary {
  background-color: var(--color-primary);
}
.text-on-surface {
  color: var(--color-on-surface);
}
</style>
