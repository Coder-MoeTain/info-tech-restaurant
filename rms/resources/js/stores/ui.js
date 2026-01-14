import { defineStore } from 'pinia';

export const useUiStore = defineStore('ui', {
  state: () => ({
    toasts: [],
  }),
  actions: {
    toast(message, type = 'info', ttl = 3000) {
      const id = crypto.randomUUID();
      this.toasts.push({ id, message, type });
      setTimeout(() => this.remove(id), ttl);
    },
    remove(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
  },
});
