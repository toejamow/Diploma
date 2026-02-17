import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const isLoading = ref(false)
  const loadingMessage = ref('')
  const toastMessage = ref('')
  const showToastMessage = ref(false)

  const showToast = (message: string) => {
    toastMessage.value = message
    showToastMessage.value = true
    setTimeout(() => {
      showToastMessage.value = false
    }, 3000)
  }


  return {
    isLoading,
    loadingMessage,
    toastMessage, showToastMessage, showToast
  }
})
