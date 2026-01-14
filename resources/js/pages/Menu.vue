<template>
  <div class="panel glass">
    <h3>Menu</h3>
    <form @submit.prevent="saveCategory">
      <input v-model="catName" placeholder="New category" />
      <button class="btn">Add Category</button>
    </form>
    <div class="grid">
      <div v-for="c in categories" :key="c.id" class="glass cat">
        <div class="row">
          <strong>{{ c.name }}</strong>
          <button class="btn" @click="selectCategory(c)">Items</button>
        </div>
      </div>
    </div>

    <div v-if="activeCat" class="glass editor">
      <h4>{{ activeCat.name }} Items</h4>
      <form @submit.prevent="saveItem">
        <input v-model="itemForm.name" placeholder="Item name" />
        <input type="number" step="0.01" v-model.number="itemForm.price" placeholder="Price" />
        <button class="btn">Save Item</button>
      </form>
      <div v-for="i in items" :key="i.id" class="glass row">
        <div>{{ i.name }}</div><div>${{ i.price }}</div>
        <button class="btn" @click="editItem(i)">Edit</button>
        <button class="btn" @click="delItem(i)">Delete</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const categories = ref([]); const items = ref([]); const activeCat = ref(null);
const catName = ref(''); const itemForm = ref({});

async function load(){ categories.value = (await api.get('/categories')).data; if(activeCat.value){ await loadItems(activeCat.value.id); } }
async function loadItems(catId){ items.value = (await api.get('/menu-items')).data.filter(i=>i.category_id===catId); }
async function saveCategory(){ await api.post('/categories',{name:catName.value}); catName.value=''; load(); }
function selectCategory(c){ activeCat.value = c; loadItems(c.id); }
function editItem(i){ itemForm.value = {...i}; }
async function saveItem(){
  itemForm.value.category_id = activeCat.value.id;
  itemForm.value.id
    ? await api.put(`/menu-items/${itemForm.value.id}`, itemForm.value)
    : await api.post('/menu-items', itemForm.value);
  itemForm.value={}; loadItems(activeCat.value.id);
}
async function delItem(i){ await api.delete(`/menu-items/${i.id}`); loadItems(activeCat.value.id); }
onMounted(load);
</script>

<style scoped>
.grid { display:grid; grid-template-columns: repeat(auto-fill,minmax(200px,1fr)); gap:10px; }
.cat { padding:12px; }
.editor { margin-top:16px; padding:12px; }
.row { display:grid; grid-template-columns: 1fr 80px 80px 80px; gap:6px; padding:6px; margin:6px 0; }
form { display:flex; gap:8px; margin-bottom:12px; }
</style>
