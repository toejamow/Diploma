<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import axios from 'axios'
import router from '@/router'
import AppButton from '@/components/ui/AppButton.vue'
import type { Task } from '@/stores/notes'

const ui = useUiStore()
const route = useRoute()
const note = ref<Task | null>(null)

onMounted(async () => {
    ui.isLoading = true

    const token = route.params.token
    try {
        const response = await axios.get(`http://localhost:8000/api/public-note/${token}`)
        console.log('Содержимое ответа:', response.data)

        const noteData = response.data.data
        if (!noteData || !noteData.goals) throw new Error('Некорректный формат данных')

        // Преобразуем is_completed к boolean
        noteData.goals.forEach((goal: any) => {
            goal.is_completed = !!goal.is_completed
        })

        note.value = noteData
    } catch (error: any) {
        console.error('Ошибка при получении заметки:', error)
        note.value = null
    } finally {
        console.log(note)
        ui.isLoading = false

    }
})

const statusOptions = [
    'Новая',
    'Выполнена',
    'В процессе',
    'Просрочена'
]

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        0: 'bg-sky-100 text-sky-700',
        1: 'bg-green-100 text-green-700',
        2: 'bg-blue-100 text-blue-700',
        3: 'bg-orange-100 text-orange-700'
    }
    return colors[status] || 'bg-neutral-100 text-neutral-700'
}

const progress = computed(() => {
    if (!note.value?.goals?.length) return 0
    const completed = note.value.goals.filter(goal => goal.is_completed).length
    return Math.round((completed / note.value.goals.length) * 100)
})

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const viewProfile = (userId: number) => {
  router.push(`/user/${userId}`)
}
</script>

<template>
    <div class="min-h-screen bg-neutral-50">
        <div class="max-w-2xl mx-auto px-4 py-10">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div v-if="note" class="space-y-4">
                    
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-bold text-neutral-900">{{ note.title }}</h1>
                        <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(note.status)]">
                            {{ statusOptions[Number(note.status)] }}
                        </span>
                    </div>

                    <div class="text-sm text-neutral-500">
                        Создана {{ formatDate(note.created_at) }} &nbsp;|&nbsp; Срок: {{ formatDate(note.deadline) }}
                    </div>

                    <div @click="viewProfile(note.user_id)" class="mb-6 p-2 bg-neutral-50 rounded-lg cursor-pointer">
                        <div class="flex items-center gap-3 justify-between">
                            <div class="flex gap-2">

                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary-700">
                                        account_circle
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900">
                                        {{ note.user?.[0]?.name || 'Аноним' }}
                                    </p>
                                    <p class="text-sm text-neutral-500">
                                        {{ note.user?.[0]?.email || 'Почта не найдена' }}
                                    </p>
                                    
                                </div>
                            </div>
                            <AppButton @click="viewProfile(note.user_id)" size="sm">Перейти на профиль</AppButton>
                        </div>
                    </div>

                    
                    <div v-if="note.goals.length" class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-neutral-500 mb-1">
                                <span>Прогресс выполнения</span>
                                <span>
                                    {{note.goals.filter(g => g.is_completed).length}}/{{ note.goals.length }}
                                </span>
                            </div>
                            <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 transition-all duration-300"
                                    :style="{ width: `${progress}%` }"></div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div v-for="goal in note.goals" :key="goal.id"
                                class="flex items-start gap-3 p-3 bg-neutral-50 rounded-lg">
                                <input v-if="false" type="checkbox" :checked="goal.is_completed" disabled
                                    class="mt-1 h-4 w-4 text-primary-600 border-neutral-300 rounded" />
                                <span
                                    :class="{ 'line-through text-neutral-400': goal.is_completed || note.status == '4' }">
                                    {{ goal.description }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-neutral-500">
                        У этой заметки нет задач.
                    </div>
                </div>

                <div v-else class="text-center text-neutral-500">
                    <p>Заметка не найдена или недоступна.</p>
                </div>
            </div>
        </div>
    </div>
</template>
