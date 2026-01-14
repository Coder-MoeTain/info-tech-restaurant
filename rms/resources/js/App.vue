<template>
  <div class="app-shell" :class="theme">
    <header class="topbar glass">
      <div class="brand">GlassRMS</div>
      <div class="top-actions">
        <div class="badge-group">
          <router-link to="/app/pos" class="badge-link">Open Orders <span class="pill">{{ openCount }}</span></router-link>
          <router-link to="/app/kitchen" class="badge-link">KOT <span class="pill">{{ kotCount }}</span></router-link>
        </div>
        <button class="btn" @click="toggleTheme">{{ theme === 'dark' ? 'Light' : 'Dark' }}</button>
        <div class="user-chip">
          <span class="avatar">U</span>
          <div>
            <div class="user-name">User</div>
            <div class="user-role">Role</div>
          </div>
        </div>
        <button class="btn" title="Lock">🔒</button>
      </div>
    </header>
    <div class="layout">
    <aside class="glass sidebar" :class="{ collapsed: collapsed }">
        <div class="brand mini" @click="collapsed = !collapsed">☰</div>
        <router-link to="/app/pos" class="nav-link" active-class="active">POS</router-link>
        <router-link to="/app/kitchen" class="nav-link" active-class="active">Kitchen</router-link>
        <router-link to="/app/reports" class="nav-link" active-class="active">Reports</router-link>
        <router-link to="/app/orders" class="nav-link" active-class="active">Orders</router-link>
        <router-link to="/app/printers" class="nav-link" active-class="active">Printers</router-link>
      <router-link to="/app/settings" class="nav-link" active-class="active">Settings</router-link>
      </aside>
      <main class="content">
        <router-view @open-count="openCount = $event" @kot-count="kotCount = $event" />
      </main>
    </div>
    <ToastHost />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ToastHost from './components/ToastHost.vue';

const theme = ref('light');
const collapsed = ref(false);
const openCount = ref(0);
const kotCount = ref(0);

function toggleTheme(){
  theme.value = theme.value === 'dark' ? 'light' : 'dark';
  document.documentElement.dataset.theme = theme.value;
  localStorage.setItem('theme', theme.value);
}

const saved = localStorage.getItem('theme');
if(saved){ theme.value = saved; document.documentElement.dataset.theme = saved; }
</script>

<style scoped>
.app-shell.light { --bg-main: var(--bg, linear-gradient(135deg,#dfe9f3,#f5f7fa)); }
.app-shell.dark { --bg-main: linear-gradient(135deg,#0f172a,#111827); color:#e2e8f0; }
.app-shell { min-height:100vh; background: var(--bg-main); }
.topbar { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; }
.brand { font-weight:700; font-size:18px; }
.top-actions { display:flex; align-items:center; gap:12px; }
.badge-group { display:flex; gap:8px; }
.badge-link { text-decoration:none; color:inherit; display:flex; align-items:center; gap:6px; padding:8px 10px; border-radius:12px; }
.pill { background:#0f172a; color:#fff; padding:2px 8px; border-radius:999px; font-size:12px; }
.user-chip { display:flex; align-items:center; gap:8px; padding:6px 10px; border-radius:12px; background:rgba(255,255,255,0.3); }
.avatar { width:28px; height:28px; border-radius:50%; background:#0f172a; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
.user-name { font-weight:600; }
.user-role { font-size:12px; opacity:0.8; }
.layout { display:flex; min-height:calc(100vh - 56px); }
.sidebar { width:220px; padding:14px; display:flex; flex-direction:column; gap:10px; position:sticky; top:0; height:calc(100vh - 56px); transition: width 0.2s ease; overflow:hidden; }
.sidebar.collapsed { width:64px; }
.nav-link { text-decoration:none; color:#0f172a; font-weight:600; padding:12px 12px; border-radius:12px; display:block; }
.nav-link.active { background: rgba(255,255,255,0.5); border:1px solid rgba(255,255,255,0.7); }
.content { flex:1; padding:16px; }
@media (max-width: 900px){
  .sidebar { position:fixed; z-index:1500; height:auto; top:56px; left:0; }
  .layout { flex-direction:column; }
}
</style>
