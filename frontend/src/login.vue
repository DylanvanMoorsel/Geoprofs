<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Card from 'primevue/card'
import { ref } from 'vue'
import logo from './assets/logo.svg'

const username = ref('')
const password = ref('')
</script>

<template>
  <div class="min-h-screen flex justify-center items-center">
    <Card class="w-full max-w-sm p-4 bg-[#FAF8F5]  shadow-md">
      <template #title>
        <img :src="logo" alt="Logo" class="h-10 mx-auto" />
        <span class="block text-center mb-4 text-black">Verlofsysteem Portaal</span>
      </template>

      <template #content>
        <div class="flex flex-col gap-6">
          <div class="flex flex-col gap-2">
            <label for="email" class="text-black">E-mailadres</label>
            <InputText id="email" type="text" v-model="username" placeholder="E-mailadres" class="bg-white border border-gray-400" fluid />
          </div>

          <div class="flex flex-col gap-2">
            <label for="password" class="text-black">Wachtwoord</label>
            <Password inputId="password" v-model="password" :feedback="false" toggleMask inputClass="bg-white border border-gray-400" fluid />
          </div>

          <Button label="Inloggen" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" fluid />
        </div>
      </template>
    </Card>
  </div>
</template>