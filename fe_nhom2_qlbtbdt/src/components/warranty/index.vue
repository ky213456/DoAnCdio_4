<template>
  <div class="warranty-page-wrapper">
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <header class="warranty-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">BẢO HÀNH ĐIỆN TỬ</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">Tra cứu bảo hành (Serial/IMEI)</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/my-orders" class="btn-top-action me-2">
            <i class="bi bi-bag-check me-1"></i> Đơn hàng
          </router-link>
          <router-link to="/cart" class="btn-top-action me-2">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <div class="user-info-pill" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </div>
          <router-link to="/login?redirect=/warranty" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <main class="main-width warranty-main-content">
      <!-- HERO SEARCH CARD -->
      <div class="warranty-hero-card card border-0 shadow-sm p-4 p-md-5 text-center mb-4">
        <div class="hero-shield-badge mx-auto mb-3">
          <i class="bi bi-shield-check fs-1"></i>
        </div>
        <h1 class="page-title mb-2">TRA CỨU BẢO HÀNH ĐIỆN TỬ TECHMART AI</h1>
        <p class="text-muted mx-auto mb-4" style="max-width: 620px;">
          Nhập mã <strong>Serial / IMEI</strong> in trên vỏ hộp, tem máy hoặc trong chi tiết đơn hàng để kiểm tra thời hạn bảo hành chính hãng 24 tháng.
        </p>

        <form @submit.prevent="handleLookup" class="mx-auto w-100" style="max-width: 640px;">
          <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 ps-3">
              <i class="bi bi-upc-scan text-primary fs-4"></i>
            </span>
            <input
              type="text"
              v-model="serialQuery"
              class="form-control border-0 shadow-none"
              placeholder="Nhập mã Serial hoặc IMEI (VD: LG-27GR75Q-003)..."
            />
            <button type="submit" class="btn btn-primary px-4 fw-bold" :disabled="isLoading">
              <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-search me-1"></i> Tra cứu ngay
            </button>
          </div>
        </form>

        <!-- MÃ MẪU ĐỂ THỬ NHANH -->
        <div class="mt-3 d-flex flex-wrap justify-content-center align-items-center gap-2 small">
          <span class="text-muted">Thử nhanh với mã mẫu:</span>
          <button
            v-for="sample in ['LG-27GR75Q-003', 'LG-27GR75Q-001']"
            :key="sample"
            type="button"
            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
            @click="serialQuery = sample; handleLookup();"
          >
            {{ sample }}
          </button>
        </div>
      </div>

      <!-- THÔNG BÁO LỖI KHÔNG TÌM THẤY -->
      <div v-if="errorMessage" class="alert alert-danger border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i>
        <div>
          <strong class="d-block">Không tìm thấy thông tin bảo hành</strong>
          <span class="small">{{ errorMessage }}</span>
        </div>
      </div>

      <!-- KẾT QUẢ BẢO HÀNH ĐIỆN TỬ -->
      <div v-if="warrantyResult" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="p-4 text-white d-flex flex-wrap justify-content-between align-items-center gap-2" :class="statusHeaderClass">
          <div class="d-flex align-items-center gap-3">
            <i class="bi bi-patch-check-fill fs-2"></i>
            <div>
              <div class="small opacity-75 text-uppercase fw-semibold">Phiếu bảo hành điện tử chính hãng</div>
              <h4 class="mb-0 fw-bold">{{ warrantyResult.ten_san_pham }}</h4>
            </div>
          </div>
          <span class="badge bg-white text-dark fs-6 px-3 py-2 rounded-pill fw-bold">
            {{ statusLabel }}
          </span>
        </div>

        <div class="p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                  <i class="bi bi-cpu text-primary me-1"></i> Thông tin thiết bị
                </h6>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Phiên bản / Biến thể</span>
                  <strong class="text-dark small">{{ warrantyResult.ten_bien_the || 'Tiêu chuẩn' }}</strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Số Serial (S/N)</span>
                  <strong class="text-primary small">{{ warrantyResult.so_serial || '-' }}</strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Mã IMEI</span>
                  <strong class="text-dark small">{{ warrantyResult.imei || 'Không áp dụng' }}</strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2">
                  <span class="text-muted small">Mã đơn hàng liên kết</span>
                  <strong class="text-dark small">{{ warrantyResult.ma_don_hang ? '#' + warrantyResult.ma_don_hang : 'Chưa xuất bán (Trong kho)' }}</strong>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 h-100">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                  <i class="bi bi-calendar-check text-success me-1"></i> Thời hạn & Chính sách bảo hành
                </h6>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Thời gian bảo hành chuẩn</span>
                  <strong class="text-dark small">{{ warrantyResult.thoi_gian_bao_hanh || 24 }} tháng chính hãng</strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Ngày kích hoạt / Xuất kho</span>
                  <strong class="text-dark small">{{ formatDate(warrantyResult.ngay_xuat_kho) }}</strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2 border-bottom">
                  <span class="text-muted small">Ngày hết hạn bảo hành</span>
                  <strong class="small" :class="isExpired ? 'text-danger' : 'text-success'">
                    {{ warrantyResult.han_bao_hanh ? formatDate(warrantyResult.han_bao_hanh) : 'Chưa kích hoạt (Tính từ ngày mua)' }}
                  </strong>
                </div>
                <div class="info-row d-flex justify-content-between py-2">
                  <span class="text-muted small">Trung tâm tiếp nhận</span>
                  <strong class="text-dark small">TechMart Care - 1900.6789</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';

export default {
  name: 'WarrantyLookupView',
  data() {
    return {
      serialQuery: '',
      warrantyResult: null,
      errorMessage: '',
      isLoading: false,
      currentUser: null
    };
  },
  computed: {
    isExpired() {
      if (!this.warrantyResult || !this.warrantyResult.han_bao_hanh) return false;
      return new Date(this.warrantyResult.han_bao_hanh) < new Date();
    },
    statusLabel() {
      if (!this.warrantyResult) return '';
      if (this.warrantyResult.trang_thai_may === 'trong_kho') return 'Chưa kích hoạt (Trong kho)';
      if (this.isExpired) return 'Đã hết hạn bảo hành';
      return 'Đang còn bảo hành';
    },
    statusHeaderClass() {
      if (!this.warrantyResult) return 'bg-primary';
      if (this.warrantyResult.trang_thai_may === 'trong_kho') return 'bg-info text-dark';
      if (this.isExpired) return 'bg-danger';
      return 'bg-success';
    }
  },
  created() {
    this.checkCurrentUser();
    if (this.$route.query.serial) {
      this.serialQuery = String(this.$route.query.serial);
      this.handleLookup();
    }
  },
  methods: {
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('techmart_user') || localStorage.getItem('khach_hang_info');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {}
    },
    async handleLookup() {
      const code = (this.serialQuery || '').trim();
      if (!code) {
        this.errorMessage = 'Vui lòng nhập số Serial hoặc mã IMEI để tra cứu!';
        this.warrantyResult = null;
        return;
      }

      this.isLoading = true;
      this.errorMessage = '';
      this.warrantyResult = null;

      try {
        const res = await axios.get(apiUrl('/client/tra-cuu-bao-hanh'), {
          params: { serial: code }
        });
        if (res.data && res.data.status) {
          this.warrantyResult = res.data.data;
        } else {
          this.errorMessage = res.data?.message || 'Không tìm thấy thông tin bảo hành cho mã này.';
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Không tìm thấy thông tin bảo hành cho Serial/IMEI này.';
      } finally {
        this.isLoading = false;
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return 'Chưa xuất kho';
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    }
  }
};
</script>

<style scoped>
.warranty-page-wrapper {
  background-color: #f4f7fb;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.warranty-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 14px 0;
  margin-bottom: 28px;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.header-left {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.brand-logo {
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
  font-size: 20px;
  color: #0f172a;
}

.logo-icon {
  background: linear-gradient(135deg, #0284c7, #2563eb);
  color: #fff;
  border-radius: 8px;
  width: 34px;
  height: 34px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.badge-sub {
  font-size: 11px;
  background: #e0f2fe;
  color: #0369a1;
  padding: 3px 9px;
  border-radius: 6px;
  font-weight: 700;
}

.breadcrumb-trail {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  padding-left: 12px;
  border-left: 1px solid #e2e8f0;
}

.breadcrumb-item-link {
  color: #64748b;
  text-decoration: none;
}

.breadcrumb-current {
  color: #0f172a;
  font-weight: 600;
}

.btn-top-action {
  display: inline-flex;
  align-items: center;
  font-size: 13.5px;
  color: #334155;
  text-decoration: none;
  font-weight: 600;
  padding: 7px 14px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
}

.user-info-pill {
  display: inline-flex;
  align-items: center;
  background: #f1f5f9;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 600;
  color: #0f172a;
  border: 1px solid #e2e8f0;
}

.btn-login-link {
  display: inline-flex;
  align-items: center;
  font-size: 13.5px;
  font-weight: 600;
  color: #ffffff;
  background: #0284c7;
  padding: 7px 16px;
  border-radius: 8px;
  text-decoration: none;
}

.warranty-hero-card {
  border-radius: 18px;
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
}

.hero-shield-badge {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, #e0f2fe, #dbeafe);
  color: #0284c7;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
}
</style>
