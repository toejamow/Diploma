<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import FormInput from '../../components/auth/FormInput.vue'
import AppButton from '../../components/ui/AppButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const errors = reactive({
  email: '',
  password: '',
  form: ''
})

const isSubmitting = ref(false)

const handleSubmit = async () => {
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  isSubmitting.value = true

  const result = await authStore.signIn(form.email, form.password)

  isSubmitting.value = false

  if (!result.success) {
    errors.form = result.message || 'Ошибка авторизации'
    return
  }

  if(result.success) {
    router.push({ name: 'Notes' })
  }
}

</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-primary-50 to-neutral-50">
    <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg animate-fade-in">
      <div class="text-center">
        <h1 class="text-3xl font-bold text-neutral-900 tracking-tight">С возвращением</h1>
        <p class="mt-2 text-neutral-600">Войдите в систему, чтобы получить доступ к своим заметкам</p>
      </div>
      
<form @submit.prevent="handleSubmit">
        <div v-if="errors.form" class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm animate-fade-in">
          {{ errors.form }}
        </div>
        
        <FormInput
          v-model="form.email"
          label="Email"
          type="email"
          required
          :error="errors.email"
          autofocus
           autocomplete="off"
        />
        
        <FormInput
          v-model="form.password"
          label="Пароль"
          type="password"
          required
          :error="errors.password"
           autocomplete="off"
        />

        
        <div class="space-y-4">
          <AppButton 
            type="submit" 
            variant="primary" 
            :loading="isSubmitting" 
            full
          >
            Войти
          </AppButton>
          
          <div class="text-center mt-4">
            <span class="text-neutral-600 text-sm">Вы здесь впервые?</span>
            <router-link 
              to="/signup" 
              class="ml-1 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors"
            >
              Зарегистрироваться
            </router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>