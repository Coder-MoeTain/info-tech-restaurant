import { createRouter, createWebHistory } from 'vue-router';
import Login from '../pages/Login.vue';
import POS from '../pages/POS.vue';
import Kitchen from '../pages/Kitchen.vue';
import Reports from '../pages/Reports.vue';
import Orders from '../pages/Orders.vue';
import Tables from '../pages/Tables.vue';
import Menu from '../pages/Menu.vue';
import Users from '../pages/Users.vue';
import Printers from '../pages/Printers.vue';

const routes = [
  { path: '/login', component: Login },
  { path: '/', redirect: '/pos' },
  { path: '/pos', component: POS },
  { path: '/kitchen', component: Kitchen },
  { path: '/reports', component: Reports },
  { path: '/orders', component: Orders },
  { path: '/tables', component: Tables },
  { path: '/menu', component: Menu },
  { path: '/users', component: Users },
  { path: '/printers', component: Printers },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
