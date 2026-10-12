<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Card from 'primevue/card'
import { ref } from 'vue'
import logo from './assets/logo.svg'
import { useRouter } from 'vue-router'
const router = useRouter()

const username = ref('')
const password = ref('')

function login(){
    router.push({ name: 'adminotp' })
}
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
            <span class="block text-center mb-4 text-black">Na het inloggen wordt er een code verstuurd naar u mail</span>
            <label for="email" class="text-black">E-mailadres</label>
            <InputText id="email" type="text" v-model="username" placeholder="E-mailadres" class="bg-white border border-gray-400" fluid />
          </div>
            <Button label="Inloggen" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" fluid @click="login" />
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