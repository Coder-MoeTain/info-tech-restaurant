<template>
  <div class="login glass">
    <h2>GlassRMS</h2>
    <form @submit.prevent="login">
      <input v-model="email" placeholder="Email" />
      <input type="password" v-model="password" placeholder="Password" />
      <button class="btn">Login</button>
      <p v-if="error" class="error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../services/api';
import router from '../router';

const email = ref('admin@example.com');
const password = ref('password');
const error = ref('');

async function login() {
  try {
    await api.post('/login', { email: email.value, password: password.value });
    router.push('/pos');
  } catch (e) {
    error.value = 'Invalid credentials';
  }
}
</script>

<style scoped>
.login { max-width: 360px; margin: 80px auto; padding: 24px; text-align: center; }
input { width: 100%; margin: 8px 0; }
.error { color: #b00020; }
</style>
