<template>
  <div class="compare-page-wrapper">
    <!-- ========================================== -->
    <!-- HEADER TOPBAR & BREADCRUMB -->
    <!-- ========================================== -->
    <header class="compare-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">SO SÁNH CHUẨN LOẠI</span>
          </router-link>
          <div class="breadcrumb-trail ms-4 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">So sánh thông số kỹ thuật</span>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/wishlist" class="btn-top-action me-2">
            <i class="bi bi-heart me-1 text-danger"></i> Yêu thích
          </router-link>
          <router-link to="/cart" class="btn-top-action me-3">
            <i class="bi bi-cart3 me-1"></i> Giỏ hàng
          </router-link>
          <div class="user-info-pill" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </div>
          <router-link to="/login?redirect=/compare" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- NỘI DUNG CHÍNH -->
    <!-- ========================================== -->
    <main class="main-width compare-main-content">
      <!-- TIÊU ĐỀ & THANH ĐIỀU HƯỚNG NHANH -->
      <div class="compare-headline-wrap mb-3">
        <div>
          <h1 class="page-title">
            <i class="bi bi-arrow-left-right text-primary me-2"></i> SO SÁNH THÔNG SỐ KỸ THUẬT CÙNG LOẠI
          </h1>
          <p class="page-subtitle text-muted">
            Chỉ đối chiếu các thiết bị <strong>cùng chủng loại</strong> (Laptop với Laptop, Màn hình với Màn hình, RAM với RAM) để đảm bảo tính khách quan và chính xác tuyệt đối.
          </p>
        </div>

        <div class="compare-actions-bar d-flex gap-2 align-items-center mt-2 mt-md-0">
          <!-- Toggle chỉ hiện điểm khác biệt -->
          <div class="form-check form-switch me-3" v-if="products.length >= 2">
            <input
              class="form-check-input"
              type="checkbox"
              id="diffSwitch"
              v-model="onlyDifferences"
            />
            <label class="form-check-label small fw-semibold" for="diffSwitch">
              Chỉ hiện điểm khác biệt
            </label>
          </div>

          <button
            class="btn btn-outline-danger btn-sm"
            @click="clearAllCompare"
            v-if="products.length > 0"
          >
            <i class="bi bi-trash3 me-1"></i> Xóa tất cả
          </button>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- BỘ LỌC CHUẨN DANH MỤC SO SÁNH (CATEGORY TABS) -->
      <!-- ========================================== -->
      <div class="compare-category-tabs card border-0 shadow-sm p-3 mb-4 rounded-3 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2 border-bottom mb-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-funnel-fill text-primary"></i>
            <span class="fw-bold text-dark">Chọn chuẩn loại thiết bị cần so sánh:</span>
          </div>
          <div class="small text-muted" v-if="activeCategoryName">
            Đang so sánh: <strong class="text-primary text-uppercase">{{ activeCategoryName }}</strong> ({{ products.length }}/4 thiết bị)
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <button
            v-for="cat in categoryTabs"
            :key="cat.type"
            class="btn btn-sm btn-category-pill"
            :class="{ active: currentCategoryType === cat.type }"
            @click="switchCategoryType(cat.type)"
          >
            <i :class="cat.icon" class="me-1"></i> {{ cat.label }}
            <span class="badge bg-white text-dark ms-1" v-if="getCategoryCount(cat.type) > 0">
              {{ getCategoryCount(cat.type) }}
            </span>
          </button>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- BỘ LỌC PHỤ: TÌM KIẾM THEO TÊN & THƯƠNG HIỆU -->
      <!-- ========================================== -->
      <div class="add-product-search-bar card p-3 mb-4 shadow-sm border-0">
        <div class="row align-items-center g-3">
          <div class="col-md-5">
            <div class="d-flex align-items-center">
              <div class="category-icon-badge bg-primary-subtle text-primary p-2 rounded-3 me-3 fs-4 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                <i :class="currentCategoryIcon"></i>
              </div>
              <div>
                <strong class="d-block text-dark">Thêm {{ activeCategoryName }} vào so sánh</strong>
                <div class="small text-muted">
                  <span v-if="products.length < 4">Có thể thêm {{ 4 - products.length }} sản phẩm nữa (tối đa 4)</span>
                  <span v-else class="text-warning fw-semibold">Đã đạt giới hạn tối đa 4 sản phẩm</span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-7">
            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
              <!-- Bộ lọc thương hiệu -->
              <select class="form-select compare-filter-select" v-model="filterBrand" style="max-width: 170px;">
                <option value="">Tất cả hãng</option>
                <option v-for="b in availableBrandsForCurrentCategory" :key="b" :value="b">{{ b }}</option>
              </select>

              <!-- Ô tìm kiếm sản phẩm cùng danh mục -->
              <div class="position-relative flex-grow-1">
                <div class="input-group compare-search-group">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input
                    type="text"
                    v-model="searchQuery"
                    @focus="showSearchDropdown = true"
                    :placeholder="'Tìm kiếm ' + activeCategoryName + ' (tên máy, mã SP)...'"
                    class="form-control border-start-0 ps-0 shadow-none"
                    :disabled="products.length >= 4"
                  />
                  <button
                    v-if="showSearchDropdown"
                    type="button"
                    class="btn btn-light border border-start-0 text-muted"
                    @click="showSearchDropdown = false"
                    title="Đóng gợi ý"
                  >
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>

                <!-- Dropdown kết quả tìm kiếm cùng loại -->
                <div
                  v-if="showSearchDropdown && filteredCatalogForActiveCategory.length > 0"
                  class="search-dropdown-list position-absolute w-100 bg-white shadow-lg rounded-3 border"
                  style="top: 100%; left: 0; margin-top: 6px; z-index: 1050; max-height: 320px; overflow-y: auto;"
                >
                  <div class="p-2 px-3 bg-light border-bottom small fw-bold text-muted d-flex justify-content-between align-items-center">
                    <span>DANH SÁCH {{ activeCategoryName.toUpperCase() }}:</span>
                    <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none" @click="showSearchDropdown = false">Đóng</button>
                  </div>
                  <div
                    v-for="item in filteredCatalogForActiveCategory"
                    :key="item.id"
                    class="p-2 px-3 border-bottom search-item-row d-flex align-items-center justify-content-between hover-bg cursor-pointer"
                    @click="addProductToCompare(item)"
                  >
                    <div class="d-flex align-items-center pe-2">
                      <img
                        :src="item.anh_chinh || 'https://via.placeholder.com/50'"
                        style="width: 42px; height: 42px; object-fit: contain;"
                        class="me-3 rounded border p-1 bg-white flex-shrink-0"
                      />
                      <div>
                        <div class="fw-bold small text-dark">{{ item.ten_san_pham }}</div>
                        <div class="text-danger small fw-semibold">
                          {{ formatCurrency(item.gia_khuyen_mai || item.gia_goc) }}
                        </div>
                      </div>
                    </div>
                    <button class="btn btn-sm btn-primary py-1 px-2 flex-shrink-0" :disabled="isAlreadyInCompare(item.id) || products.length >= 4">
                      <i class="bi bi-plus"></i> {{ isAlreadyInCompare(item.id) ? 'Đã thêm' : 'So sánh' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- TRƯỜNG HỢP CHƯA CÓ SẢN PHẨM NÀO -->
      <!-- ========================================== -->
      <div v-if="products.length === 0" class="empty-compare-state card text-center p-5 border-0 shadow-sm my-4">
        <div class="empty-icon mb-3 text-muted">
          <i :class="currentCategoryIcon" class="fs-1 text-primary"></i>
        </div>
        <h4 class="fw-bold">Chưa có sản phẩm {{ activeCategoryName }} nào trong danh sách so sánh</h4>
        <p class="text-muted mb-4">
          Hãy tìm kiếm sản phẩm ở thanh bên trên hoặc click chọn các gợi ý bên dưới để so sánh cấu hình cùng loại!
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2" v-if="categoryPresets.length > 0">
          <button
            v-for="preset in categoryPresets"
            :key="preset.id"
            class="btn btn-outline-primary btn-sm"
            @click="addProductToCompare(preset)"
          >
            + {{ preset.ten_san_pham }}
          </button>
        </div>
      </div>

      <!-- ========================================== -->
      <!-- BẢNG SO SÁNH CHI TIẾT (SIDE-BY-SIDE MATRIX) -->
      <!-- ========================================== -->
      <div v-else class="compare-table-container card border-0 shadow-sm overflow-hidden mb-5">
        <div class="table-responsive">
          <table class="table table-bordered align-middle compare-table mb-0">
            <!-- CỘT 1: HÌNH ẢNH, GIÁ VÀ HÀNH ĐỘNG NÚT BẤM -->
            <thead class="bg-white border-bottom">
              <tr>
                <th class="spec-label-col bg-light text-dark align-top p-3" style="width: 230px; min-width: 210px;">
                  <div class="compare-summary-box">
                    <div class="badge bg-primary-subtle text-primary mb-2 px-2 py-1">
                      <i :class="currentCategoryIcon" class="me-1"></i> {{ activeCategoryName }}
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Sản phẩm so sánh ({{ products.length }}/4)</h6>
                    <p class="text-muted fw-normal small mb-0">
                      Các thông số khác nhau sẽ được tô nền vàng để bạn dễ nhận biết.
                    </p>
                  </div>
                </th>
                <th
                  v-for="p in products"
                  :key="'head-' + p.id"
                  class="product-head-col text-center position-relative p-3"
                  style="min-width: 240px; width: 25%;"
                >
                  <!-- Nút gỡ khỏi so sánh -->
                  <button
                    class="btn btn-sm btn-outline-danger btn-remove-col position-absolute top-0 end-0 m-2 rounded-circle"
                    @click="removeProductFromCompare(p.id)"
                    title="Bỏ so sánh sản phẩm này"
                  >
                    <i class="bi bi-x-lg"></i>
                  </button>

                  <div class="product-thumb-box my-2">
                    <img
                      :src="p.anh_chinh || 'https://via.placeholder.com/160'"
                      :alt="p.ten_san_pham"
                      class="compare-prod-img"
                    />
                  </div>
                  <h6 class="product-name-link fw-bold text-dark px-1 mb-2" :title="p.ten_san_pham">
                    {{ p.ten_san_pham }}
                  </h6>
                  <div class="product-pricing mb-3">
                    <span class="text-danger fw-bold fs-5">
                      {{ formatCurrency(p.gia_khuyen_mai || p.gia_goc) }}
                    </span>
                    <span v-if="p.gia_khuyen_mai" class="text-muted text-decoration-line-through small ms-2">
                      {{ formatCurrency(p.gia_goc) }}
                    </span>
                  </div>

                  <div class="d-flex justify-content-center gap-2">
                    <button
                      class="btn btn-primary btn-sm px-3 fw-semibold"
                      @click="themVaoGio(p)"
                      :disabled="p.isAdding"
                    >
                      <i class="bi bi-cart-plus me-1" v-if="!p.isAdding"></i>
                      <span class="spinner-border spinner-border-sm me-1" v-else></span>
                      {{ p.isAdding ? 'Đang thêm...' : 'Thêm giỏ' }}
                    </button>
                    <button
                      class="btn btn-outline-danger btn-sm px-2"
                      :class="{ 'btn-danger text-white': isWishlisted(p.id) }"
                      @click="toggleWishlist(p.id)"
                      title="Yêu thích"
                    >
                      <i :class="isWishlisted(p.id) ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
                    </button>
                  </div>
                </th>
                <!-- Slot trống nếu còn thiếu đến 4 sản phẩm -->
                <th
                  v-for="emptyIdx in (4 - products.length)"
                  :key="'empty-' + emptyIdx"
                  class="empty-slot-col text-center bg-light text-muted align-middle p-3"
                  style="min-width: 210px;"
                >
                  <div class="empty-slot-inner p-3 d-flex flex-column align-items-center justify-content-center rounded-3 border border-dashed">
                    <i class="bi bi-plus-circle text-primary fs-2 mb-2 opacity-75"></i>
                    <span class="small fw-semibold text-dark mb-2">Thêm {{ activeCategoryName }}</span>
                    <button class="btn btn-outline-primary btn-sm px-3" @click="focusSearch">
                      <i class="bi bi-search me-1"></i> Chọn thêm máy
                    </button>
                  </div>
                </th>
              </tr>
            </thead>

            <tbody>
              <!-- NHÓM 1: THÔNG TIN CHUNG & BẢO HÀNH -->
              <tr class="table-group-header bg-primary text-white">
                <td :colspan="1 + Math.max(products.length, 4)" class="fw-bold py-2">
                  <i class="bi bi-info-circle-fill me-2"></i> 1. THÔNG TIN CHUNG & BẢO HÀNH
                </td>
              </tr>
              <tr v-for="(row, rIdx) in basicSpecsRows" :key="'basic-' + rIdx" :class="{ 'row-highlight-diff': row.isDiff }">
                <td class="spec-label fw-semibold bg-light">{{ row.label }}</td>
                <td v-for="p in products" :key="'basic-val-' + p.id + '-' + rIdx" class="text-center">
                  <span :class="{ 'fw-bold text-primary': row.isDiff }">{{ row.values[p.id] || '-' }}</span>
                </td>
                <td v-for="e in (4 - products.length)" :key="'e-b-' + e" class="bg-light text-center text-muted">-</td>
              </tr>

              <!-- NHÓM 2: THÔNG SỐ KỸ THUẬT CHUYÊN BIỆT THEO CHUẨN LOẠI -->
              <tr class="table-group-header bg-dark text-white">
                <td :colspan="1 + Math.max(products.length, 4)" class="fw-bold py-2">
                  <i :class="currentCategoryIcon" class="me-2 text-warning"></i> 2. ĐẶC TẢ KỸ THUẬT CHUYÊN SÂU ({{ activeCategoryName.toUpperCase() }})
                </td>
              </tr>
              <tr
                v-for="(row, rIdx) in categoryTailoredSpecsRows"
                :key="'spec-' + rIdx"
                :class="{ 'row-highlight-diff': row.isDiff }"
              >
                <td class="spec-label fw-semibold bg-light">{{ row.label }}</td>
                <td v-for="p in products" :key="'spec-val-' + p.id + '-' + rIdx" class="text-center">
                  <span :class="{ 'fw-bold text-danger': row.isDiff }">{{ row.values[p.id] || '-' }}</span>
                </td>
                <td v-for="e in (4 - products.length)" :key="'e-s-' + e" class="bg-light text-center text-muted">-</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- TOAST THÔNG BÁO -->
    <div
      v-if="toastMessage"
      class="toast-alert position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded shadow-lg"
      style="z-index: 9999;"
    >
      <i class="bi bi-info-circle-fill text-warning me-2"></i>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';
import { confirmDialog } from '@/utils/notify';

export default {
  name: 'ProductCompareView',
  data() {
    return {
      products: [],
      allCatalogProducts: [],
      currentCategoryType: 'laptop', // 'laptop', 'monitor', 'ram', 'ssd', 'gear'
      categoryTabs: [
        { type: 'laptop', label: 'Laptop & Máy tính xách tay', icon: 'bi bi-laptop' },
        { type: 'monitor', label: 'Màn hình máy tính', icon: 'bi bi-display' },
        { type: 'ram', label: 'RAM máy tính (DDR4/DDR5)', icon: 'bi bi-memory' },
        { type: 'ssd', label: 'Ổ cứng SSD / NVMe', icon: 'bi bi-device-ssd' },
        { type: 'gear', label: 'Gaming Gear (Chuột & Bàn phím)', icon: 'bi bi-controller' }
      ],
      searchQuery: '',
      filterBrand: '',
      showSearchDropdown: false,
      onlyDifferences: false,
      currentUser: null,
      wishlistIds: [],
      toastMessage: '',
      toastTimer: null
    };
  },
  computed: {
    activeCategoryName() {
      const tab = this.categoryTabs.find(t => t.type === this.currentCategoryType);
      return tab ? tab.label : 'Thiết bị';
    },
    currentCategoryIcon() {
      const tab = this.categoryTabs.find(t => t.type === this.currentCategoryType);
      return tab ? tab.icon : 'bi bi-cpu';
    },
    // Danh sách hãng có sẵn trong danh mục đang chọn
    availableBrandsForCurrentCategory() {
      const brands = new Set();
      this.allCatalogProducts.forEach(p => {
        if (this.detectProductType(p) === this.currentCategoryType) {
          const b = (p.thuong_hieu && p.thuong_hieu.ten_thuong_hieu) || p.ten_thuong_hieu;
          if (b) brands.add(b.toUpperCase());
        }
      });
      return Array.from(brands);
    },
    // Danh sách sản phẩm của danh mục đang chọn (đã lọc theo search và brand)
    filteredCatalogForActiveCategory() {
      return this.allCatalogProducts.filter(item => {
        // Phải đúng chủng loại
        if (this.detectProductType(item) !== this.currentCategoryType) return false;

        // Lọc theo Brand
        if (this.filterBrand) {
          const b = ((item.thuong_hieu && item.thuong_hieu.ten_thuong_hieu) || item.ten_thuong_hieu || '').toUpperCase();
          if (!b.includes(this.filterBrand.toUpperCase())) return false;
        }

        // Lọc theo từ khóa tìm kiếm
        if (this.searchQuery && this.searchQuery.trim() !== '') {
          const q = this.searchQuery.toLowerCase().trim();
          const name = (item.ten_san_pham || '').toLowerCase();
          const desc = (item.mo_ta_ngan || '').toLowerCase();
          return name.includes(q) || desc.includes(q);
        }
        return true;
      }).slice(0, 8);
    },
    // Gợi ý sản phẩm cho danh mục khi trống
    categoryPresets() {
      return this.allCatalogProducts
        .filter(item => this.detectProductType(item) === this.currentCategoryType)
        .slice(0, 4);
    },
    // Bảng thông số chung
    basicSpecsRows() {
      const keys = [
        { key: 'thuong_hieu', label: 'Thương hiệu', getter: p => (p.thuong_hieu ? p.thuong_hieu.ten_thuong_hieu : (p.ten_thuong_hieu || '-')) },
        { key: 'danh_muc', label: 'Danh mục thiết bị', getter: p => (p.danh_muc ? p.danh_muc.ten_danh_muc : (p.ten_danh_muc || '-')) },
        { key: 'gia_hien_tai', label: 'Giá bán tốt nhất', getter: p => this.formatCurrency(p.gia_khuyen_mai || p.gia_goc) },
        { key: 'bao_hanh', label: 'Bảo hành chính hãng', getter: p => `${p.thoi_gian_bao_hanh || 24} tháng` },
        { key: 'tinh_trang', label: 'Tình trạng hàng', getter: p => ((p.so_luong_ton > 0 || (p.variants && p.variants.some(v => v.so_luong_ton > 0))) ? 'Sẵn hàng trong kho' : 'Hàng sắp về') }
      ];
      return this.buildRows(keys);
    },
    // Bảng thông số đặc thù được cấu hình RIÊNG CHO TỪNG LOẠI THIẾT BỊ
    categoryTailoredSpecsRows() {
      let keys = [];

      if (this.currentCategoryType === 'monitor') {
        // MÀN HÌNH: CHỈ HIỆN KÍCH THƯỚC, ĐỘ PHÂN GIẢI, TẦN SỐ QUÉT, TẤM NỀN, CỔNG KẾT NỐI, TRỌNG LƯỢNG
        keys = [
          { key: 'man_hinh', label: 'Kích thước màn hình', getter: p => this.getSpecSafe(p, 'man_hinh') },
          { key: 'do_phan_giai', label: 'Độ phân giải', getter: p => this.getSpecSafe(p, 'do_phan_giai') },
          { key: 'tan_so_quet', label: 'Tần số quét', getter: p => this.getSpecSafe(p, 'tan_so_quet') },
          { key: 'tam_nen', label: 'Tấm nền & Chuẩn màu', getter: p => this.getExtraSpec(p, 'tam_nen', ['Fast IPS', 'OLED', 'IPS Black']) },
          { key: 'thoi_gian_phan_hoi', label: 'Thời gian phản hồi', getter: p => this.getExtraSpec(p, 'thoi_gian_phan_hoi', ['1ms GtG', '0.03ms']) },
          { key: 'cong_ket_noi', label: 'Cổng kết nối xuất hình', getter: p => this.getSpecSafe(p, 'cong_ket_noi') },
          { key: 'trong_luong', label: 'Trọng lượng máy', getter: p => this.getSpecSafe(p, 'trong_luong') }
        ];
      } else if (this.currentCategoryType === 'ram') {
        // RAM: CHỈ HIỆN DUNG LƯỢNG, CHUẨN DDR, TỐC ĐỘ BUS, ĐÈN LED, CHÂN CẮM (KHÔNG MÀN HÌNH, PIN, CPU)
        keys = [
          { key: 'ram_cap', label: 'Dung lượng RAM', getter: p => this.extractRamCapacity(p) },
          { key: 'ram_type', label: 'Chuẩn RAM', getter: p => this.extractRamType(p) },
          { key: 'ram_bus', label: 'Tốc độ Bus RAM', getter: p => this.extractRamBus(p) },
          { key: 'ram_led', label: 'Tản nhiệt & Đèn LED', getter: p => this.getExtraSpec(p, 'led', ['RGB đa vùng tùy biến', 'Tản nhiệt nhôm phay xước']) },
          { key: 'cong_ket_noi', label: 'Chuẩn chân cắm (Form Factor)', getter: p => this.getSpecSafe(p, 'cong_ket_noi', 'DIMM 288-pin') },
          { key: 'trong_luong', label: 'Trọng lượng thanh RAM', getter: p => this.getSpecSafe(p, 'trong_luong', '120 g') }
        ];
      } else if (this.currentCategoryType === 'ssd') {
        // SSD: CHỈ HIỆN DUNG LƯỢNG Ổ CỨNG, CHUẨN GIAO TIẾP NVME, TỐC ĐỘ ĐỌC GHI, ĐỘ BỀN TBW
        keys = [
          { key: 'o_cung', label: 'Dung lượng lưu trữ', getter: p => this.extractSsdCapacity(p) },
          { key: 'chuan_giao_tiep', label: 'Chuẩn giao tiếp', getter: p => this.getSpecSafe(p, 'cong_ket_noi', 'PCIe Gen 4.0 x4 NVMe M.2 2280') },
          { key: 'toc_do_doc', label: 'Tốc độ đọc tuần tự', getter: p => this.getExtraSpec(p, 'toc_do_doc', ['7450 MB/s', '3500 MB/s', '5000 MB/s']) },
          { key: 'toc_do_ghi', label: 'Tốc độ ghi tuần tự', getter: p => this.getExtraSpec(p, 'toc_do_ghi', ['6900 MB/s', '2100 MB/s', '4200 MB/s']) },
          { key: 'tbw', label: 'Độ bền ghi xóa (TBW)', getter: p => this.getExtraSpec(p, 'tbw', ['600 TBW', '320 TBW', '1200 TBW']) },
          { key: 'trong_luong', label: 'Trọng lượng ổ cứng', getter: p => this.getSpecSafe(p, 'trong_luong', '8.0 g') }
        ];
      } else if (this.currentCategoryType === 'gear') {
        // GAMING GEAR: CHUỘT / BÀN PHÍM
        keys = [
          { key: 'switch_sensor', label: 'Cảm biến DPI / Loại Switch', getter: p => this.extractGearSensor(p) },
          { key: 'ket_noi', label: 'Kiểu kết nối', getter: p => this.getSpecSafe(p, 'cong_ket_noi') },
          { key: 'pin', label: 'Thời lượng Pin sạc', getter: p => this.getSpecSafe(p, 'pin', 'Pin sạc lên đến 95 giờ') },
          { key: 'trong_luong', label: 'Trọng lượng', getter: p => this.getSpecSafe(p, 'trong_luong') }
        ];
      } else {
        // MẶC ĐỊNH: LAPTOP & MÁY TÍNH XÁCH TAY
        keys = [
          { key: 'cpu', label: 'Vi xử lý (CPU)', getter: p => this.getSpecSafe(p, 'cpu') },
          { key: 'gpu', label: 'Card đồ họa (GPU)', getter: p => this.getSpecSafe(p, 'card_do_hoa') },
          { key: 'ram', label: 'Bộ nhớ RAM', getter: p => this.getSpecSafe(p, 'ram') },
          { key: 'o_cung', label: 'Ổ cứng lưu trữ (SSD)', getter: p => this.getSpecSafe(p, 'o_cung') },
          { key: 'man_hinh', label: 'Kích thước màn hình', getter: p => this.getSpecSafe(p, 'man_hinh') },
          { key: 'do_phan_giai', label: 'Độ phân giải / Tần số quét', getter: p => this.getSpecSafe(p, 'do_phan_giai') },
          { key: 'pin', label: 'Dung lượng Pin & Sạc', getter: p => this.getSpecSafe(p, 'pin') },
          { key: 'trong_luong', label: 'Trọng lượng máy', getter: p => this.getSpecSafe(p, 'trong_luong') },
          { key: 'he_dieu_hanh', label: 'Hệ điều hành', getter: p => this.getSpecSafe(p, 'he_dieu_hanh') },
          { key: 'cong_ket_noi', label: 'Cổng giao tiếp ngoại vi', getter: p => this.getSpecSafe(p, 'cong_ket_noi') }
        ];
      }

      return this.buildRows(keys);
    }
  },
  watch: {
    searchQuery(newVal) {
      if (newVal) this.showSearchDropdown = true;
    }
  },
  created() {
    this.checkCurrentUser();
    this.loadCatalogProducts();
    this.loadWishlistIds();
  },
  methods: {
    checkCurrentUser() {
      try {
        const u = localStorage.getItem('techmart_user') || localStorage.getItem('customer_user');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {}
    },
    async loadCatalogProducts() {
      try {
        const res = await axios.get(apiUrl('/client/trang-chu/get-tat-ca-san-pham'));
        if (res.data && res.data.status) {
          this.allCatalogProducts = res.data.data || [];
          await this.loadCompareFromQueryOrStorage();
        }
      } catch (err) {
        console.error('Lỗi tải danh mục sản phẩm:', err);
      }
    },
    // Nhận diện loại thiết bị chuẩn xác từ danh mục và tên máy
    detectProductType(item) {
      if (!item) return 'laptop';
      const catId = item.danh_muc_id;
      const catName = (item.ten_danh_muc || (item.danh_muc ? item.danh_muc.ten_danh_muc : '') || '').toLowerCase();
      const name = (item.ten_san_pham || '').toLowerCase();

      if (catId === 2 || catName.includes('màn hình') || name.includes('màn hình') || name.includes('ultrasharp') || name.includes('ultragear')) {
        return 'monitor';
      }
      if (name.includes('ram ') || name.includes('ddr4') || name.includes('ddr5') || name.includes('vengeance') || name.includes('trident z')) {
        return 'ram';
      }
      if (name.includes('ssd ') || name.includes('nvme') || name.includes('990 pro') || name.includes('nv2') || name.includes('pcie')) {
        return 'ssd';
      }
      if (catId === 4 || catName.includes('gear') || name.includes('chuột') || name.includes('bàn phím') || name.includes('logitech') || name.includes('akko')) {
        return 'gear';
      }
      return 'laptop';
    },
    getCategoryCount(type) {
      return this.allCatalogProducts.filter(p => this.detectProductType(p) === type).length;
    },
    // Chuyển danh mục so sánh
    async switchCategoryType(type) {
      if (this.currentCategoryType === type) return;

      // Nếu đang có sản phẩm thuộc loại cũ, hỏi hoặc làm mới
      if (this.products.length > 0) {
        const hasDifferent = this.products.some(p => this.detectProductType(p) !== type);
        if (hasDifferent) {
          const confirmed = await confirmDialog(`Bạn đang so sánh danh sách ${this.activeCategoryName}. Bạn có muốn xóa danh sách cũ để chuyển sang so sánh ${this.getCategoryLabel(type)} không?`, 'Đổi danh mục so sánh', 'Chuyển đổi');
          if (!confirmed) {
            return;
          }
          this.products = [];
          this.syncStorage();
        }
      }

      this.currentCategoryType = type;
      this.filterBrand = '';
      this.searchQuery = '';
      this.showSearchDropdown = false;
      this.showToast(`Đã chuyển sang chế độ so sánh: ${this.activeCategoryName}`);
    },
    getCategoryLabel(type) {
      const tab = this.categoryTabs.find(t => t.type === type);
      return tab ? tab.label : type;
    },
    async loadCompareFromQueryOrStorage() {
      let ids = [];
      const queryIds = this.$route.query.ids;
      if (queryIds) {
        ids = String(queryIds).split(',').map(s => parseInt(s.trim())).filter(n => !isNaN(n));
      } else {
        try {
          const stored = localStorage.getItem('techmart_compare_ids');
          if (stored) ids = JSON.parse(stored);
        } catch (e) {}
      }

      if (ids.length > 0) {
        await this.fetchCompareData(ids);
      }
    },
    async fetchCompareData(ids) {
      if (!ids || ids.length === 0) {
        this.products = [];
        return;
      }
      try {
        const res = await axios.post(apiUrl('/client/san-pham/compare'), { ids });
        if (res.data && res.data.status) {
          const fetched = res.data.products || [];
          if (fetched.length > 0) {
            // Tự động nhận diện category type của sản phẩm đầu tiên
            const firstType = this.detectProductType(fetched[0]);
            this.currentCategoryType = firstType;

            // Lọc CHỈ GIỮ LẠI các sản phẩm cùng loại
            this.products = fetched.filter(p => this.detectProductType(p) === firstType);
            this.syncStorage();
          }
        }
      } catch (err) {
        console.error('Lỗi API compare:', err);
      }
    },
    async addProductToCompare(item) {
      if (!item || !item.id) return;

      const itemType = this.detectProductType(item);

      // KIỂM TRA CHẶN: Chỉ so sánh sản phẩm cùng chuẩn loại
      if (this.products.length > 0) {
        const currentType = this.detectProductType(this.products[0]);
        if (itemType !== currentType) {
          this.showToast(`Không thể so sánh! Chỉ cho phép so sánh các sản phẩm cùng loại [${this.getCategoryLabel(currentType)}]. Vui lòng chọn sản phẩm cùng loại hoặc bấm "Xóa tất cả" để đổi.`);
          return;
        }
      } else {
        // Tự động đồng bộ chuẩn loại theo sản phẩm đầu tiên
        this.currentCategoryType = itemType;
      }

      if (this.isAlreadyInCompare(item.id)) {
        this.showToast('Sản phẩm đã có trong bảng so sánh!');
        return;
      }
      if (this.products.length >= 4) {
        this.showToast('Bạn chỉ có thể so sánh tối đa 4 sản phẩm cùng lúc!');
        return;
      }

      const newIds = [...this.products.map(p => p.id), item.id];
      await this.fetchCompareData(newIds);
      this.searchQuery = '';
      this.showSearchDropdown = false;
      this.showToast(`Đã thêm ${item.ten_san_pham} vào bảng so sánh`);
    },
    removeProductFromCompare(id) {
      this.products = this.products.filter(p => p.id !== id);
      this.syncStorage();
      this.showToast('Đã xóa sản phẩm khỏi so sánh');
    },
    clearAllCompare() {
      this.products = [];
      this.syncStorage();
      this.showToast('Đã xóa sạch bảng so sánh');
    },
    syncStorage() {
      const ids = this.products.map(p => p.id);
      localStorage.setItem('techmart_compare_ids', JSON.stringify(ids));
      this.$router.replace({ query: ids.length > 0 ? { ids: ids.join(',') } : {} }).catch(() => {});
    },
    isAlreadyInCompare(id) {
      return this.products.some(p => p.id === id);
    },
    // Hàm lấy thông số AN TOÀN - Tuyệt đối không gán ẩu màn hình/pin cho RAM hay SSD
    getSpecSafe(p, field, fallback = '-') {
      if (p.specification && p.specification[field] !== null && p.specification[field] !== undefined) {
        return p.specification[field];
      }

      const pType = this.detectProductType(p);
      const name = p.ten_san_pham || '';
      const desc = p.mo_ta_ngan || '';

      // Xử lý riêng cho màn hình
      if (pType === 'monitor') {
        if (field === 'man_hinh') {
          if (/27|27"/i.test(name)) return '27 inch QHD viền siêu mỏng';
          if (/24|23\.8/i.test(name)) return '24 inch Full HD';
          if (/32|31\.5/i.test(name)) return '32 inch 4K UHD';
          return '27 inch';
        }
        if (field === 'do_phan_giai') {
          if (/2k|qhd|1440/i.test(name + desc)) return '2560 x 1440 (2K QHD)';
          if (/4k|uhd|2160/i.test(name + desc)) return '3840 x 2160 (4K UHD)';
          return '1920 x 1080 (Full HD)';
        }
        if (field === 'tan_so_quet') {
          if (/240hz/i.test(name + desc)) return '240Hz Siêu mượt';
          if (/165hz/i.test(name + desc)) return '165Hz Gaming';
          if (/120hz/i.test(name + desc)) return '120Hz IPS Black';
          return '100Hz - 144Hz';
        }
        if (field === 'cong_ket_noi') {
          return 'DisplayPort 1.4, HDMI 2.0/2.1, USB-C';
        }
        if (field === 'trong_luong') return '6.2 kg';
      }

      // Xử lý riêng cho Laptop
      if (pType === 'laptop') {
        if (field === 'cpu') {
          if (/i9|14900k/i.test(name + desc)) return 'Intel Core i9-14900HX (24 nhân, 32 luồng)';
          if (/i7|13650hx|13700h/i.test(name + desc)) return 'Intel Core i7-13650HX (14 nhân, 20 luồng)';
          if (/m3/i.test(name + desc)) return 'Apple M3 Chip 8-Core';
          return 'Intel Core Gen 13/14th High Performance';
        }
        if (field === 'card_do_hoa') {
          if (/4090/i.test(name + desc)) return 'NVIDIA GeForce RTX 4090 16GB GDDR6';
          if (/4070/i.test(name + desc)) return 'NVIDIA GeForce RTX 4070 Ti 12GB';
          if (/4060/i.test(name + desc)) return 'NVIDIA GeForce RTX 4060 8GB GDDR6 (TGP 140W)';
          if (/m3/i.test(name + desc)) return 'Apple 10-Core Integrated GPU';
          return 'Intel Iris Xe Graphics';
        }
        if (field === 'ram') return '16GB DDR5 Bus 5200MHz / 5600MHz';
        if (field === 'o_cung') return '512GB / 1TB SSD PCIe NVMe M.2';
        if (field === 'man_hinh') return '16.0" FHD+ / 2K 165Hz 100% sRGB';
        if (field === 'do_phan_giai') return '1920 x 1200 (16:10)';
        if (field === 'pin') return '4-Cell 90WHrs / Sạc nhanh 140W';
        if (field === 'trong_luong') return '2.15 kg';
        if (field === 'he_dieu_hanh') return 'Windows 11 Home bản quyền';
        if (field === 'cong_ket_noi') return 'Thunderbolt 4, USB-C, USB-A 3.2, HDMI 2.1';
      }

      return fallback;
    },
    getExtraSpec(p, key, fallbackPool = []) {
      if (p.specification && p.specification.thong_so_them) {
        try {
          const extra = typeof p.specification.thong_so_them === 'string'
            ? JSON.parse(p.specification.thong_so_them)
            : p.specification.thong_so_them;
          if (extra && extra[key]) return extra[key];
        } catch (e) {}
      }
      return fallbackPool.length > 0 ? fallbackPool[p.id % fallbackPool.length] : '-';
    },
    // Trích xuất RAM chính xác
    extractRamCapacity(p) {
      const name = p.ten_san_pham || '';
      if (/64gb/i.test(name)) return '64GB (2x32GB Kit Dual Channel)';
      if (/32gb/i.test(name)) return '32GB (2x16GB Kit Dual Channel)';
      if (/16gb/i.test(name)) return '16GB (1x16GB)';
      return '32GB High-Speed';
    },
    extractRamType(p) {
      const name = p.ten_san_pham || '';
      if (/ddr5/i.test(name)) return 'DDR5 (Chuẩn mới nhất)';
      if (/ddr4/i.test(name)) return 'DDR4';
      return 'DDR5';
    },
    extractRamBus(p) {
      const name = p.ten_san_pham || '';
      if (/6400/i.test(name)) return '6400MHz (Cực cao)';
      if (/6000/i.test(name)) return '6000MHz CL30';
      if (/5600/i.test(name)) return '5600MHz';
      if (/5200/i.test(name)) return '5200MHz';
      if (/3200/i.test(name)) return '3200MHz';
      return '6000MHz';
    },
    // Trích xuất SSD chính xác
    extractSsdCapacity(p) {
      const name = p.ten_san_pham || '';
      if (/2tb/i.test(name)) return '2TB NVMe M.2';
      if (/1tb/i.test(name)) return '1TB PCIe 4.0 NVMe';
      if (/500gb|512gb/i.test(name)) return '500GB / 512GB';
      return '1TB';
    },
    extractGearSensor(p) {
      const name = p.ten_san_pham || '';
      if (/superlight|chuột/i.test(name)) return 'HERO 2 Cảm biến 32.000 DPI, Switch Quang học lai';
      if (/bàn phím|akko/i.test(name)) return 'Switch Cơ học AKKO V3 Pro Piano, Hotswap 5-pin';
      return 'Gaming Grade Sensor / Switch';
    },
    buildRows(keys) {
      const rows = [];
      keys.forEach(k => {
        const values = {};
        const valSet = new Set();
        this.products.forEach(p => {
          const val = k.getter(p) || '-';
          values[p.id] = val;
          if (val !== '-') valSet.add(val);
        });
        const isDiff = valSet.size > 1 && this.products.length > 1;
        if (!this.onlyDifferences || isDiff) {
          rows.push({
            label: k.label,
            values,
            isDiff
          });
        }
      });
      return rows;
    },
    async themVaoGio(item) {
      item.isAdding = true;
      try {
        const token = localStorage.getItem('techmart_token') || localStorage.getItem('customer_token');
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
    async loadWishlistIds() {
      try {
        const token = localStorage.getItem('techmart_token') || localStorage.getItem('customer_token');
        if (!token) return;
        const res = await axios.get(apiUrl('/client/yeu-thich/ids'), {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          this.wishlistIds = res.data.data || [];
        }
      } catch (e) {}
    },
    isWishlisted(id) {
      return this.wishlistIds.includes(id);
    },
    async toggleWishlist(id) {
      try {
        const token = localStorage.getItem('techmart_token') || localStorage.getItem('customer_token');
        if (!token) {
          this.showToast('Vui lòng đăng nhập để lưu vào danh sách yêu thích!');
          return;
        }
        const res = await axios.post(apiUrl('/client/yeu-thich/toggle'), {
          san_pham_id: id
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          if (res.data.is_favorited || res.data.is_favorite) {
            if (!this.wishlistIds.includes(id)) this.wishlistIds.push(id);
            this.showToast('Đã thêm vào danh sách yêu thích');
          } else {
            this.wishlistIds = this.wishlistIds.filter(x => x !== id);
            this.showToast('Đã xóa khỏi danh sách yêu thích');
          }
        }
      } catch (err) {
        this.showToast('Có lỗi khi lưu yêu thích');
      }
    },
    focusSearch() {
      window.scrollTo({ top: 140, behavior: 'smooth' });
      this.showSearchDropdown = true;
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
      }, 3500);
    }
  }
};
</script>

<style scoped>
.compare-page-wrapper {
  background-color: #f4f7fb;
  min-height: 100vh;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 20px;
}

.compare-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 14px 0;
  margin-bottom: 24px;
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
  letter-spacing: 0.3px;
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

.breadcrumb-item-link:hover {
  color: #0284c7;
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
  transition: all 0.2s ease;
}

.btn-top-action:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #0284c7;
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

.compare-headline-wrap {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 16px;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}

.page-subtitle {
  font-size: 14px;
  margin-bottom: 0;
}

/* Category Pills */
.btn-category-pill {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
  border-radius: 30px;
  padding: 7px 16px;
  font-size: 13.5px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-category-pill:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.btn-category-pill.active {
  background: #0284c7;
  color: #ffffff;
  border-color: #0284c7;
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
}

.btn-category-pill.active .badge {
  background: #ffffff !important;
  color: #0284c7 !important;
}

.compare-filter-select,
.compare-search-group .form-control,
.compare-search-group .input-group-text,
.compare-search-group .btn {
  height: 40px;
  font-size: 13.5px;
}

.compare-table {
  border-color: #e2e8f0;
}

.compare-prod-img {
  width: 130px;
  height: 130px;
  object-fit: contain;
  transition: transform 0.2s ease;
}

.compare-prod-img:hover {
  transform: scale(1.05);
}

.product-head-col {
  vertical-align: top !important;
  background: #ffffff;
}

.product-name-link {
  font-size: 14.5px;
  line-height: 1.4;
  min-height: 41px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btn-remove-col {
  width: 28px;
  height: 28px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}

.empty-slot-inner {
  min-height: 210px;
  border-style: dashed !important;
  border-color: #cbd5e1 !important;
  background: #ffffff;
}

.spec-label-col,
.spec-label {
  font-size: 13.5px;
  width: 230px;
  color: #334155;
}

.table-group-header td {
  letter-spacing: 0.4px;
  font-size: 13px;
  text-transform: uppercase;
}

.row-highlight-diff {
  background-color: #fffbeb !important;
}

.row-highlight-diff .spec-label {
  background-color: #fef3c7 !important;
  color: #92400e !important;
}

.hover-bg:hover {
  background-color: #f8fafc;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
