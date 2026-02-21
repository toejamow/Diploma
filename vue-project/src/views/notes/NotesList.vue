<script setup lang="ts">
import { computed, watch } from 'vue'
import { onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotesStore } from '@/stores/notes'
import { useUiStore } from '@/stores/ui'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const notesStore = useNotesStore()
const note = computed(() => notesStore.getNoteById(route.params.id as string))
const ui = useUiStore()

const selectedStatus = ref('')
const sortBy = ref('created_at')
const sortOrder = ref('asc')
const searchQuery = ref('')

// Категории
const categories = ref([
  { label: 'Все заметки', value: '' },
  { label: 'Дом', value: '1' },
  { label: 'Работа', value: '2' },
  { label: 'Личное', value: '3' },
  { label: 'Учёба', value: '4' },
  { label: 'Спорт и здоровье', value: '5' }


])
const selectedCategory = ref(categories.value[0])
const showCategoryDropdown = ref(false)

const toggleCategoryDropdown = () => {
  showCategoryDropdown.value = !showCategoryDropdown.value
}

const selectCategory = async (category: typeof selectedCategory.value) => {
  selectedCategory.value = category
  showCategoryDropdown.value = false
  await notesStore.fetchNotes({
    category: category.value,
    status: selectedStatus.value,
    sortBy: sortBy.value,
    sortOrder: sortOrder.value,
    search: searchQuery.value
  })
}
// ---------------------------

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const handleDelete = async (id: number) => {
  if (!confirm('Вы хотите удалить эту заметку?')) {
    if (route.name === 'ViewNote') {
      await router.push('/notes')
    }
    return
  }

  ui.isLoading = true
  ui.loadingMessage = 'Удаляем заметку...'

  try {
    await notesStore.deleteTask(id)
    await notesStore.fetchNotes()

    if (route.name === 'ViewNote') {
      await router.push('/notes')
    }
  } finally {
    ui.isLoading = false
  }
}

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/signin')
  }

  ui.isLoading = true
  ui.loadingMessage = 'Загружаем заметки..'
  await notesStore.fetchNotes()
  ui.isLoading = false
})

const progress = computed(() => {
  if (!note.value?.goals?.length) return 0
  const completed = note.value.goals.filter(goal => goal.is_completed).length
  return Math.round((completed / note.value.goals.length) * 100)
})

const statusOptions = [
  'Новая',
  'Выполнена',
  'В процессе',
  'Просрочена'
]

const getStatusColor = (status: string) => {
  const colors = {
    '0': 'bg-sky-100 text-sky-700',
    '1': 'bg-green-100 text-green-700',
    '2': 'bg-blue-100 text-blue-700',
    '3': 'bg-yellow-100 text-yellow-700'
  }
  return colors[status as keyof typeof colors] || colors[0]
}

watch([selectedStatus, sortBy, sortOrder, searchQuery, selectedCategory], async () => {
  await notesStore.fetchNotes({
    category: selectedCategory.value?.value || '',
    status: selectedStatus.value,
    sortBy: sortBy.value,
    sortOrder: sortOrder.value,
    search: searchQuery.value
  })
})

const toggleSortOrder = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
}
</script>


<template>
  <div class="min-h-screen bg-neutral-50">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex justify-between items-center mb-6">
        <!-- заголовок с выпадающим списком категорий -->
        <div class="relative">
          <button @click="toggleCategoryDropdown"
            class="text-lg font-semibold text-neutral-800 flex items-center gap-1 hover:underline cursor-pointer">
            {{ selectedCategory.label }}
            <span class="material-symbols-outlined text-base">
              {{ showCategoryDropdown ? 'expand_less' : 'expand_more' }}
            </span>
          </button>

          <div v-if="showCategoryDropdown"
            class="absolute mt-2 w-48 bg-white border border-neutral-200 rounded-lg shadow-lg z-10">
            <ul>
              <li v-for="category in categories" :key="category.value">
                <button @click="selectCategory(category)"
                  class="w-full text-left px-4 py-2 hover:bg-neutral-100 text-neutral-700">
                  {{ category.label }}
                </button>
              </li>
            </ul>
          </div>
        </div>

        <!-- Кнопки справа -->
        <div class="flex justify-between gap-5">
          <AppButton variant="primary" size="sm" @click="router.push('/notes/new')">
            Создать заметку
          </AppButton>
        </div>
      </div>


      <!-- Filters Search -->
      <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="relative">
            <input v-model="searchQuery" type="text" placeholder="Поиск по названиям заметок..."
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
          </div>

          <!-- Status Filter -->
          <div>
            <select v-model="selectedStatus"
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
              <option value="">Все</option>
              <option value="0">Новая</option>
              <option value="2">В процессе</option>
              <option value="1">Выполнена</option>
              <option value="3">Просрочена</option>
            </select>
          </div>

          <!-- Sort By -->
          <div>
            <select v-model="sortBy"
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
              <option value="created_at">Сортировать по дате создания</option>
              <option value="title">Сортировать по названию</option>
            </select>

          </div>

          <!-- Sort Order -->
          <div>
            <button @click="toggleSortOrder"
              class="w-full px-3 py-2 border border-neutral-300 rounded-lg hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 flex items-center justify-center gap-2">
              <span>{{ sortOrder === 'asc' ? 'По возрастанию' : 'По убыванию' }}</span>
              <span class="material-symbols-outlined">
                {{ sortOrder === 'asc' ? 'arrow_upward' : 'arrow_downward' }}
              </span>
            </button>
          </div>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="note in notesStore.tasks" :key="note.id"
          class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">

          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <h3 class="font-medium text-lg hover:underline text-neutral-900 line-clamp-1 cursor-pointer"
                @click="router.push(`/notes/${note.id}`)">
                {{ note.title }}
              </h3>

              <div class="flex gap-2 opacity-40 hover:opacity-100 transition-opacity">
                <button @click.stop="router.push(`/notes/${note.id}/edit`)"
                  class="text-neutral-400 hover:text-primary-600">
                  <span class="material-symbols-outlined">edit</span>
                </button>
                <button @click="handleDelete(note.id)" class="text-neutral-400 hover:text-red-500">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </div>

            <div class="mb-3 flex gap-2">
              <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(note.status)]">
                {{ statusOptions[Number(note.status)] }}
              </span>
              <span
                class="flex items-center bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                {{ note.category?.name || 'Без категории' }}
              </span>

            </div>

            <div v-if="note.goals?.length > 0" class="mb-2">
              <div class="flex justify-between text-sm text-neutral-500 mb-1">
                <span>Прогресс выполнения</span>
                <span>{{note.goals.filter(t => t.is_completed).length}}/{{ note.goals.length }}</span>
              </div>
              <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 transition-all duration-300" :style="{
                  width: `${Math.round((note.goals.filter(g => g.is_completed).length / note.goals.length) * 100)}%`
                }"></div>
              </div>
              <div class="mt-4 text-xs text-neutral-500">
                Создана {{ formatDate(note.created_at) }}
              </div>
            </div>

            <div v-else class="mb-2">
              <div class="flex justify-between text-sm text-neutral-500 mb-1">
                <span>Задач нет</span>
              </div>
              <div class="mt-7 text-xs text-neutral-500">
                Создана {{ formatDate(note.created_at) }}
              </div>
            </div>

          </div>
        </div>

        <div v-if="notesStore.tasks.length === 0" class="col-span-full bg-white rounded-lg shadow-sm p-8 text-center">
          <template v-if="selectedCategory.value != ''">
            <p class="text-neutral-600 mb-4">
              В этой категории пока нет заметок.
            </p>
          </template>
          <template v-else-if="searchQuery || selectedStatus">
            <p class="text-neutral-600 mb-4">
              По заданным параметрам заметок не найдено.
            </p>
          </template>
          <template v-else>
            <div>
              <p class="text-neutral-600 mb-4">
                Вы еще не создавали заметок...
              </p>
              <AppButton variant="primary" @click="router.push('/notes/new')">
                Создать первую заметку
              </AppButton>
            </div>
          </template>
        </div>

      </div>
    </main>
  </div>
</template>