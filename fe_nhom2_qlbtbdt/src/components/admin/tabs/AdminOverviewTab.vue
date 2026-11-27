<template>
  <div>
    <!-- Welcome & Time Filter -->
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">
          <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> HỆ THỐNG TRỰC TUYẾN • TECHMART AI
        </div>
        <h2 class="fw-bold text-dark mb-1">Xin chào, {{ adminName }}!</h2>
        <p class="text-muted small mb-0">Dưới đây là diễn biến kinh doanh thiết bị công nghệ và tồn kho thực tế từ MySQL.</p>
      </div>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="btn-group bg-white p-1 rounded-3 border shadow-sm">
          <button
            v-for="p in ['Hôm nay', '7 ngày qua', 'Tháng này']"
            :key="p"
            class="btn btn-sm rounded-2 fw-semibold"
            :class="timeRange === p ? 'btn-primary' : 'btn-light text-secondary'"
            @click="timeRange = p"
          >{{ p }}</button>
        </div>
        <button class="btn btn-sm btn-outline-secondary bg-white" @click="$emit('export-report')">
          <i class="bi bi-download me-1"></i> Xuất báo cáo
        </button>
      </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="ea-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="ea-kpi-label">TỔNG DOANH THU</span>
            <span class="ea-icon-box bg-primary-subtle text-primary"><i class="bi bi-currency-dollar"></i></span>
          </div>
          <div class="fs-4 fw-bold text-dark mb-2">{{ formatCurrency(kpi.tong_doanh_thu || 0) }}</div>
          <div class="small text-success fw-semibold">
            <i class="bi bi-graph-up-arrow me-1"></i>+14.8% <span class="text-muted fw-normal">so với kỳ trước</span>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="ea-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="ea-kpi-label">ĐƠN HÀNG MỚI</span>
            <span class="ea-icon-box bg-info-subtle text-info"><i class="bi bi-cart-check"></i></span>
          </div>
          <div class="fs-4 fw-bold text-dark mb-2">{{ orders.length }} đơn</div>
          <div class="d-flex align-items-center justify-content-between small">
            <span class="text-success fw-semibold">+8.2%</span>
            <span class="badge bg-danger-subtle text-danger rounded-pill px-2">
              {{ pendingOrdersCount }} chờ duyệt
            </span>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="ea-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="ea-kpi-label">THIẾT BỊ ĐANG BÁN</span>
            <span class="ea-icon-box bg-primary-subtle text-primary"><i class="bi bi-laptop"></i></span>
          </div>
          <div class="fs-4 fw-bold text-dark mb-2">{{ products.length }} sản phẩm</div>
          <div class="small text-muted text-truncate">
            Tổng tồn kho: <strong class="text-dark">{{ totalStock }}</strong> chiếc
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="ea-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="ea-kpi-label">TỶ LỆ TỒN KHO AN TOÀN</span>
            <span class="ea-icon-box bg-warning-subtle text-warning"><i class="bi bi-shield-check"></i></span>
          </div>
          <div class="fs-4 fw-bold text-dark mb-2">{{ safeStockRate }}%</div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-primary" :style="{ width: safeStockRate + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
      <div class="col-lg-8">
        <div class="ea-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h6 class="fw-bold text-dark mb-0">Diễn biến Doanh thu & Lợi nhuận (7 Ngày qua)</h6>
              <small class="text-muted">Tổng thu: {{ formatCurrency(kpi.tong_doanh_thu || 0) }} • Lợi nhuận ước đạt ~24%</small>
            </div>
            <div class="d-flex gap-3 small">
              <span><i class="bi bi-square-fill text-primary me-1"></i>Doanh thu</span>
              <span><i class="bi bi-square-fill text-info me-1"></i>Lợi nhuận</span>
            </div>
          </div>
          <div class="d-flex align-items-end justify-content-between gap-2 pt-3" style="height: 190px;">
            <div v-for="(d, idx) in weeklyBars" :key="idx" class="d-flex flex-column align-items-center flex-grow-1 h-100 justify-content-end">
              <div class="d-flex align-items-end gap-1 w-100 justify-content-center" style="height: 150px;">
                <div class="bg-primary rounded-top" :style="{ width: '18px', height: d.rev + '%' }" :title="'Doanh thu: ' + d.label"></div>
                <div class="bg-info rounded-top opacity-75" :style="{ width: '14px', height: d.prof + '%' }" :title="'Lợi nhuận: ' + d.label"></div>
              </div>
              <div class="small text-muted mt-2" style="font-size: 11.5px;">{{ d.label }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="ea-card p-4 h-100">
          <h6 class="fw-bold text-dark mb-1">Cơ cấu Doanh thu</h6>
          <p class="small text-muted mb-3">Phân bổ sản phẩm theo danh mục thiết bị</p>
          <div class="d-flex justify-content-center my-3">
            <div class="donut-ring d-flex align-items-center justify-content-center">
              <div class="donut-inner text-center">
                <div class="fw-bold fs-5 text-dark">100%</div>
                <div class="text-muted" style="font-size: 11px;">Danh mục</div>
              </div>
            </div>
          </div>
          <div class="d-flex flex-column gap-2 small mt-3">
            <div v-for="(cat, idx) in categoryBreakdown" :key="idx" class="d-flex justify-content-between">
              <span><i class="bi bi-circle-fill me-2" :style="{ color: cat.color }"></i>{{ cat.name }}</span>
              <strong class="text-dark">{{ cat.pct }}%</strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Urgent Orders + Top Products + Low Stock -->
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="ea-card p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
              <h6 class="fw-bold text-dark mb-0">Đơn hàng cần xử lý gấp</h6>
              <span class="badge bg-danger-subtle text-danger rounded-pill">{{ urgentOrders.length }} đơn ưu tiên</span>
            </div>
            <button class="btn btn-sm btn-link text-decoration-none fw-semibold" @click="$emit('switch-tab', 'orders')">
              Tất cả đơn hàng <i class="bi bi-chevron-right"></i>
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
              <thead class="table-light text-muted">
                <tr>
                  <th>MÃ ĐƠN</th>
                  <th>KHÁCH HÀNG</th>
                  <th>TỔNG TIỀN</th>
                  <th>THANH TOÁN</th>
                  <th>TRẠNG THÁI</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="o in urgentOrders" :key="o.id" class="cursor-pointer" @click="$emit('switch-tab', 'orders')">
                  <td class="fw-bold text-primary">#{{ o.ma_don_hang || o.id }}</td>
                  <td>
                    <div class="fw-semibold text-dark">{{ o.ho_ten_nguoi_nhan }}</div>
                    <div class="text-muted" style="font-size: 11px;">{{ o.so_dien_thoai_nhan }}</div>
                  </td>
                  <td class="fw-bold text-dark">{{ formatCurrency(o.tong_thanh_toan) }}</td>
                  <td>
                    <span class="badge bg-light text-dark border text-uppercase">{{ o.phuong_thuc_thanh_toan || 'COD' }}</span>
                  </td>
                  <td>
                    <span class="badge rounded-pill" :class="o.trang_thai_don_hang === 'cho_xac_nhan' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info-emphasis'">
                      {{ o.trang_thai_don_hang === 'cho_xac_nhan' ? 'Chờ xác nhận' : 'Đang xử lý' }}
                    </span>
                  </td>
                </tr>
                <tr v-if="urgentOrders.length === 0">
                  <td colspan="5" class="text-center text-muted py-4">Không có đơn hàng nào đang chờ xử lý.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4 d-flex flex-column gap-3">
        <!-- Top Thiết bị Bán chạy -->
        <div class="ea-card p-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Top Thiết bị Nổi bật</h6>
            <span class="small text-muted">Lượt xem</span>
          </div>
          <div v-for="(sp, idx) in topProducts" :key="sp.id" class="d-flex align-items-center justify-content-between py-2 border-bottom small">
            <div class="d-flex align-items-center gap-2 text-truncate pe-2">
              <span class="fw-bold text-muted" style="width: 18px;">0{{ idx + 1 }}</span>
              <div class="text-truncate">
                <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;">{{ sp.ten_san_pham }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ sp.luot_xem || 0 }} lượt xem</div>
              </div>
            </div>
            <strong class="text-primary">{{ formatCurrency(sp.gia_khuyen_mai || sp.gia_goc) }}</strong>
          </div>
        </div>

        <!-- Cảnh báo Tồn kho Thấp -->
        <div class="ea-card p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-bell-fill text-danger me-1"></i>Cảnh báo Tồn kho</h6>
            <span class="badge bg-danger-subtle text-danger">{{ lowStockProducts.length }} SKU</span>
          </div>
          <div v-for="lp in lowStockProducts.slice(0, 3)" :key="lp.id" class="p-2 bg-light rounded-3 mb-2 small d-flex justify-content-between align-items-center">
            <div class="text-truncate pe-2">
              <div class="fw-semibold text-dark text-truncate" style="max-width: 190px;">{{ lp.ten_san_pham }}</div>
              <div class="text-danger" style="font-size: 11px;">Còn {{ getStock(lp) }} chiếc • Ngưỡng an toàn: 10</div>
            </div>
            <button class="btn btn-sm btn-outline-primary py-0 px-2" @click="$emit('switch-tab', 'serial')">Nhập kho</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminOverviewTab',
  props: {
    adminName: { type: String, default: 'Quản trị viên' },
    kpi: { type: Object, default: () => ({}) },
    orders: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] }
  },
  data() {
    return {
      timeRange: 'Tháng này',
      weeklyBars: [
        { label: 'Thứ 2', rev: 55, prof: 28 },
        { label: 'Thứ 3', rev: 70, prof: 36 },
        { label: 'Thứ 4', rev: 48, prof: 24 },
        { label: 'Thứ 5', rev: 78, prof: 42 },
        { label: 'Thứ 6', rev: 85, prof: 46 },
        { label: 'Thứ 7', rev: 95, prof: 52 },
        { label: 'CN (Nay)', rev: 42, prof: 22 }
      ]
    };
  },
  computed: {
    pendingOrdersCount() {
      return this.orders.filter(o => o.trang_thai_don_hang === 'cho_xac_nhan').length;
    },
    urgentOrders() {
      return this.orders.filter(o => ['cho_xac_nhan', 'da_xac_nhan', 'dang_xu_ly'].includes(o.trang_thai_don_hang)).slice(0, 5);
    },
    totalStock() {
      return this.products.reduce((sum, p) => sum + this.getStock(p), 0);
    },
    lowStockProducts() {
      return this.products.filter(p => this.getStock(p) < 10);
    },
    safeStockRate() {
      if (!this.products.length) return 100;
      const safe = this.products.length - this.lowStockProducts.length;
      return Math.round((safe / this.products.length) * 100);
    },
    topProducts() {
      return [...this.products].sort((a, b) => (b.luot_xem || 0) - (a.luot_xem || 0)).slice(0, 4);
    },
    categoryBreakdown() {
      const colors = ['#0b5ed7', '#0dcaf0', '#2563eb', '#6366f1'];
      const total = Math.max(this.products.length, 1);
      return this.categories.slice(0, 4).map((c, idx) => {
        const count = this.products.filter(p => p.danh_muc_id === c.id).length;
        return {
          name: c.ten_danh_muc,
          pct: Math.round((count / total) * 100),
          color: colors[idx % colors.length]
        };
      });
    }
  },
  methods: {
    getStock(p) {
      if (Array.isArray(p.variants) && p.variants.length > 0) {
        return p.variants.reduce((s, v) => s + Number(v.so_luong_ton || 0), 0);
      }
      return Number(p.so_luong_ton || 0);
    },
    formatCurrency(val) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
    }
  }
};
</script>

<style scoped>
.ea-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
}
.ea-kpi-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.4px;
}
.ea-icon-box {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}
.donut-ring {
  width: 132px;
  height: 132px;
  border-radius: 50%;
  background: conic-gradient(#0b5ed7 0% 42%, #0dcaf0 42% 77%, #2563eb 77% 92%, #6366f1 92% 100%);
}
.donut-inner {
  width: 86px;
  height: 86px;
  border-radius: 50%;
  background: #ffffff;
}
.cursor-pointer {
  cursor: pointer;
}
</style>
