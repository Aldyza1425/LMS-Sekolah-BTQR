<script setup lang="ts">
import Navbar from '../parts/Navbar.vue';
import Footer from '../parts/Footer.vue';
</script>

<template>
  <div class="min-h-screen bg-surface">
    <Navbar />
    <main>
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<style scoped>
.bg-surface {
  background-color: var(--color-surface);
}
</style>
