<script setup>
import Sidebar from './components/ManagerSidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Card from 'primevue/card'
import Button from 'primevue/button'

const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL ?? 'http://geoprofs.test/api'

const dashboard = ref({
    pendingRequests: 0,
    employeesPresent: 0,
    employeesTotal: 0,
    tomorrowCoverage: 0,
    minimumCoverage: 75
})
const priorityRequests = ref([])
const absentEmployees = ref([])
const loading = ref(true)
const error = ref('')

const TYPE_LABELS = {
    vakantie: 'Vakantie',
    ziekte: 'Ziek gemeld',
    afspraak: 'Afspraak',
    overig: 'Bijzonder verlof'
}
const TYPE_COLORS = {
    vakantie: '#EA580C',
    ziekte: '#DC2626',
    afspraak: '#1976D2',
    overig: '#1976D2'
}

function authHeaders() {
    return {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        Accept: 'application/json',
    }
}

function shortDate(iso) {
    return new Date(iso).toLocaleDateString('nl-NL', { day: 'numeric', month: 'short' })
}

function applyData(data) {
    dashboard.value = {
        pendingRequests: data.pending_requests,
        employeesPresent: data.today?.present ?? 0,
        employeesTotal: data.today?.total ?? 0,
        tomorrowCoverage: data.tomorrow?.percentage ?? 0,
        minimumCoverage: data.minimum_coverage
    }

    priorityRequests.value = data.priority_requests.map(request => ({
        id: request.id,
        employee: request.employee.name,
        type: TYPE_LABELS[request.type] ?? request.type,
        days: request.workdays,
        status: 'pending'
    }))

    absentEmployees.value = data.absent_today.map(absent => ({
        id: absent.id,
        employee: absent.name,
        reason: TYPE_LABELS[absent.type] ?? absent.type,
        period: absent.start_date === absent.end_date
            ? 'Vandaag'
            : `t/m ${shortDate(absent.end_date)}`,
        color: TYPE_COLORS[absent.type] ?? '#78716C'
    }))
}

async function loadDashboard() {
    if (!localStorage.getItem('token')) {
        error.value = 'Je bent niet ingelogd. Log eerst in als manager.'
        loading.value = false
        return
    }

    try {
        const response = await fetch(`${API_URL}/manager/dashboard`, { headers: authHeaders() })

        if (response.status === 401) {
            localStorage.removeItem('token')
            error.value = 'Je sessie is verlopen. Log opnieuw in als manager.'
            return
        }

        if (response.status === 403) {
            error.value = 'Dit account is geen manager.'
            return
        }

        if (!response.ok) {
            error.value = `Er ging iets mis op de server (${response.status}).`
            return
        }

        applyData(await response.json())
        error.value = ''
    } catch {
        error.value = 'De server is niet bereikbaar.'
    } finally {
        loading.value = false
    }
}

onMounted(loadDashboard)

const presencePercentage = computed(() => {
    if (dashboard.value.employeesTotal === 0) return 0

    return Math.round(
        dashboard.value.employeesPresent /
        dashboard.value.employeesTotal * 100
    )
})

const pendingPriorityRequests = computed(() => {
    return priorityRequests.value.filter(
        request => request.status === 'pending'
    )
})

async function approveRequest(id) {
    const request = priorityRequests.value.find(request => request.id === id)

    if (!request || request.status !== 'pending') return

    try {
        const response = await fetch(`${API_URL}/manager/absences/${id}/approve`, {
            method: 'POST',
            headers: authHeaders(),
        })

        if (!response.ok) {
            const body = await response.json().catch(() => ({}))
            error.value = body.errors?.absence?.[0] ?? body.message ?? 'Goedkeuren is mislukt.'
            return
        }

        error.value = ''
        await loadDashboard()
    } catch {
        error.value = 'De server is niet bereikbaar.'
    }
}
</script>

<template>
    <div class="flex">
        <Sidebar />
        <main class="flex-1 min-h-screen bg-[#FAF8F5] p-8 text-[#292524]">
            <div class="border-b border-[#E8E3DF] pb-4">
                <h1 class="text-2xl font-bold text-[#292524]">
                    Manager Dashboard
                </h1>

                <p class="text-sm text-[#78716C] mt-1">
                    Real-time overzicht van afdelingsbezetting, openstaande aanvragen en teamstatus.
                </p>
            </div>

            <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <Card class="border border-[#E8E3DF] shadow-none rounded-xl">
                    <template #title>
                        <span class="text-sm text-[#57534E]">
                            Openstaande Aanvragen
                        </span>
                    </template>

                    <template #content>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold">
                                {{ dashboard.pendingRequests }}
                            </span>

                            <span class="text-sm">
                                in afwachting
                            </span>
                        </div>

                        <p class="text-xs text-[#78716C] mt-3">
                            Vereist onmiddellijke actie
                        </p>
                    </template>
                </Card>

                <Card class="border border-[#E8E3DF] shadow-none rounded-xl">
                    <template #title>
                        <span class="text-sm text-[#57534E]">
                            Team Aanwezigheid (Vandaag)
                        </span>
                    </template>

                    <template #content>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold">
                                {{ presencePercentage }}%
                            </span>

                            <span class="text-sm">
                                bezetting
                            </span>
                        </div>

                        <p class="text-xs text-[#78716C] mt-3">
                            {{ dashboard.employeesPresent }} van de
                            {{ dashboard.employeesTotal }} werknemers aanwezig
                        </p>
                    </template>
                </Card>

                <Card class="border border-[#E8E3DF] shadow-none rounded-xl">
                    <template #title>
                        <span class="text-sm text-[#57534E]">
                            Kritieke bezetting (Morgen)
                        </span>
                    </template>

                    <template #content>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold" :class="dashboard.tomorrowCoverage >= dashboard.minimumCoverage
                                ? 'text-green-700'
                                : 'text-red-600'">
                                {{ dashboard.tomorrowCoverage }}%
                            </span>
                        </div>

                        <p class="text-xs text-[#78716C] mt-3">
                            {{
                                dashboard.tomorrowCoverage >= dashboard.minimumCoverage
                                    ? 'Voldoet aan de minimumgrens van'
                                    : 'Onder de minimumgrens van'
                            }}
                            {{ dashboard.minimumCoverage }}%
                        </p>
                    </template>
                </Card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                <Card class="border border-[#E8E3DF] shadow-none rounded-xl" :pt="{ body: { class: '!p-4' } }">
                    <template #title>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[#292524]">
                                Prioriteit
                            </span>

                            <RouterLink to="/beoordelen" class="text-xs font-medium text-[#1976D2]">
                                Bekijk alles ({{ dashboard.pendingRequests }})
                            </RouterLink>
                        </div>
                    </template>

                    <template #content>
                        <div class="flex flex-col gap-2 mt-3">
                            <div v-for="request in pendingPriorityRequests" :key="request.id"
                                class="flex items-center justify-between gap-3 bg-[#F8F6F4] border border-[#E8E3DF] rounded-lg p-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#292524]">
                                        {{ request.employee }}
                                    </p>

                                    <p class="text-xs text-[#78716C]">
                                        {{ request.type }} ·
                                        {{ request.days }}
                                        {{ request.days === 1 ? 'dag' : 'dagen' }}
                                    </p>
                                </div>

                                <Button label="Keur goed" size="small"
                                    class="!text-xs !bg-[#1976D2] !border-[#1976D2] !shrink-0"
                                    @click="approveRequest(request.id)" />
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="border border-[#E8E3DF] shadow-none rounded-xl" :pt="{ body: { class: '!p-4' } }">
                    <template #title>
                        <span class="text-sm font-semibold text-[#292524]">
                            Afwezig Vandaag
                        </span>
                    </template>

                    <template #content>
                        <div class="flex flex-col gap-3 mt-3">
                            <div v-for="employee in absentEmployees" :key="employee.id" class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full shrink-0"
                                    :style="{ backgroundColor: employee.color }"></span>

                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-[#292524]">
                                        {{ employee.employee }}
                                    </span>

                                    <span class="text-xs text-[#78716C]">
                                        {{ employee.reason }}
                                        ({{ employee.period }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </main>
    </div>

</template>