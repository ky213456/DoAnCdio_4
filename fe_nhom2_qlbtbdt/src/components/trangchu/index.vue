<template>
  <div class="homepage-container">
    <!-- THANH TIẾN TRÌNH CUỘN TRANG (SCROLL PROGRESS INDICATOR) -->
    <div class="scroll-progress-bar" :style="{ width: scrollProgress + '%' }"></div>

    <!-- ========================================== -->
    <!-- ❶ CALLOUT 1: TOPBAR HỆ THỐNG -->
    <!-- ========================================== -->
    <header class="topbar">
      <div class="main-width topbar-inner">
        <div class="topbar-left">
          <span class="hotline-badge">
            <i class="bi bi-telephone-fill me-1 text-warning"></i> Hotline Kỹ thuật: <strong>1900.6789</strong>
          </span>
          <span class="divider">|</span>
          <span>
            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> Showroom: Đà Nẵng & Toàn quốc
          </span>
        </div>
        <div class="topbar-right">
          <a href="#" class="topbar-link">
            <i class="bi bi-shield-check me-1 text-info"></i> Tra cứu bảo hành điện tử (Serial/IMEI)
          </a>
          <span class="divider">|</span>
          <span class="lang-selector">
            <i class="bi bi-globe me-1"></i> Tiếng Việt
          </span>
          <span class="divider">|</span>
          <router-link to="/admin" class="topbar-link text-warning fw-bold">
            <i class="bi bi-speedometer2 me-1"></i> Admin BI Portal
          </router-link>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- ❷ - ❻ CALLOUT 2 - 6: MAIN HEADER -->
    <!-- ========================================== -->
    <div class="main-header-wrap">
      <div class="main-width main-header">
        <!-- ❷ Logo TechMart -->
        <div class="header-logo">
          <router-link to="/" class="logo-box">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <div class="logo-text">
              <span class="brand-name">TECHMART</span>
              <span class="brand-sub">Hệ Thống Bán Thiết Bị Điện Tử Số</span>
            </div>
          </router-link>
        </div>

        <!-- ❸ Thanh tìm kiếm Auto-suggest -->
        <div class="header-search">
          <div class="search-input-group">
            <input
              type="text"
              v-model="searchQuery"
              @focus="showSuggestions = true"
              @blur="setTimeout(() => showSuggestions = false, 200)"
              placeholder="Tìm kiếm laptop gaming, macbook, card rtx 4060, cpu intel..."
            />
            <button class="btn-search">
              <i class="bi bi-search me-1"></i> Tìm kiếm
            </button>
          </div>
          <!-- Auto-suggest Popup -->
          <div v-if="showSuggestions" class="search-suggestions">
            <div class="suggest-header"><i class="bi bi-fire me-1 text-danger"></i>Từ khóa tìm kiếm thịnh hành:</div>
            <div
              v-for="(item, idx) in searchSuggestions"
              :key="idx"
              class="suggest-item"
              @mousedown="searchQuery = item; showSuggestions = false"
            >
              <i class="bi bi-arrow-up-right me-2 text-muted"></i>{{ item }}
            </div>
          </div>
        </div>

        <!-- ❹ - ❻ Header Actions -->
        <div class="header-actions">
          <!-- ❹ Nút Trợ lý AI tư vấn -->
          <button class="btn-action-ai" @click="isAiChatOpen = true">
            <i class="bi bi-robot action-icon text-warning"></i>
            <div class="action-text">
              <span class="action-title">[ AI TƯ VẤN ]</span>
              <span class="action-sub">Hỏi cấu hình máy</span>
            </div>
          </button>

          <!-- ❺ Nút Giỏ hàng -->
          <router-link to="/cart" class="btn-action-cart" title="Xem giỏ hàng của bạn">
            <div class="cart-icon-wrap">
              <i class="bi bi-cart3 action-icon"></i>
              <span class="cart-badge" :class="{ 'badge-bounce': isCartBouncing }">{{ cartTotalCount }}</span>
            </div>
            <span class="action-title d-none d-md-inline ms-1">Giỏ hàng</span>
          </router-link>

          <!-- ❻ Menu Người dùng / Đăng nhập / Đăng ký -->
          <div class="user-menu-wrapper" v-if="currentUser">
            <button class="btn-user" @click="showUserMenu = !showUserMenu">
              <img v-if="currentUser.anh_dai_dien" :src="currentUser.anh_dai_dien" class="user-avatar-img" />
              <i v-else class="bi bi-person-circle user-avatar"></i>
              <span class="user-name">{{ currentUser.ho_ten }}</span>
              <i class="bi bi-chevron-down dropdown-arrow"></i>
            </button>
            <div v-if="showUserMenu" class="user-dropdown">
              <div class="user-dropdown-header">
                <strong class="user-dropdown-name">{{ currentUser.ho_ten }}</strong>
                <span class="user-dropdown-email">{{ currentUser.email }}</span>
              </div>
              <hr class="dropdown-divider" />
              <a href="#" class="user-dropdown-item"><i class="bi bi-person me-2"></i>Hồ sơ cá nhân</a>
              <router-link to="/cart" class="user-dropdown-item"><i class="bi bi-cart3 me-2"></i>Giỏ hàng của tôi</router-link>
              <a href="#" class="user-dropdown-item"><i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi</a>
              <a href="#" class="user-dropdown-item"><i class="bi bi-shield-check me-2"></i>Tra cứu bảo hành</a>
              <router-link to="/admin" v-if="currentUser.vai_tro === 'admin' || currentUser.vai_tro === 'staff'" class="user-dropdown-item text-primary"><i class="bi bi-gear me-2"></i>Vào Admin Portal</router-link>
              <hr class="dropdown-divider" />
              <a href="javascript:void(0)" @click="handleLogout" class="user-dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a>
            </div>
          </div>
          <!-- Khi chưa đăng nhập: Nút Đăng nhập / Đăng ký -->
          <div class="guest-auth-actions" v-else>
            <button class="btn-header-login" @click="openAuthModal('login')">
              <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
            </button>
            <router-link to="/register" class="btn-header-register">
              <i class="bi bi-person-plus me-1"></i> Đăng ký
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- ❼ CALLOUT 7: MEGA MENU / NAVIGATION BAR (STICKY NAV) -->
    <!-- ========================================== -->
    <nav :class="['main-navbar', { 'sticky-nav': isSticky }]">
      <div class="main-width navbar-inner">
        <div class="nav-catalog-btn">
          <i class="bi bi-grid-3x3-gap-fill menu-bars"></i>
          <span class="catalog-text">TẤT CẢ DANH MỤC</span>
        </div>
        <ul class="nav-links">
          <li class="nav-item"><a href="#" class="nav-link active"><i class="bi bi-laptop me-1"></i> LAPTOP</a></li>
          <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-pc-display me-1"></i> PC & MÁY TRẠM</a></li>
          <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-phone me-1"></i> SMARTPHONE</a></li>
          <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-cpu me-1"></i> LINH KIỆN PC</a></li>
          <li class="nav-item"><a href="#" class="nav-link"><i class="bi bi-controller me-1"></i> PHỤ KIỆN</a></li>
          <li class="nav-item"><a href="#" class="nav-link flash-link"><i class="bi bi-lightning-fill text-warning me-1"></i> FLASH SALE</a></li>
          <li class="nav-item"><a href="#" class="nav-link promo-link"><i class="bi bi-gift-fill text-danger me-1"></i> KHUYẾN MÃI</a></li>
        </ul>
      </div>
    </nav>

    <!-- ========================================== -->
    <!-- ❽ - ❾ CALLOUT 8 - 9: HERO BANNER SECTION (SLIDER CÓ HÌNH ẢNH THỰC TẾ) -->
    <!-- ========================================== -->
    <section class="hero-section main-width scroll-reveal">
      <div class="hero-grid">
        <!-- ❽ Hero Banner Chính (Trái) với Slider & Hình Ảnh Đẹp -->
        <div class="hero-main-banner">
          <div class="hero-banner-content">
            <div class="banner-badge">
              <i class="bi bi-fire text-danger me-1"></i> {{ currentSlide.badge }}
            </div>
            <h1 class="banner-title hero-title-anim" :key="'title-' + currentHeroIndex">
              {{ currentSlide.title }}<br />
              <span class="banner-highlight">{{ currentSlide.subTitle }}</span>
            </h1>
            <ul class="banner-perks hero-perks-anim" :key="'perks-' + currentHeroIndex">
              <li v-for="(perk, pIndex) in currentSlide.perks" :key="pIndex">
                <i class="bi bi-check-circle-fill text-success me-2"></i> {{ perk }}
              </li>
            </ul>
            <div class="banner-action hero-cta-anim">
              <button class="btn-hero-cta">
                {{ currentSlide.ctaText }} <i class="bi bi-chevron-double-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- Banner Visual với Hình Ảnh Sản Phẩm Thực Tế & Hiệu Ứng Lơ Lửng 3D Sống Động -->
          <div class="banner-slider-box">
            <div class="banner-image-wrapper" :key="'img-wrap-' + currentHeroIndex">
              <!-- Khối thiết bị lơ lửng chuyển động nổi 3D -->
              <div class="floating-device-container">
                <img :src="currentSlide.image" :alt="currentSlide.title" class="banner-product-img" />
                <div class="banner-img-glow"></div>
                <div class="banner-floating-tag">
                  <i class="bi bi-cpu-fill text-warning me-1"></i> {{ currentSlide.tag }}
                </div>
              </div>
              <!-- Bóng đổ 3D co dãn theo nhịp lơ lửng -->
              <div class="banner-shadow-3d"></div>
            </div>

            <!-- Slider Controls (Dots & Navigation Arrows) -->
            <div class="slider-controls-wrap">
              <button class="btn-slide-nav" @click="prevHeroSlide" title="Slide trước"><i class="bi bi-chevron-left"></i></button>
              <div class="slider-dots">
                <span
                  v-for="(slide, sIdx) in heroSlides"
                  :key="sIdx"
                  :class="['dot', { active: currentHeroIndex === sIdx }]"
                  @click="setHeroSlide(sIdx)"
                ></span>
              </div>
              <button class="btn-slide-nav" @click="nextHeroSlide" title="Slide tiếp"><i class="bi bi-chevron-right"></i></button>
            </div>
          </div>
        </div>

        <!-- ❾ 2 Banner Phụ (Phải) có Hình Ảnh Thực Tế & Hiệu Ứng Hover -->
        <div class="hero-sub-banners">
          <!-- Sub-banner 1: Build PC Gaming -->
          <div class="sub-banner-card sub-card-pc" style="background-image: url('https://images.unsplash.com/photo-1587202372634-32705e3bf49c?auto=format&fit=crop&w=600&q=80')">
            <div class="sub-banner-overlay"></div>
            <div class="sub-banner-content">
              <div class="sub-badge"><i class="bi bi-tools me-1 text-primary"></i> DỊCH VỤ CHUYÊN NGHIỆP</div>
              <h3 class="sub-title">BUILD PC GAMING THEO YÊU CẦU</h3>
              <p class="sub-desc">
                Giảm ngay 2.000.000đ khi ráp kèm Card RTX.<br />
                Trợ lý AI tự động kiểm tra tương thích linh kiện.
              </p>
              <a href="#" class="sub-link">Xem chi tiết <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
          </div>

          <!-- Sub-banner 2: Smartphone Flagship -->
          <div class="sub-banner-card sub-card-mobile" style="background-image: url('https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80')">
            <div class="sub-banner-overlay"></div>
            <div class="sub-banner-content">
              <div class="sub-badge"><i class="bi bi-stars me-1 text-warning"></i> FLAGSHIP MỚI NHẤT</div>
              <h3 class="sub-title">IPHONE 16 PRO MAX & S24 ULTRA</h3>
              <p class="sub-desc">
                Thu cũ đổi mới trợ giá lên tới 3.000.000đ.<br />
                Bảo hành điện tử 1 đổi 1 trong 30 ngày.
              </p>
              <a href="#" class="sub-link">Khám phá ngay <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ❿ - ⓫ CALLOUT 10 - 11: FLASH SALE GIÁ SỐC -->
    <!-- ========================================== -->
    <section class="flash-sale-section main-width scroll-reveal">
      <!-- ❿ Flash Sale Header & Timer -->
      <div class="flash-sale-header">
        <div class="flash-title-wrap">
          <i class="bi bi-lightning-charge-fill flash-bolt text-danger"></i>
          <span class="flash-title">FLASH SALE GIÁ SỐC</span>
        </div>
        <div class="flash-timer-wrap">
          <span class="timer-label"><i class="bi bi-alarm me-1"></i> Kết thúc trong:</span>
          <div class="timer-boxes">
            <span class="timer-box">{{ String(hours).padStart(2, '0') }}</span>
            <span class="timer-colon">:</span>
            <span class="timer-box">{{ String(minutes).padStart(2, '0') }}</span>
            <span class="timer-colon">:</span>
            <span class="timer-box">{{ String(seconds).padStart(2, '0') }}</span>
          </div>
        </div>
      </div>

      <!-- ⓫ Lưới sản phẩm Flash Sale (Hiển thị đa dạng 8 sản phẩm các ngành hàng) -->
      <div class="product-grid-4-cols flash-sale-grid">
        <!-- Render từ API nếu có dữ liệu -->
        <template v-if="list_flash_sale && list_flash_sale.length > 0">
          <div v-for="(item, index) in list_flash_sale.slice(0, 8)" :key="item.id || index" class="product-card flash-card">
            <div class="discount-badge"><i class="bi bi-fire me-1"></i>HOT SALE</div>
            <div class="product-img-box">
              <img v-if="item.anh_chinh" :src="item.anh_chinh" :alt="item.ten_san_pham" />
              <div v-else class="img-mock-box">
                <i class="bi bi-laptop fs-1 text-primary"></i>
                <span class="img-placeholder">TechMart 360</span>
              </div>
            </div>
            <h4 class="product-title" :title="item.ten_san_pham">{{ item.ten_san_pham }}</h4>
            <p class="product-specs">{{ item.mo_ta_ngan || 'Bảo hành chính hãng 24 tháng theo số Serial/IMEI' }}</p>
            <div class="product-pricing">
              <span class="price-current">{{ formatCurrency(item.gia_khuyen_mai || item.gia_goc) }}</span>
              <span v-if="item.gia_khuyen_mai" class="price-original">{{ formatCurrency(item.gia_goc) }}</span>
            </div>
            <div class="progress-container">
              <div class="progress-bar" style="width: 80%;"></div>
              <span class="progress-text"><i class="bi bi-lightning-fill me-1"></i>ĐANG BÁN CHẠY</span>
            </div>
          </div>
        </template>

        <!-- Dữ liệu Mockup tĩnh mặc định nếu chưa seed DB -->
        <template v-else>
          <!-- Card 1 -->
          <div class="product-card flash-card">
            <div class="discount-badge"><i class="bi bi-fire me-1"></i>-19%</div>
            <div class="product-img-box">
              <div class="img-mock-box">
                <i class="bi bi-laptop fs-1 text-primary"></i>
                <span class="img-placeholder">ASUS ROG G16</span>
              </div>
            </div>
            <h4 class="product-title">Laptop ASUS ROG Strix G16 2026</h4>
            <p class="product-specs">i7-13650HX | 16GB RAM | 512GB SSD | RTX 4060 8GB</p>
            <div class="product-pricing">
              <span class="price-current">29.990.000đ</span>
              <span class="price-original">36.990.000đ</span>
            </div>
            <div class="progress-container">
              <div class="progress-bar" style="width: 90%;"></div>
              <span class="progress-text"><i class="bi bi-fire me-1"></i>ĐÃ BÁN 18/20</span>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="product-card flash-card">
            <div class="discount-badge"><i class="bi bi-fire me-1"></i>-13%</div>
            <div class="product-img-box">
              <div class="img-mock-box">
                <i class="bi bi-apple fs-1 text-dark"></i>
                <span class="img-placeholder">MacBook Pro M3</span>
              </div>
            </div>
            <h4 class="product-title">Apple MacBook Pro 14 M3 Chip</h4>
            <p class="product-specs">M3 Chip 8-Core CPU | 18GB Unified | 512GB SSD</p>
            <div class="product-pricing">
              <span class="price-current">37.490.000đ</span>
              <span class="price-original">42.990.000đ</span>
            </div>
            <div class="progress-container">
              <div class="progress-bar" style="width: 60%;"></div>
              <span class="progress-text"><i class="bi bi-fire me-1"></i>ĐÃ BÁN 9/15</span>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="product-card flash-card">
            <div class="discount-badge"><i class="bi bi-fire me-1"></i>-11%</div>
            <div class="product-img-box">
              <div class="img-mock-box">
                <i class="bi bi-pc-display-horizontal fs-1 text-info"></i>
                <span class="img-placeholder">PC Ultra 7</span>
              </div>
            </div>
            <h4 class="product-title">PC Gaming Intel Ultra 7 Ráp Sẵn</h4>
            <p class="product-specs">i7-14700F | 32GB RAM Bus 6000 | 1TB NVMe | RTX 4070S</p>
            <div class="product-pricing">
              <span class="price-current">39.900.000đ</span>
              <span class="price-original">45.000.000đ</span>
            </div>
            <div class="progress-container">
              <div class="progress-bar" style="width: 100%;"></div>
              <span class="progress-text"><i class="bi bi-check-circle-fill me-1"></i>CHÁY HÀNG 12/12</span>
            </div>
          </div>

          <!-- Card 4 -->
          <div class="product-card flash-card">
            <div class="discount-badge"><i class="bi bi-fire me-1"></i>-19%</div>
            <div class="product-img-box">
              <div class="img-mock-box">
                <i class="bi bi-gpu-card fs-1 text-danger"></i>
                <span class="img-placeholder">VGA RTX 4060</span>
              </div>
            </div>
            <h4 class="product-title">Card Màn Hình MSI RTX 4060 OC</h4>
            <p class="product-specs">Ventus 2X 8GB GDDR6 OC Edition Dual Fan</p>
            <div class="product-pricing">
              <span class="price-current">7.290.000đ</span>
              <span class="price-original">8.990.000đ</span>
            </div>
            <div class="progress-container">
              <div class="progress-bar" style="width: 83%;"></div>
              <span class="progress-text"><i class="bi bi-fire me-1"></i>ĐÃ BÁN 25/30</span>
            </div>
          </div>
        </template>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ⓬ CALLOUT 12: DANH MỤC THIẾT BỊ NỔI BẬT -->
    <!-- ========================================== -->
    <section class="categories-section main-width scroll-reveal">
      <h2 class="section-title"><i class="bi bi-grid-fill me-2 text-primary"></i>DANH MỤC THIẾT BỊ NỔI BẬT</h2>
      <div class="categories-grid">
        <div class="category-item">
          <div class="cat-icon-box cat-blue"><i class="bi bi-laptop"></i></div>
          <span class="cat-name">Laptop Gaming</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-slate"><i class="bi bi-briefcase-fill"></i></div>
          <span class="cat-name">Laptop Văn Phòng</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-dark"><i class="bi bi-apple"></i></div>
          <span class="cat-name">Macbook - Apple</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-cyan"><i class="bi bi-pc-display"></i></div>
          <span class="cat-name">PC Ráp Sẵn</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-rose"><i class="bi bi-gpu-card"></i></div>
          <span class="cat-name">Card VGA</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-amber"><i class="bi bi-cpu-fill"></i></div>
          <span class="cat-name">CPU Vi Xử Lý</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-emerald"><i class="bi bi-display"></i></div>
          <span class="cat-name">Màn Hình 2K/4K</span>
        </div>
        <div class="category-item">
          <div class="cat-icon-box cat-indigo"><i class="bi bi-headset"></i></div>
          <span class="cat-name">Phụ Kiện Gaming</span>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ⓭ CALLOUT 13: GỢI Ý THIẾT BỊ BÁN CHẠY (RED SHOWCASE CAROUSEL BANNER) -->
    <!-- ========================================== -->
    <section class="best-seller-showcase-section main-width scroll-reveal">
      <!-- Tiêu đề Header nổi bật nền đỏ với chữ nghệ thuật rỗng viền trắng -->
      <div class="showcase-header text-center">
        <div class="title-with-badge">
          <span class="fire-badge"><i class="bi bi-fire"></i></span>
          <span class="text-white-bold">SẢN PHẨM</span>
          <span class="text-outline-glow">NỔI BẬT</span>
        </div>
      </div>

      <!-- Khung Carousel Trượt Ngang với nút bấm trái/phải -->
      <div class="showcase-carousel-wrapper">
        <button class="btn-carousel-nav btn-carousel-prev" @click="scrollBestSellers('left')" title="Xem sản phẩm trước">
          <i class="bi bi-chevron-left"></i>
        </button>

        <div class="showcase-cards-container" ref="bestSellerScroll">
          <div
            v-for="(item, index) in (list_sp_noi_bat.length > 0 ? list_sp_noi_bat : list_san_pham)"
            :key="item.id || index"
            class="showcase-product-card"
          >
            <!-- Badges góc trên thẻ -->
            <div class="card-top-badges">
              <span class="badge-discount">
                Giảm {{ calculateDiscount(item.gia_goc, item.gia_khuyen_mai, index) }}%
              </span>
              <span class="badge-installment">Trả góp 0%</span>
            </div>

            <!-- Khung hình ảnh sản phẩm & icon đồ họa -->
            <div class="card-visual-box">
              <img v-if="item.anh_chinh" :src="item.anh_chinh" :alt="item.ten_san_pham" class="showcase-img" />
              <div v-else class="img-mock-box">
                <i class="bi bi-laptop fs-1 text-primary"></i>
                <span class="img-placeholder">TechMart 360</span>
              </div>
              <!-- Dải icon ứng dụng thiết kế nhỏ xinh bên phải -->
              <div class="app-icons-strip">
                <span class="app-icon app-ai" title="Thiết kế Illustrator">Ai</span>
                <span class="app-icon app-ps" title="Chỉnh ảnh Photoshop">Ps</span>
                <span class="app-icon app-office" title="Văn phòng Office">W</span>
              </div>
            </div>

            <!-- Khung thông số kỹ thuật dạng pills box (chuẩn như ảnh) -->
            <div class="card-specs-box">
              <div class="spec-line-1">
                {{ extractCpuGpu(item) }}
              </div>
              <div class="spec-line-2">
                {{ extractRamScreen(item) }}
              </div>
            </div>

            <!-- Tên sản phẩm -->
            <h4 class="showcase-prod-title" :title="item.ten_san_pham">
              {{ item.ten_san_pham }}
            </h4>

            <!-- Giá bán -->
            <div class="showcase-pricing">
              <span class="showcase-price-current">
                {{ formatCurrency(item.gia_khuyen_mai || item.gia_goc) }}
              </span>
              <span v-if="item.gia_khuyen_mai" class="showcase-price-original">
                {{ formatCurrency(item.gia_goc) }}
              </span>
            </div>

            <!-- Footer thẻ: Giao siêu tốc 2 Giờ & Nút Thêm Giỏ & Tim Yêu Thích -->
            <div class="showcase-card-footer">
              <div class="badge-shipping-speed">
                <i class="bi bi-truck me-1"></i> 2 Giờ
              </div>
              <div class="showcase-actions-group">
                <button
                  class="btn-showcase-add-cart"
                  @click.stop="themVaoGio(item)"
                  title="Thêm vào giỏ hàng"
                  :disabled="item.isAdding"
                >
                  <i class="bi bi-arrow-repeat spin" v-if="item.isAdding"></i>
                  <i class="bi bi-cart-plus-fill" v-else></i>
                </button>
                <button
                  class="btn-wishlist"
                  :class="{ active: isWishlisted(item.id) }"
                  @click="toggleWishlist(item.id)"
                  title="Lưu vào danh sách yêu thích"
                >
                  <i :class="isWishlisted(item.id) ? 'bi bi-heart-fill text-danger' : 'bi bi-heart'"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <button class="btn-carousel-nav btn-carousel-next" @click="scrollBestSellers('right')" title="Xem sản phẩm tiếp">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ⓭.1 CALLOUT MỚI: TẤT CẢ SẢN PHẨM CÔNG NGHỆ -->
    <!-- ========================================== -->
    <section class="all-products-section main-width scroll-reveal">
      <div class="section-header-flex">
        <div>
          <h2 class="section-title mb-1">
            <i class="bi bi-collection-fill me-2 text-primary"></i>TẤT CẢ SẢN PHẨM CÔNG NGHỆ
            <span class="badge bg-primary ms-2 fs-6 rounded-pill px-3">{{ list_all_san_pham.length }} sản phẩm</span>
          </h2>
          <p class="section-subtitle">Toàn bộ thiết bị chính hãng tại TechMart: Laptop, Màn hình, VGA, CPU, RAM, SSD và Gaming Gear</p>
        </div>
        <div class="header-action-wrap">
          <button class="btn-toggle-view-all-top" @click="isExpandedAll = !isExpandedAll">
            <template v-if="!isExpandedAll">
              <i class="bi bi-arrows-expand me-1"></i> Xem tất cả ({{ list_all_san_pham.length }})
            </template>
            <template v-else>
              <i class="bi bi-arrows-collapse me-1"></i> Thu gọn (còn 8)
            </template>
          </button>
        </div>
      </div>

      <!-- Lưới 4 cột chuẩn e-commerce (Mặc định 8 sản phẩm vừa khít 2 hàng, bấm xem tất cả thì bung hết) -->
      <div class="product-grid-4-cols all-products-grid">
        <div
          v-for="(item, index) in displayedAllProducts"
          :key="item.id || index"
          class="product-card"
        >
          <div class="tag-badge">
            <i class="bi bi-patch-check-fill me-1 text-primary"></i>{{ item.ten_thuong_hieu || item.ten_danh_muc || 'Chính hãng' }}
          </div>
          <div class="product-img-box">
            <img v-if="item.anh_chinh" :src="item.anh_chinh" :alt="item.ten_san_pham" />
            <div v-else class="img-mock-box">
              <i class="bi bi-cpu fs-1 text-primary"></i>
              <span class="img-placeholder">TechMart 360</span>
            </div>
          </div>
          <h4 class="product-title" :title="item.ten_san_pham">{{ item.ten_san_pham }}</h4>
          <p class="product-specs">{{ item.mo_ta_ngan || 'Hàng chính hãng fullbox, bảo hành chính hãng 24T' }}</p>
          <div class="product-pricing">
            <span class="price-current">{{ formatCurrency(item.gia_khuyen_mai || item.gia_goc) }}</span>
            <span v-if="item.gia_khuyen_mai" class="price-original ms-2">{{ formatCurrency(item.gia_goc) }}</span>
          </div>
          <div class="card-btn-group">
            <button class="btn-card-detail" @click="$router.push('/cart')"><i class="bi bi-eye me-1"></i>Chi tiết</button>
            <button class="btn-card-add" @click="themVaoGio(item)" :disabled="item.isAdding">
              <i class="bi bi-arrow-repeat spin me-1" v-if="item.isAdding"></i>
              <i class="bi bi-cart-plus me-1" v-else></i>
              <span>{{ item.isAdding ? 'Đang thêm...' : '+ Thêm giỏ' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Nút xem tất cả / thu gọn bớt ở giữa bên dưới -->
      <div class="view-all-container">
        <button class="btn-toggle-view-all" @click="isExpandedAll = !isExpandedAll">
          <template v-if="!isExpandedAll">
            <span>Xem tất cả sản phẩm (Còn {{ Math.max(0, list_all_san_pham.length - 8) }} sản phẩm nữa)</span>
            <i class="bi bi-chevron-down ms-2"></i>
          </template>
          <template v-else>
            <span>Thu gọn bớt (chỉ hiển thị 8 sản phẩm)</span>
            <i class="bi bi-chevron-up ms-2"></i>
          </template>
        </button>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ⓮ CALLOUT 14: THƯƠNG HIỆU & 4 CAM KẾT DỊCH VỤ -->
    <!-- ========================================== -->
    <section class="brand-and-commitments main-width scroll-reveal">
      <!-- Thương hiệu chính hãng -->
      <div class="brands-wrapper">
        <h3 class="brands-title"><i class="bi bi-patch-check-fill me-2 text-primary"></i>THƯƠNG HIỆU CÔNG NGHỆ CHÍNH HÃNG</h3>
        <div class="brands-grid">
          <div class="brand-box">APPLE</div>
          <div class="brand-box">ASUS ROG</div>
          <div class="brand-box">DELL</div>
          <div class="brand-box">HP</div>
          <div class="brand-box">LENOVO</div>
          <div class="brand-box">MSI</div>
          <div class="brand-box">GIGABYTE</div>
          <div class="brand-box">SAMSUNG</div>
        </div>
      </div>

      <!-- 4 Cam kết dịch vụ -->
      <div class="commitments-grid">
        <div class="commit-item">
          <div class="commit-icon-wrap text-success"><i class="bi bi-shield-fill-check"></i></div>
          <div class="commit-content">
            <h4 class="commit-name">100% Chính Hãng</h4>
            <p class="commit-desc">Cam kết hàng nguyên seal, đền 200% nếu phát hiện giả</p>
          </div>
        </div>
        <div class="commit-item">
          <div class="commit-icon-wrap text-warning"><i class="bi bi-lightning-charge-fill"></i></div>
          <div class="commit-content">
            <h4 class="commit-name">Giao Siêu Tốc 2H</h4>
            <p class="commit-desc">Giao hàng hỏa tốc nội thành có bảo hiểm giá trị cao</p>
          </div>
        </div>
        <div class="commit-item">
          <div class="commit-icon-wrap text-info"><i class="bi bi-upc-scan"></i></div>
          <div class="commit-content">
            <h4 class="commit-name">Bảo Hành Serial/IMEI</h4>
            <p class="commit-desc">Tra cứu thời hạn bảo hành điện tử chính xác theo thân máy</p>
          </div>
        </div>
        <div class="commit-item">
          <div class="commit-icon-wrap text-primary"><i class="bi bi-robot"></i></div>
          <div class="commit-content">
            <h4 class="commit-name">Trợ Lý AI 24/7</h4>
            <p class="commit-desc">Tư vấn cấu hình chuẩn xác mọi lúc mọi nơi theo ngân sách</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ========================================== -->
    <!-- ⓯ - ⓰ CALLOUT 15 - 16: FOOTER CHUẨN THƯƠNG MẠI -->
    <!-- ========================================== -->
    <footer class="main-footer scroll-reveal">
      <div class="main-width footer-grid">
        <!-- Cột 1: Thông tin công ty -->
        <div class="footer-col">
          <h5 class="footer-heading">TECHMART AI - HỆ THỐNG THIẾT BỊ ĐIỆN TỬ SỐ</h5>
          <p class="footer-text">
            Chuyên kinh doanh laptop, smartphone, máy tính để bàn và linh kiện PC.<br />
            Tích hợp Trợ lý Trí tuệ Nhân tạo tư vấn cấu hình theo nhu cầu.
          </p>
          <div class="footer-contact-item">
            <i class="bi bi-geo-alt-fill me-2 text-danger"></i> Showroom: Hòa Khánh Bắc, Liên Chiểu, TP. Đà Nẵng
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-telephone-fill me-2 text-primary"></i> Hotline: 1900.6789 - Email: contact@techmart-ai.vn
          </div>
        </div>

        <!-- Cột 2: Chính sách dịch vụ -->
        <div class="footer-col">
          <h5 class="footer-heading">CHÍNH SÁCH DỊCH VỤ</h5>
          <ul class="footer-links">
            <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Chính sách bảo hành điện tử 1 đổi 1</a></li>
            <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Hướng dẫn tra cứu Serial / IMEI máy</a></li>
            <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Quy định đổi trả hàng trong 30 ngày</a></li>
            <li><a href="#"><i class="bi bi-chevron-right me-1"></i> Chính sách bảo mật thông tin</a></li>
          </ul>
        </div>

        <!-- Cột 3: Phương thức thanh toán -->
        <div class="footer-col">
          <h5 class="footer-heading">PHƯƠNG THỨC THANH TOÁN</h5>
          <ul class="footer-links">
            <li><i class="bi bi-qr-code me-2"></i> Cổng thanh toán trực tuyến VNPAY QR</li>
            <li><i class="bi bi-bank me-2"></i> Thanh toán chuyển khoản tự động PayOS</li>
            <li><i class="bi bi-credit-card me-2"></i> Trả góp 0% qua thẻ tín dụng</li>
            <li><i class="bi bi-cash-stack me-2"></i> Thanh toán tiền mặt khi nhận hàng (COD)</li>
          </ul>
        </div>

        <!-- Cột 4: Hỗ trợ khách hàng -->
        <div class="footer-col">
          <h5 class="footer-heading">HỖ TRỢ KHÁCH HÀNG</h5>
          <p class="hotline-highlight">Hotline Bán hàng: <strong>1900.6789</strong></p>
          <p class="hotline-highlight">Hotline Kỹ thuật: <strong>0987.070.219</strong></p>
          <p class="text-muted small">Thời gian làm việc: 8:00 - 21:30</p>
          <p class="text-muted small">Email: cskh@techmart-ai.vn</p>
        </div>
      </div>

      <!-- ⓰ Copyright Bar -->
      <div class="copyright-bar">
        <div class="main-width text-center">
          © 2026 TechMart AI - Nhóm 1 Đồ án CS445 Đại Học Duy Tân. All rights reserved.
        </div>
      </div>
    </footer>

    <!-- ========================================== -->
    <!-- ⓱ CALLOUT 17: FLOATING AI CHAT WIDGET -->
    <!-- ========================================== -->
    <div class="floating-ai-widget">
      <!-- Nút tròn nổi AI với sóng Radar Animation -->
      <button class="ai-float-btn" @click="isAiChatOpen = !isAiChatOpen" title="Trợ lý AI tư vấn cấu hình máy">
        <i class="bi bi-robot"></i>
        <span class="ai-btn-label">AI TƯ VẤN</span>
      </button>

      <!-- Khung chat AI Popover -->
      <div v-if="isAiChatOpen" class="ai-chat-box shadow-lg">
        <div class="chat-header">
          <div class="chat-header-left">
            <i class="bi bi-robot text-warning me-2 fs-5"></i>
            <div>
              <div class="chat-header-title">Trợ Lý AI TechMart</div>
              <div class="chat-header-status">
                <span class="status-dot"></span> Gemini RAG • Khóa phạm vi CSDL dự án
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button
              class="btn-reset-chat"
              @click="clearAiSession"
              :disabled="isAiThinking"
              title="Làm mới phiên trò chuyện"
            >
              <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="btn-close-chat" @click="isAiChatOpen = false"><i class="bi bi-x-lg"></i></button>
          </div>
        </div>

        <div class="chat-body" ref="aiChatBody">
          <div
            v-for="(msg, index) in aiMessages"
            :key="index"
            :class="['chat-bubble', msg.sender === 'user' ? 'bubble-user' : 'bubble-ai']"
          >
            <div v-if="msg.sender === 'user'">{{ msg.text }}</div>
            <div v-else class="ai-formatted-text" v-html="formatAiText(msg.text)"></div>

            <!-- Danh sách sản phẩm đề xuất trích xuất từ CSDL -->
            <div
              v-if="msg.suggested_products && msg.suggested_products.length > 0"
              class="ai-suggested-cards mt-2"
            >
              <div class="ai-suggest-label">Sản phẩm từ CSDL TechMart:</div>
              <div
                v-for="prod in msg.suggested_products"
                :key="prod.id"
                class="ai-prod-card"
              >
                <img
                  v-if="prod.anh_chinh"
                  :src="prod.anh_chinh"
                  :alt="prod.ten_san_pham"
                  class="ai-prod-thumb"
                />
                <div class="ai-prod-info">
                  <div class="ai-prod-name" :title="prod.ten_san_pham">{{ prod.ten_san_pham }}</div>
                  <div class="ai-prod-price">{{ formatCurrency(prod.gia_khuyen_mai || prod.gia_goc) }}</div>
                  <div class="ai-prod-meta">BH: {{ prod.thoi_gian_bao_hanh }} tháng</div>
                </div>
                <button
                  class="btn-ai-add-cart"
                  @click="themVaoGio(prod)"
                  title="Thêm vào giỏ hàng"
                >
                  <i class="bi bi-cart-plus-fill"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Trạng thái AI đang truy xuất CSDL -->
          <div v-if="isAiThinking" class="chat-bubble bubble-ai ai-thinking-bubble">
            <span class="thinking-dot"></span>
            <span class="thinking-dot"></span>
            <span class="thinking-dot"></span>
            <span class="ms-1 text-muted">Đang truy xuất CSDL TechMart...</span>
          </div>
        </div>

        <div class="chat-quick-chips">
          <button
            class="chip"
            :disabled="isAiThinking"
            @click="sendAiQuestion('Xu hướng sản phẩm xem nhiều và bán chạy nhất trong CSDL là gì?')"
          >
            <i class="bi bi-graph-up-arrow me-1"></i>Xu hướng SP hot
          </button>
          <button
            class="chip"
            :disabled="isAiThinking"
            @click="sendAiQuestion('Tư vấn laptop gaming đang có trong kho kèm mã giảm giá')"
          >
            <i class="bi bi-laptop me-1"></i>Laptop Gaming
          </button>
          <button
            class="chip"
            :disabled="isAiThinking"
            @click="sendAiQuestion('Shop đang có màn hình 2K nào đáng mua?')"
          >
            <i class="bi bi-display me-1"></i>Màn hình 2K
          </button>
          <button
            class="chip"
            :disabled="isAiThinking"
            @click="sendAiQuestion('Shop đang có những mã giảm giá Voucher nào?')"
          >
            <i class="bi bi-ticket-perforated me-1"></i>Mã Voucher
          </button>
        </div>

        <div class="chat-input-area">
          <input
            type="text"
            v-model="aiInput"
            :disabled="isAiThinking"
            @keyup.enter="sendAiQuestion()"
            placeholder="Hỏi cấu hình, xu hướng, voucher trong CSDL..."
          />
          <button class="btn-send-ai" :disabled="isAiThinking" @click="sendAiQuestion()">
            <i class="bi bi-send-fill me-1"></i>{{ isAiThinking ? '...' : 'Gửi' }}
          </button>
        </div>
      </div>
    </div>

    <!-- NÚT CUỘN LÊN ĐẦU TRANG (BACK TO TOP VỚI VÒNG TIẾN ĐỘ) -->
    <transition name="fade">
      <button
        v-if="showBackToTop"
        class="btn-back-to-top"
        @click="scrollToTop"
        title="Cuộn lên đầu trang"
      >
        <svg class="progress-ring" width="46" height="46">
          <circle
            class="progress-ring__circle-bg"
            stroke="#e2e8f0"
            stroke-width="3"
            fill="transparent"
            r="19"
            cx="23"
            cy="23"
          />
          <circle
            class="progress-ring__circle"
            stroke="#2563eb"
            stroke-width="3"
            fill="transparent"
            r="19"
            cx="23"
            cy="23"
            :style="{ strokeDashoffset: ringOffset }"
          />
        </svg>
        <i class="bi bi-chevron-up"></i>
      </button>
    </transition>
    <!-- ========================================== -->
    <!-- TOAST THÔNG BÁO THÊM GIỎ HÀNG THÀNH CÔNG -->
    <!-- ========================================== -->
    <transition name="toast-popup">
      <div v-if="showToast" class="cart-toast-fixed" role="alert">
        <div class="toast-left-icon">
          <i class="bi bi-check2-circle"></i>
        </div>
        <div class="toast-center-content">
          <div class="toast-head">
            <strong>Đã thêm vào giỏ hàng!</strong>
          </div>
          <p class="toast-msg">{{ toastMessage }}</p>
          <div class="toast-preview-item" v-if="toastProduct">
            <img :src="toastProduct.anh_chinh || toastProduct.hinh_anh" :alt="toastProduct.ten_san_pham" class="toast-thumb" />
            <div class="toast-thumb-info">
              <span class="toast-prod-name">{{ toastProduct.ten_san_pham }}</span>
              <span class="toast-prod-price">{{ formatCurrency(toastProduct.gia_khuyen_mai || toastProduct.gia_goc) }}</span>
            </div>
          </div>
        </div>
        <div class="toast-right-actions">
          <router-link to="/cart" class="btn-toast-view-cart">
            <span>Xem giỏ</span>
            <i class="bi bi-arrow-right ms-1"></i>
          </router-link>
          <button class="btn-toast-dismiss" @click="showToast = false" title="Đóng thông báo">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </transition>
    <!-- ========================================== -->
    <!-- MODAL POPUP YÊU CẦU ĐĂNG NHẬP / ĐĂNG KÝ -->
    <!-- ========================================== -->
    <transition name="modal-fade">
      <div v-if="showAuthModal" class="modal-backdrop-custom">
        <!-- Lớp phủ nền bắt sự kiện đóng -->
        <div class="modal-overlay-bg" @click="closeAuthModal"></div>

        <!-- Khung Dialog trung tâm: chặn mọi sự kiện nổi bọt ra ngoài -->
        <div class="auth-modal-dialog" @click.stop>
          <!-- Header Modal -->
          <div class="auth-modal-header">
            <div class="modal-title-wrap">
              <div class="modal-icon-badge"><i class="bi bi-shield-lock-fill"></i></div>
              <div>
                <h5 class="modal-title mb-0">Tài Khoản TechMart AI</h5>
                <span class="modal-subtitle">Đăng nhập hoặc đăng ký để thêm sản phẩm vào giỏ hàng và mua sắm</span>
              </div>
            </div>
            <button type="button" class="btn-close-modal" @click="closeAuthModal" title="Đóng cửa sổ">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>

          <!-- Tabs chuyển đổi Đăng Nhập / Đăng Ký ngay trên Modal -->
          <div class="modal-tabs-bar">
            <button
              type="button"
              class="modal-tab-pill"
              :class="{ active: modalTab === 'login' }"
              @click="modalTab = 'login'; modalAlertMessage = ''"
            >
              <i class="bi bi-box-arrow-in-right me-1"></i> ĐĂNG NHẬP
            </button>
            <button
              type="button"
              class="modal-tab-pill"
              :class="{ active: modalTab === 'register' }"
              @click="modalTab = 'register'; modalAlertMessage = ''"
            >
              <i class="bi bi-person-plus-fill me-1"></i> TẠO TÀI KHOẢN MỚI
            </button>
          </div>

          <!-- Body Modal -->
          <div class="auth-modal-body">
            <!-- Alert thông báo lỗi / thành công trong Modal -->
            <div v-if="modalAlertMessage" class="auth-alert" :class="'alert-' + modalAlertType">
              <i :class="modalAlertType === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill'" class="me-2 fs-5"></i>
              <span>{{ modalAlertMessage }}</span>
            </div>

            <!-- FORM 1: ĐĂNG NHẬP -->
            <form v-if="modalTab === 'login'" @submit.prevent="handleModalLogin" class="modal-auth-form">
              <div class="form-group mb-3">
                <label class="modal-label">
                  <i class="bi bi-envelope-fill me-1 text-primary"></i> Email hoặc Số điện thoại <span class="text-danger">*</span>
                </label>
                <div class="input-with-icon">
                  <input
                    type="text"
                    v-model="modalLoginForm.email"
                    class="form-control modal-input"
                    placeholder="Nhập email hoặc số điện thoại của bạn"
                    required
                    autocomplete="username"
                  />
                  <i class="bi bi-person input-icon"></i>
                </div>
              </div>

              <div class="form-group mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="modal-label mb-0">
                    <i class="bi bi-lock-fill me-1 text-primary"></i> Mật khẩu <span class="text-danger">*</span>
                  </label>
                  <router-link to="/login" class="text-decoration-none small text-primary" @click="closeAuthModal">
                    Quên mật khẩu?
                  </router-link>
                </div>
                <div class="input-with-icon">
                  <input
                    :type="showModalPassword ? 'text' : 'password'"
                    v-model="modalLoginForm.password"
                    class="form-control modal-input"
                    placeholder="Nhập mật khẩu"
                    required
                    autocomplete="current-password"
                  />
                  <button type="button" class="btn-toggle-eye" @click="showModalPassword = !showModalPassword" tabindex="-1">
                    <i :class="showModalPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn-modal-submit" :disabled="isModalLoading">
                <span v-if="isModalLoading" class="spinner-border spinner-border-sm me-2"></span>
                <span v-if="!isModalLoading">ĐĂNG NHẬP VÀ TIẾP TỤC</span>
                <span v-else>ĐANG XÁC THỰC...</span>
                <i class="bi bi-arrow-right ms-2" v-if="!isModalLoading"></i>
              </button>

              <div class="modal-footer-switch text-center mt-3">
                <span>Chưa có tài khoản TechMart? </span>
                <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none" @click="modalTab = 'register'; modalAlertMessage = ''">
                  Đăng ký ngay
                </a>
              </div>
            </form>

            <!-- FORM 2: ĐĂNG KÝ -->
            <form v-else @submit.prevent="handleModalRegister" class="modal-auth-form">
              <div class="form-group mb-2">
                <label class="modal-label">Họ và tên <span class="text-danger">*</span></label>
                <div class="input-with-icon">
                  <input
                    type="text"
                    v-model="modalRegisterForm.ho_ten"
                    class="form-control modal-input"
                    placeholder="Nhập họ và tên"
                    required
                  />
                  <i class="bi bi-person-badge input-icon"></i>
                </div>
              </div>

              <div class="form-group mb-2">
                <label class="modal-label">Địa chỉ Email <span class="text-danger">*</span></label>
                <div class="input-with-icon">
                  <input
                    type="email"
                    v-model="modalRegisterForm.email"
                    class="form-control modal-input"
                    placeholder="name@example.com"
                    required
                  />
                  <i class="bi bi-envelope input-icon"></i>
                </div>
              </div>

              <div class="form-group mb-2">
                <label class="modal-label">Số điện thoại</label>
                <div class="input-with-icon">
                  <input
                    type="tel"
                    v-model="modalRegisterForm.so_dien_thoai"
                    class="form-control modal-input"
                    placeholder="Ví dụ: 0905123456"
                  />
                  <i class="bi bi-telephone input-icon"></i>
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="modal-label">Mật khẩu <span class="text-danger">*</span></label>
                  <input
                    :type="showModalPassword ? 'text' : 'password'"
                    v-model="modalRegisterForm.password"
                    class="form-control modal-input"
                    placeholder="Tối thiểu 6 ký tự"
                    required
                    minlength="6"
                  />
                </div>
                <div class="col-6">
                  <label class="modal-label">Xác nhận <span class="text-danger">*</span></label>
                  <input
                    :type="showModalPassword ? 'text' : 'password'"
                    v-model="modalRegisterForm.password_confirmation"
                    class="form-control modal-input"
                    placeholder="Nhập lại mật khẩu"
                    required
                  />
                </div>
              </div>

              <button type="submit" class="btn-modal-submit btn-modal-register" :disabled="isModalLoading">
                <span v-if="isModalLoading" class="spinner-border spinner-border-sm me-2"></span>
                <span v-if="!isModalLoading">TẠO TÀI KHOẢN & TIẾP TỤC</span>
                <span v-else>ĐANG XỬ LÝ...</span>
                <i class="bi bi-check-circle-fill ms-2" v-if="!isModalLoading"></i>
              </button>

              <div class="modal-footer-switch text-center mt-3">
                <span>Đã có tài khoản? </span>
                <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none" @click="modalTab = 'login'; modalAlertMessage = ''">
                  Đăng nhập ngay
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios'
import apiUrl from '../../utils/api'

export default {
  data() {
    return {
      list_flash_sale: [],
      list_sp_noi_bat: [],
      list_san_pham: [],
      list_all_san_pham: [],
      isExpandedAll: false,
      list_danh_muc: [],
      list_thuong_hieu: [],

      // Hero Slider State (Các Slide Quảng Bá với Hình Ảnh Thực Tế Chất Lượng Cao)
      currentHeroIndex: 0,
      heroSlideTimer: null,
      heroSlides: [
        {
          badge: 'SIÊU ĐẠI HỘI CÔNG NGHỆ 2026 - GIẢM ĐẾN 40%',
          title: 'LAPTOP GAMING & MÁY TRẠM ĐỒ HỌA',
          subTitle: 'TRẢ GÓP 0% LÃI SUẤT',
          perks: [
            'Tặng kèm Balo công nghệ cao cấp & Chuột không dây',
            'Bảo hành chính hãng 24 tháng theo số Serial/IMEI vật lý',
            'Hỗ trợ Trợ lý AI tư vấn build cấu hình chuẩn xác theo nhu cầu'
          ],
          image: 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80',
          tag: 'RTX 4060 / 4070 Ti SUPER',
          ctaText: 'MUA NGAY'
        },
        {
          badge: 'HIỆU NĂNG ĐỈNH CAO - BẢO HÀNH 36 THÁNG',
          title: 'SIÊU CHIẾN HẠM PC GAMING & WORKSTATION',
          subTitle: 'TẶNG TẢN NHIỆT NƯỚC AIO CAO CẤP',
          perks: [
            'Vi xử lý Intel Gen 14th i7/i9 & AMD Ryzen 7000 Series',
            'Linh kiện 100% chính hãng fullbox, tự động kiểm tra tương thích',
            'Hỗ trợ giao hàng & lắp đặt tận nơi, bảo trì trọn đời'
          ],
          image: 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
          tag: 'RTX 4090 / i9-14900K',
          ctaText: 'BUILD PC NGAY'
        },
        {
          badge: 'CHÍNH HÃNG APPLE VIỆT NAM - THU CŨ ĐỔI MỚI',
          title: 'APPLE MACBOOK PRO M3 & IPHONE 16 PRO',
          subTitle: 'TRỢ GIÁ THU CŨ LÊN TỚI 3.000.000Đ',
          perks: [
            'Sức mạnh đột phá từ vi xử lý Apple Silicon M3 kiến trúc 3nm',
            'Màn hình Liquid Retina XDR siêu sắc nét cho đồ họa chuyên nghiệp',
            'Hỗ trợ thanh toán đa kênh, trả góp thẻ tín dụng 0% phí'
          ],
          image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
          tag: 'Apple M3 Pro / 18GB Unified',
          ctaText: 'KHÁM PHÁ NGAY'
        }
      ],

      // Tabs Bộ Lọc Gợi Ý Sản Phẩm Bán Chạy
      activeBestSellerTab: 'all',
      bestSellerTabs: [
        { key: 'all', label: 'Tất cả sản phẩm', icon: 'bi-grid-fill' },
        { key: 'laptop', label: 'Laptop & MacBook', icon: 'bi-laptop' },
        { key: 'man-hinh', label: 'Màn hình Gaming & 4K', icon: 'bi-display' },
        { key: 'linh-kien', label: 'PC & Linh kiện VGA/CPU', icon: 'bi-cpu-fill' },
        { key: 'gear', label: 'Gaming Gear & Phụ kiện', icon: 'bi-headset' },
      ],

      // Navigation & Scroll State
      isSticky: false,

      // Search state
      searchQuery: '',
      searchSuggestions: [
        'Laptop ASUS ROG Strix G16 RTX 4060',
        'Apple MacBook Pro 14 M3 18GB RAM',
        'Dell XPS 13 Plus Core i7 1TB SSD',
        'Lenovo Legion Pro 5 Ryzen 7 RTX 4070',
        'Card màn hình MSI RTX 4060 OC',
        'Màn hình ASUS ROG 27 inch 240Hz'
      ],
      showSuggestions: false,

      // Countdown timer for Flash Sale
      hours: 6,
      minutes: 34,
      seconds: 15,
      timerInterval: null,

      // User Menu Dropdown
      showUserMenu: false,

      // AI Chat Widget Quick State
      isAiChatOpen: false,
      isAiThinking: false,
      aiMessages: [
        {
          sender: 'ai',
          text: 'Xin chào bạn! Em là Trợ lý AI TechMart (kết nối trực tiếp CSDL dự án). Bạn cần tư vấn Laptop, Màn hình, Linh kiện PC, Gaming Gear, xem xu hướng bán chạy hay tra cứu Voucher/Bảo hành ạ?',
          suggested_products: []
        }
      ],
      aiInput: '',

      // Scroll Effects & Progress State
      scrollProgress: 0,
      showBackToTop: false,

      // Danh sách yêu thích Wishlist
      wishlist: [],

      // Giỏ hàng & Toast State
      cartTotalCount: 0,
      isCartBouncing: false,
      showToast: false,
      toastMessage: '',
      toastProduct: null,
      toastTimer: null,

      // Khách hàng & Auth Modal State
      currentUser: null,
      showAuthModal: false,
      modalTab: 'login',
      isModalLoading: false,
      showModalPassword: false,
      modalAlertMessage: '',
      modalAlertType: 'danger',
      modalLoginForm: {
        email: '',
        password: ''
      },
      modalRegisterForm: {
        ho_ten: '',
        email: '',
        so_dien_thoai: '',
        password: '',
        password_confirmation: ''
      },
      pendingAuthAction: null
    }
  },
  computed: {
    ringOffset() {
      const circumference = 2 * Math.PI * 19; // ~119.38
      return circumference - (circumference * (this.scrollProgress / 100));
    },
    currentSlide() {
      return this.heroSlides[this.currentHeroIndex] || this.heroSlides[0];
    },
    filteredBestSellers() {
      const source = (this.list_san_pham && this.list_san_pham.length > 0)
        ? this.list_san_pham
        : this.list_sp_noi_bat;

      if (!source || source.length === 0) return [];

      if (this.activeBestSellerTab === 'all') {
        return source.slice(0, 8);
      }
      if (this.activeBestSellerTab === 'laptop') {
        return source.filter(p => p.danh_muc_id === 1);
      }
      if (this.activeBestSellerTab === 'man-hinh') {
        return source.filter(p => p.danh_muc_id === 2);
      }
      if (this.activeBestSellerTab === 'linh-kien') {
        return source.filter(p => [3, 5, 6, 7, 8].includes(p.danh_muc_id));
      }
      if (this.activeBestSellerTab === 'gear') {
        return source.filter(p => p.danh_muc_id === 4);
      }
      return source;
    },
    displayedAllProducts() {
      const source = (this.list_all_san_pham && this.list_all_san_pham.length > 0)
        ? this.list_all_san_pham
        : (this.list_san_pham && this.list_san_pham.length > 0 ? this.list_san_pham : this.list_sp_noi_bat);

      if (!source || source.length === 0) return [];

      if (this.isExpandedAll) {
        return source;
      }
      return source.slice(0, 8);
    }
  },
  mounted() {
    this.checkCurrentUser();
    window.addEventListener('auth-change', this.checkCurrentUser);
    this.loadData();
    this.loadAllSanPham();
    this.fetchCartData();
    this.loadAiChatHistory();
    this.startTimer();
    this.startHeroSlider();
    window.addEventListener('scroll', this.handleScroll, { passive: true });
    this.initScrollReveal();
  },
  beforeUnmount() {
    window.removeEventListener('auth-change', this.checkCurrentUser);
    if (this.timerInterval) clearInterval(this.timerInterval);
    if (this.heroSlideTimer) clearInterval(this.heroSlideTimer);
    if (this.toastTimer) clearTimeout(this.toastTimer);
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    checkCurrentUser() {
      const stored = localStorage.getItem('customer_user');
      const token = localStorage.getItem('customer_token');
      if (stored && token) {
        try {
          this.currentUser = JSON.parse(stored);
        } catch (e) {
          this.currentUser = null;
        }
      } else {
        this.currentUser = null;
      }
    },
    closeAuthModal() {
      this.showAuthModal = false;
      this.modalAlertMessage = '';
    },
    openAuthModal(tab = 'login', callback = null) {
      this.modalTab = tab || 'login';
      this.pendingAuthAction = callback;
      this.modalAlertMessage = '';
      this.modalLoginForm.email = '';
      this.modalLoginForm.password = '';
      this.modalRegisterForm.ho_ten = '';
      this.modalRegisterForm.email = '';
      this.modalRegisterForm.so_dien_thoai = '';
      this.modalRegisterForm.password = '';
      this.modalRegisterForm.password_confirmation = '';
      this.showAuthModal = true;
    },
    async handleModalLogin() {
      this.isModalLoading = true;
      this.modalAlertMessage = '';
      const sessionId = this.getSessionId();

      try {
        const res = await axios.post(apiUrl('client/dang-nhap'), {
          email: this.modalLoginForm.email,
          password: this.modalLoginForm.password,
          session_id: sessionId
        });

        if (res.data && res.data.status) {
          this.modalAlertType = 'success';
          this.modalAlertMessage = res.data.message || 'Đăng nhập thành công!';

          localStorage.setItem('customer_token', res.data.token);
          localStorage.setItem('customer_user', JSON.stringify(res.data.user));
          localStorage.setItem('user_id', res.data.user.id);
          localStorage.setItem('user_name', res.data.user.ho_ten);

          this.currentUser = res.data.user;
          this.fetchCartData();
          window.dispatchEvent(new Event('auth-change'));

          setTimeout(() => {
            this.closeAuthModal();
            // Tự động kích hoạt hành động vừa thực hiện (ví dụ thêm giỏ hàng)
            if (typeof this.pendingAuthAction === 'function') {
              const act = this.pendingAuthAction;
              this.pendingAuthAction = null;
              act();
            }
          }, 450);
        } else {
          this.modalAlertType = 'danger';
          this.modalAlertMessage = res.data ? res.data.message : 'Đăng nhập không thành công!';
        }
      } catch (err) {
        this.modalAlertType = 'danger';
        if (err.response && err.response.data && err.response.data.message) {
          this.modalAlertMessage = err.response.data.message;
        } else {
          this.modalAlertMessage = 'Lỗi kết nối máy chủ! Vui lòng thử lại sau.';
        }
      } finally {
        this.isModalLoading = false;
      }
    },
    async handleModalRegister() {
      if (this.modalRegisterForm.password !== this.modalRegisterForm.password_confirmation) {
        this.modalAlertType = 'danger';
        this.modalAlertMessage = 'Mật khẩu xác nhận không khớp, vui lòng nhập lại!';
        return;
      }

      this.isModalLoading = true;
      this.modalAlertMessage = '';
      const sessionId = this.getSessionId();

      try {
        const res = await axios.post(apiUrl('client/dang-ky'), {
          ho_ten: this.modalRegisterForm.ho_ten,
          email: this.modalRegisterForm.email,
          so_dien_thoai: this.modalRegisterForm.so_dien_thoai,
          password: this.modalRegisterForm.password,
          session_id: sessionId
        });

        if (res.data && res.data.status) {
          this.modalAlertType = 'success';
          this.modalAlertMessage = res.data.message || 'Tạo tài khoản thành công!';

          localStorage.setItem('customer_token', res.data.token);
          localStorage.setItem('customer_user', JSON.stringify(res.data.user));
          localStorage.setItem('user_id', res.data.user.id);
          localStorage.setItem('user_name', res.data.user.ho_ten);

          this.currentUser = res.data.user;
          this.fetchCartData();
          window.dispatchEvent(new Event('auth-change'));

          setTimeout(() => {
            this.closeAuthModal();
            if (typeof this.pendingAuthAction === 'function') {
              const act = this.pendingAuthAction;
              this.pendingAuthAction = null;
              act();
            }
          }, 450);
        } else {
          this.modalAlertType = 'danger';
          this.modalAlertMessage = res.data ? res.data.message : 'Đăng ký thất bại!';
        }
      } catch (err) {
        this.modalAlertType = 'danger';
        if (err.response && err.response.data && err.response.data.message) {
          this.modalAlertMessage = err.response.data.message;
        } else {
          this.modalAlertMessage = 'Đăng ký không thành công, vui lòng kiểm tra lại!';
        }
      } finally {
        this.isModalLoading = false;
      }
    },
    handleLogout() {
      if (confirm('Bạn có chắc chắn muốn đăng xuất tài khoản?')) {
        const token = localStorage.getItem('customer_token');
        if (token) {
          axios.post(apiUrl('client/dang-xuat'), {}, {
            headers: { Authorization: `Bearer ${token}` }
          }).catch(() => {});
        }
        localStorage.removeItem('customer_token');
        localStorage.removeItem('customer_user');
        localStorage.removeItem('user_id');
        localStorage.removeItem('user_name');
        this.currentUser = null;
        this.showUserMenu = false;
        this.showCartToast('Đã đăng xuất tài khoản thành công!');
        window.dispatchEvent(new Event('auth-change'));
      }
    },
    getSessionId() {
      let sess = localStorage.getItem('techmart_session_id');
      if (!sess) {
        sess = 'guest_' + Math.random().toString(36).substring(2, 12) + Date.now().toString(36);
        localStorage.setItem('techmart_session_id', sess);
      }
      return sess;
    },
    async fetchCartData() {
      const sessionId = this.getSessionId();
      const userId = localStorage.getItem('user_id') || null;
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/client/gio-hang/get-data', {
          params: { session_id: sessionId, user_id: userId }
        });
        if (res.data && res.data.status && Array.isArray(res.data.data)) {
          this.cartTotalCount = res.data.total_items ?? res.data.data.length;
          localStorage.setItem('techmart_cart', JSON.stringify(res.data.data));
          localStorage.setItem('techmart_cart_count', String(this.cartTotalCount));
        } else {
          this.cartTotalCount = 0;
        }
      } catch (e) {
        this.cartTotalCount = 0;
      }
    },
    async themVaoGio(item) {
      if (!item) return;

      // KIỂM TRA ĐĂNG NHẬP: BẮT BUỘC ĐĂNG NHẬP TRƯỚC KHI THÊM GIỎ
      const token = localStorage.getItem('customer_token');
      if (!token) {
        this.openAuthModal('login', () => this.themVaoGio(item));
        return;
      }

      item.isAdding = true;
      const sessionId = this.getSessionId();
      const userId = localStorage.getItem('user_id') || (this.currentUser ? this.currentUser.id : null);

      const giaSanPham = (item.gia_khuyen_mai && Number(item.gia_khuyen_mai) > 0)
        ? Number(item.gia_khuyen_mai)
        : Number(item.gia_goc || item.gia_ban || 0);

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/client/gio-hang/them-vao-gio', {
          san_pham_id: item.id,
          bien_the_id: item.bien_the_id || null,
          so_luong: 1,
          gia: giaSanPham > 0 ? giaSanPham : null,
          session_id: sessionId,
          user_id: userId
        });

        if (res.data && res.data.status) {
          if (res.data.total_items !== undefined) {
            this.cartTotalCount = res.data.total_items;
          } else {
            this.cartTotalCount++;
          }
          this.triggerCartBounce();
          this.showCartToast(res.data.message || `Đã thêm "${item.ten_san_pham}" vào giỏ hàng!`, item);
          await this.fetchCartData();
        } else {
          throw new Error(res.data ? res.data.message : 'Lỗi');
        }
      } catch (err) {
        alert((err && err.response && err.response.data && err.response.data.message) || 'Không thể thêm vào giỏ hàng qua API. Vui lòng kiểm tra Backend!');
      } finally {
        setTimeout(() => {
          item.isAdding = false;
        }, 350);
      }
    },
    triggerCartBounce() {
      this.isCartBouncing = true;
      setTimeout(() => {
        this.isCartBouncing = false;
      }, 700);
    },
    showCartToast(msg, prod) {
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastMessage = msg;
      this.toastProduct = prod;
      this.showToast = true;
      this.toastTimer = setTimeout(() => {
        this.showToast = false;
      }, 4500);
    },
    syncLocalCartAfterAdd(item) {
      try {
        let cart = [];
        const stored = localStorage.getItem('techmart_cart');
        if (stored) {
          cart = JSON.parse(stored);
        }
        const existing = cart.find(c => c.san_pham_id === item.id || c.name === item.ten_san_pham);
        if (existing) {
          existing.quantity = (existing.quantity || 1) + 1;
        } else {
          cart.unshift({
            id: Date.now(),
            san_pham_id: item.id,
            name: item.ten_san_pham,
            sku: (item.ma_san_pham || 'SP' + item.id) + '-STD',
            specs: item.mo_ta_ngan || 'Hàng chính hãng fullbox, bảo hành chính hãng 24T',
            warrantyNote: 'Bảo hành: 24 Tháng chính hãng theo Serial/IMEI',
            price: item.gia_khuyen_mai && item.gia_khuyen_mai > 0 ? item.gia_khuyen_mai : item.gia_goc,
            originalPrice: item.gia_goc || 0,
            quantity: 1,
            stock: 15,
            selected: true,
            isGift: false,
            image: item.anh_chinh || 'https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_4__2_108.png'
          });
        }
        localStorage.setItem('techmart_cart', JSON.stringify(cart));
        localStorage.setItem('techmart_cart_count', cart.length);
      } catch (e) {
        console.warn('Lỗi lưu local cart:', e);
      }
    },
    startHeroSlider() {
      this.heroSlideTimer = setInterval(() => {
        this.nextHeroSlide();
      }, 5000);
    },
    nextHeroSlide() {
      this.currentHeroIndex = (this.currentHeroIndex + 1) % this.heroSlides.length;
    },
    prevHeroSlide() {
      this.currentHeroIndex =
        (this.currentHeroIndex - 1 + this.heroSlides.length) % this.heroSlides.length;
    },
    setHeroSlide(index) {
      this.currentHeroIndex = index;
      if (this.heroSlideTimer) clearInterval(this.heroSlideTimer);
      this.startHeroSlider();
    },
    handleScroll() {
      const scrollY = window.scrollY || window.pageYOffset;
      this.isSticky = scrollY > 80;
      this.showBackToTop = scrollY > 320;

      const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
      this.scrollProgress = totalHeight > 0 ? Math.min(100, Math.max(0, (scrollY / totalHeight) * 100)) : 0;
    },
    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    },
    initScrollReveal() {
      this.$nextTick(() => {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
              }
            });
          },
          { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
        );

        const targets = document.querySelectorAll('.scroll-reveal');
        targets.forEach((target) => observer.observe(target));
      });
    },
    loadData() {
      axios
        .get("http://127.0.0.1:8000/api/client/trang-chu/get-data")
        .then((res) => {
          this.list_flash_sale   = res.data.data_flash_sale || res.data.data?.flash_sale || [];
          this.list_sp_noi_bat   = res.data.data_sp_noi_bat || res.data.data?.san_pham_noi_bat || [];
          this.list_san_pham     = res.data.list_san_pham   || res.data.data?.tat_ca_san_pham || [];
          this.list_danh_muc     = res.data.data_danh_muc   || res.data.data?.danh_muc || [];
          this.list_thuong_hieu  = res.data.data_thuong_hieu || res.data.data?.thuong_hieu || [];
          this.initScrollReveal();
        })
        .catch((err) => {
          console.warn("Chưa khởi động backend Laravel hoặc chưa seed data:", err);
        });
    },
    loadAllSanPham() {
      axios
        .get("http://127.0.0.1:8000/api/client/trang-chu/get-tat-ca-san-pham")
        .then((res) => {
          this.list_all_san_pham = res.data.data || [];
          this.initScrollReveal();
        })
        .catch((err) => {
          console.warn("Chưa tải được tất cả sản phẩm:", err);
        });
    },
    startTimer() {
      this.timerInterval = setInterval(() => {
        if (this.seconds > 0) {
          this.seconds--;
        } else {
          this.seconds = 59;
          if (this.minutes > 0) {
            this.minutes--;
          } else {
            this.minutes = 59;
            if (this.hours > 0) {
              this.hours--;
            } else {
              this.hours = 8;
            }
          }
        }
      }, 1000);
    },
    scrollToBottomAiChat() {
      this.$nextTick(() => {
        const el = this.$refs.aiChatBody;
        if (el) {
          el.scrollTop = el.scrollHeight;
        }
      });
    },
    formatAiText(rawText) {
      if (!rawText) return '';
      let escaped = String(rawText)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
      escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
      escaped = escaped.replace(/`([^`]+)`/g, '<code class="ai-inline-code">$1</code>');
      escaped = escaped.replace(/\n/g, '<br/>');
      return escaped;
    },
    async loadAiChatHistory() {
      const sessionId = this.getSessionId();
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/client/chat-ai/history', {
          params: { session_id: sessionId }
        });
        if (res.data && res.data.status && Array.isArray(res.data.messages) && res.data.messages.length > 0) {
          this.aiMessages = [
            this.aiMessages[0],
            ...res.data.messages
          ];
          this.scrollToBottomAiChat();
        }
      } catch (e) {
        // Giữ nguyên lời chào mặc định nếu chưa có lịch sử
      }
    },
    async clearAiSession() {
      if (this.isAiThinking) return;
      const sessionId = this.getSessionId();
      try {
        await axios.post('http://127.0.0.1:8000/api/client/chat-ai/clear-session', {
          session_id: sessionId
        });
      } catch (e) {
        // Bỏ qua lỗi mạng nếu có
      }
      this.aiMessages = [
        {
          sender: 'ai',
          text: 'Đã làm mới cuộc trò chuyện! Bạn cần em tư vấn sản phẩm, cấu hình phần cứng hay kiểm tra xu hướng từ CSDL TechMart ạ?',
          suggested_products: []
        }
      ];
    },
    async sendAiQuestion(promptText) {
      if (this.isAiThinking) return;
      const query = (promptText || this.aiInput || '').trim();
      if (!query) return;

      this.aiMessages.push({ sender: 'user', text: query });
      if (!promptText) {
        this.aiInput = '';
      }
      this.isAiThinking = true;
      this.scrollToBottomAiChat();

      const sessionId = this.getSessionId();
      const userId = localStorage.getItem('user_id') || null;

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/client/chat-ai', {
          session_id: sessionId,
          user_id: userId,
          message: query
        });

        if (res.data && res.data.status) {
          this.aiMessages.push({
            sender: 'ai',
            text: res.data.reply,
            suggested_products: res.data.suggested_products || []
          });
        } else {
          this.aiMessages.push({
            sender: 'ai',
            text: (res.data && res.data.message) || 'Không thể lấy phản hồi từ Trợ lý AI lúc này.',
            suggested_products: []
          });
        }
      } catch (err) {
        this.aiMessages.push({
          sender: 'ai',
          text: 'Không kết nối được máy chủ Backend (http://127.0.0.1:8000). Vui lòng kiểm tra php artisan serve!',
          suggested_products: []
        });
      } finally {
        this.isAiThinking = false;
        this.scrollToBottomAiChat();
      }
    },
    scrollBestSellers(direction) {
      const container = this.$refs.bestSellerScroll;
      if (!container) return;
      const step = container.offsetWidth * 0.75;
      container.scrollBy({
        left: direction === 'left' ? -step : step,
        behavior: 'smooth'
      });
    },
    toggleWishlist(id) {
      if (!id) return;
      const idx = this.wishlist.indexOf(id);
      if (idx > -1) {
        this.wishlist.splice(idx, 1);
      } else {
        this.wishlist.push(id);
      }
    },
    isWishlisted(id) {
      return this.wishlist.includes(id);
    },
    calculateDiscount(goc, km, index = 0) {
      if (goc && km && goc > km) {
        return Math.round(((goc - km) / goc) * 100);
      }
      // Dải % giảm giá tự nhiên hấp dẫn như CellphoneS
      const presets = [15, 27, 10, 24, 18, 30, 12, 22];
      return presets[index % presets.length];
    },
    extractCpuGpu(item) {
      const name = (item.ten_san_pham || '').toUpperCase();
      const desc = (item.mo_ta_ngan || '').toUpperCase();
      const combined = name + ' ' + desc;

      if (combined.includes('I9') || combined.includes('14900K')) return 'CORE I9-14900K | RTX 4090';
      if (combined.includes('I7') || combined.includes('13650HX') || combined.includes('14700K')) return 'CORE I7-13650HX | RTX 4060';
      if (combined.includes('I5') || combined.includes('120U')) return 'CORE 5-120U | Intel Graphics';
      if (combined.includes('M3') || combined.includes('MACBOOK')) return 'APPLE M3 CHIP | 10-CORE GPU';
      if (combined.includes('RYZEN 7') || combined.includes('7745HX')) return 'RYZEN 7-7745HX | RTX 4070';
      if (combined.includes('RTX 4070')) return 'RTX 4070 Ti SUPER 16GB';
      if (combined.includes('MÀN HÌNH') || combined.includes('ULTRASHARP') || combined.includes('IPS')) return 'FAST IPS 2K QHD | 120Hz';
      if (combined.includes('SSD') || combined.includes('NVME')) return 'PCIe Gen 4.0 | 7450 MB/s';
      if (combined.includes('DDR5') || combined.includes('RAM')) return 'DDR5 BUS 6000MHz RGB';
      return 'INTEL GEN 14TH | HIỆU NĂNG CAO';
    },
    extractRamScreen(item) {
      const name = (item.ten_san_pham || '').toUpperCase();
      const desc = (item.mo_ta_ngan || '').toUpperCase();
      const combined = name + ' ' + desc;

      if (combined.includes('16GB') && combined.includes('512GB')) return '16GB | 512GB | 14" Full HD+';
      if (combined.includes('32GB') && combined.includes('1TB')) return '32GB | 1TB NVMe | 16" 2K 165Hz';
      if (combined.includes('8GB') && combined.includes('512GB')) return '8GB | 512GB | 15.6" WUXGA';
      if (combined.includes('27 INCH') || combined.includes('27"')) return '27 Inch | 2K QHD | sRGB 100%';
      if (combined.includes('SSD')) return '1TB NVMe M.2 2280 | Fullbox';
      if (combined.includes('RAM')) return 'Kit 32GB (2x16GB) | Heatsink';
      if (combined.includes('PHÍM') || combined.includes('CHUỘT') || combined.includes('GEAR')) return 'Wireless 2.4GHz | Pin 90h';
      return '16GB RAM | 512GB SSD | 15.6" FHD';
    },
    formatCurrency(val) {
      if (!val) return '0đ';
      return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
    }
  }
}
</script>

<style scoped>
/* ==========================================
   THEME CYBER TECH + MODERN ANIMATIONS
   ========================================== */
.homepage-container {
  font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  color: #0f172a;
  background-color: #f8fafc;
  min-height: 100vh;
}

.main-width {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
}

/* ❶ TOPBAR */
.topbar {
  background-color: #0b1120;
  color: #94a3b8;
  font-size: 12px;
  padding: 7px 0;
  border-bottom: 1px solid #1e293b;
}

.topbar-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.divider {
  margin: 0 8px;
  color: #334155;
}

.topbar-link {
  color: #cbd5e1;
  text-decoration: none;
  transition: color 0.15s;
}

.topbar-link:hover {
  color: #38bdf8;
}

/* ❷ - ❻ MAIN HEADER */
.main-header-wrap {
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.main-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.header-logo .logo-box {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  text-decoration: none;
}

.logo-icon {
  font-size: 24px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #fbbf24;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.logo-box:hover .logo-icon {
  transform: rotate(-10deg) scale(1.08);
}

.brand-name {
  font-size: 22px;
  font-weight: 900;
  letter-spacing: -0.5px;
  color: #0f172a;
  display: block;
  line-height: 1.1;
}

.brand-sub {
  font-size: 10.5px;
  color: #64748b;
  font-weight: 500;
  display: block;
}

/* ❸ SEARCH BAR */
.header-search {
  flex: 1;
  max-width: 520px;
  position: relative;
}

.search-input-group {
  display: flex;
  border: 2px solid #0f172a;
  border-radius: 8px;
  overflow: hidden;
  background-color: #ffffff;
  transition: all 0.25s ease;
}

.search-input-group:focus-within {
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
  transform: translateY(-1px);
}

.search-input-group input {
  flex: 1;
  padding: 10px 14px;
  font-size: 13.5px;
  border: none;
  outline: none;
}

.btn-search {
  background-color: #0f172a;
  color: #ffffff;
  padding: 0 20px;
  font-weight: 700;
  font-size: 13px;
  border: none;
  transition: background 0.2s, transform 0.15s;
  display: flex;
  align-items: center;
  cursor: pointer;
}

.btn-search:hover {
  background-color: #2563eb;
}

.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  z-index: 50;
  margin-top: 6px;
  overflow: hidden;
  animation: dropDownFade 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes dropDownFade {
  0% { opacity: 0; transform: translateY(-8px); }
  100% { opacity: 1; transform: translateY(0); }
}

.suggest-header {
  padding: 10px 14px;
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  background: #f8fafc;
  border-bottom: 1px solid #f1f5f9;
}

.suggest-item {
  padding: 10px 14px;
  font-size: 13px;
  color: #334155;
  cursor: pointer;
  transition: background 0.15s, padding-left 0.15s;
}

.suggest-item:hover {
  background: #eff6ff;
  color: #2563eb;
  padding-left: 18px;
}

/* ❹ - ❻ HEADER ACTIONS */
.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-action-ai {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
  color: #ffffff;
  border: 1px solid #334155;
  border-radius: 8px;
  padding: 6px 14px;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
}

.btn-action-ai:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
  border-color: #6366f1;
}

.action-icon {
  font-size: 20px;
}

.action-text {
  text-align: left;
}

.action-title {
  display: block;
  font-size: 12px;
  font-weight: 800;
  line-height: 1.2;
}

.action-sub {
  display: block;
  font-size: 10.5px;
  color: #94a3b8;
}

.btn-action-cart {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  color: #0f172a;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 700;
  font-size: 13px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-action-cart:hover {
  background: #e2e8f0;
  color: #2563eb;
  transform: translateY(-2px);
}

.cart-icon-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -10px;
  background: #ef4444;
  color: #ffffff;
  font-size: 10px;
  font-weight: 800;
  border-radius: 10px;
  padding: 1px 6px;
  animation: badgePop 1.5s ease-in-out infinite;
}

@keyframes badgePop {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15); }
}

.user-menu-wrapper {
  position: relative;
}

.btn-user {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  border: 1.5px solid #cbd5e1;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-user:hover {
  border-color: #0f172a;
  transform: translateY(-1px);
}

.user-avatar {
  font-size: 20px;
  color: #0f172a;
}

.user-name {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
}

.dropdown-arrow {
  font-size: 11px;
  color: #64748b;
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  width: 200px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  margin-top: 8px;
  padding: 6px 0;
  z-index: 50;
  animation: dropDownFade 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.user-dropdown-item {
  display: flex;
  align-items: center;
  padding: 8px 14px;
  font-size: 13px;
  color: #334155;
  text-decoration: none;
  transition: background 0.15s;
}

.user-dropdown-item:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.dropdown-divider {
  margin: 4px 0;
  border: 0;
  border-top: 1px solid #e2e8f0;
}

/* ❼ NAVBAR (STICKY ANIMATION + SLIDING UNDERLINE) */
.main-navbar {
  background-color: #0f172a;
  border-top: 1px solid #1e293b;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.main-navbar.sticky-nav {
  position: sticky;
  top: 0;
  z-index: 1000;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  background-color: rgba(15, 23, 42, 0.94);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  animation: stickySlideDown 0.32s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes stickySlideDown {
  0% { transform: translateY(-100%); }
  100% { transform: translateY(0); }
}

.navbar-inner {
  display: flex;
  align-items: center;
}

.nav-catalog-btn {
  background-color: #1e293b;
  color: #ffffff;
  padding: 12px 18px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  border-right: 1px solid #334155;
  transition: background 0.2s;
}

.nav-catalog-btn:hover {
  background-color: #2563eb;
}

.nav-links {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1;
}

.nav-item .nav-link {
  display: block;
  padding: 12px 16px;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-decoration: none;
  position: relative;
  transition: color 0.2s;
}

/* Sliding underline effect on hover */
.nav-item .nav-link::after {
  content: '';
  position: absolute;
  bottom: 8px;
  left: 14px;
  right: 14px;
  height: 2px;
  background: #38bdf8;
  border-radius: 2px;
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-item .nav-link:hover::after,
.nav-item .nav-link.active::after {
  transform: scaleX(1);
  transform-origin: left;
}

.nav-item .nav-link:hover,
.nav-item .nav-link.active {
  color: #ffffff;
  background-color: rgba(30, 41, 59, 0.5);
}

.flash-link {
  color: #fbbf24 !important;
}

.promo-link {
  color: #f87171 !important;
}

/* ❽ - ❾ HERO SECTION (ANIMATED DYNAMIC GRADIENT & ENTRANCE) */
.hero-section {
  margin-top: 16px;
}

.hero-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
}

@keyframes cyberMeshGradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.banner-badge {
  display: inline-flex;
  align-items: center;
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  border: 1px solid rgba(239, 68, 68, 0.35);
  font-size: 11px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 6px;
  margin-bottom: 12px;
  width: fit-content;
  box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);
  animation: badgeGlow 2.5s infinite alternate;
}

@keyframes badgeGlow {
  0% { box-shadow: 0 0 4px rgba(239, 68, 68, 0.15); }
  100% { box-shadow: 0 0 14px rgba(239, 68, 68, 0.45); }
}

/* Hero text slide-up animations */
.hero-title-anim {
  animation: heroTextSlideUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.hero-perks-anim {
  animation: heroTextSlideUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
}

.hero-cta-anim {
  animation: heroTextSlideUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
}

@keyframes heroTextSlideUp {
  0% {
    opacity: 0;
    transform: translateY(24px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.banner-title {
  font-size: 25px;
  font-weight: 900;
  line-height: 1.25;
  margin-bottom: 14px;
}

.banner-perks {
  list-style: none;
  font-size: 13px;
  color: #cbd5e1;
  line-height: 1.7;
  margin-bottom: 22px;
  padding: 0;
}

/* CTA BUTTON WITH SHIMMER LIGHT SWEEP & PULSE GLOW */
.btn-hero-cta {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #ffffff;
  font-weight: 800;
  font-size: 14px;
  padding: 11px 26px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  display: inline-flex;
  align-items: center;
  box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
  position: relative;
  overflow: hidden;
  animation: ctaPulseGlow 3.5s infinite;
}

@keyframes ctaPulseGlow {
  0%, 100% {
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
  }
  50% {
    box-shadow: 0 6px 24px rgba(56, 189, 248, 0.65), 0 0 12px rgba(37, 99, 235, 0.5);
  }
}

.btn-hero-cta::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -75%;
  width: 50%;
  height: 200%;
  background: linear-gradient(
    60deg,
    rgba(255, 255, 255, 0) 20%,
    rgba(255, 255, 255, 0.45) 50%,
    rgba(255, 255, 255, 0) 80%
  );
  transform: rotate(25deg);
  animation: ctaShimmerSweep 3.5s infinite ease-in-out;
}

@keyframes ctaShimmerSweep {
  0% { left: -75%; }
  40%, 100% { left: 125%; }
}

.btn-hero-cta i {
  display: inline-block;
  transition: transform 0.25s ease;
  animation: arrowNudge 2s infinite ease-in-out;
}

@keyframes arrowNudge {
  0%, 100% { transform: translateX(0); }
  50% { transform: translateX(4px); }
}

.btn-hero-cta:hover {
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 10px 28px rgba(37, 99, 235, 0.7), 0 0 20px rgba(56, 189, 248, 0.6);
}

.btn-hero-cta:hover i {
  transform: translateX(6px);
  animation: none;
}

.hero-main-banner {
  background: linear-gradient(135deg, #090d16 0%, #0f172a 35%, #1e1b4b 70%, #0c192c 100%);
  background-size: 250% 250%;
  animation: cyberMeshGradient 10s ease infinite;
  color: #ffffff;
  border-radius: 14px;
  padding: 24px 28px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  min-height: 330px;
  border: 1px solid rgba(56, 189, 248, 0.2);
  box-shadow: 0 12px 30px -8px rgba(15, 23, 42, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.hero-main-banner::before {
  content: '';
  position: absolute;
  top: -50px;
  left: 20%;
  width: 250px;
  height: 250px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(56, 189, 248, 0) 70%);
  pointer-events: none;
  animation: orbDrift1 8s ease-in-out infinite alternate;
}

.hero-main-banner::after {
  content: '';
  position: absolute;
  bottom: -60px;
  right: 15%;
  width: 280px;
  height: 280px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(147, 51, 234, 0.18) 0%, rgba(147, 51, 234, 0) 70%);
  pointer-events: none;
  animation: orbDrift2 9s ease-in-out infinite alternate;
}

@keyframes orbDrift1 {
  0% { transform: translate(0, 0) scale(0.9); }
  100% { transform: translate(40px, 30px) scale(1.2); }
}

@keyframes orbDrift2 {
  0% { transform: translate(0, 0) scale(1); }
  100% { transform: translate(-35px, -25px) scale(1.25); }
}

.hero-banner-content {
  flex: 1;
  max-width: 58%;
  z-index: 2;
}

.banner-highlight {
  color: #38bdf8;
  font-size: 20px;
  display: block;
  margin-top: 4px;
}

/* BANNER SLIDER BOX VỚI HÌNH ẢNH THỰC TẾ & NỔI */
.banner-slider-box {
  width: 260px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.banner-image-wrapper {
  position: relative;
  width: 250px;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: bannerSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes bannerSlideIn {
  0% {
    opacity: 0;
    transform: scale(0.92);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* KHỐI THIẾT BỊ LƠ LỬNG CHUYỂN ĐỘNG SỐNG ĐỘNG (FLOATING DEVICE) */
.floating-device-container {
  position: relative;
  width: 100%;
  height: 100%;
  z-index: 2;
  animation: floatDevice 2.8s ease-in-out infinite alternate !important;
  will-change: transform;
}

@keyframes floatDevice {
  0% {
    transform: translateY(0px) rotate(0deg);
  }
  100% {
    transform: translateY(-16px) rotate(1.2deg);
  }
}

/* BÓNG ĐỔ 3D CO DÃN THEO NHỊP NÂNG HẠ CỦA THIẾT BỊ */
.banner-shadow-3d {
  position: absolute;
  bottom: -16px;
  left: 12%;
  width: 76%;
  height: 14px;
  background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.8) 0%, rgba(56, 189, 248, 0.18) 45%, transparent 75%);
  border-radius: 50%;
  pointer-events: none;
  z-index: 1;
  animation: shadowPulse 2.8s ease-in-out infinite alternate !important;
  will-change: transform, opacity;
}

@keyframes shadowPulse {
  0% {
    transform: scale(1);
    opacity: 0.85;
    filter: blur(4px);
  }
  100% {
    transform: scale(0.7);
    opacity: 0.3;
    filter: blur(8px);
  }
}

.banner-product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45), 0 0 20px rgba(56, 189, 248, 0.25);
  border: 1px solid rgba(255, 255, 255, 0.18);
  transition: transform 0.4s ease;
}

.banner-product-img:hover {
  transform: scale(1.04);
}

.banner-img-glow {
  position: absolute;
  inset: -14px;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.32) 0%, rgba(0, 0, 0, 0) 70%);
  pointer-events: none;
  z-index: -1;
  animation: techCardGlow 4s ease-in-out infinite;
}

@keyframes techCardGlow {
  0%, 100% {
    opacity: 0.45;
    transform: scale(0.92);
    filter: blur(14px);
  }
  50% {
    opacity: 0.9;
    transform: scale(1.15);
    filter: blur(20px);
  }
}

.banner-floating-tag {
  position: absolute;
  bottom: -8px;
  right: 6px;
  background: rgba(15, 23, 42, 0.9);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(56, 189, 248, 0.4);
  border-radius: 20px;
  padding: 5px 14px;
  font-size: 11px;
  font-weight: 800;
  color: #f8fafc;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4), 0 0 10px rgba(56, 189, 248, 0.25);
  white-space: nowrap;
  animation: tagGlowPulse 3s ease-in-out infinite;
}

@keyframes tagGlowPulse {
  0%, 100% {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4), 0 0 8px rgba(56, 189, 248, 0.25);
    border-color: rgba(56, 189, 248, 0.35);
  }
  50% {
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.5), 0 0 16px rgba(56, 189, 248, 0.6);
    border-color: rgba(56, 189, 248, 0.85);
  }
}

.slider-controls-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 14px;
}

.btn-slide-nav {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.18);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-slide-nav:hover {
  background: #38bdf8;
  color: #0f172a;
  transform: scale(1.12);
}

.slider-dots {
  display: flex;
  align-items: center;
  gap: 6px;
}

.slider-dots .dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.25);
  cursor: pointer;
  transition: all 0.25s ease;
}

.slider-dots .dot.active {
  background: #38bdf8;
  width: 20px;
  border-radius: 4px;
}

/* ❾ 2 BANNER PHỤ BÊN PHẢI CÓ HÌNH ẢNH NỀN VÀ OVERLAY */
.hero-sub-banners {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.sub-banner-card {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  background-size: cover;
  background-position: center;
  border: 1px solid #e2e8f0;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}

.sub-banner-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.15);
}

.sub-banner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.65) 100%);
  transition: background 0.3s ease;
  z-index: 1;
}

.sub-banner-card:hover .sub-banner-overlay {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(15, 23, 42, 0.5) 100%);
}

.sub-banner-content {
  position: relative;
  z-index: 2;
  padding: 18px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
}

.sub-banner-card .sub-badge {
  font-size: 10.5px;
  font-weight: 800;
  color: #94a3b8;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.sub-banner-card .sub-title {
  color: #ffffff;
  font-size: 15px;
  font-weight: 800;
  margin-bottom: 6px;
}

.sub-banner-card .sub-desc {
  color: #cbd5e1;
  font-size: 12px;
  line-height: 1.45;
  margin-bottom: 10px;
}

.sub-banner-card .sub-link {
  color: #38bdf8;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: transform 0.2s;
}

.sub-banner-card:hover .sub-link {
  transform: translateX(4px);
  text-decoration: underline;
}

/* BEST SELLER SECTION HEADER FLEX & FILTER TABS */
.section-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 12px;
}

.bestseller-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tab-btn {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 14px;
  font-size: 12.5px;
  font-weight: 700;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
}

.tab-btn:hover {
  background: #e2e8f0;
  color: #0f172a;
}

.tab-btn.active {
  background: #0f172a;
  border-color: #0f172a;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2);
}

/* ==========================================
   ⓭ RED SHOWCASE CAROUSEL BANNER (SẢN PHẨM NỔI BẬT NHƯ MẪU)
   ========================================== */
.best-seller-showcase-section {
  margin-top: 32px;
  background: linear-gradient(135deg, #ff3358 0%, #ef4444 50%, #dc2626 100%);
  border-radius: 20px;
  padding: 24px 22px 28px;
  box-shadow: 0 14px 34px -6px rgba(239, 68, 68, 0.4);
  position: relative;
  overflow: hidden;
}

.best-seller-showcase-section::before {
  content: '';
  position: absolute;
  top: -40px;
  right: -40px;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, transparent 70%);
  pointer-events: none;
}

.showcase-header {
  margin-bottom: 20px;
}

.title-with-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.fire-badge {
  background: #ffffff;
  color: #ef4444;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  animation: fireBounce 2s infinite ease-in-out;
}

@keyframes fireBounce {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.15) rotate(-5deg); }
}

.text-white-bold {
  color: #ffffff;
  font-size: 28px;
  font-weight: 900;
  letter-spacing: -0.5px;
  text-transform: uppercase;
}

.text-outline-glow {
  font-size: 28px;
  font-weight: 900;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: transparent;
  -webkit-text-stroke: 1.8px #ffffff;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* CAROUSEL WRAPPER VỚI NÚT TRƯỢT 2 BÊN */
.showcase-carousel-wrapper {
  position: relative;
  width: 100%;
}

.showcase-cards-container {
  display: flex;
  gap: 14px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 6px 4px 12px;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.showcase-cards-container::-webkit-scrollbar {
  display: none;
}

/* NÚT ĐIỀU HƯỚNG TRƯỢT CAROUSEL */
.btn-carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  color: #0f172a;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  font-size: 15px;
  font-weight: 800;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-carousel-prev {
  left: -14px;
}

.btn-carousel-next {
  right: -14px;
}

.btn-carousel-nav:hover {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
  transform: translateY(-50%) scale(1.12);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

/* THẺ SẢN PHẨM NỔI BẬT NỀN TRẮNG CHUẨN FORM MẪU */
.showcase-product-card {
  flex: 0 0 calc(20% - 11.2px);
  min-width: 215px;
  background: #ffffff;
  border-radius: 12px;
  padding: 12px 14px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
  box-sizing: border-box;
}

.showcase-product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 28px rgba(0, 0, 0, 0.16);
}

/* BADGES GÓC TRÊN THẺ */
.card-top-badges {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.badge-discount {
  background: #dc2626;
  color: #ffffff;
  font-size: 11px;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 4px;
}

.badge-installment {
  background: #e0f2fe;
  color: #0284c7;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 4px;
}

/* KHUNG HÌNH ẢNH & DẢI ICON ĐỒ HỌA */
.card-visual-box {
  position: relative;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 4px 0 10px;
}

.showcase-img {
  max-height: 100%;
  max-width: 82%;
  object-fit: contain;
  transition: transform 0.35s ease;
}

.showcase-product-card:hover .showcase-img {
  transform: scale(1.06);
}

.app-icons-strip {
  position: absolute;
  top: 6px;
  right: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.app-icon {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
}

.app-ai { background: #ff7700; }
.app-ps { background: #001e36; color: #31a8ff; }
.app-office { background: #d83b01; }

/* KHUNG THÔNG SỐ PILLS BOX (NHƯ MẪU) */
.card-specs-box {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 8px;
  padding: 6px 8px;
  margin-bottom: 10px;
  text-align: center;
  line-height: 1.35;
}

.spec-line-1 {
  font-size: 11px;
  font-weight: 800;
  color: #ef4444;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.spec-line-2 {
  font-size: 11px;
  font-weight: 700;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 2px;
}

/* TIÊU ĐỀ SẢN PHẨM */
.showcase-prod-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.4;
  height: 38px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 8px;
}

.showcase-product-card:hover .showcase-prod-title {
  color: #ef4444;
}

/* GIÁ TIỀN */
.showcase-pricing {
  margin-bottom: 10px;
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 6px;
}

.showcase-price-current {
  font-size: 16.5px;
  font-weight: 900;
  color: #e11d48;
}

.showcase-price-original {
  font-size: 11.5px;
  color: #94a3b8;
  text-decoration: line-through;
}

/* FOOTER THẺ: GIAO 2 GIỜ & NÚT TIM */
.showcase-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 8px;
  border-top: 1px solid #f1f5f9;
}

.badge-shipping-speed {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  color: #2563eb;
  font-size: 11px;
  font-weight: 800;
  padding: 3px 8px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
}

.btn-wishlist {
  background: none;
  border: none;
  font-size: 18px;
  color: #2563eb;
  cursor: pointer;
  padding: 0 4px;
  transition: transform 0.2s ease;
}

.btn-wishlist:hover {
  transform: scale(1.25);
}

.btn-wishlist.active {
  color: #ef4444;
}

/* ❿ - ⓫ FLASH SALE */
.flash-sale-section {
  margin-top: 24px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 18px 20px 22px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.flash-sale-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 14px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 18px;
}

.flash-title-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}

.flash-bolt {
  font-size: 22px;
  animation: flashBoltAnim 1.8s infinite;
}

@keyframes flashBoltAnim {
  0%, 100% { transform: scale(1); filter: drop-shadow(0 0 2px rgba(239, 68, 68, 0.5)); }
  50% { transform: scale(1.18); filter: drop-shadow(0 0 8px rgba(239, 68, 68, 0.9)); }
}

.flash-title {
  font-size: 17px;
  font-weight: 900;
  letter-spacing: 0.5px;
  color: #0f172a;
}

.flash-timer-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #475569;
}

.timer-boxes {
  display: flex;
  align-items: center;
  gap: 4px;
}

.timer-box {
  background: #0f172a;
  color: #ffffff;
  font-weight: 800;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 13px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ==========================================
   PRODUCT CARDS & GRIDS (SMOOTH HOVER & ZOOM ANIMATIONS)
   ========================================== */
.product-grid-4-cols,
.flash-sale-grid,
.best-seller-grid,
.all-products-grid {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
  gap: 16px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.product-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-width: 0 !important;
  width: 100% !important;
  box-sizing: border-box !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
              box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
              border-color 0.25s ease;
  will-change: transform, box-shadow;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 26px -4px rgba(15, 23, 42, 0.12),
              0 4px 10px -2px rgba(37, 99, 235, 0.1);
  border-color: #3b82f6;
}

.discount-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #ffffff;
  font-size: 10.5px;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 4px;
  z-index: 5;
  box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
  animation: fireBadgePulse 2.5s infinite;
}

@keyframes fireBadgePulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.6);
  }
}

.tag-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #0f172a;
  color: #ffffff;
  font-size: 10.5px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 4px;
  z-index: 5;
}

.product-img-box {
  height: 165px;
  background: #ffffff;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 18px 0 10px;
  padding: 6px;
  position: relative;
  overflow: hidden;
}

.product-img-box img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
  transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
  will-change: transform;
}

.product-card:hover .product-img-box img {
  transform: scale(1.09);
}

.img-mock-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #f8fafc;
  width: 100%;
  height: 100%;
  border-radius: 8px;
  transition: background 0.2s;
}

.product-card:hover .img-mock-box {
  background: #f1f5f9;
}

.img-placeholder {
  font-size: 11px;
  color: #64748b;
  font-weight: 700;
}

.product-title {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.35;
  height: 38px;
  transition: color 0.15s;
}

.product-card:hover .product-title {
  color: #2563eb;
}

.product-specs {
  font-size: 11.5px;
  color: #64748b;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
  height: 32px;
}

.product-pricing {
  margin-bottom: 8px;
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
}

.price-current {
  font-size: 17px;
  font-weight: 800;
  color: #e11d48;
}

.price-original {
  font-size: 11.5px;
  color: #94a3b8;
  text-decoration: line-through;
  margin-left: 6px;
}

.progress-container {
  background: #fee2e2;
  height: 20px;
  border-radius: 12px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 6px;
}

.progress-bar {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  background: linear-gradient(90deg, #ea580c 0%, #dc2626 100%);
  border-radius: 12px;
  transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.progress-text {
  position: relative;
  z-index: 2;
  font-size: 10.5px;
  font-weight: 800;
  color: #ffffff;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
}

/* ⓬ CALLOUT 12: DANH MỤC THIẾT BỊ NỔI BẬT */
.categories-section {
  margin-top: 28px;
}

.section-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 14px;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(8, minmax(0, 1fr));
  gap: 12px;
}

.category-item {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 14px 6px;
  text-align: center;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.category-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 10px 20px -3px rgba(59, 130, 246, 0.15);
  transform: translateY(-4px);
}

.cat-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.category-item:hover .cat-icon-box {
  transform: scale(1.15) rotate(3deg);
}

.cat-blue { background: #eff6ff; color: #2563eb; }
.cat-slate { background: #f1f5f9; color: #475569; }
.cat-dark { background: #f8fafc; color: #0f172a; border: 1px solid #e2e8f0; }
.cat-cyan { background: #ecfeff; color: #0891b2; }
.cat-rose { background: #fff1f2; color: #e11d48; }
.cat-amber { background: #fffbeb; color: #d97706; }
.cat-emerald { background: #ecfdf5; color: #059669; }
.cat-indigo { background: #e0e7ff; color: #4f46e5; }

.cat-name {
  font-size: 11.5px;
  font-weight: 700;
  color: #334155;
  line-height: 1.25;
}

/* ⓭ CALLOUT 13: GỢI Ý BÁN CHẠY ACTION BUTTONS */
.best-seller-section {
  margin-top: 28px;
}

.card-btn-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
  margin-top: 10px;
}

.btn-card-detail {
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  color: #334155;
  font-size: 11.5px;
  font-weight: 600;
  padding: 6px 0;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-card-detail:hover {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
  transform: translateY(-2px);
}

.btn-card-add {
  background: #0f172a;
  border: 1px solid #0f172a;
  color: #ffffff;
  font-size: 11.5px;
  font-weight: 700;
  padding: 6px 0;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-card-add:hover {
  background: #2563eb;
  border-color: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}

/* ⓭.1 CALLOUT: TẤT CẢ SẢN PHẨM CÔNG NGHỆ */
.all-products-section {
  margin-top: 32px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 22px 24px 28px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}

.section-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.header-action-wrap {
  display: flex;
  align-items: center;
}

.btn-toggle-view-all-top {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #2563eb;
  font-size: 12.5px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  display: inline-flex;
  align-items: center;
}

.btn-toggle-view-all-top:hover {
  background: #2563eb;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

/* NÚT XEM TẤT CẢ / THU GỌN LỚN Ở PHÍA DƯỚI */
.view-all-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 26px;
  padding-top: 18px;
  border-top: 1px dashed #e2e8f0;
}

.btn-toggle-view-all {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #ffffff;
  border: 1px solid #334155;
  font-size: 13.5px;
  font-weight: 700;
  padding: 10px 32px;
  border-radius: 25px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
}

.btn-toggle-view-all:hover {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  border-color: #2563eb;
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35);
}

.btn-toggle-view-all i {
  transition: transform 0.3s ease;
}

.btn-toggle-view-all:hover i {
  transform: translateY(2px);
}

/* ⓮ CALLOUT 14: THƯƠNG HIỆU & CAM KẾT */
.brand-and-commitments {
  margin-top: 28px;
}

.brands-wrapper {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 18px 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.brands-title {
  font-size: 15px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 14px;
}

.brands-grid {
  display: grid;
  grid-template-columns: repeat(8, minmax(0, 1fr));
  gap: 10px;
}

.brand-box {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px 6px;
  text-align: center;
  font-size: 12.5px;
  font-weight: 800;
  color: #334155;
  letter-spacing: 0.5px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
}

.brand-box:hover {
  border-color: #2563eb;
  color: #2563eb;
  box-shadow: 0 6px 14px rgba(37, 99, 235, 0.12);
  transform: translateY(-3px);
}

.commitments-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-top: 16px;
}

.commit-item {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
  transition: all 0.2s ease;
}

.commit-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.commit-icon-wrap {
  font-size: 26px;
  flex-shrink: 0;
  transition: transform 0.2s;
}

.commit-item:hover .commit-icon-wrap {
  transform: scale(1.15);
}

.commit-name {
  font-size: 13.5px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 2px;
}

.commit-desc {
  font-size: 11.5px;
  color: #64748b;
  line-height: 1.35;
  margin: 0;
}

/* ⓯ - ⓰ FOOTER */
.main-footer {
  background: #0b1120;
  color: #94a3b8;
  margin-top: 40px;
  padding-top: 36px;
  border-top: 1px solid #1e293b;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr;
  gap: 28px;
  padding-bottom: 30px;
}

.footer-heading {
  font-size: 13.5px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.3px;
  margin-bottom: 14px;
}

.footer-text {
  font-size: 12.5px;
  line-height: 1.6;
  color: #cbd5e1;
  margin-bottom: 12px;
}

.footer-contact-item {
  font-size: 12px;
  color: #94a3b8;
  margin-bottom: 6px;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  font-size: 12.5px;
  margin-bottom: 8px;
}

.footer-links a {
  color: #cbd5e1;
  text-decoration: none;
  transition: color 0.15s, padding-left 0.15s;
}

.footer-links a:hover {
  color: #38bdf8;
  padding-left: 4px;
}

.hotline-highlight {
  font-size: 13px;
  color: #f8fafc;
  margin-bottom: 4px;
}

.hotline-highlight strong {
  color: #fbbf24;
}

.copyright-bar {
  border-top: 1px solid #1e293b;
  padding: 14px 0;
  font-size: 12px;
  color: #64748b;
}

/* ==========================================
   SCROLL REVEAL ANIMATIONS (INTERSECTION OBSERVER & STAGGER)
   ========================================== */
.scroll-progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 3.5px;
  background: linear-gradient(90deg, #38bdf8 0%, #6366f1 50%, #ec4899 100%);
  z-index: 9999;
  transition: width 0.1s linear;
  box-shadow: 0 0 10px rgba(56, 189, 248, 0.7);
}

.scroll-reveal {
  opacity: 0;
  transform: translateY(32px);
  transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1),
              transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
  will-change: opacity, transform;
}

.scroll-reveal.is-revealed {
  opacity: 1;
  transform: translateY(0);
}

/* STAGGERED SCROLL REVEAL CHO PRODUCT CARDS */
.scroll-reveal .product-card {
  opacity: 0;
  transform: translateY(28px) scale(0.97);
  transition: opacity 0.55s cubic-bezier(0.16, 1, 0.3, 1),
              transform 0.55s cubic-bezier(0.16, 1, 0.3, 1),
              box-shadow 0.35s ease,
              border-color 0.35s ease;
  will-change: opacity, transform;
}

.scroll-reveal.is-revealed .product-card {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.scroll-reveal.is-revealed .product-card:nth-child(1) { transition-delay: 0.04s; }
.scroll-reveal.is-revealed .product-card:nth-child(2) { transition-delay: 0.10s; }
.scroll-reveal.is-revealed .product-card:nth-child(3) { transition-delay: 0.16s; }
.scroll-reveal.is-revealed .product-card:nth-child(4) { transition-delay: 0.22s; }
.scroll-reveal.is-revealed .product-card:nth-child(5) { transition-delay: 0.28s; }
.scroll-reveal.is-revealed .product-card:nth-child(6) { transition-delay: 0.34s; }
.scroll-reveal.is-revealed .product-card:nth-child(7) { transition-delay: 0.40s; }
.scroll-reveal.is-revealed .product-card:nth-child(8) { transition-delay: 0.46s; }

/* NÚT BACK TO TOP VỚI VÒNG TIẾN ĐỘ TIẾN TRÌNH */
.btn-back-to-top {
  position: fixed;
  bottom: 84px;
  right: 26px;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  color: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 99;
  box-shadow: 0 6px 20px rgba(15, 23, 42, 0.15);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-back-to-top:hover {
  transform: translateY(-4px) scale(1.08);
  box-shadow: 0 12px 28px rgba(37, 99, 235, 0.3);
  border-color: #3b82f6;
  color: #2563eb;
}

.progress-ring {
  position: absolute;
  top: 0;
  left: 0;
  transform: rotate(-90deg);
}

.progress-ring__circle-bg {
  stroke: #f1f5f9;
}

.progress-ring__circle {
  stroke: #2563eb;
  stroke-linecap: round;
  stroke-dasharray: 119.38;
  transition: stroke-dashoffset 0.1s linear;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(14px) scale(0.9);
}

/* ⓱ FLOATING AI WIDGET (PULSE RADAR ANIMATION) */
.floating-ai-widget {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 100;
}

.ai-float-btn {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
  color: #fbbf24;
  border: 2px solid #6366f1;
  border-radius: 30px;
  padding: 10px 18px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 800;
  font-size: 13px;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
}

/* Pulse Radar wave effect */
.ai-float-btn::before {
  content: '';
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  bottom: -4px;
  border-radius: 35px;
  border: 2px solid #6366f1;
  opacity: 0.8;
  animation: aiRadarWave 2.4s infinite cubic-bezier(0.24, 0, 0.38, 1);
}

@keyframes aiRadarWave {
  0% {
    transform: scale(1);
    opacity: 0.8;
  }
  100% {
    transform: scale(1.3);
    opacity: 0;
  }
}

.ai-float-btn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 12px 28px rgba(99, 102, 241, 0.6);
}

.ai-btn-label {
  color: #ffffff;
  letter-spacing: 0.5px;
}

.ai-chat-box {
  position: absolute;
  bottom: 60px;
  right: 0;
  width: 390px;
  max-width: calc(100vw - 32px);
  height: 510px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  animation: chatPopIn 0.32s cubic-bezier(0.16, 1, 0.3, 1) both;
  transform-origin: bottom right;
}

@keyframes chatPopIn {
  0% {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.chat-header {
  background: #0f172a;
  color: #ffffff;
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-header-left {
  display: flex;
  align-items: center;
}

.chat-header-title {
  font-size: 13.5px;
  font-weight: 800;
}

.chat-header-status {
  font-size: 11px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 4px;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #22c55e;
}

.btn-reset-chat,
.btn-close-chat {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  font-size: 14px;
  transition: color 0.15s;
}

.btn-reset-chat:hover,
.btn-close-chat:hover {
  color: #ffffff;
}

.ai-inline-code {
  background: #eff6ff;
  color: #1d4ed8;
  padding: 1px 5px;
  border-radius: 4px;
  font-size: 11.5px;
  font-weight: 600;
}

.ai-suggested-cards {
  border-top: 1px dashed #cbd5e1;
  padding-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ai-suggest-label {
  font-size: 11px;
  font-weight: 700;
  color: #475569;
}

.ai-prod-card {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 6px 8px;
  transition: border-color 0.15s;
}

.ai-prod-card:hover {
  border-color: #3b82f6;
}

.ai-prod-thumb {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 6px;
  background: #ffffff;
  flex-shrink: 0;
}

.ai-prod-info {
  flex: 1;
  min-width: 0;
}

.ai-prod-name {
  font-size: 11.5px;
  font-weight: 700;
  color: #0f172a;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ai-prod-price {
  font-size: 11.5px;
  font-weight: 800;
  color: #dc2626;
}

.ai-prod-meta {
  font-size: 10px;
  color: #64748b;
}

.btn-ai-add-cart {
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 6px;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex-shrink: 0;
  transition: background 0.15s;
}

.btn-ai-add-cart:hover {
  background: #1d4ed8;
}

.ai-thinking-bubble {
  display: flex;
  align-items: center;
  gap: 4px;
}

.thinking-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #3b82f6;
  animation: aiBlink 1.2s infinite ease-in-out;
}

.thinking-dot:nth-child(2) {
  animation-delay: 0.2s;
}

.thinking-dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes aiBlink {
  0%, 80%, 100% { opacity: 0.25; transform: scale(0.8); }
  40% { opacity: 1; transform: scale(1.1); }
}

.chat-body {
  flex: 1;
  padding: 14px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #f8fafc;
}

.chat-bubble {
  max-width: 82%;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 12.5px;
  line-height: 1.45;
}

.bubble-ai {
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e2e8f0;
  align-self: flex-start;
  border-bottom-left-radius: 2px;
}

.bubble-user {
  background: #2563eb;
  color: #ffffff;
  align-self: flex-end;
  border-bottom-right-radius: 2px;
}

.chat-quick-chips {
  padding: 8px 12px;
  display: flex;
  gap: 6px;
  overflow-x: auto;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
}

.chip {
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  color: #334155;
  font-size: 11px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 20px;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s;
}

.chip:hover {
  background: #0f172a;
  color: #ffffff;
}

.chat-input-area {
  display: flex;
  padding: 10px 12px;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
  gap: 8px;
}

.chat-input-area input {
  flex: 1;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 12.5px;
  outline: none;
}

.btn-send-ai {
  background: #2563eb;
  color: #ffffff;
  border: none;
  border-radius: 6px;
  padding: 0 12px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

/* ==========================================
   RESPONSIVE
   ========================================== */
@media (max-width: 1200px) {
  .product-grid-4-cols,
  .flash-sale-grid,
  .best-seller-grid,
  .all-products-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 12px !important;
  }
}

@media (max-width: 1024px) {
  .hero-grid {
    grid-template-columns: 1fr;
  }
  .showcase-product-card {
    flex: 0 0 calc(25% - 10.5px);
  }
  .product-grid-4-cols,
  .flash-sale-grid,
  .best-seller-grid,
  .all-products-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
  }
  .categories-grid,
  .brands-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  .commitments-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .footer-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 768px) {
  .showcase-product-card {
    flex: 0 0 calc(33.333% - 9.5px);
  }
  .text-white-bold,
  .text-outline-glow {
    font-size: 22px;
  }
  .product-grid-4-cols,
  .flash-sale-grid,
  .best-seller-grid,
  .all-products-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 10px !important;
  }
  .categories-grid,
  .brands-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
  .commitments-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .footer-grid {
    grid-template-columns: 1fr;
  }
  .main-header {
    flex-direction: column;
    align-items: stretch;
  }
  .header-actions {
    justify-content: space-between;
  }
}

@media (max-width: 480px) {
  .showcase-product-card {
    flex: 0 0 calc(50% - 7px);
    min-width: 170px;
  }
  .text-white-bold,
  .text-outline-glow {
    font-size: 18px;
  }
  .fire-badge {
    width: 28px;
    height: 28px;
    font-size: 15px;
  }
  .product-grid-4-cols,
  .flash-sale-grid,
  .best-seller-grid,
  .all-products-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 8px !important;
  }
  .categories-grid,
  .brands-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .commitments-grid {
    grid-template-columns: 1fr;
  }
}

/* ==========================================
   CSS TOAST NOTIFICATION & CART ANIMATION
   ========================================== */
.cart-toast-fixed {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 12px 36px -4px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.08);
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 18px;
  max-width: 440px;
  width: calc(100vw - 32px);
  backdrop-filter: blur(10px);
}

.toast-left-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(16, 185, 129, 0.12);
  color: #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.toast-center-content {
  flex: 1;
  min-width: 0;
}

.toast-head strong {
  font-size: 13.5px;
  font-weight: 700;
  color: #0f172a;
}

.toast-msg {
  font-size: 12px;
  color: #64748b;
  margin: 2px 0 6px 0;
  line-height: 1.4;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.toast-preview-item {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  padding: 6px 10px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.toast-thumb {
  width: 32px;
  height: 32px;
  object-fit: contain;
  border-radius: 4px;
  background: #ffffff;
}

.toast-thumb-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.toast-prod-name {
  font-size: 11.5px;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
}

.toast-prod-price {
  font-size: 11.5px;
  font-weight: 700;
  color: #ef4444;
}

.toast-right-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn-toast-view-cart {
  background: #2563eb;
  color: #ffffff !important;
  font-size: 11.5px;
  font-weight: 700;
  padding: 7px 12px;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-toast-view-cart:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

.btn-toast-dismiss {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 14px;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px;
  transition: color 0.2s;
}

.btn-toast-dismiss:hover {
  color: #0f172a;
}

/* Animations */
.toast-popup-enter-active {
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.toast-popup-leave-active {
  transition: all 0.25s ease-in;
}
.toast-popup-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
.toast-popup-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.95);
}

.badge-bounce {
  animation: badgeBounceKeyframe 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes badgeBounceKeyframe {
  0% { transform: scale(1); }
  35% { transform: scale(1.45); background: #10b981; }
  70% { transform: scale(0.9); }
  100% { transform: scale(1); }
}

.showcase-actions-group {
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-showcase-add-cart {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: rgba(37, 99, 235, 0.1);
  border: 1px solid rgba(37, 99, 235, 0.25);
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-showcase-add-cart:hover {
  background: #2563eb;
  color: #ffffff;
  transform: scale(1.1);
}

.spin {
  display: inline-block;
  animation: spinKeyframe 0.8s linear infinite;
}

@keyframes spinKeyframe {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ==========================================
   CSS AUTH HEADER & MODAL POPUP
   ========================================== */
.user-avatar-img {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  object-fit: cover;
  border: 1.5px solid #2563eb;
  margin-right: 6px;
}

.user-dropdown-header {
  padding: 8px 14px 10px;
  display: flex;
  flex-direction: column;
}

.user-dropdown-name {
  font-size: 13.5px;
  color: #0f172a;
  font-weight: 700;
}

.user-dropdown-email {
  font-size: 11.5px;
  color: #64748b;
}

.guest-auth-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-header-login {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #0f172a;
  font-size: 12.5px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
}

.btn-header-login:hover {
  background: #e2e8f0;
  border-color: #94a3b8;
}

.btn-header-register {
  background: #2563eb;
  border: 1px solid #2563eb;
  color: #ffffff !important;
  font-size: 12.5px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
}

.btn-header-register:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

/* Modal Backdrop & Overlay */
.modal-backdrop-custom {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.modal-overlay-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.72);
  backdrop-filter: blur(8px);
  z-index: 1;
}

.auth-modal-dialog {
  position: relative;
  z-index: 2;
  background: #ffffff;
  border-radius: 24px;
  max-width: 480px;
  width: 100%;
  box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
  overflow: hidden;
  animation: modalPop 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.92) translateY(16px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.auth-modal-header {
  padding: 18px 24px;
  background: #ffffff;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.modal-icon-badge {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(59, 130, 246, 0.2));
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.modal-title {
  font-size: 17px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.3px;
}

.modal-subtitle {
  font-size: 12px;
  color: #64748b;
  display: block;
  margin-top: 2px;
}

.btn-close-modal {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #f1f5f9;
  border: none;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-close-modal:hover {
  background: #fee2e2;
  color: #ef4444;
}

/* Modal Tabs Bar */
.modal-tabs-bar {
  display: flex;
  background: #f8fafc;
  padding: 6px;
  border-bottom: 1px solid #f1f5f9;
  gap: 6px;
}

.modal-tab-pill {
  flex: 1;
  padding: 10px 14px;
  border: none;
  border-radius: 10px;
  background: transparent;
  color: #64748b;
  font-size: 12.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-tab-pill:hover {
  color: #0f172a;
  background: rgba(255, 255, 255, 0.6);
}

.modal-tab-pill.active {
  background: #ffffff;
  color: #2563eb;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.auth-modal-body {
  padding: 20px 24px 24px;
  max-height: calc(85vh - 120px);
  overflow-y: auto;
}

/* Alert inside modal */
.auth-alert {
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
  margin-bottom: 16px;
}

.auth-alert.alert-danger {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.auth-alert.alert-success {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

/* Form controls */
.modal-auth-form .modal-label {
  font-size: 12.5px;
  font-weight: 700;
  color: #334155;
  margin-bottom: 5px;
  display: block;
}

.input-with-icon {
  position: relative;
  display: flex;
  align-items: center;
}

.input-with-icon .input-icon {
  position: absolute;
  left: 12px;
  color: #94a3b8;
  font-size: 16px;
  pointer-events: none;
}

.modal-input {
  width: 100%;
  padding: 10px 12px 10px 38px !important;
  font-size: 13.5px !important;
  border-radius: 10px !important;
  background: #f8fafc !important;
  color: #0f172a !important;
  border: 1px solid #e2e8f0 !important;
  transition: all 0.2s ease;
}

.input-with-icon:not(:has(.input-icon)) .modal-input,
.row .modal-input {
  padding-left: 12px !important;
}

.modal-input:focus {
  background: #ffffff !important;
  border-color: #2563eb !important;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
  outline: none;
}

.btn-toggle-eye {
  position: absolute;
  right: 12px;
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0;
  font-size: 16px;
  transition: color 0.2s;
}

.btn-toggle-eye:hover {
  color: #334155;
}

.btn-modal-submit {
  width: 100%;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  padding: 12px;
  font-size: 13.5px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 18px -3px rgba(37, 99, 235, 0.35);
  margin-top: 18px;
}

.btn-modal-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #1d4ed8, #1e40af);
  transform: translateY(-1px);
  box-shadow: 0 8px 22px -3px rgba(37, 99, 235, 0.45);
}

.btn-modal-register {
  background: linear-gradient(135deg, #10b981, #059669) !important;
  box-shadow: 0 6px 18px -3px rgba(16, 185, 129, 0.35) !important;
}

.btn-modal-register:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669, #047857) !important;
  box-shadow: 0 8px 22px -3px rgba(16, 185, 129, 0.45) !important;
}

.btn-modal-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.modal-footer-switch {
  font-size: 12.5px;
  color: #64748b;
  margin-top: 14px;
}

.modal-footer-switch a {
  cursor: pointer;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
