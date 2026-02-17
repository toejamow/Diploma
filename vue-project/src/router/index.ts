import FollowList from '@/views/FollowList.vue'
import SharedNote from '@/views/notes/SharedNote.vue'
import Notifications from '@/views/Notifications.vue'
import Statistics from '@/views/Statistics.vue'
import UserProfile from '@/views/UserProfile.vue'
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

const SignIn = () => import('../views/auth/SignIn.vue')
const SignUp = () => import('../views/auth/SignUp.vue')
const NotesList = () => import('../views/notes/NotesList.vue')
const NoteEditor = () => import('../views/notes/NoteEditor.vue')
const NoteView = () => import('../views/notes/NoteView.vue')

const routes: RouteRecordRaw[] = [  {
    path: '/',
    redirect: '/notes'
  },
  {
    path: '/signin',
    name: 'SignIn',
    component: SignIn,
    meta: { requiresGuest: true }
  },
  {
    path: '/signup',
    name: 'SignUp',
    component: SignUp,
    meta: { requiresGuest: true }
  },
  {
    path: '/notes',
    name: 'Notes',
    component: NotesList,
    meta: { requiresAuth: true }
  },
  {
    path: '/notes/new',
    name: 'NewNote',
    component: NoteEditor,
    meta: { requiresAuth: true }
  },
  {
    path: '/notes/:id',
    name: 'ViewNote',
    component: NoteView,
    meta: { requiresAuth: true }
  },
  {
    path: '/notes/:id/edit',
    name: 'EditNote',
    component: NoteEditor,
    meta: { requiresAuth: true }
  },
  {
    path: '/notifications',
    name: 'Notifications',
    component: Notifications,
    meta: { requiresAuth: true }
  },
  {
    path: '/follow_list',
    name: 'FollowList',
    component: FollowList,
    meta: { requiresAuth: true }
  },
  {
    path: '/user/:id',
    name: 'UserProfile',
    component: UserProfile,
    meta: { requiresAuth: false }
  },
  {
    path: '/statistics',
    name: 'Statistics',
    component: Statistics,
    meta: { requiresAuth: true }
  },
  {
    path: '/shared/:token',
    name: 'SharedNote',
    component: SharedNote,
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  
  if (to.meta.requiresAuth && !localStorage.getItem('token')) {
    next({ name: 'SignIn' })
  } 
  else if (to.meta.requiresGuest && localStorage.getItem('token')) {
    next({ name: 'Notes' })
  }
  else {
    next()
  }
})

export default router