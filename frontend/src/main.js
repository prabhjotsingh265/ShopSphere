import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.min.js'
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import 'vue-loading-overlay/dist/css/index.css'
import 'vue-image-zoomer/dist/style.css'
import "vue-toastification/dist/index.css"
import './assets/main.css'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import VueDOMPurifyHTML from 'vue-dompurify-html'
import VueImageZoomer from 'vue-image-zoomer'
import Toast from "vue-toastification"
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'


const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(Toast)
app.use(VueImageZoomer)
app.use(VueDOMPurifyHTML)
pinia.use(piniaPluginPersistedstate)

app.mount('#app')
