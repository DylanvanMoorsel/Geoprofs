import { createRouter, createWebHistory } from 'vue-router'
import ManagerTest from '../ManagerTest.vue'
import Login from '@/login.vue'
import AdminLogin from '@/AdminLogin.vue'
import AdminOtp from '@/AdminOtp.vue'
import ManagerDashboard from '@/ManagerDashboard.vue'
import Beoordelen from '@/Beoordelen.vue'
import Afdelingsbezetting from '@/Afdelingsbezetting.vue'
import RequestLeave from '@/RequestLeave.vue'
import ProfilePage from '@/ProfilePage.vue'
import EmployeeDashboard from '@/EmployeeDashboard.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/adminlogin', component: AdminLogin },
    { path: '/adminotp', component: AdminOtp },
    { path: '/manager-test', component: ManagerTest },
    { path: '/managerDashboard', name: 'managerdashboard', component: ManagerDashboard },
    { path: '/beoordelen', name: 'beoordelen', component: Beoordelen },
    { path: '/afdelingsbezetting', name: 'afdelingsbezetting', component: Afdelingsbezetting },
    { path: '/requestleave', name: 'requestleave', component: RequestLeave },
    { path: '/profiel', name: 'profile', component: ProfilePage },
    { path: '/medewerkerDashboard', name: 'medewerkerDashboard', component: EmployeeDashboard },
  ],
})

export default router