<script setup lang="ts">
import { ref, computed, onMounted, watchEffect, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useNotesStore, type Task } from '@/stores/notes'
import { useUiStore } from '@/stores/ui'
import AppButton from '@/components/ui/AppButton.vue'
import FormInput from '@/components/auth/FormInput.vue'
import { format } from 'date-fns'

const router = useRouter()
const route = useRoute()
const notesStore = useNotesStore()

const isEditing = computed(() => route.params.id !== undefined)
const task = computed(() =>
  isEditing.value ? notesStore.getNoteById(route.params.id as string) : null
)
const originalGoalIds = ref<number[]>([])
const ui = useUiStore()

const errors = reactive({
  title: '',
  deadline: '',
  form: ''
})

onMounted(() => {
  if (task.value) {
    originalGoalIds.value = task.value.goals.map(g => g.id!)
  }
})

const title = ref('')
const deadline = ref('')
const goals = ref<{ id?: number; description: string; is_completed: boolean }[]>([])
const category = ref<number | ''>('')
const categories = [
  { label: 'Без категории', value: '' },
  { label: 'Дом', value: 1 },
  { label: 'Работа', value: 2 },
  { label: 'Личное', value: 3 },
  { label: 'Учёба', value: 4 },
  { label: 'Спорт и здоровье', value: 5 },
]

const isGeneratingGoals = ref(false)

onMounted(async () => {
  if (!notesStore.tasks.length && route.params.id) {
    await notesStore.fetchNotes()
  }
})

const progress = computed(() => {
  if (goals.value.length === 0) return 0
  const completed = goals.value.filter(task => task.is_completed).length
  return Math.round((completed / goals.value.length) * 100)
})

const addGoal = () => {
  goals.value.push({
    description: '',
    is_completed: false
  })
}

const removeGoal = (index: number) => {
  goals.value.splice(index, 1)
}

const generateGoals = async () => {
  if (!title.value) return
  isGeneratingGoals.value = true
  try {
    const generated = await notesStore.generateTasks(title.value)
    goals.value = [
      ...goals.value,
      ...generated.map(text => ({
        description: text,
        is_completed: false
      }))
    ]
  } finally {
    isGeneratingGoals.value = false
  }
}

console.log(goals.value)

const handleSubmit = async () => {
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  let hasError = false

  if (hasError) {
    ui.isLoading = false
    return
  }

  const taskData: any = {
    title: title.value,
    deadline: deadline.value ? new Date(deadline.value).toISOString() : undefined,
    // status: status.value,
    goals: goals.value.map(goal => {
      const goalData: {
        id?: number
        description: string
        is_completed: boolean
      } = {
        description: goal.description,
        is_completed: goal.is_completed
      }

      if (typeof goal.id === 'number') {
        goalData.id = goal.id
      }

      return goalData
    })
  }

  if (category.value !== '') {
    taskData.category_id = category.value
  }

  try {
    let result

    if (isEditing.value && task.value) {
      result = await notesStore.updateTask(task.value.id, taskData, originalGoalIds.value)
    } else {
      result = await notesStore.createTask(taskData)
    }

    if (result !== true) {
      if(result) {
        if (result.errors) {
          for (const key in result.errors) {
            errors[key as keyof typeof errors] = result.errors[key][0]
          }
        } else {
          errors.form = result.message
        }
      }
      return
    }
    router.push('/notes')
  } finally {
    ui.isLoading = false
  }
}

watchEffect(() => {
  if (isEditing.value && task.value) {
    title.value = task.value.title
    category.value = task.value?.category?.id ?? ''
    deadline.value = task.value.deadline
      ? format(new Date(task.value.deadline), 'yyyy-MM-dd')
      : ''
    goals.value = task.value.goals.map(g => ({
      id: g.id ?? undefined,
      description: g.description,
      is_completed: g.is_completed
    }))
  }
})

const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <div class="max-w-3xl mx-auto px-4 py-8">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-neutral-900">
            {{ isEditing ? 'Редактирование заметки' : 'Создание заметки' }}
          </h1>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-3">
          <FormInput v-model="title" label="Название" required autofocus />
          <p v-if="errors.title" class="text-sm text-red-600">{{ errors.title }}</p>
          <div class="space-y-2">
            <label class="block text-sm font-medium text-neutral-700">
              Категория
            </label>
            <select v-model="category"
              class="block w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
              <option v-for="option in categories" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-medium text-neutral-700">
              Срок сдачи*
            </label>
            <input :min="minDate" required v-model="deadline" type="date"
              class="block w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>

          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <label class="block text-sm font-medium text-neutral-700">
                Задачи
              </label>
              <div class="flex gap-2">
                <AppButton v-if="goals.length < 10" :disabled="!title" variant="secondary" size="sm"
                  :loading="isGeneratingGoals" @click="generateGoals">
                  Создать с помощью ИИ
                </AppButton>
                <AppButton v-if="goals.length < 10" variant="outline" size="sm" @click="addGoal">
                  Добавить свою задачу
                </AppButton>
              </div>
            </div>

            <div v-if="goals.length > 0" class="mb-4">
              <div class="flex justify-between text-xs text-neutral-500 mb-1">
                <span>Прогресс выполнения: {{goals.filter(g => g.is_completed).length}}/{{ goals.length }}</span>
                <span>{{ progress }}%</span>

              </div>
              <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 transition-all duration-300" :style="{ width: `${progress}%` }"></div>
              </div>
            </div>

            <div v-if="goals.length > 0" class="space-y-2">
              <div v-for="(goal, index) in goals" :key="goal.id"
                class="flex items-start gap-3 p-3 bg-neutral-50 rounded-lg group">
                <input v-model="goal.description" type="text" placeholder="Описание задачи"
                  class="flex-1 bg-transparent border-0 focus:ring-0 p-0 text-neutral-900 placeholder-neutral-400" />
                <button type="button" @click="removeGoal(index)"
                  class="opacity-0 group-hover:opacity-100 text-neutral-400 hover:text-red-500 transition-opacity">

                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </div>

            <div v-else class="text-center py-8 text-neutral-500">
              Задачи отсутствуют. Добавьте свои или сгенерируйте с помощью ИИ.
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <AppButton variant="outline" @click="router.push('/notes')">
              Отмена
            </AppButton>
            <AppButton :loading="ui.isLoading" type="submit" variant="primary">
              {{ isEditing ? 'Сохранить изменения' : 'Создать заметку' }}
            </AppButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>