<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import FormInput from '../../components/auth/FormInput.vue'
import AppButton from '../../components/ui/AppButton.vue'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: ''
})

const errors = reactive({
  name: '',
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

  try {
    await authStore.signUp(form.name, form.email, form.password)
    router.push({ name: 'Notes' })
  } catch (err: any) {
    if (err.response?.status === 422) {
      const validationErrors = err.response.data.errors
      console.log(validationErrors)
      for (const key in validationErrors) {
        errors[key as keyof typeof errors] = validationErrors[key][0]
      }
    } else {
      errors.form = 'Ошибка регистрации'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-primary-50 to-neutral-50">
    <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg animate-fade-in">
      <div class="text-center">
        <h1 class="text-3xl font-bold text-neutral-900 tracking-tight">Создайте аккаунт</h1>
        <p class="mt-2 text-neutral-600">Зарегистрируйтесь, чтобы начать создавать заметки</p>
      </div>
      
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6">
        <div v-if="errors.form" class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm animate-fade-in">
          {{ errors.form }}
        </div>
        
        <FormInput
          v-model="form.name"
          label="Имя"
          required
          :error="errors.name"
          autofocus
        />
        
        <FormInput
          v-model="form.email"
          label="Email"
          type="email"
          required
          :error="errors.email"
        />
        
        <div>
          <FormInput
            v-model="form.password"
            label="Пароль"
            type="password"
            required
            :error="errors.password"
          />
        </div>
        
        <div class="space-y-4">
          <AppButton 
            type="submit" 
            variant="primary" 
            :loading="isSubmitting" 
            full
          >
            Зарегистрироваться
          </AppButton>
          
          <div class="text-center mt-4">
            <span class="text-neutral-600 text-sm">Уже есть аккаунт?</span>
            <router-link to="/signin" class="ml-1 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">Войти</router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
