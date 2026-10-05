<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Card from 'primevue/card'
import { ref } from 'vue'

const username = ref('')
const password = ref('')
</script>

<template>
  <div class="min-h-screen flex justify-center items-center">
    <Card class="w-full max-w-sm p-4">
      <template #title>
        <span class="block text-center mb-4">Verlofsysteem Portaal</span>
      </template>

      <template #content>
        <div class="flex flex-col gap-6">
          <div class="flex flex-col gap-2">
            <label for="email">E-mailadres</label>
            <InputText id="email" type="text" v-model="username" placeholder="E-mailadres" fluid />
          </div>

          <div class="flex flex-col gap-2">
            <label for="password">Wachtwoord</label>
            <Password inputId="password" v-model="password" :feedback="false" toggleMask fluid />
          </div>

          <Button label="Inloggen" fluid />
        </div>
      </template>
    </Card>
  </div>
</template>