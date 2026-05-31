<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/api'

const router = useRouter()
const route = useRoute()
const moduleId = route.params.id
const materiId = route.params.mid
const isEdit = !!materiId

const form = ref({
  title: '',
  type: 'Teks',
  durasi: 0,
  status: 'Published',
  content: ''
})

const file = ref<File | null>(null)
const videoLink = ref('')
const videoType = ref('Link')
const hasPreTest = ref(true)
const showTableDeleteDropdown = ref(false)

interface QuizQuestion {
  id?: number;
  soal: string;
  a: string;
  b: string;
  c: string;
  d: string;
  jawaban: string;
  [key: string]: any;
}

const preTests = ref<QuizQuestion[]>([
  { soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }
])

const hasPostTest = ref(true)
const postTests = ref<QuizQuestion[]>([
  { soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }
])

// Removed 'Teks' from buttons, it's now the default state
const optionalTypes = ['Video', 'PDF'] 
const isLoading = ref(false)

const editorRef = ref<HTMLElement | null>(null)

// For Draggable Resizing
const selectedImg = ref<HTMLImageElement | null>(null)
const isResizing = ref(false)
const startWidth = ref(0)
const startX = ref(0)

// Flash Message State
const flashMessage = ref({
  show: false,
  message: '',
  type: 'success',
  progress: 100
})

let flashTimer: any = null

const showFlash = (msg: string, type: 'success' | 'error' = 'success') => {
  flashMessage.value.message = msg
  flashMessage.value.type = type
  flashMessage.value.show = true
  flashMessage.value.progress = 100
  
  if (flashTimer) clearInterval(flashTimer)
  
  const duration = 3000
  const interval = 10
  const step = (interval / duration) * 100
  
  flashTimer = setInterval(() => {
    flashMessage.value.progress -= step
    if (flashMessage.value.progress <= 0) {
      flashMessage.value.show = false
      clearInterval(flashTimer)
    }
  }, interval)
}

// Auto-Save & Recovery Logic
const isLoaded = ref(false)
const isDraftRestored = ref(false)
const draftKey = `btqr_draft_${moduleId}_${materiId || 'new'}`

const saveDraft = () => {
  if (!isLoaded.value) return
  const draftData = {
    form: form.value,
    videoLink: videoLink.value,
    videoType: videoType.value,
    preTests: preTests.value,
    postTests: postTests.value,
    timestamp: Date.now()
  }
  localStorage.setItem(draftKey, JSON.stringify(draftData))
}

const loadDraft = () => {
  const saved = localStorage.getItem(draftKey)
  if (saved) {
    try {
      const draftData = JSON.parse(saved)
      form.value = draftData.form
      videoLink.value = draftData.videoLink || ''
      videoType.value = draftData.videoType || 'Link'
      preTests.value = draftData.preTests || []
      postTests.value = draftData.postTests || []
      
      nextTick(() => {
        if (editorRef.value) {
          editorRef.value.innerHTML = form.value.content || ''
        }
      })
      
      isDraftRestored.value = false // load silently
      return true
    } catch (e) {
      console.error('Failed to parse draft:', e)
    }
  }
  return false
}

const clearDraft = () => {
  localStorage.removeItem(draftKey)
}

const discardDraftAndReset = async () => {
  clearDraft()
  isDraftRestored.value = false
  isLoaded.value = false
  
  if (isEdit) {
    await fetchMateri()
  } else {
    form.value = {
      title: '',
      type: 'Teks',
      durasi: 0,
      status: 'Published',
      content: ''
    }
    videoLink.value = ''
    videoType.value = 'Link'
    file.value = null
    preTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
    postTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
    if (editorRef.value) {
      editorRef.value.innerHTML = ''
    }
  }
  isLoaded.value = true
  showFlash('Draf diabaikan dan formulir disetel ulang')
}

// Watch for deep updates to form fields and auto-save
watch(
  [form, videoLink, videoType, preTests, postTests],
  () => {
    saveDraft()
  },
  { deep: true }
)

const fetchMateri = async () => {
  if (!isEdit) return
  try {
    isLoading.value = true
    const response = await api.get(`/pengajar/moduls/${moduleId}/materi/${materiId}`)
    if (response.data.success) {
      const data = response.data.data
      form.value.title = data.judul
      form.value.content = data.deskripsi || ''
      form.value.durasi = data.durasi || 0
      
      if (editorRef.value) {
        editorRef.value.innerHTML = form.value.content
      }

      if (data.tipe === 'video') {
        form.value.type = 'Video'
        if (data.link_url) {
          videoType.value = 'Link'
          videoLink.value = data.link_url
        } else {
          videoType.value = 'Upload'
        }
      } else if (data.tipe === 'dokumen' && data.file_path) {
        form.value.type = 'PDF'
      } else {
        form.value.type = 'Teks'
      }

      // Quizzes are always active on this form
      hasPreTest.value = true
      hasPostTest.value = true

      if (data.soals && data.soals.length > 0) {
        const preTestSoals = data.soals.filter((s: any) => s.tipe_soal === 'pre_test')
        if (preTestSoals.length > 0) {
          preTests.value = preTestSoals.map((s: any) => ({
            id: s.id,
            soal: s.soal || '',
            a: s.a || '',
            b: s.b || '',
            c: s.c || '',
            d: s.d || '',
            jawaban: s.jawaban || 'a'
          }))
        } else {
          preTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
        }

        const postTestSoals = data.soals.filter((s: any) => s.tipe_soal === 'post_test')
        if (postTestSoals.length > 0) {
          postTests.value = postTestSoals.map((s: any) => ({
            id: s.id,
            soal: s.soal || '',
            a: s.a || '',
            b: s.b || '',
            c: s.c || '',
            d: s.d || '',
            jawaban: s.jawaban || 'a'
          }))
        } else {
          postTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
        }
      } else {
        preTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
        postTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
      }
    }
  } catch (error) {
    console.error('Error fetching materi:', error)
    showFlash('Gagal memuat materi', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  const hasDraft = loadDraft()
  if (hasDraft) {
    isLoaded.value = true
  } else {
    if (isEdit) {
      await fetchMateri()
    } else {
      preTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
      postTests.value = [{ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }]
    }
    isLoaded.value = true
  }
  window.addEventListener('mousemove', handleGlobalMouseMove)
  window.addEventListener('mouseup', stopResizing)
  window.addEventListener('scroll', handleScroll)
})

const goBackAndClearDraft = () => {
  clearDraft()
  router.back()
}

const addPreTestQuestion = () => {
  preTests.value.push({ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' })
}

const removePreTestQuestion = (index: number) => {
  if (preTests.value.length > 1) {
    preTests.value.splice(index, 1)
  } else {
    // Reset fields instead of turning off
    preTests.value[0] = { soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }
  }
}

const addPostTestQuestion = () => {
  postTests.value.push({ soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' })
}

const removePostTestQuestion = (index: number) => {
  if (postTests.value.length > 1) {
    postTests.value.splice(index, 1)
  } else {
    // Reset fields instead of turning off
    postTests.value[0] = { soal: '', a: '', b: '', c: '', d: '', jawaban: 'a' }
  }
}

const toggleType = (t: string) => {
  if (form.value.type === t) {
    form.value.type = 'Teks'
  } else {
    form.value.type = t
  }
}

const execCommand = (command: string, value: string = '') => {
  document.execCommand(command, false, value)
  updateContent()
}

const updateContent = () => {
  if (editorRef.value) {
    form.value.content = editorRef.value.innerHTML
  }
}

const handlePaste = async (e: ClipboardEvent) => {
  const items = e.clipboardData?.items
  if (!items) return
  
  for (let i = 0; i < items.length; i++) {
    const item = items[i]
    if (item && item.type.indexOf('image') !== -1) {
      e.preventDefault()
      const blob = item.getAsFile()
      if (blob) {
        const formData = new FormData()
        formData.append('image', blob)
        try {
          const res = await api.post('/pengajar/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          if (res.data.success) {
            const imageUrl = res.data.url
            const img = `<img src="${imageUrl}" class="resizable-image rounded-2xl my-4 shadow-lg border border-gray-100 cursor-pointer transition-all" style="width: 300px; display: block; margin-left: 0; margin-right: auto;">`
            document.execCommand('insertHTML', false, img)
            updateContent()
          }
        } catch (err) {
          console.error('Image upload failed:', err)
        }
      }
    }
  }
}

// Table Tools
const insertTable = () => {
  const table = `<table class="custom-table w-full border-collapse border border-gray-200 my-6"><thead><tr class="bg-gray-50 text-xs font-black uppercase tracking-widest text-gray-400"><th class="border border-gray-200 px-4 py-3 text-left">Header 1</th><th class="border border-gray-200 px-4 py-3 text-left">Header 2</th></tr></thead><tbody><tr><td class="border border-gray-200 px-4 py-3 text-gray-600">Data...</td><td class="border border-gray-200 px-4 py-3"></td></tr></tbody></table><p><br></p>`
  document.execCommand('insertHTML', false, table)
  updateContent()
}

const addRow = () => {
    const selection = window.getSelection()
    if (!selection || !selection.rangeCount) return
    const row = findParent(selection.anchorNode, 'TR') as HTMLTableRowElement
    if (row) {
        const newRow = row.cloneNode(true) as HTMLTableRowElement
        Array.from(newRow.cells).forEach(cell => cell.innerHTML = '')
        row.after(newRow)
        updateContent()
    }
}

const addColumn = () => {
    const selection = window.getSelection()
    if (!selection || !selection.rangeCount) return
    const cell = findParent(selection.anchorNode, 'TD') || findParent(selection.anchorNode, 'TH')
    if (cell) {
        const table = findParent(cell, 'TABLE') as HTMLTableElement
        const index = (cell as HTMLTableCellElement).cellIndex
        Array.from(table.rows).forEach(row => {
            const newCell = row.insertCell(index + 1)
            newCell.className = cell.className
            newCell.innerHTML = ''
        })
        updateContent()
    }
}

const deleteRow = () => {
    const selection = window.getSelection()
    if (!selection || !selection.rangeCount) return
    const row = findParent(selection.anchorNode, 'TR') as HTMLTableRowElement
    if (row) { row.remove(); updateContent() }
    showTableDeleteDropdown.value = false
}

const deleteColumn = () => {
    const selection = window.getSelection()
    if (!selection || !selection.rangeCount) return
    const cell = findParent(selection.anchorNode, 'TD') || findParent(selection.anchorNode, 'TH')
    if (cell) {
        const table = findParent(cell, 'TABLE') as HTMLTableElement
        const index = (cell as HTMLTableCellElement).cellIndex
        Array.from(table.rows).forEach(row => { row.deleteCell(index) })
        updateContent()
    }
    showTableDeleteDropdown.value = false
}

const findParent = (node: Node | null, tagName: string): HTMLElement | null => {
  let curr = node as HTMLElement | null
  while (curr && curr !== editorRef.value) {
    if (curr.tagName === tagName) return curr
    curr = curr.parentElement
  }
  return null
}

// DRAGGABLE RESIZE IMPLEMENTATION
const handleEditorMouseDown = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (target.tagName === 'IMG') {
    selectedImg.value = target as HTMLImageElement
    const rect = target.getBoundingClientRect()
    const edgeSize = 20
    if (e.clientX > rect.right - edgeSize) {
      isResizing.value = true
      startWidth.value = target.clientWidth
      startX.value = e.clientX
      e.preventDefault()
    }
  } else {
    selectedImg.value = null
  }
}

const handleGlobalMouseMove = (e: MouseEvent) => {
  if (isResizing.value && selectedImg.value) {
    const deltaX = e.clientX - startX.value
    const newWidth = startWidth.value + deltaX
    if (newWidth > 50) {
      selectedImg.value.style.width = newWidth + 'px'
    }
  }
}

const stopResizing = () => {
  if (isResizing.value) {
    isResizing.value = false
    updateContent()
  }
}

const alignImage = (align: 'left' | 'center') => {
  if (selectedImg.value) {
    if (align === 'center') {
      selectedImg.value.style.marginLeft = 'auto'
      selectedImg.value.style.marginRight = 'auto'
    } else {
      selectedImg.value.style.marginLeft = '0'
      selectedImg.value.style.marginRight = 'auto'
    }
    updateContent()
  }
}

const handleFileChange = (e: any) => {
  file.value = e.target.files[0]
}

const saveLesson = async () => {
  updateContent()
  if (!form.value.title) {
    showFlash('Judul materi wajib diisi!', 'error')
    return
  }

  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('judul', form.value.title)
    formData.append('deskripsi', form.value.content)
    
    let backendType = 'teks'
    if (form.value.type === 'Video') backendType = 'video'
    else if (form.value.type === 'PDF') backendType = 'dokumen'
    formData.append('tipe', backendType)
    formData.append('durasi', form.value.durasi.toString())
    
    if (form.value.type === 'Video') {
      if (videoType.value === 'Upload' && file.value) {
        formData.append('file', file.value)
      } else {
        formData.append('link_url', videoLink.value)
      }
    } else if (form.value.type === 'PDF' && file.value) {
        formData.append('file', file.value)
    } else if (form.value.type === 'Teks') {
        formData.append('deskripsi', form.value.content)
    }

    // Filter out completely empty questions from being submitted
    const activePreTests = preTests.value.filter(q => q.soal.trim() || q.a.trim() || q.b.trim() || q.c.trim() || q.d.trim())
    const activePostTests = postTests.value.filter(q => q.soal.trim() || q.a.trim() || q.b.trim() || q.c.trim() || q.d.trim())

    const sendPreTest = activePreTests.length > 0
    const sendPostTest = activePostTests.length > 0

    formData.append('has_pretest', sendPreTest ? '1' : '0')
    formData.append('has_posttest', sendPostTest ? '1' : '0')

    if (sendPreTest) {
        activePreTests.forEach((q, index) => {
            if (q.id) {
                formData.append(`pre_tests[${index}][id]`, q.id.toString())
            }
            formData.append(`pre_tests[${index}][soal]`, q.soal)
            formData.append(`pre_tests[${index}][a]`, q.a)
            formData.append(`pre_tests[${index}][b]`, q.b)
            formData.append(`pre_tests[${index}][c]`, q.c)
            formData.append(`pre_tests[${index}][d]`, q.d)
            formData.append(`pre_tests[${index}][jawaban]`, q.jawaban)
        })
    }

    if (sendPostTest) {
        activePostTests.forEach((q, index) => {
            if (q.id) {
                formData.append(`quizzes[${index}][id]`, q.id.toString())
            }
            formData.append(`quizzes[${index}][soal]`, q.soal)
            formData.append(`quizzes[${index}][a]`, q.a)
            formData.append(`quizzes[${index}][b]`, q.b)
            formData.append(`quizzes[${index}][c]`, q.c)
            formData.append(`quizzes[${index}][d]`, q.d)
            formData.append(`quizzes[${index}][jawaban]`, q.jawaban)
        })
    }
    
    const url = isEdit 
      ? `/pengajar/moduls/${moduleId}/materi/${materiId}`
      : `/pengajar/moduls/${moduleId}/materi`

    const response = await api.post(url, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (response.data.success) {
      clearDraft()
      showFlash('Materi berhasil disimpan')
      setTimeout(() => router.back(), 500)
    }
  } catch (error: any) {
    console.error('Save error:', error)
    if (error.response?.data?.message) {
      showFlash(error.response.data.message, 'error')
    } else {
      showFlash('Gagal menyimpan materi', 'error')
    }
  } finally {
    isLoading.value = false
  }
}

// Scroll to Top
const showScrollTop = ref(false)

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 300
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('mousemove', handleGlobalMouseMove)
  window.removeEventListener('mouseup', stopResizing)
})
</script>

<template>
  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20 font-['Plus_Jakarta_Sans']">
    
    <!-- Premium Flash Message -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="flashMessage.show" class="fixed top-8 right-8 z-[100] w-full max-w-sm overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl border border-white/20 shadow-2xl shadow-red-900/10 animate-in slide-in-from-right-8">
        <div class="p-6">
          <div class="flex items-center gap-4">
            <div :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#8B2323]'" class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg">
              <span class="material-symbols-outlined text-xl">{{ flashMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Notification</p>
              <p class="text-sm font-bold text-gray-900">{{ flashMessage.message }}</p>
            </div>
          </div>
        </div>
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
          <div 
            class="h-full transition-all duration-100 ease-linear"
            :class="flashMessage.type === 'success' ? 'bg-green-500' : 'bg-[#8B2323]'"
            :style="{ width: flashMessage.progress + '%' }"
          ></div>
        </div>
      </div>
    </transition>

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="goBackAndClearDraft" class="w-10 h-10 bg-white hover:bg-gray-50 rounded-2xl transition-all text-gray-400 shadow-sm border border-gray-100 flex items-center justify-center active:scale-95 flex-shrink-0 cursor-pointer">
          <span class="material-symbols-outlined text-xl">arrow_back</span>
        </button>
        <div>
          <div class="text-[9px] font-black text-[#8B2323] uppercase tracking-widest mb-0.5">Premium Content Editor</div>
          <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ isEdit ? 'Edit Materi' : 'Buat Materi Baru' }}</h2>
        </div>
      </div>

      <div class="flex items-center gap-3">
         <button 
           @click="saveLesson"
           :disabled="isLoading"
           class="bg-[#8B2323] text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-red-900/20 hover:shadow-2xl transition-all disabled:opacity-50"
         >
           {{ isLoading ? 'Menyimpan...' : 'Simpan Materi' }}
         </button>
      </div>
    </div>

    <!-- Recovery Banner -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="-translate-y-4 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isDraftRestored" class="bg-amber-50 border border-amber-200 rounded-[2rem] p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-in slide-in-from-top-4 duration-300">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-2xl">history</span>
          </div>
          <div>
            <p class="text-sm font-extrabold text-amber-900 font-['Plus_Jakarta_Sans']">Draf Pemulihan Otomatis Aktif</p>
            <p class="text-xs text-amber-700 font-medium font-['Plus_Jakarta_Sans']">Kami memulihkan perubahan yang belum disimpan dari sesi terakhir Anda.</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button @click="discardDraftAndReset" class="px-6 py-2.5 bg-amber-100 hover:bg-amber-200 active:scale-95 transition-all text-amber-900 rounded-xl font-black text-[9px] uppercase tracking-widest border border-amber-300 cursor-pointer">Abaikan Draf</button>
          <button @click="isDraftRestored = false" class="px-6 py-2.5 bg-amber-800 hover:bg-amber-900 active:scale-95 transition-all text-white rounded-xl font-black text-[9px] uppercase tracking-widest cursor-pointer shadow-lg shadow-amber-950/20">Gunakan Draf</button>
        </div>
      </div>
    </transition>

    <!-- Main Content Area -->
    <div class="space-y-8">
      
      <!-- Editor Card -->
      <div class="bg-white rounded-[3rem] shadow-2xl shadow-red-900/5 border border-gray-100 overflow-hidden">
        
        <!-- Advanced Toolbar -->
        <div class="px-8 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center gap-2 flex-wrap relative">
           <div class="flex items-center gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-100">
              <button @click="execCommand('bold')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]"><span class="material-symbols-outlined text-xl">format_bold</span></button>
              <button @click="execCommand('italic')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]"><span class="material-symbols-outlined text-xl">format_italic</span></button>
              <button @click="execCommand('underline')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]"><span class="material-symbols-outlined text-xl">format_underlined</span></button>
           </div>

           <div class="w-px h-6 bg-gray-200 mx-1"></div>

           <div class="flex items-center gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-100">
              <button @click="execCommand('justifyLeft')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]"><span class="material-symbols-outlined text-xl">format_align_left</span></button>
              <button @click="execCommand('justifyCenter')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]"><span class="material-symbols-outlined text-xl">format_align_center</span></button>
           </div>

           <div class="w-px h-6 bg-gray-200 mx-1"></div>

           <div class="flex items-center gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-100 relative">
              <button @click="insertTable" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-blue-600"><span class="material-symbols-outlined text-xl">table_chart</span></button>
              <button @click="addRow" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-blue-600"><span class="material-symbols-outlined text-xl">playlist_add</span></button>
              <button @click="addColumn" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-blue-600"><span class="material-symbols-outlined text-xl">view_column</span></button>
              
              <div class="relative">
                <button @click="showTableDeleteDropdown = !showTableDeleteDropdown" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-red-500">
                  <span class="material-symbols-outlined text-xl">delete_sweep</span>
                </button>
                <div v-if="showTableDeleteDropdown" class="absolute left-0 top-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 w-40 z-[100]">
                   <button @click="deleteRow" class="w-full px-4 py-2 text-left text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-red-50 hover:text-red-600 flex items-center gap-2">Hapus Baris</button>
                   <button @click="deleteColumn" class="w-full px-4 py-2 text-left text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-red-50 hover:text-red-600 flex items-center gap-2">Hapus Kolom</button>
                </div>
              </div>
           </div>

           <div class="w-px h-6 bg-gray-200 mx-1"></div>

           <!-- Image Alignment Tools -->
           <div class="flex items-center gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-100">
              <button @click="alignImage('left')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]" title="Align Image Left"><span class="material-symbols-outlined text-xl">align_horizontal_left</span></button>
              <button @click="alignImage('center')" class="p-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-400 hover:text-[#8B2323]" title="Center Image"><span class="material-symbols-outlined text-xl">align_horizontal_center</span></button>
           </div>

           <div class="ml-auto flex items-center gap-2 px-4">
              <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Klik Gambar lalu tekan Align Center untuk ke tengah</span>
           </div>
        </div>

        <div class="p-8 md:p-14 space-y-10">
          <input v-model="form.title" type="text" placeholder="Judul Materi..." class="w-full text-4xl font-black text-gray-900 placeholder:text-gray-100 outline-none border-none bg-transparent tracking-tight">

          <div 
            @mousedown="handleEditorMouseDown"
            ref="editorRef"
            contenteditable="true"
            @input="updateContent"
            @paste="handlePaste"
            class="w-full min-h-[500px] outline-none text-xl text-gray-700 leading-[1.8] font-medium placeholder:text-gray-200 prose prose-red max-w-none"
            placeholder="Mulai menulis materi pelajaran..."
          ></div>
        </div>
      </div>

      <!-- Pre Test Area -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl shadow-red-900/5 space-y-8">
          <div class="flex items-center justify-between border-b border-gray-50 pb-4">
             <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center animate-pulse">
                   <span class="material-symbols-outlined text-2xl">assignment_turned_in</span>
                </div>
                <div>
                   <h3 class="font-black text-gray-900 text-lg">Pre-Test Materi</h3>
                   <p class="text-xs text-gray-400 font-medium mt-1">Ujian awal untuk mengukur pemahaman awal siswa</p>
                </div>
             </div>
          </div>

          <div class="space-y-8 animate-in fade-in slide-in-from-top-4 duration-300">
             <div v-for="(q, idx) in preTests" :key="'pre_'+idx" class="p-8 rounded-[2.5rem] bg-gray-50 border border-gray-100 hover:bg-gray-100/50 hover:border-gray-200 transition-all duration-300 space-y-6 relative group">
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-black text-orange-600 bg-orange-100/50 px-3.5 py-1.5 rounded-xl uppercase tracking-wider">Soal {{ idx + 1 }}</span>
                  <button @click="removePreTestQuestion(idx)" class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
                <textarea v-model="q.soal" placeholder="Pertanyaan Pre-Test..." class="w-full px-6 py-4 rounded-xl bg-white border-2 border-transparent text-gray-900 placeholder:text-gray-300 hover:bg-gray-100 hover:border-gray-300 focus:bg-white focus:text-gray-900 focus:border-orange-500 outline-none font-bold resize-none transition-all duration-300"></textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="relative">
                    <input v-model="q[opt]" type="text" :placeholder="'Opsi ' + opt.toUpperCase()" class="w-full pl-12 pr-4 py-3 rounded-xl bg-white border-2 border-transparent text-gray-900 placeholder:text-gray-300 hover:bg-gray-100 hover:border-gray-300 focus:bg-white focus:text-gray-900 focus:border-orange-500 outline-none font-bold text-sm transition-all duration-300">
                    <button @click="q.jawaban = opt" class="absolute left-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg font-black text-[10px] uppercase border-2 flex items-center justify-center" :class="q.jawaban === opt ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-200 border-gray-50'">{{ opt }}</button>
                  </div>
                </div>
             </div>
             <button @click="addPreTestQuestion" class="w-full py-5 border-2 border-dashed border-gray-200 rounded-3xl text-gray-300 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-orange-50 hover:border-orange-500 hover:text-orange-600 transition-all">TAMBAH PERTANYAAN PRE-TEST</button>
          </div>
      </div>


      <!-- Post Test Area -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl shadow-red-900/5 space-y-8">
          <div class="flex items-center justify-between border-b border-gray-50 pb-4">
             <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center animate-pulse">
                   <span class="material-symbols-outlined text-2xl">quiz</span>
                </div>
                <div>
                   <h3 class="font-black text-gray-900 text-lg">Post-Test Materi</h3>
                   <p class="text-xs text-gray-400 font-medium mt-1">Ujian kecil sebelum materi dianggap selesai</p>
                </div>
             </div>
          </div>

          <div class="space-y-8 animate-in fade-in slide-in-from-top-4 duration-300">
             <div v-for="(q, idx) in postTests" :key="'post_'+idx" class="p-8 rounded-[2.5rem] bg-gray-50 border border-gray-100 hover:bg-gray-100/50 hover:border-gray-200 transition-all duration-300 space-y-6 relative group">
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-black text-green-600 bg-green-100/50 px-3.5 py-1.5 rounded-xl uppercase tracking-wider">Soal {{ idx + 1 }}</span>
                  <button @click="removePostTestQuestion(idx)" class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
                <textarea v-model="q.soal" placeholder="Pertanyaan Post-Test..." class="w-full px-6 py-4 rounded-xl bg-white border-2 border-transparent text-gray-900 placeholder:text-gray-300 hover:bg-gray-100 hover:border-gray-300 focus:bg-white focus:text-gray-900 focus:border-green-500 outline-none font-bold resize-none transition-all duration-300"></textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt" class="relative">
                    <input v-model="q[opt]" type="text" :placeholder="'Opsi ' + opt.toUpperCase()" class="w-full pl-12 pr-4 py-3 rounded-xl bg-white border-2 border-transparent text-gray-900 placeholder:text-gray-300 hover:bg-gray-100 hover:border-gray-300 focus:bg-white focus:text-gray-900 focus:border-green-500 outline-none font-bold text-sm transition-all duration-300">
                    <button @click="q.jawaban = opt" class="absolute left-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg font-black text-[10px] uppercase border-2 flex items-center justify-center" :class="q.jawaban === opt ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-200 border-gray-50'">{{ opt }}</button>
                  </div>
                </div>
             </div>
             <button @click="addPostTestQuestion" class="w-full py-5 border-2 border-dashed border-gray-200 rounded-3xl text-gray-300 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-green-50 hover:border-green-500 hover:text-green-600 transition-all">TAMBAH PERTANYAAN POST-TEST</button>
          </div>
      </div>



      <!-- Advanced Settings Area (Bottom) -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-xl shadow-red-900/5 space-y-10">
          <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center">
                  <span class="material-symbols-outlined text-2xl">settings</span>
              </div>
              <h3 class="font-black text-gray-900 text-lg">Pengaturan Tambahan</h3>
          </div>

          <div>
              <!-- Optional Attachment Selection -->
              <div class="space-y-6 max-w-xl">
                  <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lampiran Materi (Opsional)</label>
                  <div class="grid grid-cols-2 gap-3">
                      <button v-for="t in optionalTypes" :key="t" @click="toggleType(t)" class="py-4 px-6 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all text-center flex flex-col items-center gap-2 border-2" :class="form.type === t ? 'bg-[#8B2323] text-white border-[#8B2323] shadow-lg shadow-red-900/20' : 'bg-white text-gray-400 border-gray-50 hover:border-gray-200'">
                          <span class="material-symbols-outlined text-xl">{{ t === 'Video' ? 'movie' : 'picture_as_pdf' }}</span>
                          {{ t }}
                      </button>
                  </div>

                  <!-- Conditional PDF/Video Uploads -->
                  <div v-if="form.type !== 'Teks'" class="mt-6 p-6 rounded-3xl bg-gray-50 border border-gray-100 animate-in fade-in slide-in-from-top-4 duration-300">
                      <div v-if="form.type === 'Video'" class="space-y-4">
                          <div class="flex gap-2">
                             <button @click="videoType = 'Link'" class="flex-1 py-3 rounded-xl font-black text-[9px] uppercase border-2 transition-all" :class="videoType === 'Link' ? 'bg-white border-[#8B2323] text-[#8B2323]' : 'bg-transparent border-transparent text-gray-400'">Link URL</button>
                             <button @click="videoType = 'Upload'" class="flex-1 py-3 rounded-xl font-black text-[9px] uppercase border-2 transition-all" :class="videoType === 'Upload' ? 'bg-white border-[#8B2323] text-[#8B2323]' : 'bg-transparent border-transparent text-gray-400'">Upload File</button>
                          </div>
                          <input v-if="videoType === 'Link'" v-model="videoLink" type="text" placeholder="https://youtube.com/..." class="w-full px-5 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-sm">
                          <input v-else @change="handleFileChange" type="file" accept="video/*" class="w-full px-5 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-sm">
                      </div>
                      <div v-if="form.type === 'PDF'" class="space-y-4">
                          <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Lampirkan File PDF</label>
                          <input @change="handleFileChange" type="file" accept="application/pdf" class="w-full px-5 py-3 rounded-xl bg-white border border-gray-200 outline-none font-bold text-sm">
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

  <!-- Scroll to Top Button -->
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-4 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <button
      v-if="showScrollTop"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 z-50 w-12 h-12 bg-[#8B2323] text-white rounded-2xl shadow-2xl shadow-red-900/30 flex items-center justify-center hover:bg-red-900 active:scale-95 transition-all"
      title="Scroll ke atas"
    >
      <span class="material-symbols-outlined text-xl">arrow_upward</span>
    </button>
  </transition>
</template>

<style scoped>
[contenteditable]:empty:before { content: attr(placeholder); color: #e5e7eb; }
:deep(table) { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
:deep(th), :deep(td) { border: 1px solid #e5e7eb; padding: 0.75rem 1rem; min-width: 50px; }
:deep(th) { background-color: #f9fafb; }

:deep(.resizable-image) {
  transition: outline 0.2s ease;
  cursor: ew-resize; /* Indica resize horizontal */
  user-select: none;
}
:deep(.resizable-image:hover) {
  outline: 4px solid #8B2323;
  outline-offset: 4px;
}
:deep(.resizable-image:active) {
  outline: 4px solid #006D3E;
}
</style>
