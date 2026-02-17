import { defineStore } from 'pinia'
import axios from '@/axios'
import { useUiStore } from './ui'

export interface Notification {
  id: number
  type_id: number
  created_at: string
  task: {
    id: number
    title: string
  }
  highlighted?: boolean
}

export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    notifications: [] as Notification[],
    unreadCount: 0,
  }),

  actions: {
    async fetchNotifications() {
      const ui = useUiStore()
      ui.isLoading = true
      ui.loadingMessage = 'Загрузка уведомлений...'

      try {
        const { data } = await axios.get('/notifications')

        this.notifications = data.map((n: any) => ({
          ...n,
          highlighted: n.is_read == 0 // ← ключевая строка
        }))

        setTimeout(() => {
          this.notifications.forEach((n) => (n.highlighted = false))
        }, 3000)
      } catch (error) {
        ui.showToast('Ошибка при загрузке уведомлений')
      } finally {
        ui.isLoading = false
        ui.loadingMessage = ''
      }
    }
    ,

    async fetchUnreadCount() {
      const { data } = await axios.get('/notifications/unread-count')
      this.unreadCount = data.unread
    },

    async markAllAsRead() {
      await axios.post('/notifications/mark-read')
      this.unreadCount = 0
    },
    async deleteNotification(id: number) {
      try {
        await axios.delete(`/notifications/${id}`)
        this.notifications = this.notifications.filter(n => n.id !== id)

      } catch (error) {
        const ui = useUiStore()
        ui.showToast('Не удалось удалить уведомление')
      }
    }

  }
})
