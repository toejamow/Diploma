<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFollowStore } from '@/stores/follow'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const followStore = useFollowStore()

const activeTab = ref<'following' | 'followers'>('following')

onMounted(() => {
  followStore.fetchFollowsAndFollowers()
})

const currentUsers = computed(() => {
  return activeTab.value === 'following' ? followStore.following : followStore.followers
})

const followingCount = computed(() => followStore.followingCount)
const followersCount = computed(() => followStore.followersCount)

const toggleFollow = async (userId: number) => {
  const list = activeTab.value === 'following' ? followStore.following : followStore.followers
  const user = list.find(u => u.id === userId)
  if (!user) return

  if (user.is_following) {
    await followStore.unfollow(userId)
  } else {
    await followStore.follow(userId)
  }
}

const viewProfile = (userId: number) => {
  router.push(`/user/${userId}`)
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-xl shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b border-neutral-200">
          <h1 class="text-2xl font-bold text-neutral-900">Ваши подписки и подписчики</h1>
        </div>

        <!-- Tabs -->
        <div class="border-b border-neutral-200">
          <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <button @click="activeTab = 'following'" :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'following'
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300'
            ]">
              Подписки
              <span class="ml-2 bg-neutral-100 text-neutral-600 py-0.5 px-2 rounded-full text-xs">
                {{ followingCount }}
              </span>
            </button>
            <button @click="activeTab = 'followers'" :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'followers'
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300'
            ]">
              Подписчики
              <span class="ml-2 bg-neutral-100 text-neutral-600 py-0.5 px-2 rounded-full text-xs">
                {{ followersCount }}
              </span>
            </button>
          </nav>
        </div>

        <!-- Users List -->
        <div class="p-6">
          <div class="space-y-4">
            <div v-for="user in currentUsers" :key="user.id"
              class="flex items-center justify-between p-4 bg-neutral-50 rounded-lg hover:bg-neutral-100 transition-colors">
              <div class="flex items-center space-x-4 flex-1 cursor-pointer" @click="viewProfile(user.id)">
                <span class="material-symbols-outlined scale-125 mb-2 text-primary-700">
                                    account_circle
                                </span>
                <div class="flex-1">
                  <h3 class="font-medium text-neutral-900 hover:text-primary-600 transition-colors">
                    {{ user.name }}
                  </h3>
                  <p class="text-sm text-neutral-500">{{ user.email }}</p>
                  <p class="text-xs text-neutral-500 mt-1">{{ user.notesCount }} заметок</p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <AppButton v-if="activeTab === 'following'" variant="outline" size="sm" @click="toggleFollow(user.id)">
                  Отписаться
                </AppButton>
                <AppButton v-else :variant="user.is_following ? 'outline' : 'primary'" size="sm"
                  @click="toggleFollow(user.id)">
                  {{ user.is_following ? 'Отписаться' : 'Подписаться' }}
                </AppButton>

              </div>
            </div>

            <!-- Empty State -->
            <div v-if="currentUsers.length === 0" class="text-center py-12">
              <div class="text-neutral-400 mb-4">
                <span class="material-symbols-outlined text-4xl">group</span>
              </div>
              <h3 class="text-lg font-medium text-neutral-900 mb-2">
                {{ activeTab === 'following' ? 'У Вас нет активных подписок' : 'На Вас еще не успели подписаться' }}
              </h3>
              <p class="text-neutral-600">
                {{ activeTab === 'following'
                  ? 'Подпишитесь на других пользователей, чтобы следить за их прогрессом.'
                  : 'Делитесь своими заметками и профилем, чтобы заинтересовать других пользователей.'
                }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>