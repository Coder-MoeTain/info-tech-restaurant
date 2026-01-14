import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import './styles/glass.css';
import axios from 'axios';
import { useUiStore } from './stores/ui';

axios.defaults.baseURL = '/api';
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) config.headers['Authorization'] = `Bearer ${token}`;
  return config;
});
axios.interceptors.response.use(
  (r) => r,
  (error) => {
    const ui = useUiStore();
    const msg = error.response?.data?.message || 'Request failed';
    ui.toast(msg, 'error');
    return Promise.reject(error);
  }
);

createApp(App).use(createPinia()).use(router).mount('#app');
import './bootstrap';
