import { createRouter, createWebHistory } from 'vue-router'
import { hasPermission, getFirstAllowedAdminRoute } from '@/utils/permissions'
import { toast } from '@/utils/notify'

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
    component: () => import('@/components/catalog/index.vue'),
    meta: { title: 'Danh Mục Sản Phẩm - TechMart AI', layout: 'client' }
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
    path: '/my-orders',
    name: 'client-my-orders',
    component: () => import('@/components/orders/index.vue'),
    meta: { title: 'Lịch Sử Đơn Hàng Của Tôi - TechMart AI', layout: 'client' }
  },
  {
    path: '/compare',
    name: 'client-compare',
    component: () => import('@/components/compare/index.vue'),
    meta: { title: 'So Sánh Thông Số Kỹ Thuật - TechMart AI', layout: 'client' }
  },
  {
    path: '/profile',
    name: 'client-profile',
    component: () => import('@/components/profile/index.vue'),
    meta: { title: 'Hồ Sơ Cá Nhân & Địa Chỉ - TechMart AI', layout: 'client' }
  },
  {
    path: '/wishlist',
    name: 'client-wishlist',
    component: () => import('@/components/wishlist/index.vue'),
    meta: { title: 'Danh Sách Yêu Thích - TechMart AI', layout: 'client' }
  },
  {
    path: '/warranty',
    alias: '/tra-cuu-bao-hanh',
    name: 'client-warranty',
    component: () => import('@/components/warranty/index.vue'),
    meta: { title: 'Tra Cứu Bảo Hành Điện Tử - TechMart AI', layout: 'client' }
  },
  {
    path: '/product/:id',
    name: 'client-product-detail',
    component: () => import('@/components/product/index.vue'),
    meta: { title: 'Chi Tiết Sản Phẩm - TechMart AI', layout: 'client' }
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
    meta: { title: 'Tổng Quan Hệ Thống - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'overview', perm: 'UC09' }
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Sản Phẩm - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'products', subTab: 'products', perm: 'UC12' }
  },
  {
    path: '/admin/categories',
    name: 'admin-categories',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Danh Mục & Thương Hiệu - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'products', subTab: 'categories', perm: 'UC22' }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Đơn Hàng - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'orders', perm: 'UC14' }
  },
  {
    path: '/admin/serial',
    name: 'admin-serial',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Kho Serial & IMEI FIFO - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'serial', perm: 'UC23' }
  },
  {
    path: '/admin/vouchers',
    name: 'admin-vouchers',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Voucher - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'vouchers', subTab: 'vouchers', perm: 'UC13' }
  },
  {
    path: '/admin/reviews',
    name: 'admin-reviews',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Bình Luận - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'vouchers', subTab: 'reviews', perm: 'UC16' }
  },
  {
    path: '/admin/customers',
    name: 'admin-customers',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Khách Hàng - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'warranty', subTab: 'customers', perm: 'UC15' }
  },
  {
    path: '/admin/warranty',
    name: 'admin-warranty',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Bảo Hành & Đổi Trả - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'warranty', subTab: 'tickets', perm: 'UC19' }
  },
  {
    path: '/admin/staff',
    name: 'admin-staff',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Quản Lý Nhân Viên - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'staff', subTab: 'staff', perm: 'UC10' }
  },
  {
    path: '/admin/permissions',
    name: 'admin-permissions',
    component: () => import('@/components/admin/index.vue'),
    meta: { title: 'Phân Quyền Hệ Thống - ElectroAdmin', layout: 'admin', requiresAuth: true, adminTab: 'staff', subTab: 'rbac', perm: 'UC11' }
  },
  {
    path: '/serial',
    name: 'serial-page',
    component: () => import('@/components/serial/index.vue'),
    meta: { title: 'Kho Serial & IMEI Độc Bản - TechMart Admin', layout: 'admin', requiresAuth: true, perm: 'UC23' }
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
      toast.warning('Vui lòng đăng nhập tài khoản Quản trị viên để truy cập trang này!');
      next({ path: '/login', query: { redirect: to.fullPath } });
      return;
    }

    // Đã đăng nhập nhưng vai_tro là customer -> Từ chối quyền
    if (user.vai_tro === 'customer') {
      toast.error('Truy cập bị từ chối! Tài khoản của bạn là Khách hàng, không có quyền vào cổng Quản trị.');
      next({ path: '/' });
      return;
    }

    // Kiểm tra phân quyền chi tiết theo từng chức năng
    if (to.meta && to.meta.perm && !hasPermission(user, to.meta.perm)) {
      const fallbackPath = getFirstAllowedAdminRoute(user);
      if (fallbackPath && fallbackPath !== to.path) {
        next({ path: fallbackPath });
      } else {
        toast.warning('Bạn chưa được cấp quyền truy cập chức năng này!');
        next({ path: '/' });
      }
      return;
    }
  }

  next()
})

export default router
