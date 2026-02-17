<!-- <script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/register'
import AppButton from '../components/ui/AppButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleSignOut = () => {
  authStore.signOut()
  router.push('/signin')
}

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/signin')
  }
})
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-neutral-900">Personal Notes</h1>
        
        <div class="flex items-center gap-4">
          <span class="text-sm text-neutral-600">
            Signed in as <strong>{{ authStore.user?.name }}</strong>
          </span>
          <AppButton 
            variant="outline" 
            size="sm" 
            @click="handleSignOut"
          >
            Sign out
          </AppButton>
        </div>
      </div>
    </header>
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center animate-fade-in">
        <h2 class="text-2xl font-semibold text-neutral-800 mb-4">
          Welcome to Your Notes
        </h2>
        <p class="text-neutral-600 text-center max-w-md mb-6">
          This is a placeholder for your notes app content. You've successfully authenticated and can now implement the notes functionality.
        </p>
        <div class="p-8 border-2 border-dashed border-neutral-300 rounded-lg w-full max-w-xl text-center">
          <p class="text-neutral-500">Your notes will appear here</p>
        </div>
      </div>
    </main>
  </div>
</template> -->