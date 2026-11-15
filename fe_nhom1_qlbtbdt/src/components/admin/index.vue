<template>
  <div class="admin-layout">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-brand">
        <i class="bi bi-shield-lock-fill text-warning fs-4 me-2"></i>
        <div>
          <div class="brand-title">TECHMART ADMIN</div>
          <div class="brand-sub">Hệ Thống Quản Trị BI</div>
        </div>
      </div>

      <nav class="sidebar-menu">
        <div class="menu-label">TỔNG QUAN</div>
        <router-link to="/admin" class="menu-item active">
          <i class="bi bi-speedometer2 me-2"></i> Tổng Quan BI (UI_13)
        </router-link>

        <div class="menu-label">QUẢN TRỊ NGHIỆP VỤ</div>
        <a href="#" class="menu-item">
          <i class="bi bi-laptop me-2"></i> Quản Lý Sản Phẩm
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-folder-fill me-2"></i> Cây Danh Mục (UI_14)
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-upc-scan me-2"></i> Quản Lý Kho Serial FIFO
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-bag-check me-2"></i> Quản Lý Đơn Hàng
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-ticket-perforated me-2"></i> Quản Lý Voucher
        </a>

        <div class="menu-label">HỆ THỐNG</div>
        <a href="#" class="menu-item">
          <i class="bi bi-people-fill me-2"></i> Người Dùng & Phân Quyền
        </a>
        <a href="#" class="menu-item">
          <i class="bi bi-shield-check me-2"></i> Bảo Hành Điện Tử (UI_15)
        </a>
        <router-link to="/" class="menu-item text-warning mt-4">
          <i class="bi bi-box-arrow-left me-2"></i> Về Website Khách Hàng
        </router-link>
      </nav>
    </aside>

    <!-- Admin Main Content -->
    <main class="admin-main">
      <!-- Admin Topbar -->
      <header class="admin-topbar">
        <div class="topbar-search">
          <i class="bi bi-search text-muted me-2"></i>
          <input type="text" placeholder="Tìm kiếm nhanh đơn hàng, serial..." class="search-input" />
        </div>
        <div class="topbar-actions">
          <span class="badge bg-danger rounded-pill me-3"><i class="bi bi-bell-fill me-1"></i>3</span>
          <div class="admin-profile">
            <i class="bi bi-person-circle fs-4 me-2 text-primary"></i>
            <div>
              <div class="fw-bold small">Nguyễn Văn Kỳ</div>
              <div class="text-muted" style="font-size: 11px;">Quản Trị Viên (Lead)</div>
            </div>
          </div>
        </div>
      </header>

      <!-- Admin Page Content -->
      <div class="admin-content">
        <!-- Page Title & Quick Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="h4 fw-bold mb-1">BẢNG ĐIỀU KHIỂN DOANH THU & KPI (UI_13)</h2>
            <p class="text-muted small mb-0">Hệ thống phân tích dữ liệu bán hàng thời gian thực TechMart AI</p>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Xuất Báo Cáo BI</button>
            <button class="btn btn-outline-secondary btn-sm" @click="loadData()"><i class="bi bi-arrow-clockwise me-1"></i> Làm Mới</button>
          </div>
        </div>

        <!-- 4 KPI Cards -->
        <div class="row g-3 mb-4">
          <div v-for="(kpi, index) in kpis" :key="index" class="col-md-3">
            <div class="card border-0 shadow-sm p-3 kpi-card">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">{{ kpi.title }}</span>
                <i :class="['bi', kpi.icon, kpi.color, 'fs-4']"></i>
              </div>
              <div class="h4 fw-bold mb-1">{{ kpi.value }}</div>
              <div class="small text-success"><i class="bi bi-arrow-up-right me-1"></i>{{ kpi.change }} so với hôm qua</div>
            </div>
          </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card border-0 shadow-sm p-3 mb-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-table me-2 text-primary"></i>Đơn Hàng Gần Đây (Xử Lý Xuất Kho Serial)</h5>
            <span class="badge bg-primary">Realtime</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Mã Đơn</th>
                  <th>Khách Hàng</th>
                  <th>Sản Phẩm Đặt Mua</th>
                  <th>Tổng Tiền</th>
                  <th>Trạng Thái</th>
                  <th>Thời Gian</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ord in recentOrders" :key="ord.id">
                  <td class="fw-bold text-primary">{{ ord.id }}</td>
                  <td>{{ ord.customer }}</td>
                  <td>{{ ord.product }}</td>
                  <td class="fw-bold">{{ ord.total }}</td>
                  <td>
                    <span :class="['badge', ord.status.includes('PAID') ? 'bg-success' : 'bg-warning text-dark']">
                      {{ ord.status }}
                    </span>
                  </td>
                  <td class="small text-muted">{{ ord.date }}</td>
                  <td>
                    <button class="btn btn-outline-dark btn-sm py-0 px-2">Gán Serial FIFO</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import apiUrl from '../../utils/api'

export default {
  data() {
    return {
      kpis: [
        { title: 'Doanh Thu Hôm Nay', value: '148.500.000đ', icon: 'bi-currency-dollar', color: 'text-success', change: '+18.5%' },
        { title: 'Đơn Hàng Mới', value: '24 Đơn', icon: 'bi-cart-check', color: 'text-primary', change: '+12%' },
        { title: 'Khách Hàng Mới', value: '18 Khách', icon: 'bi-people', color: 'text-info', change: '+5.4%' },
        { title: 'Tồn Kho Cảnh Báo', value: '3 Máy', icon: 'bi-exclamation-triangle', color: 'text-danger', change: 'Cần nhập' }
      ],
      recentOrders: [
        { id: 'DH-1029', customer: 'Nguyễn Văn Kỳ', product: 'Laptop ASUS ROG Strix G16', total: '29.990.000đ', status: 'PAID (VNPAY)', date: '14/11/2026 14:20' },
        { id: 'DH-1028', customer: 'Võ Đình Huy', product: 'PC Gaming Intel Ultra 7', total: '39.900.000đ', status: 'PAID (VietQR)', date: '14/11/2026 13:45' },
        { id: 'DH-1027', customer: 'Lê Quốc Khánh', product: 'MacBook Pro 14 M3', total: '37.490.000đ', status: 'PENDING', date: '14/11/2026 11:10' }
      ]
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      // 1. Gọi API Lấy Thống kê KPI Dashboard
      axios
        .get("http://127.0.0.1:8000/api/admin/thong-ke/kpi-dashboard")
        .then((res) => {
          if (res.data.status && res.data.data) {
            const d = res.data.data;
            this.kpis[0].value = new Intl.NumberFormat('vi-VN').format(d.tong_doanh_thu || 0) + 'đ';
            this.kpis[1].value = (d.tong_don_hang || 0) + ' Đơn';
            this.kpis[2].value = (d.tong_khach_hang || 0) + ' Khách';
            this.kpis[3].value = (d.tong_san_pham || 0) + ' Sản Phẩm';
          }
        })
        .catch((err) => {
          console.warn("Chưa kết nối backend admin KPI:", err);
        });

      // 2. Gọi API Lấy Danh sách Đơn hàng
      axios
        .get("http://127.0.0.1:8000/api/admin/don-hang/get-data")
        .then((res) => {
          if (res.data.status && res.data.data && res.data.data.length > 0) {
            this.recentOrders = res.data.data.slice(0, 5).map(ord => ({
              id: ord.ma_don_hang || ('DH-' + ord.id),
              customer: ord.ten_khach_hang || ord.ho_ten_nguoi_nhan,
              product: 'Đơn hàng công nghệ #' + ord.id,
              total: new Intl.NumberFormat('vi-VN').format(ord.tong_thanh_toan) + 'đ',
              status: ord.trang_thai_thanh_toan === 'da_thanh_toan' ? 'PAID (' + (ord.phuong_thuc_thanh_toan || 'ONLINE').toUpperCase() + ')' : 'PENDING',
              date: ord.created_at || ord.ngay_dat
            }));
          }
        })
        .catch((err) => {
          console.warn("Chưa kết nối backend admin Đơn hàng:", err);
        });
    }
  }
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f1f5f9;
  font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
}

/* Sidebar */
.admin-sidebar {
  width: 260px;
  background-color: #0f172a;
  color: #cbd5e1;
  display: flex;
  flex-direction: column;
}

.sidebar-brand {
  padding: 20px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #1e293b;
}

.brand-title {
  font-size: 14px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.brand-sub {
  font-size: 11px;
  color: #64748b;
}

.sidebar-menu {
  padding: 16px 12px;
  flex: 1;
}

.menu-label {
  font-size: 10px;
  font-weight: 800;
  color: #64748b;
  letter-spacing: 0.5px;
  margin: 16px 0 6px 10px;
  text-transform: uppercase;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #94a3b8;
  text-decoration: none;
  margin-bottom: 4px;
  transition: all 0.15s;
}

.menu-item:hover,
.menu-item.active {
  background-color: #1e293b;
  color: #38bdf8;
}

/* Main Content */
.admin-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.admin-topbar {
  background: #ffffff;
  padding: 12px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
}

.search-input {
  border: 1px solid #cbd5e1;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  width: 320px;
}

.admin-profile {
  display: flex;
  align-items: center;
}

.admin-content {
  padding: 24px;
}

.kpi-card {
  border-radius: 10px;
  transition: transform 0.15s;
}

.kpi-card:hover {
  transform: translateY(-2px);
}
</style>
