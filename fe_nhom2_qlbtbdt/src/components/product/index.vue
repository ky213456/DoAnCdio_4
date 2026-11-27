<template>
  <div class="product-detail-page-wrapper">
    <!-- ========================================== -->
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="detail-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">CHI TIẾT</span>
          </router-link>
          <div class="breadcrumb-trail ms-3 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span v-if="product && product.category" class="breadcrumb-item-link">{{ product.category.ten_danh_muc }}</span>
            <i v-if="product && product.category" class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current text-truncate" style="max-width: 320px;">
              {{ product ? product.ten_san_pham : 'Đang tải...' }}
            </span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/compare" class="btn-top-action">
            <i class="bi bi-arrow-left-right me-1 text-primary"></i> So sánh
          </router-link>
          <router-link to="/wishlist" class="btn-top-action">
            <i class="bi bi-heart me-1 text-danger"></i> Yêu thích
          </router-link>
          <router-link to="/cart" class="btn-top-action">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <router-link to="/profile" class="user-info-pill text-decoration-none" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </router-link>
          <router-link :to="'/login?redirect=/product/' + $route.params.id" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- LOADING & ERROR STATES -->
    <!-- ========================================== -->
    <main class="main-width py-4">
      <div v-if="isLoading" class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
        <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
        <div class="text-muted fw-semibold">Đang tải thông tin chi tiết sản phẩm...</div>
      </div>

      <div v-else-if="errorMessage" class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
        <i class="bi bi-exclamation-circle text-danger fs-1 mb-3"></i>
        <h4 class="fw-bold">{{ errorMessage }}</h4>
        <router-link to="/" class="btn btn-primary mt-3 px-4">
          <i class="bi bi-arrow-left me-1"></i> Quay lại Trang chủ
        </router-link>
      </div>

      <!-- ========================================== -->
      <!-- NỘI DUNG CHI TIẾT SẢN PHẨM -->
      <!-- ========================================== -->
      <div v-else-if="product">
        <!-- KHỐI TRÊN: GALLERY ẢNH + THÔNG TIN MUA HÀNG -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
          <div class="row g-4">
            <!-- CỘT TRÁI: THƯ VIỆN ẢNH SẢN PHẨM -->
            <div class="col-lg-5">
              <div class="main-image-box position-relative border rounded-4 p-4 bg-light d-flex align-items-center justify-content-center mb-3">
                <span v-if="discountPercent > 0" class="badge bg-danger position-absolute top-0 start-0 m-3 fs-6 px-3 py-2 rounded-pill shadow-sm">
                  Giảm {{ discountPercent }}%
                </span>
                <button
                  class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm border"
                  style="width: 40px; height: 40px;"
                  @click="toggleWishlist"
                  :title="isWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích'"
                >
                  <i :class="isWishlisted ? 'bi bi-heart-fill text-danger' : 'bi bi-heart text-muted'" class="fs-5"></i>
                </button>
                <img
                  :src="activeImage || 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png'"
                  :alt="product.ten_san_pham"
                  class="img-fluid main-prod-img"
                  @error="e => { e.target.onerror=null; e.target.src='https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png'; }"
                />
              </div>

              <!-- Dải ảnh thu nhỏ (Thumbnails) -->
              <div class="d-flex gap-2 overflow-x-auto pb-1" v-if="galleryImages.length > 1">
                <div
                  v-for="(imgUrl, idx) in galleryImages"
                  :key="idx"
                  class="thumb-item border rounded-3 p-1 cursor-pointer bg-white flex-shrink-0"
                  :class="{ 'border-primary border-2 shadow-sm': activeImage === imgUrl }"
                  @click="activeImage = imgUrl"
                >
                  <img :src="imgUrl" style="width: 68px; height: 68px; object-fit: contain;" />
                </div>
              </div>
            </div>

            <!-- CỘT PHẢI: THÔNG TIN, CHỌN BIẾN THỂ, GIÁ, ĐẶT HÀNG -->
            <div class="col-lg-7">
              <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-bold" v-if="product.brand">
                  {{ product.brand.ten_thuong_hieu }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill fw-semibold" v-if="product.category">
                  {{ product.category.ten_danh_muc }}
                </span>
                <span class="text-muted small ms-auto">
                  Mã SP: <strong>{{ product.ma_san_pham || ('SP-' + product.id) }}</strong>
                </span>
              </div>

              <h1 class="product-detail-title fw-bold text-dark mb-2">{{ product.ten_san_pham }}</h1>

              <!-- Đánh giá sao & Lượt xem -->
              <div class="d-flex flex-wrap align-items-center gap-3 mb-3 small">
                <div class="d-flex align-items-center gap-1 text-warning cursor-pointer" @click="activeTab = 'reviews'">
                  <strong class="text-dark fs-6 me-1">{{ ratingStats.average || 5.0 }}</strong>
                  <i class="bi bi-star-fill" v-for="s in 5" :key="s"></i>
                  <span class="text-primary ms-1 fw-semibold">({{ ratingStats.total || reviewsList.length }} đánh giá)</span>
                </div>
                <span class="text-muted">|</span>
                <span class="text-muted"><i class="bi bi-eye me-1"></i>{{ product.luot_xem || 0 }} lượt xem</span>
                <span class="text-muted">|</span>
                <span class="text-success fw-semibold">
                  <i class="bi bi-shield-check me-1"></i>Bảo hành {{ product.thoi_gian_bao_hanh || 24 }} tháng
                </span>
              </div>

              <!-- Khung Giá bán -->
              <div class="pricing-highlight-box p-3 rounded-4 mb-4">
                <div class="d-flex align-items-baseline flex-wrap gap-3">
                  <span class="current-price text-danger fw-bold fs-2">
                    {{ formatCurrency(effectivePrice) }}
                  </span>
                  <span v-if="effectiveOriginalPrice > effectivePrice" class="original-price text-muted text-decoration-line-through fs-5">
                    {{ formatCurrency(effectiveOriginalPrice) }}
                  </span>
                  <span v-if="effectiveOriginalPrice > effectivePrice" class="badge bg-danger text-white">
                    Tiết kiệm {{ formatCurrency(effectiveOriginalPrice - effectivePrice) }}
                  </span>
                </div>
                <div class="small text-muted mt-1">
                  <i class="bi bi-check-circle-fill text-success me-1"></i> Giá đã bao gồm VAT • Hỗ trợ xuất hóa đơn điện tử
                </div>
              </div>

              <!-- Chọn Phiên bản / Cấu hình (Variants) -->
              <div class="mb-4" v-if="product.variants && product.variants.length > 0">
                <label class="form-label small fw-bold text-dark mb-2">
                  <i class="bi bi-sliders me-1 text-primary"></i> Chọn phiên bản / cấu hình:
                </label>
                <div class="d-flex flex-wrap gap-2">
                  <button
                    v-for="v in product.variants"
                    :key="v.id"
                    type="button"
                    class="btn btn-variant-chip text-start"
                    :class="{ active: selectedVariant && selectedVariant.id === v.id }"
                    @click="selectVariant(v)"
                  >
                    <div class="fw-bold small">{{ v.ten_bien_the }}</div>
                    <div class="small" :class="selectedVariant && selectedVariant.id === v.id ? 'text-primary fw-bold' : 'text-danger fw-semibold'">
                      {{ formatCurrency(v.gia_khuyen_mai || v.gia_ban || effectivePrice) }}
                    </div>
                  </button>
                </div>
              </div>

              <!-- Chọn số lượng -->
              <div class="d-flex align-items-center gap-3 mb-4">
                <span class="small fw-bold text-dark">Số lượng:</span>
                <div class="input-group" style="width: 130px;">
                  <button class="btn btn-outline-secondary" type="button" @click="quantity = Math.max(1, quantity - 1)">-</button>
                  <input type="number" v-model.number="quantity" min="1" max="20" class="form-control text-center fw-bold" />
                  <button class="btn btn-outline-secondary" type="button" @click="quantity = Math.min(20, quantity + 1)">+</button>
                </div>
                <span class="small text-muted" v-if="selectedVariant">
                  (Còn {{ selectedVariant.so_luong_ton ?? 10 }} sản phẩm trong kho)
                </span>
              </div>

              <!-- CÁC NÚT HÀNH ĐỘNG CHÍNH -->
              <div class="row g-2 mb-4">
                <div class="col-sm-6">
                  <button
                    class="btn btn-danger btn-lg w-100 fw-bold py-3 shadow-sm"
                    @click="muaNgay"
                  >
                    <i class="bi bi-lightning-charge-fill me-1"></i> ĐẶT HÀNG NGAY
                    <div class="small fw-normal opacity-75" style="font-size: 11px;">Giao siêu tốc 2H hoặc nhận tại showroom</div>
                  </button>
                </div>
                <div class="col-sm-6">
                  <button
                    class="btn btn-outline-primary btn-lg w-100 fw-bold py-3"
                    @click="themVaoGio"
                    :disabled="isAddingToCart"
                  >
                    <span v-if="isAddingToCart" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bi bi-cart-plus-fill me-1"></i> THÊM VÀO GIỎ HÀNG
                    <div class="small fw-normal" style="font-size: 11px;">Lưu vào giỏ để tiếp tục mua sắm</div>
                  </button>
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-outline-secondary btn-sm px-3" @click="addToCompare">
                  <i class="bi bi-arrow-left-right me-1 text-primary"></i> So sánh thông số với máy khác
                </button>
                <button class="btn btn-outline-secondary btn-sm px-3" @click="toggleWishlist">
                  <i :class="isWishlisted ? 'bi bi-heart-fill text-danger' : 'bi bi-heart text-danger'" class="me-1"></i>
                  {{ isWishlisted ? 'Đã lưu yêu thích' : 'Lưu vào yêu thích' }}
                </button>
              </div>

              <!-- 4 Cam kết dịch vụ TechMart -->
              <div class="row g-2 p-3 bg-light rounded-3 border small">
                <div class="col-6 d-flex align-items-center gap-2">
                  <i class="bi bi-patch-check-fill text-primary fs-5"></i>
                  <span>Hàng chính hãng 100%, Fullbox</span>
                </div>
                <div class="col-6 d-flex align-items-center gap-2">
                  <i class="bi bi-upc-scan text-success fs-5"></i>
                  <span>Cấp Serial/IMEI bảo hành điện tử</span>
                </div>
                <div class="col-6 d-flex align-items-center gap-2">
                  <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                  <span>Lỗi 1 đổi 1 trong 30 ngày đầu</span>
                </div>
                <div class="col-6 d-flex align-items-center gap-2">
                  <i class="bi bi-truck text-danger fs-5"></i>
                  <span>Miễn phí giao hàng toàn quốc</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- KHỐI DƯỚI: TABS MÔ TẢ / THÔNG SỐ KỸ THUẬT / ĐÁNH GIÁ BÌNH LUẬN -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
          <div class="d-flex border-bottom bg-light px-4 pt-3 gap-3 overflow-x-auto">
            <button
              class="btn btn-tab-pill pb-3"
              :class="{ active: activeTab === 'desc' }"
              @click="activeTab = 'desc'"
            >
              <i class="bi bi-file-text me-1"></i> Mô tả sản phẩm
            </button>
            <button
              class="btn btn-tab-pill pb-3"
              :class="{ active: activeTab === 'specs' }"
              @click="activeTab = 'specs'"
            >
              <i class="bi bi-cpu me-1"></i> Thông số kỹ thuật chi tiết
            </button>
            <button
              class="btn btn-tab-pill pb-3"
              :class="{ active: activeTab === 'reviews' }"
              @click="activeTab = 'reviews'"
            >
              <i class="bi bi-star-fill text-warning me-1"></i> Đánh giá & Bình luận
              <span class="badge bg-danger rounded-pill ms-1">{{ reviewsList.length }}</span>
            </button>
          </div>

          <div class="p-4">
            <!-- TAB 1: MÔ TẢ SẢN PHẨM -->
            <div v-if="activeTab === 'desc'">
              <h5 class="fw-bold text-dark mb-3">Giới thiệu tổng quan {{ product.ten_san_pham }}</h5>
              <p class="text-dark fs-6 mb-3">{{ product.mo_ta_ngan }}</p>
              <div v-if="product.noi_dung_chi_tiet" class="rich-content text-secondary" v-html="product.noi_dung_chi_tiet"></div>
              <div v-else class="text-muted small">Sản phẩm chính hãng phân phối tại hệ thống TechMart AI với chế độ bảo hành điện tử toàn diện.</div>
            </div>

            <!-- TAB 2: THÔNG SỐ KỸ THUẬT -->
            <div v-else-if="activeTab === 'specs'">
              <h5 class="fw-bold text-dark mb-3">Bảng thông số kỹ thuật chuẩn xác</h5>
              <div v-if="specRows.length === 0" class="text-muted">Thông số kỹ thuật đang được cập nhật.</div>
              <div v-else class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0" style="max-width: 820px;">
                  <tbody>
                    <tr v-for="(row, idx) in specRows" :key="idx">
                      <th class="bg-light text-secondary fw-semibold" style="width: 240px;">{{ row.label }}</th>
                      <td class="text-dark">{{ row.value }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 3: ĐÁNH GIÁ & BÌNH LUẬN (UC03) -->
            <div v-else-if="activeTab === 'reviews'">
              <div class="row g-4">
                <div class="col-lg-5">
                  <div class="card p-4 bg-light border-0 rounded-4 mb-3">
                    <div class="text-center mb-3">
                      <div class="display-4 fw-bold text-warning">{{ ratingStats.average || 5.0 }}/5</div>
                      <div class="text-warning fs-5 mb-1">
                        <i class="bi bi-star-fill" v-for="s in 5" :key="s"></i>
                      </div>
                      <div class="text-muted small">{{ ratingStats.total || reviewsList.length }} đánh giá từ khách hàng đã mua</div>
                    </div>
                    <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="d-flex align-items-center gap-2 small mb-2">
                      <span style="min-width: 48px;">{{ star }} sao</span>
                      <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" :style="{ width: getStarPercent(star) + '%' }"></div>
                      </div>
                      <span class="text-muted" style="min-width: 24px;">{{ getStarCount(star) }}</span>
                    </div>
                  </div>

                  <!-- Form viết đánh giá (chỉ cho phép khi đã đặt mua) -->
                  <div v-if="!currentUser" class="alert alert-warning border-0 rounded-3 small fw-semibold">
                    <i class="bi bi-lock-fill me-2"></i>Vui lòng <router-link :to="'/login?redirect=/product/' + product.id" class="fw-bold text-decoration-underline">đăng nhập</router-link> và đặt mua sản phẩm để gửi đánh giá.
                  </div>
                  <div v-else-if="!canReview" class="alert alert-info border-0 rounded-3 small fw-semibold">
                    <i class="bi bi-bag-check-fill me-2 text-primary"></i>Chỉ khách hàng đã đặt mua sản phẩm này tại TechMart AI mới có thể gửi đánh giá công khai.
                  </div>
                  <div v-else class="card p-3 border rounded-3 shadow-sm">
                    <h6 class="fw-bold mb-2 text-dark">Viết đánh giá của bạn</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                      <span class="small text-muted">Chọn sao:</span>
                      <div class="cursor-pointer text-warning fs-5">
                        <i
                          v-for="st in 5"
                          :key="st"
                          :class="st <= newReview.so_sao ? 'bi bi-star-fill' : 'bi bi-star'"
                          @click="newReview.so_sao = st"
                          class="me-1"
                        ></i>
                      </div>
                    </div>
                    <textarea
                      rows="3"
                      v-model="newReview.noi_dung"
                      class="form-control mb-3"
                      placeholder="Chia sẻ trải nghiệm thực tế của bạn về sản phẩm..."
                    ></textarea>
                    <button class="btn btn-primary w-100 fw-bold" @click="submitReview" :disabled="isSubmittingReview">
                      <span v-if="isSubmittingReview" class="spinner-border spinner-border-sm me-1"></span>
                      Gửi đánh giá ngay
                    </button>
                  </div>
                </div>

                <div class="col-lg-7">
                  <h6 class="fw-bold mb-3 text-dark">Nhận xét thực tế ({{ reviewsList.length }})</h6>
                  <div v-if="reviewsList.length === 0" class="text-center py-5 text-muted bg-light rounded-4">
                    <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
                    Chưa có đánh giá nào cho sản phẩm này.
                  </div>
                  <div v-else class="d-flex flex-column gap-3">
                    <div v-for="rv in reviewsList" :key="rv.id" class="p-3 border rounded-3 bg-white">
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 13px;">
                            {{ ((rv.user && rv.user.ho_ten) || 'KH').substring(0, 2).toUpperCase() }}
                          </div>
                          <div>
                            <strong class="d-block small text-dark">{{ (rv.user && rv.user.ho_ten) || 'Khách hàng TechMart' }}</strong>
                            <span v-if="rv.da_mua_hang" class="badge bg-success-subtle text-success" style="font-size: 11px;">
                              <i class="bi bi-patch-check-fill me-1"></i>Đã mua hàng tại TechMart
                            </span>
                          </div>
                        </div>
                        <div class="text-warning small">
                          <i class="bi bi-star-fill" v-for="s in (rv.so_sao || 5)" :key="s"></i>
                        </div>
                      </div>
                      <p class="mb-1 text-dark small">{{ rv.noi_dung }}</p>
                      <div class="text-muted" style="font-size: 11.5px;">
                        {{ rv.created_at ? new Date(rv.created_at).toLocaleDateString('vi-VN') : '' }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- TOAST THÔNG BÁO -->
    <div
      v-if="toastMessage"
      class="toast-alert position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded-3 shadow-lg"
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
  name: 'ProductDetailView',
  data() {
    return {
      product: null,
      selectedVariant: null,
      activeImage: '',
      quantity: 1,
      activeTab: 'desc',
      isLoading: false,
      errorMessage: '',
      isAddingToCart: false,
      currentUser: null,
      isWishlisted: false,
      reviewsList: [],
      ratingStats: { average: 5, total: 0, stars_breakdown: {} },
      canReview: false,
      isSubmittingReview: false,
      newReview: { so_sao: 5, noi_dung: '' },
      toastMessage: '',
      toastTimer: null
    };
  },
  computed: {
    galleryImages() {
      if (!this.product) return [];
      const urls = [];
      if (this.product.anh_chinh) urls.push(this.product.anh_chinh);
      if (Array.isArray(this.product.images)) {
        this.product.images.forEach(img => {
          const u = img.duong_dan || img.url || img.hinh_anh;
          if (u && !urls.includes(u)) urls.push(u);
        });
      }
      return urls;
    },
    effectivePrice() {
      if (this.selectedVariant) {
        return Number(this.selectedVariant.gia_khuyen_mai || this.selectedVariant.gia_ban || this.product.gia_khuyen_mai || this.product.gia_goc || 0);
      }
      return Number((this.product && (this.product.gia_khuyen_mai || this.product.gia_goc)) || 0);
    },
    effectiveOriginalPrice() {
      if (this.selectedVariant && this.selectedVariant.gia_ban) {
        return Number(this.selectedVariant.gia_ban);
      }
      return Number((this.product && this.product.gia_goc) || 0);
    },
    discountPercent() {
      if (this.effectiveOriginalPrice > this.effectivePrice && this.effectiveOriginalPrice > 0) {
        return Math.round(((this.effectiveOriginalPrice - this.effectivePrice) / this.effectiveOriginalPrice) * 100);
      }
      return 0;
    },
    specRows() {
      if (!this.product || !this.product.specification) return [];
      const sp = this.product.specification;
      const labels = {
        cpu: 'Bộ vi xử lý (CPU)',
        ram: 'Bộ nhớ RAM',
        o_cung: 'Ổ cứng lưu trữ',
        card_do_hoa: 'Card đồ họa (GPU)',
        man_hinh: 'Kích thước màn hình',
        do_phan_giai: 'Độ phân giải',
        tan_so_quet: 'Tần số quét',
        pin: 'Dung lượng Pin',
        trong_luong: 'Trọng lượng',
        cong_ket_noi: 'Cổng kết nối',
        he_dieu_hanh: 'Hệ điều hành'
      };
      const rows = [];
      Object.keys(labels).forEach(k => {
        if (sp[k]) rows.push({ label: labels[k], value: sp[k] });
      });
      if (sp.thong_so_them) {
        try {
          const extra = typeof sp.thong_so_them === 'string' ? JSON.parse(sp.thong_so_them) : sp.thong_so_them;
          if (extra && typeof extra === 'object') {
            Object.entries(extra).forEach(([ek, ev]) => {
              if (ev) rows.push({ label: ek.replace(/_/g, ' ').toUpperCase(), value: ev });
            });
          }
        } catch (e) {}
      }
      return rows;
    }
  },
  watch: {
    '$route.params.id'(newId) {
      if (newId) this.loadProductDetail(newId);
    }
  },
  created() {
    this.checkCurrentUser();
    this.loadProductDetail(this.$route.params.id);
  },
  methods: {
    getToken() {
      return localStorage.getItem('customer_token') || localStorage.getItem('techmart_token') || localStorage.getItem('token_khach_hang');
    },
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('techmart_user') || localStorage.getItem('khach_hang_info');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {}
    },
    async loadProductDetail(id) {
      if (!id) return;
      this.isLoading = true;
      this.errorMessage = '';
      try {
        const res = await axios.get(apiUrl(`/client/chi-tiet-san-pham/${id}`));
        if (res.data && res.data.status) {
          this.product = res.data.data;
          this.activeImage = this.product.anh_chinh || (this.galleryImages[0] || '');
          if (Array.isArray(this.product.variants) && this.product.variants.length > 0) {
            this.selectedVariant = this.product.variants[0];
          } else {
            this.selectedVariant = null;
          }
          await Promise.all([
            this.loadReviews(id),
            this.checkWishlistStatus(id)
          ]);
        } else {
          this.errorMessage = res.data?.message || 'Không tìm thấy sản phẩm.';
        }
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Không thể tải thông tin sản phẩm.';
      } finally {
        this.isLoading = false;
      }
    },
    async loadReviews(id) {
      try {
        const token = this.getToken();
        const res = await axios.get(apiUrl(`/client/san-pham/${id}/danh-gia`), {
          headers: token ? { Authorization: `Bearer ${token}` } : {}
        });
        if (res.data && res.data.status) {
          this.reviewsList = res.data.data || [];
          this.ratingStats = res.data.stats || { average: 5, total: 0, stars_breakdown: {} };
          this.canReview = !!res.data.can_review;
        }
      } catch (e) {}
    },
    async checkWishlistStatus(id) {
      const numId = Number(id);
      const token = this.getToken();
      if (token) {
        try {
          const res = await axios.get(apiUrl('/client/yeu-thich/ids'), {
            headers: { Authorization: `Bearer ${token}` }
          });
          if (res.data && res.data.status) {
            this.isWishlisted = (res.data.data || []).map(Number).includes(numId);
            return;
          }
        } catch (e) {}
      }
      const localIds = JSON.parse(localStorage.getItem('techmart_wishlist_ids') || '[]');
      this.isWishlisted = localIds.map(Number).includes(numId);
    },
    selectVariant(v) {
      this.selectedVariant = v;
      if (v.hinh_anh) this.activeImage = v.hinh_anh;
    },
    getSessionId() {
      let sess = localStorage.getItem('techmart_session_id');
      if (!sess) {
        sess = 'guest_' + Math.random().toString(36).substring(2, 12) + Date.now().toString(36);
        localStorage.setItem('techmart_session_id', sess);
      }
      return sess;
    },
    async themVaoGio() {
      const token = this.getToken();
      if (!token) {
        this.$router.push('/login?redirect=/product/' + this.product.id);
        return;
      }
      this.isAddingToCart = true;
      try {
        const res = await axios.post(apiUrl('/client/gio-hang/them-vao-gio'), {
          san_pham_id: this.product.id,
          bien_the_id: this.selectedVariant ? this.selectedVariant.id : null,
          so_luong: this.quantity,
          gia: this.effectivePrice,
          session_id: this.getSessionId(),
          user_id: this.currentUser ? this.currentUser.id : null
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          this.showToast(`Đã thêm ${this.quantity} x ${this.product.ten_san_pham} vào giỏ hàng!`);
        } else {
          this.showToast(res.data?.message || 'Không thể thêm vào giỏ hàng');
        }
      } catch (err) {
        this.showToast(err.response?.data?.message || 'Lỗi khi thêm vào giỏ hàng');
      } finally {
        this.isAddingToCart = false;
      }
    },
    muaNgay() {
      const token = this.getToken();
      if (!token) {
        this.$router.push('/login?redirect=/product/' + this.product.id);
        return;
      }
      const checkoutItem = {
        gio_hang_id: null,
        bien_the_id: this.selectedVariant ? this.selectedVariant.id : null,
        ten_san_pham: this.product.ten_san_pham,
        ten_bien_the: this.selectedVariant ? this.selectedVariant.ten_bien_the : 'Tiêu chuẩn',
        quantity: this.quantity,
        price: this.effectivePrice,
        image: this.activeImage || this.product.anh_chinh || ''
      };
      localStorage.setItem('techmart_checkout_items', JSON.stringify([checkoutItem]));
      this.$router.push('/checkout');
    },
    async toggleWishlist() {
      const numId = Number(this.product.id);
      const token = this.getToken();
      if (!token) {
        this.$router.push('/login?redirect=/product/' + numId);
        return;
      }
      try {
        const res = await axios.post(apiUrl('/client/yeu-thich/toggle'), { san_pham_id: numId }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          this.isWishlisted = !!(res.data.is_favorited || res.data.is_favorite);
          this.showToast(this.isWishlisted ? 'Đã lưu vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích!');
        }
      } catch (e) {
        this.showToast('Không thể cập nhật yêu thích');
      }
    },
    addToCompare() {
      this.$router.push({ path: '/compare', query: { ids: String(this.product.id) } });
    },
    async submitReview() {
      if (!this.newReview.noi_dung || this.newReview.noi_dung.trim().length < 5) {
        this.showToast('Nội dung đánh giá phải có ít nhất 5 ký tự!');
        return;
      }
      this.isSubmittingReview = true;
      try {
        const res = await axios.post(apiUrl(`/client/san-pham/${this.product.id}/danh-gia`), this.newReview, {
          headers: { Authorization: `Bearer ${this.getToken()}` }
        });
        if (res.data && res.data.status) {
          this.showToast('Cảm ơn bạn đã gửi đánh giá!');
          this.newReview.noi_dung = '';
          this.newReview.so_sao = 5;
          await this.loadReviews(this.product.id);
        } else {
          this.showToast(res.data?.message || 'Không thể gửi đánh giá');
        }
      } catch (err) {
        this.showToast(err.response?.data?.message || 'Bạn cần đặt mua sản phẩm trước khi đánh giá!');
      } finally {
        this.isSubmittingReview = false;
      }
    },
    getStarCount(star) {
      return (this.ratingStats.stars_breakdown && this.ratingStats.stars_breakdown[star]) || 0;
    },
    getStarPercent(star) {
      const total = this.ratingStats.total || this.reviewsList.length;
      if (!total) return 0;
      return Math.round((this.getStarCount(star) / total) * 100);
    },
    formatCurrency(val) {
      if (!val) return '0 ₫';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
    },
    showToast(msg) {
      this.toastMessage = msg;
      clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => { this.toastMessage = ''; }, 3000);
    }
  }
};
</script>

<style scoped>
.product-detail-page-wrapper {
  background-color: #f4f7fb;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 20px;
}

.detail-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 14px 0;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
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
  flex-wrap: nowrap;
  gap: 12px;
  min-width: 0;
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
  white-space: nowrap;
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
  white-space: nowrap;
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
  white-space: nowrap;
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
  white-space: nowrap;
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
  white-space: nowrap;
}

.main-image-box {
  height: 390px;
}

.main-prod-img {
  max-height: 330px;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.main-image-box:hover .main-prod-img {
  transform: scale(1.04);
}

.product-detail-title {
  font-size: 24px;
  line-height: 1.35;
}

.pricing-highlight-box {
  background: linear-gradient(135deg, #fff1f2 0%, #fff7ed 100%);
  border: 1px solid #fecdd3;
}

.btn-variant-chip {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  padding: 8px 14px;
  transition: all 0.2s ease;
}

.btn-variant-chip:hover {
  border-color: #93c5fd;
  background: #f8fafc;
}

.btn-variant-chip.active {
  border-color: #0284c7;
  background: #eff6ff;
  box-shadow: 0 2px 8px rgba(2, 132, 199, 0.15);
}

.btn-tab-pill {
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  font-weight: 700;
  color: #64748b;
  border-radius: 0;
  white-space: nowrap;
}

.btn-tab-pill.active {
  color: #0284c7;
  border-bottom-color: #0284c7;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
