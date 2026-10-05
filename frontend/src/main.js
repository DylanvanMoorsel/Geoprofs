import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import '@fontsource/inter/600.css'


import App from './App.vue'
import router from './router'
import './assets/main.css'


const app = createApp(App)

app.use(PrimeVue, {
  theme: {
    preset: Aura
  }
})
app.use(createPinia())
app.use(router)

app.mount('#app')
