<script setup>
import { ref, computed, watch } from 'vue'
import Button from 'primevue/button'
import Sidebar from './components/ManagerSidebar.vue'

const requests = ref([
    {
        id: 1,
        employee: 'Pim van der Vlist',
        role: 'Landmeter Junior',
        type: 'Vakantie',
        period: '20 Jul - 24 Jul 2026',
        duration: '5 werkdagen',
        reason: 'Bezoek aan familie in het buitenland.',
        status: 'pending'
    },
    {
        id: 2,
        employee: 'Lieke de Boer',
        role: 'GIS Specialist',
        type: 'Persoonlijk',
        period: '23 Jul 2026',
        duration: '1 werkdag',
        reason: 'Verhuizing & overdracht nieuwe woning.',
        status: 'pending'
    },
    {
        id: 3,
        employee: 'Bram Verbruggen',
        role: 'Data Analyst',
        type: 'Compensatie',
        period: '28 Jul 2026',
        duration: '8 uur',
        reason: 'Opname wegens overuren vorig project.',
        status: 'pending'
    }
])

const selectedIds = ref([1, 2])

const selectedType = ref('all')

const pendingRequests = computed(() => {
    return requests.value.filter(
        request => request.status === 'pending'
    )
})

const filteredRequests = computed(() => {
    if (selectedType.value === 'all') {
        return pendingRequests.value
    }

    return pendingRequests.value.filter(
        request => request.type === selectedType.value
    )
})

const selectedCount = computed(() => {
    return pendingRequests.value.filter(
        request => selectedIds.value.includes(request.id)
    ).length
})

watch(selectedType, () => {
    selectedIds.value = []
})

function approveRequest(id) {
    const request = requests.value.find(
        request => request.id === id
    )

    if (!request || request.status !== 'pending') return

    request.status = 'approved'

    selectedIds.value = selectedIds.value.filter(
        selectedId => selectedId !== id
    )
}

function rejectRequest(id) {
    const request = requests.value.find(
        request => request.id === id
    )

    if (!request || request.status !== 'pending') return

    request.status = 'rejected'

    selectedIds.value = selectedIds.value.filter(
        selectedId => selectedId !== id
    )
}

function approveSelected() {
    const selected = new Set(selectedIds.value)

    requests.value.forEach(request => {
        if (
            selected.has(request.id) &&
            request.status === 'pending'
        ) {
            request.status = 'approved'
        }
    })

    selectedIds.value = []
}
</script>

<template>
    <div class="flex min-h-screen">
        <Sidebar />
        <main class="flex-1 min-w-0 min-h-screen bg-[#FAF8F5] p-8 text-[#292524]">
            <div class="border-b border-[#E8E3DF] pb-4">
                <h1 class="text-2xl font-bold text-[#292524]">
                    Aanvragen Beoordelen
                </h1>

                <p class="text-sm text-[#78716C] mt-1">
                    Beoordeel openstaande verlofaanvragen van uw
                    afdelingsmedewerkers individueel of in bulk.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 mt-6 mb-6">
                <div class="flex flex-wrap items-center gap-3">

                    <Button :label="`Geselecteerde goedkeuren (${selectedCount})`" size="small"
                        :disabled="selectedCount === 0"
                        class="!bg-[#F3F0ED] !border-[#F3F0ED] !text-[#17233C] !text-xs !font-semibold !rounded-lg !px-4 !py-2 disabled:!opacity-50"
                        @click="approveSelected" />

                    <div class="flex items-center gap-1">
                        <label for="leaveType" class="text-xs text-[#78716C]">
                            Filteren op type:
                        </label>

                        <select id="leaveType" v-model="selectedType"
                            class="bg-transparent text-xs text-[#78716C] cursor-pointer outline-none focus:ring-2 focus:ring-[#1976D2] rounded">
                            <option value="all">Alle verlof</option>
                            <option value="Vakantie">Vakantie</option>
                            <option value="Persoonlijk">
                                Persoonlijk
                            </option>
                            <option value="Compensatie">
                                Compensatie
                            </option>
                        </select>
                    </div>
                </div>

                <span class="text-xs text-[#78716C]">
                    {{ pendingRequests.length }}
                    openstaande verzoeken
                </span>
            </div>

            <div class="bg-white border border-[#E8E3DF] rounded-xl p-5 min-h-[245px]">

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[850px] table-fixed border-separate border-spacing-y-2 text-left">
                        <colgroup>
                            <col style="width: 4%" />
                            <col style="width: 17%" />
                            <col style="width: 16%" />
                            <col style="width: 11%" />
                            <col style="width: 28%" />
                            <col style="width: 24%" />
                        </colgroup>

                        <thead>
                            <tr class="text-[#78716C]">

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF]">
                                    Bulk
                                </th>

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF]">
                                    Medewerker
                                </th>

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF]">
                                    Type & Periode
                                </th>

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF]">
                                    Duur
                                </th>

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF]">
                                    Reden van Aanvraag
                                </th>

                                <th class="text-xs font-semibold px-2 pb-3 border-b border-[#E8E3DF] text-right">
                                    Acties
                                </th>

                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="request in filteredRequests" :key="request.id" class="transition-colors" :class="selectedIds.includes(request.id)
                                    ? 'bg-[#FAF8F6]'
                                    : 'bg-white hover:bg-[#FAF8F6]'
                                ">

                                <td class="px-2 py-3 rounded-l-lg">
                                    <input v-model="selectedIds" type="checkbox" :value="request.id"
                                        :aria-label="`Selecteer aanvraag van ${request.employee}`"
                                        class="w-4 h-4 accent-[#1976D2] cursor-pointer" />
                                </td>

                                <td class="px-2 py-3">
                                    <div class="flex flex-col gap-1">

                                        <span class="text-xs font-semibold text-[#292524]">
                                            {{ request.employee }}
                                        </span>

                                        <span class="text-[11px] text-[#78716C]">
                                            {{ request.role }}
                                        </span>

                                    </div>
                                </td>

                                <td class="px-2 py-3">
                                    <div class="flex flex-col gap-1">

                                        <span class="text-xs font-semibold" :class="request.type === 'Compensatie'
                                                ? 'text-[#57534E]'
                                                : 'text-[#1976D2]'
                                            ">
                                            {{ request.type }}
                                        </span>

                                        <span class="text-[11px] text-[#78716C]">
                                            {{ request.period }}
                                        </span>

                                    </div>
                                </td>

                                <td class="px-2 py-3">
                                    <span class="text-xs text-[#57534E]">
                                        {{ request.duration }}
                                    </span>
                                </td>

                                <td class="px-2 py-3">
                                    <span class="text-xs text-[#78716C]">
                                        {{ request.reason }}
                                    </span>
                                </td>

                                <td class="px-2 py-3 rounded-r-lg">
                                    <div class="flex items-center justify-end gap-2">

                                        <Button label="Afwijzen" size="small" outlined severity="danger"
                                            class="!text-[11px] !rounded-md !px-2 !py-1 !border-[#DC2626] !text-[#DC2626] !bg-transparent"
                                            @click="rejectRequest(request.id)" />

                                        <Button label="Goedkeuren" size="small"
                                            class="!text-[11px] !rounded-md !px-2 !py-1 !bg-[#1976D2] !border-[#1976D2] !text-white"
                                            @click="approveRequest(request.id)" />

                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>

                    <div v-if="filteredRequests.length === 0" class="text-center text-sm text-[#78716C] py-12">
                        Geen openstaande verlofaanvragen gevonden.
                    </div>

                </div>
            </div>

        </main>
    </div>
</template>
