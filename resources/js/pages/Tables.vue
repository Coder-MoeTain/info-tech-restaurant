<template>
  <div class="panel glass">
    <h3>Tables</h3>
    <form @submit.prevent="save">
      <input v-model="form.name" placeholder="Name" />
      <input type="number" v-model.number="form.capacity" placeholder="Capacity" />
      <select v-model="form.status">
        <option value="available">Available</option>
        <option value="occupied">Occupied</option>
        <option value="reserved">Reserved</option>
        <option value="cleaning">Cleaning</option>
      </select>
      <select v-model="form.floor_id">
        <option v-for="f in floors" :value="f.id" :key="f.id">{{ f.name }}</option>
      </select>
      <button class="btn">Save</button>
    </form>
    <div v-for="t in tables" :key="t.id" class="glass row">
      <div>{{ t.name }}</div><div>{{ t.capacity }}</div><div>{{ t.status }}</div>
      <button class="btn" @click="edit(t)">Edit</button>
      <button class="btn" @click="del(t)">Delete</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const tables = ref([]); const floors = ref([]);
const form = ref({ status:'available' });
async function load(){
  tables.value = (await api.get('/tables')).data;
  floors.value = (await api.get('/floors')).data || floors.value;
}
function edit(t){ form.value = {...t}; }
async function save(){
  form.value.id
    ? await api.put(`/tables/${form.value.id}`, form.value)
    : await api.post('/tables', form.value);
  form.value = { status:'available' }; load();
}
async function del(t){ await api.delete(`/tables/${t.id}`); load(); }
onMounted(load);
</script>

<style scoped>
.row { display:grid; grid-template-columns: 1fr 80px 120px 80px 80px; gap:8px; padding:8px; margin:6px 0; }
form { display:grid; grid-template-columns: repeat(5,1fr); gap:8px; margin-bottom:12px; }
</style>
