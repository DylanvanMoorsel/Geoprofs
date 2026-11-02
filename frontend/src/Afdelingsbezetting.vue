<script setup>
import Sidebar from './components/ManagerSidebar.vue'
</script>

<template>
    <div class="flex">
        <Sidebar />
        <main class="flex-1 min-h-screen bg-[#FAF8F5] p-8 text-[#292524]">
            <div class="border-b border-[#E8E3DF] pb-4">
                <h1 class="text-2xl font-bold text-[#292524]">
                    Afdelingsbezetting </h1>

                <p class="text-sm text-[#78716C] mt-1">
                    Bezetting deze week. Zorg ervoor dat het personeelsbestand boven de kritieke limiet (75%) blijft.
                </p>
            </div>
        </main>
    </div>

</template>