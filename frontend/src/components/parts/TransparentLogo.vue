<template>
  <div class="relative overflow-hidden flex items-center justify-center p-1" :class="containerClass">
    <svg class="absolute w-0 h-0">
      <filter id="remove-white" color-interpolation-filters="sRGB">
        <feColorMatrix type="matrix" 
          values="1 0 0 0 0
                  0 1 0 0 0
                  0 0 1 0 0
                  -1.1 -1.1 -1.1 1 1" />
      </filter>
    </svg>
    <img 
      src="/logo_btqr.png" 
      alt="Transparent Logo" 
      class="w-full h-full object-contain transition-transform duration-300"
      :style="{ 
        filter: 'url(#remove-white)',
        transform: `scale(${scale || 1.30})`
      }"
    />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  containerClass?: string;
  scale?: number;
}>();
</script>
