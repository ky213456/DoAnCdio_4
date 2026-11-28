import { ref } from 'vue'

const STORAGE_KEY = 'techmart_theme'
export const isDark = ref(false)

export function applyTheme(dark) {
  isDark.value = !!dark
  const theme = isDark.value ? 'dark' : 'light'
  document.documentElement.setAttribute('data-bs-theme', theme)
  document.documentElement.classList.toggle('dark-theme', isDark.value)
  try {
    localStorage.setItem(STORAGE_KEY, theme)
  } catch (e) {}
}

export function initTheme() {
  let saved = 'light'
  try {
    saved = localStorage.getItem(STORAGE_KEY) || 'light'
  } catch (e) {}
  applyTheme(saved === 'dark')
}

export function toggleTheme() {
  applyTheme(!isDark.value)
}
