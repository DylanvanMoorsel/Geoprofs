<script setup>
import Button from 'primevue/button'
import InputOtp from 'primevue/inputotp';
import Card from 'primevue/card'
import { ref } from 'vue'
import logo from './assets/logo.svg'

const otp = ref('')
</script>

<template>
  <div class="min-h-screen flex justify-center items-center">
    <Card class="w-fit  p-4 bg-[#FAF8F5]  shadow-md">
      <template #title>
        <img :src="logo" alt="Logo" class="h-10 mx-auto" />
        <span class="block text-center mb-4 text-black">Verlofsysteem Portaal</span>
      </template>

      <template #content>
        <div class="flex flex-col gap-6">
          <div class="flex flex-col gap-2 items-center">
            <span>Er is een code verstuurd naar uw e-mailadres</span>
            <InputOtp v-model="otp" :length="4" integerOnly />
          </div>
          <Button label="Inloggen" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" fluid />
          <div class="text-center text-black">
            <a href="#" class="text-[#1A6CC4] hover:underline">Wachtwoord vergeten?</a>
          </div>
          <div class="text-center text-black">
            <a href="/login" class="text-[#1A6CC4] hover:underline">Login als medewerker</a>
          </div>
        </div>
      </template>
    </Card>
  </div>
</template>