<template>
  <div class="payos-return-page">
    <div class="return-card shadow-lg">
      <!-- Đang xác thực trực tiếp với máy chủ PayOS -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="fw-bold text-dark">Đang đối soát giao dịch trực tiếp với PayOS...</h5>
        <p class="text-muted small mb-0">Vui lòng không đóng trình duyệt trong giây lát.</p>
      </div>

      <!-- Kết quả sau khi đối soát -->
      <div v-else>
        <!-- Thành công -->
        <div v-if="payosStatus === 'PAID'" class="text-center">
          <div class="status-icon bg-success-subtle text-success mx-auto mb-3">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <span class="badge bg-success px-3 py-2 rounded-pill mb-2">THANH TOÁN VIETQR THÀNH CÔNG</span>
          <h3 class="fw-bold text-dark mt-2">Cảm ơn bạn đã đặt hàng tại TechMart AI!</h3>
          <p class="text-muted small">
            Hệ thống đã xác thực tự động khoản thanh toán qua cổng <strong>PayOS VietQR</strong> và cập nhật đơn hàng sang trạng thái <strong>Đã thanh toán</strong>.
          </p>
        </div>

        <!-- Đã hủy -->
        <div v-else-if="payosStatus === 'CANCELLED' || payosStatus === 'EXPIRED' || isCancelledParam" class="text-center">
          <div class="status-icon bg-danger-subtle text-danger mx-auto mb-3">
            <i class="bi bi-x-circle-fill"></i>
          </div>
          <span class="badge bg-danger px-3 py-2 rounded-pill mb-2">GIAO DỊCH ĐÃ HỦY</span>
          <h3 class="fw-bold text-dark mt-2">Thanh toán PayOS chưa hoàn tất</h3>
          <p class="text-muted small">
            Bạn đã hủy thanh toán hoặc mã QR đã hết hạn. Bạn có thể quay lại giỏ hàng để đặt lại đơn hoặc chọn phương thức COD.
          </p>
        </div>

        <!-- Chờ thanh toán / Lỗi khác -->
        <div v-else class="text-center">
          <div class="status-icon bg-warning-subtle text-warning mx-auto mb-3">
            <i class="bi bi-hourglass-split"></i>
          </div>
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2">ĐANG CHỜ XÁC NHẬN</span>
          <h3 class="fw-bold text-dark mt-2">{{ message || 'Giao dịch đang chờ xử lý' }}</h3>
          <p class="text-muted small">
            Nếu bạn vừa chuyển khoản xong, vui lòng bấm nút bên dưới để đồng bộ lại trạng thái từ máy chủ PayOS.
          </p>
        </div>

        <!-- Chi tiết hóa đơn -->
        <div v-if="orderData || paymentData" class="invoice-box mt-4 p-3 rounded border bg-light">
          <div class="invoice-row" v-if="orderData && orderData.ma_don_hang">
            <span class="text-muted">Mã đơn hàng:</span>
            <strong class="text-primary font-monospace">#{{ orderData.ma_don_hang }}</strong>
          </div>
          <div class="invoice-row" v-if="orderCode">
            <span class="text-muted">Mã giao dịch PayOS:</span>
            <strong class="font-monospace">PAYOS_{{ orderCode }}</strong>
          </div>
          <div class="invoice-row" v-if="orderData && orderData.ho_ten_nguoi_nhan">
            <span class="text-muted">Người nhận hàng:</span>
            <strong>{{ orderData.ho_ten_nguoi_nhan }} ({{ orderData.so_dien_thoai_nhan }})</strong>
          </div>
          <div class="invoice-row" v-if="paymentData && paymentData.so_tien">
            <span class="text-muted">Số tiền qua cổng PayOS:</span>
            <strong class="text-danger fs-6">{{ formatCurrency(paymentData.so_tien) }}</strong>
          </div>
          <div class="invoice-row" v-if="orderData && orderData.tong_thanh_toan">
            <span class="text-muted">Tổng giá trị đơn hàng:</span>
            <strong>{{ formatCurrency(orderData.tong_thanh_toan) }}</strong>
          </div>
          <div class="invoice-row">
            <span class="text-muted">Trạng thái PayOS:</span>
            <span :class="['badge', payosStatus === 'PAID' ? 'bg-success' : (payosStatus === 'CANCELLED' ? 'bg-danger' : 'bg-warning text-dark')]">
              {{ payosStatus || 'UNKNOWN' }}
            </span>
          </div>
        </div>

        <div v-if="errorMsg" class="alert alert-warning small mt-3 mb-0">
          <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ errorMsg }}
        </div>

        <!-- Các nút điều hướng -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
          <button
            v-if="payosStatus !== 'PAID' && orderCode"
            class="btn btn-outline-primary btn-sm px-3"
            @click="verifyOrder"
          >
            <i class="bi bi-arrow-clockwise me-1"></i> Kiểm tra lại với PayOS
          </button>
          <router-link to="/cart" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-cart3 me-1"></i> Về Giỏ Hàng
          </router-link>
          <router-link to="/" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-house-door-fill me-1"></i> Tiếp Tục Mua Sắm
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import apiUrl from '../../utils/api';

export default {
  name: 'PayOsReturn',
  data() {
    return {
      loading: true,
      orderCode: '',
      payosStatus: '',
      message: '',
      errorMsg: '',
      orderData: null,
      paymentData: null,
      isCancelledParam: false,
    };
  },
  mounted() {
    const q = this.$route.query || {};
    this.orderCode = String(q.orderCode || '');
    this.isCancelledParam = q.cancel === 'true' || q.status === 'cancel' || q.status === 'CANCELLED';

    if (this.orderCode) {
      this.verifyOrder();
    } else {
      this.loading = false;
      this.errorMsg = 'Không tìm thấy mã giao dịch orderCode trên đường dẫn trả về.';
    }
  },
  methods: {
    async verifyOrder() {
      this.loading = true;
      this.errorMsg = '';
      try {
        const res = await axios.get(apiUrl(`payment/payos/verify/${this.orderCode}`));
        if (res.data && res.data.status) {
          this.payosStatus = res.data.payos_status || 'PENDING';
          this.message     = res.data.message || '';
          this.orderData   = res.data.data?.order || null;
          this.paymentData = res.data.data?.payment || null;

          // Nếu đã thanh toán thành công -> xóa localStorage giỏ hàng tạm
          if (this.payosStatus === 'PAID') {
            localStorage.removeItem('techmart_cart');
            localStorage.setItem('techmart_cart_count', '0');
          }
        } else {
          this.errorMsg = (res.data && res.data.message) || 'Không thể xác thực giao dịch.';
        }
      } catch (err) {
        this.errorMsg = err?.response?.data?.message || 'Lỗi kết nối tới máy chủ Backend để xác thực PayOS.';
      } finally {
        this.loading = false;
      }
    },
    formatCurrency(val) {
      if (!val) return '0đ';
      return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
    },
  },
};
</script>

<style scoped>
.payos-return-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f1f5f9 0%, #e0f2fe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
}

.return-card {
  width: 100%;
  max-width: 560px;
  background: #ffffff;
  border-radius: 16px;
  padding: 32px 28px;
  border: 1px solid #e2e8f0;
}

.status-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
}

.invoice-box {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 13.5px;
}

.invoice-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}
</style>
