<template>
  <div class="panel glass">
    <h3>Printers</h3>
    <form @submit.prevent="save">
      <input v-model="form.name" placeholder="Name" />
      <select v-model="form.connection">
        <option value="usb">USB</option>
        <option value="lan">LAN</option>
      </select>
      <input v-if="form.connection==='lan'" v-model="form.ip" placeholder="IP" />
      <input v-if="form.connection==='lan'" v-model.number="form.port" placeholder="Port" />
      <label><input type="checkbox" v-model="form.is_kitchen" /> Kitchen</label>
      <label><input type="checkbox" v-model="form.is_cashier" /> Cashier</label>
      <button class="btn">Save</button>
    </form>
    <div v-for="p in printers" :key="p.id" class="glass row">
      <div>{{ p.name }} ({{ p.connection }})</div>
      <div>{{ p.is_kitchen?'Kitchen':'' }} {{ p.is_cashier?'Cashier':'' }}</div>
      <button class="btn" @click="edit(p)">Edit</button>
      <button class="btn" @click="del(p)">Delete</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
const printers = ref([]); const form = ref({connection:'usb'});
async function load(){ printers.value = (await api.get('/printers')).data; }
function edit(p){ form.value = {...p}; }
async function save(){
  form.value.id ? await api.put(`/printers/${form.value.id}`, form.value) : await api.post('/printers', form.value);
  form.value={connection:'usb'}; load();
}
async function del(p){ await api.delete(`/printers/${p.id}`); load(); }
onMounted(load);
</script>

<style scoped>
.row { display:grid; grid-template-columns: 1fr 1fr 80px 80px; gap:8px; padding:8px; margin:6px 0; }
form { display:grid; grid-template-columns: repeat(6,1fr); gap:8px; margin-bottom:12px; align-items:center; }
label { display:flex; gap:6px; align-items:center; }
</style>
