<script setup>
import Button from 'primevue/button'
import InputOtp from 'primevue/inputotp';
import Card from 'primevue/card'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import logo from './assets/logo.svg'

const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL ?? 'http://geoprofs.test/api'

const otp = ref('')
const error = ref('')
const loading = ref(false)

onMounted(() => {
  // No email known means the user skipped the admin login step
  if (!sessionStorage.getItem('otpEmail')) {
    router.push('/adminlogin')
  }
})

async function verify() {
  error.value = ''

  if (otp.value.length !== 6) {
    error.value = 'Vul alle 6 cijfers in.'
    return
  }

  loading.value = true

  try {
    const response = await fetch(`${API_URL}/manager/verify-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({
        email: sessionStorage.getItem('otpEmail'),
        code: otp.value,
      }),
    })

    if (response.status === 429) {
      error.value = 'Te veel pogingen, probeer het later opnieuw.'
      return
    }

    if (response.status === 422) {
      error.value = 'Code onjuist of verlopen.'
      return
    }

    if (!response.ok) {
      error.value = `Er ging iets mis op de server (${response.status}).`
      return
    }

    const data = await response.json()
    localStorage.setItem('token', data.token)
    sessionStorage.removeItem('otpEmail')

    // Temporary test page, replace with the real manager page later
    router.push('/manager-test')
  } catch {
    error.value = 'De server is niet bereikbaar.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex justify-center items-center">
    <Card class="w-fit  p-4 bg-[#FAF8F5]  shadow-md">
      <template #title>
        <img :src="logo" alt="Logo" class="h-10 mx-auto" />
        <span class="block text-center mb-4 text-black">Verlofsysteem Portaal</span>
      </template>

      <template #content>
        <form class="flex flex-col gap-6" @submit.prevent="verify">
          <div class="flex flex-col gap-2 items-center">
            <span>Er is een code verstuurd naar uw e-mailadres</span>
            <InputOtp v-model="otp" :length="6" integerOnly />
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