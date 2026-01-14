<template>
  <div class="pos grid glass">
    <div class="panel">
      <h3>Tables</h3>
      <input v-model="searchTable" placeholder="Search table" />
      <div class="list">
        <button v-for="t in filteredTables" :key="t.id" class="btn" @click="selectTable(t)">
          {{ t.name }} <small>({{ t.status }})</small>
        </button>
      </div>
      <h4>Open Orders</h4>
      <div v-for="o in openOrders" :key="o.id" class="glass order" @click="loadOrder(o)">
        #{{ o.order_number }} - {{ o.table?.name || o.order_type }} - {{ o.status }}
      </div>
    </div>

    <div class="panel">
      <div class="tabs">
        <button v-for="c in categories" :key="c.id" class="btn" @click="activeCat=c.id">{{ c.name }}</button>
      </div>
      <div class="menu-grid">
        <button class="card glass" v-for="m in filteredMenu" :key="m.id" @click="addItem(m)">
          <div>{{ m.name }}</div><div>${{ m.price }}</div>
        </button>
      </div>
    </div>

    <div class="panel">
      <h3>Cart - {{ currentTableLabel }}</h3>
      <div v-for="i in cart" :key="i.uid" class="glass row">
        <div>{{ i.name }}</div>
        <input type="number" v-model.number="i.qty" min="1" @change="recalc"/>
        <textarea v-model="i.note" placeholder="Note (no chili)"></textarea>
        <div>${{ (i.qty * i.price).toFixed(2) }}</div>
        <button class="btn" @click="remove(i)">x</button>
      </div>
      <div class="totals">
        <div>Subtotal: {{ subtotal }}</div>
        <div>Tax: {{ tax }}</div>
        <div>Service: {{ service }}</div>
        <div><strong>Total: {{ total }}</strong></div>
      </div>
      <div class="actions">
        <button class="btn" @click="sendToKitchen">Send to Kitchen</button>
        <button class="btn" @click="checkout('cash')">Cash</button>
        <button class="btn" @click="checkout('card')">Card</button>
        <button class="btn" @click="checkout('mobile')">Mobile</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../services/api';

const tables = ref([]); const categories = ref([]); const menu = ref([]);
const cart = ref([]); const openOrders = ref([]); const currentTable = ref(null);
const searchTable = ref(''); const activeCat = ref(null);

const subtotal = computed(()=> cart.value.reduce((s,i)=>s+i.qty*i.price,0).toFixed(2));
const tax = computed(()=> (subtotal.value*0.07).toFixed(2));
const service = computed(()=> (subtotal.value*0.1).toFixed(2));
const total = computed(()=> (parseFloat(subtotal.value)+parseFloat(tax.value)+parseFloat(service.value)).toFixed(2));
const filteredTables = computed(()=> tables.value.filter(t=>t.name.toLowerCase().includes(searchTable.value.toLowerCase())));
const filteredMenu = computed(()=> activeCat.value? menu.value.filter(m=>m.category_id===activeCat.value):menu.value);
const currentTableLabel = computed(()=> currentTable.value?.name || 'Takeaway');

function selectTable(t){ currentTable.value = t; }
function addItem(m){ cart.value.push({ ...m, uid: crypto.randomUUID(), qty:1, note:'' }); }
function remove(i){ cart.value = cart.value.filter(x=>x.uid!==i.uid); }
function recalc(){ cart.value = [...cart.value]; }

async function loadData(){
  tables.value = (await api.get('/tables')).data;
  categories.value = (await api.get('/categories')).data;
  menu.value = (await api.get('/menu-items')).data;
  openOrders.value = (await api.get('/orders/open')).data;
}
async function loadOrder(order){
  currentTable.value = order.table;
}
async function sendToKitchen(){
  const payload = { table_id: currentTable.value?.id, order_type: currentTable.value ? 'dine_in' : 'takeaway', items: cart.value.map(c=>({item_id:c.id, qty:c.qty, unit_price:c.price, note:c.note})) };
  await api.post('/orders', payload);
  await loadData(); cart.value = [];
}
async function checkout(method){
  const order = openOrders.value.find(o=>o.table_id===currentTable.value?.id);
  if(!order) return;
  await api.post(`/orders/${order.id}/pay`, {
    payments:[{method, amount: total.value}],
    grand_total: total.value,
    tax_total: tax.value, service_charge_total: service.value
  });
  await loadData(); cart.value = [];
}
onMounted(loadData);
</script>

<style scoped>
.pos { display:grid; grid-template-columns: 1fr 1.5fr 1fr; gap:12px; padding:16px; }
.panel { padding:12px; border-radius:16px; background:rgba(255,255,255,0.2); backdrop-filter:blur(20px); }
.menu-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(130px,1fr)); gap:10px; }
.card { padding:10px; min-height:90px; }
.row { display:grid; grid-template-columns: 1fr 70px 1fr 80px 50px; gap:6px; align-items:center; padding:8px; }
.actions { display:flex; gap:8px; flex-wrap:wrap; }
.order { padding:8px; margin-bottom:8px; cursor:pointer; }
</style>
