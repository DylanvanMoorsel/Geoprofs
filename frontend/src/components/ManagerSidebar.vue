<script setup>
import Button from 'primevue/button'
import { useRoute, useRouter } from 'vue-router'
import Avatar from 'primevue/avatar'

import logo from '../assets/logo.svg'
import icon1 from '../assets/Manager-dashboard.svg'
import icon1Active from '../assets/Manager-dashboard-Active.svg'
import icon2 from '../assets/Beoordelen.svg'
import icon2Active from '../assets/Beoordelen-Active.svg'
import icon3 from '../assets/Afdelingsbezetting.svg'
import icon3Active from '../assets/Afdelingsbezetting-Active.svg'
import icon4 from '../assets/Profiel-Instellingen.svg'
import icon4Active from '../assets/Profiel-Instellingen-Active.svg'
import logoutIcon from '../assets/Logout.svg'

const route = useRoute()
const router = useRouter()

const navItems = [
    { label: 'Manager Dashboard', icon: icon1, activeIcon: icon1Active, path: '/managerDashboard' },
    { label: 'Beoordelen', icon: icon2, activeIcon: icon2Active, path: '/beoordelen' },
    { label: 'Afdelingsbezetting', icon: icon3, activeIcon: icon3Active, path: '/afdelingsbezetting' },
    { label: 'Profiel & Instellingen', icon: icon4, activeIcon: icon4Active, path: '/profielInstellingen' },
]

function logout() {
    router.push('/adminlogin')
}
</script>

<template>
    <aside class="w-64 min-h-screen bg-white p-6 text-[#292524] text-sm flex flex-col">
        <img :src="logo" alt="Logo" class="h-10 w-36" />
        <nav class="mt-6 flex flex-col gap-3">
            <RouterLink class="group flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-[#F3F0ED] cursor-pointer"
                :class="{ 'bg-[#F3F0ED]': route.path === item.path }" :key="item.label" :to="item.path"
                v-for="item in navItems">
                <img v-if="route.path === item.path" :src="item.activeIcon" class="w-5 h-5 shrink-0" alt="" />
                <template v-else>
                    <img :src="item.icon" class="w-5 h-5 shrink-0 group-hover:hidden" alt="" />
                    <img :src="item.activeIcon" class="w-5 h-5 shrink-0 hidden group-hover:block" alt="" />
                </template>
                <span class="group-hover:font-semibold group-hover:text-[#17233C]" :class="route.path === item.path
                    ? 'font-semibold text-[#17233C]'
                    : 'font-normal text-[#57534E]'">{{ item.label }}</span>
            </RouterLink>
        </nav>
        <div class="mt-auto border-t border-gray-200 pt-4">
            <div class="flex items-center gap-3">
                <Avatar label="JS" shape="circle" class="bg-[#F3EEEB] text-[#292524] shrink-0" />

                <div class="flex flex-1 min-w-0 flex-col">
                    <span class="font-semibold text-[#292524] text-xs whitespace-nowrap">
                        Jan de Sloper
                    </span>

                    <span class="text-xs text-[#78716C]">
                        Afdelingshoofd
                    </span>
                </div>
                <Button severity="danger" text rounded aria-label="Uitloggen" class="ml-auto shrink-0" @click="logout">
                    <img :src="logoutIcon" alt="" class="w-5 h-5" />
                </Button>
            </div>
        </div>
    </aside>
</template>
