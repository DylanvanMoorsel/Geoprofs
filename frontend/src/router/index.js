import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/login.vue'
import AdminLogin from '@/AdminLogin.vue'
import AdminOtp from '@/AdminOtp.vue'
import ManagerDashboard from '@/ManagerDashboard.vue'
import Beoordelen from '@/Beoordelen.vue'
import Afdelingsbezetting from '@/Afdelingsbezetting.vue'
import ProfielInstellingen from '@/ProfielInstellingen.vue'
import RequestLeave from '@/RequestLeave.vue'
import ProfilePage from '@/ProfilePage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: Login },
    { path: '/adminlogin', name: 'adminlogin', component: AdminLogin },
    { path: '/adminotp', name: 'adminotp', component: AdminOtp },
    { path: '/', redirect: '/login' },
    { path: '/managerDashboard', name: 'managerdashboard', component: ManagerDashboard },
    { path: '/beoordelen', name: 'beoordelen', component: Beoordelen },
    { path: '/afdelingsbezetting', name: 'afdelingsbezetting', component: Afdelingsbezetting },
    { path: '/profielInstellingen', name: 'profielInstellingen', component: ProfielInstellingen },
    { path: '/requestleave', name: 'requestleave', component: RequestLeave },
    { path: '/profiel', name: 'profile', component: ProfilePage },

    { path: '/', redirect: '/login' },
  ],
})

export default router