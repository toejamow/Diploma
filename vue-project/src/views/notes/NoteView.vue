<script setup lang="ts">
import { ref, computed, onMounted, watchEffect } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useNotesStore } from '@/stores/notes'
import { useUiStore } from '@/stores/ui'
import AppButton from '@/components/ui/AppButton.vue'
import axios from '@/axios'
import { useAuthStore } from '@/stores/auth'
import type { Goal } from '@/stores/notes'
import type { Task } from '@/stores/notes'

const router = useRouter()
const route = useRoute()
const notesStore = useNotesStore()
const ui = useUiStore()
const publicUrl = ref<string | null>(null)
const isMakingPublic = ref(false)
const errorMessage = ref('')

const togglePublic = async () => {
  if (!note.value) return;

  isMakingPublic.value = true;
  errorMessage.value = '';

  if (publicUrl.value) {
    try {
      await axios.post(`/revoke-share/${note.value.id}`);
      publicUrl.value = null;
    ui.showToast('Ссылка удалена!');
    } catch (error: any) {
      errorMessage.value = error.response?.data?.message || 'Ошибка при удалении ссылки';
    } finally {
      isMakingPublic.value = false;
    }
  } else {
    await makeNotePublic();
  }
};

const makeNotePublic = async () => {
  if (!note.value) return;

  isMakingPublic.value = true;
  errorMessage.value = '';
  publicUrl.value = null;

  try {
    const response = await axios.post(`/make-public/${note.value.id}`);
    publicUrl.value = response.data.public_url;
    if(publicUrl.value) {
      await navigator.clipboard.writeText(publicUrl.value);
    }
    ui.showToast('Ссылка скопирована в буфер обмена!');
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Ошибка при публикации';
  } finally {
    isMakingPublic.value = false;
  }
};

const copyUrl = async () => {
  if (!publicUrl.value) return;

  try {
    await navigator.clipboard.writeText(publicUrl.value);
    ui.showToast('Ссылка скопирована в буфер обмена!');
  } catch (err) {
    console.error('Ошибка при копировании ссылки', err);
    alert('Не удалось скопировать ссылку');
  }
}

const note = ref<Task | null>(null)
const noteId = route.params.id as string

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

onMounted(async () => {
  ui.isLoading = true
  const noteData = await notesStore.fetchNoteById(noteId)

  //  is_completed в булевое значение=
  noteData.goals.forEach((goal: Goal) => {
    goal.is_completed = !!goal.is_completed
  })

  note.value = noteData
  ui.isLoading = false
})

const handleGoalChange = async (goal: Goal) => {
  try {
    await axios.put(`/update-goal/${goal.id}`, { is_completed: goal.is_completed })
  } catch (error) {
    console.error('Failed to update goal', error)
  }
}

const progress = computed(() => {
  if (!note.value?.goals?.length) return 0
  const completed = note.value.goals.filter(goal => goal.is_completed).length
  return Math.round((completed / note.value.goals.length) * 100)
})

const handleDelete = async () => {
  if (!note.value) return

  if (confirm('Вы хотите удалить эту заметку?')) {
    await notesStore.deleteTask(note.value.id)
    router.push('/notes')
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

watchEffect(() => {
  if (note.value && note.value.public_token) {
    publicUrl.value = `${window.location.origin}/shared/${note.value.public_token}`;
  } else {
    publicUrl.value = null;
  }
});

const authStore = useAuthStore()
const isOwner = computed(() => {
  return note.value?.user_id === Number(authStore.userId)
})

const fromUserId = computed(() => {
  const queryId = Number(route.query.fromUserId)
  return isNaN(queryId) ? null : queryId
})

const handleBack = () => {
  if (fromUserId.value && fromUserId.value !== Number(authStore.userId)) {
    router.push(`/user/${fromUserId.value}`)
  } else {
    router.push('/notes')
  }
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <div class="max-w-3xl mx-auto px-4 py-8">
      <div v-if="note" class="bg-white rounded-xl shadow-sm p-6">
        <div class=" flex-col gap-3 md:gap-1 md:flex-row flex justify-between items-start mb-6">
          <div >
            <h1 class="text-2xl font-bold text-neutral-900">
              {{ note.title }}
            </h1>

            <div class="mt-2 space-y-2">
              <div class="flex gap-2">

                <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(note.status)]">
                  {{ statusOptions[Number(note.status)] }}
                </span>
                  <span class="flex items-center bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">
                    {{ note.category?.name || 'Без категории' }}
                </span>
              </div>
              <p v-if="note.deadline" class="text-sm text-neutral-500 mt-1">
                        Создана {{ formatDate(note.created_at) }} &nbsp;|&nbsp; Срок: {{ formatDate(note.deadline) }}
              </p>
            </div>
          </div>

          <div class="flex gap-2 " v-if="isOwner">
            <AppButton variant="outline" size="sm" @click="router.push(`/notes/${note.id}/edit`)">
              Редактировать
            </AppButton>
            <AppButton variant="outline" size="sm" @click="handleDelete">
              Удалить
            </AppButton>

          </div>
        </div>

        <!-- progress bar -->
        <div v-if="note.goals?.length > 0" class="mb-6">
          <div class="flex justify-between text-sm text-neutral-500 mb-2">
            <span>Прогресс выполнения</span>
            <span>{{note.goals.filter(t => t.is_completed).length}}/{{ note.goals.length }}</span>
          </div>
          <div class="h-2 bg-neutral-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-500 transition-all duration-300" :style="{ width: `${progress}%` }"></div>
          </div>
        </div>

        <!-- tasks list -->
        <div v-if="note.goals?.length > 0" class="space-y-2">
          <div v-for="goal in note.goals" :key="goal.id" class="flex items-start gap-3 p-3 bg-neutral-50 rounded-lg">
            <input   type="checkbox" @change="handleGoalChange(goal)" v-model="goal.is_completed"
              v-if="isOwner && note.status !== '1' && note.status !== '3'"
              class="mt-1 h-4 w-4 text-primary-600 border-neutral-300 rounded focus:ring-primary-500" />
            <span
              :class="{ 'line-through text-neutral-400': goal.is_completed || note.status == '3' }">
              {{ goal.description }}
            </span>
          </div>
        </div>

        <div v-else class="text-center py-8 text-neutral-500">
          У этой цели нет задач.
        </div>

        <div class="flex flex-col-reverse gap-3 md:gap-1 md:flex-row mt-6 pt-6 border-t flex justify-between">
          <AppButton variant="outline" @click="handleBack">
            Вернуться к списку заметок
          </AppButton>

          <div v-if="isOwner">
            <button loading="isMakingPublic"
              class="px-3 h-11 text-base font-medium border border-neutral-300 bg-white hover:bg-neutral-50 text-neutral-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
              :class="publicUrl ? 'rounded-l-lg border-r-0' : 'rounded-lg' "
              @click="togglePublic">
              {{ publicUrl ? 'Удалить ссылку' : 'Поделиться' }}
            </button>
            <button v-if="publicUrl"
              class="px-3  h-11 text-base font-medium rounded-r-lg border border-neutral-300 bg-white hover:bg-neutral-50 text-neutral-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
              @click="copyUrl">
              Скопировать ссылку
            </button>
          </div>

        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-neutral-500">Заметка не найдена.</p>
        <AppButton variant="primary" class="mt-4"  @click="handleBack">
          Вернуться к списку заметок
        </AppButton>

      </div>
    </div>
  </div>
</template>