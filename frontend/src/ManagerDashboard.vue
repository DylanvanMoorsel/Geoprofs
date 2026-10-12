<script setup>
import Sidebar from './components/ManagerSidebar.vue'
import { ref, computed } from 'vue'
import Card from 'primevue/card'

const dashboard = ref({
    pendingRequests: 4,
    employeesPresent: 15,
    employeesTotal: 17,
    tomorrowCoverage: 80,
    minimumCoverage: 75
})

const presencePercentage = computed(() => {
    if (dashboard.value.employeesTotal === 0) return 0

    return Math.round(
        dashboard.value.employeesPresent /
        dashboard.value.employeesTotal * 100
    )
})

const priorityRequests = ref([
    {
        id: 1,
        employee: 'Pim van der Vlist',
        type: 'Vakantie',
        days: 5,
        status: 'pending'
    },
    {
        id: 2,
        employee: 'Lieke de Boer',
        type: 'Persoonlijk verlof',
        days: 1,
        status: 'pending'
    }
])

</script>

<template>
    <div class="flex">
        <Sidebar />
        <main class="flex-1 min-h-screen bg-[#FAF8F5] p-8 text-[#292524]">
            <h1>
                Manager Dashboard
            </h1>

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
                <Card class="border border-[#E8E3DF] shadow-none rounded-xl">
                    <template #title>
                        <span class="text-lg font-semibold text-[#292524]">
                            Prioriteit
                        </span>
                    </template>

                    <template #content>
                        <div v-for="request in priorityRequests" :key="request.id"
                            class="border-b border-gray-200 py-3 last:border-b-0">
                            <p class="font-semibold">
                                {{ request.employee }}
                            </p>

                            <p class="text-sm text-[#78716C]">
                                {{ request.type }} - {{ request.days }} dagen
                            </p>
                        </div>
                    </template>
                </Card>
            </div>
        </main>
    </div>

</template>