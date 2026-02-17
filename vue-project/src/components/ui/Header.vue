<script setup lang="ts">
import router from '@/router'
import { useAuthStore } from '../../stores/auth'
import AppButton from './AppButton.vue'
import { ref } from 'vue'
import { useNotificationsStore } from '@/stores/notifications'
import { onMounted } from 'vue'

onMounted(() => {
  if (authStore.isAuthenticated) {
    notificationsStore.fetchUnreadCount()
  }
})

const authStore = useAuthStore()
const notificationsStore = useNotificationsStore()

const handleSignOut = () => {
  authStore.logout()
  router.push('/signin')
}

const handleSignup = () => {
  router.push('/signup')
}

const handleSignIn = () => {
  router.push('/signin')
}


const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}


</script>

<template>
  <header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <!-- Логотип и бургер -->
      <div class="flex justify-between items-center w-full lg:w-auto">
        <RouterLink :to="authStore.isAuthenticated ? '/notes' : '/'" class="text-2xl font-bold text-blue-700">
          Persona
        </RouterLink>

        <!-- Бургер кнопка -->
        <button @click="toggleMobileMenu" class="lg:hidden text-neutral-700">
          <span class="material-symbols-outlined text-3xl">
            {{ isMobileMenuOpen ? 'close' : 'menu' }}
          </span>
        </button>
      </div>

      <!-- Основное меню -->
      <div class="hidden lg:flex gap-10 items-center">
        <router-link v-if="authStore.isAuthenticated" to="/follow_list"
          class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
          Подписки
        </router-link>
        <router-link v-if="authStore.isAuthenticated" to="/statistics"
          class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
          Статистика
        </router-link>
      </div>

      <!-- Кнопки -->
      <div class="hidden lg:flex items-center gap-4">
        <span class="text-sm text-neutral-600">{{ authStore.userName }}</span>

        <router-link v-if="authStore.isAuthenticated" to="/notifications"
          class="relative text-neutral-600 hover:text-neutral-900">
          <span class="material-symbols-outlined">notifications</span>
          <span v-if="notificationsStore.unreadCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
            {{ notificationsStore.unreadCount }}
          </span>
        </router-link>

        <AppButton variant="outline" size="sm" @click="handleSignOut" v-if="authStore.isAuthenticated">Выход</AppButton>
        <AppButton variant="outline" size="sm" @click="handleSignup" v-if="!authStore.isAuthenticated">
          Зарегистрироваться</AppButton>
        <AppButton variant="outline" size="sm" @click="handleSignIn" v-if="!authStore.isAuthenticated">Войти</AppButton>
      </div>
    </div>

    <!-- Мобильное меню -->
    <div v-if="isMobileMenuOpen" class="lg:hidden px-4 pb-4 space-y-2">
      <router-link v-if="authStore.isAuthenticated" to="/follow_list"
        class="block text-sm text-primary-600 hover:text-primary-700">Подписки</router-link>
      <router-link v-if="authStore.isAuthenticated" to="/statistics"
        class="block text-sm text-primary-600 hover:text-primary-700">Статистика</router-link>

      <div class="flex items-center gap-3 pt-2">
        <span class="text-sm text-neutral-600">{{ authStore.userName }}</span>

        <router-link v-if="authStore.isAuthenticated" to="/notifications"
          class="relative text-neutral-600 hover:text-neutral-900">
          <span class="material-symbols-outlined">notifications</span>
          <span v-if="notificationsStore.unreadCount > 0"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
            {{ notificationsStore.unreadCount }}
          </span>
        </router-link>
      </div>

      <div class="flex flex-col gap-2 pt-2">
        <AppButton variant="outline" size="sm" @click="handleSignOut" v-if="authStore.isAuthenticated">Выход</AppButton>
        <AppButton variant="outline" size="sm" @click="handleSignup" v-if="!authStore.isAuthenticated">
          Зарегистрироваться</AppButton>
        <AppButton variant="outline" size="sm" @click="handleSignIn" v-if="!authStore.isAuthenticated">Войти</AppButton>
      </div>
    </div>
  </header>
</template>
