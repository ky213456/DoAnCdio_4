export const API_BASE_URL = 'http://127.0.0.1:8000/api';

export function apiUrl(path) {
  const base = API_BASE_URL.replace(/\/+$/, '');
  const p = String(path || '').replace(/^\/+/, '');
  return `${base}/${p}`;
}

export default apiUrl;

