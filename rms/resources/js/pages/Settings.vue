<template>
  <div class="panel glass">
    <h3>Settings</h3>
    <form @submit.prevent="save">
      <div class="row-gap">
        <label class="setting">
          <span>Tax Rate (%)</span>
          <input type="number" step="0.01" v-model.number="form.taxRate" />
        </label>
        <label class="setting">
          <span>Service Rate (%)</span>
          <input type="number" step="0.01" v-model.number="form.serviceRate" />
        </label>
      </div>
      <button class="btn" style="margin-top:12px;">Save</button>
    </form>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { loadSettings, saveSettings } from '../services/settings';
import { useUiStore } from '../stores/ui';

const ui = useUiStore();
const form = reactive(loadSettings());

function save(){
  saveSettings(form);
  ui.toast('Settings saved', 'info');
}
</script>

<style scoped>
.setting { display:flex; flex-direction:column; gap:6px; min-width:180px; }
.row-gap { display:flex; gap:12px; flex-wrap:wrap; }
</style>
