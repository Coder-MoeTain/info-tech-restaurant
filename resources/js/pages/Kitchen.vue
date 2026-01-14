<template>
  <div class="panel glass kitchen">
    <h3>Kitchen Tickets</h3>
    <div class="tickets">
      <div v-for="t in tickets" :key="t.id" class="glass ticket">
        <div class="row">
          <strong>#{{ t.order_number }}</strong>
          <span>{{ t.table?.name || t.order_type }}</span>
          <span>{{ t.status }}</span>
        </div>
        <div v-for="i in t.items" :key="i.id">
          {{ i.qty }} x {{ i.item?.name }} <span v-if="i.note">({{ i.note }})</span>
        </div>
        <div class="actions">
          <button class="btn" @click="update(t,'preparing')">Preparing</button>
          <button class="btn" @click="update(t,'ready')">Ready</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const tickets = ref([]);
async function load(){ tickets.value = (await api.get('/kitchen/tickets')).data; }
async function update(ticket,status){ await api.post(`/kitchen/${ticket.id}/status`, {status}); load(); }
onMounted(load);
</script>

<style scoped>
.kitchen { padding:16px; }
.tickets { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:12px; }
.ticket { padding:12px; }
.row { display:flex; justify-content:space-between; }
</style>
