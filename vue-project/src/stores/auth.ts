import { defineStore } from 'pinia'
import axios from '@/axios'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') as string | null,
        user: localStorage.getItem('name') || null,
        userId: localStorage.getItem('user_id') || null, // добавили userId
        errors: null as null | Record<string, string[]> | string,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user,
    },

    actions: {
        async signIn(email: string, password: string) {
            try {
                const response = await axios.post('/login', { email, password })

                this.token = response.data.token
                this.user = response.data.data.name
                this.userId = response.data.data.id // сохраняем id

                if(this.token) localStorage.setItem('token', this.token)
                if(this.user) localStorage.setItem('name', this.user)
                if(this.userId) localStorage.setItem('user_id', this.userId) // сохраняем id

                return { success: true }
            } catch (error: any) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Ошибка входа'
                }
            }
        },
        async signUp(name: string, email: string, password: string) {
            this.errors = null
            try {
                const response = await axios.post('/register', { name, email, password })
                console.log(response)

                if (response.data && !response.data.errors) {
                    router.push({ name: 'SignIn' })
                } else {
                    this.errors = response.data.errors
                }
            } catch (error: any) {
                console.error('Registration failed', error)
                this.errors = error.response?.data?.errors || error.response?.data?.message || 'Ошибка регистрации'

                throw error
            }
        },

        async logout() {
            try {
                await axios.get('/logout')

                this.token = null
                this.user = null

                localStorage.removeItem('token')
                localStorage.removeItem('name')

                router.push({ name: 'SignIn' })
            } catch (error: any) {
                console.error('Logout failed', error)
                throw error.response?.data?.message || 'Ошибка выхода'
            }
        }
    }
})