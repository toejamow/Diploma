<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useNotesStore } from '../stores/notes'
import { useFollowStore } from '../stores/follow'
import AppButton from '../components/ui/AppButton.vue'
import type { Goal } from '../stores/notes'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const notesStore = useNotesStore()
const followStore = useFollowStore()


const userId = Number(route.params.id)
const userData = ref<any>(null)
const userNotes = ref<any[]>([])
const isFollowing = ref(false)

onMounted(async () => {
  const profile = await followStore.fetchUserProfile(userId)
  console.log(profile)
  if (profile) {
    userData.value = profile.user
    userNotes.value = profile.tasks
    isFollowing.value = profile.user.is_following ?? false
  }
})

const calculateProgress = (tasks?: { completed: boolean }[]) => {
  if (!tasks || tasks.length === 0) return 0
  const completed = tasks.filter(task => task.completed).length
  return Math.round((completed / tasks.length) * 100)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

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

const toggleFollow = async () => {
  if (!userData.value) return;

  if (isFollowing.value) {
    await followStore.unfollow(userData.value.id)
  } else {
    await followStore.follow(userData.value.id)
  }

  // Перезапрашиваем данные, чтобы статус точно был актуальный
  const profile = await followStore.fetchUserProfile(userId)
  if (profile) {
    userData.value = profile.user
    userNotes.value = profile.tasks
    isFollowing.value = profile.user.is_following ?? false
  }
}

const selectedStatus = ref('')
const sortBy = ref('created_at')
const sortOrder = ref('asc')
const searchQuery = ref('')

onMounted(() => fetchUserNotes(true))

const fetchUserNotes = async (showLoading = false) => {
  const profile = await followStore.fetchUserProfile(userId, {
    category: selectedCategory.value?.value || '',
    status: selectedStatus.value,
    sortBy: sortBy.value,
    sortOrder: sortOrder.value,
    search: searchQuery.value
  }, showLoading)

  if (profile) {
    userData.value = profile.user
    userNotes.value = profile.tasks
    isFollowing.value = profile.user.is_following ?? false
  }
}

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

const selectCategory = (category: typeof selectedCategory.value) => {
  selectedCategory.value = category
  showCategoryDropdown.value = false
  fetchUserNotes(false)
}

watch([selectedStatus, sortBy, sortOrder, searchQuery, selectedCategory], async () => {
  await fetchUserNotes(false)
})

const hasActiveFilters = computed(() => {
  return (
    searchQuery.value.trim() !== '' ||
    selectedStatus.value !== '' ||
    selectedCategory.value?.value !== ''
  )
})

</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- User Profile Header -->
      <div v-if="userData" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
              <span class="material-symbols-outlined scale-150 text-primary-700">
                account_circle
              </span>
              <div>
                <h1 class="text-2xl font-bold text-neutral-900">{{ userData.name }}</h1>
                <p class="text-neutral-500">{{ userData.email }}</p>

              </div>
            </div>
            <AppButton v-if="userData.id !== Number(authStore.userId)" :variant="isFollowing ? 'outline' : 'primary'"
              size="sm" @click="toggleFollow">
              {{ isFollowing ? 'Отписаться' : 'Подписаться' }}
            </AppButton>

          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input v-model="searchQuery" type="text" placeholder="Поиск..." class="w-full px-3 py-2 border rounded-lg" />

          <select v-model="selectedStatus" class="w-full px-3 py-2 border rounded-lg bg-white">
            <option value="">Все</option>
            <option value="0">Новая</option>
            <option value="1">Выполнена</option>
            <option value="2">В процессе</option>
            <option value="3">Просрочена</option>
          </select>

          <select v-model="sortBy" class="w-full px-3 py-2 border rounded-lg bg-white">
            <option value="created_at">Сортировать по дате создания</option>
            <option value="title">Сортировать по названию</option>
          </select>

          <button @click="sortOrder = sortOrder === 'asc' ? 'desc' : 'asc'" class="w-full px-3 py-2 border rounded-lg">
            {{ sortOrder === 'asc' ? 'По возрастанию' : 'По убыванию' }}
          </button>
        </div>
      </div>

      <!-- User's Notes -->
      <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-neutral-200">
          <div class="flex justify-between items-center">
            <!-- Категории -->
            <div class="flex justify-between items-center mb-4">
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
            </div>
            <span class="text-sm text-neutral-500">{{ userNotes.length }} заметок</span>
          </div>
        </div>

        <div class="p-6">
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="note in userNotes" :key="note.id"
              class="bg-neutral-50 rounded-lg p-4  transition-colors cursor-pointer group"
              @click="router.push({ name: 'ViewNote', params: { id: note.id }, query: { fromUserId: userId } })">

              <div class="flex flex-col gap-2 justify-between items-start mb-3">
                <h3 class="font-medium text-neutral-900 group-hover:text-primary-600 transition-colors">
                  {{ note.title }}
                </h3>
                <div class="flex gap-1 text-center">
                  <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(note.status)]">
                    {{ statusOptions[note.status] }}
                  </span>
                  <span
                    class="flex items-center bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                    {{ note.category?.name || 'Без категории' }}
                  </span>
                </div>
              </div>

              <!-- Прогресс -->
              <div v-if="note.goals?.length > 0" class="mb-2">
                <div class="flex justify-between text-sm text-neutral-500 mb-1">
                  <span>Прогресс выполнения</span>
                  <span>{{note.goals.filter((t: Goal) => t.is_completed).length}}/{{ note.goals.length }}</span>
                </div>
                <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
                  <div class="h-full bg-green-500 transition-all duration-300" :style="{
                    width: `${Math.round((note.goals.filter((g: Goal) => g.is_completed).length / note.goals.length) * 100)}%`
                  }"></div>
                </div>
                <div class="mt-4 text-xs text-neutral-500">
                  Создана {{ formatDate(note.created_at) }}
                </div>
              </div>

              <!-- Нет задач -->
              <div v-else class="mb-2 text-sm text-neutral-500">
                Задач нет
                <div class="mt-4 text-xs text-neutral-500">
                  Создана {{ formatDate(note.created_at) }}
                </div>
              </div>

              <!-- Превью задач -->
              <div v-if="note.goals?.length > 0" class="mb-3">
                <div class="space-y-1">
                  <div v-for="(goal, index) in note.goals.slice(0, 3)" :key="goal.id"
                    class="flex items-center space-x-2 text-xs">
                    <span class="material-symbols-outlined text-lg"
                      :class="goal.is_completed ? 'text-green-500' : 'text-neutral-400'">
                      {{ goal.is_completed ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                    <span :class="[
                      'text-xs',
                      { 'line-through text-neutral-400': goal.is_completed },
                      'line-clamp-1 sm:line-clamp-none'
                    ]">

                      {{ goal.description }}
                    </span>

                  </div>
                  <div v-if="note.goals.length > 3" class="text-xs text-neutral-400 ml-5">
                    + {{ note.goals.length - 3 }} задач
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Empty State -->
          <!-- Empty State -->
          <div v-if="userNotes.length === 0" class="text-center py-8">
            <p class="text-neutral-600">
              <template v-if="hasActiveFilters">
                По заданным параметрам заметок не найдено.
              </template>
              <template v-else>
                Пользователь еще не создал заметок.
              </template>
            </p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>