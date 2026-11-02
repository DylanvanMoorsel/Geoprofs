<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import { ref } from 'vue'
import logo from './assets/logo.svg'
import { useRouter } from 'vue-router'
import Select from 'primevue/select';
import Password from 'primevue/password'
</script>

<template>
  <div>
    <div class="px-10 py-6">
      <h1 class="text-3xl font-bold text-black">Profiel & Instellingen</h1>
      <h2 class= "text-lg text-gray-600">Beheer uw persoonlijke gegevens, notificatievoorkeuren en beveiligingsopties.</h2>
      <hr class="border-gray-400"></hr>
    </div>
    <div class="min-h-screen flex justify-center items-center px-4 py-8">
      <Card class="w-full max-w-2xl p-6 bg-[#FAF8F5] shadow-md">
        <template #title>
          <span class="mb-4 text-2xl text-black">Wachtwoord wijzigen</span>
        </template>

        <template #content>
          <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label for="password" class="text-black">Huidig Wachtwoord</label>
                <Password inputId="password" v-model="password" :feedback="false" toggleMask inputClass="bg-white border border-gray-400" fluid />
            </div>
             <div class="flex flex-col gap-2">
                <label for="password" class="text-black">Nieuw Wachtwoord</label>
                <Password inputId="password" v-model="password" :feedback="false" toggleMask inputClass="bg-white border border-gray-400" fluid />
            </div>
             <div class="flex flex-col gap-2">
                <label for="password" class="text-black">Bevestig Nieuw Wachtwoord</label>
                <Password inputId="password" v-model="password" :feedback="false" toggleMask inputClass="bg-white border border-gray-400" fluid />
            </div>
            <Button label="Opslaan" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" fluid />
      
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>