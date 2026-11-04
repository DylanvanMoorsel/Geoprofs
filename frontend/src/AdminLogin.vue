<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Card from 'primevue/card'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import logo from './assets/logo.svg'

const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL ?? 'http://geoprofs.test/api'

const username = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function login() {
  error.value = ''
  loading.value = true

  try {
    const response = await fetch(`${API_URL}/manager/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ email: username.value, password: password.value }),
    })

    if (response.status === 429) {
      error.value = 'Te veel pogingen, probeer het later opnieuw.'
      return
    }

    if (response.status === 422) {
      error.value = 'E-mailadres of wachtwoord onjuist.'
      return
    }

    if (!response.ok) {
      error.value = `Er ging iets mis op de server (${response.status}).`
      return
    }

    sessionStorage.setItem('otpEmail', username.value)
    router.push('/adminotp')
  } catch {
    error.value = 'De server is niet bereikbaar.'
  } finally {
    loading.value = false
  }
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
        <form class="flex flex-col gap-6" @submit.prevent="login">
          <div class="flex flex-col gap-2">
            <label for="email" class="text-black">E-mailadres</label>
            <InputText id="email" type="text" v-model="username" placeholder="E-mailadres" class="bg-white border border-gray-400" fluid />
          </div>

          <div class="flex flex-col gap-2">
            <label for="password" class="text-black">Wachtwoord</label>
            <Password inputId="password" v-model="password" :feedback="false" toggleMask inputClass="bg-white border border-gray-400" fluid />
          </div>

          <p v-if="error" class="text-center text-sm text-red-600">{{ error }}</p>

          <Button type="submit" label="Inloggen" :loading="loading" class="bg-[#1A6CC4] text-white hover:bg-[#0056b3]" fluid />
          <div class="text-center text-black">
            <a href="#" class="text-[#1A6CC4] hover:underline">Wachtwoord vergeten?</a>
          </div>
          <div class="text-center text-black">
            <a href="/login" class="text-[#1A6CC4] hover:underline">Login als medewerker</a>
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>