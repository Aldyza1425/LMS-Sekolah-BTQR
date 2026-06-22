<script setup lang="ts">
/**
 * SkeletonLoader — Reusable skeleton loading component
 *
 * Props:
 *  - type    : 'table' | 'card' | 'stat' | 'profile' | 'list' | 'form' | 'detail' | 'custom'
 *  - rows    : number of skeleton rows/items (default 5)
 *  - cols    : number of skeleton cards per row (default 3)
 */
defineProps<{
  type?:  'table' | 'card' | 'stat' | 'profile' | 'list' | 'form' | 'detail' | 'custom'
  rows?:  number
  cols?:  number
}>()
</script>

<template>

  <!-- ════════════ TABLE ROWS ════════════ -->
  <template v-if="type === 'table'">
    <tr v-for="i in (rows ?? 5)" :key="i" class="border-b border-gray-50">
      <td class="px-6 py-5">
        <div class="flex items-center gap-3">
          <div class="skeleton w-10 h-10 rounded-lg flex-shrink-0"></div>
          <div class="space-y-2 flex-1">
            <div class="skeleton h-3.5 rounded w-32"></div>
            <div class="skeleton h-2.5 rounded w-20"></div>
          </div>
        </div>
      </td>
      <td class="px-6 py-5">
        <div class="space-y-2">
          <div class="skeleton h-3 rounded w-36"></div>
          <div class="skeleton h-3 rounded w-24"></div>
        </div>
      </td>
      <td class="px-6 py-5 text-center">
        <div class="skeleton h-6 w-16 rounded-lg mx-auto"></div>
      </td>
      <td class="px-6 py-5 text-right">
        <div class="flex justify-end gap-2">
          <div class="skeleton w-9 h-9 rounded-lg"></div>
          <div class="skeleton w-9 h-9 rounded-lg"></div>
        </div>
      </td>
    </tr>
  </template>

  <!-- ════════════ STAT CARDS ════════════ -->
  <template v-else-if="type === 'stat'">
    <div v-for="i in (cols ?? 3)" :key="i"
      class="bg-white p-5 rounded-lg border border-gray-100 flex items-center gap-4">
      <div class="skeleton w-12 h-12 rounded-xl flex-shrink-0"></div>
      <div class="space-y-2 flex-1">
        <div class="skeleton h-2.5 rounded w-20"></div>
        <div class="skeleton h-7 rounded w-12"></div>
      </div>
    </div>
  </template>

  <!-- ════════════ MODULE / CONTENT CARDS ════════════ -->
  <template v-else-if="type === 'card'">
    <div v-for="i in (cols ?? 3)" :key="i"
      class="bg-white rounded-xl overflow-hidden border border-gray-100">
      <div class="skeleton h-48 w-full"></div>
      <div class="p-6 space-y-3">
        <div class="skeleton h-4 rounded w-3/4"></div>
        <div class="skeleton h-3 rounded w-1/2"></div>
        <div class="skeleton h-3 rounded w-full"></div>
        <div class="skeleton h-3 rounded w-2/3"></div>
        <div class="flex justify-between items-center pt-3 border-t border-gray-50">
          <div class="skeleton h-3 rounded w-1/4"></div>
          <div class="flex gap-2">
            <div class="skeleton w-9 h-9 rounded-lg"></div>
            <div class="skeleton w-9 h-9 rounded-lg"></div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <!-- ════════════ LIST ITEMS ════════════ -->
  <template v-else-if="type === 'list'">
    <div v-for="i in (rows ?? 5)" :key="i"
      class="flex items-center gap-4 px-6 py-5 border-b border-gray-50 last:border-0">
      <div class="skeleton w-12 h-12 rounded-xl flex-shrink-0"></div>
      <div class="flex-1 space-y-2">
        <div class="skeleton h-3.5 rounded w-48"></div>
        <div class="skeleton h-2.5 rounded w-32"></div>
      </div>
      <div class="skeleton h-6 w-16 rounded-lg"></div>
    </div>
  </template>

  <!-- ════════════ PROFILE ════════════ -->
  <template v-else-if="type === 'profile'">
    <div class="flex flex-col items-center gap-4 p-8">
      <div class="skeleton w-24 h-24 rounded-lg"></div>
      <div class="space-y-2 text-center w-full max-w-xs">
        <div class="skeleton h-5 rounded w-40 mx-auto"></div>
        <div class="skeleton h-3 rounded w-24 mx-auto"></div>
      </div>
    </div>
    <div class="p-6 space-y-4">
      <div v-for="i in (rows ?? 4)" :key="i" class="space-y-1.5">
        <div class="skeleton h-2.5 rounded w-20"></div>
        <div class="skeleton h-11 rounded-lg w-full"></div>
      </div>
    </div>
  </template>

  <!-- ════════════ FORM ════════════ -->
  <template v-else-if="type === 'form'">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div v-for="i in (rows ?? 4)" :key="i" class="space-y-1.5">
        <div class="skeleton h-2.5 rounded w-24"></div>
        <div class="skeleton h-11 rounded-lg w-full"></div>
      </div>
    </div>
  </template>

  <!-- ════════════ DETAIL / LEARNING PAGE ════════════ -->
  <template v-else-if="type === 'detail'">
    <div class="space-y-6">
      <div class="skeleton h-8 rounded w-64"></div>
      <div class="skeleton h-4 rounded w-48"></div>
      <div class="skeleton h-52 rounded-lg w-full"></div>
      <div class="space-y-2">
        <div v-for="i in (rows ?? 5)" :key="i"
          class="skeleton h-3 rounded"
          :style="`width: ${60 + Math.random() * 40}%`"
        ></div>
      </div>
    </div>
  </template>

</template>

<style scoped>
.skeleton {
  background: linear-gradient(
    90deg,
    #f3f4f6 25%,
    #e5e7eb 50%,
    #f3f4f6 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite linear;
  border-radius: 6px;
}

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
