import axios from 'axios';

export default {
  get: (url, cfg = {}) => axios.get(url, cfg),
  post: (url, data, cfg = {}) => axios.post(url, data, cfg),
  put: (url, data, cfg = {}) => axios.put(url, data, cfg),
  delete: (url, cfg = {}) => axios.delete(url, cfg),
};
