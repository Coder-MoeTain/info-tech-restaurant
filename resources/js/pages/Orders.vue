<template>
  <div class="panel glass">
    <h3>Orders</h3>
    <div v-for="o in orders" :key="o.id" class="glass row">
      <div>#{{ o.order_number }}</div>
      <div>{{ o.table?.name || o.order_type }}</div>
      <div>{{ o.status }}</div>
      <button class="btn" @click="reprint(o)">Reprint</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
const orders = ref([]);
async function load(){ orders.value = (await api.get('/orders')).data.data || []; }
async function reprint(o){ await api.post(`/orders/${o.id}/reprint`); }
onMounted(load);
</script>

<style scoped>
.row { display:grid; grid-template-columns: 100px 1fr 120px 120px; gap:8px; padding:8px; margin-bottom:8px; }
</style>
