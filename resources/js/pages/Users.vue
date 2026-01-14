<template>
  <div class="panel glass">
    <h3>Users</h3>
    <form @submit.prevent="save">
      <input v-model="form.name" placeholder="Name" />
      <input v-model="form.email" placeholder="Email" />
      <input v-model="form.password" placeholder="Password" :type="form.id ? 'text' : 'password'" />
      <select v-model="form.roles" multiple>
        <option v-for="r in roles" :value="r.name" :key="r.id">{{ r.name }}</option>
      </select>
      <button class="btn">Save</button>
    </form>
    <div v-for="u in users" :key="u.id" class="glass row">
      <div>{{ u.name }} ({{ u.email }})</div>
      <div>{{ u.roles.map(r=>r.name).join(', ') }}</div>
      <button class="btn" @click="edit(u)">Edit</button>
      <button class="btn" @click="del(u)">Delete</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const users = ref([]); const roles = ref([]); const form = ref({ roles: [] });
async function load(){ users.value = (await api.get('/users')).data.data || []; roles.value = (await api.get('/roles')).data; }
function edit(u){ form.value = { ...u, roles: u.roles.map(r=>r.name), password:'' }; }
async function save(){
  form.value.id
    ? await api.put(`/users/${form.value.id}`, form.value)
    : await api.post('/users', form.value);
  form.value = { roles: [] }; load();
}
async function del(u){ await api.delete(`/users/${u.id}`); load(); }
onMounted(load);
</script>

<style scoped>
.row { display:grid; grid-template-columns: 1fr 1fr 80px 80px; gap:8px; padding:8px; margin:6px 0; }
form { display:grid; grid-template-columns: repeat(5,1fr); gap:8px; margin-bottom:12px; }
select { min-height: 44px; }
</style>
