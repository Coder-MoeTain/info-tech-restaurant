<template>
  <div class="panel glass reports">
    <div class="header">
      <h3>Daily Sales Report</h3>
      <div class="row-gap">
        <input type="date" v-model="date" @change="load" />
        <button class="btn" @click="exportFile">Export XLSX</button>
      </div>
    </div>

    <div class="grid cards">
      <div class="glass stat">
        <div class="label">Orders</div>
        <div class="value">{{ data.summary?.orders || 0 }}</div>
      </div>
      <div class="glass stat">
        <div class="label">Gross</div>
        <div class="value">{{ money(data.summary?.gross) }}</div>
      </div>
      <div class="glass stat">
        <div class="label">Avg Ticket</div>
        <div class="value">{{ money(dash.summary?.avg_ticket) }}</div>
      </div>
      <div class="glass stat">
        <div class="label">Items Sold</div>
        <div class="value">{{ dash.summary?.items_sold || 0 }}</div>
      </div>
      <div class="glass stat">
        <div class="label">Void Count</div>
        <div class="value">{{ dash.voids || 0 }}</div>
      </div>
      <div class="glass stat">
        <div class="label">Refund Count</div>
        <div class="value">{{ dash.refunds || 0 }}</div>
      </div>
    </div>

    <div class="section">
      <h4>Payment Mix</h4>
      <table>
        <thead><tr><th>Method</th><th>Total</th></tr></thead>
        <tbody>
          <tr v-for="p in data.by_payment || []" :key="p.method">
            <td>{{ p.method }}</td><td>{{ money(p.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="section">
      <h4>Top Dishes</h4>
      <table>
        <thead><tr><th>Dish</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
          <tr v-for="d in data.by_dish || []" :key="d.item_id">
            <td>{{ d.item?.name }}</td><td>{{ d.qty }}</td><td>{{ money(d.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const date = ref(new Date().toISOString().substring(0,10));
const data = ref({});
const dash = ref({});
async function load(){
  data.value = (await api.get('/reports/daily', { params:{date: date.value} })).data;
  dash.value = (await api.get('/reports/dashboard', { params:{date: date.value} })).data;
}
async function exportFile(){
  const res = await api.get('/reports/export', { params:{ start: date.value, end: date.value }, responseType:'blob' });
  const url = window.URL.createObjectURL(new Blob([res.data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', 'sales.xlsx');
  document.body.appendChild(link);
  link.click();
  link.remove();
}
function money(val){ return Number(val||0).toFixed(2); }
onMounted(load);
</script>

<style scoped>
.reports { padding:16px; display:flex; flex-direction:column; gap:16px; }
.grid.cards { display:grid; grid-template-columns: repeat(auto-fill,minmax(180px,1fr)); gap:12px; }
.stat { padding:12px; }
.label { font-size:13px; opacity:0.8; }
.value { font-size:20px; font-weight:700; margin-top:4px; }
.section table { width:100%; border-collapse: collapse; }
.section th, .section td { padding:8px; text-align:left; }
.section tr:nth-child(even){ background: rgba(255,255,255,0.1); }
.header { display:flex; justify-content:space-between; align-items:center; }
.row-gap { display:flex; gap:8px; flex-wrap:wrap; }
</style>
