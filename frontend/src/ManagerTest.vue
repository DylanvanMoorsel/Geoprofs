<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL ?? 'http://geoprofs.test/api'

const loading = ref(true)
const error = ref('')
const user = ref(null)
const team = ref([])
const teamNote = ref('')

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
    Accept: 'application/json',
  }
}

onMounted(async () => {
  if (!localStorage.getItem('token')) {
    error.value = 'Geen token gevonden. Je bent niet ingelogd.'
    loading.value = false
    return
  }

  try {
    const userResponse = await fetch(`${API_URL}/user`, { headers: authHeaders() })

    if (userResponse.status === 401) {
      localStorage.removeItem('token')
      error.value = 'Token is ongeldig of verlopen.'
      return
    }

    if (!userResponse.ok) {
      error.value = `Er ging iets mis op de server (${userResponse.status}).`
      return
    }

    user.value = await userResponse.json()

    const teamResponse = await fetch(`${API_URL}/team`, { headers: authHeaders() })

    if (teamResponse.status === 403) {
      teamNote.value = 'Dit account is geen manager, dus er is geen team om te tonen.'
    } else if (teamResponse.ok) {
      team.value = await teamResponse.json()
    } else {
      teamNote.value = `Team ophalen mislukt (${teamResponse.status}).`
    }
  } catch {
    error.value = 'De server is niet bereikbaar.'
  } finally {
    loading.value = false
  }
})

async function logout() {
  try {
    await fetch(`${API_URL}/logout`, { method: 'POST', headers: authHeaders() })
  } finally {
    localStorage.removeItem('token')
    router.push('/adminlogin')
  }
}
</script>

<template>
  <div class="min-h-screen flex justify-center items-center p-4">
    <div class="w-full max-w-md bg-[#FAF8F5] shadow-md rounded-lg p-6 text-black">
      <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Tijdelijke testpagina</p>

      <p v-if="loading">Bezig met laden...</p>

      <div v-else-if="error">
        <h1 class="text-xl font-semibold text-red-600 mb-2">Niet gelukt</h1>
        <p class="mb-4">{{ error }}</p>
        <a href="/adminlogin" class="text-[#1A6CC4] hover:underline">Terug naar admin login</a>
      </div>

      <div v-else-if="user">
        <h1 class="text-xl font-semibold text-green-700 mb-4">Succesvol ingelogd</h1>

        <dl class="mb-6 space-y-1">
          <div><dt class="inline font-medium">Naam: </dt><dd class="inline">{{ user.first_name }} {{ user.last_name }}</dd></div>
          <div><dt class="inline font-medium">E-mail: </dt><dd class="inline">{{ user.email }}</dd></div>
          <div><dt class="inline font-medium">Rol: </dt><dd class="inline">{{ user.role }}</dd></div>
          <div><dt class="inline font-medium">Afdeling: </dt><dd class="inline">{{ user.department }}</dd></div>
          <div><dt class="inline font-medium">Vakantie-uren: </dt><dd class="inline">{{ user.vacation_hours }}</dd></div>
        </dl>

        <h2 class="font-semibold mb-2">Mijn team</h2>
        <p v-if="teamNote" class="text-sm text-gray-600 mb-4">{{ teamNote }}</p>
        <ul v-else class="mb-6 space-y-1">
          <li v-for="member in team" :key="member.id">
            {{ member.first_name }} {{ member.last_name }}
            ({{ member.department }}), {{ member.vacation_hours }} uur
          </li>
        </ul>

        <button
          type="button"
          class="w-full bg-[#1A6CC4] text-white rounded px-4 py-2 hover:bg-[#0056b3]"
          @click="logout"
        >
          Uitloggen
        </button>
      </div>
    </div>
  </div>
</template>