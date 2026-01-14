import { createRouter, createWebHistory } from 'vue-router';
import POS from '../pages/POS.vue';
import Kitchen from '../pages/Kitchen.vue';
import Reports from '../pages/Reports.vue';
import Orders from '../pages/Orders.vue';
import Printers from '../pages/Printers.vue';
import Settings from '../pages/Settings.vue';

const routes = [
  { path: '/app', redirect: '/app/pos' },
  { path: '/app/pos', component: POS },
  { path: '/app/kitchen', component: Kitchen },
  { path: '/app/reports', component: Reports },
  { path: '/app/orders', component: Orders },
  { path: '/app/printers', component: Printers },
   { path: '/app/settings', component: Settings },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
