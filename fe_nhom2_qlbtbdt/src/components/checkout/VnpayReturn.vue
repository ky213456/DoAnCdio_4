<template>
  <div class="vnpay-return-page">
    <div class="return-card shadow-lg">
      <!-- Đang xác thực chữ ký HMAC-SHA512 từ VNPAY -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="fw-bold text-dark">Đang xác thực chữ ký giao dịch VNPAY Sandbox...</h5>
        <p class="text-muted small mb-0">Vui lòng không đóng trình duyệt trong giây lát.</p>
      </div>

      <!-- Kết quả -->
      <div v-else>
        <!-- Thành công -->
        <div v-if="isPaid" class="text-center">
          <div class="status-icon bg-success-subtle text-success mx-auto mb-3">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <span class="badge bg-success px-3 py-2 rounded-pill mb-2">THANH TOÁN VNPAY THÀNH CÔNG</span>
          <h3 class="fw-bold text-dark mt-2">Đơn hàng đã được thanh toán qua VNPAY!</h3>
          <p class="text-muted small">
            Hệ thống đã kiểm tra hợp lệ chữ ký bảo mật <strong>HMAC-SHA512</strong> và tự động cập nhật đơn hàng sang trạng thái <strong>Đã thanh toán</strong>.
          </p>
        </div>

        <!-- Thất bại / Hủy -->
        <div v-else class="text-center">
          <div class="status-icon bg-danger-subtle text-danger mx-auto mb-3">
            <i class="bi bi-x-circle-fill"></i>
          </div>
          <span class="badge bg-danger px-3 py-2 rounded-pill mb-2">GIAO DỊCH VNPAY KHÔNG THÀNH CÔNG</span>
          <h3 class="fw-bold text-dark mt-2">{{ message || 'Thanh toán VNPAY thất bại' }}</h3>
          <p class="text-muted small">
            Vui lòng kiểm tra lại số dư / thông tin thẻ test NCB hoặc quay lại giỏ hàng để thử lại.
          </p>
        </div>

        <!-- Chi tiết hóa đơn -->
        <div v-if="orderData || txData.txn_ref" class="invoice-box mt-4 p-3 rounded border bg-light">
          <div class="invoice-row" v-if="orderData && orderData.ma_don_hang">
            <span class="text-muted">Mã đơn hàng:</span>
            <strong class="text-primary font-monospace">#{{ orderData.ma_don_hang }}</strong>
          </div>
          <div class="invoice-row" v-if="txData.txn_ref">
            <span class="text-muted">Mã tham chiếu (vnp_TxnRef):</span>
            <strong class="font-monospace">{{ txData.txn_ref }}</strong>
          </div>
          <div class="invoice-row" v-if="txData.transaction_no">
            <span class="text-muted">Mã giao dịch VNPAY:</span>
            <strong class="font-monospace">{{ txData.transaction_no }}</strong>
          </div>
          <div class="invoice-row" v-if="txData.bank_code">
            <span class="text-muted">Ngân hàng thanh toán:</span>
            <span class="badge bg-primary-subtle text-primary">{{ txData.bank_code }} ({{ txData.card_type || 'ATM' }})</span>
          </div>
          <div class="invoice-row" v-if="txData.amount">
            <span class="text-muted">Số tiền thanh toán:</span>
            <strong class="text-danger fs-6">{{ formatCurrency(txData.amount) }}</strong>
          </div>
          <div class="invoice-row" v-if="responseCode">
            <span class="text-muted">Mã phản hồi VNPAY:</span>
            <span :class="['badge', isPaid ? 'bg-success' : 'bg-danger']">
              {{ responseCode }} — {{ isPaid ? 'Thành công' : 'Thất bại' }}
            </span>
          </div>
        </div>

        <div v-if="errorMsg" class="alert alert-warning small mt-3 mb-0">
          <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ errorMsg }}
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
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
  name: 'VnpayReturn',
  data() {
    return {
      loading: true,
      isPaid: false,
      responseCode: '',
      message: '',
      errorMsg: '',
      orderData: null,
      txData: {},
    };
  },
  mounted() {
    const q = this.$route.query || {};
    if (q.vnp_TxnRef) {
      this.verifyVnpay(q);
    } else {
      this.loading = false;
      this.errorMsg = 'Không tìm thấy tham số giao dịch VNPAY (vnp_TxnRef) trên URL.';
    }
  },
  methods: {
    async verifyVnpay(queryParams) {
      this.loading = true;
      this.errorMsg = '';
      try {
        const res = await axios.get(apiUrl('payment/vnpay/verify'), {
          params: queryParams,
        });
        if (res.data && res.data.status) {
          this.isPaid       = !!res.data.is_paid;
          this.responseCode = res.data.response_code || '';
          this.message      = res.data.message || '';
          this.txData       = res.data.data || {};
          this.orderData    = res.data.data?.order || null;

          if (this.isPaid) {
            localStorage.removeItem('techmart_cart');
            localStorage.setItem('techmart_cart_count', '0');
          }
        } else {
          this.isPaid   = false;
          this.errorMsg = (res.data && res.data.message) || 'Xác thực chữ ký VNPAY thất bại.';
        }
      } catch (err) {
        this.isPaid   = false;
        this.errorMsg = err?.response?.data?.message || 'Lỗi kết nối Backend để xác thực VNPAY.';
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
.vnpay-return-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #fee2e2 100%);
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
