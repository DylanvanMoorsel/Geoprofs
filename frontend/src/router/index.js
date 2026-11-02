import { createRouter, createWebHistory } from 'vue-router'
import Login from '../login.vue'
import AdminLogin from '../AdminLogin.vue'
import AdminOtp from '../AdminOtp.vue'
import ManagerTest from '../ManagerTest.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/adminlogin', component: AdminLogin },
    { path: '/adminotp', component: AdminOtp },
    { path: '/manager-test', component: ManagerTest },
  ],
})

export default router