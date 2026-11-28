<template>
  <div class="catalog-page-wrapper">
    <!-- ========================================== -->
    <!-- ❶ HEADER TOPBAR -->
    <!-- ========================================== -->
    <header class="catalog-header">
      <div class="main-width header-inner">
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-sub ms-2">CATALOG</span>
          </router-link>
          <div class="breadcrumb-trail ms-3 d-none d-md-flex">
            <router-link to="/" class="breadcrumb-item-link">Trang chủ</router-link>
            <i class="bi bi-chevron-right separator"></i>
            <span class="breadcrumb-current">{{ currentCategoryTitle }}</span>
          </div>
        </div>

        <div class="header-search flex-grow-1 mx-4 d-none d-lg-block" style="max-width: 480px;">
          <div class="search-box-pill">
            <i class="bi bi-search search-icon"></i>
            <input
              type="text"
              v-model="searchQuery"
              @keyup.enter="applyFilters"
              placeholder="Tìm theo tên sản phẩm, cấu hình, mã SKU..."
              class="search-input"
            />
            <button class="search-btn" @click="applyFilters">Tìm kiếm</button>
          </div>
        </div>

        <div class="header-right">
          <router-link to="/compare" class="btn-top-action" title="So sánh sản phẩm">
            <i class="bi bi-arrow-left-right text-primary me-1"></i> So sánh
            <span class="action-badge bg-info text-dark" v-if="compareList.length > 0">{{ compareList.length }}</span>
          </router-link>
          <router-link to="/wishlist" class="btn-top-action" title="Danh sách yêu thích">
            <i class="bi bi-heart text-danger me-1"></i> Yêu thích
            <span class="action-badge bg-danger text-white" v-if="wishlistIds.length > 0">{{ wishlistIds.length }}</span>
          </router-link>
          <router-link to="/cart" class="btn-top-action" title="Giỏ hàng">
            <i class="bi bi-cart3 text-warning me-1"></i> Giỏ hàng
            <span class="action-badge bg-warning text-dark" v-if="cartCount > 0">{{ cartCount }}</span>
          </router-link>
          <router-link to="/profile" class="user-info-pill text-decoration-none" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || 'Khách hàng' }}</span>
          </router-link>
          <router-link to="/login?redirect=/catalog" class="btn-login-link" v-else>
            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- ❷ NAVIGATION BAR (TẤT CẢ DANH MỤC, LAPTOP, PC...) -->
    <!-- ========================================== -->
    <nav class="main-navbar">
      <div class="main-width navbar-inner">
        <div class="nav-catalog-btn" :class="{ active: selectedCategoryKey === 'all' }" @click="selectNavCategory('all')">
          <i class="bi bi-grid-3x3-gap-fill menu-bars"></i>
          <span class="catalog-text">TẤT CẢ DANH MỤC</span>
        </div>
        <ul class="nav-links">
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" :class="{ active: selectedCategoryKey === 'laptop' }" @click="selectNavCategory('laptop')">
              <i class="bi bi-laptop me-1"></i> LAPTOP
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" :class="{ active: selectedCategoryKey === 'pc-may-tram' }" @click="selectNavCategory('pc-may-tram')">
              <i class="bi bi-pc-display me-1"></i> PC & MÁY TRẠM
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" :class="{ active: selectedCategoryKey === 'smartphone' }" @click="selectNavCategory('smartphone')">
              <i class="bi bi-phone me-1"></i> SMARTPHONE
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" :class="{ active: selectedCategoryKey === 'linh-kien-pc' }" @click="selectNavCategory('linh-kien-pc')">
              <i class="bi bi-cpu me-1"></i> LINH KIỆN PC
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" :class="{ active: selectedCategoryKey === 'gaming-gear' }" @click="selectNavCategory('gaming-gear')">
              <i class="bi bi-controller me-1"></i> PHỤ KIỆN
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link flash-link" :class="{ active: selectedCategoryKey === 'flash-sale' }" @click="selectNavCategory('flash-sale')">
              <i class="bi bi-lightning-fill text-warning me-1"></i> FLASH SALE
            </a>
          </li>
          <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link promo-link" :class="{ active: selectedCategoryKey === 'promo' }" @click="selectNavCategory('promo')">
              <i class="bi bi-gift-fill text-danger me-1"></i> KHUYẾN MÃI
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- ========================================== -->
    <!-- ❸ CATEGORY HERO BANNER -->
    <!-- ========================================== -->
    <section class="category-banner-section main-width my-4">
      <div class="category-hero-card" :class="'theme-' + selectedCategoryKey">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <div class="badge-tag mb-2">
              <i class="bi bi-patch-check-fill me-1 text-warning"></i> TECHMART CHÍNH HÃNG 100%
            </div>
            <h1 class="hero-title fw-bold text-white mb-2">{{ heroBanner.title }}</h1>
            <p class="hero-desc text-white-50 mb-3">{{ heroBanner.desc }}</p>
            <div class="quick-tags d-flex flex-wrap gap-2">
              <button
                v-for="tag in heroBanner.tags"
                :key="tag"
                class="btn btn-sm btn-tag"
                :class="{ 'btn-tag-active': searchQuery === tag }"
                @click="searchByTag(tag)"
              >
                {{ tag }}
              </button>
            </div>
          </div>
          <div class="col-lg-4 d-none d-lg-flex justify-content-end">
            <div class="hero-stat-box text-center p-3 rounded-4 bg-white bg-opacity-10 backdrop-blur border border-white border-opacity-20 text-white">
              <div class="stat-number fw-bold fs-2 text-warning">{{ filteredProducts.length }}</div>
              <div class="stat-label small text-white-50">Sản phẩm khả dụng</div>
              <div class="stat-sub small mt-1 text-info"><i class="bi bi-shield-lock me-1"></i>Bảo hành tới 36 tháng</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ❹ MAIN WORKSPACE: SIDEBAR + PRODUCT GRID -->
    <!-- ========================================== -->
    <main class="main-width pb-5">
      <div class="row g-4">
        <!-- SIDEBAR BỘ LỌC -->
        <aside class="col-lg-3">
          <div class="filter-sidebar card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px; z-index: 10;">
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-funnel-fill text-primary me-2"></i>Bộ lọc tìm kiếm
              </h5>
              <button class="btn btn-link btn-sm text-decoration-none text-muted p-0" @click="resetAllFilters">
                Đặt lại
              </button>
            </div>

            <!-- 1. Danh mục -->
            <div class="filter-group mb-4">
              <div class="filter-title fw-semibold text-dark small mb-2 text-uppercase">Danh mục sản phẩm</div>
              <div class="category-radio-list">
                <label class="category-radio-item" :class="{ selected: selectedCategoryId === null }">
                  <input type="radio" :value="null" v-model="selectedCategoryId" @change="applyFilters" />
                  <span class="cat-label">Tất cả danh mục</span>
                  <span class="cat-count">({{ allProducts.length }})</span>
                </label>
                <label
                  v-for="cat in categories"
                  :key="cat.id"
                  class="category-radio-item"
                  :class="{ selected: selectedCategoryId === cat.id }"
                >
                  <input type="radio" :value="cat.id" v-model="selectedCategoryId" @change="applyFilters" />
                  <span class="cat-label">{{ cat.ten_danh_muc }}</span>
                  <span class="cat-count">({{ getCategoryProductCount(cat.id) }})</span>
                </label>
              </div>
            </div>

            <!-- 2. Khoảng giá -->
            <div class="filter-group mb-4">
              <div class="filter-title fw-semibold text-dark small mb-2 text-uppercase">Mức giá</div>
              <div class="price-range-list">
                <label
                  v-for="(pr, idx) in priceRanges"
                  :key="idx"
                  class="price-range-item"
                  :class="{ selected: selectedPriceRangeIndex === idx }"
                >
                  <input type="radio" :value="idx" v-model="selectedPriceRangeIndex" @change="applyFilters" />
                  <span>{{ pr.label }}</span>
                </label>
              </div>
            </div>

            <!-- 3. Thương hiệu -->
            <div class="filter-group mb-4" v-if="availableBrands.length > 0">
              <div class="filter-title fw-semibold text-dark small mb-2 text-uppercase">Hãng sản xuất</div>
              <div class="brand-chips-grid">
                <button
                  v-for="b in availableBrands"
                  :key="b.id"
                  class="brand-chip"
                  :class="{ active: selectedBrandIds.includes(b.id) }"
                  @click="toggleBrand(b.id)"
                >
                  {{ b.ten_thuong_hieu }}
                </button>
              </div>
            </div>

            <!-- 4. Bộ lọc RAM -->
            <div class="filter-group mb-4">
              <div class="filter-title fw-semibold text-dark small mb-2 text-uppercase">Dung lượng RAM</div>
              <div class="d-flex flex-wrap gap-1">
                <button
                  v-for="ram in ['8GB', '16GB', '32GB', '64GB']"
                  :key="ram"
                  class="btn btn-sm"
                  :class="selectedRam === ram ? 'btn-primary' : 'btn-outline-secondary'"
                  style="font-size: 11.5px;"
                  @click="toggleRam(ram)"
                >
                  {{ ram }}
                </button>
              </div>
            </div>

            <!-- Nút áp dụng trên di động -->
            <button class="btn btn-primary w-100 fw-bold mt-2 d-lg-none" @click="applyFilters">
              Áp dụng bộ lọc
            </button>
          </div>
        </aside>

        <!-- CỘT DANH SÁCH SẢN PHẨM -->
        <section class="col-lg-9">
          <!-- Toolbar trên cùng -->
          <div class="catalog-toolbar card border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div>
                <span class="text-muted small">Tìm thấy </span>
                <span class="fw-bold text-dark">{{ filteredProducts.length }}</span>
                <span class="text-muted small"> sản phẩm phù hợp</span>
                <span v-if="searchQuery" class="badge bg-primary-subtle text-primary ms-2">
                  "{{ searchQuery }}"
                </span>
              </div>

              <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-sm-inline">Sắp xếp:</span>
                <select v-model="sortBy" class="form-select form-select-sm border-0 bg-light fw-semibold" style="width: 170px;" @change="applySort">
                  <option value="newest">Mới nhất</option>
                  <option value="price_asc">Giá tăng dần</option>
                  <option value="price_desc">Giá giảm dần</option>
                  <option value="views">Xem nhiều nhất</option>
                  <option value="name_asc">Tên A → Z</option>
                </select>

                <div class="btn-group bg-light p-1 rounded-3">
                  <button
                    class="btn btn-sm btn-icon"
                    :class="{ 'btn-white shadow-sm': viewMode === 'grid' }"
                    @click="viewMode = 'grid'"
                    title="Dạng lưới"
                  >
                    <i class="bi bi-grid-fill"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-icon"
                    :class="{ 'btn-white shadow-sm': viewMode === 'list' }"
                    @click="viewMode = 'list'"
                    title="Dạng danh sách"
                  >
                    <i class="bi bi-list-task"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="spinner-border text-primary mx-auto mb-3" role="status"></div>
            <div class="text-muted fw-semibold">Đang nạp danh sách sản phẩm TechMart...</div>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredProducts.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="empty-icon-wrap mx-auto mb-3">
              <i class="bi bi-search text-muted fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Không tìm thấy sản phẩm nào!</h5>
            <p class="text-muted small mb-3">Thử thay đổi từ khóa tìm kiếm hoặc chọn lại bộ lọc danh mục/mức giá.</p>
            <div class="d-flex justify-content-center gap-2">
              <button class="btn btn-primary px-4 fw-bold" @click="resetAllFilters">
                <i class="bi bi-arrow-clockwise me-1"></i> Xem tất cả sản phẩm
              </button>
            </div>
          </div>

          <!-- Product Grid View -->
          <div v-else-if="viewMode === 'grid'" class="row g-3">
            <div
              v-for="item in filteredProducts"
              :key="item.id"
              class="col-12 col-sm-6 col-xl-4"
            >
              <div class="product-catalog-card h-100" @click="goToDetail(item.id)">
                <!-- Badge Giảm giá & BH -->
                <div class="card-badges">
                  <span v-if="getDiscountPercent(item) > 0" class="badge-discount">
                    -{{ getDiscountPercent(item) }}%
                  </span>
                  <span class="badge-ship"><i class="bi bi-lightning-fill"></i> Freeship 2H</span>
                </div>

                <!-- Nút Yêu thích -->
                <button
                  class="btn-wishlist-pill"
                  :class="{ active: isWishlisted(item.id) }"
                  @click.stop="toggleWishlist(item.id)"
                  title="Yêu thích"
                >
                  <i :class="isWishlisted(item.id) ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
                </button>

                <!-- Hình ảnh sản phẩm -->
                <div class="product-img-box">
                  <img
                    :src="item.anh_chinh || (item.images && item.images[0]?.duong_dan) || getPlaceholder(item.ten_san_pham)"
                    :alt="item.ten_san_pham"
                    loading="lazy"
                    @error="onImgError($event, item.ten_san_pham)"
                  />
                </div>

                <!-- Nội dung thẻ -->
                <div class="product-info-box">
                  <div class="brand-tag text-muted small mb-1">
                    {{ item.brand ? item.brand.ten_thuong_hieu : (item.category ? item.category.ten_danh_muc : 'TECHMART') }}
                  </div>
                  <h6 class="product-title fw-bold text-dark mb-2" :title="item.ten_san_pham">
                    {{ item.ten_san_pham }}
                  </h6>

                  <!-- Thông số chip ngắn gọn -->
                  <div class="specs-chips-row mb-2" v-if="item.specification">
                    <span class="spec-mini-chip" v-if="item.specification.cpu">
                      <i class="bi bi-cpu me-1"></i>{{ item.specification.cpu.split('(')[0] }}
                    </span>
                    <span class="spec-mini-chip" v-if="item.specification.ram">
                      {{ item.specification.ram }}
                    </span>
                    <span class="spec-mini-chip" v-if="item.specification.card_do_hoa">
                      {{ item.specification.card_do_hoa }}
                    </span>
                  </div>

                  <!-- Giá bán -->
                  <div class="price-box mt-auto pt-2">
                    <div class="current-price fw-bold text-danger fs-5">
                      {{ formatPrice(item.gia_khuyen_mai || item.gia_goc) }}
                    </div>
                    <div class="old-price-box" v-if="item.gia_khuyen_mai && item.gia_khuyen_mai < item.gia_goc">
                      <span class="old-price text-muted text-decoration-line-through small">
                        {{ formatPrice(item.gia_goc) }}
                      </span>
                    </div>
                  </div>

                  <!-- Nút thao tác -->
                  <div class="action-buttons-row mt-3 pt-2 border-top d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm flex-grow-1 fw-semibold" @click.stop="quickAddToCart(item)">
                      <i class="bi bi-cart-plus me-1"></i> Thêm giỏ
                    </button>
                    <button class="btn btn-light btn-sm px-2 text-muted" @click.stop="toggleCompare(item)" title="So sánh">
                      <i class="bi bi-arrow-left-right" :class="{ 'text-primary fw-bold': isCompared(item.id) }"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Product List View -->
          <div v-else class="d-flex flex-column gap-3">
            <div
              v-for="item in filteredProducts"
              :key="item.id"
              class="product-catalog-list-card card border-0 shadow-sm rounded-4 p-3"
              @click="goToDetail(item.id)"
            >
              <div class="row align-items-center g-3">
                <div class="col-md-3 text-center">
                  <img
                    :src="item.anh_chinh || getPlaceholder(item.ten_san_pham)"
                    :alt="item.ten_san_pham"
                    class="img-fluid rounded-3"
                    style="max-height: 160px; object-fit: contain;"
                    @error="onImgError($event, item.ten_san_pham)"
                  />
                </div>
                <div class="col-md-6">
                  <div class="brand-tag text-muted small mb-1">
                    {{ item.brand ? item.brand.ten_thuong_hieu : (item.category ? item.category.ten_danh_muc : 'TECHMART') }}
                  </div>
                  <h5 class="product-title fw-bold text-dark mb-2">{{ item.ten_san_pham }}</h5>
                  <p class="text-muted small mb-2 line-clamp-2">{{ item.mo_ta_ngan || 'Sản phẩm công nghệ chính hãng tại TechMart với bảo hành đầy đủ và hậu mãi chuyên nghiệp.' }}</p>
                  <div class="d-flex flex-wrap gap-2" v-if="item.specification">
                    <span class="badge bg-light text-dark border"><i class="bi bi-cpu me-1"></i>{{ item.specification.cpu }}</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-memory me-1"></i>{{ item.specification.ram }}</span>
                    <span class="badge bg-light text-dark border"><i class="bi bi-gpu-card me-1"></i>{{ item.specification.card_do_hoa }}</span>
                  </div>
                </div>
                <div class="col-md-3 text-md-end border-start-md ps-md-4">
                  <div class="current-price fw-bold text-danger fs-4">
                    {{ formatPrice(item.gia_khuyen_mai || item.gia_goc) }}
                  </div>
                  <div class="old-price text-muted text-decoration-line-through small mb-3" v-if="item.gia_khuyen_mai">
                    {{ formatPrice(item.gia_goc) }}
                  </div>
                  <div class="d-flex flex-column gap-2">
                    <button class="btn btn-primary btn-sm fw-bold" @click.stop="goToDetail(item.id)">
                      Xem chi tiết
                    </button>
                    <button class="btn btn-outline-primary btn-sm" @click.stop="quickAddToCart(item)">
                      <i class="bi bi-cart-plus me-1"></i> Thêm giỏ hàng
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- FLOATING COMPARE DOCK -->
    <div v-if="compareList.length > 0" class="floating-compare-dock shadow-lg border rounded-4 bg-white p-3">
      <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <span class="fw-bold small text-dark"><i class="bi bi-arrow-left-right text-primary me-1"></i> So sánh ({{ compareList.length }}/4):</span>
          <div class="d-flex gap-2">
            <span v-for="cp in compareList" :key="cp.id" class="badge bg-light text-dark border py-2 px-3 position-relative">
              {{ cp.ten_san_pham.substring(0, 18) }}...
              <button class="btn-close ms-2" style="font-size: 8px;" @click.stop="removeCompare(cp.id)"></button>
            </span>
          </div>
        </div>
        <div class="d-flex gap-2">
          <router-link to="/compare" class="btn btn-primary btn-sm px-3 fw-bold">So sánh ngay</router-link>
          <button class="btn btn-outline-secondary btn-sm" @click="clearCompare">Xóa</button>
        </div>
      </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div v-if="toastMsg" class="position-fixed bottom-0 end-0 m-4 p-3 bg-dark text-white rounded-3 shadow-lg d-flex align-items-center gap-2" style="z-index: 3000;">
      <i class="bi bi-check-circle-fill text-success fs-5"></i>
      <span class="small">{{ toastMsg }}</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { apiUrl } from '@/utils/api';
import { toast } from '@/utils/notify';

export default {
  name: 'TechMartProductCatalog',
  data() {
    return {
      isLoading: false,
      allProducts: [],
      filteredProducts: [],
      categories: [],
      brands: [],

      // Navigation & Filter state
      selectedCategoryKey: 'all', // all, laptop, pc-may-tram, smartphone, linh-kien-pc, gaming-gear, flash-sale, promo
      selectedCategoryId: null,
      selectedBrandIds: [],
      selectedPriceRangeIndex: 0,
      selectedRam: null,
      searchQuery: '',
      sortBy: 'newest',
      viewMode: 'grid',

      // User state
      currentUser: null,
      cartCount: 0,
      wishlistIds: [],
      compareList: [],

      // UI
      toastMsg: '',
      toastTimer: null,
      defaultImage: 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png',

      // Mức giá định nghĩa sẵn
      priceRanges: [
        { label: 'Tất cả mức giá', min: null, max: null },
        { label: 'Dưới 10 triệu', min: 0, max: 10000000 },
        { label: 'Từ 10 - 20 triệu', min: 10000000, max: 20000000 },
        { label: 'Từ 20 - 35 triệu', min: 20000000, max: 35000000 },
        { label: 'Trên 35 triệu', min: 35000000, max: null }
      ],

      // Cấu hình banner theo từng danh mục
      categoryBanners: {
        all: {
          title: 'KHO THIẾT BỊ CÔNG NGHỆ CHÍNH HÃNG TECHMART',
          desc: 'Khám phá hơn 1.800 sản phẩm Laptop, Màn hình, PC, Linh kiện và Gaming Gear hàng đầu với ưu đãi trả góp 0% và giao siêu tốc 2H.',
          tags: ['Laptop Gaming', 'Màn hình OLED', 'VGA RTX 4070', 'RAM DDR5', 'Bàn phím cơ']
        },
        laptop: {
          title: 'LAPTOP GAMING & MÁY TRẠM DOANH NHÂN CHÍNH HÃNG',
          desc: 'Tổng hợp các dòng Laptop ASUS ROG, Dell XPS, Apple MacBook, Lenovo Legion cấu hình mạnh mẽ, màn hình 2K/165Hz chuẩn màu.',
          tags: ['Laptop ASUS ROG', 'Dell XPS 15', 'Apple M3 Pro', 'Core i7-13700H', 'RTX 4060']
        },
        'pc-may-tram': {
          title: 'MÁY TÍNH BÀN PC & MÀN HÌNH ĐỒ HỌA CHUYÊN NGHIỆP',
          desc: 'Màn hình chuẩn màu LG UltraGear, Dell UltraSharp, Asus ROG OLED và case máy tính hiệu năng cao dành cho Designer & Gamer.',
          tags: ['LG UltraGear 27GR75Q', 'Dell UltraSharp U2724D', 'ASUS ROG OLED', '2K 165Hz', 'Màn hình 27 inch']
        },
        smartphone: {
          title: 'SMARTPHONE & THIẾT BỊ DI ĐỘNG CAO CẤP',
          desc: 'Điện thoại thông minh Flagship, iPad, máy tính bảng hàng chính hãng VN/A, bảo hành 1 đổi 1 và bảo hiểm thiết bị toàn diện.',
          tags: ['iPhone 15 Pro Max', 'Samsung Galaxy S24 Ultra', 'iPad Pro M4', 'Flagship']
        },
        'linh-kien-pc': {
          title: 'LINH KIỆN NÂNG CẤP MÁY TÍNH & CASE BUILD PC',
          desc: 'CPU Intel Core Gen 14, Card đồ họa RTX 40 series, RAM DDR5 tản nhiệt RGB, SSD NVMe tốc độ cao 7000MB/s.',
          tags: ['CPU i7-14700K', 'RTX 4070 Ti SUPER', 'RAM DDR5 6000MHz', 'SSD Samsung 990 Pro']
        },
        'gaming-gear': {
          title: 'GAMING GEAR & PHỤ KIỆN CHƠI GAME CHUYÊN NGHIỆP',
          desc: 'Chuột không dây siêu nhẹ Logitech Pro X, Bàn phím cơ Akko/Corsair Switch cao cấp, Tai nghe vòm 7.1 chuẩn E-Sports.',
          tags: ['Logitech Superlight 2', 'Corsair K70 RGB', 'AKKO 3087 RF', 'HyperX Cloud II']
        },
        'flash-sale': {
          title: 'FLASH SALE CÔNG NGHỆ • GIẢM ĐẾN 40%',
          desc: 'Ưu đãi chớp nhoáng giá tốt nhất thị trường, áp dụng số lượng có hạn mỗi ngày.',
          tags: ['Giảm sốc', 'Freeship 2H', 'Trả góp 0%']
        },
        promo: {
          title: 'CHƯƠNG TRÌNH KHUYẾN MÃI & QUÀ TẶNG CÔNG NGHỆ',
          desc: 'Nhận voucher giảm giá trực tiếp và combo quà tặng phụ kiện chính hãng khi mua sắm tại TechMart.',
          tags: ['Voucher 500K', 'Tặng Balo Gaming', 'Tặng Chuột không dây']
        }
      }
    };
  },
  computed: {
    heroBanner() {
      return this.categoryBanners[this.selectedCategoryKey] || this.categoryBanners.all;
    },
    currentCategoryTitle() {
      const titles = {
        all: 'Tất cả sản phẩm',
        laptop: 'Laptop & Máy tính xách tay',
        'pc-may-tram': 'PC & Màn hình máy tính',
        smartphone: 'Điện thoại & Smartphone',
        'linh-kien-pc': 'Linh kiện máy tính PC',
        'gaming-gear': 'Gaming Gear & Phụ kiện',
        'flash-sale': 'Flash Sale giá sốc',
        promo: 'Chương trình khuyến mãi'
      };
      return titles[this.selectedCategoryKey] || 'Danh mục sản phẩm';
    },
    availableBrands() {
      if (!this.selectedCategoryId) return this.brands;
      // Lọc các hãng có sản phẩm trong danh mục hiện tại
      const brandIdsInCat = new Set(
        this.allProducts
          .filter(p => p.danh_muc_id === this.selectedCategoryId && p.thuong_hieu_id)
          .map(p => p.thuong_hieu_id)
      );
      return this.brands.filter(b => brandIdsInCat.has(b.id));
    }
  },
  watch: {
    '$route.query': {
      immediate: true,
      handler(newQuery) {
        this.syncStateFromRoute(newQuery);
      }
    }
  },
  created() {
    this.resolveCurrentUser();
    this.loadMetadata();
    this.loadCompareList();
    this.loadWishlistIds();
  },
  methods: {
    getPlaceholder(name) {
      return 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png';
    },
    onImgError(e, name) {
      e.target.onerror = null;
      e.target.src = this.getPlaceholder(name);
    },
    resolveCurrentUser() {
      try {
        const u = localStorage.getItem('customer_user') || localStorage.getItem('admin_user');
        if (u) this.currentUser = JSON.parse(u);
      } catch (e) {}
    },
    async loadMetadata() {
      this.isLoading = true;
      try {
        const [catRes, brandRes, prodRes] = await Promise.allSettled([
          axios.get(apiUrl('/client/danh-muc/get-data')),
          axios.get(apiUrl('/client/thuong-hieu/get-data')),
          axios.get(apiUrl('/client/san-pham/get-data'))
        ]);

        if (catRes.status === 'fulfilled' && catRes.value.data?.status) {
          this.categories = catRes.value.data.data || [];
        }
        if (brandRes.status === 'fulfilled' && brandRes.value.data?.status) {
          this.brands = brandRes.value.data.data || [];
        }
        if (prodRes.status === 'fulfilled' && prodRes.value.data?.status) {
          this.allProducts = prodRes.value.data.data || [];
        }
      } catch (e) {
        console.error('Lỗi nạp metadata catalog:', e);
      } finally {
        this.isLoading = false;
        this.syncStateFromRoute(this.$route.query);
        this.applyFilters();
      }
    },

    syncStateFromRoute(query) {
      if (query.q) {
        this.searchQuery = query.q;
      }
      if (query.category) {
        const catKey = query.category.toLowerCase();
        this.selectedCategoryKey = catKey;
        // Map category key to category ID in DB
        this.mapNavKeyToCategoryId(catKey);
      } else if (query.filter) {
        this.selectedCategoryKey = query.filter.toLowerCase();
        this.selectedCategoryId = null;
      } else if (query.danh_muc_id) {
        this.selectedCategoryId = parseInt(query.danh_muc_id, 10);
        this.selectedCategoryKey = 'custom';
      } else {
        this.selectedCategoryKey = 'all';
        this.selectedCategoryId = null;
      }

      if (query.brand) {
        this.selectedBrandIds = query.brand.split(',').map(id => parseInt(id, 10)).filter(Boolean);
      }
      if (query.sort) {
        this.sortBy = query.sort;
      }
      this.applyFilters();
    },

    mapNavKeyToCategoryId(key) {
      if (!this.categories || this.categories.length === 0) return;
      if (key === 'laptop') {
        const found = this.categories.find(c => c.slug === 'laptop' || c.ten_danh_muc.toLowerCase().includes('laptop'));
        this.selectedCategoryId = found ? found.id : 1;
      } else if (key === 'pc-may-tram') {
        // Màn hình + linh kiện PC
        const found = this.categories.find(c => c.slug === 'man-hinh' || c.ten_danh_muc.toLowerCase().includes('màn hình'));
        this.selectedCategoryId = found ? found.id : 2;
      } else if (key === 'linh-kien-pc') {
        const found = this.categories.find(c => c.slug === 'linh-kien-pc' || c.ten_danh_muc.toLowerCase().includes('linh kiện'));
        this.selectedCategoryId = found ? found.id : 3;
      } else if (key === 'gaming-gear') {
        const found = this.categories.find(c => c.slug === 'gaming-gear' || c.ten_danh_muc.toLowerCase().includes('gear'));
        this.selectedCategoryId = found ? found.id : 4;
      } else if (key === 'smartphone') {
        const found = this.categories.find(c => c.slug === 'smartphone' || c.ten_danh_muc.toLowerCase().includes('thoại'));
        this.selectedCategoryId = found ? found.id : null;
      } else {
        this.selectedCategoryId = null;
      }
    },

    selectNavCategory(key) {
      this.selectedCategoryKey = key;
      this.selectedRam = null;
      this.selectedBrandIds = [];
      this.selectedPriceRangeIndex = 0;
      this.searchQuery = '';

      if (key === 'all') {
        this.$router.push({ path: '/catalog' });
      } else if (['flash-sale', 'promo'].includes(key)) {
        this.$router.push({ path: '/catalog', query: { filter: key } });
      } else {
        this.$router.push({ path: '/catalog', query: { category: key } });
      }
    },

    getCategoryProductCount(catId) {
      return this.allProducts.filter(p => {
        if (p.danh_muc_id === catId) return true;
        // Nếu là danh mục cha Linh kiện PC (id=3), tính cả RAM (5), SSD (6), VGA (7), CPU (8)
        if (catId === 3 && [5, 6, 7, 8].includes(p.danh_muc_id)) return true;
        return false;
      }).length;
    },

    applyFilters() {
      let list = [...this.allProducts];

      // 1. Lọc theo Danh mục
      if (this.selectedCategoryId) {
        const catId = this.selectedCategoryId;
        list = list.filter(p => {
          if (p.danh_muc_id === catId) return true;
          if (catId === 3 && [5, 6, 7, 8].includes(p.danh_muc_id)) return true;
          return false;
        });
      } else if (this.selectedCategoryKey === 'laptop') {
        list = list.filter(p => p.danh_muc_id === 1 || p.ten_san_pham.toLowerCase().includes('laptop'));
      } else if (this.selectedCategoryKey === 'pc-may-tram') {
        list = list.filter(p => p.danh_muc_id === 2 || [3, 5, 6, 7, 8].includes(p.danh_muc_id));
      } else if (this.selectedCategoryKey === 'linh-kien-pc') {
        list = list.filter(p => [3, 5, 6, 7, 8].includes(p.danh_muc_id));
      } else if (this.selectedCategoryKey === 'gaming-gear') {
        list = list.filter(p => p.danh_muc_id === 4 || p.ten_san_pham.toLowerCase().includes('chuột') || p.ten_san_pham.toLowerCase().includes('phím') || p.ten_san_pham.toLowerCase().includes('tai nghe'));
      } else if (this.selectedCategoryKey === 'flash-sale') {
        list = list.filter(p => p.gia_khuyen_mai && p.gia_khuyen_mai < p.gia_goc);
      }

      // 2. Lọc theo Khoảng giá
      const pr = this.priceRanges[this.selectedPriceRangeIndex];
      if (pr) {
        if (pr.min !== null) {
          list = list.filter(p => {
            const price = p.gia_khuyen_mai || p.gia_goc;
            return price >= pr.min;
          });
        }
        if (pr.max !== null) {
          list = list.filter(p => {
            const price = p.gia_khuyen_mai || p.gia_goc;
            return price <= pr.max;
          });
        }
      }

      // 3. Lọc theo Thương hiệu
      if (this.selectedBrandIds.length > 0) {
        list = list.filter(p => this.selectedBrandIds.includes(p.thuong_hieu_id));
      }

      // 4. Lọc theo RAM
      if (this.selectedRam) {
        list = list.filter(p => p.specification && p.specification.ram && p.specification.ram.includes(this.selectedRam));
      }

      // 5. Tìm kiếm từ khóa
      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase().trim();
        list = list.filter(p => {
          const matchName = p.ten_san_pham && p.ten_san_pham.toLowerCase().includes(q);
          const matchDesc = p.mo_ta_ngan && p.mo_ta_ngan.toLowerCase().includes(q);
          const matchBrand = p.brand && p.brand.ten_thuong_hieu && p.brand.ten_thuong_hieu.toLowerCase().includes(q);
          const matchSpec = p.specification && (
            (p.specification.cpu && p.specification.cpu.toLowerCase().includes(q)) ||
            (p.specification.card_do_hoa && p.specification.card_do_hoa.toLowerCase().includes(q)) ||
            (p.specification.ram && p.specification.ram.toLowerCase().includes(q))
          );
          return matchName || matchDesc || matchBrand || matchSpec;
        });
      }

      // Sắp xếp
      this.sortList(list);
      this.filteredProducts = list;
    },

    sortList(list) {
      if (this.sortBy === 'price_asc') {
        list.sort((a, b) => (a.gia_khuyen_mai || a.gia_goc) - (b.gia_khuyen_mai || b.gia_goc));
      } else if (this.sortBy === 'price_desc') {
        list.sort((a, b) => (b.gia_khuyen_mai || b.gia_goc) - (a.gia_khuyen_mai || a.gia_goc));
      } else if (this.sortBy === 'views') {
        list.sort((a, b) => (b.luot_xem || 0) - (a.luot_xem || 0));
      } else if (this.sortBy === 'name_asc') {
        list.sort((a, b) => a.ten_san_pham.localeCompare(b.ten_san_pham));
      } else {
        // newest
        list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      }
    },

    applySort() {
      this.applyFilters();
    },

    toggleBrand(brandId) {
      const idx = this.selectedBrandIds.indexOf(brandId);
      if (idx > -1) this.selectedBrandIds.splice(idx, 1);
      else this.selectedBrandIds.push(brandId);
      this.applyFilters();
    },

    toggleRam(ram) {
      this.selectedRam = this.selectedRam === ram ? null : ram;
      this.applyFilters();
    },

    searchByTag(tag) {
      this.searchQuery = tag;
      this.applyFilters();
    },

    resetAllFilters() {
      this.selectedCategoryKey = 'all';
      this.selectedCategoryId = null;
      this.selectedBrandIds = [];
      this.selectedPriceRangeIndex = 0;
      this.selectedRam = null;
      this.searchQuery = '';
      this.sortBy = 'newest';
      this.$router.push({ path: '/catalog' });
      this.applyFilters();
    },

    goToDetail(id) {
      this.$router.push(`/product/${id}`);
    },

    formatPrice(price) {
      if (!price) return '0 đ';
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    },

    getDiscountPercent(item) {
      if (!item.gia_khuyen_mai || item.gia_khuyen_mai >= item.gia_goc) return 0;
      return Math.round(((item.gia_goc - item.gia_khuyen_mai) / item.gia_goc) * 100);
    },

    // Cart logic
    async quickAddToCart(item) {
      const token = localStorage.getItem('customer_token') || localStorage.getItem('admin_token');
      if (!token) {
        this.$router.push(`/login?redirect=/catalog`);
        return;
      }
      try {
        const res = await axios.post(apiUrl('/client/gio-hang/them-vao-gio'), {
          san_pham_id: item.id,
          bien_the_id: item.variants && item.variants[0] ? item.variants[0].id : null,
          so_luong: 1,
          gia: item.gia_khuyen_mai || item.gia_goc,
          user_id: this.currentUser ? this.currentUser.id : null
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data && res.data.status) {
          this.cartCount++;
          this.showToast(`Đã thêm "${item.ten_san_pham}" vào giỏ hàng!`);
        }
      } catch (err) {
        this.showToast('Lỗi khi thêm vào giỏ hàng');
      }
    },

    // Wishlist logic
    loadWishlistIds() {
      try {
        const stored = localStorage.getItem('techmart_wishlist_ids');
        if (stored) this.wishlistIds = JSON.parse(stored);
      } catch (e) {}
    },
    isWishlisted(id) {
      return this.wishlistIds.includes(id);
    },
    async toggleWishlist(id) {
      const idx = this.wishlistIds.indexOf(id);
      if (idx > -1) {
        this.wishlistIds.splice(idx, 1);
        this.showToast('Đã xóa khỏi danh sách yêu thích');
      } else {
        this.wishlistIds.push(id);
        this.showToast('Đã thêm vào danh sách yêu thích!');
      }
      localStorage.setItem('techmart_wishlist_ids', JSON.stringify(this.wishlistIds));
      const token = localStorage.getItem('customer_token');
      if (token) {
        try {
          await axios.post(apiUrl('/client/yeu-thich/toggle'), { san_pham_id: id }, {
            headers: { Authorization: `Bearer ${token}` }
          });
        } catch (e) {}
      }
    },

    // Compare logic
    loadCompareList() {
      try {
        const stored = localStorage.getItem('techmart_compare_ids');
        if (stored) {
          const ids = JSON.parse(stored);
          this.compareList = ids.map(id => this.allProducts.find(p => p.id === id) || { id, ten_san_pham: 'Sản phẩm' });
        }
      } catch (e) {}
    },
    isCompared(id) {
      return this.compareList.some(p => p.id === id);
    },
    toggleCompare(item) {
      const idx = this.compareList.findIndex(p => p.id === item.id);
      if (idx > -1) {
        this.compareList.splice(idx, 1);
      } else {
        if (this.compareList.length >= 4) {
          toast.warning('Tối đa so sánh 4 sản phẩm cùng lúc!');
          return;
        }
        this.compareList.push(item);
      }
      localStorage.setItem('techmart_compare_ids', JSON.stringify(this.compareList.map(p => p.id)));
    },
    removeCompare(id) {
      this.compareList = this.compareList.filter(p => p.id !== id);
      localStorage.setItem('techmart_compare_ids', JSON.stringify(this.compareList.map(p => p.id)));
    },
    clearCompare() {
      this.compareList = [];
      localStorage.removeItem('techmart_compare_ids');
    },

    showToast(msg) {
      this.toastMsg = msg;
      clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => { this.toastMsg = ''; }, 3000);
    }
  }
};
</script>

<style scoped>
.catalog-page-wrapper {
  background-color: #f8fafc;
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.main-width {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 16px;
}

/* ❶ HEADER */
.catalog-header {
  background: #0f172a;
  border-bottom: 1px solid #1e293b;
  position: sticky;
  top: 0;
  z-index: 1000;
  padding: 12px 0;
}
.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.brand-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}
.logo-icon {
  width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #0284c7, #3b82f6);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  font-size: 18px;
  margin-right: 10px;
}
.logo-text {
  font-size: 20px;
  font-weight: 900;
  color: #fff;
  letter-spacing: 0.5px;
}
.badge-sub {
  background: rgba(2, 132, 199, 0.2);
  color: #38bdf8;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
}
.breadcrumb-trail {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}
.breadcrumb-item-link {
  color: #94a3b8;
  text-decoration: none;
}
.breadcrumb-item-link:hover {
  color: #38bdf8;
}
.separator {
  color: #475569;
  font-size: 10px;
}
.breadcrumb-current {
  color: #f1f5f9;
  font-weight: 600;
}

/* Search Box */
.search-box-pill {
  position: relative;
  display: flex;
  align-items: center;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 9999px;
  padding: 4px 6px 4px 14px;
}
.search-icon {
  color: #94a3b8;
  margin-right: 8px;
}
.search-input {
  background: transparent;
  border: none;
  outline: none;
  color: #f8fafc;
  font-size: 13px;
  width: 100%;
}
.search-input::placeholder {
  color: #64748b;
}
.search-btn {
  background: #0284c7;
  color: #fff;
  border: none;
  border-radius: 9999px;
  padding: 6px 16px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
}
.search-btn:hover {
  background: #0369a1;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.btn-top-action {
  background: #1e293b;
  color: #cbd5e1;
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
  position: relative;
  transition: all 0.2s;
}
.btn-top-action:hover {
  background: #334155;
  color: #fff;
}
.action-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 9999px;
  margin-left: 6px;
}
.user-info-pill {
  background: rgba(255, 255, 255, 0.08);
  padding: 6px 12px;
  border-radius: 9999px;
  color: #f8fafc;
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
}
.btn-login-link {
  background: #0284c7;
  color: #fff;
  text-decoration: none;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
}

/* ❷ NAVBAR */
.main-navbar {
  background: #1e293b;
  border-bottom: 1px solid #334155;
}
.navbar-inner {
  display: flex;
  align-items: center;
  gap: 16px;
  overflow-x: auto;
}
.nav-catalog-btn {
  background: #0284c7;
  color: #fff;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}
.nav-catalog-btn.active {
  background: #0369a1;
}
.nav-links {
  list-style: none;
  display: flex;
  align-items: center;
  margin: 0;
  padding: 0;
  gap: 6px;
}
.nav-link {
  color: #cbd5e1;
  text-decoration: none;
  padding: 10px 14px;
  font-size: 12.5px;
  font-weight: 700;
  white-space: nowrap;
  display: flex;
  align-items: center;
  border-bottom: 3px solid transparent;
  transition: all 0.2s;
}
.nav-link:hover {
  color: #38bdf8;
  background: rgba(255, 255, 255, 0.04);
}
.nav-link.active {
  color: #38bdf8;
  border-bottom-color: #38bdf8;
  background: rgba(56, 189, 248, 0.08);
}

/* ❸ HERO BANNER */
.category-hero-card {
  border-radius: 20px;
  padding: 36px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}
.theme-all { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.theme-laptop { background: linear-gradient(135deg, #0284c7, #1e1b4b); }
.theme-pc-may-tram { background: linear-gradient(135deg, #0d9488, #0f172a); }
.theme-smartphone { background: linear-gradient(135deg, #7c3aed, #1e1b4b); }
.theme-linh-kien-pc { background: linear-gradient(135deg, #b91c1c, #0f172a); }
.theme-gaming-gear { background: linear-gradient(135deg, #ea580c, #1e293b); }
.theme-flash-sale { background: linear-gradient(135deg, #e11d48, #4c0519); }
.theme-promo { background: linear-gradient(135deg, #059669, #064e3b); }

.badge-tag {
  display: inline-block;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  color: #f8fafc;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 9999px;
  letter-spacing: 0.5px;
}
.hero-title {
  font-size: 26px;
  letter-spacing: -0.5px;
}
.hero-desc {
  font-size: 14px;
  max-width: 650px;
  line-height: 1.5;
}
.btn-tag {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
}
.btn-tag:hover, .btn-tag-active {
  background: #fff;
  color: #0f172a;
}

/* ❹ SIDEBAR FILTERS */
.filter-sidebar {
  background: #fff;
}
.category-radio-list, .price-range-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.category-radio-item, .price-range-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.15s;
  color: #334155;
}
.category-radio-item:hover, .price-range-item:hover {
  background: #f1f5f9;
}
.category-radio-item.selected, .price-range-item.selected {
  background: #e0f2fe;
  color: #0369a1;
  font-weight: 600;
}
.category-radio-item input, .price-range-item input {
  margin-right: 8px;
}
.cat-count {
  margin-left: auto;
  font-size: 11.5px;
  color: #94a3b8;
}

/* Brand Chips */
.brand-chips-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.brand-chip {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 5px 10px;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  transition: all 0.15s;
}
.brand-chip:hover {
  border-color: #0284c7;
  color: #0284c7;
}
.brand-chip.active {
  background: #0284c7;
  border-color: #0284c7;
  color: #fff;
}

/* ❺ PRODUCT CARDS */
.product-catalog-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
.product-catalog-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}
.card-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  z-index: 2;
}
.badge-discount {
  background: #ef4444;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 6px;
}
.badge-ship {
  background: #10b981;
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 6px;
}
.btn-wishlist-pill {
  position: absolute;
  top: 12px;
  right: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  transition: all 0.15s;
}
.btn-wishlist-pill:hover {
  background: #fee2e2;
  border-color: #fca5a5;
}
.product-img-box {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}
.product-img-box img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.25s;
}
.product-catalog-card:hover .product-img-box img {
  transform: scale(1.05);
}
.product-info-box {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
.product-title {
  font-size: 13.5px;
  line-height: 1.4;
  height: 38px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.specs-chips-row {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}
.spec-mini-chip {
  background: #f1f5f9;
  color: #475569;
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 4px;
  white-space: nowrap;
}

/* Floating Compare Dock */
.floating-compare-dock {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  width: 90%;
  max-width: 800px;
  z-index: 1050;
  background: #fff;
  border-radius: 16px;
}
</style>
