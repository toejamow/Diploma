import { defineStore } from 'pinia'
import axios from '@/axios'
import { useUiStore } from './ui'

export const useFollowStore = defineStore('follow', {
  state: () => ({
    followers: [],
    following: [],
    followersCount: 0,
    followingCount: 0,
    isLoading: false,
    actionLoadingId: null as number | null // ID юзера
  }),

  actions: {
    async fetchFollowsAndFollowers() {
      const ui = useUiStore()
      ui.isLoading = true
      ui.loadingMessage = 'Загрузка подписок...'
      try {
        const res = await axios.get(`/follows`)
        this.followers = res.data.followers
        this.following = res.data.following
        this.followersCount = res.data.followers_count
        this.followingCount = res.data.following_count
      } catch (e) {
        console.error('Ошибка загрузки подписок', e)
      } finally {
        ui.isLoading = false
        ui.loadingMessage = ''
      }
    },

    async follow(userId: number) {
      const ui = useUiStore()
      ui.isLoading = true
      ui.loadingMessage = 'Загрузка...'
      try {
        await axios.post(`/follow/${userId}`)
        await this.fetchFollowsAndFollowers()
      } catch (e) {
        console.error('Ошибка при подписке', e)
      } finally {
        ui.isLoading = false
        ui.loadingMessage = ''
      }
    },

    async unfollow(userId: number) {
      const ui = useUiStore()
      ui.isLoading = true
      ui.loadingMessage = 'Загрузка...'
      try {
        await axios.post(`/unfollow/${userId}`)
        await this.fetchFollowsAndFollowers()
      } catch (e) {
        console.error('Ошибка при отписке', e)
      } finally {
        ui.isLoading = false
        ui.loadingMessage = ''
      }
    },
    async fetchUserProfile(userId: number, params = {}, showLoading = true) {
  const ui = useUiStore()

  if (showLoading) {
    ui.isLoading = true
    ui.loadingMessage = 'Загрузка профиля...'
  }

  try {
    const res = await axios.get(`/user/${userId}`, {
      params: params
    })
    return res.data
  } catch (e) {
    console.error('Ошибка при загрузке профиля', e)
    return null
  } finally {
    if (showLoading) {
      ui.isLoading = false
      ui.loadingMessage = ''
    }
  }
}
}
})
