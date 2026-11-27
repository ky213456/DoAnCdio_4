<template>
  <div class="wishlist-page-wrapper">
    <!-- ========================================== -->
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="wishlist-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">YÊU THÍCH</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">Sản phẩm yêu thích</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/compare" class="btn-top-action me-2">
            <i class="bi bi-arrow-left-right me-1"></i> So sánh
          </router-link>
          <router-link to="/cart" class="btn-top-action me-3">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <div class="user-info-pill" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </div>
          <router-link to="/login?redirect=/wishlist" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- NỘI DUNG CHÍNH -->
    <!-- ========================================== -->
    <main class="main-width wishlist-main-content">
      <div class="wishlist-headline-wrap mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h1 class="page-title">
            <i class="bi bi-heart-fill text-danger me-2"></i> DANH SÁCH SẢN PHẨM YÊU THÍCH
          </h1>
          <p class="page-subtitle text-muted">
            Lưu trữ những mẫu Laptop, linh kiện và thiết bị công nghệ bạn quan tâm để dễ dàng theo dõi giá và đặt hàng.
          </p>
        </div>
        <div class="wishlist-stats-badge badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill">
          <i class="bi bi-bookmark-heart-fill me-1"></i> {{ items.length }} sản phẩm đã lưu
        </div>
      </div>

      <!-- LOADING STATE -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-danger" role="status"></div>
        <div class="mt-2 text-muted">Đang tải danh sách yêu thích...</div>
      </div>

      <!-- EMPTY STATE -->
      <div v-else-if="items.length === 0" class="empty-wishlist-card card border-0 shadow-sm text-center p-5 my-4">
        <div class="empty-icon text-muted mb-3">
          <i class="bi bi-heartbreak text-danger fs-1"></i>
        </div>
        <h4 class="fw-bold">Bạn chưa lưu sản phẩm yêu thích nào</h4>
        <p class="text-muted mb-4">
          Bấm vào biểu tượng trái tim <i class="bi bi-heart text-danger"></i> trên mỗi sản phẩm để lưu lại vào danh sách yêu thích của bạn!
        </p>
        <div class="d-flex justify-content-center gap-3">
          <router-link to="/" class="btn btn-danger px-4">
            <i class="bi bi-bag-plus me-1"></i> Khám phá sản phẩm ngay
          </router-link>
          <router-link to="/compare" class="btn btn-outline-secondary px-4">
            <i class="bi bi-arrow-left-right me-1"></i> Xem bảng so sánh
          </router-link>
        </div>
      </div>

      <!-- GRID SẢN PHẨM YÊU THÍCH -->
      <div v-else class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mb-5">
        <div v-for="item in items" :key="item.id" class="col">
          <div class="wishlist-card card h-100 border-0 shadow-sm position-relative">
            <!-- Nút xóa khỏi yêu thích -->
            <button
              class="btn btn-remove-wishlist position-absolute top-0 end-0 m-2 rounded-circle"
              @click="removeFromWishlist(item.id)"
              title="Xóa khỏi yêu thích"
            >
              <i class="bi bi-x-lg"></i>
            </button>

            <!-- Brand Badge -->
            <div class="wishlist-brand-badge m-2">
              <span class="badge bg-light text-dark border">
                {{ (item.thuong_hieu && item.thuong_hieu.ten_thuong_hieu) || item.ten_thuong_hieu || 'Chính hãng' }}
              </span>
            </div>

            <!-- Product Image -->
            <div class="wishlist-img-box text-center p-3 cursor-pointer" @click="$router.push('/product/' + item.id)">
              <img
                :src="item.anh_chinh || 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png'"
                :alt="item.ten_san_pham"
                class="wishlist-thumb"
                @error="e => { e.target.onerror=null; e.target.src='https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png'; }"
              />
            </div>

            <div class="card-body d-flex flex-column pt-0">
              <!-- Tên sản phẩm -->
              <h5 class="wishlist-title fw-bold text-dark mb-1 cursor-pointer" :title="item.ten_san_pham" @click="$router.push('/product/' + item.id)">
                {{ item.ten_san_pham }}
              </h5>

              <!-- Thông số tóm tắt -->
              <div class="wishlist-spec-tag mb-2 text-muted small">
                <i class="bi bi-cpu me-1 text-primary"></i> {{ item.mo_ta_ngan || 'Fullbox bảo hành 24T' }}
              </div>

              <!-- Giá tiền -->
              <div class="wishlist-pricing mt-auto mb-3">
                <span class="price-current text-danger fw-bold fs-5">
                  {{ formatCurrency(item.gia_khuyen_mai || item.gia_goc) }}
                </span>
                <span v-if="item.gia_khuyen_mai" class="price-original text-muted text-decoration-line-through small ms-2">
                  {{ formatCurrency(item.gia_goc) }}
                </span>
              </div>

              <!-- Các nút hành động -->
              <div class="d-grid gap-2">
                <button
                  class="btn btn-danger btn-sm"
                  @click="themVaoGio(item)"
                  :disabled="item.isAdding"
                >
                  <i class="bi bi-cart-plus me-1" v-if="!item.isAdding"></i>
                  <span class="spinner-border spinner-border-sm me-1" v-else></span>
                  {{ item.isAdding ? 'Đang thêm...' : 'Thêm vào giỏ hàng' }}
                </button>
                <button
                  class="btn btn-outline-primary btn-sm"
                  @click="addToCompare(item)"
                >
                  <i class="bi bi-arrow-left-right me-1"></i> So sánh cấu hình
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- TOAST THÔNG BÁO -->
    <div
      v-if="toastMessage"
      class="toast-alert position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded shadow-lg"
      style="z-index: 9999;"
    >
      <i class="bi bi-check-circle-fill text-success me-2"></i>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';

export default {
  name: 'WishlistView',
  data() {
    return {
      items: [],
      isLoading: false,
      currentUser: null,
      toastMessage: '',
      toastTimer: null
    };
  },
  created() {
    this.checkCurrentUser();
    this.loadWishlist();
  },
  methods: {
    getToken() {
      return localStorage.getItem('customer_token') || localStorage.getItem('techmart_token') || localStorage.getItem('token_khach_hang');
    },
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('techmart_user') || localStorage.getItem('khach_hang_info');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {
        console.error(e);
      }
    },
    async loadWishlist() {
      this.isLoading = true;
      try {
        const token = this.getToken();
        if (!token) {
          // Khách vãng lai: lấy từ local storage
          const localIds = JSON.parse(localStorage.getItem('techmart_wishlist_ids') || '[]');
          if (localIds.length > 0) {
            const res = await axios.post(apiUrl('/client/san-pham/compare'), { ids: localIds });
            if (res.data && res.data.status) {
              this.items = res.data.products || [];
            }
          } else {
            this.items = [];
          }
          this.isLoading = false;
          return;
        }

        const res = await axios.get(apiUrl('/client/yeu-thich/danh-sach'), {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          this.items = res.data.data || [];
          // Đồng bộ hóa localStorage với CSDL thực tế
          const syncedIds = this.items.map(i => Number(i.id));
          localStorage.setItem('techmart_wishlist_ids', JSON.stringify(syncedIds));
        }
      } catch (err) {
        console.error('Lỗi tải danh sách yêu thích:', err);
      } finally {
        this.isLoading = false;
      }
    },
    async removeFromWishlist(id) {
      const numId = Number(id);
      try {
        const token = this.getToken();
        if (token) {
          await axios.post(apiUrl('/client/yeu-thich/remove'), { san_pham_id: numId }, {
            headers: { Authorization: `Bearer ${token}` }
          });
        }
        // Cập nhật UI & đồng bộ localStorage ngay lập tức
        this.items = this.items.filter(i => Number(i.id) !== numId);
        const localIds = JSON.parse(localStorage.getItem('techmart_wishlist_ids') || '[]');
        const nextIds = localIds.filter(x => Number(x) !== numId);
        localStorage.setItem('techmart_wishlist_ids', JSON.stringify(nextIds));

        this.showToast('Đã xóa sản phẩm khỏi danh sách yêu thích');
      } catch (err) {
        this.showToast('Không thể xóa sản phẩm lúc này');
      }
    },
    async themVaoGio(item) {
      item.isAdding = true;
      try {
        const token = this.getToken();
        const res = await axios.post(apiUrl('/client/gio-hang/them-vao-gio'), {
          san_pham_id: item.id,
          so_luong: 1
        }, {
          headers: token ? { Authorization: `Bearer ${token}` } : {}
        });

        if (res.data && res.data.status) {
          this.showToast(`Đã thêm ${item.ten_san_pham} vào giỏ hàng!`);
        } else {
          this.showToast(res.data ? res.data.message : 'Thêm giỏ hàng thất bại');
        }
      } catch (err) {
        this.showToast('Vui lòng đăng nhập để lưu sản phẩm vào giỏ hàng');
      } finally {
        item.isAdding = false;
      }
    },
    addToCompare(item) {
      if (!item || !item.id) return;
      this.$router.push({ path: '/compare', query: { ids: String(item.id) } });
    },
    formatCurrency(val) {
      if (!val) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
    showToast(msg) {
      this.toastMessage = msg;
      clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => {
        this.toastMessage = '';
      }, 3000);
    }
  }
};
</script>

<style scoped>
.wishlist-page-wrapper {
  background-color: #f8fafc;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 16px;
}

.wishlist-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 0;
  margin-bottom: 24px;
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
  background: #dc2626;
  padding: 7px 16px;
  border-radius: 8px;
  text-decoration: none;
}

.brand-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
  font-size: 20px;
  color: #0f172a;
}

.logo-icon {
  background: #dc2626;
  color: #fff;
  border-radius: 8px;
  padding: 4px 8px;
  margin-right: 8px;
}

.badge-sub {
  font-size: 11px;
  background: #fee2e2;
  color: #dc2626;
  padding: 3px 8px;
  border-radius: 6px;
  font-weight: 700;
}

.breadcrumb-trail {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
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
  font-size: 14px;
  color: #334155;
  text-decoration: none;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.btn-top-action:hover {
  background: #f1f5f9;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
}

.page-subtitle {
  font-size: 14px;
  margin-bottom: 0;
}

.wishlist-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 12px;
  overflow: hidden;
}

.wishlist-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
}

.btn-remove-wishlist {
  background: rgba(255, 255, 255, 0.9);
  color: #dc2626;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #fee2e2;
  box-shadow: 0 2px 5px rgba(0,0,0,0.08);
}

.btn-remove-wishlist:hover {
  background: #fee2e2;
}

.wishlist-thumb {
  width: 150px;
  height: 150px;
  object-fit: contain;
}

.wishlist-title {
  font-size: 15px;
  line-height: 1.4;
  height: 42px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.wishlist-spec-tag {
  height: 38px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
