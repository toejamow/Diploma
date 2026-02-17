<template>
  <Header />
  <router-view />

  <!-- оверлей -->
<div
  v-if="ui.isLoading"
  class="fixed inset-0 z-50 bg-white backdrop-blur-sm flex items-center justify-center transition-opacity duration-300"
>

  <div class="flex flex-col items-center space-y-4">
    <svg class="animate-spin h-10 w-10 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
      <path class="opacity-75" fill="currentColor"
        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
    </svg>
    <p class="text-sm text-neutral-700">{{ ui.loadingMessage }}</p>
  </div>
</div>

<Transition name="fade">
  <div v-if="ui.showToastMessage" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
    {{ ui.toastMessage }}
  </div>
</Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease ;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-30px) ;
}
</style>



<script setup lang="ts">
import Header from './components/ui/Header.vue'
import { useUiStore } from '@/stores/ui'
const ui = useUiStore()
</script>