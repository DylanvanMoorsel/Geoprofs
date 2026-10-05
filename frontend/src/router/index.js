import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/login.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: Login },
    { path: '/', redirect: '/login' },
  ],
})

export default router