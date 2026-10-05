import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/login.vue'
import AdminLogin from '@/AdminLogin.vue'
import AdminOtp from '@/AdminOtp.vue'
import RequestLeave from '@/RequestLeave.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: Login },
    { path: '/adminlogin', name: 'adminlogin', component: AdminLogin },
    { path: '/adminotp', name: 'adminotp', component: AdminOtp },
    { path: '/requestleave', name: 'requestleave', component: RequestLeave },
    { path: '/', redirect: '/login' },
  ],
})

export default router