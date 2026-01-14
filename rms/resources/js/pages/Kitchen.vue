<template>
  <div class="panel glass kitchen">
    <div class="header">
      <h3>Kitchen Tickets</h3>
      <div class="filters">
        <select v-model="station">
          <option value="">All Stations</option>
          <option v-for="s in stations" :key="s" :value="s">{{ s }}</option>
        </select>
        <button class="btn" @click="load">Refresh</button>
      </div>
    </div>
    <div class="tickets">
      <div v-for="t in filteredTickets" :key="t.id" class="glass ticket" :class="ageClass(t)">
        <div class="row">
          <strong>#{{ t.order_number }}</strong>
          <span>{{ t.table?.name || t.order_type }}</span>
          <span>{{ t.status }}</span>
        </div>
        <div class="timer">Sent {{ since(t.sent_to_kitchen_at || t.created_at) }} ago</div>
        <div v-for="i in t.items" :key="i.id">
          <span v-if="!showItem(i)"> <!-- filtered out by station --></span>
          <template v-else>
            {{ i.qty }} x {{ i.item?.name }} <span v-if="i.note">({{ i.note }})</span>
          </template>
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
import { ref, computed, onMounted } from 'vue';
const emit = defineEmits(['kot-count']);
import api from '../services/api';

const tickets = ref([]);
const categories = ref([]);
const station = ref('');

async function load(){
  categories.value = (await api.get('/categories')).data;
  tickets.value = (await api.get('/kitchen/tickets')).data;
  emit('kot-count', tickets.value.length);
}
async function update(ticket,status){ await api.post(`/kitchen/${ticket.id}/status`, {status}); load(); }
onMounted(load);

const stations = computed(()=> {
  const s = categories.value.map(c=>c.station).filter(Boolean);
  return [...new Set(s)];
});

function catStation(catId){
  return categories.value.find(c=>c.id===catId)?.station || '';
}

const filteredTickets = computed(()=> {
  if(!station.value) return tickets.value;
  return tickets.value.filter(t => t.items.some(i => catStation(i.item?.category_id) === station.value));
});

function showItem(item){
  if(!station.value) return true;
  return catStation(item.item?.category_id) === station.value;
}

function since(ts){
  if(!ts) return '0m';
  const diffMs = Date.now() - new Date(ts).getTime();
  const mins = Math.floor(diffMs/60000);
  const secs = Math.floor((diffMs%60000)/1000);
  return `${mins}m ${secs}s`;
}

function ageClass(t){
  const diff = Date.now() - new Date(t.sent_to_kitchen_at || t.created_at).getTime();
  const mins = diff/60000;
  if (mins > 15) return 'age-high';
  if (mins > 5) return 'age-mid';
  return '';
}
</script>

<style scoped>
.kitchen { padding:16px; }
.header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.filters { display:flex; gap:8px; align-items:center; }
.tickets { display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:12px; }
.ticket { padding:12px; }
.row { display:flex; justify-content:space-between; }
.timer { font-size:12px; color:#475569; margin-bottom:6px; }
.age-mid { border: 1px solid #f59e0b; }
.age-high { border: 1px solid #ef4444; }
</style>
