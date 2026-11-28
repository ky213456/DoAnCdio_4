<script setup>
import { RouterView } from 'vue-router'
import { isDark, initTheme, toggleTheme } from '@/utils/theme'

initTheme()
</script>

<template>
  <div class="app-root">
    <RouterView />

    <!-- Nút chuyển Sáng / Tối nhỏ gọn, tách rời ở góc dưới trái -->
    <button
      type="button"
      class="theme-float-btn"
      @click="toggleTheme"
      :title="isDark ? 'Chuyển sang giao diện Sáng' : 'Chuyển sang giao diện Tối'"
      aria-label="Đổi giao diện Sáng/Tối"
    >
      <i :class="['bi', isDark ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill text-primary']"></i>
    </button>
  </div>
</template>

<style scoped>
.app-root {
  min-height: 100vh;
  width: 100%;
}

.theme-float-btn {
  position: fixed;
  bottom: 22px;
  left: 22px;
  z-index: 1060;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.14);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.theme-float-btn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.22);
}

:global(:root.dark-theme) .theme-float-btn {
  background: #1e293b;
  border-color: #475569;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
}
</style>

<style>
/* Global Dark Theme overrides for Storefront & Admin */
:root.dark-theme {
  color-scheme: dark;
  --bs-body-bg: #0b1120;
  --bs-body-color: #f8fafc;
  --bs-emphasis-color: #ffffff;
  --bs-secondary-color: #cbd5e1;
  --bs-tertiary-bg: #1e293b;
  --bs-border-color: #334155;
  --bs-card-bg: #1e293b;
  --bs-card-color: #f8fafc;
}

:root.dark-theme html,
:root.dark-theme body,
:root.dark-theme #app,
:root.dark-theme .app-root {
  background-color: #0b1120 !important;
  color: #f8fafc !important;
}

:root.dark-theme .homepage-container,
:root.dark-theme .techmart-page,
:root.dark-theme .ea-layout,
:root.dark-theme .ea-main-area,
:root.dark-theme .ea-content,
:root.dark-theme .auth-page-container,
:root.dark-theme .cart-page-wrapper,
:root.dark-theme .catalog-page-wrapper,
:root.dark-theme .checkout-page-wrapper,
:root.dark-theme .compare-page-wrapper,
:root.dark-theme .orders-page-wrapper,
:root.dark-theme .product-detail-page-wrapper,
:root.dark-theme .profile-page-wrapper,
:root.dark-theme .warranty-page-wrapper,
:root.dark-theme .wishlist-page-wrapper {
  background: #0b1120 !important;
  background-color: #0b1120 !important;
  color: #e2e8f0 !important;
}

:root.dark-theme .main-header,
:root.dark-theme .main-header-wrapper,
:root.dark-theme .main-navbar,
:root.dark-theme .flash-sale-section,
:root.dark-theme .categories-section,
:root.dark-theme .best-seller-showcase-section,
:root.dark-theme .all-products-section,
:root.dark-theme .brand-and-commitments,
:root.dark-theme .category-card,
:root.dark-theme .commitment-card,
:root.dark-theme .bestseller-card,
:root.dark-theme .filter-sidebar,
:root.dark-theme .search-box,
:root.dark-theme .mega-menu-dropdown,
:root.dark-theme .dropdown-menu,
:root.dark-theme .ea-sidebar,
:root.dark-theme .ea-topbar,
:root.dark-theme .ea-card,
:root.dark-theme .ea-table-card,
:root.dark-theme .cart-summary-card,
:root.dark-theme .order-card,
:root.dark-theme .profile-card,
:root.dark-theme .ai-chat-window,
:root.dark-theme .product-card,
:root.dark-theme .card,
:root.dark-theme .user-dropdown,
:root.dark-theme .search-suggestions-dropdown,
:root.dark-theme .modal-content,
:root.dark-theme .ea-sync-widget,
:root.dark-theme .ea-search-pill,
:root.dark-theme .ea-date-pill,
:root.dark-theme .ea-user-chip,
:root.dark-theme .table {
  background-color: #1e293b !important;
  color: #f8fafc !important;
  border-color: #334155 !important;
}

:root.dark-theme .main-footer {
  background-color: #090d16 !important;
  color: #cbd5e1 !important;
  border-top: 1px solid #1e293b !important;
}

:root.dark-theme .bg-white,
:root.dark-theme .bg-light,
:root.dark-theme .card-header,
:root.dark-theme .card-footer,
:root.dark-theme .list-group-item {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
  border-color: #334155 !important;
}

:root.dark-theme h1,
:root.dark-theme h2,
:root.dark-theme h3,
:root.dark-theme h4,
:root.dark-theme h5,
:root.dark-theme h6,
:root.dark-theme .text-dark,
:root.dark-theme .text-black,
:root.dark-theme .text-body,
:root.dark-theme .form-label,
:root.dark-theme .form-check-label,
:root.dark-theme label,
:root.dark-theme th,
:root.dark-theme td,
:root.dark-theme .product-title,
:root.dark-theme .ea-brand-title,
:root.dark-theme .section-title,
:root.dark-theme .flash-title,
:root.dark-theme .user-dropdown-name,
:root.dark-theme .user-name,
:root.dark-theme .action-title,
:root.dark-theme .cat-name,
:root.dark-theme .brand-name,
:root.dark-theme .commitment-title,
:root.dark-theme .filter-title,
:root.dark-theme .filter-label,
:root.dark-theme .spec-value,
:root.dark-theme .ea-kpi-value,
:root.dark-theme .nav-link,
:root.dark-theme .dropdown-item,
:root.dark-theme .breadcrumb-item,
:root.dark-theme .page-link {
  color: #f8fafc !important;
}

:root.dark-theme p,
:root.dark-theme .product-specs,
:root.dark-theme .user-dropdown-email,
:root.dark-theme .ea-brand-sub,
:root.dark-theme .action-sub,
:root.dark-theme .cat-count,
:root.dark-theme .commitment-desc,
:root.dark-theme .section-subtitle,
:root.dark-theme .spec-label,
:root.dark-theme .ea-kpi-label,
:root.dark-theme .ea-nav-label,
:root.dark-theme .suggestion-item,
:root.dark-theme .text-secondary,
:root.dark-theme .text-muted {
  color: #cbd5e1 !important;
}

:root.dark-theme .page-link,
:root.dark-theme .tab-btn,
:root.dark-theme .bestseller-tab,
:root.dark-theme .hot-keyword-chip,
:root.dark-theme .brand-pill {
  background-color: #273549 !important;
  color: #f8fafc !important;
  border-color: #475569 !important;
}

:root.dark-theme .tab-btn.active,
:root.dark-theme .bestseller-tab.active,
:root.dark-theme .page-item.active .page-link {
  background-color: #2563eb !important;
  color: #ffffff !important;
  border-color: #3b82f6 !important;
}

:root.dark-theme .price-original,
:root.dark-theme del,
:root.dark-theme .small.text-muted {
  color: #94a3b8 !important;
}

:root.dark-theme .price-current {
  color: #f87171 !important;
}

:root.dark-theme .text-primary {
  color: #60a5fa !important;
}

:root.dark-theme .text-success {
  color: #4ade80 !important;
}

:root.dark-theme .text-danger {
  color: #f87171 !important;
}

:root.dark-theme .text-warning {
  color: #fbbf24 !important;
}

:root.dark-theme .btn-light,
:root.dark-theme .btn-outline-secondary,
:root.dark-theme .btn-outline-dark,
:root.dark-theme .btn-card-detail {
  background-color: #273549 !important;
  border-color: #475569 !important;
  color: #f8fafc !important;
}

:root.dark-theme .btn-light:hover,
:root.dark-theme .btn-outline-secondary:hover,
:root.dark-theme .btn-outline-dark:hover {
  background-color: #334155 !important;
  color: #ffffff !important;
}

:root.dark-theme .table > :not(caption) > * > * {
  background-color: #1e293b !important;
  color: #f1f5f9 !important;
  border-color: #334155 !important;
}

:root.dark-theme .table thead th {
  background-color: #0f172a !important;
  color: #f8fafc !important;
}

:root.dark-theme .btn-action-icon-pill,
:root.dark-theme .btn-action-cart,
:root.dark-theme .btn-action-ai,
:root.dark-theme .btn-user {
  background-color: #1e293b !important;
  border-color: #334155 !important;
  color: #f8fafc !important;
}

:root.dark-theme .user-dropdown-item {
  color: #f1f5f9 !important;
}

:root.dark-theme .user-dropdown-item:hover {
  background-color: #334155 !important;
}

:root.dark-theme .ea-nav-item {
  color: #e2e8f0 !important;
}

:root.dark-theme .ea-nav-item:hover,
:root.dark-theme .ea-nav-item.active {
  background-color: #334155 !important;
  color: #ffffff !important;
}

:root.dark-theme input,
:root.dark-theme select,
:root.dark-theme textarea {
  background-color: #0f172a !important;
  color: #f8fafc !important;
  border-color: #475569 !important;
}

:root.dark-theme input::placeholder,
:root.dark-theme textarea::placeholder {
  color: #94a3b8 !important;
  opacity: 1;
}
</style>
