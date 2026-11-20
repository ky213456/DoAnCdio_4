import { createRouter, createWebHistory } from 'vue-router'

// ==========================================
// 1. ĐƯỜNG TRUYỀN PHÍA NGƯỜI DÙNG (CLIENT ROUTES)
// ==========================================
const clientRoutes = [
  {
    path: '/',
    name: 'client-home',
    component: () => import('@/components/trangchu/index.vue'),
    meta: { title: 'Trang Chủ - TechMart AI', layout: 'client' }
  },
  {
    path: '/catalog',
    name: 'client-catalog',
    // Sau này liên kết với view Danh mục của Võ Đình Huy
    component: () => import('@/components/trangchu/index.vue'),
    meta: { title: 'Danh Mục Sản Phẩm', layout: 'client' }
  },
  {
    path: '/cart',
    name: 'client-cart',
    // Wireframe 07: Giao diện Giỏ hàng trực tuyến & Bảng chi tiết đơn hàng (Lê Quốc Khánh)
    component: () => import('@/components/cart/index.vue'),
    meta: { title: 'Giỏ Hàng - TechMart AI', layout: 'client' }
  },
  {
    path: '/checkout',
    name: 'client-checkout',
    component: () => import('@/components/checkout/index.vue'),
    meta: { title: 'Đặt Hàng & Thanh Toán - TechMart AI', layout: 'client' }
  },
  {
    path: '/product/:id',
    name: 'client-product-detail',
    // Sau này liên kết với view Chi tiết của Phạm Tiến Ninh
    component: () => import('@/components/trangchu/index.vue'),
    meta: { title: 'Chi Tiết Sản Phẩm', layout: 'client' }
  },
  {
    path: '/login',
    name: 'client-login',
    component: () => import('@/components/auth/index.vue'),
    meta: { title: 'Đăng Nhập Khách Hàng - TechMart AI', layout: 'auth' }
  },
  {
    path: '/register',
    name: 'client-register',
    component: () => import('@/components/auth/index.vue'),
    meta: { title: 'Đăng Ký Tài Khoản - TechMart AI', layout: 'auth' }
  },
  {
    path: '/payment/payos-return',
    name: 'payos-return',
    component: () => import('@/components/checkout/PayOsReturn.vue'),
    meta: { title: 'Kết Quả Thanh Toán PayOS - TechMart AI', layout: 'client' }
  },
  {
    path: '/payment/vnpay-return',
    name: 'vnpay-return',
    component: () => import('@/components/checkout/VnpayReturn.vue'),
    meta: { title: 'Kết Quả Thanh Toán VNPAY - TechMart AI', layout: 'client' }
  }
]

// ==========================================
// 2. ĐƯỜNG TRUYỀN PHÍA QUẢN TRỊ (ADMIN ROUTES)
// ==========================================
const adminRoutes = [
  {
    path: '/admin',
    name: 'admin-dashboard',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Bảng Điều Khiển Quản Trị - Admin BI', layout: 'admin', requiresAuth: true }
  },
  {
    path: '/admin/categories',
    name: 'admin-categories',
    // Sau này liên kết với Quản lý danh mục của Dương Văn Trọng Đại
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Danh Mục', layout: 'admin' }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    // Sau này liên kết với Quản lý đơn hàng & Kho Serial
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Đơn Hàng & Serial', layout: 'admin' }
  }
]

// Hợp nhất 2 luồng đường truyền riêng biệt
const routes = [
  ...clientRoutes,
  ...adminRoutes,
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

// Navigation Guard: Cập nhật tiêu đề & Phân quyền bảo vệ Route
router.beforeEach((to, from, next) => {
  if (to.meta && to.meta.title) {
    document.title = to.meta.title
  }

  // 1. Phân quyền bảo vệ các Route Quản trị (/admin...)
  if (to.matched.some(record => record.meta.requiresAuth)) {
    const adminToken = localStorage.getItem('admin_token') || localStorage.getItem('customer_token');
    const storedUser = localStorage.getItem('admin_user') || localStorage.getItem('customer_user');
    let user = null;
    if (storedUser) {
      try { user = JSON.parse(storedUser); } catch (e) { user = null; }
    }

    // Chưa đăng nhập
    if (!adminToken || !user) {
      alert('Vui lòng đăng nhập tài khoản Quản trị viên để truy cập trang này!');
      next({ path: '/login', query: { redirect: to.fullPath } });
      return;
    }

    // Đã đăng nhập nhưng vai_tro là customer -> Từ chối quyền
    if (user.vai_tro === 'customer') {
      alert('Truy cập bị từ chối! Tài khoản của bạn là Khách hàng, không có quyền vào cổng Quản trị.');
      next({ path: '/' });
      return;
    }
  }

  next()
})

export default router
