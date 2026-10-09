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
const leaveTypes = ref([
    { label: 'Vakantie', value: 'vakantie' },
    { label: 'Ziekte', value: 'ziekte' },
    { label: 'Afspraak', value: 'afspraak' },
    { label: 'Overig', value: 'overig' }
]);
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold text-black"> Verlof Aanvragen</h1>
    <h2 class= "text-lg text-gray-600">Dien uw aanvraag in. Uw manager zal deze beoordelen.</h2>
    <hr class="border-gray-400"></hr>
    <div class="min-h-screen flex justify-center items-center px-4 py-8">
      <Card class="w-full max-w-2xl p-6 bg-[#FAF8F5] shadow-md">
        <template #title>
          <span class="mb-4 text-2xl text-black">Nieuwe Aanvraag</span>
        </template>

        <template #content>
          <div class="flex flex-col gap-6">
            <div class="flex gap-4">
              <div class="flex min-w-0 flex-1 flex-col gap-2">
                <label for="startDate" class="text-black">Startdatum (van)</label>
                <DatePicker id="startDate" v-model="startDate" placeholder="Startdatum(van)" class="bg-white border rounded-md border-gray-400" size="large" fluid />
              </div>
              <div class="flex min-w-0 flex-1 flex-col gap-2">
                <label for="endDate" class="text-black">Einddatum (tot)</label>
                <DatePicker id="endDate" v-model="endDate" placeholder="Einddatum(tot)" class="bg-white border rounded-md border-gray-400" size="large" fluid />
              </div>
            </div>
            <div class="flex flex-col gap-2">
              <label for="leaveType" class="text-black">Type Verlof</label>
              <Select v-model="selectedReason" :options="leaveTypes" optionLabel="label" optionValue="value" placeholder="Type verlof" class="w-full bg-white border border-gray-400" size="large" />
            </div>
            <div class="flex flex-col gap-2">
              <label for="reden" class="text-black">Reden (Optioneel)</label>
              <InputText id="reden" type="text" v-model="reden" placeholder="Reden" class="bg-white border border-gray-400" size="large" fluid />
            </div>
            <div class="flex justify-end gap-4">
              <Button label="Annuleren" class="bg-[#FFFFFF] text-black hover:bg-[#F24438] border border-gray-400" size="large" />
              <Button label="Verlof indienen" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" size="large" />
            </div>

      
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>