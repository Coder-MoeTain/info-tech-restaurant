<script setup>
import { ref, computed, onMounted, onBeforeUnmount, defineEmits } from 'vue';
import api from '../services/api';
import { loadSettings } from '../services/settings';

const tables = ref([]); const categories = ref([]); const menu = ref([]); const modifiers = ref([]);
const cart = ref([]); const openOrders = ref([]); const currentTable = ref(null);
const searchTable = ref(''); const activeCat = ref(null); const searchTerm = ref('');
const discount = ref(0); const discountType = ref('amount');
const payments = ref([{ method: 'cash', amount: 0 }]);
const editItem = ref(null);
const loading = ref(false);
const customer = ref({ name:'', phone:'', address:'' });
const tip = ref(0);
const emit = defineEmits(['open-count','kot-count']);
const settings = ref(loadSettings());
const favorites = ref(loadFavorites());
const selectedFavorite = ref('');

const subtotal = computed(()=> cart.value.reduce((s,i)=> s + i.qty * (effectivePrice(i)),0).toFixed(2));
const discountValue = computed(()=> discountType.value === 'percent' ? (subtotal.value * (discount.value||0) / 100) : Number(discount.value||0));
const tax = computed(()=> ((subtotal.value - discountValue.value) * (settings.value.taxRate || 0)).toFixed(2));
const service = computed(()=> ((subtotal.value - discountValue.value) * (settings.value.serviceRate || 0)).toFixed(2));
const total = computed(()=> (parseFloat(subtotal.value) - parseFloat(discountValue.value) + parseFloat(tax.value) + parseFloat(service.value) + parseFloat(tip.value||0)).toFixed(2));
const filteredTables = computed(()=> tables.value.filter(t=>t.name.toLowerCase().includes(searchTable.value.toLowerCase())));
const filteredMenu = computed(()=> {
  let list = activeCat.value? menu.value.filter(m=>m.category_id===activeCat.value):menu.value;
  if (searchTerm.value) {
    const q = searchTerm.value.toLowerCase();
    list = list.filter(m=> m.name.toLowerCase().includes(q));
  }
  return list;
});
const currentTableLabel = computed(()=> currentTable.value?.name || 'Takeaway');

function sumMods(item){ return (item.mods || []).reduce((s,m)=> s + m.price,0); }
function effectivePrice(item){ const base = item.price + sumMods(item); const d = item.discount_amount || 0; return Math.max(0, base - d); }
function selectTable(t){ currentTable.value = t; }
function addItem(m){ editItem.value = { ...m, uid: crypto.randomUUID(), qty:1, note:'', mods: [] }; }
function confirmItem(){
  if(!editItem.value) return;
  cart.value.push(editItem.value);
  editItem.value = null;
}
function remove(i){ cart.value = cart.value.filter(x=>x.uid!==i.uid); }
function recalc(){ cart.value = [...cart.value]; }

async function loadData(){
  loading.value = true;
  try {
    tables.value = (await api.get('/tables')).data;
    categories.value = (await api.get('/categories')).data;
    menu.value = (await api.get('/menu-items')).data;
    modifiers.value = (await api.get('/modifiers')).data;
    openOrders.value = (await api.get('/orders/open')).data;
    emit('open-count', openOrders.value.length);
    emit('kot-count', 0);
  } finally {
    loading.value = false;
  }
}
async function loadOrder(order){
  currentTable.value = order.table;
}
async function sendToKitchen(){
  const payload = {
    table_id: currentTable.value?.id,
    order_type: currentTable.value ? 'dine_in' : 'takeaway',
    customer_name: customer.value.name,
    customer_phone: customer.value.phone,
    customer_address: customer.value.address,
    items: cart.value.map(c=>({
      item_id:c.id, qty:c.qty, unit_price:(c.price + sumMods(c)),
      discount_amount: c.discount_amount || 0,
      note:noteWithMods(c)
    }))
  };
  await api.post('/orders', payload);
  localStorage.setItem('posLastOrder', JSON.stringify({ cart: cart.value, customer: customer.value }));
  await loadData(); resetCart();
}
async function checkout(method){
  const order = openOrders.value.find(o=>o.table_id===currentTable.value?.id);
  if(!order) return;
  payments.value[0].method = method;
  payments.value[0].amount = total.value;
  await api.post(`/orders/${order.id}/pay`, {
    payments: payments.value,
    grand_total: total.value,
    discount_total: discountValue.value,
    tax_total: tax.value,
    service_charge_total: service.value,
    tip_amount: tip.value || 0,
  });
  localStorage.setItem('posLastOrder', JSON.stringify({ cart: cart.value, customer: customer.value }));
  await loadData(); resetCart();
}
onMounted(() => {
  loadData();
  window.addEventListener('keydown', onKeys);
});
onBeforeUnmount(() => window.removeEventListener('keydown', onKeys));

function onKeys(e){
  if(e.ctrlKey && e.key === 'f'){ e.preventDefault(); document.querySelector('#itemSearch')?.focus(); }
  if(e.key === 'F2'){ e.preventDefault(); sendToKitchen(); }
  if(e.key === 'F4'){ e.preventDefault(); checkout('cash'); }
}

function noteWithMods(c){
  const modText = (c.mods||[]).map(m=>m.name).join(', ');
  return [c.note, modText].filter(Boolean).join(' | ');
}

function toggleMod(mod){
  if(!editItem.value) return;
  const exists = editItem.value.mods?.find(m=>m.id===mod.id);
  if(exists){
    editItem.value.mods = editItem.value.mods.filter(m=>m.id!==mod.id);
  } else {
    editItem.value.mods = [...(editItem.value.mods||[]), mod];
  }
}

function addPayment(){ payments.value.push({ method:'cash', amount:0 }); }
function removePayment(i){ payments.value.splice(i,1); if(!payments.value.length) payments.value.push({method:'cash',amount:0}); }
function autoFillRemaining(){
  const sum = payments.value.reduce((s,p)=> s + Number(p.amount||0),0);
  const remaining = parseFloat(total.value) - sum;
  if(remaining > 0) payments.value[0].amount = Number(payments.value[0].amount||0) + remaining;
}
function resetCart(){
  cart.value = [];
  payments.value=[{method:'cash', amount:0}];
  discount.value=0;
  tip.value=0;
}

function repeatLast(){
  const raw = localStorage.getItem('posLastOrder');
  if(!raw) return;
  const last = JSON.parse(raw);
  cart.value = last.cart || [];
  customer.value = last.customer || {name:'',phone:'',address:''};
}

function saveFavorite(){
  const name = prompt('Favorite name?');
  if(!name) return;
  const fav = { name, cart: cart.value };
  const list = loadFavorites().filter(f=>f.name!==name);
  list.push(fav);
  localStorage.setItem('posFavorites', JSON.stringify(list));
  favorites.value = list;
}

function loadFavorites(){
  try { return JSON.parse(localStorage.getItem('posFavorites')) || []; } catch { return []; }
}

function applyFavorite(){
  const fav = favorites.value.find(f=>f.name === selectedFavorite.value);
  if(!fav) return;
  cart.value = JSON.parse(JSON.stringify(fav.cart || []));
}
</script>

<style scoped>
.pos { display:grid; grid-template-columns: 1fr 1.5fr 1fr; gap:12px; padding:16px; }
.panel { padding:14px; border-radius:16px; background:rgba(255,255,255,0.2); backdrop-filter:blur(20px); }
.panel button, .panel input, .panel textarea, .panel select { min-height:44px; }
.menu-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(130px,1fr)); gap:10px; }
.card { padding:14px; min-height:90px; }
.row { display:grid; grid-template-columns: 1fr 110px 1fr 110px 50px; gap:10px; align-items:center; padding:10px; }
.actions { display:flex; gap:8px; flex-wrap:wrap; }
.order { padding:8px; margin-bottom:8px; cursor:pointer; }
.modal {
  position: fixed; inset: 0; background: rgba(0,0,0,0.35);
  display:flex; align-items:center; justify-content:center; z-index:1500;
}
.modal-card { background: white; padding:16px; border-radius:14px; width:320px; max-width:90vw; }
.mods { display:flex; flex-wrap:wrap; gap:8px; margin:8px 0; }
.mods button { padding:8px 10px; border-radius:10px; border:1px solid rgba(0,0,0,0.1); background:rgba(255,255,255,0.7); }
.payments { display:flex; flex-direction:column; gap:6px; margin-top:8px; }
.payments-row { display:grid; grid-template-columns: 120px 1fr 50px; gap:6px; align-items:center; }
.sticky-footer { position:sticky; bottom:0; background:rgba(255,255,255,0.7); padding:10px; border-radius:12px; margin-top:10px; }
.skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.2), rgba(255,255,255,0.6), rgba(255,255,255,0.2)); height:18px; border-radius:8px; animation: shimmer 1.5s infinite; }
@keyframes shimmer { 0% {background-position: -200px 0;} 100% {background-position: 200px 0;} }
.cart-panel { display:flex; flex-direction:column; height:100%; }
.cart-scroll { flex:1; overflow:auto; margin-top:8px; display:flex; flex-direction:column; gap:8px; }
.tab-row { display:flex; gap:8px; flex-wrap:wrap; }
.list { display:flex; flex-direction:column; gap:8px; max-height:300px; overflow:auto; }
.qty-controls { display:flex; gap:6px; align-items:center; }
</style>

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
      <div v-if="loading" class="skeleton"></div>
      <div v-else v-for="o in openOrders" :key="o.id" class="glass order" @click="loadOrder(o)">
        #{{ o.order_number }} - {{ o.table?.name || o.order_type }} - {{ o.status }}
      </div>
      <div class="row-gap" style="margin-top:8px;">
        <button class="btn" @click="repeatLast()">Repeat Last Order</button>
        <button class="btn" @click="saveFavorite()">Save Favorite</button>
        <select v-model="selectedFavorite" @change="applyFavorite" style="min-width:140px;">
          <option value="">Favorites</option>
          <option v-for="f in favorites" :key="f.name" :value="f.name">{{ f.name }}</option>
        </select>
      </div>
    </div>

    <div class="panel">
      <div class="tabs">
        <input id="itemSearch" v-model="searchTerm" placeholder="Search items" style="width:100%; margin-bottom:8px;" />
        <div class="tab-row">
          <button v-for="c in categories" :key="c.id" class="btn" @click="activeCat=c.id">{{ c.name }}</button>
        </div>
      </div>
      <div class="menu-grid">
        <button class="card glass" v-for="m in filteredMenu" :key="m.id" @click="addItem(m)" @mousedown.prevent="addItem(m)">
          <img v-if="m.image_url" :src="m.image_url" alt="" style="width:100%; height:80px; object-fit:cover; border-radius:12px; margin-bottom:6px;">
          <div class="row-gap" style="justify-content:space-between; align-items:center;">
            <span>{{ m.name }}</span>
            <span v-if="m.stock !== null" class="tag" :style="{background:(m.stock <= m.low_stock_threshold ? 'rgba(239,68,68,0.15)' : 'var(--accent-soft)'), color:(m.stock <= m.low_stock_threshold ? '#b91c1c' : 'var(--accent)')}">
              {{ m.stock ?? 0 }} in stock
            </span>
          </div>
          <div>${{ m.price }}</div>
        </button>
        <div v-if="loading" class="skeleton" style="height:120px;"></div>
      </div>
    </div>

    <div class="panel cart-panel">
      <h3>Cart - {{ currentTableLabel }}</h3>
      <div class="customer">
        <input v-model="customer.name" placeholder="Customer name" />
        <input v-model="customer.phone" placeholder="Phone" />
        <input v-model="customer.address" placeholder="Address (delivery/takeaway)" />
      </div>
      <div class="cart-scroll">
        <div v-for="i in cart" :key="i.uid" class="glass row hover-card">
          <div>{{ i.name }}</div>
          <div class="qty-controls">
            <button class="btn" @click="i.qty=Math.max(1,i.qty-1); recalc();">-</button>
            <input type="number" v-model.number="i.qty" min="1" @change="recalc"/>
            <button class="btn" @click="i.qty++; recalc();">+</button>
          </div>
          <textarea v-model="i.note" placeholder="Note (no chili)"></textarea>
          <div>
            <input type="number" v-model.number="i.discount_amount" placeholder="Discount" style="width:90px;" />
            <div>${{ (i.qty * effectivePrice(i)).toFixed(2) }}</div>
          </div>
          <button class="btn" @click="remove(i)">x</button>
        </div>
      </div>
      <div class="sticky-footer glass">
        <div class="totals">
          <div>Subtotal: {{ subtotal }}</div>
          <div>Discount:
            <input type="number" v-model.number="discount" style="width:80px;" />
            <select v-model="discountType" style="width:90px;">
              <option value="amount">$</option>
              <option value="percent">%</option>
            </select>
            ({{ discountValue.toFixed(2) }})
          </div>
          <div>Tax: {{ tax }}</div>
          <div>Service: {{ service }}</div>
          <div>Tip: <input type="number" v-model.number="tip" style="width:90px;" /></div>
          <div><strong>Total: {{ total }}</strong></div>
        </div>
        <div class="payments">
          <div class="payments-row" v-for="(p,idx) in payments" :key="idx">
            <select v-model="p.method">
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="mobile">Mobile</option>
            </select>
            <input type="number" v-model.number="p.amount" min="0" step="0.01" />
            <button class="btn" @click="removePayment(idx)">-</button>
          </div>
          <div style="display:flex; gap:6px;">
            <button class="btn" @click="addPayment">Add Payment</button>
            <button class="btn" @click="autoFillRemaining">Auto-fill</button>
          </div>
        </div>
        <div class="actions">
          <button class="btn" @click="sendToKitchen">F2 Send to Kitchen</button>
          <button class="btn" @click="checkout('cash')">F4 Checkout</button>
        </div>
      </div>
    </div>
  </div>

  <div v-if="editItem" class="modal">
    <div class="modal-card">
      <h4>{{ editItem.name }}</h4>
      <label>Qty <input type="number" v-model.number="editItem.qty" min="1" /></label>
      <label>Note <textarea v-model="editItem.note"></textarea></label>
      <div class="mods">
        <button v-for="m in modifiers" :key="m.id"
          :class="{ active: editItem.mods?.some(x=>x.id===m.id) }"
          @click="toggleMod(m)">
          {{ m.name }} <small v-if="m.price">+{{ m.price }}</small>
        </button>
      </div>
      <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:10px;">
        <button class="btn" @click="editItem=null">Cancel</button>
        <button class="btn" @click="confirmItem">Add</button>
      </div>
    </div>
  </div>
</template>
