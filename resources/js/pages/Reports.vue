<template>
  <div class="panel glass reports">
    <h3>Daily Report</h3>
    <input type="date" v-model="date" @change="load" />
    <div class="grid">
      <div class="glass stat">Orders: {{ data.summary?.orders || 0 }}</div>
      <div class="glass stat">Gross: {{ data.summary?.gross || 0 }}</div>
      <div class="glass stat">Tax: {{ data.summary?.tax || 0 }}</div>
      <div class="glass stat">Service: {{ data.summary?.service || 0 }}</div>
    </div>
    <h4>By Payment</h4>
    <div v-for="p in data.by_payment || []" :key="p.method">{{ p.method }}: {{ p.total }}</div>
    <h4>By Dish</h4>
    <div v-for="d in data.by_dish || []" :key="d.item_id">{{ d.item?.name }} - {{ d.qty }}</div>
    <button class="btn" @click="exportFile">Export XLSX</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const date = ref(new Date().toISOString().substring(0,10));
const data = ref({});
async function load(){ data.value = (await api.get('/reports/daily', { params:{date: date.value} })).data; }
async function exportFile(){ await api.get('/reports/export', { params:{ start: date.value, end: date.value }, responseType:'blob' }); }
onMounted(load);
</script>

<style scoped>
.reports { padding:16px; }
.grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(160px,1fr)); gap:10px; }
.stat { padding:12px; }
</style>
