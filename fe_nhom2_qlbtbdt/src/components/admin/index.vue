<template>
  <div class="ea-layout">
    <!-- ========================================== -->
    <!-- LEFT SIDEBAR (ELECTROADMIN V2.4 PRO) -->
    <!-- ========================================== -->
    <aside class="ea-sidebar">
      <div class="ea-brand-box">
        <div class="ea-brand-icon"><i class="bi bi-cpu-fill"></i></div>
        <div>
          <div class="ea-brand-title">ElectroAdmin</div>
          <div class="ea-brand-sub">Hệ thống Quản lý Thiết bị</div>
        </div>
        <span class="badge bg-info-subtle text-primary ms-auto fw-bold" style="font-size: 10px;">V2.4 PRO</span>
      </div>

      <nav class="ea-nav-list">
        <div class="ea-nav-label">PHÂN HỆ ĐIỀU HÀNH</div>
        <router-link v-if="can('UC09')" to="/admin" exact-active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-grid-1x2-fill me-2"></i> Tổng quan
        </router-link>
        <router-link v-if="can('UC12')" to="/admin/products" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-laptop me-2"></i> Sản phẩm
        </router-link>
        <router-link v-if="can('UC22')" to="/admin/categories" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-folder2-open me-2"></i> Danh mục & Hãng
        </router-link>
        <router-link v-if="can('UC14')" to="/admin/orders" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-bag-check me-2"></i> Đơn hàng
          <span v-if="pendingOrdersCount > 0" class="badge bg-danger ms-auto rounded-pill">{{ pendingOrdersCount }}</span>
        </router-link>
        <router-link v-if="can('UC23')" to="/admin/serial" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-upc-scan me-2 text-success"></i> Kho Serial/IMEI
        </router-link>
        <router-link v-if="can('UC13')" to="/admin/vouchers" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-ticket-perforated me-2"></i> Voucher
        </router-link>
        <router-link v-if="can('UC16')" to="/admin/reviews" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-chat-square-quote me-2"></i> Bình luận
        </router-link>

        <div v-if="can('UC15') || can('UC19') || can('UC10') || can('UC11')" class="ea-nav-label mt-2">KHÁCH HÀNG & NHÂN SỰ</div>
        <router-link v-if="can('UC15')" to="/admin/customers" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-person-lines-fill me-2"></i> Khách hàng
        </router-link>
        <router-link v-if="can('UC19')" to="/admin/warranty" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-shield-check me-2"></i> Bảo hành & Đổi trả
        </router-link>
        <router-link v-if="can('UC10')" to="/admin/staff" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-people-fill me-2"></i> Nhân viên
        </router-link>
        <router-link v-if="can('UC11')" to="/admin/permissions" active-class="active" class="ea-nav-item text-decoration-none">
          <i class="bi bi-shield-lock me-2"></i> Phân quyền
        </router-link>
        <router-link to="/" class="ea-nav-item text-decoration-none text-primary mt-2">
          <i class="bi bi-box-arrow-left me-2"></i> Về Website Khách hàng
        </router-link>
      </nav>

      <!-- Bottom Data Sync Widget -->
      <div class="ea-sync-widget mt-auto">
        <div class="d-flex justify-content-between small fw-bold text-dark mb-1">
          <span>KHO DỮ LIỆU</span>
          <span class="text-primary">100%</span>
        </div>
        <div class="progress mb-2" style="height: 6px;">
          <div class="progress-bar bg-primary" style="width: 100%"></div>
        </div>
        <div class="text-muted" style="font-size: 11px;">Đồng bộ thời gian thực: MySQL 8.0 API</div>
      </div>
    </aside>

    <!-- ========================================== -->
    <!-- MAIN WORKSPACE -->
    <!-- ========================================== -->
    <div class="ea-main-area">
      <!-- Topbar -->
      <header class="ea-topbar">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="ea-search-pill">
            <i class="bi bi-search text-muted me-2"></i>
            <input v-model="globalSearch" type="text" placeholder="Tìm nhanh trong trang quản trị..." />
          </div>
          <span class="ea-date-pill d-none d-md-inline-flex">
            <i class="bi bi-calendar3 me-2 text-primary"></i>Hôm nay: {{ todayStr }}
          </span>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary bg-white" @click="loadAllAdminData" :disabled="isLoading">
            <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin-anim': isLoading }"></i>
            {{ isLoading ? 'Đang tải...' : 'Làm mới' }}
          </button>
          <button v-if="can('UC14')" class="btn btn-sm btn-primary fw-semibold px-3" @click="switchTab('orders')">
            <i class="bi bi-bag-plus me-1"></i> Xử lý đơn hàng
          </button>
          <div class="ea-user-chip ms-2">
            <div class="ea-avatar">{{ adminInitial }}</div>
            <div class="d-none d-sm-block">
              <div class="fw-bold text-dark" style="font-size: 12.5px; line-height: 1.2;">{{ adminName }}</div>
              <div class="text-muted" style="font-size: 11px;">{{ roleDisplayName }}</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Workspace Content -->
      <main class="ea-content">
        <AdminOverviewTab
          v-if="activeTab === 'overview'"
          :admin-name="adminName"
          :kpi="kpiData"
          :orders="orders"
          :products="products"
          :categories="categories"
          @switch-tab="switchTab"
          @export-report="notify('Đã xuất báo cáo doanh thu định dạng CSV!')"
        />

        <AdminProductsTab
          v-else-if="activeTab === 'products'"
          :products="products"
          :categories="categories"
          :brands="brands"
          :initial-sub-tab="activeSubTab"
          @refresh="loadAllAdminData"
          @notify="notify"
        />

        <AdminOrdersTab
          v-else-if="activeTab === 'orders'"
          :orders="orders"
          @refresh="loadAllAdminData"
          @notify="notify"
        />

        <AdminWarrantyCustomersTab
          v-else-if="activeTab === 'warranty'"
          :customers="customers"
          :tickets="warrantyTickets"
          :initial-sub-tab="activeSubTab"
          @refresh="loadAllAdminData"
          @notify="notify"
        />

        <AdminVouchersReviewsTab
          v-else-if="activeTab === 'vouchers'"
          :vouchers="vouchers"
          :reviews="reviews"
          :initial-sub-tab="activeSubTab"
          @refresh="loadAllAdminData"
          @notify="notify"
        />

        <AdminStaffRbacTab
          v-else-if="activeTab === 'staff'"
          :staff-list="staffList"
          :initial-sub-tab="activeSubTab"
          @refresh="loadAllAdminData"
          @notify="notify"
        />

        <SerialWorkspace
          v-else-if="activeTab === 'serial'"
          :embedded="true"
        />
      </main>
    </div>

    <!-- Toast thông báo -->
    <div v-if="toastMsg" class="position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded-3 shadow-lg" style="z-index: 3000;">
      <i class="bi bi-check-circle-fill text-success me-2"></i>{{ toastMsg }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';
import { hasPermission } from '@/utils/permissions';
import AdminOverviewTab from './tabs/AdminOverviewTab.vue';
import AdminProductsTab from './tabs/AdminProductsTab.vue';
import AdminOrdersTab from './tabs/AdminOrdersTab.vue';
import AdminWarrantyCustomersTab from './tabs/AdminWarrantyCustomersTab.vue';
import AdminVouchersReviewsTab from './tabs/AdminVouchersReviewsTab.vue';
import AdminStaffRbacTab from './tabs/AdminStaffRbacTab.vue';
import SerialWorkspace from '../serial/index.vue';

export default {
  name: 'ElectroAdminPortal',
  components: {
    AdminOverviewTab,
    AdminProductsTab,
    AdminOrdersTab,
    AdminWarrantyCustomersTab,
    AdminVouchersReviewsTab,
    AdminStaffRbacTab,
    SerialWorkspace
  },
  data() {
    return {
      activeTab: 'overview',
      activeSubTab: '',
      globalSearch: '',
      isLoading: false,
      currentUser: null,
      adminName: 'Quản trị viên',
      todayStr: new Date().toLocaleDateString('vi-VN'),
      kpiData: {},
      orders: [],
      products: [],
      categories: [],
      brands: [],
      customers: [],
      staffList: [],
      vouchers: [],
      reviews: [],
      warrantyTickets: [],
      toastMsg: '',
      toastTimer: null,
      _pollTimer: null,
      _bc: null,
      _storageHandler: null,
      _visHandler: null
    };
  },
  computed: {
    adminInitial() {
      return (this.adminName || 'AD').substring(0, 2).toUpperCase();
    },
    pendingOrdersCount() {
      return this.orders.filter(o => o.trang_thai_don_hang === 'cho_xac_nhan').length;
    },
    roleDisplayName() {
      if (!this.currentUser) return 'Quản trị viên';
      return this.currentUser.vai_tro === 'admin' ? 'Quản trị viên cấp cao' : 'Nhân viên hệ thống';
    }
  },
  watch: {
    '$route': {
      immediate: true,
      handler(to) {
        if (to.meta && to.meta.adminTab) {
          this.activeTab = to.meta.adminTab;
          this.activeSubTab = to.meta.subTab || '';
        } else {
          this.activeTab = 'overview';
          this.activeSubTab = '';
        }
      }
    }
  },
  created() {
    this.resolveAdminUser();
    this.loadAllAdminData();
  },
  mounted() {
    this.initRealtimeSync();
  },
  beforeUnmount() {
    if (this._pollTimer) clearInterval(this._pollTimer);
    if (this.toastTimer) clearTimeout(this.toastTimer);
    if (this._bc) {
      try { this._bc.close(); } catch (e) {}
    }
    if (this._storageHandler) window.removeEventListener('storage', this._storageHandler);
    if (this._visHandler) document.removeEventListener('visibilitychange', this._visHandler);
  },
  methods: {
    initRealtimeSync() {
      try {
        if (typeof BroadcastChannel !== 'undefined') {
          this._bc = new BroadcastChannel('techmart_realtime');
          this._bc.onmessage = (ev) => {
            if (ev.data?.type) this.loadAllAdminData(true);
          };
        }
      } catch (e) {}

      this._storageHandler = (ev) => {
        if (ev.key === 'techmart_realtime_ping') this.loadAllAdminData(true);
      };
      window.addEventListener('storage', this._storageHandler);

      this._visHandler = () => {
        if (!document.hidden) this.loadAllAdminData(true);
      };
      document.addEventListener('visibilitychange', this._visHandler);

      this._pollTimer = setInterval(() => {
        if (!document.hidden) this.loadAllAdminData(true);
      }, 5000);
    },
    can(permCode) {
      return hasPermission(this.currentUser, permCode);
    },
    resolveAdminUser() {
      try {
        const u = localStorage.getItem('admin_user') || localStorage.getItem('customer_user') || localStorage.getItem('techmart_user');
        if (u) {
          const parsed = JSON.parse(u);
          this.currentUser = parsed;
          if (parsed && parsed.ho_ten) this.adminName = parsed.ho_ten;
        }
      } catch (e) {}
    },
    switchTab(tab) {
      const routeMap = {
        overview: '/admin',
        products: '/admin/products',
        orders: '/admin/orders',
        serial: '/admin/serial',
        vouchers: '/admin/vouchers',
        warranty: '/admin/warranty',
        staff: '/admin/staff'
      };
      this.$router.push(routeMap[tab] || '/admin');
    },
    async loadAllAdminData(silent = false) {
      if (!silent) this.isLoading = true;
      try {
        const [kpiRes, ordRes, prodRes, catRes, brandRes, custRes, staffRes, vouchRes, revRes, warRes] = await Promise.allSettled([
          axios.get(apiUrl('/admin/thong-ke/kpi-dashboard')),
          axios.get(apiUrl('/admin/don-hang/get-data')),
          axios.get(apiUrl('/admin/san-pham/get-data')),
          axios.get(apiUrl('/admin/danh-muc/get-data')),
          axios.get(apiUrl('/admin/thuong-hieu/get-data')),
          axios.get(apiUrl('/admin/khach-hang/get-data')),
          axios.get(apiUrl('/admin/nhan-vien/get-data')),
          axios.get(apiUrl('/admin/voucher/get-data')),
          axios.get(apiUrl('/admin/danh-gia/get-data')),
          axios.get(apiUrl('/admin/bao-hanh/get-data'))
        ]);

        if (kpiRes.status === 'fulfilled' && kpiRes.value.data?.status) this.kpiData = kpiRes.value.data.data || kpiRes.value.data;
        if (ordRes.status === 'fulfilled' && ordRes.value.data?.status) this.orders = ordRes.value.data.data || [];
        if (prodRes.status === 'fulfilled' && prodRes.value.data?.status) this.products = prodRes.value.data.data || [];
        if (catRes.status === 'fulfilled' && catRes.value.data?.status) this.categories = catRes.value.data.data || [];
        if (brandRes.status === 'fulfilled' && brandRes.value.data?.status) this.brands = brandRes.value.data.data || [];
        if (custRes.status === 'fulfilled' && custRes.value.data?.status) this.customers = custRes.value.data.data || [];
        if (staffRes.status === 'fulfilled' && staffRes.value.data?.status) {
          this.staffList = staffRes.value.data.data || [];
          if (this.currentUser) {
            const me = this.staffList.find(s => s.id === this.currentUser.id);
            if (me) {
              this.currentUser = { ...this.currentUser, vai_tro: me.vai_tro, quyen_han: me.quyen_han };
              if (localStorage.getItem('admin_user')) {
                localStorage.setItem('admin_user', JSON.stringify(this.currentUser));
              }
            }
          }
        }
        if (vouchRes.status === 'fulfilled' && vouchRes.value.data?.status) this.vouchers = vouchRes.value.data.data || [];
        if (revRes.status === 'fulfilled' && revRes.value.data?.status) this.reviews = revRes.value.data.data || [];
        if (warRes.status === 'fulfilled' && warRes.value.data?.status) this.warrantyTickets = warRes.value.data.data || [];
      } catch (e) {
        console.error('Lỗi tải dữ liệu Admin:', e);
      } finally {
        if (!silent) this.isLoading = false;
      }
    },
    notify(msg) {
      this.toastMsg = msg;
      clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => { this.toastMsg = ''; }, 3000);
    }
  }
};
</script>

<style scoped>
.ea-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f4f6fa;
  font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
}

.ea-sidebar {
  width: 256px;
  flex-shrink: 0;
  background: #ffffff;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  padding: 18px 14px;
  position: sticky;
  top: 0;
  height: 100vh;
}

.ea-brand-box {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 18px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 16px;
}

.ea-brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #0b5ed7;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.ea-brand-title {
  font-weight: 800;
  font-size: 15px;
  color: #0f172a;
  line-height: 1.1;
}

.ea-brand-sub {
  font-size: 11px;
  color: #64748b;
}

.ea-nav-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.ea-nav-label {
  font-size: 10.5px;
  font-weight: 700;
  color: #94a3b8;
  letter-spacing: 0.6px;
  padding: 6px 10px;
}

.ea-nav-item {
  display: flex;
  align-items: center;
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  transition: all 0.2s ease;
}

.ea-nav-item:hover {
  background: #f1f5f9;
  color: #0b5ed7;
}

.ea-nav-item.active {
  background: #eff6ff;
  color: #0b5ed7;
}

.ea-sync-widget {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px;
}

.ea-main-area {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.ea-topbar {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  position: sticky;
  top: 0;
  z-index: 900;
}

.ea-search-pill {
  display: flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 12px;
  width: 260px;
}

.ea-search-pill input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  width: 100%;
}

.ea-date-pill {
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 12px;
  font-size: 12.5px;
  font-weight: 600;
  color: #334155;
}

.ea-user-chip {
  display: flex;
  align-items: center;
  gap: 8px;
}

.ea-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #0b5ed7;
  color: #ffffff;
  font-weight: 700;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ea-content {
  padding: 24px;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
