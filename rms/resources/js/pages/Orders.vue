<template>
  <div class="panel glass">
    <h3>Orders</h3>
    <div class="filters">
      <input type="date" v-model="filters.start" />
      <input type="date" v-model="filters.end" />
      <select v-model="filters.status">
        <option value="">All</option>
        <option value="paid">Paid</option>
        <option value="open">Open</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <input v-model="filters.search" placeholder="Table/order #/waiter" />
      <button class="btn" @click="load">Filter</button>
    </div>
    <div v-for="o in orders" :key="o.id" class="glass row">
      <div>#{{ o.order_number }}</div>
      <div>{{ o.table?.name || o.order_type }}</div>
      <div>{{ o.status }}</div>
      <div>{{ o.waiter?.name }}</div>
      <div>{{ o.paid_at }}</div>
      <button class="btn" @click="reprint(o)">Reprint</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
const orders = ref([]);
const filters = ref({
  start: '',
  end: '',
  status: '',
  search: '',
});

async function load(){
  const params = {};
  if(filters.value.start) params.start = filters.value.start;
  if(filters.value.end) params.end = filters.value.end;
  if(filters.value.status) params.status = filters.value.status;
  if(filters.value.search) params.search = filters.value.search;
  const res = await api.get('/orders', { params });
  orders.value = res.data.data || res.data;
}
async function reprint(o){ await api.post(`/orders/${o.id}/reprint`); }
onMounted(load);
</script>

<style scoped>
.row { display:grid; grid-template-columns: 120px 1fr 120px 140px 160px 120px; gap:8px; padding:8px; margin-bottom:8px; }
.filters { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
</style>
