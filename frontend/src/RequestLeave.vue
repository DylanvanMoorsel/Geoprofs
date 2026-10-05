<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import DatePicker from 'primevue/datepicker';
import { ref } from 'vue'
import logo from './assets/logo.svg'
import { useRouter } from 'vue-router'
import Select from 'primevue/select';
const router = useRouter()

const startDate = ref('')
const endDate = ref('')
const selectedReason = ref('')
const languages = ref([
    { label: 'Vakantie', value: '' },
    { label: 'Ziekte', value: '' },
    { label: 'Afspraak', value: '' },
    { label: 'Overig', value: '' }
]);
</script>

<template>
  <div class="min-h-screen flex justify-center items-center">
    <Card class="w-full max-w-sm p-4 bg-[#FAF8F5]  shadow-md">
      <template #title>
        <span class="block text-center mb-4 text-black">Nieuwe Aanvraag</span>
      </template>

      <template #content>
        <div class="flex flex-col gap-6">
          <div class="flex flex-col gap-2">
            <label for="email" class="text-black">Startdatum(van)</label>
            <DatePicker id="startDate" v-model="startDate" placeholder="Startdatum(van)" class="bg-white border border-gray-400" fluid />
          </div>
          <div class="flex flex-col gap-2">
            <label for="email" class="text-black">Einddatum(tot)</label>
            <DatePicker id="endDate" v-model="endDate" placeholder="Einddatum(tot)" class="bg-white border border-gray-400" fluid />
          </div>
          <div class="flex flex-col gap-2">
            <label for="email" class="text-black">Type Verlof</label>
            <Select v-model="selectedReason" :options="languages" optionLabel="label" optionValue="value" placeholder="Type verlof" class="w-full md:w-56" />
          </div>
    
        </div>
      </template>
    </Card>
  </div>
</template>