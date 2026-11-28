<template>
  <div>
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
      <div>
        <div class="text-primary fw-bold small mb-1">TRUNG TÂM ĐIỀU PHỐI ĐƠN HÀNG & VẬN CHUYỂN</div>
        <h2 class="fw-bold text-dark mb-1">Quản lý Đơn hàng</h2>
        <p class="text-muted small mb-0">Theo dõi trạng thái đơn, kiểm tra thanh toán PayOS & COD và gán Serial/IMEI xuất kho.</p>
      </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">TỔNG ĐƠN HÀNG</div>
          <div class="fs-4 fw-bold text-dark">{{ orders.length }}</div>
          <div class="small text-primary">Tất cả đơn</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">CHỜ XÁC NHẬN</div>
          <div class="fs-4 fw-bold text-warning">{{ countStatus('cho_xac_nhan') }}</div>
          <div class="small text-muted">Cần duyệt ngay</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">ĐANG XỬ LÝ / GIAO</div>
          <div class="fs-4 fw-bold text-info">{{ countStatus('da_xac_nhan') + countStatus('dang_dong_goi') + countStatus('dang_giao') }}</div>
          <div class="small text-muted">Đang đóng gói & vận chuyển</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="ea-card p-3">
          <div class="ea-kpi-label mb-1">HOÀN THÀNH / HỦY</div>
          <div class="fs-4 fw-bold text-success">{{ countStatus('giao_thanh_cong') + countStatus('da_huy') }}</div>
          <div class="small text-muted">{{ countStatus('da_huy') }} đơn đã hủy</div>
        </div>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="ea-card p-2 mb-4 d-flex flex-wrap gap-2">
      <button
        v-for="t in statusTabs"
        :key="t.key"
        class="btn btn-sm fw-semibold rounded-3"
        :class="currentTab === t.key ? 'btn-primary' : 'btn-light text-secondary'"
        @click="currentTab = t.key"
      >
        {{ t.label }} ({{ t.key === 'all' ? orders.length : countStatus(t.key) }})
      </button>
    </div>

    <!-- Master-Detail Split View -->
    <div class="row g-4">
      <!-- Left: Orders Table -->
      <div class="col-lg-7">
        <div class="ea-card p-3 h-100">
          <div class="input-group input-group-sm mb-3">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input v-model="searchOrder" class="form-control shadow-none" placeholder="Tìm mã #DH, tên khách hàng, SĐT..." />
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle small mb-0">
              <thead class="table-light text-muted">
                <tr>
                  <th>MÃ ĐƠN & NGÀY ĐẶT</th>
                  <th>KHÁCH HÀNG</th>
                  <th>TỔNG THANH TOÁN</th>
                  <th>TRẠNG THÁI</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="o in filteredOrders"
                  :key="o.id"
                  class="cursor-pointer"
                  :class="{ 'table-primary': selectedOrder && selectedOrder.id === o.id }"
                  @click="selectedOrder = o"
                >
                  <td>
                    <div class="fw-bold text-primary">#{{ o.ma_don_hang || o.id }}</div>
                    <div class="text-muted" style="font-size: 11px;">{{ formatDate(o.ngay_dat || o.created_at) }}</div>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ o.ho_ten_nguoi_nhan }}</div>
                    <div class="text-muted" style="font-size: 11px;">{{ o.so_dien_thoai_nhan }}</div>
                  </td>
                  <td>
                    <div class="fw-bold text-dark">{{ formatCurrency(o.tong_thanh_toan) }}</div>
                    <span class="badge bg-light text-dark border" style="font-size: 10px;">{{ o.phuong_thuc_thanh_toan }}</span>
                  </td>
                  <td>
                    <span class="badge rounded-pill" :class="statusBadgeClass(o.trang_thai_don_hang)">
                      {{ statusText(o.trang_thai_don_hang) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right: Order Detail Inspector -->
      <div class="col-lg-5">
        <div class="ea-card p-4 sticky-top" style="top: 86px;" v-if="selectedOrder">
          <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
            <div>
              <h5 class="fw-bold text-primary mb-1">#{{ selectedOrder.ma_don_hang || selectedOrder.id }}</h5>
              <div class="small text-muted">Khởi tạo: {{ formatDate(selectedOrder.ngay_dat || selectedOrder.created_at) }}</div>
            </div>
            <span class="badge rounded-pill px-3 py-2" :class="statusBadgeClass(selectedOrder.trang_thai_don_hang)">
              {{ statusText(selectedOrder.trang_thai_don_hang) }}
            </span>
          </div>

          <div class="p-3 bg-light rounded-3 mb-3 small">
            <div class="fw-bold text-dark mb-1"><i class="bi bi-person-fill text-primary me-1"></i>{{ selectedOrder.ho_ten_nguoi_nhan }}</div>
            <div class="text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ selectedOrder.so_dien_thoai_nhan }}</div>
            <div class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ selectedOrder.dia_chi_giao_hang }}</div>
          </div>

          <div class="mb-3">
            <div class="small fw-bold text-muted mb-2">DANH SÁCH THIẾT BỊ TRONG ĐƠN:</div>
            <div v-for="it in (selectedOrder.items || [])" :key="it.id" class="p-2 border rounded-3 mb-2 small bg-white">
              <div class="d-flex justify-content-between">
                <strong class="text-dark">{{ it.ten_san_pham }}</strong>
                <span class="fw-bold text-primary">x{{ it.so_luong }}</span>
              </div>
              <div class="text-muted" style="font-size: 11px;">{{ it.ten_bien_the }}</div>
              <div v-if="it.danh_sach_serial && it.danh_sach_serial.length" class="mt-1">
                <span v-for="sn in it.danh_sach_serial" :key="sn" class="badge bg-success-subtle text-success me-1">SN: {{ sn }}</span>
              </div>
            </div>
          </div>

          <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark">Tổng cộng thanh toán:</span>
            <span class="fs-5 fw-bold text-danger">{{ formatCurrency(selectedOrder.tong_thanh_toan) }}</span>
          </div>

          <!-- Đổi trạng thái đơn hàng -->
          <div class="mb-2">
            <label class="form-label small fw-bold text-dark">Cập nhật trạng thái xử lý:</label>
            <div class="d-flex gap-2">
              <select v-model="nextStatus" class="form-select form-select-sm">
                <option value="cho_xac_nhan">Chờ xác nhận</option>
                <option value="da_xac_nhan">Đã xác nhận</option>
                <option value="dang_dong_goi">Đang đóng gói</option>
                <option value="dang_giao">Đang giao hàng</option>
                <option value="giao_thanh_cong">Giao thành công</option>
                <option value="da_huy">Hủy đơn hàng</option>
              </select>
              <button class="btn btn-primary btn-sm px-3 fw-bold text-nowrap" @click="updateOrderStatus">
                Cập nhật
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';

export default {
  name: 'AdminOrdersTab',
  props: {
    orders: { type: Array, default: () => [] }
  },
  data() {
    return {
      currentTab: 'all',
      searchOrder: '',
      selectedOrder: null,
      nextStatus: 'da_xac_nhan',
      statusTabs: [
        { key: 'all', label: 'Tất cả đơn' },
        { key: 'cho_xac_nhan', label: 'Chờ xác nhận' },
        { key: 'da_xac_nhan', label: 'Đã xác nhận' },
        { key: 'dang_dong_goi', label: 'Đang đóng gói' },
        { key: 'dang_giao', label: 'Đang giao' },
        { key: 'giao_thanh_cong', label: 'Hoàn thành' },
        { key: 'da_huy', label: 'Đã hủy' }
      ]
    };
  },
  watch: {
    orders: {
      immediate: true,
      handler(val) {
        if (!this.selectedOrder && val.length > 0) {
          this.selectedOrder = val[0];
          this.nextStatus = val[0].trang_thai_don_hang;
        }
      }
    },
    selectedOrder(val) {
      if (val) this.nextStatus = val.trang_thai_don_hang;
    }
  },
  computed: {
    filteredOrders() {
      return this.orders.filter(o => {
        if (this.currentTab !== 'all' && o.trang_thai_don_hang !== this.currentTab) return false;
        if (this.searchOrder) {
          const q = this.searchOrder.toLowerCase();
          return (o.ma_don_hang || '').toLowerCase().includes(q) ||
                 (o.ho_ten_nguoi_nhan || '').toLowerCase().includes(q) ||
                 (o.so_dien_thoai_nhan || '').includes(q);
        }
        return true;
      });
    }
  },
  methods: {
    countStatus(st) {
      return this.orders.filter(o => o.trang_thai_don_hang === st).length;
    },
    statusText(st) {
      const m = {
        cho_xac_nhan: 'Chờ xác nhận',
        da_xac_nhan: 'Đã xác nhận',
        dang_dong_goi: 'Đang đóng gói',
        dang_giao: 'Đang giao',
        giao_thanh_cong: 'Giao thành công',
        da_huy: 'Đã hủy'
      };
      return m[st] || st;
    },
    statusBadgeClass(st) {
      if (st === 'cho_xac_nhan') return 'bg-warning-subtle text-warning-emphasis';
      if (st === 'giao_thanh_cong') return 'bg-success-subtle text-success';
      if (st === 'da_huy') return 'bg-danger-subtle text-danger';
      return 'bg-primary-subtle text-primary';
    },
    async updateOrderStatus() {
      if (!this.selectedOrder) return;
      const res = await axios.post(apiUrl('/admin/don-hang/change-status'), {
        id: this.selectedOrder.id,
        trang_thai_don_hang: this.nextStatus
      });
      if (res.data?.status) {
        this.selectedOrder.trang_thai_don_hang = this.nextStatus;
        try {
          if (typeof BroadcastChannel !== 'undefined') {
            const bc = new BroadcastChannel('techmart_realtime');
            bc.postMessage({
              type: 'ORDER_STATUS_UPDATED',
              orderId: this.selectedOrder.id,
              maDonHang: this.selectedOrder.ma_don_hang,
              status: this.nextStatus,
              ts: Date.now()
            });
            bc.close();
          }
          localStorage.setItem('techmart_realtime_ping', JSON.stringify({
            type: 'ORDER_STATUS_UPDATED',
            orderId: this.selectedOrder.id,
            status: this.nextStatus,
            ts: Date.now()
          }));
        } catch (e) { /* ignore storage/channel errors */ }
        this.$emit('notify', `Đã cập nhật trạng thái đơn #${this.selectedOrder.ma_don_hang}`);
        this.$emit('refresh');
      }
    },
    formatCurrency(val) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
    },
    formatDate(d) {
      if (!d) return '';
      return new Date(d).toLocaleDateString('vi-VN');
    }
  }
};
</script>

<style scoped>
.ea-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
}
.ea-kpi-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748b;
}
.cursor-pointer { cursor: pointer; }
</style>
