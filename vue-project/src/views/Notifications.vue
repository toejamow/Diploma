<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNotificationsStore } from '../stores/notifications'
import AppButton from '../components/ui/AppButton.vue'
import { format } from 'date-fns'
import { ru } from 'date-fns/locale'

const router = useRouter()
const notificationsStore = useNotificationsStore()

const formatDate = (date: string) => {
  return format(new Date(date), 'd MMMM yyyy в HH:mm', { locale: ru })
}



onMounted(async () => {
  await notificationsStore.fetchNotifications()
  await notificationsStore.markAllAsRead()
})
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <div class="max-w-3xl mx-auto px-4 py-8">
      <div class="bg-white rounded-xl shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b border-neutral-200 flex justify-between items-center">
          <h1 class="text-2xl font-bold text-neutral-900">Уведомления</h1>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-neutral-200">
          <div v-for="notification in notificationsStore.notifications" :key="notification.id" :class="[
            'p-4 hover:bg-neutral-50 transition-colors',
            { 'bg-primary-50': notification.highlighted }
          ]">

            <div class="flex gap-4">
              <!-- Картинка по типу уведомления -->
              <img :src="`/images/notifications/${notification.type_id}.png`" alt="Уведомление"
                class="w-40 h-40 object-cover rounded-lg border" />

              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <div v-if="notification.type_id == 3">
                    <h3 class="font-medium text-neutral-900">
                      Вы успели завершить заметку!
                    </h3>
                    <p class="text-neutral-600 mt-1">
                      Все задачи заметки "<RouterLink class="text-blue-600 hover:underline" @click.stop
                        :to="`/notes/${notification.task.id}`">{{ notification.task.title }}</RouterLink>" были
                      завершены. Вы великолепны!
                    </p>
                  </div>
                  <div v-if="notification.type_id == 2">
                    <h3 class="font-medium text-neutral-900">
                      Подходит срок сдачи
                    </h3>
                    <p class="text-neutral-600 mt-1">
                      У заметки "<RouterLink class="text-blue-600 hover:underline" @click.stop
                        :to="`/notes/${notification.task.id}`">{{ notification.task.title }}</RouterLink>" подошел срок
                      сдачи. Успейте выполнить поставленные задачи!
                    </p>
                  </div>
                  <div v-if="notification.type_id == 1">
                    <h3 class="font-medium text-neutral-900">
                      Заметка просрочена
                    </h3>
                    <p class="text-neutral-600 mt-1">
                      Заметка "<RouterLink class="text-blue-600 hover:underline" @click.stop
                        :to="`/notes/${notification.task.id}`">{{ notification.task.title }}</RouterLink>" была
                      просрочена. Может, стоит сдвинуть сроки?
                    </p>
                  </div>

                  <button @click.stop="notificationsStore.deleteNotification?.(notification.id)"
                    class="text-neutral-400 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </div>

                <div class="mt-2 flex items-center gap-2 text-center">
                  <span :class="[
                    'px-2 py-0.5 text-xs font-medium rounded-full',
                    notification.type_id === 1
                      ? 'bg-red-100 text-red-800'
                      : notification.type_id === 2
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-green-100 text-green-800'
                  ]">
                    {{
                      notification.type_id === 1
                        ? 'Просрочено'
                        : notification.type_id === 2
                          ? 'Скоро срок'
                          : 'Успешно завершено'
                    }}
                  </span>
                  <span class="text-xs text-neutral-500">
                    {{ formatDate(notification.created_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="notificationsStore.notifications.length === 0" class="p-8 text-center text-neutral-500">
            Уведомлений нет
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
