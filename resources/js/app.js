import { createApp } from 'vue';
import App from './App.vue';
import router from './routers';
import { createPinia } from 'pinia';
import vuetify from './plugins/vuetify';
import Swal from 'sweetalert2';

const app = createApp(App);

app.use(router);
app.use(createPinia());
app.use(vuetify);

app.config.globalProperties.$swal = Swal;

app.mount('#app');
