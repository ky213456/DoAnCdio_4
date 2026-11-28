<template>
  <div class="checkout-page-wrapper">
    <!-- ========================================== -->
    <!-- ❶ HEADER TRANG THANH TOÁN (TOPBAR & STEPS) -->
    <!-- ========================================== -->
    <header class="checkout-header">
      <div class="main-width header-inner">
        <!-- Logo & Nút về giỏ hàng -->
        <div class="header-left">
          <router-link to="/" class="brand-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            <span class="logo-text">TECHMART</span>
            <span class="badge-checkout ms-2">CHECKOUT</span>
          </router-link>
          <div class="header-breadcrumb ms-4 d-none d-md-flex">
            <router-link to="/cart" class="breadcrumb-link">
              <i class="bi bi-arrow-left me-1"></i> Quay lại Giỏ hàng
            </router-link>
          </div>
        </div>

        <!-- Quy trình các bước (Checkout Stepper) -->
        <div class="checkout-stepper d-none d-lg-flex">
          <div class="step-item step-completed">
            <span class="step-number"><i class="bi bi-check-lg"></i></span>
            <span class="step-label">1. Giỏ hàng</span>
          </div>
          <div class="step-line active"></div>
          <div class="step-item step-active">
            <span class="step-number">2</span>
            <span class="step-label">2. Địa chỉ & Bản đồ</span>
          </div>
          <div class="step-line"></div>
          <div class="step-item">
            <span class="step-number">3</span>
            <span class="step-label">3. Thanh toán</span>
          </div>
        </div>

        <!-- Hotline & Khách hàng -->
        <div class="header-right">
          <div class="hotline-box d-none d-sm-flex">
            <i class="bi bi-headset me-2 text-danger fs-5"></i>
            <div>
              <span class="d-block small text-muted">Hỗ trợ đặt hàng:</span>
              <strong class="text-danger">1900.6789</strong>
            </div>
          </div>
          <div class="user-pill ms-3" v-if="currentUser">
            <i class="bi bi-person-circle text-primary me-1"></i>
            <span class="user-name">{{ currentUser.ho_ten || currentUser.name || 'Khách hàng' }}</span>
          </div>
        </div>
      </div>
    </header>

    <!-- ========================================== -->
    <!-- ❷ THÂN TRANG CHECKOUT: 2 CỘT HIỆN ĐẠI -->
    <!-- ========================================== -->
    <main class="main-width checkout-main-content">
      <!-- Trường hợp giỏ hàng thanh toán rỗng -->
      <div v-if="orderItems.length === 0 && !isLoadingCart" class="empty-checkout-card">
        <div class="empty-icon text-muted">
          <i class="bi bi-cart-x"></i>
        </div>
        <h3 class="mt-3 fw-bold">Chưa có sản phẩm nào được chọn để thanh toán!</h3>
        <p class="text-muted">Vui lòng quay lại giỏ hàng và tích chọn ít nhất 1 thiết bị để tiếp tục quy trình mua hàng.</p>
        <div class="mt-4">
          <router-link to="/cart" class="btn btn-primary px-4 py-2 me-3">
            <i class="bi bi-cart3 me-1"></i> Quay lại Giỏ hàng
          </router-link>
          <router-link to="/" class="btn btn-outline-secondary px-4 py-2">
            <i class="bi bi-house-door me-1"></i> Về Trang chủ
          </router-link>
        </div>
      </div>

      <!-- Khung 2 cột Đặt hàng chính -->
      <div v-else class="checkout-grid">
        <!-- ========================================== -->
        <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG & BẢN ĐỒ -->
        <!-- ========================================== -->
        <div class="checkout-left-col">
          <!-- KHỐI 1: THÔNG TIN LIÊN HỆ -->
          <div class="checkout-card mb-4">
            <div class="card-header-custom">
              <div class="header-icon-circle bg-primary-subtle text-primary">
                <i class="bi bi-person-vcard-fill"></i>
              </div>
              <div>
                <h4 class="card-title-custom">1. Thông tin người nhận hàng</h4>
                <p class="card-subtitle-custom">TechMart sẽ liên hệ theo thông tin này để xác nhận và giao hàng tận tay.</p>
              </div>
            </div>

            <div class="card-body-custom">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label-custom">
                    Họ và tên người nhận <span class="text-danger">*</span>
                  </label>
                  <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-person"></i></span>
                    <input
                      type="text"
                      v-model="customerForm.ho_ten"
                      placeholder="Ví dụ: Nguyễn Văn Kỳ"
                      class="form-control-custom"
                      :class="{ 'is-invalid': formErrors.ho_ten }"
                    />
                  </div>
                  <div v-if="formErrors.ho_ten" class="error-feedback">{{ formErrors.ho_ten }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label-custom">
                    Số điện thoại nhận hàng <span class="text-danger">*</span>
                  </label>
                  <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-telephone"></i></span>
                    <input
                      type="tel"
                      v-model="customerForm.so_dien_thoai"
                      placeholder="Ví dụ: 0905123456 (10 số)"
                      class="form-control-custom"
                      :class="{ 'is-invalid': formErrors.so_dien_thoai }"
                    />
                  </div>
                  <div v-if="formErrors.so_dien_thoai" class="error-feedback">{{ formErrors.so_dien_thoai }}</div>
                </div>

                <div class="col-12">
                  <label class="form-label-custom">
                    Email nhận thông báo hóa đơn & kích hoạt bảo hành điện tử
                  </label>
                  <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                    <input
                      type="email"
                      v-model="customerForm.email"
                      placeholder="Ví dụ: nguyenvanky@gmail.com"
                      class="form-control-custom"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- KHỐI 2: ĐỊA CHỈ PHÂN CẤP & BẢN ĐỒ TƯƠNG TÁC -->
          <div class="checkout-card mb-4">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="header-icon-circle bg-success-subtle text-success me-3">
                  <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                  <h4 class="card-title-custom">2. Địa chỉ giao hàng & Bản đồ định vị</h4>
                  <p class="card-subtitle-custom">Chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã hoặc ghim trực tiếp trên bản đồ.</p>
                </div>
              </div>

              <!-- Nút lấy GPS hiện tại -->
              <button
                type="button"
                class="btn-gps-locate"
                @click="getCurrentLocation"
                :disabled="isLocatingGps"
                title="Tự động tìm vị trí qua định vị vệ tinh GPS"
              >
                <i class="bi bi-crosshair me-1" :class="{ 'spin-icon': isLocatingGps }"></i>
                {{ isLocatingGps ? 'Đang định vị GPS...' : 'Vị trí của tôi' }}
              </button>
            </div>

            <div class="card-body-custom">
              <!-- Cụm 3 Dropdown Tỉnh/Thành - Quận/Huyện - Phường/Xã -->
              <div class="row g-3 mb-3">
                <!-- 1. Tỉnh / Thành phố -->
                <div class="col-md-4">
                  <label class="form-label-custom">
                    Tỉnh / Thành phố <span class="text-danger">*</span>
                  </label>
                  <select
                    v-model="addressForm.provinceCode"
                    @change="onProvinceChange"
                    class="form-select-custom"
                    :class="{ 'is-invalid': formErrors.province }"
                  >
                    <option value="">-- Chọn Tỉnh / TP --</option>
                    <option v-for="prov in provincesList" :key="prov.code" :value="prov.code">
                      {{ prov.name }}
                    </option>
                  </select>
                </div>

                <!-- 2. Quận / Huyện -->
                <div class="col-md-4">
                  <label class="form-label-custom">
                    Quận / Huyện <span class="text-danger">*</span>
                  </label>
                  <select
                    v-model="addressForm.districtCode"
                    @change="onDistrictChange"
                    class="form-select-custom"
                    :disabled="!addressForm.provinceCode"
                    :class="{ 'is-invalid': formErrors.district }"
                  >
                    <option value="">-- Chọn Quận / Huyện --</option>
                    <option v-for="dist in districtsList" :key="dist.code" :value="dist.code">
                      {{ dist.name }}
                    </option>
                  </select>
                </div>

                <!-- 3. Phường / Xã -->
                <div class="col-md-4">
                  <label class="form-label-custom">
                    Phường / Xã <span class="text-danger">*</span>
                  </label>
                  <select
                    v-model="addressForm.wardCode"
                    @change="onWardChange"
                    class="form-select-custom"
                    :disabled="!addressForm.districtCode"
                    :class="{ 'is-invalid': formErrors.ward }"
                  >
                    <option value="">-- Chọn Phường / Xã --</option>
                    <option v-for="ward in wardsList" :key="ward.code" :value="ward.code">
                      {{ ward.name }}
                    </option>
                  </select>
                </div>

                <!-- Số nhà, Tên đường -->
                <div class="col-12">
                  <label class="form-label-custom">
                    Số nhà, Tên đường, Tòa nhà / Căn hộ <span class="text-danger">*</span>
                  </label>
                  <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-signpost-2"></i></span>
                    <input
                      type="text"
                      v-model="addressForm.street"
                      @input="updateCombinedAddress"
                      placeholder="Ví dụ: Số 254 Nguyễn Văn Linh, Tòa A Tầng 5"
                      class="form-control-custom"
                      :class="{ 'is-invalid': formErrors.street }"
                    />
                  </div>
                  <div v-if="formErrors.street" class="error-feedback">{{ formErrors.street }}</div>
                </div>
              </div>

              <!-- Thanh tìm kiếm địa danh trên bản đồ -->
              <div class="map-search-bar mb-2">
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input
                    type="text"
                    v-model="mapSearchQuery"
                    @keyup.enter="searchPlaceOnMap"
                    placeholder="Tìm kiếm địa điểm nhanh (ví dụ: Đại học Duy Tân, Chợ Cồn, Sân bay Đà Nẵng...)"
                    class="form-control border-start-0 ps-0 shadow-none"
                  />
                  <button
                    class="btn btn-outline-primary px-3"
                    type="button"
                    @click="searchPlaceOnMap"
                    :disabled="isSearchingMap"
                  >
                    <i class="bi bi-pin-map-fill me-1"></i>
                    {{ isSearchingMap ? 'Đang tìm...' : 'Ghim trên bản đồ' }}
                  </button>
                </div>
              </div>

              <!-- KHUNG BẢN ĐỒ TƯƠNG TÁC LEAFLET (Kiểu Map) -->
              <div class="map-wrapper-box">
                <div class="map-toolbar-badge">
                  <span class="badge bg-dark bg-opacity-75 text-white">
                    <i class="bi bi-info-circle me-1 text-warning"></i>
                    Mẹo: Bấm chuột hoặc kéo thả ghim đỏ để chọn vị trí nhà bạn chính xác
                  </span>
                  <span class="badge bg-primary text-white ms-2" v-if="currentCoords">
                    <i class="bi bi-geo"></i> {{ currentCoords.lat.toFixed(4) }}, {{ currentCoords.lng.toFixed(4) }}
                  </span>
                </div>
                <!-- Container chứa bản đồ Leaflet -->
                <div id="checkout-leaflet-map" class="interactive-leaflet-map"></div>
              </div>

              <!-- HỘP HIỂN THỊ ĐỊA CHỈ HOÀN CHỈNH TỔNG HỢP -->
              <div class="combined-address-card mt-3">
                <div class="address-preview-icon text-primary">
                  <i class="bi bi-truck"></i>
                </div>
                <div class="address-preview-content">
                  <span class="preview-label">ĐỊA CHỈ GIAO HÀNG ĐÃ CHỌN:</span>
                  <strong class="preview-text text-dark d-block">
                    {{ fullShippingAddress || 'Vui lòng chọn Tỉnh/Thành và nhập số nhà tên đường...' }}
                  </strong>
                  <span class="preview-status text-success small mt-1 d-inline-block">
                    <i class="bi bi-shield-check me-1"></i> Miễn phí giao hàng hỏa tốc 2H toàn quốc
                  </span>
                </div>
              </div>

              <!-- GHI CHÚ ĐƠN HÀNG -->
              <div class="mt-3">
                <label class="form-label-custom">Ghi chú giao hàng (Tùy chọn)</label>
                <textarea
                  v-model="customerForm.ghi_chu"
                  rows="2"
                  placeholder="Ví dụ: Gọi trước khi giao 15 phút, giao giờ hành chính, để ở phòng bảo vệ..."
                  class="form-control-custom"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- KHỐI 3: PHƯƠNG THỨC THANH TOÁN -->
          <div class="checkout-card mb-4">
            <div class="card-header-custom">
              <div class="header-icon-circle bg-warning-subtle text-warning">
                <i class="bi bi-wallet-fill"></i>
              </div>
              <div>
                <h4 class="card-title-custom">3. Phương thức thanh toán</h4>
                <p class="card-subtitle-custom">Chọn cổng thanh toán trực tuyến an toàn hoặc thanh toán tiền mặt khi nhận hàng.</p>
              </div>
            </div>

            <div class="card-body-custom">
              <div class="payment-methods-stack">
                <!-- 1. PayOS VietQR -->
                <label
                  class="payment-option-card"
                  :class="{ 'option-selected': customerForm.phuong_thuc_thanh_toan === 'payos' }"
                >
                  <div class="d-flex align-items-center">
                    <input
                      type="radio"
                      v-model="customerForm.phuong_thuc_thanh_toan"
                      value="payos"
                      class="form-check-input me-3"
                    />
                    <div class="payment-brand-badge payos-badge">
                      <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div class="ms-3">
                      <strong class="d-block text-dark">
                        Chuyển khoản VietQR tự động (PayOS)
                        <span class="badge bg-primary ms-2">Tức thì 24/7</span>
                      </strong>
                      <span class="text-muted small">
                        Tự động hiển thị mã QR kèm số tiền và nội dung, ngân hàng khớp giao dịch sau 3 giây.
                      </span>
                    </div>
                  </div>
                </label>

                <!-- 3. COD Tiền mặt -->
                <label
                  class="payment-option-card"
                  :class="{ 'option-selected': customerForm.phuong_thuc_thanh_toan === 'cod' }"
                >
                  <div class="d-flex align-items-center">
                    <input
                      type="radio"
                      v-model="customerForm.phuong_thuc_thanh_toan"
                      value="cod"
                      class="form-check-input me-3"
                    />
                    <div class="payment-brand-badge cod-badge">
                      <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="ms-3">
                      <strong class="d-block text-dark">Thanh toán khi nhận hàng (COD)</strong>
                      <span class="text-muted small">
                        Nhận máy, mở hộp kiểm tra mã Serial/IMEI vật lý trùng khớp trước khi gửi tiền cho shipper.
                      </span>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & NÚT ĐẶT HÀNG -->
        <!-- ========================================== -->
        <div class="checkout-right-col">
          <div class="order-summary-sticky-card">
            <div class="summary-header">
              <h4 class="summary-title">
                <i class="bi bi-receipt-cutoff me-2 text-primary"></i>
                Đơn hàng của bạn ({{ totalItemsCount }} sản phẩm)
              </h4>
              <router-link to="/cart" class="btn-edit-cart">Sửa giỏ hàng</router-link>
            </div>

            <!-- DANH SÁCH THIẾT BỊ ĐẶT MUA -->
            <div class="order-items-scroll">
              <div v-for="(item, idx) in orderItems" :key="idx" class="order-item-row">
                <div class="item-img-wrap">
                  <img
                    :src="item.image || '/laptop-placeholder.png'"
                    :alt="item.ten_san_pham"
                    class="item-img"
                    @error="$event.target.src = 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=200&auto=format&fit=crop&q=80'"
                  />
                  <span class="item-qty-badge">{{ item.quantity || 1 }}</span>
                </div>
                <div class="item-details">
                  <h6 class="item-name">{{ item.ten_san_pham }}</h6>
                  <span class="item-specs text-muted small d-block">
                    {{ item.ten_bien_the || 'Cấu hình tiêu chuẩn' }}
                  </span>
                  <div class="item-price-calc mt-1">
                    <span class="unit-price text-muted small">{{ formatCurrency(item.price) }} × {{ item.quantity || 1 }}</span>
                    <strong class="line-total text-primary ms-2">{{ formatCurrency((item.price || 0) * (item.quantity || 1)) }}</strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- MÃ GIẢM GIÁ (VOUCHER) -->
            <div class="voucher-box-checkout mt-3">
              <label class="small fw-bold text-secondary mb-1 d-block">
                <i class="bi bi-ticket-perforated-fill text-danger me-1"></i> Mã giảm giá (Coupon):
              </label>
              <div class="input-group">
                <input
                  type="text"
                  v-model="voucherInput"
                  placeholder="Nhập mã (TECH500, VIP10...)"
                  class="form-control shadow-none"
                  :disabled="isVoucherApplied"
                />
                <button
                  class="btn"
                  :class="isVoucherApplied ? 'btn-danger' : 'btn-dark'"
                  type="button"
                  @click="toggleVoucherAction"
                >
                  {{ isVoucherApplied ? 'Hủy' : 'Áp dụng' }}
                </button>
              </div>
              <div v-if="voucherAlert" :class="['alert-mini mt-2', isVoucherApplied ? 'text-success' : 'text-danger']">
                <i :class="isVoucherApplied ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill'"></i>
                {{ voucherAlert }}
              </div>
            </div>

            <!-- BẢNG TÍNH TIỀN CHI TIẾT -->
            <div class="cost-breakdown mt-4">
              <div class="cost-row">
                <span class="cost-label text-muted">Tạm tính hàng hóa:</span>
                <span class="cost-value">{{ formatCurrency(subtotalAmount) }}</span>
              </div>
              <div class="cost-row" v-if="voucherDiscount > 0">
                <span class="cost-label text-success">
                  <i class="bi bi-tag-fill me-1"></i> Giảm giá Voucher ({{ appliedVoucherCode }}):
                </span>
                <span class="cost-value text-success fw-bold">-{{ formatCurrency(voucherDiscount) }}</span>
              </div>
              <div class="cost-row">
                <span class="cost-label text-muted">Phí giao hàng:</span>
                <span class="cost-value text-success fw-bold">0đ (Miễn phí)</span>
              </div>
              <div class="cost-row">
                <span class="cost-label text-muted">Bảo hiểm máy chính hãng:</span>
                <span class="cost-value text-primary">0đ (TechMart tài trợ)</span>
              </div>
            </div>

            <hr class="my-3" />

            <!-- TỔNG TIỀN THANH TOÁN LỚN -->
            <div class="total-payable-box">
              <div class="d-flex justify-content-between align-items-center">
                <span class="payable-label">TỔNG THANH TOÁN:</span>
                <span class="payable-amount">{{ formatCurrency(finalTotalAmount) }}</span>
              </div>
              <span class="vat-note small text-muted d-block text-end mt-1">Đã bao gồm thuế VAT 10%</span>
            </div>

            <!-- THÔNG BÁO LỖI NẾU CÓ -->
            <div v-if="submissionError" class="alert alert-danger mt-3 py-2 small">
              <i class="bi bi-exclamation-circle-fill me-1"></i> {{ submissionError }}
            </div>

            <!-- NÚT XÁC NHẬN ĐẶT HÀNG & THANH TOÁN -->
            <button
              type="button"
              class="btn-submit-order mt-3"
              @click="submitOrder"
              :disabled="isSubmitting || orderItems.length === 0"
            >
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-shield-check me-2"></i>
              <span>{{ getSubmitButtonText() }}</span>
            </button>

            <!-- CAM KẾT CHÍNH HÃNG -->
            <div class="checkout-guarantees mt-3">
              <div class="guarantee-item">
                <i class="bi bi-patch-check-fill text-success me-2"></i>
                <span>Cam kết 100% hàng chính hãng dán Serial/IMEI vật lý</span>
              </div>
              <div class="guarantee-item">
                <i class="bi bi-arrow-repeat text-primary me-2"></i>
                <span>1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất</span>
              </div>
              <div class="guarantee-item">
                <i class="bi bi-lock-fill text-warning me-2"></i>
                <span>Mã hóa bảo mật giao dịch SSL 256-bit chuẩn ngân hàng</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- ❸ MODAL XÁC NHẬN ĐẶT HÀNG THÀNH CÔNG (COD) -->
    <!-- ========================================== -->
    <div class="modal fade show d-block" v-if="successOrderModal" style="background: rgba(0,0,0,0.6);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-success text-white py-3">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-check-circle-fill me-2"></i> ĐẶT HÀNG THÀNH CÔNG!
            </h5>
          </div>
          <div class="modal-body p-4 text-center">
            <div class="success-icon-anim mb-3 text-success">
              <i class="bi bi-bag-check-fill" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold text-dark">Cảm ơn quý khách đã mua sắm tại TechMart!</h4>
            <p class="text-muted">
              Đơn hàng của bạn đã được tiếp nhận và nhân viên chăm sóc sẽ gọi điện xác nhận trong ít phút.
            </p>

            <div class="order-receipt-box bg-light rounded-3 p-3 text-start mb-3 border">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Mã đơn hàng:</span>
                <strong class="text-primary font-monospace fs-5">#{{ successOrderModal.ma_don_hang }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Người nhận:</span>
                <strong>{{ successOrderModal.ho_ten_nguoi_nhan }} ({{ successOrderModal.so_dien_thoai_nhan }})</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Địa chỉ nhận hàng:</span>
                <span class="text-end text-truncate ms-3" style="max-width: 250px;">{{ successOrderModal.dia_chi_giao_hang }}</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Phương thức:</span>
                <span class="badge bg-secondary">COD - Tiền mặt khi nhận</span>
              </div>
              <hr class="my-2" />
              <div class="d-flex justify-content-between">
                <span class="fw-bold">Tổng thanh toán:</span>
                <strong class="text-danger fs-5">{{ formatCurrency(successOrderModal.tong_thanh_toan) }}</strong>
              </div>
            </div>

            <div class="alert alert-info py-2 small mb-0">
              <i class="bi bi-info-circle-fill me-1"></i> Quý khách được quyền kiểm tra đối chiếu mã Serial/IMEI trên hộp máy trước khi thanh toán.
            </div>
          </div>
          <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-center">
            <router-link to="/my-orders" class="btn btn-primary px-4 py-2">
              <i class="bi bi-bag-check-fill me-1"></i> Xem đơn hàng của tôi
            </router-link>
            <router-link to="/" class="btn btn-outline-secondary px-4 py-2">
              <i class="bi bi-house-door-fill me-1"></i> Về Trang chủ
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { loadProvinces, loadDistricts, loadWards, ALL_63_PROVINCES } from '@/data/vietnamAddress.js'

// Khắc phục lỗi hiển thị icon marker của Leaflet trong Vite
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'
})

export default {
  name: 'TechMartCheckout',
  data() {
    return {
      currentUser: null,
      orderItems: [],
      isLoadingCart: true,
      isSubmitting: false,
      submissionError: '',
      successOrderModal: null,

      // Form thông tin khách
      customerForm: {
        ho_ten: '',
        so_dien_thoai: '',
        email: '',
        ghi_chu: '',
        phuong_thuc_thanh_toan: 'payos' // Mặc định PayOS VietQR
      },

      // Form địa chỉ phân cấp
      addressForm: {
        provinceCode: '',
        provinceName: '',
        districtCode: '',
        districtName: '',
        wardCode: '',
        wardName: '',
        street: ''
      },

      // Danh sách dropdown
      provincesList: [],
      districtsList: [],
      wardsList: [],

      // Validation errors
      formErrors: {
        ho_ten: '',
        so_dien_thoai: '',
        province: '',
        district: '',
        ward: '',
        street: ''
      },

      // Voucher
      voucherInput: '',
      isVoucherApplied: false,
      appliedVoucherCode: '',
      voucherDiscount: 0,
      voucherAlert: '',

      // Bản đồ Leaflet
      mapInstance: null,
      markerInstance: null,
      currentCoords: { lat: 16.0544, lng: 108.2022 }, // Tọa độ Đà Nẵng mặc định
      isLocatingGps: false,
      mapSearchQuery: '',
      isSearchingMap: false
    }
  },
  computed: {
    totalItemsCount() {
      return this.orderItems.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0)
    },
    subtotalAmount() {
      return this.orderItems.reduce((sum, item) => {
        const p = Number(item.price) || 0
        const q = Number(item.quantity) || 1
        return sum + p * q
      }, 0)
    },
    finalTotalAmount() {
      return Math.max(0, this.subtotalAmount - this.voucherDiscount)
    },
    fullShippingAddress() {
      const parts = []
      if (this.addressForm.street.trim()) parts.push(this.addressForm.street.trim())
      if (this.addressForm.wardName) parts.push(this.addressForm.wardName)
      if (this.addressForm.districtName) parts.push(this.addressForm.districtName)
      if (this.addressForm.provinceName) parts.push(this.addressForm.provinceName)
      return parts.join(', ')
    }
  },
  async mounted() {
    this.loadUserData()
    await this.loadCartItems()
    await this.initProvinces()
    this.initLeafletMap()
  },
  beforeUnmount() {
    if (this.mapInstance) {
      this.mapInstance.remove()
      this.mapInstance = null
    }
  },
  methods: {
    // ==========================================
    // 1. NẠP THÔNG TIN NGƯỜI DÙNG & GIỎ HÀNG
    // ==========================================
    loadUserData() {
      const stored = localStorage.getItem('customer_user') || localStorage.getItem('admin_user')
      if (stored) {
        try {
          this.currentUser = JSON.parse(stored)
          this.customerForm.ho_ten = this.currentUser.ho_ten || this.currentUser.name || ''
          this.customerForm.so_dien_thoai = this.currentUser.so_dien_thoai || ''
          this.customerForm.email = this.currentUser.email || ''
          if (this.currentUser.dia_chi) {
            this.addressForm.street = this.currentUser.dia_chi
          }
        } catch (e) {
          console.error(e)
        }
      }

      // Nạp voucher đã áp dụng từ giỏ hàng nếu có
      const storedVoucher = localStorage.getItem('techmart_checkout_voucher')
      if (storedVoucher) {
        try {
          const v = JSON.parse(storedVoucher)
          if (v && v.code) {
            this.voucherInput = v.code
            this.appliedVoucherCode = v.code
            this.voucherDiscount = Number(v.discount) || 0
            this.isVoucherApplied = true
            this.voucherAlert = `Áp dụng thành công mã giảm giá ${v.code}!`
          }
        } catch (e) {
          console.error(e)
        }
      }
    },

    async loadCartItems() {
      this.isLoadingCart = true
      // 1. Thử lấy từ localStorage đã lưu khi bấm nút Tiến hành đặt hàng ở /cart
      const localItems = localStorage.getItem('techmart_checkout_items')
      if (localItems) {
        try {
          const parsed = JSON.parse(localItems)
          if (Array.isArray(parsed) && parsed.length > 0) {
            this.orderItems = parsed
            this.isLoadingCart = false
            return
          }
        } catch (e) {
          console.error(e)
        }
      }

      // 2. Nếu localStorage không có, gọi API lấy giỏ hàng thực tế từ CSDL
      try {
        const userId = localStorage.getItem('user_id') || (this.currentUser ? this.currentUser.id : null)
        const sessionId = localStorage.getItem('techmart_session_id') || null
        const res = await axios.get('http://127.0.0.1:8000/api/client/gio-hang/get-data', {
          params: { nguoi_dung_id: userId, session_id: sessionId }
        })

        if (res.data && res.data.status && Array.isArray(res.data.data)) {
          this.orderItems = res.data.data.map(i => ({
            gio_hang_id: i.gio_hang_id || i.id,
            bien_the_id: i.bien_the_id,
            ten_san_pham: i.name || i.ten_san_pham,
            ten_bien_the: i.ten_bien_the || i.specs || 'Tiêu chuẩn',
            quantity: i.quantity || 1,
            price: Number(i.gia_gio_hang || i.gia || i.price || 0),
            image: i.image || i.hinh_anh || ''
          }))
        }
      } catch (err) {
        console.warn('Không thể nạp giỏ hàng từ API, giỏ hàng đang trống.')
      } finally {
        this.isLoadingCart = false
      }
    },

    // ==========================================
    // 2. KHỞI TẠO TỈNH THÀNH & XỬ LÝ CHỌN CASCADED
    // ==========================================
    async initProvinces() {
      this.provincesList = await loadProvinces()
      // Mặc định chọn Đà Nẵng nếu chưa chọn gì
      const defaultProv = this.provincesList.find(p => p.code === '48' || p.name.includes('Đà Nẵng'))
      if (defaultProv) {
        this.addressForm.provinceCode = defaultProv.code
        this.addressForm.provinceName = defaultProv.name
        await this.onProvinceChange()
      }
    },

    async onProvinceChange() {
      this.addressForm.districtCode = ''
      this.addressForm.districtName = ''
      this.addressForm.wardCode = ''
      this.addressForm.wardName = ''
      this.districtsList = []
      this.wardsList = []

      const selected = this.provincesList.find(p => String(p.code) === String(this.addressForm.provinceCode))
      if (selected) {
        this.addressForm.provinceName = selected.name
        this.districtsList = await loadDistricts(selected.code)
        // Di chuyển bản đồ đến Tỉnh/Thành đã chọn
        if (selected.lat && selected.lng) {
          this.flyToLocation(selected.lat, selected.lng, 12, `Khu vực: ${selected.name}`)
        }
      }
    },

    async onDistrictChange() {
      this.addressForm.wardCode = ''
      this.addressForm.wardName = ''
      this.wardsList = []

      const selected = this.districtsList.find(d => String(d.code) === String(this.addressForm.districtCode))
      if (selected) {
        this.addressForm.districtName = selected.name
        this.wardsList = await loadWards(selected.code)
        // Tự động tìm kiếm tọa độ quận trên bản đồ
        this.searchPlaceOnMapQuietly(`${selected.name}, ${this.addressForm.provinceName}`)
      }
    },

    onWardChange() {
      const selected = this.wardsList.find(w => String(w.code) === String(this.addressForm.wardCode))
      if (selected) {
        this.addressForm.wardName = selected.name
        this.searchPlaceOnMapQuietly(`${selected.name}, ${this.addressForm.districtName}, ${this.addressForm.provinceName}`)
      }
    },

    updateCombinedAddress() {
      // Tự động tính toán lại fullShippingAddress
    },

    // ==========================================
    // 3. KHỞI TẠO BẢN ĐỒ TƯƠNG TÁC LEAFLET
    // ==========================================
    initLeafletMap() {
      this.$nextTick(() => {
        const container = document.getElementById('checkout-leaflet-map')
        if (!container || this.mapInstance) return

        // Tọa độ khởi tạo: Đà Nẵng
        const initLat = 16.0544
        const initLng = 108.2022

        this.mapInstance = L.map('checkout-leaflet-map', {
          center: [initLat, initLng],
          zoom: 13,
          zoomControl: true
        })

        // Nguồn gạch bản đồ OpenStreetMap (Miễn phí, chuẩn xác)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap contributors'
        }).addTo(this.mapInstance)

        // Tạo Ghim vị trí (Marker) có thể kéo thả
        this.markerInstance = L.marker([initLat, initLng], {
          draggable: true
        }).addTo(this.mapInstance)

        this.markerInstance.bindPopup('<b>📍 Vị trí giao hàng TechMart</b><br>Kéo thả ghim để chọn vị trí chính xác nhất.').openPopup()

        // Bắt sự kiện khi người dùng Kéo thả ghim
        this.markerInstance.on('dragend', (e) => {
          const position = e.target.getLatLng()
          this.handleMapLocationSelected(position.lat, position.lng)
        })

        // Bắt sự kiện khi người dùng Click trực tiếp lên bản đồ
        this.mapInstance.on('click', (e) => {
          const { lat, lng } = e.latlng
          this.markerInstance.setLatLng([lat, lng])
          this.handleMapLocationSelected(lat, lng)
        })
      })
    },

    flyToLocation(lat, lng, zoom = 14, popupText = '') {
      this.currentCoords = { lat, lng }
      if (this.mapInstance && this.markerInstance) {
        this.mapInstance.flyTo([lat, lng], zoom, { duration: 1.2 })
        this.markerInstance.setLatLng([lat, lng])
        if (popupText) {
          this.markerInstance.setPopupContent(`<b>📍 Điểm giao hàng:</b><br>${popupText}`).openPopup()
        }
      }
    },

    // Xử lý khi chọn vị trí mới trên bản đồ (Reverse Geocoding)
    async handleMapLocationSelected(lat, lng) {
      this.currentCoords = { lat, lng }
      try {
        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
        if (res.ok) {
          const data = await res.json()
          if (data && data.display_name) {
            const shortName = data.name || data.address?.road || data.display_name.split(',')[0]
            if (!this.addressForm.street) {
              this.addressForm.street = shortName
            }
            this.markerInstance.setPopupContent(`<b>📍 Vị trí đã ghim:</b><br>${data.display_name}`).openPopup()
          }
        }
      } catch (err) {
        console.warn('Reverse geocoding không khả dụng:', err)
      }
    },

    // Định vị vị trí hiện tại của thiết bị qua HTML5 Geolocation (GPS)
    getCurrentLocation() {
      if (!navigator.geolocation) {
        alert('Trình duyệt của bạn không hỗ trợ định vị Geolocation.')
        return
      }

      this.isLocatingGps = true
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude
          const lng = position.coords.longitude
          this.isLocatingGps = false
          this.flyToLocation(lat, lng, 16, 'Vị trí hiện tại của bạn (GPS)')
          this.handleMapLocationSelected(lat, lng)
        },
        (error) => {
          this.isLocatingGps = false
          alert('Không thể lấy vị trí hiện tại: ' + (error.message || 'Vui lòng cho phép quyền truy cập vị trí.'))
        },
        { enableHighAccuracy: true, timeout: 10000 }
      )
    },

    // Tìm kiếm địa điểm trên bản đồ
    async searchPlaceOnMap() {
      const q = this.mapSearchQuery.trim()
      if (!q) return

      this.isSearchingMap = true
      try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q + ', Vietnam')}&limit=1`)
        if (res.ok) {
          const results = await res.json()
          if (results && results.length > 0) {
            const item = results[0]
            const lat = parseFloat(item.lat)
            const lon = parseFloat(item.lon)
            this.flyToLocation(lat, lon, 15, item.display_name)
            if (!this.addressForm.street) {
              this.addressForm.street = q
            }
          } else {
            alert('Không tìm thấy địa điểm trên bản đồ. Vui lòng nhập địa danh cụ thể hơn.')
          }
        }
      } catch (e) {
        console.error(e)
      } finally {
        this.isSearchingMap = false
      }
    },

    async searchPlaceOnMapQuietly(query) {
      try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Vietnam')}&limit=1`)
        if (res.ok) {
          const results = await res.json()
          if (results && results.length > 0) {
            const item = results[0]
            this.flyToLocation(parseFloat(item.lat), parseFloat(item.lon), 14, item.display_name)
          }
        }
      } catch (e) {
        // bỏ qua nếu lỗi mạng tìm kiếm ngầm
      }
    },

    // ==========================================
    // 4. XỬ LÝ VOUCHER & TÍNH TIỀN
    // ==========================================
    toggleVoucherAction() {
      if (this.isVoucherApplied) {
        this.isVoucherApplied = false
        this.appliedVoucherCode = ''
        this.voucherDiscount = 0
        this.voucherInput = ''
        this.voucherAlert = 'Đã hủy áp dụng voucher.'
        localStorage.removeItem('techmart_checkout_voucher')
        return
      }

      const code = (this.voucherInput || '').trim().toUpperCase()
      if (!code) {
        this.voucherAlert = 'Vui lòng nhập mã voucher!'
        return
      }

      // Giả lập danh mục voucher hợp lệ
      const vouchers = {
        'TECH500': { discount: 500000, desc: 'Giảm 500.000đ cho đơn hàng thiết bị' },
        'TECH1M': { discount: 1000000, desc: 'Giảm 1.000.000đ cho đơn Laptop Gaming' },
        'VIP10': { discount: 300000, desc: 'Giảm 300.000đ tri ân khách hàng thân thiết' }
      }

      if (vouchers[code]) {
        this.isVoucherApplied = true
        this.appliedVoucherCode = code
        this.voucherDiscount = vouchers[code].discount
        this.voucherAlert = `Áp dụng thành công mã ${code}: ${vouchers[code].desc}!`
        localStorage.setItem('techmart_checkout_voucher', JSON.stringify({ code, discount: this.voucherDiscount }))
      } else {
        this.voucherAlert = 'Mã giảm giá không tồn tại hoặc đã hết hạn!'
      }
    },

    // ==========================================
    // 5. VALIDATION & GỬI ĐƠN HÀNG CHECKOUT
    // ==========================================
    validateForm() {
      let isValid = true
      this.formErrors = {
        ho_ten: '',
        so_dien_thoai: '',
        province: '',
        district: '',
        ward: '',
        street: ''
      }

      if (!this.customerForm.ho_ten.trim()) {
        this.formErrors.ho_ten = 'Vui lòng nhập họ và tên người nhận.'
        isValid = false
      }

      const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/
      if (!this.customerForm.so_dien_thoai.trim()) {
        this.formErrors.so_dien_thoai = 'Vui lòng nhập số điện thoại.'
        isValid = false
      } else if (!phoneRegex.test(this.customerForm.so_dien_thoai.trim())) {
        this.formErrors.so_dien_thoai = 'Số điện thoại không đúng định dạng (10 chữ số).'
        isValid = false
      }

      if (!this.addressForm.provinceCode) {
        this.formErrors.province = 'Vui lòng chọn Tỉnh / Thành phố.'
        isValid = false
      }
      if (!this.addressForm.districtCode) {
        this.formErrors.district = 'Vui lòng chọn Quận / Huyện.'
        isValid = false
      }
      if (!this.addressForm.wardCode) {
        this.formErrors.ward = 'Vui lòng chọn Phường / Xã.'
        isValid = false
      }
      if (!this.addressForm.street.trim()) {
        this.formErrors.street = 'Vui lòng nhập số nhà, tên đường hoặc địa chỉ cụ thể.'
        isValid = false
      }

      return isValid
    },

    getSubmitButtonText() {
      const method = this.customerForm.phuong_thuc_thanh_toan
      if (method === 'payos') return 'THANH TOÁN QUA PAYOS VIETQR >>'
      return 'XÁC NHẬN ĐẶT HÀNG (COD) >>'
    },

    async submitOrder() {
      this.submissionError = ''

      if (!this.validateForm()) {
        this.submissionError = 'Vui lòng kiểm tra và điền đầy đủ các thông tin giao hàng bắt buộc.'
        window.scrollTo({ top: 100, behavior: 'smooth' })
        return
      }

      if (this.orderItems.length === 0) {
        this.submissionError = 'Giỏ hàng thanh toán đang trống.'
        return
      }

      this.isSubmitting = true
      const userId = localStorage.getItem('user_id') || (this.currentUser ? this.currentUser.id : null)
      const sessionId = localStorage.getItem('techmart_session_id') || null

      const payload = {
        user_id: userId,
        session_id: sessionId,
        ho_ten_nguoi_nhan: this.customerForm.ho_ten.trim(),
        so_dien_thoai_nhan: this.customerForm.so_dien_thoai.trim(),
        dia_chi_giao_hang: this.fullShippingAddress,
        ghi_chu: this.customerForm.ghi_chu.trim(),
        phuong_thuc_thanh_toan: this.customerForm.phuong_thuc_thanh_toan,
        ma_voucher: this.isVoucherApplied ? this.appliedVoucherCode : '',
        so_tien_giam: this.voucherDiscount,
        items: this.orderItems.map(i => ({
          gio_hang_id: i.gio_hang_id || null,
          bien_the_id: i.bien_the_id || null,
          ten_san_pham: i.ten_san_pham,
          ten_bien_the: i.ten_bien_the || 'Tiêu chuẩn',
          quantity: i.quantity || 1,
          price: i.price || 0
        }))
      }

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/client/don-hang/checkout', payload)

        if (res.data && res.data.status) {
          // Xóa giỏ hàng tạm khỏi localStorage
          localStorage.removeItem('techmart_checkout_items')
          localStorage.removeItem('techmart_checkout_voucher')

          // 1. Chuyển hướng nếu là PayOS
          if (res.data.payment_method === 'payos' && res.data.checkout_url) {
            window.location.href = res.data.checkout_url
            return
          }

          // 2. Nếu là COD -> Mở modal thành công
          this.successOrderModal = res.data.don_hang || {
            ma_don_hang: 'DH' + Date.now().toString().slice(-6),
            ho_ten_nguoi_nhan: this.customerForm.ho_ten,
            so_dien_thoai_nhan: this.customerForm.so_dien_thoai,
            dia_chi_giao_hang: this.fullShippingAddress,
            tong_thanh_toan: this.finalTotalAmount
          }
        } else {
          this.submissionError = res.data?.message || 'Không thể tạo đơn hàng. Vui lòng thử lại.'
        }
      } catch (err) {
        this.submissionError = err.response?.data?.message || 'Lỗi kết nối máy chủ thanh toán. Vui lòng kiểm tra lại.'
      } finally {
        this.isSubmitting = false
      }
    },

    formatCurrency(val) {
      if (!val || isNaN(val)) return '0đ'
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val)
    }
  }
}
</script>

<style scoped>
/* ==========================================
   STYLE CHUẨN TECHMART AI CHECKOUT & BẢN ĐỒ
   ========================================== */
.checkout-page-wrapper {
  background-color: #f4f6fa;
  min-height: 100vh;
  color: #1e293b;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  padding-bottom: 60px;
}

.main-width {
  max-width: 1240px;
  margin: 0 auto;
  padding: 0 16px;
}

/* Header */
.checkout-header {
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 72px;
}

.brand-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
  font-size: 1.25rem;
  color: #0f172a;
}

.logo-icon {
  width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  border-radius: 10px;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
  font-size: 1.1rem;
}

.badge-checkout {
  background: #eff6ff;
  color: #2563eb;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
  letter-spacing: 0.5px;
}

.breadcrumb-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #2563eb;
}

/* Stepper */
.checkout-stepper {
  display: flex;
  align-items: center;
  gap: 12px;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  color: #94a3b8;
}

.step-number {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

.step-completed .step-number {
  background: #10b981;
  color: #fff;
}

.step-completed .step-label {
  color: #10b981;
}

.step-active .step-number {
  background: #2563eb;
  color: #fff;
}

.step-active .step-label {
  color: #0f172a;
  font-weight: 700;
}

.step-line {
  width: 32px;
  height: 2px;
  background: #e2e8f0;
}

.step-line.active {
  background: #10b981;
}

.user-pill {
  background: #f1f5f9;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.88rem;
  font-weight: 600;
}

/* Content layout */
.checkout-main-content {
  margin-top: 28px;
}

.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 24px;
  align-items: start;
}

@media (max-width: 992px) {
  .checkout-grid {
    grid-template-columns: 1fr;
  }
}

/* Cards */
.checkout-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
  overflow: hidden;
}

.card-header-custom {
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
}

.header-icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  margin-right: 14px;
  flex-shrink: 0;
}

.card-title-custom {
  font-size: 1.08rem;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
}

.card-subtitle-custom {
  font-size: 0.82rem;
  color: #64748b;
  margin: 2px 0 0;
}

.card-body-custom {
  padding: 20px 24px;
}

/* Form styles */
.form-label-custom {
  font-size: 0.88rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
  display: block;
}

.input-group-custom {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  color: #94a3b8;
  font-size: 1rem;
}

.form-control-custom,
.form-select-custom {
  width: 100%;
  padding: 10px 14px 10px 40px;
  border: 1.5px solid #cbd5e1;
  border-radius: 10px;
  font-size: 0.92rem;
  color: #0f172a;
  background-color: #fff;
  transition: all 0.2s ease;
}

.form-select-custom {
  padding-left: 14px;
}

.form-control-custom:focus,
.form-select-custom:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.form-control-custom.is-invalid,
.form-select-custom.is-invalid {
  border-color: #ef4444;
}

.error-feedback {
  font-size: 0.78rem;
  color: #ef4444;
  margin-top: 4px;
}

/* GPS Button */
.btn-gps-locate {
  background: #f8fafc;
  border: 1.5px solid #cbd5e1;
  color: #0f172a;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  transition: all 0.2s ease;
}

.btn-gps-locate:hover {
  background: #eff6ff;
  border-color: #2563eb;
  color: #2563eb;
}

.spin-icon {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  100% { transform: rotate(360deg); }
}

/* Map */
.map-wrapper-box {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  border: 2px solid #e2e8f0;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

.map-toolbar-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 500;
  pointer-events: none;
}

.interactive-leaflet-map {
  width: 100%;
  height: 290px;
  background: #e5e7eb;
}

/* Preview Address */
.combined-address-card {
  background: #f8fafc;
  border: 1.5px dashed #93c5fd;
  border-radius: 12px;
  padding: 14px 18px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.address-preview-icon {
  font-size: 1.8rem;
  line-height: 1;
}

.preview-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: #2563eb;
  letter-spacing: 0.5px;
}

.preview-text {
  font-size: 0.94rem;
  line-height: 1.4;
  margin-top: 2px;
}

/* Payment Methods */
.payment-methods-stack {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.payment-option-card {
  display: block;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 18px;
  cursor: pointer;
  background: #ffffff;
  transition: all 0.2s ease;
}

.payment-option-card:hover {
  border-color: #93c5fd;
  background: #f8fafc;
}

.payment-option-card.option-selected {
  border-color: #2563eb;
  background: #f0f7ff;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}

.payment-brand-badge {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}

.vnpay-badge {
  background: #ed1c24;
  color: #fff;
}

.vnpay-text {
  font-size: 0.8rem;
  font-weight: 900;
  letter-spacing: -0.5px;
}

.payos-badge {
  background: #0284c7;
  color: #fff;
}

.cod-badge {
  background: #16a34a;
  color: #fff;
}

/* Right Sticky Summary */
.order-summary-sticky-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  padding: 24px;
  position: sticky;
  top: 96px;
}

.summary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 14px;
}

.summary-title {
  font-size: 1.05rem;
  font-weight: 700;
  margin: 0;
}

.btn-edit-cart {
  font-size: 0.82rem;
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
}

.order-items-scroll {
  max-height: 280px;
  overflow-y: auto;
  padding-right: 4px;
  margin-top: 14px;
}

.order-item-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f8fafc;
}

.item-img-wrap {
  position: relative;
  width: 54px;
  height: 54px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-img {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
}

.item-qty-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #475569;
  color: #fff;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.item-name {
  font-size: 0.88rem;
  font-weight: 600;
  margin: 0;
  color: #0f172a;
  line-height: 1.3;
}

.cost-breakdown {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cost-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
}

.total-payable-box {
  background: #f8fafc;
  padding: 14px 18px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.payable-label {
  font-weight: 700;
  font-size: 0.95rem;
  color: #0f172a;
}

.payable-amount {
  font-size: 1.35rem;
  font-weight: 800;
  color: #dc2626;
}

.btn-submit-order {
  width: 100%;
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  color: #ffffff;
  border: none;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3);
  transition: all 0.2s ease;
}

.btn-submit-order:hover:not(:disabled) {
  background: linear-gradient(135deg, #b91c1c, #991b1b);
  box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4);
  transform: translateY(-1px);
}

.btn-submit-order:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.checkout-guarantees {
  display: flex;
  flex-direction: column;
  gap: 8px;
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
}

.guarantee-item {
  display: flex;
  align-items: center;
  font-size: 0.78rem;
  color: #475569;
}

/* Empty state */
.empty-checkout-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  padding: 60px 20px;
  text-align: center;
  max-width: 600px;
  margin: 40px auto;
}

.empty-icon {
  font-size: 4rem;
}
</style>
