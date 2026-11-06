
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Sidebar from './components/EmployeeSidebar.vue'
import Card from 'primevue/card'
import Button from 'primevue/button'

const router = useRouter()

const employee = ref({
    firstName: 'Willem',
    lastName: 'Bakker',
    role: 'Landmeter'
})

const leaveStats = ref([
    {
        id: 1,
        title: 'Vakantiedagen',
        value: 18,
        unit: 'dagen',
        description: 'Beschikbaar van de 25'
    },
    {
        id: 2,
        title: 'Ziektedagen (opgenomen)',
        value: 2,
        unit: 'dagen',
        description: 'Geregistreerd dit jaar'
    },
    {
        id: 3,
        title: 'Bijzonder Verlof',
        value: 1,
        unit: 'dag',
        description: 'Opgenomen overige uren'
    }
])

const recentRequests = ref([
    {
        id: 1,
        title: 'Vakantie Zomer',
        period: '15 Jul 2026 - 29 Jul 2026',
        duration: '10 werkdagen',
        status: 'Goedgekeurd'
    },
    {
        id: 2,
        title: 'Tandartsbezoek',
        period: '12 Apr 2026 (09:00)',
        duration: '2 uur',
        status: 'In behandeling'
    }
])

function requestLeave() {
    router.push('/requestleave')
}

function statusClass(status) {
    if (status === 'Goedgekeurd') {
        return 'bg-[#E8F5EA] text-[#388E3C]'
    }

    if (status === 'In behandeling') {
        return 'bg-[#FFF2E3] text-[#EA580C]'
    }

    if (status === 'Afgewezen') {
        return 'bg-[#FEECEC] text-[#DC2626]'
    }

    return 'bg-[#F1F1F1] text-[#57534E]'
}
</script>

<template>
    <div class="flex min-h-screen">

        <Sidebar />

        <main class="flex-1 min-w-0 min-h-screen bg-[#FAF8F5] p-8 text-[#292524]">

            <div class="border-b border-[#E8E3DF] pb-4">
                <h1 class="text-2xl font-bold text-[#292524]">
                    Welkom terug, {{ employee.firstName }}
                </h1>

                <p class="text-sm text-[#78716C] mt-1">
                    Hier is uw actuele verlofoverzicht voor dit kalenderjaar.
                </p>
            </div>

            <Card
                class="border border-[#E8E3DF] shadow-none rounded-xl mt-8"
                :pt="{
                    body: { class: '!p-5' }
                }"
            >
                <template #content>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        <div>
                            <h2 class="text-base font-semibold text-[#292524]">
                                Direct verlof opnemen?
                            </h2>

                            <p class="text-xs text-[#78716C] mt-2">
                                Dien snel een nieuwe verlofaanvraag in voor vakantie,
                                ziekte of bijzonder verlof.
                            </p>
                        </div>

                        <Button
                            label="Verlof Aanvragen"
                            size="small"
                            class="!bg-[#1976D2] !border-[#1976D2] !text-white !text-xs !font-semibold !rounded-md !px-4 !py-2.5 !shrink-0"
                            @click="requestLeave"
                        />

                    </div>
                </template>
            </Card>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                <Card
                    v-for="stat in leaveStats"
                    :key="stat.id"
                    class="border border-[#E8E3DF] shadow-none rounded-xl"
                >
                    <template #title>
                        <span class="text-sm text-[#57534E]">
                            {{ stat.title }}
                        </span>
                    </template>

                    <template #content>
                        <div class="flex items-baseline gap-2">

                            <span class="text-4xl font-bold text-[#292524]">
                                {{ stat.value }}
                            </span>

                            <span class="text-sm text-[#57534E]">
                                {{ stat.unit }}
                            </span>

                        </div>

                        <p class="text-xs text-[#78716C] mt-3">
                            {{ stat.description }}
                        </p>
                    </template>
                </Card>

            </div>

            <div class="mt-6">

                <Card
                    class="border border-[#E8E3DF] shadow-none rounded-xl"
                    :pt="{
                        body: { class: '!p-4' }
                    }"
                >
                    <template #title>
                        <span class="text-sm font-semibold text-[#292524]">
                            Recente Aanvragen
                        </span>
                    </template>

                    <template #content>
                        <div class="flex flex-col mt-3">

                            <div
                                v-for="request in recentRequests"
                                :key="request.id"
                                class="grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-4 sm:items-center py-4 border-b border-[#E8E3DF] last:border-b-0"
                            >

                                <div>
                                    <span class="text-xs font-medium text-[#292524]">
                                        {{ request.title }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-xs text-[#78716C]">
                                        {{ request.period }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-xs text-[#78716C]">
                                        {{ request.duration }}
                                    </span>
                                </div>

                                <div>
                                    <span
                                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                                        :class="statusClass(request.status)"
                                    >
                                        {{ request.status }}
                                    </span>
                                </div>

                            </div>
                            
                            <p
                                v-if="recentRequests.length === 0"
                                class="text-sm text-[#78716C] py-4"
                            >
                                Je hebt nog geen verlofaanvragen ingediend.
                            </p>

                        </div>
                    </template>
                </Card>

            </div>

        </main>
    </div>
</template>
