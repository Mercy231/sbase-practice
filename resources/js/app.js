import { createApp } from "vue";
import App from './App.vue';
import Routes from './routes';

const app = createApp(App);
app.use(Routes);
app.mount('#app');
