<template>
  <div class="toast-wrap">
    <div v-for="t in toasts" :key="t.id" class="toast glass" :class="t.type">
      <span>{{ t.message }}</span>
      <button @click="remove(t.id)">×</button>
    </div>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { useUiStore } from '../stores/ui';
const ui = useUiStore();
const { toasts } = storeToRefs(ui);
const remove = ui.remove;
</script>

<style scoped>
.toast-wrap {
  position: fixed;
  top: 16px;
  right: 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  z-index: 2000;
}
.toast {
  padding: 10px 14px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 200px;
  color: #0f172a;
}
.toast.info { border:1px solid rgba(255,255,255,0.6); }
.toast.error { border:1px solid rgba(255,0,0,0.4); background: rgba(255,0,0,0.1); }
.toast button { background: none; border: none; cursor: pointer; font-size: 16px; }
</style>
