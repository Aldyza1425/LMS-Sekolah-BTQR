import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePageStore = defineStore('page', () => {
  const title = ref('')

  function setTitle(newTitle: string) {
    title.value = newTitle
  }

  function resetTitle() {
    title.value = ''
  }

  return { title, setTitle, resetTitle }
})
